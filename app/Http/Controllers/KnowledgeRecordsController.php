<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\KnowledgeBase;
use App\Models\KnowledgeRecord;
use App\Models\RfpBundle;
use App\Models\RfpAnswer;
use App\Models\UsersDepartaments;
use App\Imports\KnowledgeBaseImport;
use App\Imports\KnowledgeBaseInfoImport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use DateTime;


class KnowledgeRecordsController extends Controller
{
    
    /**
    * Valida as Informações Gerais de um REGISTRO ESPECIFICO
    */
    public function index(string $id)
    {
        $KnowledgeBase = KnowledgeBase::findOrFail($id);
        if($KnowledgeBase->user_id == Auth::id() || Auth::user()->role->role_priority >= 90){   
            if($KnowledgeBase->status != "processando"){
                $ListClassificacaoRecebidas = KnowledgeRecord::where('knowledge_base_id', $KnowledgeBase->id)->groupBy('processo')->pluck('processo');
                $ListRespostaRecebidas = KnowledgeRecord::where('knowledge_base_id', $KnowledgeBase->id)->groupBy('resposta')->pluck('resposta');
                $ListProdutosRecebidas = KnowledgeRecord::where('knowledge_base_id', $KnowledgeBase->id)->groupBy('bundle_old')->pluck('bundle_old');
                $UsersDepartaments = UsersDepartaments::where('departament_type', '!=', 'Admin')->orderBy('departament', 'asc')->get();

                $AllAnswers =  RfpAnswer::orderBy('order', 'asc')->get();
                $AllBundles = RfpBundle::orderBy('bundle', 'asc')->get();

                $ListProdutos = DB::table('knowledge_records')
                ->leftJoin('rfp_bundles', 'knowledge_records.bundle_id', '=', 'rfp_bundles.bundle_id')
                ->where('knowledge_records.knowledge_base_id', $KnowledgeBase->id)
                ->groupBy('knowledge_records.bundle_id')
                ->select('knowledge_records.bundle_id', 'rfp_bundles.bundle')
                ->groupBy('rfp_bundles.bundle') // Agrupa pelo ID do bundle
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
                    'UsersDepartaments' => $UsersDepartaments,
                    'ListClassificacao' => $ListClassificacaoRecebidas,
                    'ListResposta' => $ListRespostaRecebidas,
                    'ListProdutosRecebidas' => $ListProdutosRecebidas,
                    'ListProdutos' => $ListProdutos,
                    'AllBundles' => $AllBundles,
                    'AllAnswers' => $AllAnswers,
                    'CountCountRecordsResultado' => $CountRecords,
                );
                
                if($KnowledgeBase->status != "processando"){
                    return view('knowledge.records.list')->with($data);
                }else{
                    return view('knowledge.records.view')->with($data);
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
            $KnowledgeBase = KnowledgeBase::findOrFail($id);
            $query = KnowledgeRecord::query()->with('rfp_bundles');

            // Adicionando explicitamente a cláusula where para garantir que o filtro está correto
            $query->where('knowledge_base_id', '=', $KnowledgeBase->id);

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



    public function updateDetails(Request $request, string $id)
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
    * Valida as Informações Gerais de um REGISTRO ESPECIFICO
    */
    public function errors(string $id)
    {
        $KnowledgeBase = KnowledgeBase::findOrFail($id);
        if($KnowledgeBase->user_id == Auth::id() || Auth::user()->role->role_priority >= 90){  
            
            $ListClassificacaoRecebidas = KnowledgeRecord::where('knowledge_base_id', $KnowledgeBase->id)->groupBy('classificacao')->pluck('classificacao');
            $ListRespostaRecebidas = KnowledgeRecord::where('knowledge_base_id', $KnowledgeBase->id)->groupBy('resposta')->pluck('resposta');
            $ListProdutosRecebidas = KnowledgeRecord::where('knowledge_base_id', $KnowledgeBase->id)->groupBy('bundle_old')->pluck('bundle_old');
            $UsersDepartaments = UsersDepartaments::where('departament_type', '!=', 'Admin')->orderBy('departament', 'asc')->get();

            $AllAnswers =  RfpAnswer::orderBy('order', 'asc')->get();
            $AllBundles = RfpBundle::orderBy('bundle', 'asc')->get();

            $ListProdutos = DB::table('knowledge_records')
                ->leftJoin('rfp_bundles', 'knowledge_records.bundle_id', '=', 'rfp_bundles.bundle_id')
                ->where('knowledge_records.knowledge_base_id', $KnowledgeBase->id)
                ->where(function ($query) {
                    $query->orWhereNull('knowledge_records.bundle_id')
                        ->orWhereNull('knowledge_records.classificacao')
                        ->orWhereNull('knowledge_records.requisito')
                        ->orWhereNull('knowledge_records.resposta')
                        ->orWhereNull('knowledge_records.resposta2')
                        ->orWhere('knowledge_records.classificacao', '')
                        ->orWhere('knowledge_records.requisito', '')
                        ->orWhere('knowledge_records.resposta', '')
                        ->orWhere('knowledge_records.resposta2', '');
                })
                ->groupBy('knowledge_records.bundle_id', 'rfp_bundles.bundle')
                ->select('knowledge_records.bundle_id', 'rfp_bundles.bundle')
                ->get();


                $CountRecordsEmpty = KnowledgeRecord::where('knowledge_base_id', $KnowledgeBase->id)->whereNull('bundle_id')->orWhere('bundle_id', '')->count();
                $CountRecords = KnowledgeRecord::where('knowledge_base_id', $KnowledgeBase->id)->count();
            

                $data = array(
                    'title' => 'Todos Arquivos',
                    'idKnowledgeBase' => $id,
                    'KnowledgeBase' => $KnowledgeBase,
                    'UsersDepartaments' => $UsersDepartaments,
                    'ListClassificacao' => $ListClassificacaoRecebidas,
                    'ListResposta' => $ListRespostaRecebidas,
                    'ListProdutosRecebidas' => $ListProdutosRecebidas,
                    'ListProdutos' => $ListProdutos,
                    'AllBundles' => $AllBundles,
                    'AllAnswers' => $AllAnswers,
                    'CountCountRecordsResultado' => $CountRecords,
                    'CountCountRecordsEmpty' => $CountRecordsEmpty,
                );

            return view('knowledge.records.erro')->with($data);
        }
    }


    public function filterError(Request $request, string $id)
    { 
        if(Auth::user()->role->role_priority >= 90){       
            $KnowledgeBase = KnowledgeBase::findOrFail($id);
            $query = KnowledgeRecord::query()->with('rfp_bundles');

            // Adicionando explicitamente a cláusula where para garantir que o filtro está correto
            $query->where('knowledge_base_id', '=', $KnowledgeBase->id);
            $query->whereNull('bundle_id')->orWhere('bundle_id', ''); // Para strings vazias
                        
            // Paginação
            $records = $query->paginate(100);

            return response()->json($records);
        }
    }



    /**
     * Página de Processamento da BASE
     */
    public function processing(Request $request, string $id)
    {
        if(Auth::user()->role->role_priority >= 90){     
            $KnowledgeBase = KnowledgeBase::findOrFail($id);
            if($KnowledgeBase->status == "não enviado"){
                $KnowledgeBase->status = "processando";
                $KnowledgeBase->save();
    
                return view('knowledge.records.processing');
            }else{
                if($KnowledgeBase->status == "processando"){
                    session(['error' => 'Esta Base de Conhecimento já está em processamento.']);
                }else if($KnowledgeBase->status == "processando"){
                    session(['error' => 'Esta Base de Conhecimento já está processada e Concluída.']);
                }
                
                // Redireciona para outra página, ou de volta com a mensagem
                return redirect()->route('knowledge.list')->with('error', session('error'));
            }
        }
    }


     /**
     * Remove the specified resource from storage.
     */
    public function filterRemove(string $id)
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


    









}
