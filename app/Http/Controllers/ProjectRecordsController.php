<?php
namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\Project;
use App\Models\ProjectFiles;
use App\Models\ProjectRecord;
use App\Models\RfpBundle;
use App\Models\RfpProcess;
use App\Models\RfpAnswer;
use App\Models\UsersDepartaments;
use App\Models\Module;

use App\Imports\KnowledgeBaseImport;
use App\Imports\KnowledgeBaseInfoImport;
use App\Exports\KnowledgeBaseExport;
use App\Models\KnowledgeRecord;
use App\Models\ProjectAnswer;
use App\Models\ProjectHistory;
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
        if (Auth::user()->hasAnyPermission(['projects.all', 'projects.my', 'projects.all.manage',  'projects.all.add', 'projects.all.edit', 'projects.all.delete', 'projects.my.manage', 'projects.my.add', 'projects.my.edit', 'projects.my.delete'])) {
            if($ProjectFile->status != "processando"){
                $Project = Project::with('user')->findOrFail($ProjectFile->project_id);
       
                $ListClassificacaoRecebidas = ProjectRecord::where('project_file_id', $ProjectFile->id)->groupBy('processo')->pluck('processo');
                $UsersDepartaments = UsersDepartaments::where('departament_type', '!=', 'Admin')->orderBy('departament', 'asc')->get();

                $AllAnswers =  RfpAnswer::orderBy('order', 'asc')->get();
                $AllBundles = RfpBundle::orderBy('bundle', 'asc')->get();
                $AllProcess = RfpProcess::orderBy('order', 'asc')->get();

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
                    'AllProcess' => $AllProcess,
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
        $Project = ProjectFiles::findOrFail($id);
        if (Auth::user()->hasAnyPermission(['projects.all', 'projects.my', 'projects.all.manage',  'projects.all.add', 'projects.all.edit', 'projects.all.delete', 'projects.my.manage', 'projects.my.add', 'projects.my.edit', 'projects.my.delete'])) {   
            $query = ProjectRecord::query()->with('rfp_bundles');

            // Adicionando explicitamente a cláusula where para garantir que o filtro está correto
            $query->where('project_file_id', '=', $Project->id);
            
            // Aplicar filtros
            if ($request->has('keyWord') && !empty($request->keyWord)) {
                $query->where(function ($q) use ($request) {
                    $q->where('requisito', 'like', '%' . $request->keyWord . '%')
                      ->orWhere('processo', 'like', '%' . $request->keyWord . '%')
                      ->orWhere('subprocesso', 'like', '%' . $request->keyWord . '%');
                });
            }
            
            $processo = $request->processo === "null" ? null : $request->processo;
            if (filled($processo)) {
                $query->where('processo', 'like', '%' . $request->processo . '%');
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
        if (Auth::user()->hasAnyPermission(['projects.all.manage', 'projects.all.edit', 'projects.my.manage', 'projects.my.edit'])) {
            $ProjectRecord = ProjectRecord::findOrFail($id);
            if($request->resposta){
                $ProjectRecord->resposta = $request->resposta;
            }

            if($request->bundle){
                $ProjectRecord->bundle_id = $request->bundle;
            }
             
            if($request->processo_id){
                $Processo = RfpProcess::find($request->processo_id);
                $ProjectRecord->processo_id = $request->processo_id;
                $ProjectRecord->processo = $Processo->process;
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
    public function answerErrors(string $id)
    {
        $ProjectFile = ProjectFiles::with('rfp_bundles')->findOrFail($id);
        if (Auth::user()->hasAnyPermission(['projects.all', 'projects.my', 'projects.all.manage',  'projects.all.add', 'projects.all.edit', 'projects.all.delete', 'projects.my.manage', 'projects.my.add', 'projects.my.edit', 'projects.my.delete'])) {
            if($ProjectFile->status != "processando"){
                $Project = Project::with('user')->findOrFail($ProjectFile->project_id);
       
                $ListClassificacaoRecebidas = ProjectRecord::where('project_file_id', $ProjectFile->id)->groupBy('processo')->pluck('processo');
                

                $ListRespostaRecebidas = ProjectAnswer::whereHas('requisito', function($query) use ($id) {
                    $query->where('project_file_id', $id);
                })
                ->groupBy('aderencia_na_mesma_linha')
                ->pluck('aderencia_na_mesma_linha');
        
                $UsersDepartaments = UsersDepartaments::where('departament_type', '!=', 'Admin')->orderBy('departament', 'asc')->get();


                $AllAnswers = RfpAnswer::orderBy('order', 'asc')->get();
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

                $countIA = ProjectRecord::where('project_file_id', $ProjectFile->id)
                    ->where('status', 'respondido ia')
                    ->whereHas('answers', function ($query) {
                        $query->whereNotNull('id')
                            ->where('aderencia_na_mesma_linha', '!=', 'Desconhecido');
                    })
                    ->count();

                $countUser = ProjectRecord::where('project_file_id', $ProjectFile->id)
                ->where('status', 'user edit') 
                ->whereHas('answers', function ($query) {
                    $query->whereNotNull('id'); // Garante que answer_id está preenchido
                })
                ->whereHas('answers', function ($query) {
                    $query->where('aderencia_na_mesma_linha', '!=', 'Desconhecido');
                })
                ->count();

                
                $registrosSemResposta = $CountRecords - ($countIA + $countUser);
                $porcentagemSemResposta = ($registrosSemResposta / $CountRecords) * 100;

                $data = array(
                    'idProjectFile' => $id,
                    'Project' => $Project,
                    'ProjectFile' => $ProjectFile,
                    'UsersDepartaments' => $UsersDepartaments,
                    'ListClassificacao' => $ListClassificacaoRecebidas,
                    'ListRespostaRecebidas' => $ListRespostaRecebidas,
                    'ListProdutos' => $ListProdutos,
                    'AllBundles' => $AllBundles,
                    'AllModules' => $AllModules,
                    'AllAnswers' => $AllAnswers,
                    'CountRequisitos' => $CountRecords,
                    'CountAnswerIA' => $countIA,
                    'CountAnswerUser' => $countUser,
                    'progress' => $porcentagemSemResposta,
                    'registrosSemResposta' => $registrosSemResposta,
                    'CountCountRecordsResultado' => $CountRecords,
                );
                
                if($ProjectFile->status != "processando"){
                    return view('project.records.erro_answer')->with($data);
                }else{
                    return view('project.records.erro_answer')->with($data);
                }
            }else{
                // Caso a base esteja sendo processada não deixa fazer nada.
                session(['error' => 'Esta Base de Conhecimento está em processamentoe não pode ser editada.']);
                return redirect()->route('project.answer')->with('error', session('error'));
            }
        }
    }


    public function filterAnswerError(Request $request, string $id)
    { 
        $Project = ProjectFiles::findOrFail($id);
        if (Auth::user()->hasAnyPermission(['projects.all', 'projects.my', 'projects.all.manage',  'projects.all.add', 'projects.all.edit', 'projects.all.delete', 'projects.my.manage', 'projects.my.add', 'projects.my.edit', 'projects.my.delete'])) {
            //$query = ProjectAnswer::query()->with('rfp_bundles');

            $query = ProjectRecord::query()
            ->with(['rfp_bundles', 'answers'])
            ->where('project_file_id', '=', $Project->id)
            ->whereHas('answers', function($q) {
                $q->where('aderencia_na_mesma_linha', '=', 'Desconhecido');
            });

                            
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
    public function errors(string $id)
    {
        $ProjectFile = ProjectFiles::findOrFail($id);
        if (Auth::user()->hasAnyPermission(['projects.all', 'projects.my', 'projects.all.manage',  'projects.all.add', 'projects.all.edit', 'projects.all.delete', 'projects.my.manage', 'projects.my.add', 'projects.my.edit', 'projects.my.delete'])) {
            $Project = Project::findOrFail($ProjectFile->project_id);

            $ListClassificacaoRecebidas = ProjectRecord::where('project_file_id', $ProjectFile->id)->groupBy('processo')->pluck('processo');

            $AllAnswers =  RfpAnswer::orderBy('order', 'asc')->get();
            $AllBundles = RfpBundle::orderBy('bundle', 'asc')->get();

            $ListProdutos = DB::table('project_records')
                ->leftJoin('rfp_bundles', 'project_records.bundle_id', '=', 'rfp_bundles.bundle_id')
                ->where('project_records.project_file_id', $ProjectFile->id)
                ->where(function ($query) {
                    $query->orWhereNull('project_records.processo_id')
                        ->orWhere('project_records.processo_id', '');
                })
                ->groupBy('project_records.bundle_id')
                ->select('project_records.bundle_id', 'rfp_bundles.bundle')
                ->groupBy('rfp_bundles.bundle') // Agrupa pelo ID do bundle
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
        $Project = ProjectFiles::findOrFail($id);
        if (Auth::user()->hasAnyPermission(['projects.all', 'projects.my', 'projects.all.manage',  'projects.all.add', 'projects.all.edit', 'projects.all.delete', 'projects.my.manage', 'projects.my.add', 'projects.my.edit', 'projects.my.delete'])) {
            $query = ProjectRecord::query()->with('rfp_bundles');

            // Adicionando explicitamente a cláusula where para garantir que o filtro está correto
            $query->where('project_file_id', '=', $Project->id);

            // Pelo menos uma das três condições opcionais deve ser verdadeira
            $query->where(function($q) {
                $q->where(function($subQ) {
                    $subQ->whereNull('bundle_id')
                        ->orWhere('bundle_id', '');
                })
                ->orWhere(function($subQ) {
                    $subQ->whereNull('processo_id')
                        ->orWhere('processo_id', '');
                }) ;
            });

                        
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
        $Project = ProjectFiles::findOrFail($id);
        if (Auth::user()->hasAnyPermission(['projects.all', 'projects.my', 'projects.all.manage',  'projects.all.add', 'projects.all.edit', 'projects.all.delete', 'projects.my.manage', 'projects.my.add', 'projects.my.edit', 'projects.my.delete'])) {
            
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
     * Página de Processamento da BASE
     */
    public function processingAnswer(Request $request, string $id)
    {
        $Project = ProjectFiles::findOrFail($id);
        if (Auth::user()->hasAnyPermission(['projects.all', 'projects.my', 'projects.all.manage',  'projects.all.add', 'projects.all.edit', 'projects.all.delete', 'projects.my.manage', 'projects.my.add', 'projects.my.edit', 'projects.my.delete'])) {
            
            if($Project->status == "processado"){
                $Project->status = "concluído";
                $Project->save();

                return view('project.records.processingAnswer');
            }else{
                if($Project->status == "em processamento"){
                    session(['error' => 'Este Projeto já está em processamento.']);
                }else if($Project->status == "processada" || $Project->status == "concluída"){
                    session(['error' => 'Este Projeto já está processada e Concluída.']);
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
        if (Auth::user()->hasAnyPermission(['projects.all', 'projects.my', 'projects.all.manage',  'projects.all.add', 'projects.all.edit', 'projects.all.delete', 'projects.my.manage', 'projects.my.add', 'projects.my.edit', 'projects.my.delete'])) {
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
        if (Auth::user()->hasAnyPermission(['projects.all', 'projects.my', 'projects.all.manage',  'projects.all.add', 'projects.all.edit', 'projects.all.delete', 'projects.my.manage', 'projects.my.add', 'projects.my.edit', 'projects.my.delete'])) {
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
        if (Auth::user()->hasAnyPermission(['projects.all', 'projects.my', 'projects.all.manage',  'projects.all.add', 'projects.all.edit', 'projects.all.delete', 'projects.my.manage', 'projects.my.add', 'projects.my.edit', 'projects.my.delete'])) {
            if($ProjectFile->status != "processando"){
                $Project = Project::with('user')->findOrFail($ProjectFile->project_id);
       
                $ListClassificacaoRecebidas = ProjectRecord::where('project_file_id', $ProjectFile->id)->groupBy('processo')->pluck('processo');
                

                $ListRespostaRecebidas = ProjectAnswer::whereHas('requisito', function($query) use ($id) {
                    $query->where('project_file_id', $id);
                })
                ->groupBy('aderencia_na_mesma_linha')
                ->pluck('aderencia_na_mesma_linha');
        
                $UsersDepartaments = UsersDepartaments::where('departament_type', '!=', 'Admin')->orderBy('departament', 'asc')->get();


                $AllAnswers = RfpAnswer::orderBy('order', 'asc')->get();
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


                $countIA = ProjectRecord::where('project_file_id', $ProjectFile->id)
                    ->where('status', 'respondido ia')
                    ->whereHas('answers', function ($query) {
                        $query->whereNotNull('id')
                            ->where('aderencia_na_mesma_linha', '!=', 'Desconhecido');
                    })
                    ->count();

                $countUser = ProjectRecord::where('project_file_id', $ProjectFile->id)
                ->where('status', 'user edit') 
                ->whereHas('answers', function ($query) {
                    $query->whereNotNull('id'); // Garante que answer_id está preenchido
                })
                ->whereHas('answers', function ($query) {
                    $query->where('aderencia_na_mesma_linha', '!=', 'Desconhecido');
                })
                ->count();

                
                $registrosSemResposta = $CountRecords - ($countIA + $countUser);
                $porcentagemSemResposta = ($registrosSemResposta / $CountRecords) * 100;

                $data = array(
                    'idProjectFile' => $id,
                    'Project' => $Project,
                    'ProjectFile' => $ProjectFile,
                    'UsersDepartaments' => $UsersDepartaments,
                    'ListClassificacao' => $ListClassificacaoRecebidas,
                    'ListRespostaRecebidas' => $ListRespostaRecebidas,
                    'ListProdutos' => $ListProdutos,
                    'AllBundles' => $AllBundles,
                    'AllModules' => $AllModules,
                    'AllAnswers' => $AllAnswers,
                    'CountRequisitos' => $CountRecords,
                    'CountAnswerIA' => $countIA,
                    'CountAnswerUser' => $countUser,
                    'progress' => $porcentagemSemResposta,
                    'registrosSemResposta' => $registrosSemResposta,
                    'CountCountRecordsResultado' => $CountRecords,
                );
                
                if($ProjectFile->status != "processado"){
                    return view('project.records.answerView')->with($data);
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
        if (Auth::user()->hasAnyPermission(['projects.all', 'projects.my', 'projects.all.manage',  'projects.all.add', 'projects.all.edit', 'projects.all.delete', 'projects.my.manage', 'projects.my.add', 'projects.my.edit', 'projects.my.delete'])) {
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
            
            $processo = $request->processo === "null" ? null : $request->processo;
            if (filled($processo)) {
                $query->where('processo', 'like', '%' . $request->processo . '%');
            }
             
            $resposta = $request->answer === "null" ? null : $request->answer;
            if (filled($resposta)) {
                $query->whereHas('answers', function($q) use ($request) {
                    $q->where('aderencia_na_mesma_linha', 'like', '%' . $request->answer . '%');
                });
            }

            $product = $request->product === "null" ? null : $request->product;
            if (filled($product)) {
                $query->where('bundle_old', 'like', '%' . $request->product . '%');
            }

            // Validação e formatação do min_percent
            if ($request->filled(['min_percent', 'max_percent'])) {
                $minPercent = $request->min_percent;
                $maxPercent = $request->max_percent;
        
                $query->whereHas('answers', function ($q) use ($minPercent, $maxPercent) {
                    $q->whereRaw('CAST(REPLACE(acuracidade_porcentagem, "%", "") AS UNSIGNED) >= ?', [$minPercent])
                      ->whereRaw('CAST(REPLACE(acuracidade_porcentagem, "%", "") AS UNSIGNED) <= ?', [$maxPercent]);
                });
            }
        
            //$results = $query->with('answers')->get();
            
            // Paginação
            $records = $query->with('answers')->paginate(100);

            // Retornar dados em JSON
            return response()->json($records);
        }
    }




    public function references(string $id)
    { 
        $Project = ProjectRecord::findOrFail($id);
        if (Auth::user()->hasAnyPermission(['projects.all', 'projects.my', 'projects.all.manage',  'projects.all.add', 'projects.all.edit', 'projects.all.delete', 'projects.my.manage', 'projects.my.add', 'projects.my.edit', 'projects.my.delete'])) { 
            //$query = ProjectRecord::query()->with('rfp_bundles');

            $ProjectAnswer = ProjectAnswer::where('id', '=', $Project->project_answer_id)->first();
            $Referencia = $ProjectAnswer->referencia;

            //$linhas = array_map('trim', explode("\n", $Referencia));

            $linhas = preg_split('/[\n;]+/', $Referencia, -1, PREG_SPLIT_NO_EMPTY);
            $linhas = array_map('trim', $linhas);


            $dados = [];

            foreach($linhas as $linha) {
                if(!empty($linha)) {
                    $partes = explode(':', $linha, 2);
                    if(count($partes) == 2) {
                        $chave = trim($partes[0]);
                        $valor = trim($partes[1]);
                        $dados[$chave] = $valor;
                    }
                }
            }         
            
            
            $KnowledgeAll = KnowledgeRecord::with('rfp_bundles')->where('id_record', '=', $dados['ID Registro'])->get();
            $KnowledgeRecords = [];

            if(count($KnowledgeAll) >= 1 ){
                foreach ($KnowledgeAll as $key => $Knowledge) {
                    $KnowledgeRecords[] = $Knowledge->toArray();  
                }
            }else{
                $KnowledgeAll = ProjectRecord::with('rfp_bundles')->where('id', '=', $dados['ID Registro'])->get();
                foreach ($KnowledgeAll as $key => $Knowledge) {
                    $KnowledgeRecords[] = $Knowledge->toArray();               
                }
            }




            $data = array(
                'ReferenciasIA' => $dados,
                'ReferenciasBanco' => $KnowledgeRecords,
            );

  
            // Retornar dados em JSON
            return response()->json($data);
        }
    }




    public function detail(string $id)
    { 
        $Project = ProjectRecord::findOrFail($id);
        if (Auth::user()->hasAnyPermission(['projects.all', 'projects.my', 'projects.all.manage',  'projects.all.add', 'projects.all.edit', 'projects.all.delete', 'projects.my.manage', 'projects.my.add', 'projects.my.edit', 'projects.my.delete'])) {
            //$query = ProjectRecord::query()->with('rfp_bundles');
            $ProjectAnswer = ProjectAnswer::where('id', '=', $Project->project_answer_id)->first()->toArray();

            if($ProjectAnswer['aderencia_na_mesma_linha'] != 'Desconhecido'){
                $RfpAnswer = RfpAnswer::where('anwser', '=', $ProjectAnswer['aderencia_na_mesma_linha'])->first();
                $ProjectAnswer['answer_id'] =  $RfpAnswer->id;            
            }
           
            //$RfpBundle = RfpBundle::where('bundle', '=', $ProjectAnswer->linha_produto)->first();         
            //$ProjectAnswer['bundle_id'] =  $RfpBundle->bundle_id;
            
            $ProjectHistory = ProjectHistory::where('answer_id', '=', $ProjectAnswer['id'])->with('user')->get();

            $data = array(
                'ProjectAnswer' => $ProjectAnswer,
                'ProjectHistory' => $ProjectHistory,
            );

            // Retornar dados em JSON
            return response()->json($data);
        }
    }




    public function historyUpdate(Request $request, string $id)
    { 
            $Project = ProjectRecord::find($id);
            $ProjectAnswer = ProjectAnswer::where('id', '=', $Project->project_answer_id)->first();

            $Produto = RfpBundle::find($request->produto);
            $Resposta = RfpAnswer::find($request->resposta);

            $History = new ProjectHistory();          
            $History->old_answer = $ProjectAnswer->aderencia_na_mesma_linha;
            $History->new_answer = $Resposta->anwser;
            $History->old_module = $ProjectAnswer->modulo;
            $History->new_module = $request->modulo;
            $History->old_observation = $ProjectAnswer->observacao;
            $History->new_observation = $request->observacao;
            $History->old_bundle = $ProjectAnswer->linha_produto;
            $History->new_bundle = $Produto->bundle;
            $History->user_id = Auth::id();
            $History->answer_id = $ProjectAnswer->id;
            $History->save();
            
            $Project->status = "user edit";
            $Project->save();

            $ProjectAnswer->aderencia_na_mesma_linha = $Resposta->anwser;
            $ProjectAnswer->linha_produto = $Produto->bundle;
            $ProjectAnswer->bundle_id = $Produto->bundle_id;
            $ProjectAnswer->modulo = $request->modulo;
            $ProjectAnswer->observacao = $request->observacao;
            $ProjectAnswer->save();

           
            
            // Retornar dados em JSON
            return response()->json('save');
        
    }

    


}
