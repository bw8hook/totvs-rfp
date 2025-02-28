<?php
namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\KnowledgeBase;
use App\Models\KnowledgeRecord;
use App\Models\RfpBundle;
use App\Models\Agent;
use App\Models\ProjectAnswer;
use App\Models\RfpProcess;
use App\Models\RfpAnswer;
use App\Models\UsersDepartaments;
use App\Imports\ProjectRecordsImport;
use App\Imports\KnowledgeBaseInfoImport;
use App\Exports\KnowledgeBaseExport;
use App\Models\Project;
use App\Models\ProjectFiles;
use App\Models\ProjectRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use App\Http\Controllers\MentoriaController;
use App\Models\Module;
use Illuminate\Support\Str;
use DateTime;


use App\Services\ParallelRequests;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Exception\RequestException;


class ProjectController extends Controller
{
    protected $baseUrlConteudo;
    protected $baseUrl;
    protected $apiKey;
    protected $knowledgeBaseId;

    public function __construct()
    {
        $this->baseUrl = 'https://api.conteudo.rdstationmentoria.com.br/rest';
        $this->baseUrlConteudo = 'https://api.conteudo.staging.rdstationmentoria.com.br/rest';
        $this->apiKey = 'eyJhbGciOiJSUzUxMiIsInR5cCI6IkpXVCJ9.eyJ0eXBlIjoiYXBpIiwicHJvcGVydGllcyI6eyJrZXlJZCI6ImtleV8wMUpGMEhQWEc1SkRSNTlBUzU2OFgwVjk3WSIsIndvcmtzcGFjZUlkIjoid3BjXzAxSjVCSFdCNTRFSldDRE42QVFZMlg2NUo3IiwidXNlcklkIjoidXNlcl9hcGkifSwiaWF0IjoxNzM0MTExNjIyfQ.cLjsIB85bybra-rQOTAI-GLuIQKeQP95HLdXu-JG1yxMbrdzHwjqLGl8xzo3aVwz94uD3mWaOhajdqync0CCusVM_VF3dEsg2bRd9OM02HMD-rxil360HClB--5zYKOW7NZUPKmj0Q8rl-1v-aE4lFes6U7-_zB1gJWiGTLR9HLuZd3E5EsSqxu_mS49ss5tAFHQYrVotns6Ug5OGmxSgJ-IqlluVMPRbI8dtSb0ZsiYHe_xYtaERhTInevjaqgHbhZwLzyHg50R7MMoJLsGlw8CD3KRfcitxj8NZmilKK4vCkjm4dN5QYTuRxSqpgnGRtCCk-3fR0q8N3GwYF4oWQ';
    }

     /**
     * Display a listing of the resource.
     */
    public function index()
    { 
        // Validação se mostra Tudo ou apenas do usuário
        if(Auth::user()->role->role_priority >= 90){  
            $AllProject = Project::withCount('records')->get();
            $AllFiles = ProjectFiles::withCount('records')->get();

            // Último atualizado
            $lastUpdated = ProjectFiles::orderBy('updated_at', 'desc')->first();
            if ($lastUpdated) {
                $lastUpdatedDate = Carbon::parse($lastUpdated->updated_at)->format('d/m/Y');
                $lastUpdatedTime = Carbon::parse($lastUpdated->updated_at)->format('H\hi');
            } else {
                $lastUpdatedDate = null;
                $lastUpdatedTime = null;
            }
        }else{
            $AllProject = Project::withCount('records')->where('iduser_responsable', Auth::id())->get();
            $AllFiles = ProjectFiles::withCount('records')->where('user_id', Auth::id())->get();

            // Último atualizado
            $lastUpdated = ProjectFiles::where('user_id', Auth::id())->orderBy('updated_at', 'desc')->first();
            if ($lastUpdated) {
                $lastUpdatedDate = Carbon::parse($lastUpdated->updated_at)->format('d/m/Y');
                $lastUpdatedTime = Carbon::parse($lastUpdated->updated_at)->format('H\hi');
            } else {
                $lastUpdatedDate = null;
                $lastUpdatedTime = null;
            }
        }

            $ListFiles = array();
            $CountRFPs = 0;
            $CountRequisitos = 0;
            $CountNotAnswer = 0;
            $CountAnswerUser = 0;
            $CountAnswerIA = 0;

            foreach ($AllFiles as $key => $File) {
                $CountRFPs++;
                $ListFile = array();
                $ListFile['project_file_id'] = $File->id;
                $ListFile['bundle'] = RfpBundle::firstWhere('bundle_id', $File->bundle_id);
                $ListFile['filepath'] = $File->filepath;
                $ListFile['filename_original'] = $File->filename_original;
                $ListFile['filename'] = $File->filename;
                $ListFile['file_extension'] = $File->file_extension;
                $ListFile['status'] = $File->status;
                $ListFile['created_at'] = date("d/m/Y", strtotime($File->created_at));;
                $ListFiles[] = $ListFile;

                $CountRequisitos += $File->records_count;
                $CountNotAnswer += $File->records->where('status', 'aguardando')->count();
                $CountAnswerUser += $File->records->where('status', 'user edit')->count();
                $CountAnswerIA += $File->records->where('status', 'respondido ia')->count();
            }

            $data = array(
                'title' => 'Todos Arquivos',
                'lastUpdated' => $lastUpdated,
                'lastUpdatedDate' => $lastUpdatedDate,
                'lastUpdatedTime' => $lastUpdatedTime,
                'ListFiles' => $ListFiles,
                'CountProject' => $AllProject->count(),
                'CountRFPs' => $CountRFPs,
                'CountRequisitos' => $CountRequisitos,
                'CountNotAnswer' => $CountNotAnswer,
                'CountAnswerUser' => $CountAnswerUser,
                'CountAnswerIA' =>  $CountAnswerIA,
            );
    
            return view('project.list')->with($data);
        

    }


    public function filter(Request $request)
    { 
        // Valida a Permissão do usuário
        if(Auth::user()->role->role_priority >= 90){       
            $query = Project::query()->with('user')->withCount(relations: 'records');
        }else{
            $query = Project::query()->with('user')->withCount(relations: 'records')->where('iduser_responsable', Auth::id());
        }

            // Aplicar filtros
            if ($request->has('filter')) {
                foreach ($request->filter as $field => $value) {
                    if (!empty($value)) {
                        $query->where($field, 'like', '%' . $value . '%');
                    }
                }
            }

            // Aplicar ordenação
            if ($request->has('sort_by') && $request->has('sort_order')) {
                $sortBy = $request->sort_by;
                $sortOrder = $request->sort_order;

                if (in_array($sortBy, ['name', 'id', 'gestor','email', 'account_type', 'status', 'created_at']) && in_array($sortOrder, ['asc', 'desc'])) {
                    $query->orderBy($sortBy, $sortOrder);
                }
            }

            // Paginação
            $data = $query->paginate(40);          
            
            // Retornar dados em JSON
            return response()->json($data);
        
    }



    public function detail(string $id)
    { 
        $Detail = Project::with('user')->find($id);
        if(Auth::user()->role->role_priority >= 90 || $Detail->iduser_responsable == Auth::id()){  
            $AllFiles = ProjectFiles::where('project_id', $id)->withCount('records')->get();

            $lastUpdated = ProjectFiles::where('user_id', Auth::id())->orderBy('updated_at', 'desc')->first();
            if ($lastUpdated) {
                $lastUpdatedDate = Carbon::parse($lastUpdated->updated_at)->format('d/m/Y');
                $lastUpdatedTime = Carbon::parse($lastUpdated->updated_at)->format('H\hi');
            } else {
                $lastUpdatedDate = null;
                $lastUpdatedTime = null;
            }

            $ListFiles = array();
            $CountRFPs = 0;
            $CountRequisitos = 0;
            $CountAnswerUser = 0;
            $CountAnswerIA = 0;
            $CountNotAnswer = 0;

            $ListProducts = array();

            foreach ($AllFiles as $key => $File) {
                    $CountRFPs++;
                    $ListFile = array();
                    $ListFile['project_file_id'] = $File->id;
                    $ListFile['bundle'] = RfpBundle::firstWhere('bundle_id', $File->bundle_id);
                    $ListFile['filepath'] = $File->filepath;
                    $ListFile['filename_original'] = $File->filename_original;
                    $ListFile['filename'] = $File->filename;
                    $ListFile['file_extension'] = $File->file_extension;
                    $ListFile['status'] = $File->status;
                    $ListFile['created_at'] = date("d/m/Y", strtotime($File->created_at));;
                    $CountRequisitos += $File->records_count;
                    $CountNotAnswer += $File->records->where('status', 'aguardando')->count();
                    $CountAnswerUser += $File->records->where('status', 'user edit')->count();
                    $CountAnswerIA += $File->records->where('status', 'respondido ia')->count();

                    $ListProducts[] = $ListFile['bundle']->bundle;
                    $ListProducts = array_unique($ListProducts);
                    
                    $ListFiles[] = $ListFile;
            }

            $data = array(
                'Detail' => $Detail,
                'lastUpdated' => $lastUpdated,
                'lastUpdatedDate' => $lastUpdatedDate,
                'lastUpdatedTime' => $lastUpdatedTime,
                'ListFiles' => $ListFiles,
                'ListProducts' => $ListProducts,
                'CountRFPs' => $CountRFPs,
                'CountRequisitos' => $CountRequisitos,
                'CountAnswerUser' => $CountAnswerUser,
                'CountAnswerIA' => $CountAnswerIA,
                'CountNotAnswer' => $CountNotAnswer,
            );
    
            return view('project.detail')->with($data);
        }

    }


    public function filterDetail(Request $request)
    { 
        // Valida a Permissão do usuário
        

        if(Auth::user()->role->role_priority >= 90){    
            //$query = ProjectFiles::query()->with('user')->with('rfp_bundles')->withCount('projectRecords')->where('project_id', $request->id);
            
            $query = ProjectFiles::query()
            ->with(['user', 'rfp_bundles'])
            ->withCount([
                'projectRecords',
                'projectRecords as respondidos_ia_count' => function ($query) {
                    $query->where('status', 'respondido ia');
                },
                'projectRecords as respondidos_user_count' => function ($query) {
                    $query->where('status', 'respondido user');
                }
            ])
            ->where('project_id', $request->id);


            
            // Aplicar filtros
            if ($request->has('filter')) {
                foreach ($request->filter as $field => $value) {
                    if (!empty($value)) {
                        $query->where($field, 'like', '%' . $value . '%');
                    }
                }
            }

            // Aplicar ordenação
            if ($request->has('sort_by') && $request->has('sort_order')) {
                $sortBy = $request->sort_by;
                $sortOrder = $request->sort_order;

                if (in_array($sortBy, ['name', 'id', 'gestor','email', 'account_type', 'status', 'created_at']) && in_array($sortOrder, ['asc', 'desc'])) {
                    $query->orderBy($sortBy, $sortOrder);
                }
            }

            // Paginação
            $data = $query->paginate(40);          

            // Retornar dados em JSON
            return response()->json($data);
        }
    }


    public function updateInfos(Request $request, string $id)
    { 
        // Valida a Permissão do usuário
        if(Auth::user()->role->role_priority >= 90){          
            $data = htmlspecialchars(trim($request->rfp_date)); // Sanitiza o input
            $resultado = $this->validarEConverterData($data);

            if ($resultado) {
                $KnowledgeBase = KnowledgeBase::findOrFail($id);
                $KnowledgeBase->project = $request->project;
                $KnowledgeBase->rfp_date = $resultado;
                $KnowledgeBase->project_team = $request->project_team;
               
                try{
                    $KnowledgeBase->save();
                    // Retornar dados em JSON
                    return response()->json("success");    
                } catch (\Exception $e) {
                    $CatchError = json_decode($e->getMessage());
                }
                
            } else {
               
                return response()->json([
                    'message' => 'Data Inválida!',
                ], 422);
            }                
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function add()
    {

        $userId = auth()->user()->id;
        $data = [ 'userId' => $userId,];
    
        return view('project.create')->with($data);
    
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request){    
        try {
            $NewProject = new Project();
            $NewProject->name = $request->name;
            $NewProject->iduser_responsable = Auth::id();
            $NewProject->project_date = now();
            $NewProject->save();
            return redirect()->route('project.file', ['id' => $NewProject->id]);
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with('error', 'Ocorreu um erro ao processar a requisição!');
        }    
    }


    /**
     * Show the form for creating a new resource.
     */
    public function file(string $id)
    {
        $Project = Project::findOrFail($id);
        if($Project){
            if ($Project['iduser_responsable'] == Auth::id() || Auth::user()->role->role_priority >= 90){
                $Bundles = RfpBundle::all();
                $data = [ 'Project' => $Project, 'bundles' => $Bundles, 'userId' => Auth::id() ];            
                return view('project.files')->with($data);
            }else{
                return redirect()->back()->with('error', 'Você não tem permissão para ver esse projeto!');
            }
        }else{
            return redirect()->back()->with('error', 'Não encontramos esse projeto');
        }
    }


    /**
     * UPLOAD - Do arquivo da base de conhecimento
     */
    public function file_upload(Request $request, string $id) {

        $RfpBundle = RfpBundle::findOrFail($request->bundle);
        $ProjectData = Project::findOrFail($id);

        // Faz o upload para o S3 (Para BACKUP)
        $File = $request->file('file');
        $fileName = $request->name;
        $fileName = preg_replace('/[^\w\-_\.]/', '', $fileName); // Substitui caracteres não permitidos por "_"
        $fileName = trim($fileName, '_');
        $fileName = Str::slug($fileName).'_'.$File->hashName();;
        $filePath = 'cdn/projects/archives/'.$RfpBundle->bundle.'/'.$fileName;
        $UploadedFile = Storage::disk('s3')->put($filePath, file_get_contents($File));

        // Sobe o arquivo para historico e salva no BD
        $ProjectFile = new ProjectFiles();
        $ProjectFile->user_id = $ProjectData->iduser_responsable;
        $ProjectFile->bundle_id = $request->bundle;
        $ProjectFile->project_id = $id;
        $ProjectFile->filepath = $filePath;
        $ProjectFile->filename = $fileName;
        $ProjectFile->filename_original = $File->getClientOriginalName();
        $ProjectFile->file_extension = $File->getClientOriginalExtension();
        $ProjectFile->save();
        
        try {
            // Chama a importação do EXCEL
            $import = new ProjectRecordsImport($ProjectFile->id, $ProjectFile->bundle_id);
            // Executa a importação
            $Excel = Excel::import($import, $File);

            // Retornar a URL como resposta JSON
            return response()->json([
                'success' => true,
                'message' => 'Arquivo atualizado com sucesso!',
                'redirectUrl' => '/project/records/'.$ProjectFile->id,
            ]);
          
        } catch (ValidationException $e) {
            // Captura exceções de validação específicas do Maatwebsite Excel
            $failures = $e->failures();
    
            return response()->json([
                'message' => 'Erros durante a validação!',
                'failures' => $failures, // Detalhes das linhas com falhas
            ], 422);

        } catch (\Exception $e) {
            $CatchError = json_decode($e->getMessage());
           
            // Captura quaisquer outras exceções
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }
  }

    
   


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Encontrar o usuário pelo ID
        $Arquivo = KnowledgeBase::where('knowledge_base_id', $id)->first();
        if ($Arquivo['user_id'] == Auth::id() || Auth::user()->role->role_priority >= 90){
            if (Storage::exists($Arquivo->filepath)){
                if (Storage::delete($Arquivo->filepath)){
                    KnowledgeRecord::where('knowledge_base_id', $id)->delete();// 
                    KnowledgeBase::where('knowledge_base_id', $id)->delete();// Exclui o usuário do banco de dados
                    return redirect()->back()->with('success', 'Arquivo excluído com sucesso.');
                }else{
                    KnowledgeRecord::where('knowledge_base_id', $id)->delete();// 
                    KnowledgeBase::where('knowledge_base_id', $id)->delete();// Exclui o usuário do banco de dados
                    return redirect()->back()->with('error', 'Erro ao excluir arquivo.');
                }
            }else{
                return redirect()->back()->with('error', 'Arquivo não encontrado.');
            }
        } else {
            return redirect()->back()->with('error', 'Usuário sem permissão para excluir.');
        }
    }


    private function validarEConverterData($data) {
        // Formato esperado da entrada
        $formatoEntrada = 'd/m/Y';
        $formatoSaida = 'Y-m-d';
    
        // Tentar criar um objeto DateTime com base no formato esperado
        $objData = DateTime::createFromFormat($formatoEntrada, $data);
    
        // Verificar se a data é válida (cuidado com entradas como "30/02/2024" que são ajustadas automaticamente pelo DateTime!)
        if ($objData && $objData->format($formatoEntrada) === $data) {
            // Converte para o formato desejado
            return $objData->format($formatoSaida);
        }
    
        // Retorna false se a data for inválida
        return false;
    }




    public function cron2(Request $request)
    {
        try {
            $ProjectFiles = ProjectFiles::where('status', "em processamento")->get();
                foreach ($ProjectFiles as $File) {
                    $Records = ProjectRecord::whereNotNull('project_records.bundle_id')
                        ->where('project_records.project_file_id', $File->id)
                        ->where('project_records.status', "processando")
                        ->join('rfp_bundles', 'project_records.bundle_id', '=', 'rfp_bundles.bundle_id')
                        ->count();
            
                        if($Records == 0){
                            $File->status = 'processado';
                            $File->save();
                        }
                }
            Log::info("Executado com sucesso");
        } catch (\Exception $e) {
            Log::error("Erro no processamento: " . $e->getMessage());
        }
    }





    public function cron(Request $request)
    {
        try {
            $ProjectFiles = ProjectFiles::where('status', "em processamento")->get();
    
            $client = new Client([
                'base_uri' => $this->baseUrl,
                'timeout' => 30,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ],
            ]);

    
            $requests = function () use ($ProjectFiles, $client) {
                foreach ($ProjectFiles as $File) {
                    $Records = ProjectRecord::whereNotNull('project_records.bundle_id')
                        ->where('project_records.project_file_id', $File->id)
                        ->where('project_records.status', "processando")
                        ->join('rfp_bundles', 'project_records.bundle_id', '=', 'rfp_bundles.bundle_id')
                        ->get();
            
                    foreach ($Records as $Record) {
                        $Agent = Agent::where('id', $Record->agent_id)->first();
                        $Processo = RfpProcess::where('id', $Record->processo_id)->first();
            
                        $prompt = 'Você é uma IA avançada especializada em sistemas ERP da TOTVS, representando a equipe de engenharia da empresa. Seu objetivo é fornecer respostas técnicas precisas e claras sobre os produtos ERP da TOTVS, baseando-se na base de conhecimento fornecida.

                        Instruções de busca e resposta:

                        1. Ao receber uma pergunta, identifique o produto, o processo, a classificação e o requisito mencionados pelo usuário.
                        2. Realize a busca considerando tanto a coluna "DESCRIÇÃO DO REQUISITO" quanto a coluna "PROCESSO". Priorize correspondências que atendam tanto ao requisito quanto ao processo especificado.
                        3. Use uma busca case-insensitive e considere sinônimos ou termos relacionados tanto para o requisito quanto para o processo.
                        4. Se encontrar múltiplas correspondências, priorize as mais relevantes com base na similaridade com a pergunta do usuário e na correspondência do processo.
                        5. Ao analisar a aderência do produto ao requisito, considere as seguintes categorias:
                            - Atende: O produto atende completamente ao requisito no processo especificado.
                            - Atende Parcial: O produto atende parte do requisito ou atende em um processo relacionado.
                            - Customizável: O requisito pode ser atendido através de customizações para o processo específico.
                            - Não Atende: O produto não atende ao requisito no processo especificado.
                            - Desconhecido: Não há informações suficientes para determinar a aderência no processo especificado.
                        6. Forneça uma análise detalhada justificando como o produto atende ou não ao requisito no contexto do processo especificado. Inclua considerações sobre complexidade e frequência de uso, se relevante.
                        7. Se disponível, inclua informações da coluna OBSERVAÇÕES relacionadas ao requisito e processo encontrados.
                        8. Quando não encontrar uma correspondência exata para o requisito e processo, forneça a informação mais próxima disponível, indicando claramente que é uma aproximação e explicando as diferenças.
                        9. Se a pergunta for ambígua ou não houver informações suficientes na base de conhecimento sobre o requisito ou o processo, indique claramente essa limitação.
                        10. Estime a acuracidade de sua resposta em porcentagem, considerando tanto a correspondência do requisito quanto do processo, e explique brevemente como chegou a essa estimativa.
                        11. Mantenha sempre um tom profissional e técnico na resposta.
                        12. Não divulgue informações confidenciais ou detalhes técnicos sensíveis que possam comprometer a segurança dos sistemas.
                        13. Se necessário, sugira que o usuário entre em contato com o suporte técnico da TOTVS para informações mais detalhadas ou específicas sobre o requisito no contexto do processo mencionado.

                        Lembre-se: Sua prioridade é fornecer informações precisas e úteis, considerando sempre tanto o requisito quanto o processo especificado. Baseie-se estritamente nas informações disponíveis na base de conhecimento fornecida, dando ênfase à relação entre o requisito e o processo mencionado.';
                                                
                        $requisito = 'É possível fazer modificação dos rótulos dos campos do sistema através de configuração na própria ferramenta, sem necessidade de codificação?';
                        $processo = 'Configurações do sistema';

                        $body = [
                            'system' => $prompt,
                            'kbs' => [$Agent->knowledge_id],
                            'messages' => [
                                [
                                    'role' => 'user',
                                    'content' => json_encode([
                                        'requisito' => $requisito,
                                        'processo' => $processo
                                    ])
                                ]
                            ],
                            'responseFormat' => [
                                'type' => 'json_schema',
                                'schema' => $this->getSchema()
                            ]
                        ];                      
    
                        yield function () use ($client, $body, $Record) {
                            return $client->postAsync('/rest/completions', [
                                'json' => $body,
                                'headers' => ['Content-Type' => 'application/json'],
                            ])->then(function ($response) use ($Record) {
                                return ['response' => $response, 'record' => $Record];
                            });
                        };
                    }
                }
            };
    
            $pool = new Pool($client, $requests(), [
                'concurrency' => 5,
                'fulfilled' => function ($result, $index) {
                    $response = $result['response'];
                    $Record = $result['record'];
                    $data = json_decode($response->getBody(), true);
                
                    print_r($Record->requisito);

                    dd($data);

                    $DadosResposta = new ProjectAnswer;
                    $DadosResposta->bundle_id = $Record->bundle_id;
                    $DadosResposta->user_id = $Record->user_id;
                    $DadosResposta->requisito_id = $Record->id;
                    $DadosResposta->requisito = $Record->requisito;
    
                    $Answer = json_decode($data['output']['content']);
                  
    
                    $DadosResposta->aderencia_na_mesma_linha = $Answer->aderencia_na_mesma_linha ?? null;
                    $DadosResposta->linha_produto = $Answer->linha_produto ?? null;
                    $DadosResposta->resposta = $Answer->resposta ?? null;
                    $DadosResposta->referencia = $Answer->referencia ?? null;
                    $DadosResposta->observacao = $Answer->observacao ?? null;
                    $DadosResposta->acuracidade_porcentagem = $Answer->acuracidade_porcentagem ?? null;
                    $DadosResposta->acuracidade_explicacao = $Answer->acuracidade_explicacao ?? null;
                    $DadosResposta->save();
    
                    // Atualizar o status do Record
                    if($Answer->aderencia_na_mesma_linha != 'desconhecido'){
                        $Record->update(['status' => 'respondido ia']);
                        $Record->update(['project_answer_id' => $DadosResposta->id]);

                        $ProjectFile = ProjectFiles::where('id', $Record->project_id)->first();
                        if($ProjectFile->status == "processando"){
                            $ProjectFile->status = 'processado';
                            $ProjectFile->save();
                        }
        
                    }
                   
                    Log::info("Processamento de todos os arquivos concluído com sucesso");
                   

                },
                'rejected' => function ($reason, $index) {
                    Log::error("Request failed: " . $reason->getMessage());
                    // Você pode querer atualizar o status do Record aqui também
                },
            ]);
    
            Log::info("Executado com sucesso");
            // Executa o pool
            $promise = $pool->promise();
            $promise->wait();
    
            //return response()->json(['message' => 'Processamento concluído']);
        } catch (\Exception $e) {
            //dd($e->getMessage());
            Log::error("Erro no processamento: " . $e->getMessage());
            //return response()->json(['error' => 'Ocorreu um erro durante o processamento'], 500);
        }
    }






    // O método getSchema deve ser definido fora da classe MentoriaController para ser acessível
    function getSchema()
    {
        return [
            "name" => "answer",
            "description" => "Uma resposta que indica se o software atende o requisito.",
            "strict" => true,
            "parameters" => [
                "type" => "object",
                "additionalProperties" => false,
                "properties" => [
                    "aderencia_na_mesma_linha" => [
                        "type" => "string",
                        "description" => "Atende, Atende Parcial, Customizável, Não Atende ou Desconhecido. Caso não encontre uma referencia sempre traga como Desconhecido",
                        "enum" => ["Atende", "Atende Parcial", "Customizável", "Não Atende", "Desconhecido"]
                    ],
                    "linha_produto" => [
                        "type" => "string",
                        "description" => "Valor da coluna LINHA/PRODUTO para o requisito encontrado."
                    ],
                    "resposta" => [
                        "type" => "string",
                        "description" => "Análise e justificativa do porquê algum produto atende os requisitos. Pode incluir análise de complexidade e frequência na tabela."
                    ],
                    "modulo" => [
                        "type" => "string",
                        "description" => "Valor da coluna MÓDULO para o requisito encontrado."
                    ],
                    "referencia" => [
                        "type" => "string",
                        "description" => "Requisito encontrado como referência para a resposta. Deve trazer todas as informações sobre essa referência (nome do arquivo, linha, etc.). Caso não tenha encontrado, retorne todos os campos vazios.",
                    ],
                    "observacao" => [
                        "type" => "string",
                        "description" => "Valor da coluna OBSERVAÇÕES para o requisito encontrado, se houver."
                    ],
                    "acuracidade_porcentagem" => [
                        "type" => "string",
                        "description" => "Valor da acuracidade da resposta informada, em porcentagem",
                    ],
                    "acuracidade_explicacao" => [
                        "type" => "string",
                        "description" => "explicação do calculo que foi executado para chegar a respota da acuracidade_porcentagem.",
                    ]
                ],
                "required" => ["aderencia_na_mesma_linha", "linha_produto", "resposta", "modulo", "referencia", "observacao", "acuracidade_porcentagem", "acuracidade_explicacao"]
            ]
        ];
    }


}
