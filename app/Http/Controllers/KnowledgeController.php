<?php

namespace App\Http\Controllers;
use App\Exports\KnowledgeCorrectionExport;
use App\Models\Agent;
use App\Models\ProjectAnswer;
use App\Models\ProjectFiles;
use App\Models\ProjectHistory;
use App\Models\ProjectRecord;
use App\Models\RfpProcess;
use Carbon\Carbon;
use App\Models\KnowledgeBase;
use App\Models\KnowledgeRecord;
use App\Models\KnowledgeError;
use App\Models\KnowledgeBaseExported;
use App\Models\RfpBundle;
use App\Imports\KnowledgeBaseImport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exceptions\RDStationMentoria\RDStationMentoria;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\MultipartStream;
use DateTime;
use ZipArchive;


use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Exception\RequestException;




class KnowledgeController extends Controller
{
    protected $RDStationMentoria = [];

    /**
     * Display a listing of the resource.
     */
    public function index()
    { 
        if (Auth::user()->hasAnyPermission(['knowledge.manage', 'knowledge.add', 'knowledge.edit', 'knowledge.delete'])) {
            $AllFiles = KnowledgeBase::withCount('knowledgeRecords')->get();

            // Último atualizado
            
            $lastUpdated = KnowledgeBase::where('user_id', Auth::id())->orderBy('updated_at', 'desc')->first();
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

            foreach ($AllFiles as $key => $File) {
                    $CountRFPs++;
                    $ListFile = array();
                    $ListFile['knowledge_base_id'] = $File->id;
                    $ListFile['bundle'] = RfpBundle::firstWhere('bundle_id', $File->bundle_id);
                    $ListFile['filepath'] = $File->filepath;
                    $ListFile['filename_original'] = $File->filename_original;
                    $ListFile['filename'] = $File->filename;
                    $ListFile['file_extension'] = $File->file_extension;
                    $ListFile['status'] = $File->status;
                    $ListFile['created_at'] = date("d/m/Y", strtotime($File->created_at));;

                    $CountRequisitos += $File->knowledge_records_count;
                    $ListFiles[] = $ListFile;
            }

            $resultados = DB::table(table: 'knowledge_records')
            ->leftJoin('rfp_bundles', 'knowledge_records.bundle_id', '=', 'rfp_bundles.bundle_id') // INNER JOIN
            ->select('knowledge_records.bundle_id', 'rfp_bundles.bundle',  DB::raw('COUNT(*) as total'))
            ->where('knowledge_records.user_id',  Auth::id()) // Filtra pelo ID do usuário
            ->groupBy('knowledge_records.bundle_id') // Agrupa pelo ID do bundle
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
                'CountRFPs' => $CountRFPs,
                'CountPacotes' => $CountPacotes,
                'CountRequisitos' => $CountRequisitos
            );
    
            return view('knowledge.list')->with($data);
        }else{
            return redirect()->route('profile.edit')->with('error', 'Você não tem permissão para acessa essa página.');
        }

    }



    public function filter(Request $request)
    { 
        // Valida a Permissão do usuário
       if (Auth::user()->hasAnyPermission(['knowledge.manage', 'knowledge.add', 'knowledge.edit', 'knowledge.delete'])) {
            $query = KnowledgeBase::query()->with('user')->withCount('knowledgeRecords');;
            
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
            return redirect()->route('profile.edit')->with('error', 'Você não tem permissão para acessa essa página.');
        }
    }


    public function updateInfos(Request $request, string $id)
    { 
        // Valida a Permissão do usuário
       if (Auth::user()->hasAnyPermission(['knowledge.manage', 'knowledge.edit'])) {

            $KnowledgeBase = KnowledgeBase::findOrFail($id);
            $KnowledgeBase->project = $request->project;
            $KnowledgeBase->project_team = $request->project_team;

            if (!empty($request->rfp_date)) {
                $data = htmlspecialchars(trim($request->rfp_date)); // Sanitiza o input
                $resultado = $this->validarEConverterData($data);

                if ($resultado) {
                    $KnowledgeBase->rfp_date = $resultado;
                }else{
                    return response()->json([ 'message' => 'Data Inválida!',], 422);
                }  
            }

            try{
                $KnowledgeBase->save();
                // Retornar dados em JSON
                return response()->json("success");    
            } catch (\Exception $e) {
                $CatchError = json_decode($e->getMessage());
            }
                         
        }else{
            return redirect()->route('knowledge.list')->with('error', 'Você não tem permissão para acessa essa página.');
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (Auth::user()->hasAnyPermission(['knowledge.manage', 'knowledge.add'])) {

            $rfpBundles = RfpBundle::all();
            $ListBundles = array();

            //$AgentId = Auth::user()->id;

            foreach ($rfpBundles as $key => $User) {
                $ListBundle = array();
                $ListBundle['id'] = $User->bundle_id;
                $ListBundle['bundle'] = $User->bundle;
                $ListBundle['type'] = $User->type_bundle;
                $ListBundles[] = $ListBundle;
            }

            //return view('auth.register')->with($data);
        
            $userId = auth()->user()->id;
            $data = [
                'userId' => $userId,
                'ListBundles' => $ListBundles
            ];
        
            return view('knowledge.create')->with($data);

        }else{
            return redirect()->route('knowledge.list')->with('error', 'Você não tem permissão para acessa essa página.');
        }
    
    }

    /**
     * UPLOAD - Do arquivo da base de conhecimento
     */
    public function upload(Request $request) {
        
        $TotvsERP = 0;
    
        // Faz o upload para o S3 (Para BACKUP)
        $File = $request->file('file');
        $filePath = 'cdn/knowledge/base/' . $File->hashName();
        $UploadedFile = Storage::disk('s3')->put($filePath, file_get_contents($File));
        //$content = Storage::disk('s3')->temporaryUrl($path,now()->addMinutes(10));

        // Sobe o arquivo para historico e salva no BD
        $KnowledgeBaseData = new KnowledgeBase();
        $KnowledgeBaseData->user_id = Auth::id();
        $KnowledgeBaseData->name = $request->name;
        $KnowledgeBaseData->filename_original = $File->getClientOriginalName();
        $KnowledgeBaseData->filepath = $filePath;
        $KnowledgeBaseData->filename = $File->hashName();
        $KnowledgeBaseData->file_extension = $File->extension();
        $KnowledgeBaseData->save();

        $KnowledgeBaseDataid = $KnowledgeBaseData->id;

        try {
            // Chama a importação do EXCEL
            $import = new KnowledgeBaseImport($KnowledgeBaseDataid);
            // Executa a importação
            $Excel = Excel::import($import, $File);

            // Retornar a URL como resposta JSON
            return response()->json([
                'success' => true,
                'message' => 'Arquivo atualizado com sucesso!',
                'redirectUrl' => '/knowledge/records/'.$KnowledgeBaseDataid,
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
            $InsertError = KnowledgeError::create([
                'error_code' => 'ERR003',
                'error_message' => $e->getMessage(),
                'error_data' => json_encode($e),
                'user_id' => Auth::id(), // Associar ao usuário logado, se necessário
            ]);

            $InsertErrorID = $InsertError->id;
    
            // Remove a Base Enviada
            if ($KnowledgeBaseDataid) {
                DB::table('knowledge_base')->where('id', $KnowledgeBaseDataid)->delete();
            }

            // Captura quaisquer outras exceções
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }
  }


  public function cron(){
        Log::info('Iniciando o processamento da base de conhecimento');
        try {
            $Bundles = RfpBundle::with('agent')->get();

            foreach ($Bundles as $bundle) {
                $Records = ProjectRecord::whereNotNull('project_records.bundle_id')
                    ->where('project_records.bundle_id', $bundle->bundle_id)
                    ->where('project_records.status', 'user edit')
                    ->whereNull('project_records.retroalimentacao')
                    ->get();

                if (!$Records->isEmpty()) { 
                    $filenamePrev = 'retroalimentacao_'.$bundle->bundle_id.'_'.uniqid();
                    $fileName = preg_replace('/[^\w\-_\.]/', '', $filenamePrev);
                    $fileName = trim($fileName, '_');
                    $fileName = Str::slug($fileName).'.csv';

                    $BundleName = preg_replace('/[^\w\-_\.]/', '', $bundle->bundle);
                    $BundleName = trim($BundleName, '_');
                    $BundleName = Str::slug($BundleName);

                    $filePath = 'cdn/knowledge/base_exported_corrections/'.$BundleName.'/'.$fileName;

                    // Cria um Registro de Arquivo Exportado
                    $KnowledgeBaseExported = new KnowledgeBaseExported();
                    // Por ser um campo obrigatório, deixamos fixo com o ID 1
                    $KnowledgeBaseExported->user_id = 1;
                    $KnowledgeBaseExported->bundle_id = 11;
                    //$KnowledgeBaseExported->default_base_id = $item->id;
                    $KnowledgeBaseExported->save();
                    $KnowledgeBaseExportedid = $KnowledgeBaseExported->id;
                    $KnowledgeBaseExported->filepath = $filePath;
                    $KnowledgeBaseExported->filename = $fileName;

                    $RecordsData = [];
                    foreach ($Records as $key => $record) {
                        $ProjectAnswer = ProjectAnswer::where('requisito_id', $record->id)->first();
                        $RecordData = [];
                        $RecordData['id_record'] = $record->id;
                        $RecordData['processo'] = $record->processo;
                        $RecordData['subprocesso'] = $record->subprocesso;
                        $RecordData['requisito'] = $ProjectAnswer->requisito;
                        //$RecordData['Resposta'] = ;
                        $RecordData['modulo'] = $ProjectAnswer->modulo;
                        $RecordData['observações'] = $ProjectAnswer->observacao;
                        $RecordData['produto'] = $ProjectAnswer->linha_produto;
                        $RecordsData[] = $RecordData;
                    }

                    // Envia os arquivos para a S3 e Pega a URL
                    $export = new KnowledgeCorrectionExport(  1, $RecordsData, $KnowledgeBaseExportedid); 
                    $Exports = Excel::store($export, $filePath, 's3');
                    $fileUrl = Storage::disk('s3')->url($filePath);
        
                    if (!empty($fileUrl)) {
                        // Atualiza com a URL
                        $KnowledgeBaseExported->file_url = $fileUrl;
                        $KnowledgeBaseExported->save();
                        //$item->status = 'processado';
                        //$item->save();
                    }


                } else {
                    Log::error("Erro ao processar a base de conhecimento");
                }
            }
        } catch (\Exception $e) {
            Log::error("Erro: " . $e->getMessage());
        }
          
        Log::info('Finalizando o processamento da base de conhecimento'); // Adiciona log aqui
    }


  public function cron3(){
    $client = new Client();
    $Bundles = RfpBundle::with('agent')->get();
    $RDStationMentoria = new RDStationMentoria();
    $RDStationMentoria->classBases();
    $UpdateBase = false;

    $tempDir = storage_path('app/temp');
    if (!file_exists($tempDir)) {
        mkdir($tempDir, 0755, true);
    }

    // Pegar todas as respostas concluídas
    $KnowledgeBase = KnowledgeBase::where('status', "processando")->get();
    if ($KnowledgeBase->count() > 0) {
        foreach ($KnowledgeBase as $item) {
            try {
                $Bundles = RfpBundle::with('agent')->get();

                foreach ($Bundles as $bundle) {
                    $Records = KnowledgeRecord::whereNotNull('knowledge_records.bundle_id')
                        ->where('knowledge_base_id', $item->id)
                        ->where('knowledge_records.bundle_id', $bundle->bundle_id)
                        ->where('knowledge_records.status', 'aguardando')
                        ->join('rfp_bundles', 'knowledge_records.bundle_id', '=', 'rfp_bundles.bundle_id')
                        ->select('knowledge_records.id_record', 'knowledge_records.processo', 'knowledge_records.subprocesso', 'knowledge_records.requisito', 'knowledge_records.resposta', 'knowledge_records.modulo', 'knowledge_records.observacao', 'rfp_bundles.bundle')
                        ->get();
                    
                    if (!$Records->isEmpty()) {
                        $filenamePrev = $item->id.'_'.$item->name.'_'.uniqid();
                        $fileName = preg_replace('/[^\w\-_\.]/', '', $filenamePrev);
                        $fileName = trim($fileName, '_');
                        $fileName = Str::slug($fileName).'.csv';

                        $BundleName = preg_replace('/[^\w\-_\.]/', '', $bundle->bundle);
                        $BundleName = trim($BundleName, '_');
                        $BundleName = Str::slug($BundleName);

                        $filePath = 'cdn/knowledge/base_exported/'.$BundleName.'/'.$fileName;

                        // Cria um Registro de Arquivo Exportado
                        $KnowledgeBaseExported = new KnowledgeBaseExported();
                        $KnowledgeBaseExported->user_id = $item->user_id;
                        $KnowledgeBaseExported->bundle_id = $bundle->bundle_id;
                        $KnowledgeBaseExported->default_base_id = $item->id;
                        $KnowledgeBaseExported->save();
                        $KnowledgeBaseExportedid = $KnowledgeBaseExported->id;
                        $KnowledgeBaseExported->filepath = $filePath;
                        $KnowledgeBaseExported->filename = $fileName;

                        // Envia os arquivos para a S3 e Pega a URL
                        $export = new KnowledgeBaseExport($item->id, $Records, $KnowledgeBaseExportedid);
                        Excel::store($export, $filePath, 's3');
                        $fileUrl = Storage::disk('s3')->url($filePath);

                        if (!empty($fileUrl)) {
                            // Atualiza com a URL
                            $KnowledgeBaseExported->file_url = $fileUrl;
                            $KnowledgeBaseExported->save();
                            $item->status = 'processado';
                            $item->save();
                        }
                    } else {
                       Log::error("Erro ao processar a base de conhecimento");
                    }
                }
            } catch (\Exception $e) {
                Log::error("Erro: " . $e->getMessage());
            }
        }
    } else {
        Log::info('Nenhuma base de conhecimento com status "processando" encontrada');
    }













    foreach ($Bundles as $bundle) {
        $gBaseById = $RDStationMentoria->Bases->getBaseById($bundle->agent->knowledge_id);
        if (!empty($gBaseById['id'])) {
            

            $KnowledgeBaseExported = KnowledgeBaseExported::where('status', "aguardando")->where('bundle_id', $bundle->bundle_id)->get();
            if ($KnowledgeBaseExported->count() > 0) {
                foreach ($KnowledgeBaseExported as $item) {
                    $UpdateBase = true;
                    $Arquivo = array();
                    $Arquivo['url'] = $item->file_url;
                    $Arquivo['title'] = $item->filename;;
                    $Arquivo['img'] = null;
                    $Arquivo['description'] = $item->filename;
                    $Arquivo['key'] = $item->filename;
                    $Arquivo['ext'] = "text/csv";

                    $gBaseById['sources'][] = ["type" => "file", "file" => $Arquivo];

                    $fileContent = Storage::disk('s3')->get($item->filepath);
                    $localFileName = uniqid('file-') . '.csv';
                    Storage::disk('local')->put('temp/' . $item->filename, $fileContent);
                    $localFilePath = storage_path('app/temp/' . $item->filename);

                    try {
                        $url = "https://lab.hook.app.br/v1/datasets/".$bundle->agent->knowledge_id_hook."/document/create-by-file";
            
                        // Preparar os dados multipart
                        $multipart = [
                            [
                                'name' => 'data',
                                'contents' => json_encode([
                                    "indexing_technique" => "high_quality",
                                    "process_rule" => [
                                        "rules" => [
                                            "pre_processing_rules" => [
                                                ["id" => "remove_extra_spaces", "enabled" => true],
                                                ["id" => "remove_urls_emails", "enabled" => true]
                                            ],
                                            "segmentation" => [
                                                "separator" => "###",
                                                "max_tokens" => 500
                                            ]
                                        ],
                                        "mode" => "custom"
                                    ]
                                ]),
                                'headers' => [
                                    'Content-Type' => 'text/plain'
                                ]
                            ],
                            [
                                'name' => 'file',
                                'contents' => fopen($localFilePath, 'r'),
                                'filename' => basename($localFilePath),
                                'headers' => [
                                    'Content-Type' => mime_content_type($localFilePath)
                                ]
                            ]
                        ];

                        // Criar o stream multipart
                        $multipartStream = new MultipartStream($multipart);

                        // Fazer a requisição
                        $response = $client->request('POST', $url, [
                            'headers' => [
                                'Authorization' => "Bearer dataset-XSIGaQdZXZdDLux237SDv7s9",
                                'Content-Type' => 'multipart/form-data; boundary=' . $multipartStream->getBoundary()
                            ],
                            'body' => $multipartStream
                        ]);

                        $statusCode = $response->getStatusCode();
                        $body = $response->getBody()->getContents();
                    
                    } catch (GuzzleException $e) {
                        echo "Erro: " . $e->getMessage();
                    }


                    $item->status = "exportado";
                    $item->save();
                } 
    
                

                if($UpdateBase){
                    $updatedBase = $RDStationMentoria->Bases->updateBase($gBaseById['id'], $gBaseById);
                    $UpdateBase = false;
                    
                }
            }
        }
    }
  }
















function fazerRequisicao($url, $metodo = 'GET', $dados = null, $headers = []) {
    $client = new Client();
    
    $options = [
        'headers' => $headers,
    ];
    
    if ($metodo == 'POST' || $metodo == 'PUT') {
        $options['json'] = $dados;
    }
    
    try {
        $response = $client->request($metodo, $url, $options);
        return [
            'body' => $response->getBody()->getContents(),
            'status' => $response->getStatusCode()
        ];
    } catch (RequestException $e) {
        if ($e->hasResponse()) {
            return [
                'body' => $e->getResponse()->getBody()->getContents(),
                'status' => $e->getResponse()->getStatusCode()
            ];
        }
        throw $e;
    }
}




    /**
     * Remove the specified resource from storage.
     */
    public function download(string $id)
    {
        // Encontrar o usuário pelo ID
        $Arquivo = KnowledgeBase::where('id', $id)->first();
        if (Auth::user()->hasRole('Administrador')) {
            $ArquivosExportados = KnowledgeBaseExported::where('default_base_id', $id)->get();

            // Lista de arquivos no S3 que você quer baixar
            $s3Files = [];
            foreach ($ArquivosExportados as $key => $ArquivoExportado) {
               $s3Files[] = $ArquivoExportado->filepath;
            }

            // Crie um diretório temporário
            $tempDir = storage_path('app/temp_' . uniqid());
            if (!file_exists($tempDir)) {
                mkdir($tempDir);
            }

            // Baixe os arquivos do S3 para o diretório temporário
            foreach ($s3Files as $file) {
                $contents = Storage::disk('s3')->get($file);
                $localFilePath = $tempDir . '/' . basename($file);
                file_put_contents($localFilePath, $contents);
            }

            // Crie um arquivo ZIP
            $fileName = preg_replace('/[^\w\-_\.]/', '', $Arquivo['name']); // Substitui caracteres não permitidos por "_"
            $fileName = trim($fileName, '_');

            $zipFileName = 'arquivos_'.$fileName.'_'. date('Y-m-d_H-i-s') . '.zip';
            $zipFilePath = storage_path('app/' . $zipFileName);

            $zip = new ZipArchive();
            if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($tempDir),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );

                foreach ($files as $name => $file) {
                    if (!$file->isDir()) {
                        $filePath = $file->getRealPath();
                        $relativePath = substr($filePath, strlen($tempDir) + 1);
                        $zip->addFile($filePath, $relativePath);
                    }
                }
                $zip->close();
            }

            // Limpe os arquivos temporários
            $this->deleteDirectory($tempDir);

            // Ofereça o arquivo ZIP para download
            return response()->download($zipFilePath)->deleteFileAfterSend(true);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Usuário sem permissão para excluir']);
        }
    }



    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        // Encontrar o usuário pelo ID
        $Arquivo = KnowledgeBase::where('id', $id)->first();
        if (Auth::user()->hasAnyPermission(['knowledge.manage', 'knowledge.delete'])) {

            if (Storage::disk('s3')->exists($Arquivo->filepath)) {
                $fullPath = Storage::disk('s3')->url($Arquivo->filepath);                
                if (Storage::disk('s3')->delete($Arquivo->filepath)) {
                    KnowledgeRecord::where('knowledge_base_id', $id)->delete();// 
                    KnowledgeBase::where('id', $id)->delete();// Exclui o usuário do banco de dados
                    return response()->json(['status' => 'success', 'message' => 'Arquivo excluído com sucesso!']);
                }else{
                    KnowledgeRecord::where('knowledge_base_id', $id)->delete();// 
                    KnowledgeBase::where('id', $id)->delete();// Exclui o usuário do banco de dados
                    return response()->json(['status' => 'error', 'message' => 'Erro ao excluir arquivo']);
                }
            }else{
                return response()->json(['status' => 'error', 'message' => 'Arquivo não encontrado']);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Usuário sem permissão para excluir']);
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

    private function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }


}
