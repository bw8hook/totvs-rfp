<?php

namespace App\Imports;

use App\Models\KnowledgeBase;
use App\Models\RfpBundle;
use App\Models\KnowledgeRecord;
use App\Models\KnowledgeError;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Illuminate\Support\Facades\Cache;
use App\Exceptions\RDStationMentoria\RDStationMentoria;
use Exception;
use Maatwebsite\Excel\Events\BeforeImport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Events\AfterImport;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Pool;

class NewProjectImport implements ToCollection, WithChunkReading, WithStartRow, WithEvents
{
    use RegistersEventListeners;

    protected $RDStationMentoria = [];

    protected $id;
    protected $idpacote; // Variável para armazenar o ID
    public $Erros = [];
    private $ListBundles = [];

    public $updatedRows = [];

    public $updatedCount = 0; 
    public $NotUpdatedCount = 0; 

    public function __construct($id, $idpacote)
    {
        $this->id = $id;
        $this->idpacote = $idpacote; // Define o ID recebido no construtor
    }

    /**
     * Executa antes da importação e popula o array.
     */
    public static function beforeImport(BeforeImport $event)
    {
        $instance = $event->getConcernable();
        // Consulta os dados no banco de dados e preenche o array
        $instance->ListBundles = RfpBundle::all()->pluck('bundle_id', 'bundle')->toArray();
    }
    
    public function startRow(): int{
        return 1; // Ignora as duas primeiras linhas
    }

    public function collection(Collection $rows)
    {
        $client = new Client([
            'base_uri' => 'https://chat.meuassistente.rdstationmentoria.com.br',
            'headers' => [
                'Authorization' => 'Bearer eyJhbGciOiJSUzUxMiIsInR5cCI6IkpXVCJ9.eyJ0eXBlIjoiYXBpIiwicHJvcGVydGllcyI6eyJrZXlJZCI6ImtleV8wMUpGMEhQWEc1SkRSNTlBUzU2OFgwVjk3WSIsIndvcmtzcGFjZUlkIjoid3BjXzAxSjVCSFdCNTRFSldDRE42QVFZMlg2NUo3IiwidXNlcklkIjoidXNlcl9hcGkifSwiaWF0IjoxNzM0MTExNjIyfQ.cLjsIB85bybra-rQOTAI-GLuIQKeQP95HLdXu-JG1yxMbrdzHwjqLGl8xzo3aVwz94uD3mWaOhajdqync0CCusVM_VF3dEsg2bRd9OM02HMD-rxil360HClB--5zYKOW7NZUPKmj0Q8rl-1v-aE4lFes6U7-_zB1gJWiGTLR9HLuZd3E5EsSqxu_mS49ss5tAFHQYrVotns6Ug5OGmxSgJ-IqlluVMPRbI8dtSb0ZsiYHe_xYtaERhTInevjaqgHbhZwLzyHg50R7MMoJLsGlw8CD3KRfcitxj8NZmilKK4vCkjm4dN5QYTuRxSqpgnGRtCCk-3fR0q8N3GwYF4oWQ',
                'Accept' => 'application/json',
            ],
        ]);

        $promises = [];
        // Limite de requisições simultâneas
        $concurrency = 20;

        if($this->idpacote){
            $Pacote = RfpBundle::firstWhere('bundle_id', $this->idpacote);
            $NomePacote = $Pacote->bundle;
        }

        $startConversation['conversationId'] = "convo_01JF8DZFWCAPDSQRZDDGZCGPT2";

        $requests = function ($rows) use ($client) {
            foreach ($rows as $row) {
                yield function () use ($client, $row) {
                    // Suponha que o valor da coluna esteja no índice 0
                    $this->RDStationMentoria = new RDStationMentoria();
                    $RDStationMentoria = new RDStationMentoria();
                    $Agents =  $RDStationMentoria->classAgents();

                    $PERGUNTA = '
                        Pergunta: Existe algum requisito similar ao seguinte na coluna "Requisito", em sua base de conhecimento?

                        Requisito: '.$row[2].'
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

                        $askInConversation = $Agents->askInConversation('wpc_01J5BHWB54EJWCDN6AQY2X65J7', 'agt_01JEY70XQPHBDMBJ6XP2VHCJBX', 'convo_01JF8DZFWCAPDSQRZDDGZCGPT2', $PERGUNTA);

                        // Dados JSON a serem enviados
                        $data = [
                            "message" => [
                                "content" => "\n                    Pergunta: Existe algum requisito similar ao seguinte na coluna \"Requisito\", em sua base de conhecimento?\n\n                    Requisito: Permitir controlar diversas filiais por empresa.\n                    LINHA/PRODUTO :App-Carol Clock-in Kiosk\n\n                    Caso exista consegue trazer qual a acuracidade da resposta que foi gerada ?\n                                            \n                    Resposta esperada (formato JSON):\n                        {\n                            \"encontrou_similar\": \"SIM ou NÃO\",\n                            \"aderencia_na_mesma_linha\": \"Atende, Customizável ou Não Atende\",\n                            \"modulo_que_atende_na_mesma_linha\": \"Modulo encontrada, se houver\",\n                            \"importancia\": \"importancia encontrada, se houver\",\n                            \"linha_produto\": \"LINHA/PRODUTO, se houver\",\n                            \"resposta_1\": \"Resposta 1 se houver\",\n                            \"resposta_2\": \"Resposta 2 se houver\",\n                            \"observacao_na_mesma_linha\": \"Observação encontrada, se houver\",\n                            \"coluna_total\": \"todos os dados da linha que houver\",\n                            \"acuracidade\": \"em porcentagem\"\n                        }                                    \n                    ",
                                "role" => "user",
                                "metadata" => (object)[], // Metadados vazios como objeto
                            ],
                            "id" => "convo_01JF8DZFWCAPDSQRZDDGZCGPT2",
                        ];

                        return $client->postAsync('/widget/wpc_01J5BHWB54EJWCDN6AQY2X65J7/agt_01JEY70XQPHBDMBJ6XP2VHCJBX', [
                            'json' => $data,
                            'headers' => [
                                'Content-Type' => 'application/json',
                            ],
                        ]);
                };
            }
        };

        

        
        // Pool para gerenciar as requisições
        $pool = new Pool($client, $requests(rows: $rows), [
            'concurrency' => $concurrency,
            'fulfilled' => function ($response, $index) {
                // Requisição concluída com sucesso
                $data = json_decode($response->getBody(), true);
                
                // Salve ou processe os dados da API
                //if(isset($data['content'])){
                    KnowledgeRecord::create([
                        'bundle_id' => 130,
                        'user_id' => 4,
                        'knowledge_base_id' =>8
                    ]);
            },
            'rejected' => function ($reason, $index) {
                // Requisição falhou
                \Log::error('Erro na requisição: ' . $reason);
            },
        ]);

        // Executa o pool
        $promise = $pool->promise();
        $promise->wait();

        $isFirstRow = true;

        // Ignorar a primeira linha o cabeçalho
        //$rows = $rows->skip(1);
        $this->RDStationMentoria = new RDStationMentoria();
        $RDStationMentoria = new RDStationMentoria();
        $Agents =  $RDStationMentoria->classAgents();

        // Busca em Todas as linhas
        foreach ($rows as $index => $row) {
            
            if ($isFirstRow) {
                $row[8] = "Acuracidade";
                $this->updatedRows[] = $row; // Mantém o cabeçalho intacto
                $isFirstRow = false;
                continue;
            }

            $startTime = microtime(true);
            //$startConversation = $this->RDStationMentoria->Agents->startConversation($this->AGENTE['workspaceId'], $this->AGENTE['id'], "API - ".$arquivoTreinamento['nome'] );
            $startConversation['conversationId'] = "convo_01JF8DZFWCAPDSQRZDDGZCGPT2";
            if(!empty($startConversation['conversationId'])){
                $valorRequerimentos = "";
                $PERGUNTA = '
                    Pergunta: Existe algum requisito similar ao seguinte na coluna "Requisito", em sua base de conhecimento?

                    Requisito: '.$row[2].'

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
                
                $askInConversation = $Agents->askInConversation('wpc_01J5BHWB54EJWCDN6AQY2X65J7', 'agt_01JEY70XQPHBDMBJ6XP2VHCJBX', 'convo_01JF8DZFWCAPDSQRZDDGZCGPT2', $PERGUNTA);
                
                //dd($askInConversation);

                if( isset($askInConversation['content']) && $askInConversation['content'] == "Peço desculpas, mas não sei como responder esta pergunta." ){
                    $row[0] = $row[0];
                    $row[1] = $row[1];
                    $row[2] = $row[2];
                    $row[3] = $row[3];
                    $row[4] = $row[4];
                    $row[5] = $row[5];
                    $row[6] = $row[6];
                    $row[7] = $row[7];
                     // Incrementa o contador
                    $this->NotUpdatedCount++;
                }else{
                    if(isset($askInConversation['jsonData']) && $askInConversation['jsonData']['encontrou_similar'] == "SIM" ){
                        $row[0] = $row[0];
                        $row[1] = $row[1];
                        $row[2] = $row[2];
                        if(isset($askInConversation['jsonData']['aderencia_na_mesma_linha'])){
                            $row[3] = $askInConversation['jsonData']['aderencia_na_mesma_linha'];
                        }else{
                            $row[3] = null;
                        }
                        if(isset($askInConversation['jsonData']['aderencia_na_mesma_linha'])){
                            $row[3] = $askInConversation['jsonData']['aderencia_na_mesma_linha'];
                        }else{
                            $row[3] = null;
                        }
    
                        if(isset($askInConversation['jsonData']['modulo_que_atende_na_mesma_linha'])){
                            $row[4] = $askInConversation['jsonData']['modulo_que_atende_na_mesma_linha'];
                        }else{
                            $row[4] = null;
                        }
    
                        if(isset($askInConversation['jsonData']['importancia'])){
                            $row[5] = $askInConversation['jsonData']['importancia'];
                        }else{
                            $row[5] = null;
                        }
    
                        if(isset($askInConversation['jsonData']['observacao_na_mesma_linha'])){
                            $row[6] = $askInConversation['jsonData']['observacao_na_mesma_linha'];
                        }else{
                            $row[6] = null;
                        }
    
                        if(isset($askInConversation['jsonData']['linha_produto'])){
                            $row[7] = $askInConversation['jsonData']['linha_produto'];
                        }else{
                            $row[7] = null;
                        }

                        if(isset($askInConversation['jsonData']['acuracidade'])){
                            $row[8] = $askInConversation['jsonData']['acuracidade'];
                        }else{
                            $row[8] = null;
                        }

                        // Incrementa o contador
                        $this->updatedCount++;
                    }else if(isset($askInConversation['jsonData']) && $askInConversation['jsonData']['encontrou_similar'] == "NÃO" ){
                        $row[0] = $row[0];
                        $row[1] = $row[1];
                        $row[2] = $row[2];
                        $row[3] = $row[3];
                        $row[4] = $row[4];
                        $row[5] = $row[5];
                        $row[6] = $row[6];
                        $row[7] = $row[7];
                         // Incrementa o contador
                        $this->NotUpdatedCount++;
                    }else{
                        $row[0] = $row[0];
                        $row[1] = $row[1];
                        $row[2] = $row[2];
                        $row[3] = $row[3];
                        $row[4] = $row[4];
                        $row[5] = $row[5];
                        $row[6] = $row[6];
                        $row[7] = $row[7];
                    }
                }

              
               
                $endTime = microtime(true);
                $executionTime = $endTime - $startTime;
                $row[8] = $executionTime;

                $this->updatedRows[] = $row;

            }
        }
    }


    public static function afterImport(AfterImport $event)
    {

    }


    public function jsonData($request=[]){
        if(!empty($request['content'])){
            $content = $request['content'];
            if (mb_substr($content, 0, 1) === "{" && json_decode($content, true)) {
                $jsonString = $content;
            }else{
                $startPos = strpos($content, '```json');
                $endPos = strpos($content, '```', $startPos + 1);
                if ($startPos !== false && $endPos !== false) {
                    $jsonString = substr($content, $startPos + 7, $endPos - $startPos - 7);
                }
            }    

            if(!empty($jsonString)){
                $request['jsonData'] = json_decode($jsonString, true);
                if (!empty($startPos) && $startPos !== false && !empty($endPos) &&  $endPos !== false) {
                    $request['antesTexto'] = trim( str_replace(["\r", "\n",'""','  '], '', substr($content, 0, $startPos)) );
                    $request['depoisTexto'] = trim( str_replace(["\r", "\n",'""','  '], '', substr($content, $endPos + 3)) );
                }
            }
        }

        return $this->escolherResquest( $request );
    }

    public function escolherResquest($request=[]){
        if(!empty($request) && empty($request['content']) && !empty($request[0]) ){
            $keyValida = "NAO";
            for ($c=count($request); $c > 0 ; $c--) {
            //for ($c=0; $c < count($request); $c++) { 
                if($keyValida == "NAO" && !empty($request[$c]) && !empty($request[$c]['content']) ){
                    $request = $request[$c];
                    $keyValida = "SIM";
                    break;
                }
            }
        }
        return $request;     
    }

    

    /**
     * Retorna os dados atualizados.
     */
    public function getUpdatedRows()
    {
        return $this->updatedRows;
    }



    public function chunkSize(): int
    {
        return 100; // Processa 100 linhas por vez
    }

}
