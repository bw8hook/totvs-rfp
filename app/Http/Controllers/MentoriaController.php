<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Exceptions\RDStationMentoria\RDStationMentoria;

class MentoriaController
{
    private $apiUrl;
    private $apiToken;
    protected $baseUrlConteudo;
    protected $baseUrl;
    protected $apiKey;
    protected $knowledgeBaseId;

    public function __construct()
    {
        $this->baseUrl = 'https://api.conteudo.rdstationmentoria.com.br/rest';
        $this->baseUrlConteudo = 'https://api.conteudo.staging.rdstationmentoria.com.br/rest';
        $this->apiKey = 'eyJhbGciOiJSUzUxMiIsInR5cCI6IkpXVCJ9.eyJ0eXBlIjoiYXBpIiwicHJvcGVydGllcyI6eyJrZXlJZCI6ImtleV8wMUpGMEhQWEc1SkRSNTlBUzU2OFgwVjk3WSIsIndvcmtzcGFjZUlkIjoid3BjXzAxSjVCSFdCNTRFSldDRE42QVFZMlg2NUo3IiwidXNlcklkIjoidXNlcl9hcGkifSwiaWF0IjoxNzM0MTExNjIyfQ.cLjsIB85bybra-rQOTAI-GLuIQKeQP95HLdXu-JG1yxMbrdzHwjqLGl8xzo3aVwz94uD3mWaOhajdqync0CCusVM_VF3dEsg2bRd9OM02HMD-rxil360HClB--5zYKOW7NZUPKmj0Q8rl-1v-aE4lFes6U7-_zB1gJWiGTLR9HLuZd3E5EsSqxu_mS49ss5tAFHQYrVotns6Ug5OGmxSgJ-IqlluVMPRbI8dtSb0ZsiYHe_xYtaERhTInevjaqgHbhZwLzyHg50R7MMoJLsGlw8CD3KRfcitxj8NZmilKK4vCkjm4dN5QYTuRxSqpgnGRtCCk-3fR0q8N3GwYF4oWQ';
    }

    public function getAnswer(array $dados)
    {
        $requirement = $dados['requisito'];
        $knowledge_id = $dados['knowledge_id'];
         
        try {

            $body = [
                'system' => 'Você é uma IA avançada representando o time de engenharia da TOTVS. Seu objetivo é responder dúvidas técnicas sobre os sistemas ERP da TOTVS, com base em uma base de conhecimento consolidada e a tabela que pode acessar. Sua prioridade é fornecer respostas claras e precisas, indicando se o sistema atende plenamente, parcialmente ou não possui capacidade para atender a necessidade apresentada. Sempre mantenha um tom profissional e indique limitações ou a necessidade de suporte humano quando aplicável.

                O usuário irá perguntar sobre algum produto e se ele atende ou não a um requisito. 

                Você deve procurar na tabela algum requisito que atende ao produto, classificação e requisito que o usuário pedir.
                
                Quando não souber uma resposta ou não encontrar uma referência traga a resposta como "Desconhecido"


                Você pode usar a coluna "DESCRIÇÃO DO REQUISITO" que tem requisitos, buscando de forma case-insensitive por algo adequado ao que o usuário pedir.',
                'kbs' => [$knowledge_id],
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $requirement
                    ]
                ],
                'responseFormat' => [
                    'type' => 'json_schema',
                    'schema' => $this->getSchema()
                ]
            ];


            $response = Http::withHeaders([
                'Authorization' => "Bearer $this->apiKey",
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/completions", $body);

            if ($response->failed()) {
                throw new \Exception("HTTP Error: {$response->status()}");
            }

            $data = $response->json();

            return $resposta = json_decode($data['output']['content']);
            dd($resposta);

            return response()->json(json_decode($data['output']['content'], associative: true));
        } catch (\Exception $e) {
            Log::error("Failed to process request. Error: {$e->getMessage()}");
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    private function getSchema()
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
                        "description" => "Atende, Atende Parcial, Customizável, Não Atende ou Desconhecido. Dependendo da sua análise.",
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


    /**
     * Envia um registro individual para a API de Mentoria.
     */
    public function enviarRegistro(array $dados)
    {     

        $jsonSchema = json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        echo $jsonSchema;

    
        $RDStationMentoria = new RDStationMentoria();
        $Agents =  $RDStationMentoria->classAgents();

        $requirement = $dados['requisito'];
        $knowledge_id = $dados['knowledge_id'];

        //$startConversation = $this->RDStationMentoria->Agents->startConversation($this->AGENTE['workspaceId'], $this->AGENTE['id'], "API - ".$arquivoTreinamento['nome'] );
        $startConversation['conversationId'] = "convo_01JKXQK7F16DM4M14WKKK2294R";
        
        if(!empty($startConversation['conversationId'])){
                $valorRequerimentos = "";
                $PERGUNTA = '
                    Pergunta: Existe algum requisito similar ao seguinte na coluna "Requisito", em sua base de conhecimento?

                    Requisito: '.$requirement.'

                    Caso exista consegue trazer qual a acuracidade da resposta que foi gerada ?
                                            
                    Resposta esperada (formato JSON):
                        {
                            "encontrou_similar": "SIM ou NÃO",
                            "aderencia_na_mesma_linha": "Atende, Customizável ou Não Atende",
                            "modulo_que_atende_na_mesma_linha": "Modulo encontrada, se houver",
                            "importancia": "importancia encontrada, se houver",
                            "linha_produto": "LINHA/PRODUTO, se houver",
                            "resposta_1": "Resposta 1 se houver",
                            "resposta_2": "Resposta 2 se houver",
                            "observacao_na_mesma_linha": "Observação encontrada, se houver"
                            "coluna_total": "todos os dados da linha que houver"
                            "acuracidade": "em porcentagem"
                        }                                    
                    ';
                
                $askInConversation = $Agents->askInConversation('mbs_01J5BHWB65TNTV2HGHNM73FPJW', $dados['agent_id'],  $startConversation['conversationId'], $PERGUNTA);
                
                dd($askInConversation);



              
               
                $this->updatedRows[] = $row;

            
        }


          


       
    }





    
}



