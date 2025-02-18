<?php
namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\Project;
use App\Models\ProjectFiles;
use App\Models\ProjectRecord;
use App\Models\RfpBundle;
use App\Models\RfpAnswer;
use App\Models\UsersDepartaments;
use App\Models\Module;

use App\Imports\KnowledgeBaseImport;
use App\Imports\KnowledgeBaseInfoImport;
use App\Exports\KnowledgeBaseExport;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Illuminate\Support\Str;
use DateTime;


class ProjectRecordsController extends Controller
{


     /**
    * Valida as Informações Gerais de um REGISTRO ESPECIFICO
    */
    public function index(string $id)
    {
        $ProjectFile = ProjectFiles::with('rfp_bundles')->findOrFail($id);
        if($ProjectFile->user_id == Auth::id() || Auth::user()->role->role_priority >= 90){   
            if($ProjectFile->status != "processando"){
                $Project = Project::with('user')->findOrFail($ProjectFile->project_id);
       
                $ListClassificacaoRecebidas = ProjectRecord::where('project_file_id', $ProjectFile->id)->groupBy('classificacao')->pluck('classificacao');
                $UsersDepartaments = UsersDepartaments::where('departament_type', '!=', 'Admin')->orderBy('departament', 'asc')->get();

                $AllAnswers =  RfpAnswer::orderBy('order', 'asc')->get();
                $AllBundles = RfpBundle::orderBy('bundle', 'asc')->get();
                $AllModules = Module::orderBy('module_name', 'asc')->get();

                $ListProdutos = DB::table('project_records')
                ->leftJoin('rfp_bundles', 'project_records.bundle_id', '=', 'rfp_bundles.bundle_id')
                ->where('project_records.project_file_id', $ProjectFile->id)
                ->groupBy('project_records.bundle_id')
                ->select('project_records.bundle_id', 'rfp_bundles.bundle')
                ->groupBy('rfp_bundles.bundle')
                ->get();

                $Records = ProjectRecord::where('project_file_id', $ProjectFile->id)->get();
                $CountRecords = 0;

                foreach ($Records as $key => $Record) {
                    $CountRecords++;
                }

                $data = array(
                    'title' => 'Todos Arquivos',
                    'idProjectFile' => $id,
                    'Project' => $Project,
                    'ProjectFile' => $ProjectFile,
                    'UsersDepartaments' => $UsersDepartaments,
                    'ListClassificacao' => $ListClassificacaoRecebidas,
                    'ListProdutos' => $ListProdutos,
                    'AllBundles' => $AllBundles,
                    'AllModules' => $AllModules,
                    'AllAnswers' => $AllAnswers,
                    'CountCountRecordsResultado' => $CountRecords,
                );
                
                if($ProjectFile->status != "processando"){
                    return view('project.records.list')->with($data);
                }else{
                    return view('project.records.view')->with($data);
                }
            }else{
                // Caso a base esteja sendo processada não deixa fazer nada.
                session(['error' => 'Esta Base de Conhecimento está em processamentoe não pode ser editada.']);
                return redirect()->route('knowledge.list')->with('error', session('error'));
            }
        }
    }


    
    public function filter(Request $request, string $id)
    { 
        if(Auth::user()->role->role_priority >= 90){       
            $Project = ProjectFiles::findOrFail($id);
            $query = ProjectRecord::query()->with('rfp_bundles');

            // Adicionando explicitamente a cláusula where para garantir que o filtro está correto
            $query->where('project_file_id', '=', $Project->id);
            
            // Aplicar filtros
            if ($request->has('keyWord') && !empty($request->keyWord)) {
                $query->where(function ($q) use ($request) {
                    $q->where('requisito', 'like', '%' . $request->keyWord . '%')
                      ->orWhere('observacao', 'like', '%' . $request->keyWord . '%')
                      ->orWhere('processo', 'like', '%' . $request->keyWord . '%')
                      ->orWhere('subprocesso', 'like', '%' . $request->keyWord . '%')
                      ->orWhere('resposta', 'like', '%' . $request->keyWord . '%')
                      ->orWhere('modulo', 'like', '%' . $request->keyWord . '%')
                      ->orWhere('bundle_old', 'like', '%' . $request->keyWord . '%');
                });
            }
            
            $classificacao1 = $request->classificacao1 === "null" ? null : $request->classificacao1;
            if (filled($classificacao1)) {
                $query->where('classificacao', 'like', '%' . $request->classificacao1 . '%');
            }
             
            $resposta = $request->resposta === "null" ? null : $request->resposta;
            if (filled($resposta)) {
                $query->where('resposta', 'like', '%' . $request->resposta . '%');
            }

            $product = $request->product === "null" ? null : $request->product;
            if (filled($product)) {
                $query->where('bundle_old', 'like', '%' . $request->product . '%');
            }
            // Paginação
            $records = $query->paginate(100);

            // Retornar dados em JSON
            return response()->json($records);
        }
    }



    public function updateDetails(Request $request, string $id)
    { 
        // Valida a Permissão do usuário
        if(Auth::user()->role->role_priority >= 90){     
            $ProjectRecord = ProjectRecord::findOrFail($id);
            if($request->resposta){
                $ProjectRecord->resposta = $request->resposta;
            }

            if($request->bundle){
                $ProjectRecord->bundle_id = $request->bundle;
            }
             
            if($request->classificacao_id){
                $Module = Module::find($request->classificacao_id);
                $ProjectRecord->classificacao_id = $request->classificacao_id;
                $ProjectRecord->classificacao = $Module->module_name;
            }

            try{
                $ProjectRecord->save();
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
    * Valida as Informações Gerais de um REGISTRO ESPECIFICO
    */
    public function errors(string $id)
    {
        $ProjectFile = ProjectFiles::findOrFail($id);
        if($ProjectFile->user_id == Auth::id() || Auth::user()->role->role_priority >= 90){   
            $Project = Project::findOrFail($ProjectFile->project_id);

            $ListClassificacaoRecebidas = ProjectRecord::where('project_file_id', $ProjectFile->id)->groupBy('classificacao')->pluck('classificacao');

            $AllAnswers =  RfpAnswer::orderBy('order', 'asc')->get();
            $AllBundles = RfpBundle::orderBy('bundle', 'asc')->get();

            $ListProdutos = DB::table('project_records')
                ->leftJoin('rfp_bundles', 'project_records.bundle_id', '=', 'rfp_bundles.bundle_id')
                ->where('project_records.project_file_id', $ProjectFile->id)
                ->where(function ($query) {
                    $query->orWhereNull('project_records.classificacao_id')
                        ->orWhere('project_records.classificacao_id', '');
                })
                ->groupBy('project_records.bundle_id')
                ->select('project_records.bundle_id', 'rfp_bundles.bundle')
                ->get();

                $CountRecordsEmpty = ProjectRecord::where('project_file_id', $ProjectFile->id)->whereNull('bundle_id')->orWhere('bundle_id', '')->count();
                $CountRecords = ProjectRecord::where('project_file_id', $ProjectFile->id)->count();
            

                $data = array(
                    'title' => 'Todos Arquivos',
                    'idProjectFile' => $id,
                    'ProjectFile' => $ProjectFile,
                    'Project' => $Project,
                    'ListClassificacao' => $ListClassificacaoRecebidas,
                    'ListProdutos' => $ListProdutos,
                    'AllBundles' => $AllBundles,
                    'AllAnswers' => $AllAnswers,
                    'CountCountRecordsResultado' => $CountRecords,
                    'CountCountRecordsEmpty' => $CountRecordsEmpty,
                );

            return view('project.records.erro')->with($data);
        }
    }


    public function filterError(Request $request, string $id)
    { 
        if(Auth::user()->role->role_priority >= 90){       
            $Project = ProjectFiles::findOrFail($id);
            $query = ProjectRecord::query()->with('rfp_bundles');

            // Adicionando explicitamente a cláusula where para garantir que o filtro está correto
            $query->where('classificacao_id', '=', $Project->id);

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
     * Página de Processamento da BASE
     */
    public function processing(Request $request, string $id)
    {
        if(Auth::user()->role->role_priority >= 90){     
            $Project = ProjectFiles::findOrFail($id);
            if($Project->status == "não enviado"){
                $Project->status = "em processamento";
                $Project->save();
                
                $atualizados = ProjectRecord::where('project_file_id', $id)
                    ->update([
                        'bundle_id' => $Project->bundle_id,
                        'status' => 'processando'
                    ]);

    
                return view('project.records.processing');
            }else{
                if($Project->status == "em processamento"){
                    session(['error' => 'Esta Base de Conhecimento já está em processamento.']);
                }else if($Project->status == "processada" || $Project->status == "concluída"){
                    session(['error' => 'Esta Base de Conhecimento já está processada e Concluída.']);
                }
                
                // Redireciona para outra página, ou de volta com a mensagem
                return redirect()->route('project.list')->with('error', session('error'));
            }
        }
    }


     /**
     * Remove the specified resource from storage.
     */
    public function filterRemove(string $id)
    {
        // Encontrar o usuário pelo ID
        $Record = ProjectRecord::where('id_record', $id)->first();
        if (Auth::user()->role->role_priority >= 90){
            try {
                $RecordRemove = ProjectRecord::where('id_record', $id)->delete();

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
     * Display a listing of the resource.
     */
    public function index3()
    { 
        if(Auth::user()->role->role_priority >= 90){  
            $AllFiles = Project::withCount('projectRecords')->get();

            // Último atualizado

            $lastUpdated = Project::where('iduser_responsable', Auth::id())->orderBy('updated_at', 'desc')->first();
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


            $resultados = DB::table(table: 'project_records')
            ->leftJoin('rfp_bundles', 'project_records.bundle_id', '=', 'rfp_bundles.bundle_id') // INNER JOIN
            ->select('project_records.bundle_id', 'rfp_bundles.bundle',  DB::raw('COUNT(*) as total'))
            ->where('project_records.user_id',  Auth::id()) // Filtra pelo ID do usuário
            ->groupBy('project_records.bundle_id') // Agrupa pelo ID do bundle
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
                'KnowledgeBase' => $Project,
                'lastUpdated' => $lastUpdated,
                'lastUpdatedDate' => $lastUpdatedDate,
                'lastUpdatedTime' => $lastUpdatedTime,
                'ListFiles' => $ListFiles,
                'CountRFPs' => $CountRFPs,
                'CountPacotes' => $CountPacotes,
                'CountRequisitos' => $CountRequisitos,
                'totalRequisitos' => 1160,
                'totalRespostasIA' => 1000,
                'respostasUsuario' => 160
            );
    
            return view('project.records.list')->with($data);
        }

    }






      /**
    * Valida as Informações Gerais de um REGISTRO ESPECIFICO
    */
    public function answer(string $id)
    {
        $ProjectFile = ProjectFiles::with('rfp_bundles')->findOrFail($id);
        if($ProjectFile->user_id == Auth::id() || Auth::user()->role->role_priority >= 90){   
            if($ProjectFile->status != "processando"){
                $Project = Project::with('user')->findOrFail($ProjectFile->project_id);
       
                $ListClassificacaoRecebidas = ProjectRecord::where('project_file_id', $ProjectFile->id)->groupBy('classificacao')->pluck('classificacao');
                $UsersDepartaments = UsersDepartaments::where('departament_type', '!=', 'Admin')->orderBy('departament', 'asc')->get();

                $AllAnswers =  RfpAnswer::orderBy('order', 'asc')->get();
                $AllBundles = RfpBundle::orderBy('bundle', 'asc')->get();
                $AllModules = Module::orderBy('module_name', 'asc')->get();

                $ListProdutos = DB::table('project_records')
                ->leftJoin('rfp_bundles', 'project_records.bundle_id', '=', 'rfp_bundles.bundle_id')
                ->where('project_records.project_file_id', $ProjectFile->id)
                ->groupBy('project_records.bundle_id')
                ->select('project_records.bundle_id', 'rfp_bundles.bundle')
                ->groupBy('rfp_bundles.bundle')
                ->get();

                $Records = ProjectRecord::where('project_file_id', $ProjectFile->id)->get();
                $CountRecords = 0;

                foreach ($Records as $key => $Record) {
                    $CountRecords++;
                }

                $countIA = ProjectRecord::whereHas('answers', function ($query) {
                    $query->where('aderencia_na_mesma_linha', '!=', 'Desconhecido');
                })->count();

                $registrosSemResposta = $CountRecords - $countIA;
                $porcentagemSemResposta = ($countIA / $CountRecords) * 100;

                $data = array(
                    'title' => 'Todos Arquivos',
                    'idProjectFile' => $id,
                    'Project' => $Project,
                    'ProjectFile' => $ProjectFile,
                    'UsersDepartaments' => $UsersDepartaments,
                    'ListClassificacao' => $ListClassificacaoRecebidas,
                    'ListProdutos' => $ListProdutos,
                    'AllBundles' => $AllBundles,
                    'AllModules' => $AllModules,
                    'AllAnswers' => $AllAnswers,
                    'CountRFPs' => 123,
                    'CountPacotes' => 150,
                    'CountRequisitos' => $CountRecords,
                    'totalRequisitos' => 1160,
                    'totalRespostasIA' => 1000,
                    'CountAnswerIA' => $countIA,
                    'CountAnswerUser' => 0,
                    'progress' => $porcentagemSemResposta,
                    'registrosSemResposta' => $registrosSemResposta,
                    'CountCountRecordsResultado' => $CountRecords,
                );
                
                if($ProjectFile->status != "processando"){
                    return view('project.records.answer')->with($data);
                }else{
                    return view('project.records.answer')->with($data);
                }
            }else{
                // Caso a base esteja sendo processada não deixa fazer nada.
                session(['error' => 'Esta Base de Conhecimento está em processamentoe não pode ser editada.']);
                return redirect()->route('project.answer')->with('error', session('error'));
            }
        }
    }


    
    public function filterAnswer(Request $request, string $id)
    { 
        if(Auth::user()->role->role_priority >= 90){       
            $Project = ProjectFiles::findOrFail($id);
            $query = ProjectRecord::query()->with('rfp_bundles')->with('answers');

            // Adicionando explicitamente a cláusula where para garantir que o filtro está correto
            $query->where('project_file_id', '=', $Project->id);
            
            // Aplicar filtros
            if ($request->has('keyWord') && !empty($request->keyWord)) {
                $query->where(function ($q) use ($request) {
                    $q->where('requisito', 'like', '%' . $request->keyWord . '%')
                      ->orWhere('observacao', 'like', '%' . $request->keyWord . '%')
                      ->orWhere('processo', 'like', '%' . $request->keyWord . '%')
                      ->orWhere('subprocesso', 'like', '%' . $request->keyWord . '%')
                      ->orWhere('resposta', 'like', '%' . $request->keyWord . '%')
                      ->orWhere('modulo', 'like', '%' . $request->keyWord . '%')
                      ->orWhere('bundle_old', 'like', '%' . $request->keyWord . '%');
                });
            }
            
            $classificacao1 = $request->classificacao1 === "null" ? null : $request->classificacao1;
            if (filled($classificacao1)) {
                $query->where('processo', 'like', '%' . $request->classificacao1 . '%');
            }
             
            $resposta = $request->resposta === "null" ? null : $request->resposta;
            if (filled($resposta)) {
                $query->where('resposta', 'like', '%' . $request->resposta . '%');
            }

            $product = $request->product === "null" ? null : $request->product;
            if (filled($product)) {
                $query->where('bundle_old', 'like', '%' . $request->product . '%');
            }
            // Paginação
            $records = $query->paginate(100);

            // Retornar dados em JSON
            return response()->json($records);
        }
    }



}
