<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

use Carbon\Carbon;
use App\Models\KnowledgeBase;
use App\Models\KnowledgeRecord;
use App\Models\RfpBundle;
use App\Models\Agent;
use App\Models\ProjectAnswer;
use App\Models\RfpAnswer;
use App\Models\UsersDepartaments;
use App\Imports\ProjectRecordsImport;
use App\Imports\KnowledgeBaseInfoImport;
use App\Exports\KnowledgeBaseExport;
use App\Models\Project;
use App\Models\ProjectFiles;
use App\Models\ProjectRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use App\Http\Controllers\MentoriaController;
use App\Models\Module;
use Illuminate\Support\Str;
use DateTime;


use App\Services\ParallelRequests;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Exception\RequestException;

class UploadProjectToAnswer extends Command
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        parent::__construct();

        $this->baseUrl = 'https://api.conteudo.rdstationmentoria.com.br/rest';
        $this->apiKey = 'eyJhbGciOiJSUzUxMiIsInR5cCI6IkpXVCJ9.eyJ0eXBlIjoiYXBpIiwicHJvcGVydGllcyI6eyJrZXlJZCI6ImtleV8wMUpGMEhQWEc1SkRSNTlBUzU2OFgwVjk3WSIsIndvcmtzcGFjZUlkIjoid3BjXzAxSjVCSFdCNTRFSldDRE42QVFZMlg2NUo3IiwidXNlcklkIjoidXNlcl9hcGkifSwiaWF0IjoxNzM0MTExNjIyfQ.cLjsIB85bybra-rQOTAI-GLuIQKeQP95HLdXu-JG1yxMbrdzHwjqLGl8xzo3aVwz94uD3mWaOhajdqync0CCusVM_VF3dEsg2bRd9OM02HMD-rxil360HClB--5zYKOW7NZUPKmj0Q8rl-1v-aE4lFes6U7-_zB1gJWiGTLR9HLuZd3E5EsSqxu_mS49ss5tAFHQYrVotns6Ug5OGmxSgJ-IqlluVMPRbI8dtSb0ZsiYHe_xYtaERhTInevjaqgHbhZwLzyHg50R7MMoJLsGlw8CD3KRfcitxj8NZmilKK4vCkjm4dN5QYTuRxSqpgnGRtCCk-3fR0q8N3GwYF4oWQ';
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:upload-project-to-answer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia Projeto para respotas da IA';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $ProjectFiles = ProjectFiles::where('status', "em processamento")->get();
    
            $client = new Client([
                'base_uri' => $this->baseUrl,
                'timeout' => 30,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ],
            ]);
    
            $requests = function () use ($ProjectFiles, $client) {
                foreach ($ProjectFiles as $File) {
                    $Records = ProjectRecord::whereNotNull('project_records.bundle_id')
                        ->where('project_records.project_file_id', $File->id)
                        ->where('project_records.status', "processando")
                        ->join('rfp_bundles', 'project_records.bundle_id', '=', 'rfp_bundles.bundle_id')
                        ->get();
            
                    foreach ($Records as $Record) {
                        $Agent = Agent::where('id', $Record->agent_id)->first();
                        $Module = Module::where('id', $Record->classificacao_id)->first();
            
                        $body = [
                            'system' => 'Você é uma IA avançada representando o time de engenharia da TOTVS...',
                            'kbs' => [$Agent->knowledge_id],
                            'messages' => [
                                [
                                    'role' => 'user',
                                    'content' => $Record->requisito
                                ]
                            ],
                            'responseFormat' => [
                                'type' => 'json_schema',
                                'schema' => $this->getSchema()
                            ]
                        ];
    
                        yield function () use ($client, $body, $Record) {
                            return $client->postAsync('/rest/completions', [
                                'json' => $body,
                                'headers' => ['Content-Type' => 'application/json'],
                            ])->then(function ($response) use ($Record) {
                                return ['response' => $response, 'record' => $Record];
                            });
                        };
                    }
                }
            };
    
            $pool = new Pool($client, $requests(), [
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
    
                    $Answer = json_decode($data['output']['content']);
    
                    $DadosResposta->aderencia_na_mesma_linha = $Answer->aderencia_na_mesma_linha ?? null;
                    $DadosResposta->linha_produto = $Answer->linha_produto ?? null;
                    $DadosResposta->resposta = $Answer->resposta ?? null;
                    $DadosResposta->referencia = $Answer->referencia ?? null;
                    $DadosResposta->observacao = $Answer->observacao ?? null;
                    $DadosResposta->acuracidade_porcentagem = $Answer->acuracidade_porcentagem ?? null;
                    $DadosResposta->acuracidade_explicacao = $Answer->acuracidade_explicacao ?? null;
                    $DadosResposta->save();
    
                    // Atualizar o status do Record
                    if($Answer->aderencia_na_mesma_linha != 'desconhecido'){
                        $Record->update(['status' => 'respondido ia']);
                    }
                   
                    Log::info("Processamento de todos os arquivos concluído com sucesso");
                   

                },
                'rejected' => function ($reason, $index) {
                    Log::error("Request failed: " . $reason->getMessage());
                    // Você pode querer atualizar o status do Record aqui também
                },
            ]);
    
            Log::info("Executado com sucesso");
            // Executa o pool
            $promise = $pool->promise();
            $promise->wait();
    
            //return response()->json(['message' => 'Processamento concluído']);
        } catch (\Exception $e) {
            //dd($e->getMessage());
            Log::error("Erro no processamento: " . $e->getMessage());
            //return response()->json(['error' => 'Ocorreu um erro durante o processamento'], 500);
        }
    }


    // O método getSchema deve ser definido fora da classe MentoriaController para ser acessível
    function getSchema()
    {
        return [
            "name" => "answer",
            "description" => "Uma resposta que indica se o software atende o requisito.",
            "strict" => true,
            "parameters" => [
                "type" => "object",
                "additionalProperties" => false,
                "properties" => [
                    "aderencia_na_mesma_linha" => [
                        "type" => "string",
                        "description" => "Atende, Atende Parcial, Customizável, Não Atende ou Desconhecido. Caso não encontre uma referencia sempre traga como Desconhecido",
                        "enum" => ["Atende", "Atende Parcial", "Customizável", "Não Atende", "Desconhecido"]
                    ],
                    "linha_produto" => [
                        "type" => "string",
                        "description" => "Valor da coluna LINHA/PRODUTO para o requisito encontrado."
                    ],
                    "resposta" => [
                        "type" => "string",
                        "description" => "Análise e justificativa do porquê algum produto atende os requisitos. Pode incluir análise de complexidade e frequência na tabela."
                    ],
                    "referencia" => [
                        "type" => "string",
                        "description" => "Requisito encontrado como referência para a resposta. Deve trazer todas as informações sobre essa referência (nome do arquivo, linha, etc.). Caso não tenha encontrado, retorne todos os campos vazios.",
                    ],
                    "observacao" => [
                        "type" => "string",
                        "description" => "Valor da coluna OBSERVAÇÕES para o requisito encontrado, se houver."
                    ],
                    "acuracidade_porcentagem" => [
                        "type" => "string",
                        "description" => "Valor da acuracidade da resposta informada, em porcentagem",
                    ],
                    "acuracidade_explicacao" => [
                        "type" => "string",
                        "description" => "explicação do calculo que foi executado para chegar a respota da acuracidade_porcentagem.",
                    ]
                ],
                "required" => ["aderencia_na_mesma_linha", "linha_produto", "resposta", "referencia", "observacao", "acuracidade_porcentagem", "acuracidade_explicacao"]
            ]
        ];
    }

}
