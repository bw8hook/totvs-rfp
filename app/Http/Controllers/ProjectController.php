<?php
namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\KnowledgeBase;
use App\Models\KnowledgeRecord;
use App\Models\RfpBundle;
use App\Models\Agent;
use App\Models\ProjectAnswer;
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
        if(Auth::user()->role->role_priority >= 90){  
            //$AllFiles = Project::withCount('records')->get();
            $AllProject = Project::withCount('records')
                ->where('iduser_responsable', Auth::id()) // Adicione sua condição aqui
                ->get();
            $AllFiles = ProjectFiles::withCount('records')
                ->where('user_id', Auth::id()) // Adicione sua condição aqui
                ->get();
            
                //$AllFiles = ProjectFiles::withCount('records')->get();

            // Último atualizado
            
            $lastUpdated = ProjectFiles::where('user_id', Auth::id())->orderBy('updated_at', 'desc')->first();
            if ($lastUpdated) {
                $lastUpdatedDate = Carbon::parse($lastUpdated->updated_at)->format('d/m/Y'); // Apenas o dia
                $lastUpdatedTime = Carbon::parse($lastUpdated->updated_at)->format('H\hi');  // Apenas a hora
            } else {
                $lastUpdatedDate = null; // Ou algum valor padrão
                $lastUpdatedTime = null; // Ou algum valor padrão
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


            $resultados = DB::table(table: 'project_records')
            ->leftJoin('rfp_bundles', 'project_records.bundle_id', '=', 'rfp_bundles.bundle_id') // INNER JOIN
            ->select('project_records.bundle_id', 'rfp_bundles.bundle',  DB::raw('COUNT(*) as total'))
            ->where('project_records.user_id',  Auth::id()) // Filtra pelo ID do usuário
            ->groupBy('project_records.bundle_id') // Agrupa pelo ID do bundle
            ->groupBy('rfp_bundles.bundle') // Agrupa pelo ID do bundle
            ->get();
        
            $CountResultado = 0;
            $CountPacotes = 0;
            //Exibindo o resultado
            foreach ($resultados as $resultado) {
                $CountPacotes++;
                $CountResultado = $CountResultado + $resultado->total;
            }

            $data = array(
                'title' => 'Todos Arquivos',
                'lastUpdated' => $lastUpdated,
                'lastUpdatedDate' => $lastUpdatedDate,
                'lastUpdatedTime' => $lastUpdatedTime,
                'ListFiles' => $ListFiles,
                'CountProject' => $AllProject->count(),
                'CountRFPs' => $CountRFPs,
                'CountPacotes' => $CountPacotes,
                'CountRequisitos' => $CountRequisitos,
                'CountNotAnswer' => $CountNotAnswer,
                'CountAnswerUser' => $CountAnswerUser,
                'CountAnswerIA' =>  $CountAnswerIA,
            );
    
            return view('project.list')->with($data);
        }

    }


    public function filter(Request $request)
    { 
        // Valida a Permissão do usuário
        if(Auth::user()->role->role_priority >= 90){       
            $query = Project::query()->with('user')->withCount(relations: 'records');


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



    public function detail(string $id)
    { 
        if(Auth::user()->role->role_priority >= 90){  
            $Detail = Project::with('user')->find($id);
            $AllFiles = ProjectFiles::where('project_id', $id)->withCount('records')->get();


            // Último atualizado
            $lastUpdated = ProjectFiles::where('user_id', Auth::id())->orderBy('updated_at', 'desc')->first();
            if ($lastUpdated) {
                $lastUpdatedDate = Carbon::parse($lastUpdated->updated_at)->format('d/m/Y'); // Apenas o dia
                $lastUpdatedTime = Carbon::parse($lastUpdated->updated_at)->format('H\hi');  // Apenas a hora
            } else {
                $lastUpdatedDate = null; // Ou algum valor padrão
                $lastUpdatedTime = null; // Ou algum valor padrão
            }

            $ListFiles = array();
            $CountRFPs = 0;
            $CountRequisitos = 0;
            $CountAnswerUser = 0;
            $CountAnswerIA = 0;
            $CountNotAnswer = 0;

            $ListProducts = array();

            foreach ($AllFiles as $key => $File) {
                    // print_r($File->id);
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

            $resultados = DB::table(table: 'project_records')
            ->leftJoin('rfp_bundles', 'project_records.bundle_id', '=', 'rfp_bundles.bundle_id') // INNER JOIN
            ->select('project_records.bundle_id', 'rfp_bundles.bundle',  DB::raw('COUNT(*) as total'))
            ->where('project_records.user_id',  Auth::id()) // Filtra pelo ID do usuário
            ->groupBy('project_records.bundle_id') // Agrupa pelo ID do bundle
            ->groupBy('rfp_bundles.bundle') // Agrupa pelo ID do bundle
            ->get();
        
            $CountResultado = 0;
            $CountPacotes = 0;
            //Exibindo o resultado
            foreach ($resultados as $resultado) {
                $CountPacotes++;
                $CountResultado = $CountResultado + $resultado->total;
            }


            $data = array(
                'Detail' => $Detail,
                'lastUpdated' => $lastUpdated,
                'lastUpdatedDate' => $lastUpdatedDate,
                'lastUpdatedTime' => $lastUpdatedTime,
                'ListFiles' => $ListFiles,
                'ListProducts' => $ListProducts,
                'CountRFPs' => $CountRFPs,
                'CountPacotes' => $CountPacotes,
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
            $query = ProjectFiles::query()->with('user')->with('rfp_bundles')->withCount('projectRecords')->where('project_id', $request->id);
            
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
        //$content = Storage::disk('s3')->temporaryUrl($path,now()->addMinutes(10));

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
            $import = new ProjectRecordsImport($ProjectFile->id);
            // Executa a importação
            $Excel = Excel::import($import, $File);

            // Retornar a URL como resposta JSON
            return response()->json([
                'success' => true,
                'message' => 'Arquivo atualizado com sucesso!',
                'redirectUrl' => '/project/records/'.$ProjectFile->id,
            ]);

                // // Obter os dados atualizados
                // $updatedData = $import->getUpdatedRows();

                // // Gerar o arquivo Excel e salvar no storage temporário
                // $fileName = 'planilha-respondida-' . time() . '.xlsx';
                // $filePath = '/public/temp/' . $fileName;

                // Excel::store(new NewProjectExport($updatedData), $filePath, 'local');

                // // Gerar a URL para download
                // $url = Storage::url($filePath);
                // $NewUrl = str_replace("/storage//public", "", $url);

                // // Cria uma nova instância do modelo
                // $project = new RfpProject();
                // $project->user_id = Auth::id();
                // $project->title = 'Novo Projeto';
                // $project->description = 'Descrição detalhada do projeto';
                // $project->answered = $import->updatedCount;
                // $project->unanswered = $import->NotUpdatedCount;
                // $project->filename_original = $File->getClientOriginalName();
                // $project->filepath = 'https://totvs.bw8.tech/storage/'.$NewUrl;
                // $project->filename = $fileName;
                // $project->file_extension = '.xlsx';
                // $project->save();
                // // Pega o ID inserido
                // $insertedId = $project->id;            
    
             // Acessar a URL gerada dentro da classe de importação
            $MensagemErro = $import->Erros;

            //return response()->json(['success' => true, 'redirectUrl' => '/import/'.$KnowledgeBaseDataid]);
          
        } catch (ValidationException $e) {
            // Captura exceções de validação específicas do Maatwebsite Excel
            $failures = $e->failures();
    
            return response()->json([
                'message' => 'Erros durante a validação!',
                'failures' => $failures, // Detalhes das linhas com falhas
            ], 422);

        } catch (\Exception $e) {
            $CatchError = json_decode($e->getMessage());

            dd($e);

            $InsertError = KnowledgeError::create([
                'error_code' => 'ERR003',
                'error_message' => $CatchError->error_message,
                'error_data' => json_encode(value: $CatchError->error_data),
                'user_id' => Auth::id(), // Associar ao usuário logado, se necessário
            ]);

            $InsertErrorID = $InsertError->id;
    
            // Remove a Base Enviada
            // if ($KnowledgeBaseDataid) {
            //     DB::table('knowledge_base')->where('knowledge_base_id', $KnowledgeBaseDataid)->delete();
            // }

            // Captura quaisquer outras exceções
            return response()->json([
                'message' => 'Erro durante a importação!',
                'redirectUrl' => '/import/erro/'.$InsertErrorID
            ], 500);
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


    public function cron(Request $request)
    {
    
}



    public function cronBackup(Request $request){
        $ProjectFiles = ProjectFiles::where('status', "em processamento")->get();
     
        if ($ProjectFiles->count() > 0) {
            foreach ($ProjectFiles as $File) {
                try {
                    $Records = ProjectRecord::whereNotNull('project_records.bundle_id') // Garante que bundle_id está preenchido
                    ->where('project_records.project_file_id', $File->id) // Filtra apenas os registros da base específica
                    ->where('project_records.status', "processando") // Filtra apenas os registros da base específica
                    ->join('rfp_bundles', 'project_records.bundle_id', '=', 'rfp_bundles.bundle_id') // Faz o JOIN corretamente
                    ->get();
                            
                    if (!$Records->isEmpty()) {
                        foreach ($Records as $key => $Record) {

                            $Agent = Agent::where('id', $Record->agent_id)->first();
                            $Module = Module::where('id', $Record->classificacao_id)->first();


                            $dados = [
                                'agent_id' => $Agent->agent_id,
                                'knowledge_id' => $Agent->knowledge_id,
                                'requisito' => $Record->requisito,
                                'modulo' => $Module->module_name,
                                'registro' => $Record
                            ];
                            
                            $mentoria = new MentoriaController();
                            $Resposta = $mentoria->getAnswer($dados);

                            //dd($Resposta);

                            //ProjectAnswer::
                            if($Resposta){

                                // // Remove % caso tenha
                                // $acuracidade = $Resposta->acuracidade_porcentagem;
                                // $acuracidade = trim(str_replace('%', '', $acuracidade));
                                // if (is_numeric($acuracidade)) {
                                //     $acuracidade = floatval($acuracidade) . '%';
                                // } else {
                                //     $acuracidade = '0%'; 
                                // }
                            

                                $DadosResposta = new ProjectAnswer;
                                $DadosResposta->bundle_id = $Record->bundle_id;
                                $DadosResposta->user_id = $Record->user_id;
                                $DadosResposta->requisito_id = $Record->id;
                                $DadosResposta->requisito = $Record->requisito;
                                $DadosResposta->aderencia_na_mesma_linha = $Resposta->aderencia_na_mesma_linha;
                                $DadosResposta->linha_produto = $Resposta->linha_produto;
                                $DadosResposta->resposta = $Resposta->resposta;
                                $DadosResposta->referencia = $Resposta->referencia;
                                $DadosResposta->observacao = $Resposta->observacao;
                                $DadosResposta->acuracidade_porcentagem = $Resposta->acuracidade_porcentagem;
                                $DadosResposta->acuracidade_explicacao = $Resposta->acuracidade_explicacao;
                                $DadosResposta->save();
                            }
                        }

                        dd('resposta');
                    }

                    $File->status = 'processado';
                    $$File->save();

                } catch (\Exception $e) {
                    dd($e);
                }
            }
        }
    }

    /**
    * UPLOAD - Do arquivo da base de conhecimento
    */
    public function cron2(Request $request) {
        
$ProjectFiles = ProjectFiles::where('status', "em processamento")->get();

if ($ProjectFiles->count() > 0) {
    foreach ($ProjectFiles as $File) {
        try {
            $Records = ProjectRecord::whereNotNull('project_records.bundle_id')
                ->where('project_records.project_file_id', $File->id)
                ->where('project_records.status', "processando")
                ->join('rfp_bundles', 'project_records.bundle_id', '=', 'rfp_bundles.bundle_id')
                ->get();

            if (!$Records->isEmpty()) {
                // Divida os registros em blocos de 20
                $chunks = $Records->chunk(20);

                foreach ($chunks as $chunk) {
                    $client = new Client([
                        'base_uri' => 'https://api.conteudo.rdstationmentoria.com.br/rest',
                        'headers' => [
                            'Authorization' => 'Bearer eyJhbGciOiJSUzUxMiIsInR5cCI6IkpXVCJ9.eyJ0eXBlIjoiYXBpIiwicHJvcGVydGllcyI6eyJrZXlJZCI6ImtleV8wMUpGMEhQWEc1SkRSNTlBUzU2OFgwVjk3WSIsIndvcmtzcGFjZUlkIjoid3BjXzAxSjVCSFdCNTRFSldDRE42QVFZMlg2NUo3IiwidXNlcklkIjoidXNlcl9hcGkifSwiaWF0IjoxNzM0MTExNjIyfQ.cLjsIB85bybra-rQOTAI-GLuIQKeQP95HLdXu-JG1yxMbrdzHwjqLGl8xzo3aVwz94uD3mWaOhajdqync0CCusVM_VF3dEsg2bRd9OM02HMD-rxil360HClB--5zYKOW7NZUPKmj0Q8rl-1v-aE4lFes6U7-_zB1gJWiGTLR9HLuZd3E5EsSqxu_mS49ss5tAFHQYrVotns6Ug5OGmxSgJ-IqlluVMPRbI8dtSb0ZsiYHe_xYtaERhTInevjaqgHbhZwLzyHg50R7MMoJLsGlw8CD3KRfcitxj8NZmilKK4vCkjm4dN5QYTuRxSqpgnGRtCCk-3fR0q8N3GwYF4oWQ',
                            'Accept' => 'application/json',
                        ],
                    ]);

                    $requests = function ($chunk) use ($client) {
                        foreach ($chunk as $Record) {
                            yield function () use ($client, $Record) {
                                $mentoria = new MentoriaController();
                                $Agent = Agent::where('id', $Record->agent_id)->first();
                                $Module = Module::where('id', $Record->classificacao_id)->first();

                                $dados = [
                                    'agent_id' => $Agent->agent_id,
                                    'knowledge_id' => $Agent->knowledge_id,
                                    'requisito' => $Record->requisito,
                                    'modulo' => $Module->module_name,
                                    'registro' => $Record
                                ];

                                // Lógica do método getAnswer encapsulada aqui
                                try {
                                    $body = [
                                        'system' => 'Você é uma IA avançada representando o time de engenharia da TOTVS. Seu objetivo é responder dúvidas técnicas sobre os sistemas ERP da TOTVS, com base em uma base de conhecimento consolidada e a tabela que pode acessar. Sua prioridade é fornecer respostas claras e precisas, indicando se o sistema atende plenamente, parcialmente ou não possui capacidade para atender a necessidade apresentada. Sempre mantenha um tom profissional e indique limitações ou a necessidade de suporte humano quando aplicável. O usuário irá perguntar sobre algum produto e se ele atende ou não a um requisito. Você deve procurar na tabela algum requisito que atende ao produto, classificação e requisito que o usuário pedir. Você pode usar a coluna "DESCRIÇÃO DO REQUISITO" que tem requisitos, buscando de forma case-insensitive por algo adequado ao que o usuário pedir. Caso não encontre oque foi solicitado, em todas as repostas traga como DESCONHECIDO. Sempre traga respostas curtas e diretas com tom profissional.',
                                        'kbs' => [$dados['knowledge_id']],
                                        'messages' => [
                                            [
                                                'role' => 'user',
                                                'content' => $dados['requisito']
                                            ]
                                        ],
                                        'responseFormat' => [
                                            'type' => 'json_schema',
                                            'schema' => $this->getSchema()
                                        ]
                                    ];

                                    $response = Http::withHeaders([
                                        'Authorization' => "Bearer {$this->apiKey}",
                                        'Content-Type' => 'application/json',
                                    ])->post("{$this->baseUrl}/completions", $body);

                                    if ($response->failed()) {
                                        throw new \Exception("HTTP Error: {$response->status()}");
                                    }

                                    $data = $response->json();
                                    $Resposta = json_decode($data['output']['content']);

                                    // Salvar a resposta no banco de dados
                                    $DadosResposta = new ProjectAnswer;
                                    $DadosResposta->bundle_id = $Record->bundle_id;
                                    $DadosResposta->user_id = $Record->user_id;
                                    $DadosResposta->requisito_id = $Record->id;
                                    $DadosResposta->requisito = $Record->requisito;
                                    $DadosResposta->aderencia_na_mesma_linha = $Resposta->aderencia_na_mesma_linha;
                                    $DadosResposta->linha_produto = $Resposta->linha_produto;
                                    $DadosResposta->resposta = $Resposta->resposta;
                                    $DadosResposta->referencia = $Resposta->referencia;
                                    $DadosResposta->observacao = $Resposta->observacao;
                                    $DadosResposta->acuracidade_porcentagem = $Resposta->acuracidade_porcentagem;
                                    $DadosResposta->acuracidade_explicacao = $Resposta->acuracidade_explicacao;
                                    $DadosResposta->save();
                                    
                                    $Record->project_answer_id = $DadosResposta->id;
                                    $Record->save();

                                    return new \GuzzleHttp\Promise\FulfilledPromise($Resposta);
                                } catch (\Exception $e) {
                                    Log::error("Failed to process request. Error: {$e->getMessage()}");
                                    return new \GuzzleHttp\Promise\RejectedPromise($e);
                                }
                            };
                        }
                    };

                    $pool = new Pool($client, $requests($chunk), [
                        'concurrency' => 20,
                        'fulfilled' => function ($response, $index) {
                            // Esta função é chamada quando uma tarefa é bem-sucedida
                        },
                        'rejected' => function ($reason, $index) {
                            // Esta função é chamada quando uma tarefa falha
                            Log::error("Erro: " . $reason->getMessage());
                        },
                    ]);

                    // Aguarde todas as tarefas serem concluídas
                    $promise = $pool->promise();
                    $promise->wait();
                }

                dd('resposta');
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }
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
                "required" => ["aderencia_na_mesma_linha", "linha_produto", "resposta", "referencia", "observacao", "acuracidade_porcentagem", "acuracidade_explicacao"]
            ]
        ];
    }


}
