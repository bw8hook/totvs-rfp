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
use Exception;
use Maatwebsite\Excel\Events\BeforeImport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class KnowledgeBaseImport_ implements ToCollection, WithChunkReading, WithStartRow, WithEvents, WithMultipleSheets
{
    use RegistersEventListeners;
    use Importable;

    protected $id;
    protected $idpacote; // Variável para armazenar o ID
    public $Erros = [];
    private $ListBundles = [];
    public $updatedRows = [];
    public $erroRows = [];
    

    public function __construct($id, $idpacote)
    {
        $this->id = $id;
        $this->idpacote = $idpacote; // Define o ID recebido no construtor
    }


    // Validação apenas da primeira aba
    public function sheets(): array
    {
        return [
            0 => $this // Apenas a primeira aba será processada
        ];
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
        return 2; // Ignora as duas primeiras linhas
    }

    public function collection(Collection $rows)
    {
        // Ignorar a primeira linha o cabeçalho
        $rows = $rows->skip(1);

        // Busca em Todas as linhas
        foreach ($rows as $index => $row) {           
            // Caso tenha o Módulo na planilha busca no BD para ver se está correto.
            if(isset($row[1])){
                if(isset($row[7]) && $this->idpacote == 0){
                    $nome = $row[7]; // Substitua pelo índice correto do nome na linha

                    $id = $this->ListBundles[$nome] ?? null;
                    if (!$id) {
                        $DadosErros = array();
                        $DadosErros['row'] = $row;
                        if($rows->get($index-1)){
                            $DadosErros['lastInserted'] = $rows->get($index-1);
                        }else{
                            $DadosErros['lastInserted'] = null;
                        }
                        
                        $DadosErrosTotal = array();
                        $DadosErrosTotal['error_message'] = 'Erro ao processar o arquivo - Não encontramos o "'.$row[7].'" na nossa lista de Pacotes';
                        $DadosErrosTotal['error_data'] = $DadosErros;
                        
                        throw new Exception(json_encode($DadosErrosTotal));
                    }

                    $KnowledgeRecord = new KnowledgeRecord();
                    $KnowledgeRecord->bundle_id = $id;
                    $KnowledgeRecord->knowledge_base_id = $this->id;
                    $KnowledgeRecord->user_id = Auth::id();
                    $KnowledgeRecord->classificacao = $row[0];
                    $KnowledgeRecord->classificacao2 = $row[1];
                    $KnowledgeRecord->requisito = $row[2];
                    $KnowledgeRecord->resposta = $row[3];
                    $KnowledgeRecord->resposta2 = $row[4];
                    $KnowledgeRecord->importancia = $row[5];

                    // Tenta salvar
                    if ($KnowledgeRecord->save()) {
                        //$this->updatedRows[$index]['final'] = $row[2];
                    } else {
                        dd($row);
                       // $this->erroRows[] = $row[2];
                        //return response()->json(['error' => 'Falha ao salvar o registro.'], 500);
                    }
                    

                }else if($this->idpacote > 0){
                    $KnowledgeRecord = new KnowledgeRecord();
                    $KnowledgeRecord->bundle_id = $this->idpacote;
                    $KnowledgeRecord->knowledge_base_id = $this->id;
                    $KnowledgeRecord->user_id = Auth::id();
                    $KnowledgeRecord->classificacao = $row[0] ?? null;
                    $KnowledgeRecord->classificacao2 = $row[1] ?? null;
                    $KnowledgeRecord->requisito = $row[2] ?? null;
                    $KnowledgeRecord->resposta = $row[3] ?? null;
                    $KnowledgeRecord->resposta2 = $row[4] ?? null;
                    $KnowledgeRecord->importancia = $row[5] ?? null;

                    $KnowledgeRecord->save();
                }else{
                    $DadosErros = array();
                    $DadosErros['row'] = $row;
                    if($rows->get($index-1)){
                        $DadosErros['lastInserted'] = $rows->get($index-1);
                    }else{
                        $DadosErros['lastInserted'] = null;
                    }

                    if(empty($row[7])){
                        $MsgErro = 'Erro ao processar o arquivo - LINHA/PRODUTO não está preenchida';
                    }else{
                        $MsgErro = 'Erro ao processar o arquivo - Não encontramos o "'.$row[7].'" na nossa lista de Pacotes';
                    }

                    $DadosErrosTotal = array();
                    $DadosErrosTotal['error_message'] = $MsgErro;
                    $DadosErrosTotal['error_data'] = $DadosErros;
                    
                    throw new Exception(json_encode($DadosErrosTotal));
                }
            }else{
                $DadosErros = array();
                $DadosErros['row'] = $row;
                if($rows->get($index-1)){
                    $DadosErros['lastInserted'] = $rows->get($index-1);
                }else{
                    $DadosErros['lastInserted'] = null;
                }

                if(empty($row[7])){
                    $MsgErro = 'Erro ao processar o arquivo - LINHA/PRODUTO não está preenchida';
                }else{
                    $MsgErro = 'Erro ao processar o arquivo - Não encontramos o "'.$row[7].'" na nossa lista de Pacotes';
                }
                
                $DadosErrosTotal = array();
                $DadosErrosTotal['error_message'] = $MsgErro;
                $DadosErrosTotal['error_data'] = $DadosErros;
                
                throw new Exception(json_encode($DadosErrosTotal));
            }
        }
    }


    public static function afterImport(AfterImport $event)
    {

    }


    public function chunkSize(): int
    {
        return 1000; // Processa 100 linhas por vez
    }

}
