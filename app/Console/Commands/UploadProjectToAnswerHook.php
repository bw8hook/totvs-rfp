<?php
namespace App\Console\Commands;
use App\Models\RfpBundle;
use Illuminate\Console\Command;

use App\Models\Agent;
use App\Models\ProjectAnswer;
use App\Models\RfpProcess;
use App\Models\ProjectFiles;
use App\Models\ProjectRecord;

use Illuminate\Support\Facades\Log;

use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Exception\RequestException;


class UploadProjectToAnswerHook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:upload-project-to-answer-hook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia Perguntas para RESPOSTA quando a ENGINE for OPEN IA';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $ProjectFiles = ProjectFiles::where('status', "em processamento")
            ->with('bundles')
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

                        $Records = ProjectRecord::with('bundles')
                            ->where('project_records.project_file_id', $File->id)
                            ->where('project_records.status', "processando")
                            //->join('rfp_bundles', 'project_records.bundle_id', '=', 'rfp_bundles.bundle_id')
                            ->get();


                        foreach ($Records as $Record) {
            
                            //$Agent = Agent::where('id', $Record->agent_id)->first();
                            $Processo = RfpProcess::with('rfpBundles')->where('id', $Record->processo_id)->first();
                            $BundlesProcess = $Processo->rfpBundles;


                            $ProdutosPrioritarios = '';
                            foreach ($BundlesProcess as $bundleProcess) {
                                $DadosAgentePrioritario = Agent::where('id', $bundleProcess->agent_id)->first();

                                // Se a string estiver vazia, adicione direto
                                if (empty($ProdutosPrioritarios)) {
                                    $ProdutosPrioritarios = $bundleProcess->bundle; // ou outro campo que queira
                                } else {
                                    // Se já tiver conteúdo, adicione com vírgula
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
    
                            $body = [
                                'inputs' =>  [
                                    'base_id_primarios' => $DadosAgentePrioritario->knowledge_id_hook,
                                    'base_id_secundarios' => $AgentesPrioritarios,    
                                ],
                                'query' => json_encode([
                                        'requisito' => $requisito,
                                        'processo' => $processo,
                                        'produto' => $ProdutosPrioritarios,
                                        'produtos_adicionais' => $ProdutosAdicionais
                                ], JSON_UNESCAPED_UNICODE),
                                'response_mode' => 'blocking',
                                "conversation_id" => "",
                                "user" => "RFP-API-PRODUCAO",
                                "files" => [],
                            ];  
                            

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
                'concurrency' => 5,
                'fulfilled' => function ($result, $index) {
                    Log::info("Resposta Recebida");

                    $response = $result['response'];
                    $Record = $result['record'];
                    $data = json_decode($response->getBody(), true);
                    $DadosResposta = new ProjectAnswer;

                    $Answer = json_decode($data['answer']);
                    $Referencia = json_encode($data['metadata']['retriever_resources']);
                    
                    $bundleId = RfpBundle::where('bundle', 'like', '%' . $Answer->linha_produto . '%')->first();
                    
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
    
                    // Atualizar o status do Record
                    if($Answer->aderencia_na_mesma_linha != 'desconhecido'){
                        $Record->update(['status' => 'respondido ia']);
                        $Record->update(['project_answer_id' => $DadosResposta->id]);
    
                        // $ProjectFile = ProjectFiles::where('id', $Record->project_id)->first();
                        // if($ProjectFile->status == "processando"){
                        //     $ProjectFile->status = 'processado';
                        //     $ProjectFile->save();
                        // }
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
}
