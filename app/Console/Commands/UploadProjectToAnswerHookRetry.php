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
    protected $signature = 'app:upload-project-to-answer-hook-retry';

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
            $ProjectFiles = ProjectFiles::where('id', 19)
            ->with('bundles')
            ->orderBy('id', 'asc')
            ->get();
            
            // $clientHookIA = new Client([
            //     'base_uri' => 'http://57.129.138.26/v1/',
            //     'timeout' => 60,
            //     'headers' => [
            //         'Authorization' => 'Bearer app-yS9ZY7Rc1GR9Y19IKpX22XRO',
            //         'Accept' => 'application/json',
            //     ],
            // ]);

            $clientHookIA = new Client([
                'base_uri' => 'https://ubuntu-bw8-mac-server.hook.app.br/v1/',
                'timeout' => 60,
                'headers' => [
                    'Authorization' => 'Bearer app-2KkTmPKykDJPnyufxnN7H9bw',
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
                            ->where('project_records.status', "processando")
                            ->orderBy('id', 'asc')
                            ->get();

                            foreach ($Records as $Record) {

                                //$Agent = Agent::where('id', $Record->agent_id)->first();
                                $Processo = RfpProcess::with('rfpBundles')->where('id', $Record->processo_id)->first();
                                $BundlesProcess = $Processo->rfpBundles;
    
                                $ProdutosArray = [];
                                $AgentesArray = [];
    
                                foreach ($BundlesProcess as $bundleProcess) {
                                    $DadosAgentePrioritario = Agent::where('id', $bundleProcess->agent_id)->first();
    
                                    $ProdutosArray[] = $bundleProcess->bundle;
                                    $AgentesArray[] = $DadosAgentePrioritario->knowledge_id_hook;
                                }
    
                                // Pega os AGENTES e remove os itens repetidos e converte pra string
                                $AgentesUnique = array_values(array_unique($AgentesArray));
                                $AgentesPrimarios = implode(',', $AgentesUnique);
                                    $agentIds = $bundles->pluck('agent_id')->unique();
                                    $agentsList = Agent::whereIn('id', $agentIds)->get();
                                    $AgentesSecundarios = $agentsList->pluck('knowledge_id_hook')->filter()->diff($AgentesUnique)->implode(', ');
    
                                // Pega os PRODUTOS e remove os itens repetidos e converte pra string
                                $ProdutosUnique = array_values(array_unique($ProdutosArray));
                                $ProdutosPrimarios = implode(',', $ProdutosUnique);
                                    $ProdutosAdicionais = collect($bundles->pluck('bundle')->unique())->diff($ProdutosUnique)->implode(', ');
    
                                $requisito = $Record->requisito;
                                $processo = $Processo->process;
    
                                $body = [
                                    'inputs' =>  [
                                        'base_id_primarios' => $AgentesPrimarios,
                                        'base_id_secundarios' => $AgentesSecundarios,    
                                    ],
                                    'query' => json_encode([
                                            'requisito' => $requisito,
                                            'processo' => $processo,
                                            'produto' => $ProdutosPrimarios,
                                            'produtos_adicionais' => $ProdutosAdicionais
                                    ], JSON_UNESCAPED_UNICODE),
                                    'response_mode' => 'blocking',
                                    "conversation_id" => "",
                                    "user" => "RFP-API-RETRY",
                                    "files" => [],
                                ];  
    
                                yield function () use ($clientHookIA, $body, $Record) { 

                                    $Record->update(['status' => 'enviado']);
                                    $Record->update(['ia_attempts' => 1]);
                                    $Record->save();

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
                'concurrency' => 2,
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
}
