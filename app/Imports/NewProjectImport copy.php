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
        if($this->idpacote){
            $Pacote = RfpBundle::firstWhere('bundle_id', $this->idpacote);
            $NomePacote = $Pacote->bundle;
        }
        

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
                    LINHA/PRODUTO :'.$NomePacote.'

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
