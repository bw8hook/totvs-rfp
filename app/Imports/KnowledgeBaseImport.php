<?php

namespace App\Imports;

use App\Models\KnowledgeBase;
use App\Models\RfpBundle;
use App\Models\KnowledgeRecord;
use App\Models\KnowledgeError;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
// IMPORTAÇÕES DO EXCEL
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;

use Exception;

class KnowledgeBaseImport implements ToCollection, WithStartRow, WithEvents, WithMultipleSheets
{
    use RegistersEventListeners;
    use Importable;

    protected $id;
    protected $idpacote; // Variável para armazenar o ID
    public $Erros = [];
    private $ListBundles = [];
    public $updatedRows = [];
    public $erroRows = [];
    

    public function __construct($id)
    {
         $this->id = $id;
        // $this->idpacote = $idpacote; // Define o ID recebido no construtor
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
        return 1; // Ignora as duas primeiras linhas
    }

    public function collection(Collection $rows)
    {
        try {
            // Ignorar a primeira linha o cabeçalho
            $rows = $rows->skip(1);

            $this->validateAndCleanExcel($rows);

            // Busca em Todas as linhas
            foreach ($rows as $index => $row) {  
                // Busca se o PRODUTO enviado está cadastrado na lista
                $bundleIDFound = $this->ListBundles[$row[5]] ?? null;
            
                // Salva o registro
                $KnowledgeRecord = new KnowledgeRecord();
                    // Dados de configuração
                    $KnowledgeRecord->knowledge_base_id = $this->id;
                    $KnowledgeRecord->user_id = Auth::id();
                    $KnowledgeRecord->bundle_old = $row[5];
                    $KnowledgeRecord->spreadsheet_line = $index;

                    // Valida o PRODUTO
                    if (!$bundleIDFound) {
                        $KnowledgeRecord->bundle_id = null;
                    }else{
                        $KnowledgeRecord->bundle_id = $bundleIDFound;
                    }
                    
                    // Dados do arquivo
                    $KnowledgeRecord->processo = $row[0];
                    $KnowledgeRecord->subprocesso = $row[1];
                    $KnowledgeRecord->requisito = $row[2];
                    $KnowledgeRecord->resposta = $row[3];
                    $KnowledgeRecord->modulo = $row[4];
                    $KnowledgeRecord->observacao = $row[6];
                    $KnowledgeRecord->status = "aguardando";

                // Tenta salvar
                if ($KnowledgeRecord->save()) {
                    //$this->updatedRows[$index]['final'] = $row[2];
                }else{
                    dd($row);        
                }   
        
                // }else{
                //     $DadosErros = array();
                //     $DadosErros['row'] = $row;
                //     if($rows->get($index-1)){
                //         $DadosErros['lastInserted'] = $rows->get($index-1);
                //     }else{
                //         $DadosErros['lastInserted'] = null;
                //     }

                //     if(empty($row[7])){
                //         $MsgErro = 'Erro ao processar o arquivo - LINHA/PRODUTO não está preenchida';
                //     }else{
                //         $MsgErro = 'Erro ao processar o arquivo - Não encontramos o "'.$row[7].'" na nossa lista de Pacotes';
                //     }
                    
                //     $DadosErrosTotal = array();
                //     $DadosErrosTotal['error_message'] = $MsgErro;
                //     $DadosErrosTotal['error_data'] = $DadosErros;
                    
                //     throw new Exception(json_encode($DadosErrosTotal));
                // }
            }
        } catch (Exception $e) {
            Log::error('Erro no processamento do Excel', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
    
            throw new Exception(
                "Erro no processamento do arquivo Excel. \n\n" .
                $e->getMessage()
            );
        }
    }


    public static function afterImport(AfterImport $event)
    {

    }

    private function validateAndCleanExcel(Collection $rows)
    {
        $expectedHeaders = [
            'Processo',
            'Subprocesso',
            'Descrição do Requisito',
            'Resposta',
            'Módulo',
            'Produto',
            'Observações'
        ];
    
        if ($rows->isEmpty()) {
            throw new Exception('O arquivo Excel está vazio.');
        }
    
        // Limpa e valida o cabeçalho
        $headerRow = $rows->first()
            ->filter(function ($value) {
                return !empty(trim((string)$value));
            })
            ->map(function ($value) {
                return mb_strtolower(trim((string)$value), 'UTF-8');
            })
            ->values();
    
        // Converte os cabeçalhos esperados para minúsculas
        $expectedHeadersLower = array_map(function($header) {
            return mb_strtolower(trim($header), 'UTF-8');
        }, $expectedHeaders);
    
        // Encontra as colunas que estão faltando
        $missingColumns = array_diff($expectedHeadersLower, $headerRow->toArray());
    
        if (!empty($missingColumns)) {
            // Mapeia de volta para os nomes originais das colunas
            $missingOriginalNames = array_filter($expectedHeaders, function($header) use ($missingColumns) {
                return in_array(mb_strtolower(trim($header), 'UTF-8'), $missingColumns);
            });
    
            throw new Exception(
                "Colunas obrigatórias faltando no arquivo: \n\n" .
                "- " . implode("\n- ", $missingOriginalNames) . "\n\n" .
                "Por favor, adicione estas colunas ao arquivo e tente novamente."
            );
        }
    
        // Valida a ordem das colunas
        foreach ($headerRow as $index => $header) {
            if ($header !== $expectedHeadersLower[$index]) {
                throw new Exception(
                    "Coluna na posição errada ou com nome incorreto.\n" .
                    "Posição " . ($index + 1) . ":\n" .
                    "Esperado: '{$expectedHeaders[$index]}'\n" .
                    "Encontrado: '{$header}'\n\n" .
                    "A ordem correta das colunas deve ser:\n" .
                    "- " . implode("\n- ", $expectedHeaders)
                );
            }
        }
    
        // Remove colunas vazias de todas as linhas
        $expectedColumnCount = count($expectedHeaders);
        return $rows->map(function ($row) use ($expectedColumnCount) {
            return $row->filter(function ($value, $key) use ($expectedColumnCount) {
                return $key < $expectedColumnCount;
            })->values();
        });
        }
    




}
