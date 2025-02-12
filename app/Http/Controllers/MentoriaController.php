<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        $this->baseUrl = 'https://api.conteudo.rdstationmentoria.com.br/rest/completions';
        $this->baseUrlConteudo = 'https://api.conteudo.staging.rdstationmentoria.com.br/rest';
        $this->apiKey = env('eyJhbGciOiJSUzUxMiIsInR5cCI6IkpXVCJ9.eyJ0eXBlIjoiYXBpIiwicHJvcGVydGllcyI6eyJrZXlJZCI6ImtleV8wMUpGMEhQWEc1SkRSNTlBUzU2OFgwVjk3WSIsIndvcmtzcGFjZUlkIjoid3BjXzAxSjVCSFdCNTRFSldDRE42QVFZMlg2NUo3IiwidXNlcklkIjoidXNlcl9hcGkifSwiaWF0IjoxNzM0MTExNjIyfQ.cLjsIB85bybra-rQOTAI-GLuIQKeQP95HLdXu-JG1yxMbrdzHwjqLGl8xzo3aVwz94uD3mWaOhajdqync0CCusVM_VF3dEsg2bRd9OM02HMD-rxil360HClB--5zYKOW7NZUPKmj0Q8rl-1v-aE4lFes6U7-_zB1gJWiGTLR9HLuZd3E5EsSqxu_mS49ss5tAFHQYrVotns6Ug5OGmxSgJ-IqlluVMPRbI8dtSb0ZsiYHe_xYtaERhTInevjaqgHbhZwLzyHg50R7MMoJLsGlw8CD3KRfcitxj8NZmilKK4vCkjm4dN5QYTuRxSqpgnGRtCCk-3fR0q8N3GwYF4oWQ');
    }

    public function getAnswer(array $dados)
    {
        $requirement = $dados['requisito'];
        $knowledge_id = $dados['knowledge_id'];
 
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/completions", [
                'system' => "Você é o assistente virtual para preenchimento de RFPs da Totvs. Você receberá um requisito e deve verificar se o software atende ou não. Para isso, pesquise informações em sua base de conhecimento usando a ferramenta `search`. Caso não encontre nenhuma referência na sua base de conhecimento, marque a resposta como `UNKNOWN`.",
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
            ]);

            if ($response->failed()) {
                throw new \Exception("HTTP Error: {$response->status()}");
            }

            $data = $response->json();

            return response()->json(json_decode($data['output']['content'], true));
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
                    "compliance" => [
                        "type" => "string",
                        "description" => "Se o software atende ou não o requisito",
                        "enum" => ["COMPLIANT", "CUSTOMIZABLE", "NOT_COMPLIANT", "UNKNOWN"]
                    ],
                    "module" => [
                        "type" => "string",
                        "description" => "O módulo da funcionalidade"
                    ],
                    "feature" => [
                        "type" => "string",
                        "description" => "O nome ou localização da funcionalidade"
                    ],
                    "reason" => [
                        "type" => "string",
                        "description" => "Uma justificativa detalhada para a sua resposta"
                    ]
                ],
                "required" => ["compliance", "module", "feature", "reason"]
            ]
        ];
    }


    /**
     * Envia um registro individual para a API de Mentoria.
     */
    public function enviarRegistro(array $dados)
    {
        // Validação manual dos dados
        if (!isset($dados['nome']) || !isset($dados['email']) || !isset($dados['mensagem'])) {
            return ['error' => 'Os campos nome, email e mensagem são obrigatórios.'];
        }

        $schema = [
            "name" => "answer",
            "description" => "Uma resposta que indica se o software atende o requisito.",
            "strict" => true,
            "parameters" => [
                "type" => "object",
                "additionalProperties" => false,
                "properties" => [
                    "compliance" => [
                        "type" => "string",
                        "description" => "Se o software atende ou não o requisito",
                        "enum" => ["COMPLIANT", "CUSTOMIZABLE", "NOT_COMPLIANT", "UNKNOWN"]
                    ],
                    "module" => [
                        "type" => "string",
                        "description" => "O módulo da funcionalidade"
                    ],
                    "feature" => [
                        "type" => "string",
                        "description" => "O nome ou localização da funcionalidade"
                    ],
                    "reason" => [
                        "type" => "string",
                        "description" => "Uma justificativa detalhada para a sua resposta"
                    ]
                ],
                "required" => ["compliance", "module", "feature", "reason"]
            ]
        ];
        

        $jsonSchema = json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        echo $jsonSchema;


          


        try {
            // Envia os dados para a API externa via HTTP POST
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type'  => 'application/json',
            ])->post("{$this->apiUrl}/mentoria/registro", $dados);

            // Retorna a resposta da API externa
            return $response->json();

        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}



