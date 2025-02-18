<?php

namespace App\Imports;

use App\Models\Project;
use App\Models\RfpBundle;
use App\Models\ProjectRecord;
use App\Models\KnowledgeError;
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

class ProjectRecordsImport implements ToCollection, WithStartRow, WithEvents, WithMultipleSheets
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
        return 2; // Ignora as duas primeiras linhas
    }

    public function collection(Collection $rows)
    {
        // Ignorar a primeira linha o cabeçalho
        $rows = $rows->skip(1);

        // Busca em Todas as linhas
        foreach ($rows as $index => $row) {  
            // Busca se o PRODUTO enviado está cadastrado na lista
            $bundleIDFound = $this->ListBundles[$row[7]] ?? null;
           
            // Salva o registro
            $ProjectRecord = new ProjectRecord();
            // Dados de configuração
                $ProjectRecord->project_file_id = $this->id;
                $ProjectRecord->user_id = Auth::id();
                $ProjectRecord->spreadsheet_line = $index;
                
                // Valida o PRODUTO
                if (!$bundleIDFound) {
                    $ProjectRecord->bundle_id = null;
                }else{
                    $ProjectRecord->bundle_id = $bundleIDFound;
                }
                
                // Dados do arquivo
                $ProjectRecord->processo = $row[0];
                $ProjectRecord->subprocesso = $row[1];
                $ProjectRecord->requisito = $row[2];
                $ProjectRecord->status = "aguardando";

            // Tenta salvar
            if ($ProjectRecord->save()) {
                //$this->updatedRows[$index]['final'] = $row[2];
            }else{
                dd($row);        
            }   
        }
    }


    public static function afterImport(AfterImport $event)
    {

    }
}

