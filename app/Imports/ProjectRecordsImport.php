<?php

namespace App\Imports;

use App\Models\Project;
use App\Models\RfpBundle;
use App\Models\ProjectRecord;
use App\Models\KnowledgeError;
use App\Models\RfpProcess;
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

class ProjectRecordsImport implements ToCollection, WithStartRow, WithEvents, WithMultipleSheets, WithChunkReading
{
    use RegistersEventListeners;
    use Importable;

    protected $id;

    protected $bundles = [];
    protected $idpacote; // Variável para armazenar o ID
    public $Erros = [];
    private $ListBundles = [];
    public $updatedRows = [];
    public $erroRows = [];
    

    public function __construct($id, $bundles)
    {
         $this->id = $id;
         $this->bundles = $bundles;
        // $this->idpacote = $idpacote; // Define o ID recebido no construtor
    }

    public function chunkSize(): int
    {
        return 1000; // Ajuste conforme necessário
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
        $instance->ListProcess = RfpProcess::all()->pluck('id', 'process')->toArray();
    }
    
    public function startRow(): int{
        return 1; // Ignora as duas primeiras linhas
    }

    public function collection(Collection $rows)
    {
        try{
            $this->validateAndCleanExcel($rows);

            // Ignorar a primeira linha o cabeçalho
            $rows = $rows->skip(1);

            // Busca em Todas as linhas
            foreach ($rows as $index => $row) {  
                // Busca se o PRODUTO enviado está cadastrado na lista
                $processIDFound = $this->ListProcess[$row[0]] ?? null;
            
                // Salva o registro
                $ProjectRecord = new ProjectRecord();
                // Dados de configuração
                    $ProjectRecord->project_file_id = $this->id;
                    $ProjectRecord->user_id = Auth::id();
                    $ProjectRecord->spreadsheet_line = $index;
                    
                    // Valida o PRODUTO
                    if (!$processIDFound) {
                        $ProjectRecord->processo_id = null;
                    }else{
                        $ProjectRecord->processo_id = $processIDFound;
                    }
    
                    $ProjectRecord->processo = $row[0];
                    $ProjectRecord->subprocesso = $row[1];
                    $ProjectRecord->requisito = $row[2];
                    $ProjectRecord->status = "aguardando";

                // Tenta salvar
                if ($ProjectRecord->save()) {
                    // Vincula os Produtos com o RECORD
                    $ProjectRecord->bundles()->sync($this->bundles);
                }else{
                    //dd($row);        
                }   
            }
        } catch (Exception $e) {
    
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

