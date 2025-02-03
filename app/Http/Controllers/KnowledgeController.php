<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\KnowledgeBase;
use App\Models\KnowledgeRecord;
use App\Models\RfpBundle;
use App\Imports\KnowledgeBaseImport;
use App\Imports\KnowledgeBaseInfoImport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use DateTime;



class KnowledgeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    { 
        if(Auth::user()->role->role_priority >= 90){  
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
        }

    }



    public function filter(Request $request)
    { 
        // Valida a Permissão do usuário
        if(Auth::user()->role->role_priority >= 90){       
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
        }
    }




    /**
     * Display a listing of the resource.
     */
    public function listall()
    {
        if(Auth::user()->role->role_priority >= 90){
            $AllFiles = DB::table('knowledge_base')
                    ->join('users', 'knowledge_base.user_id', '=', 'users.id') // Faz o join
                    ->select('knowledge_base.*', 'users.name as role_name') // Seleciona os campos desejados
                    ->get();
            $ListFiles = array();
            $CountRFPs = 0;
            $CountRequisitos = 0;

            foreach ($AllFiles as $key => $File) {
                    $CountRFPs++;
                    $ListFile = array();
                    $ListFile['knowledge_base_id'] = $File->knowledge_base_id;
                    $ListFile['bundle'] = RfpBundle::firstWhere('bundle_id', $File->bundle_id);
                    $ListFile['filepath'] = $File->filepath;
                    $ListFile['filename_original'] = $File->filename_original;
                    $ListFile['filename'] = $File->filename;
                    $ListFile['username'] = $File->role_name;
                    $ListFile['file_extension'] = $File->file_extension;

                    $QtdRecordsBase = DB::table(table: 'knowledge_records')
                    ->select( DB::raw('COUNT(*) as total'))
                    ->where('knowledge_records.knowledge_base_id',  $File->knowledge_base_id) // Filtra pelo ID do usuário
                    ->get();
            
                    $CountRequisitos = $CountRequisitos + $QtdRecordsBase[0]->total;
                    $ListFile['QtdRecordsBase'] = $QtdRecordsBase[0]->total;
                    $ListFile['status'] = $File->status;
                    $ListFile['created_at'] = date("d/m/Y", strtotime($File->created_at));;
                    $ListFiles[$File->knowledge_base_id] = $ListFile;
            }

            $resultados = DB::table(table: 'knowledge_records')
            ->leftJoin('rfp_bundles', 'knowledge_records.bundle_id', '=', 'rfp_bundles.bundle_id') // INNER JOIN
            ->select('knowledge_records.bundle_id', 'rfp_bundles.bundle',  DB::raw('COUNT(*) as total'))
            ->groupBy('knowledge_records.bundle_id') // Agrupa pelo ID do bundle
            ->get();
        
            $CountResultado = 0;
            $CountPacotes = 0;
            $ListBundles = [];
            $ListBundlesQtds = [];

            //Exibindo o resultado
            foreach ($resultados as $resultado) {
                    $ListBundles[]= $resultado->bundle;
                    $ListBundlesQtds[]= $resultado->total;
                    $CountPacotes++;
                    $CountResultado = $CountResultado + $resultado->total;
            }
        
            $data = array(
                'title' => 'Todos Arquivos',
                'ListFiles' => $ListFiles,
                'CountResultado' => $CountRequisitos,
                'CountPacotes' => $CountPacotes,
                'CountRFPs' => $CountRFPs,
                'ResultadosRFPs' => $resultados,
                'ListBundles' => $ListBundles,
                'ListBundlesQtds' => $ListBundlesQtds
            );


            return view('knowledge.list_all')->with($data);
        } else {
            return redirect()->back()->with('error', 'Usuário sem permissão para acessar.');
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


    public function updateRecordDetails(Request $request, string $id)
    { 
        // Valida a Permissão do usuário
        if(Auth::user()->role->role_priority >= 90){     
            $KnowledgeRecords = KnowledgeRecord::findOrFail($id);
            if($request->resposta){
                $KnowledgeRecords->resposta = $request->resposta;
            }

            if($request->bundle){
                $KnowledgeRecords->bundle_id = $request->bundle;
            }
            
               
            try{
                $KnowledgeRecords->save();
                // Retornar dados em JSON
                return response()->json("success");    
            } catch (\Exception $e) {
                $CatchError = json_decode($e->getMessage());

                return response()->json([
                     'message' => 'Erro ao salva!',
                ], 422);
            }              
        }
    }






    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

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
    
    }



     /**
     * Show the form for creating a new resource.
     */
    public function createFile()
    {
       
        $userId = auth()->user()->id;
        $data = [
            'userId' => $userId
        ];
    
        return view('knowledge.createFile')->with($data);
    
    }

    /**
     * UPLOAD - Do arquivo da base de conhecimento
     */
    public function uploadFile(Request $request) {
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
    * Valida as Informações Gerais de um REGISTRO ESPECIFICO
    */
    public function records(string $id)
    {
        $KnowledgeBase = KnowledgeBase::findOrFail($id);
        if($KnowledgeBase->user_id == Auth::id() || Auth::user()->role->role_priority >= 90){     
            
            $ListClassificacao = KnowledgeRecord::where('knowledge_base_id', $KnowledgeBase->id)->groupBy('classificacao')->pluck('classificacao');
            $ListClassificacao2 = KnowledgeRecord::where('knowledge_base_id', $KnowledgeBase->id)->groupBy('classificacao2')->pluck('classificacao2');
            
            $AllBundles = RfpBundle::orderBy('bundle', 'asc')->get();

            $ListProdutos = DB::table('knowledge_records')
            ->leftJoin('rfp_bundles', 'knowledge_records.bundle_id', '=', 'rfp_bundles.bundle_id')
            ->where('knowledge_records.knowledge_base_id', $KnowledgeBase->id)
            ->groupBy('knowledge_records.bundle_id')
            ->select('knowledge_records.bundle_id', 'rfp_bundles.bundle')
            ->get();

            $Records = KnowledgeRecord::where('knowledge_base_id', $KnowledgeBase->id)->get();
            $CountRecords = 0;

            foreach ($Records as $key => $Record) {
                $CountRecords++;
            }

            $data = array(
                'title' => 'Todos Arquivos',
                'idKnowledgeBase' => $id,
                'KnowledgeBase' => $KnowledgeBase,
                'ListClassificacao' => $ListClassificacao,
                'ListClassificacao2' => $ListClassificacao2,
                'ListProdutos' => $ListProdutos,
                'AllBundles' => $AllBundles,
                'CountCountRecordsResultado' => $CountRecords,
            );
        
            return view('knowledge.records')->with($data);
        }
    }


    public function recordsFilter(Request $request, string $id)
    { 
        if(Auth::user()->role->role_priority >= 90){       
            $KnowledgeBase = KnowledgeBase::findOrFail($id);
            $query = KnowledgeRecord::query()->with('rfp_bundles');

            // Adicionando explicitamente a cláusula where para garantir que o filtro está correto
            $query->where('knowledge_base_id', '=', $KnowledgeBase->id);

            // Aplicar filtros
            if ($request->has('keyWord') && !empty($request->keyWord)) {
                $query->where(function ($q) use ($request) {
                    $q->where('requisito', 'like', '%' . $request->keyWord . '%')
                      ->orWhere('observacao', 'like', '%' . $request->keyWord . '%');
                });
            }
            
            $classificacao1 = $request->classificacao1 === "null" ? null : $request->classificacao1;
            if (filled($classificacao1)) {
                dd($request->classificacao1);
                $query->where('classificacao', 'like', '%' . $request->classificacao1 . '%');
            }
                        
            // Paginação
            $records = $query->paginate(40);

            // Retornar dados em JSON
            return response()->json([
                'data' => $records->items(),
                'next_page_url' => $records->nextPageUrl(),
            ]);

            //return response()->json($records);
        }
    }




    /**
    * Valida as Informações Gerais de um REGISTRO ESPECIFICO
    */
    public function recordsErrors(string $id)
    {
        $KnowledgeBase = KnowledgeBase::findOrFail($id);
        if($KnowledgeBase->user_id == Auth::id() || Auth::user()->role->role_priority >= 90){     
            
            $ListClassificacao = KnowledgeRecord::where('knowledge_base_id', $KnowledgeBase->id)->groupBy('classificacao')->pluck('classificacao');
            
            $ListClassificacao2 = KnowledgeRecord::where('knowledge_base_id', $KnowledgeBase->id)->groupBy('classificacao2')->pluck('classificacao2');
            $ListProdutos = DB::table('knowledge_records')
            ->leftJoin('rfp_bundles', 'knowledge_records.bundle_id', '=', 'rfp_bundles.bundle_id')
            ->where('knowledge_records.knowledge_base_id', $KnowledgeBase->id)
            ->groupBy('knowledge_records.bundle_id')
            ->select('knowledge_records.bundle_id', 'rfp_bundles.bundle')
            ->get();

            $Records = KnowledgeRecord::where('knowledge_base_id', $KnowledgeBase->id)->get();
            $CountRecords = 0;

            foreach ($Records as $key => $Record) {
                $CountRecords++;
            }

            $data = array(
                'title' => 'Todos Arquivos',
                'idKnowledgeBase' => $id,
                'KnowledgeBase' => $KnowledgeBase,
                'ListClassificacao' => $ListClassificacao,
                'ListClassificacao2' => $ListClassificacao2,
                'ListProdutos' => $ListProdutos,
                'CountCountRecordsResultado' => $CountRecords,
            );
        
            return view('knowledge.recordsErrors')->with($data);
        }
    }



    public function recordsFilterError(Request $request, string $id)
    { 
        if(Auth::user()->role->role_priority >= 90){       
            $KnowledgeBase = KnowledgeBase::findOrFail($id);
            $query = KnowledgeRecord::query()->with('rfp_bundles');

            // Adicionando explicitamente a cláusula where para garantir que o filtro está correto
            $query->where('knowledge_base_id', '=', $KnowledgeBase->id);

            $query->whereNull('bundle_id')->orWhere('bundle_id', ''); // Para strings vazias
                        
            // Paginação
            $records = $query->paginate(40);

            // Retornar dados em JSON
            return response()->json([
                'data' => $records->items(),
                'next_page_url' => $records->nextPageUrl(),
            ]);

            //return response()->json($records);
        }
    }





    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    
    /**
     * Remove the specified resource from storage.
     */
    public function recordsFilterRemove(string $id)
    {
        // Encontrar o usuário pelo ID
        $Record = KnowledgeRecord::where('id_record', $id)->first();
        if (Auth::user()->role->role_priority >= 90){
            try {
                $RecordRemove = KnowledgeRecord::where('id_record', $id)->delete();

                if($RecordRemove){
                    return response()->json([ 'status' => "success" ,'message' => "Excluído com sucesso!" ]);
                }else{
                    return response()->json([ 'status' => "error" ,'message' => "Não foi possível excluir!" ]);
                }
               
            } catch (\Throwable $th) {
                //throw $th;
                dd($th);
            }
        } else {
            return response()->json(['status' => "error" , 'message' => "Usuário sem permissão para exclusão!" ]);
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
}
