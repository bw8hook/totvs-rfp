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
use App\Models\RfpProcess;
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
    public function index(string $id, string $Record_id = null)
    {
        $KnowledgeBase = KnowledgeBase::findOrFail($id);
       if (Auth::user()->hasRole('Administrador')) {
            if($KnowledgeBase->status != "processando"){
                $ListClassificacaoRecebidas = KnowledgeRecord::where('knowledge_base_id', $KnowledgeBase->id)->groupBy('processo')->pluck('processo');
                $ListRespostaRecebidas = KnowledgeRecord::where('knowledge_base_id', $KnowledgeBase->id)->groupBy('resposta')->pluck('resposta');
                $ListProdutosRecebidas = KnowledgeRecord::where('knowledge_base_id', $KnowledgeBase->id)->groupBy('bundle_old')->pluck('bundle_old');
                $UsersDepartaments = UsersDepartaments::where('departament_type', '!=', 'Admin')->orderBy('departament', 'asc')->get();

                $AllAnswers =  RfpAnswer::orderBy('order', 'asc')->get();
                $AllProcess =  RfpProcess::orderBy('order', 'asc')->get();
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
                    'AllProcess' => $AllProcess,
                    'CountCountRecordsResultado' => $CountRecords,
                    'Record_id' => $Record_id
                );
                
                if($KnowledgeBase->status == "não enviado"){
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
        $Record_id = $request->record_id;
        $perPage = 100; // Seu número de itens por página
        $KnowledgeBase = KnowledgeBase::findOrFail($id);

        if (Auth::user()->hasAnyPermission(['knowledge.manage', 'knowledge.add', 'knowledge.edit', 'knowledge.delete'])) {
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

            // Aplicar ordenação (substitua 'id_record' pelo campo que você usa para ordenar)
            $query->orderBy('id_record', 'asc');

            $page = null;
            if ($Record_id) {
                // Clone a query para calcular a posição
                $positionQuery = clone $query;
                
                // Calcular a posição do registro
                $position = $positionQuery->where('id_record', '<', $Record_id)->count();

                // Calcular a página
                $page = floor($position / $perPage) + 1;

                // Definir a página na request para a paginação
                $request->merge(['page' => $page]);
            }

            // Paginação
            $records = $query->paginate($perPage);

            $data = [
                'response' => $records,
            ];

            if ($Record_id) {
                $data['id'] = $Record_id;
                $data['page'] = $page;
            }

            // Retornar dados em JSON
            return response()->json($data);
        }
    }


    public function updateDetails(Request $request, string $id)
    { 
        // Valida a Permissão do usuário
        if (Auth::user()->hasAnyPermission(['knowledge.manage', 'knowledge.add', 'knowledge.edit', 'knowledge.delete'])) {
            $KnowledgeRecords = KnowledgeRecord::findOrFail($id);
            if($request->resposta){
                $KnowledgeRecords->resposta = $request->resposta;
            }

            if($request->bundle){
                $KnowledgeRecords->bundle_id = $request->bundle;
            }

            if($request->processo){
                $KnowledgeProcess = RfpProcess::findOrFail($request->processo);
                $KnowledgeRecords->processo_id = $request->processo;
                $KnowledgeRecords->processo = $KnowledgeProcess->process;
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
        if (Auth::user()->hasAnyPermission(['knowledge.manage', 'knowledge.add', 'knowledge.edit', 'knowledge.delete'])) {
            
            $ListClassificacaoRecebidas = KnowledgeRecord::where('knowledge_base_id', $KnowledgeBase->id)->groupBy('processo')->pluck('processo');
            $ListRespostaRecebidas = KnowledgeRecord::where('knowledge_base_id', $KnowledgeBase->id)->groupBy('resposta')->pluck('resposta');
            $ListProdutosRecebidas = KnowledgeRecord::where('knowledge_base_id', $KnowledgeBase->id)->groupBy('bundle_old')->pluck('bundle_old');
            $UsersDepartaments = UsersDepartaments::where('departament_type', '!=', 'Admin')->orderBy('departament', 'asc')->get();

            $AllAnswers =  RfpAnswer::orderBy('order', 'asc')->get();
            $AllProcess =  RfpProcess::orderBy('order', 'asc')->get();
            $AllBundles = RfpBundle::orderBy('bundle', 'asc')->get();

            $ListProdutos = DB::table('knowledge_records')
                ->leftJoin('rfp_bundles', 'knowledge_records.bundle_id', '=', 'rfp_bundles.bundle_id')
                ->where('knowledge_records.knowledge_base_id', $KnowledgeBase->id)
                ->where(function ($query) {
                    $query->orWhereNull('knowledge_records.bundle_id')
                        ->orWhereNull('knowledge_records.processo')
                        ->orWhereNull('knowledge_records.requisito')
                        ->orWhereNull('knowledge_records.resposta')
                        ->orWhereNull('knowledge_records.modulo')
                        ->orWhere('knowledge_records.processo', '')
                        ->orWhere('knowledge_records.requisito', '')
                        ->orWhere('knowledge_records.resposta', '')
                        ->orWhere('knowledge_records.modulo', '');
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
                    'AllProcess' => $AllProcess,
                    'CountCountRecordsResultado' => $CountRecords,
                    'CountCountRecordsEmpty' => $CountRecordsEmpty,
                );

            return view('knowledge.records.erro')->with($data);
        }
    }


    public function filterError(Request $request, string $id)
    { 
        if (Auth::user()->hasAnyPermission(['knowledge.manage', 'knowledge.add', 'knowledge.edit', 'knowledge.delete'])) {
            $KnowledgeBase = KnowledgeBase::findOrFail($id);
            $query = KnowledgeRecord::query()->with('rfp_bundles');

            // Adicionando explicitamente a cláusula where para garantir que o filtro está correto
            $query->where('knowledge_base_id', '=', $KnowledgeBase->id);

            // Pelo menos uma das três condições opcionais deve ser verdadeira
            $query->where(function($q) {
                $q->where(function($subQ) {
                    $subQ->whereNull('bundle_id')
                        ->orWhere('bundle_id', '');
                })
                ->orWhere(function($subQ) {
                    $subQ->whereNull('processo_id')
                        ->orWhere('processo_id', '');
                })
                ->orWhere(function($subQ) {
                    $subQ->whereNotIn('resposta', ['Atende', 'Não Atende', 'Atende Parcial', 'Customizável'])
                        ->orWhereNull('resposta');
                });
            });


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
        if (Auth::user()->hasAnyPermission(['knowledge.manage', 'knowledge.add', 'knowledge.edit', 'knowledge.delete'])) {
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
        if (Auth::user()->hasAnyPermission(['knowledge.manage', 'knowledge.add', 'knowledge.edit', 'knowledge.delete'])) {
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
