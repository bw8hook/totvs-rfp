<?php
namespace App\Console\Commands;
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
            ->with(['rfp_bundles.agent'])
            ->get();
            
            $clientHookIA = new Client([
                'base_uri' => 'https://totvs-ia.hook.app.br/v1/',
                'timeout' => 30,
                'headers' => [
                    'Authorization' => 'Bearer app-y133Gvf5qZvY8yM3gyojkOzR',
                    'Accept' => 'application/json',
                ],
            ]);
    
            $requestsHook = function () use ($ProjectFiles, $clientHookIA) {
                foreach ($ProjectFiles as $File) {
    
                    if($File->rfp_bundles->agent->search_engine == "Open IA"){
    
                        $Records = ProjectRecord::whereNotNull('project_records.bundle_id')
                            ->where('project_records.project_file_id', $File->id)
                            ->where('project_records.status', "processando")
                            ->join('rfp_bundles', 'project_records.bundle_id', '=', 'rfp_bundles.bundle_id')
                            ->get();
                
                        foreach ($Records as $Record) {
                            $Agent = Agent::where('id', $Record->agent_id)->first();
                            $Processo = RfpProcess::where('id', $Record->processo_id)->first();
                            
                            $prompt = $Agent->prompt;
                            $requisito = $Record->requisito;
                            $processo = $Processo->process;
    
                            $body = [
                                'inputs' =>  [
                                    'base_id' => $Agent->knowledge_id_hook,
                                    'system' => $prompt,        
                                ],
                                'query' => json_encode([
                                        'requisito' => $requisito,
                                        'processo' => $processo
                                ]),
                                'response_mode' => 'blocking',
                                "conversation_id" => "",
                                "user" => "abc-123",
                                "files" => [],
                            ];       
    
                            yield function () use ($clientHookIA, $body, $Record) { 
                                return $clientHookIA->postAsync('/v1/chat-messages', [
                                    'json' => $body,
                                    'headers' => ['Content-Type' => 'application/json'],
                                ])->then(function ($response) use ($Record) {
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
                    $response = $result['response'];
                    $Record = $result['record'];
                    $data = json_decode($response->getBody(), true);
    
                    $DadosResposta = new ProjectAnswer;
                    $DadosResposta->bundle_id = $Record->bundle_id;
                    $DadosResposta->user_id = $Record->user_id;
                    $DadosResposta->requisito_id = $Record->id;
                    $DadosResposta->requisito = $Record->requisito;
    
                    $Answer = json_decode($data['answer']);
    
                    $DadosResposta->aderencia_na_mesma_linha = $Answer->aderencia_na_mesma_linha ?? null;
                    $DadosResposta->linha_produto = $Answer->linha_produto ?? null;
                    $DadosResposta->resposta = $Answer->resposta ?? null;
                    $DadosResposta->modulo = $Answer->modulo ?? null;
                    $DadosResposta->referencia = $Answer->referencia ?? null;
                    $DadosResposta->observacao = $Answer->observacao ?? null;
                    $DadosResposta->acuracidade_porcentagem = $Answer->acuracidade_porcentagem ?? null;
                    $DadosResposta->acuracidade_explicacao = $Answer->acuracidade_explicacao ?? null;
                    $DadosResposta->save();
    
                    // Atualizar o status do Record
                    if($Answer->aderencia_na_mesma_linha != 'desconhecido'){
                        $Record->update(['status' => 'respondido ia']);
                        $Record->update(['project_answer_id' => $DadosResposta->id]);
    
                        $ProjectFile = ProjectFiles::where('id', $Record->project_id)->first();
                        if($ProjectFile->status == "processando"){
                            $ProjectFile->status = 'processado';
                            $ProjectFile->save();
                        }
                    }
                   
                    //Log::info("Processamento de todos os arquivos concluído com sucesso");
                   
    
                },
                'rejected' => function ($reason, $index) {
                    dd($reason);
                    //Log::error("Request failed: " . $reason->getMessage());
                    // Você pode querer atualizar o status do Record aqui também
                },
            ]);
    
            Log::info("Executado com sucesso");
            // Executa o pool
            $promise = $pool->promise();
            $promise->wait();
        } catch (\Exception $e) {
            Log::error("Erro no processamento: " . $e->getMessage());
        }
    }
}
