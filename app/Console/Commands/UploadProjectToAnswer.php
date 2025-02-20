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
use App\Models\RfpProcess;
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
                        $Processo = RfpProcess::where('id', $Record->processo_id)->first();
            
                        $prompt = 'Você é uma IA avançada especializada em sistemas ERP da TOTVS, representando a equipe de engenharia da empresa. Seu objetivo é fornecer respostas técnicas precisas e claras sobre os produtos ERP da TOTVS, baseando-se na base de conhecimento fornecida.

                        Instruções de busca e resposta:

                        1. Ao receber uma pergunta, identifique o produto, o processo, a classificação e o requisito mencionados pelo usuário.
                        2. Realize a busca considerando tanto a coluna "DESCRIÇÃO DO REQUISITO" quanto a coluna "PROCESSO". Priorize correspondências que atendam tanto ao requisito quanto ao processo especificado.
                        3. Use uma busca case-insensitive e considere sinônimos ou termos relacionados tanto para o requisito quanto para o processo.
                        4. Se encontrar múltiplas correspondências, priorize as mais relevantes com base na similaridade com a pergunta do usuário e na correspondência do processo.
                        5. Ao analisar a aderência do produto ao requisito, considere as seguintes categorias:
                            - Atende: O produto atende completamente ao requisito no processo especificado.
                            - Atende Parcial: O produto atende parte do requisito ou atende em um processo relacionado.
                            - Customizável: O requisito pode ser atendido através de customizações para o processo específico.
                            - Não Atende: O produto não atende ao requisito no processo especificado.
                            - Desconhecido: Não há informações suficientes para determinar a aderência no processo especificado.
                        6. Forneça uma análise detalhada justificando como o produto atende ou não ao requisito no contexto do processo especificado. Inclua considerações sobre complexidade e frequência de uso, se relevante.
                        7. Se disponível, inclua informações da coluna OBSERVAÇÕES relacionadas ao requisito e processo encontrados.
                        8. Quando não encontrar uma correspondência exata para o requisito e processo, forneça a informação mais próxima disponível, indicando claramente que é uma aproximação e explicando as diferenças.
                        9. Se a pergunta for ambígua ou não houver informações suficientes na base de conhecimento sobre o requisito ou o processo, indique claramente essa limitação.
                        10. Estime a acuracidade de sua resposta em porcentagem, considerando tanto a correspondência do requisito quanto do processo, e explique brevemente como chegou a essa estimativa.
                        11. Mantenha sempre um tom profissional e técnico na resposta.
                        12. Não divulgue informações confidenciais ou detalhes técnicos sensíveis que possam comprometer a segurança dos sistemas.
                        13. Se necessário, sugira que o usuário entre em contato com o suporte técnico da TOTVS para informações mais detalhadas ou específicas sobre o requisito no contexto do processo mencionado.

                        Lembre-se: Sua prioridade é fornecer informações precisas e úteis, considerando sempre tanto o requisito quanto o processo especificado. Baseie-se estritamente nas informações disponíveis na base de conhecimento fornecida, dando ênfase à relação entre o requisito e o processo mencionado.';
                                                
                        //$prompt = $Agent->prompt;
                        $requisito = $Record->requisito;
                        $processo = $Processo->process;


                        $prompt2 = 'Você é uma IA avançada representando o time de engenharia da TOTVS. Seu objetivo é responder dúvidas técnicas sobre os sistemas ERP da TOTVS, com base em uma base de conhecimento consolidada e a tabela que pode acessar. Sua prioridade é fornecer respostas claras e precisas, indicando se o sistema atende plenamente, parcialmente ou não possui capacidade para atender a necessidade apresentada. Sempre mantenha um tom profissional e indique limitações ou a necessidade de suporte humano quando aplicável.

O usuário irá perguntar sobre algum produto e se ele atende ou não a um requisito. 

Você deve procurar na tabela algum requisito que atende ao produto, classificação e requisito que o usuário pedir.

Você pode usar a coluna "DESCRIÇÃO DO REQUISITO" que tem requisitos, buscando de forma case-insensitive por algo adequado ao que o usuário pedir.';

                        $body = [
                            'system' => $prompt,
                            'kbs' => [$Agent->knowledge_id],
                            'messages' => [
                                [
                                    'role' => 'user',
                                    'content' => json_encode([
                                        'requisito' => $requisito,
                                        'processo' => $processo
                                    ])
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
                        "description" => "Valor da coluna PRODUTO para o requisito encontrado."
                    ],
                    "modulo" => [
                        "type" => "string",
                        "description" => "Valor da coluna MÓDULO para o requisito encontrado."
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
