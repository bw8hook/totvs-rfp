<?php
namespace App\Http\Controllers;
use App\Imports\DownloadAnsweredProjectImport;
use App\Models\ProjectDownloadHistory;
use App\Models\Segments;
use App\Models\Type;
use Carbon\Carbon;
use App\Models\KnowledgeBase;
use App\Models\KnowledgeRecord;
use App\Models\RfpBundle;
use App\Models\Category;
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
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use App\Http\Controllers\MentoriaController;
use App\Models\LineOfProduct;
use App\Models\Module;
use App\Models\ServiceGroup;
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
        if (Auth::user()->hasAnyPermission(['projects.all', 'projects.my', 'projects.all.manage',  'projects.all.add', 'projects.all.edit', 'projects.all.delete', 'projects.my.manage', 'projects.my.add', 'projects.my.edit', 'projects.my.delete'])) {
            // Validação se mostra Tudo ou apenas do usuário
            if (Auth::user()->hasAnyPermission(['projects.all', 'projects.all.manage',  'projects.all.add', 'projects.all.edit', 'projects.all.delete'])) {

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
            }else if (Auth::user()->hasAnyPermission(['projects.my', 'projects.my.manage', 'projects.my.add', 'projects.my.edit', 'projects.my.delete'])) {
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
        }else{
            return redirect()->back()->with('error', 'Você não tem permissão para acessar essa página.');
        }
    }


    public function filter(Request $request)
    { 
        if (Auth::user()->hasAnyPermission(['projects.all', 'projects.my', 'projects.all.manage',  'projects.all.add', 'projects.all.edit', 'projects.all.delete', 'projects.my.manage', 'projects.my.add', 'projects.my.edit', 'projects.my.delete'])) {
            // Validação se mostra Tudo ou apenas do usuário
            if (Auth::user()->hasAnyPermission(['projects.all', 'projects.all.manage',  'projects.all.add', 'projects.all.edit', 'projects.all.delete'])) { 
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
        }else{
            return redirect()->back()->with('error', 'Você não tem permissão para acessar essa página.');
        }
    }



    public function detail(string $id)
    { 
        $Detail = Project::with('user')->find($id);
        if (Auth::user()->hasAnyPermission(['projects.all', 'projects.my', 'projects.all.manage',  'projects.all.add', 'projects.all.edit', 'projects.all.delete', 'projects.my.manage', 'projects.my.add', 'projects.my.edit', 'projects.my.delete'])) {
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

                    $ListProducts[] = $ListFile['bundle'];
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
        }else{
            return redirect()->back()->with('error', 'Você não tem permissão para acessar essa página.');
        }

    }


    public function filterDetail(Request $request)
    { 
        // Valida a Permissão do usuário
        if (Auth::user()->hasAnyPermission(['projects.all', 'projects.my', 'projects.all.manage',  'projects.all.add', 'projects.all.edit', 'projects.all.delete', 'projects.my.manage', 'projects.my.add', 'projects.my.edit', 'projects.my.delete'])) {
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
        }else{
            return redirect()->back()->with('error', 'Você não tem permissão para acessar essa página.');
        }
    }


    public function updateInfos(Request $request, string $id)
    { 
        // Valida a Permissão do usuário
        if (Auth::user()->hasAnyPermission(['projects.all', 'projects.my', 'projects.all.manage', 'projects.all.edit', 'projects.my.manage', 'projects.my.edit'])) {     
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
        }else{
            return redirect()->back()->with('error', 'Você não tem permissão para acessar essa página.');
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function add()
    {   
        if (Auth::user()->hasAnyPermission(['projects.all', 'projects.my', 'projects.all.manage', 'projects.all.add', 'projects.my.manage', 'projects.my.add'])) {     
            $userId = Auth::id();
            $data = [ 'userId' => $userId];
        
            return view('project.create')->with($data);
        }else{
            return redirect()->back()->with('error', 'Você não tem permissão para acessar essa página.');
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request){    
        if (Auth::user()->hasAnyPermission(['projects.all', 'projects.my', 'projects.all.manage', 'projects.all.add', 'projects.my.manage', 'projects.my.add'])) {     
            try {
                $NewProject = new Project();
                $NewProject->name = $request->name;
                $NewProject->iduser_responsable = Auth::id();
                $NewProject->project_date = now();
                $NewProject->save();
                return redirect()->route('project.file', ['id' => $NewProject->id]);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Ocorreu um erro ao processar a requisição!');
            }  
        }else{
            return redirect()->back()->with('error', 'Você não tem permissão para acessar essa página.');
        }  
    }


    /**
     * Show the form for creating a new resource.
     */
    public function file(string $id)
    {
        $Project = Project::findOrFail($id);
        if($Project){
            if (Auth::user()->hasAnyPermission(['projects.all', 'projects.my', 'projects.all.manage', 'projects.all.add', 'projects.my.manage', 'projects.my.add'])) {     
                $Bundles = RfpBundle::with(['agent', 'category', 'type', 'lineOfProduct', 'serviceGroup', 'segments'])->get(); 

                $lines = LineOfProduct::where('status', 'ativo')->get();

                $types = Type::all();
                $segments = Segments::all();
                $categorys = Category::all();
                $services = ServiceGroup::all();
                $agents = Agent::all();

                $data = [ 'Project' => $Project, 'lines' => $lines, 'bundles' => $Bundles, 'types' => $types,  'segments' => $segments, 'categorys' => $categorys, 'services' => $services, 'agents' => $agents,   'userId' => Auth::id() ];            
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

        //$RfpBundle = RfpBundle::findOrFail($request->bundle);
        $ProjectData = Project::findOrFail($id);

        // Faz o upload para o S3 (Para BACKUP)
        $File = $request->file('file');
        $fileName = $request->name;
        $fileName = preg_replace('/[^\w\-_\.]/', '', $fileName); // Substitui caracteres não permitidos por "_"
        $fileName = trim($fileName, '_');
        $fileName = Str::slug($fileName).'_'.$File->hashName();;
        $filePath = 'cdn/projects/archives/general/'.$fileName;
        $UploadedFile = Storage::disk('s3')->put($filePath, file_get_contents($File));

        // Sobe o arquivo para historico e salva no BD
        $ProjectFile = new ProjectFiles();
        $ProjectFile->user_id = $ProjectData->iduser_responsable;
        //$ProjectFile->bundle_id = $request->bundle;
        $ProjectFile->project_id = $id;
        $ProjectFile->filepath = $filePath;
        $ProjectFile->filename = $fileName;
        $ProjectFile->filename_original = $File->getClientOriginalName();
        $ProjectFile->file_extension = $File->getClientOriginalExtension();
        $ProjectFile->save();

        if ($request->has('bundles')) {
            $ProjectFile->bundles()->attach($request->bundles);
        }

        try {
            // Chama a importação do EXCEL
            $import = new ProjectRecordsImport($ProjectFile->id, $request->bundles);
            // Executa a importação
            $Excel = Excel::import($import, $File);

            // Retornar a URL como resposta JSON
            return response()->json([
                'success' => true,
                'excel' => $Excel,
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
        $Arquivo = ProjectFiles::where('id', $id)->first();
        if (Auth::user()->hasAnyPermission(['projects.all', 'projects.my', 'projects.all.manage', 'projects.all.delete', 'projects.my.manage', 'projects.my.delete'])) {     
            if (Storage::exists($Arquivo->filepath)){
                if (Storage::delete($Arquivo->filepath)){
                    ProjectRecord::where('project_file_id', $id)->delete();// 
                    ProjectFiles::where('id', $id)->delete();// Exclui o usuário do banco de dados
                    return redirect()->back()->with('success', 'Arquivo excluído com sucesso.');
                }else{
                    ProjectRecord::where('project_file_id', $id)->delete();// 
                    ProjectFiles::where('id', $id)->delete();// Exclui o usuário do banco de dados
                    return redirect()->back()->with('success', 'Arquivo excluído com sucesso.');
                }
            }else{
                ProjectRecord::where('project_file_id', $id)->delete();// 
                ProjectFiles::where('id', $id)->delete();// Exclui o usuário do banco de dados
                return redirect()->back()->with('success', 'Arquivo excluído com sucesso.');
            }
        } else {
            return redirect()->back()->with('error', 'Usuário sem permissão para excluir.');
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroyFile(string $id)
    {
        // Encontrar o usuário pelo ID
        $Arquivo = ProjectFiles::where('id', $id)->first();
        if (Auth::user()->hasAnyPermission(['projects.all', 'projects.my', 'projects.all.manage', 'projects.all.delete', 'projects.my.manage', 'projects.my.delete'])) {     
            if (Storage::exists($Arquivo->filepath)){
                if (Storage::delete($Arquivo->filepath)){
                    ProjectRecord::where('project_file_id', $id)->delete();// 
                    ProjectFiles::where('id', $id)->delete();// Exclui o usuário do banco de dados
                    return redirect()->back()->with('success', 'Arquivo excluído com sucesso.');
                }else{
                    ProjectRecord::where('project_file_id', $id)->delete();// 
                    ProjectFiles::where('id', $id)->delete();// Exclui o usuário do banco de dados
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
                    $Records = ProjectRecord::where('project_records.project_file_id', $File->id)
                        ->where('project_records.status', "processando")
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



    

    public function projectExport(Request $request, $id)
    {
        Log::info('Iniciando o processamento da base de conhecimento');
        try {
            $ProjectFile = ProjectFiles::where('id', $id)
            ->with('bundles')
            ->first();

            if($ProjectFile->status == 'processado' || $ProjectFile->status == 'concluído' ){
                try {

                    if($ProjectFile->status == 'concluído'){
                         // Baixar arquivo do S3 para processamento local
                        $tempInputFile = tempnam(sys_get_temp_dir(), 'excel');
                        $contents = Storage::disk('s3')->get($ProjectFile->filepath);
                        file_put_contents($tempInputFile, $contents);

                        // Criar instância de importação
                        $import = new DownloadAnsweredProjectImport($ProjectFile->id);

                        // Importar arquivo
                        Excel::import($import, $tempInputFile, null, \Maatwebsite\Excel\Excel::XLSX);

                    }else{
                        $ProjectRecords = ProjectRecord::where('project_file_id', $ProjectFile->id)
                        ->with('answers')
                        ->orderBy('id')
                        ->get();


                        // Criar planilha
                        $spreadsheet = new Spreadsheet();
                        $sheet = $spreadsheet->getActiveSheet();

                        // Adicionar cabeçalhos
                        $headers = [
                            'PROCESSO', 
                            'SUBPROCESSO', 
                            'DESCRIÇÃO DO REQUISITO',
                            'RESPOSTA',
                            'MÓDULOS',
                            'PRODUTO PRINCIPAL',
                            'OBSERVAÇÕES',
                            'PRODUTOS ADICIONAIS'
                        ];

                        // Adicionar cabeçalhos
                        foreach ($headers as $col => $header) {
                            $sheet->setCellValueByColumnAndRow($col + 1, 1, $header);
                        }

                        // Adicionar dados
                        $rowNumber = 2;
                        foreach ($ProjectRecords as $record) {
                            $sheet->setCellValueByColumnAndRow(1, $rowNumber, $record->processo);
                            $sheet->setCellValueByColumnAndRow(2, $rowNumber, $record->subprocesso);
                            $sheet->setCellValueByColumnAndRow(3, $rowNumber, $record->requisito);
                            $sheet->setCellValueByColumnAndRow(4, $rowNumber, $record->answers->aderencia_na_mesma_linha);
                            $sheet->setCellValueByColumnAndRow(5, $rowNumber, $record->answers->modulo);
                            $sheet->setCellValueByColumnAndRow(6, $rowNumber, $record->answers->linha_produto);
                            $sheet->setCellValueByColumnAndRow(7, $rowNumber, $record->answers->observacao);
                            $sheet->setCellValueByColumnAndRow(8, $rowNumber, '');

                            // Adicionar outros campos...
                            $rowNumber++;
                        }

                        // Método alternativo de escrita
                        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                        
                        // Criar caminho do arquivo
                        $tempFile = tempnam(sys_get_temp_dir(), 'import_');
                        $tempFile = $tempFile . '.xlsx';
                        
                        // Tentar salvar
                        $writer->save($tempFile);


                        // $dataArray = $ProjectRecords->map(function($record) {
                        //     return [
                        //         'PROCESSO' => $record->processo,
                        //         'SUBPROCESSO' => $record->subprocesso,
                        //         'DESCRIÇÃO DO REQUISITO' => $record->requisito,
                        //         'RESPOSTA' => optional($record->answers)->aderencia_na_mesma_linha,
                        //         'MÓDULOS' => $record->answers->modulo,
                        //         'PRODUTO PRINCIPAL' => $record->answers->linha_produto,
                        //         'OBSERVAÇÕES' => $record->answers->observacao,
                        //         'PRODUTOS ADICIONAIS' => ''
                        //     ];
                        // })->toArray();

                        // Criar instância de importação
                        $import = new DownloadAnsweredProjectImport($ProjectFile->id);
                        

                        // Importar arquivo
                        Excel::import($import, $tempFile);
                    }                    

                   

                    // Salvar arquivo processado
                    $tempOutputFile = tempnam(sys_get_temp_dir(), 'excel') . '.xlsx';
                    
                    // Usar IOFactory para criar writer
                    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($import->getSpreadsheet(), 'Xlsx');
                    $writer->save($tempOutputFile);
                    $FileName = $ProjectFile->id.'_'.$ProjectFile->status.'_'.time() . '_' . uniqid() . '.xlsx';
            
                    if($ProjectFile->status == 'concluído' ){
                        // Enviar arquivo processado para S3
                       
                        $outputFilePath = 'cdn/projects_answereds/'.$FileName;
                        Storage::disk('s3')->put(
                            $outputFilePath, 
                            file_get_contents($tempOutputFile)
                        );

                        // Gerar URL completa
                        $urlCompleta = Storage::disk('s3')->url($outputFilePath);
                        $ProjectFile->answered_file = $urlCompleta;
                        $ProjectFile->save();
                        //Opcional: Excluir arquivo temporário
                        if (file_exists($tempOutputFile)) {
                            unlink($tempOutputFile);
                        }
                    }else{

                        // Registrar um download
                        $download = ProjectDownloadHistory::recordDownload( Auth::id(),  $ProjectFile->id, $FileName);

                        // Preparar download
                        return response()->download(
                            $tempOutputFile, 
                            $FileName,
                            [
                                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'Content-Disposition' => 'attachment; filename="arquivo_processado.xlsx"'
                            ]
                        )->deleteFileAfterSend(true);
                    }
                    
                   
                   

                } catch (\Exception $e) {

                    dd($e);


                    Log::error('Erro no processamento do Excel', [
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);

                    return 'Erro no processamento';
                }        
            }
        } catch (\Exception $e) {
            Log::error("Erro: " . $e->getMessage());
        }
          
        Log::info('Finalizando o processamento da base de conhecimento'); // Adiciona log aqui
    }











    public function cron(Request $request)
    {
        try {
            //$ProjectFiles = ProjectFiles::where('status', "em processamento")

            $ProjectFiles = ProjectFiles::where('id', "18")
            ->with('bundles')
            ->orderBy('id', 'asc')
            ->get();
            
            $clientHookIA = new Client([
                'base_uri' => 'https://totvs-ia.hook.app.br/v1/',
                'timeout' => 60,
                'headers' => [
                    'Authorization' => 'Bearer app-y133Gvf5qZvY8yM3gyojkOzR',
                    'Accept' => 'application/json',
                ],
            ]);

            $requestsHook = function () use ($ProjectFiles, $clientHookIA) {
                foreach ($ProjectFiles as $File) {
                    // Pega os IDs dos bundles vinculados
                    $bundleIds = $File->bundles->pluck('bundle_id')->toArray();

                    // Busca os dados completos dos RfpBundles
                    $bundles = RfpBundle::whereIn('bundle_id', $bundleIds)->get();

                    // Primeiro pega os agent_ids dos bundles
                    $agentIds = $bundles->pluck('agent_id')->unique()->toArray();

                    // Depois busca os agents
                    $agents = Agent::whereIn('id', $agentIds)->get();

                    if($agents[0]->search_engine == "Open IA"){

                        $Records = ProjectRecord::where('project_records.project_file_id', $File->id)
                            ->where('project_records.status', "aguardando")
                            ->orderBy('id', 'asc')
                            ->get();

                        foreach ($Records as $Record) {

                            //$Agent = Agent::where('id', $Record->agent_id)->first();
                            $Processo = RfpProcess::with('rfpBundles')->where('id', $Record->processo_id)->first();
                            $BundlesProcess = $Processo->rfpBundles;

                            $ProdutosPrioritarios = '';
                            $ListaAgentesPrioritarios = '';
                            $FiltroProdutos = [];
                            $FiltroAgentes = [];
                            foreach ($BundlesProcess as $bundleProcess) {
                                $DadosAgentePrioritario = Agent::where('id', $bundleProcess->agent_id)->first();

                                $FiltroProdutos[] = $bundleProcess->bundle;
                                $FiltroAgentes[] = $DadosAgentePrioritario->knowledge_id_hook;
                                // Se a string estiver vazia, adicione direto
                                if (empty($ProdutosPrioritarios)) {
                                    $ListaAgentesPrioritarios = $DadosAgentePrioritario->knowledge_id_hook;
                                    $ProdutosPrioritarios = $bundleProcess->bundle; // ou outro campo que queira
                                } else {
                                    // Se já tiver conteúdo, adicione com vírgula
                                    $ListaAgentesPrioritarios .= ', ' . $DadosAgentePrioritario->knowledge_id_hook;
                                    $ProdutosPrioritarios .= ', ' . $bundleProcess->bundle;
                                }
                            }

                            //$ProdutosPrioritarios = $Records->rfpBundles->pluck('bundle')->implode(', ');
                            $ProdutosAdicionais = $bundles->pluck('bundle')->unique()->implode(', ');

                            $agentIds = $bundles->pluck('agent_id')->unique();
                            $agentsList = Agent::whereIn('id', $agentIds)->get();
                            $AgentesPrioritarios = $agentsList->pluck('knowledge_id_hook')->filter()->implode(', ');
                            
                            $agentsString = $agents->slice(1) // Ignora o primeiro elemento
                                ->pluck('knowledge_id_hook') // ou o campo que você quer
                                ->implode(', ' ); // Junta com vírgula

                            // OU de forma mais detalhada:
                            $agentsString = '';
                            foreach($agents->slice(1) as $key => $agent) {
                                $agentsString .= $agent->knowledge_id_hook;
                                if($key < $agents->count() - 2) { // -2 porque começamos do segundo elemento
                                    $agentsString .= ', ';
                                }
                            }

                            $prioritariosArray = array_map('trim', explode(',', $AgentesPrioritarios));
                            $agentsString = '';

                            foreach($agents->slice(1) as $key => $agent) {
                                // Só adiciona se não estiver no array de prioritários
                                if (!in_array($agent->knowledge_id_hook, $prioritariosArray)) {
                                    $agentsString .= $agent->knowledge_id_hook;
                                    
                                    // Verifica se há próximo item válido para adicionar vírgula
                                    $nextExists = false;
                                    foreach($agents->slice($key + 2) as $nextAgent) {
                                        if (!in_array($nextAgent->knowledge_id_hook, $prioritariosArray)) {
                                            $nextExists = true;
                                            break;
                                        }
                                    }
                    
                                    if ($nextExists) {
                                        $agentsString .= ', ';
                                    }
                                }
                            }

                            $requisito = $Record->requisito;
                            $processo = $Processo->process;

                            // Pego os itens Primarios, removo da lista de secundarios, e transformo em string para enviar novamente.
                            $AgentesSecundariosExplode = explode(',', $AgentesPrioritarios);
                            $AgentesSecundarios = array_filter( array_map('trim', str_replace($FiltroAgentes, '', $AgentesSecundariosExplode)));
                            $AgentesSecundariosString = implode(', ', $AgentesSecundarios);
                        
                            $body = [
                                'inputs' =>  [
                                    'base_id_primarios' => $ListaAgentesPrioritarios,
                                    'base_id_secundarios' => $AgentesSecundariosString,    
                                ],
                                'query' => json_encode([
                                        'requisito' => $requisito,
                                        'processo' => $processo,
                                        'produto' => $ProdutosPrioritarios,
                                        'produtos_adicionais' => $ProdutosAdicionais
                                ], JSON_UNESCAPED_UNICODE),
                                'response_mode' => 'blocking',
                                "conversation_id" => "",
                                "user" => "RFP-API-LOCAL",
                                "files" => [],
                            ];  


                            dd($body);

                            //TOTVS Backoffice - Linha Protheus, Minha Coleta e Entrega, TOTVS Agendamentos, TOTVS Logística TMS, TOTVS OMS, TOTVS Roteirização e Entregas, TOTVS WMS SaaS, TOTVS YMS, TOTVS Frete Embarcador
                            //TOTVS Analytics, TOTVS Backoffice - Linha Protheus, Minha Coleta e Entrega, Planejamento Orçamentário by Prophix, RD Station CRM, TOTVS Agendamentos, TOTVS Backoffice Portal de Vendas, TOTVS Cloud IaaS, TOTVS Comércio Exterior, TOTVS CRM Automação da Força de Vendas - SFA, TOTVS Fluig, TOTVS Frete Embarcador, TOTVS Gestão de Frotas - Linha Protheus, TOTVS Logística TMS, TOTVS Manufatura - Linha Protheus, TOTVS OMS, TOTVS Roteirização e Entregas, TOTVS Transmite, TOTVS Varejo Lojas - Linha Protheus, TOTVS WMS SaaS, TOTVS YMS, Universidade TOTVS, Analytics by GoodData

                            yield function () use ($clientHookIA, $body, $Record) { 
                                return $clientHookIA->postAsync('/v1/chat-messages', [
                                    'json' => $body,
                                    'headers' => ['Content-Type' => 'application/json'],
                                ])->then(function ($response) use ($Record) {
                                    Log::info("Enviado");
                                    return ['response' => $response, 'record' => $Record];
                                    
                                });
                            };               
                        }
                    }
                }
            };
           
    
            $pool = new Pool($clientHookIA, $requestsHook(), [
                'concurrency' => 10,
                'fulfilled' => function ($result, $index) {
                    Log::info("Resposta Recebida");

                    $response = $result['response'];
                    
                    $data = json_decode($response->getBody(), true);
                    $Answer = json_decode($data['answer']);
                    $Referencia = json_encode($data['metadata']['retriever_resources']);
                    
                    $bundleId = RfpBundle::where('bundle', 'like', '%' . $Answer->linha_produto . '%')->first();
                    
                    $Record = $result['record'];
                    $Record->ia_attempts = intval($Record->ia_attempts) + 1;
                    $Record->save();
                    
                    // Atualizar o status do Record
                    if($Answer->aderencia_na_mesma_linha != '3esconhecido' || $Record->ia_attempts >= 3){

                        $DadosResposta = new ProjectAnswer;
                        $DadosResposta->bundle_id = $bundleId->bundle_id ?? null;
                        $DadosResposta->user_id = $Record->user_id;
                        $DadosResposta->requisito_id = $Record->id;
                        $DadosResposta->requisito = $Record->requisito;    
                        $DadosResposta->aderencia_na_mesma_linha = $Answer->aderencia_na_mesma_linha ?? null;
                        $DadosResposta->linha_produto = $Answer->linha_produto ?? null;
                        $DadosResposta->resposta = $Answer->resposta ?? null;
                        $DadosResposta->modulo = $Answer->modulo ?? null;
                        $DadosResposta->referencia = $Answer->referencia ?? null;
                        $DadosResposta->retriever_resources = $Referencia ?? null;
                        $DadosResposta->observacao = $Answer->observacao ?? null;
                        $DadosResposta->acuracidade_porcentagem = $Answer->acuracidade_porcentagem ?? null;
                        $DadosResposta->acuracidade_explicacao = $Answer->acuracidade_explicacao ?? null;
    
                        $DadosResposta->save();
                        
                        $Record->update(['status' => 'respondido ia']);
                        $Record->update(['project_answer_id' => $DadosResposta->id]);
                        $Record->save();
                    }

                    Log::info("Processamento de todos os arquivos concluído com sucesso");
    
                },
                'rejected' => function ($reason, $index) {
                    //dd($reason);
                    Log::error("Request failed: " . $reason->getMessage());
                    // Você pode querer atualizar o status do Record aqui também
                },
            ]);
    
            Log::info("Finalizado com sucesso");
            // Executa o pool
            $promise = $pool->promise();
            $promise->wait();
        } catch (\Exception $e) {
            Log::error( $e);
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
