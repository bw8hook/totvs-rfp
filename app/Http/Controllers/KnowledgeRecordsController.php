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
        if (Auth::user()->hasAnyPermission(['knowledge.manage', 'knowledge.add', 'knowledge.edit', 'knowledge.delete'])) {
            if($KnowledgeBase->status != "processando"){
                $ListClassificacaoRecebidas = KnowledgeRecord::where('knowledge_base_id', $KnowledgeBase->id)->groupBy('processo')->pluck('processo');
                $ListRespostaRecebidas = KnowledgeRecord::where('knowledge_base_id', $KnowledgeBase->id)->groupBy('resposta')->pluck('resposta');
                $ListProdutosRecebidas = KnowledgeRecord::where('knowledge_base_id', $KnowledgeBase->id)->groupBy('bundle_old')->pluck('bundle_old');
                $UsersDepartaments = UsersDepartaments::where('departament_type', '!=', 'Admin')->orderBy('departament', 'asc')->get();

                $AllAnswers =  RfpAnswer::orderBy('order', 'asc')->get();
                $AllProcess =  RfpProcess::orderBy('order', 'asc')->get();
                $AllBundles = RfpBundle::orderBy('bundle', 'asc')->get();

            //     $ListProdutos = DB::table('knowledge_records')
            //     //->leftJoin('rfp_bundles', 'knowledge_records.bundle_id', '=', 'rfp_bundles.bundle_id')
            //     ->where('knowledge_records.knowledge_base_id', $KnowledgeBase->id)
            //    // ->groupBy('knowledge_records.bundle_id')
            //     //->select('knowledge_records.bundle_id', 'rfp_bundles.bundle')
            //    // ->groupBy('rfp_bundles.bundle') // Agrupa pelo ID do bundle
            //     ->get();


                $ListProdutos = DB::table('knowledge_records as kr')
                ->leftJoin('knowledge_records_bundles as krb', 'kr.id_record', '=', 'krb.knowledge_record_id')
                ->leftJoin('rfp_bundles as rb', 'krb.bundle_id', '=', 'rb.bundle_id')
                ->where('kr.knowledge_base_id', $KnowledgeBase->id)
                ->select(
                    'kr.*',
                    'krb.bundle_id',
                    'krb.old_bundle',
                    'krb.bundle_status',
                    'rb.bundle as bundle_name'
                )
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
            
            $query = KnowledgeRecord::query()->with('bundles');
            
            // Filtro base
            $query->where('knowledge_base_id', $KnowledgeBase->id);
            
            // Filtro de palavra-chave
            if ($request->filled('keyWord')) {
                $keyWord = $request->keyWord;
                $query->where(function($q) use ($keyWord) {
                    $q->where('requisito', 'like', '%' . $keyWord . '%')
                      ->orWhere('observacao', 'like', '%' . $keyWord . '%')
                      ->orWhere('processo', 'like', '%' . $keyWord . '%')
                      ->orWhere('subprocesso', 'like', '%' . $keyWord . '%')
                      ->orWhere('resposta', 'like', '%' . $keyWord . '%')
                      ->orWhere('modulo', 'like', '%' . $keyWord . '%')
                      ->orWhereHas('bundles', function($query) use ($keyWord) {
                          $query->where('bundle', 'like', '%' . $keyWord . '%')
                                ->orWhere('knowledge_records_bundles.old_bundle', 'like', '%' . $keyWord . '%');
                      });
                });
            }
            
            // Filtro de processo
            if ($request->filled('processo') && $request->processo !== "null") {
                $query->where('processo', 'like', '%' . $request->processo . '%');
            }
            
            // Filtro de resposta
            if ($request->filled('resposta') && $request->resposta !== "null") {
                $query->where('resposta', 'like', '%' . $request->resposta . '%');
            }
            
            // Filtro de produto
            if ($request->filled('product') && $request->product !== "null") {
                $query->whereHas('bundles', function($query) use ($request) {
                    $query->where('bundle', 'like', '%' . $request->product . '%')
                          ->orWhere('knowledge_records_bundles.old_bundle', 'like', '%' . $request->product . '%');
                });
            }
            
            // Ordenação
            $query->orderBy('id_record', 'asc');
            
            // Cálculo de página para Record_id específico
            if ($Record_id) {
                $position = (clone $query)
                    ->where('id_record', '<', $Record_id)
                    ->count();
                
                $page = floor($position / $perPage) + 1;
                $request->merge(['page' => $page]);
            }
            
            // Paginação com transformação dos dados
            $records = $query->paginate($perPage);
            

            // Na transformação
            $records->getCollection()->transform(function ($record) {
                $recordArray = $record->toArray(); // Mantém todos os campos originais

                // Sobrescreve apenas o campo 'bundles' com a versão separada
                $recordArray['bundles'] = [
                    'principais' => $record->bundles
                        ->where('pivot.bundle_status', 'principal')
                        ->values()
                        ->map(function($bundle) {
                            return [
                                'id' => $bundle->bundle_id,
                                'name' => $bundle->bundle,
                                'status' => $bundle->pivot->bundle_status,
                                'old_bundle' => $bundle->pivot->old_bundle
                            ];
                        })->toArray(),
                        
                    'adicionais' => $record->bundles
                        ->where('pivot.bundle_status', 'adicional')
                        ->values()
                        ->map(function($bundle) {
                            return [
                                'id' => $bundle->bundle_id,
                                'name' => $bundle->bundle,
                                'status' => $bundle->pivot->bundle_status,
                                'old_bundle' => $bundle->pivot->old_bundle
                            ];
                        })->toArray()
                ];

                return $recordArray;
            });


            
            $data = [
                'response' => $records
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
                $bundlePrincipalAnterior = DB::table('knowledge_records_bundles')
                    ->where('knowledge_record_id', $KnowledgeRecords->id_record)
                    ->where('bundle_status', 'principal')
                    ->join('rfp_bundles', 'knowledge_records_bundles.bundle_id', '=', 'rfp_bundles.bundle_id')
                    ->select('rfp_bundles.*', 'knowledge_records_bundles.*')
                    ->first();

                // Remove o bundle principal atual
                DB::table('knowledge_records_bundles')
                    ->where('knowledge_record_id', $KnowledgeRecords->id_record) // certifique-se de usar a chave correta
                    ->where('bundle_status', 'principal')
                    ->delete();

                // Depois adiciona o novo
                $KnowledgeRecords->bundles()->attach($request->bundle, [
                    'knowledge_record_id' => $KnowledgeRecords->id_record, // certifique-se de usar a chave correta
                    'old_bundle' => $bundlePrincipalAnterior ? $bundlePrincipalAnterior->bundle : null,
                    'bundle_status' => 'principal',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                


                // Faz o sync
                //$KnowledgeRecords->bundles()->sync($bundlesData);


                //$KnowledgeRecords->bundles()->sync($request->bundle);
                //$KnowledgeRecords->bundle_id = $request->bundle;
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

            // $ListProdutos = DB::table('knowledge_records')
            //     ->leftJoin('rfp_bundles', 'knowledge_records.bundle_id', '=', 'rfp_bundles.bundle_id')
            //     ->where('knowledge_records.knowledge_base_id', $KnowledgeBase->id)
            //     ->where(function ($query) {
            //         $query->orWhereNull('knowledge_records.processo')
            //             ->orWhereNull('knowledge_records.requisito')
            //             ->orWhereNull('knowledge_records.resposta')
            //             ->orWhereNull('knowledge_records.modulo')
            //             ->orWhere('knowledge_records.processo', '')
            //             ->orWhere('knowledge_records.requisito', '')
            //             ->orWhere('knowledge_records.resposta', '')
            //             ->orWhere('knowledge_records.modulo', '');
            //     })
            //     ->groupBy('knowledge_records.bundle_id', 'rfp_bundles.bundle')
            //     ->select('knowledge_records.bundle_id', 'rfp_bundles.bundle')
            //     ->get();


                //$CountRecordsEmpty = KnowledgeRecord::where('knowledge_base_id', $KnowledgeBase->id)->whereNull('bundle_id')->orWhere('bundle_id', '')->count();
                $CountRecords = KnowledgeRecord::where('knowledge_base_id', $KnowledgeBase->id)->count();
            

                $data = array(
                    'title' => 'Todos Arquivos',
                    'idKnowledgeBase' => $id,
                    'KnowledgeBase' => $KnowledgeBase,
                    'UsersDepartaments' => $UsersDepartaments,
                    'ListClassificacao' => $ListClassificacaoRecebidas,
                    'ListResposta' => $ListRespostaRecebidas,
                    'ListProdutosRecebidas' => $ListProdutosRecebidas,
                    'AllBundles' => $AllBundles,
                    'AllAnswers' => $AllAnswers,
                    'AllProcess' => $AllProcess,
                    'CountCountRecordsResultado' => $CountRecords,
                    'CountCountRecordsEmpty' => 0,
                );

            return view('knowledge.records.erro')->with($data);
        }
    }


    public function filterError(Request $request, string $id)
    { 
        if (Auth::user()->hasAnyPermission(['knowledge.manage', 'knowledge.add', 'knowledge.edit', 'knowledge.delete'])) {
            $KnowledgeBase = KnowledgeBase::findOrFail($id);
            $query = KnowledgeRecord::query()->with('bundles');

            // Adicionando explicitamente a cláusula where para garantir que o filtro está correto
            $query->where('knowledge_base_id', '=', $KnowledgeBase->id);

            
            // Pelo menos uma das três condições opcionais deve ser verdadeira
            $query->where(function($q) use ($request) {
                $q->where(function($subQ) {
                    // Condição 1: Resposta não padrão ou nula
                    $subQ->whereNotIn('resposta', ['Atende', 'Não Atende', 'Atende Parcial', 'Customizável'])
                        ->orWhereNull('resposta');
                })
                ->orWhere(function($subQ) {
                    // Condição 2: Processo ID nulo
                    $subQ->whereNull('processo_id');
                })
                ->orWhere(function($subQ) {
                    // Condição 3: Bundles nulos
                    $subQ->whereDoesntHave('bundles')
                         ->orWhereHas('bundles', function($bundleQuery) {
                             $bundleQuery->whereNull('knowledge_records_bundles.bundle_id');
                         });
                });
            });


             // Filtro de produto
             if ($request->filled('product') && $request->product !== "null") {
                $query->whereHas('bundles', function($query) use ($request) {
                    $query->where('bundle', 'like', '%' . $request->product . '%')
                          ->orWhere('knowledge_records_bundles.old_bundle', 'like', '%' . $request->product . '%');
                });
            }


            // Paginação
             $records = $query->paginate(100);
            
             // Na transformação
             $records->getCollection()->transform(function ($record) {
                 $recordArray = $record->toArray(); // Mantém todos os campos originais
 
                 // Sobrescreve apenas o campo 'bundles' com a versão separada
                 $recordArray['bundles'] = [
                     'principais' => $record->bundles
                         ->where('pivot.bundle_status', 'principal')
                         ->values()
                         ->map(function($bundle) {
                             return [
                                 'id' => $bundle->bundle_id,
                                 'name' => $bundle->bundle,
                                 'status' => $bundle->pivot->bundle_status,
                                 'old_bundle' => $bundle->pivot->old_bundle
                             ];
                         })->toArray(),
                         
                     'adicionais' => $record->bundles
                         ->where('pivot.bundle_status', 'adicional')
                         ->values()
                         ->map(function($bundle) {
                             return [
                                 'id' => $bundle->bundle_id,
                                 'name' => $bundle->bundle,
                                 'status' => $bundle->pivot->bundle_status,
                                 'old_bundle' => $bundle->pivot->old_bundle
                             ];
                         })->toArray()
                 ];
 
                 return $recordArray;
             });
 


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
