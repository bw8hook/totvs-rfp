<?php

namespace App\Imports;

use Illuminate\Support\Collection;
// IMPORTAÇÕES DO EXCEL
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;


use Exception;

class KnowledgeBaseInfoImport implements ToCollection, WithChunkReading, WithStartRow, WithEvents, WithMultipleSheets
{
    use RegistersEventListeners;
    use Importable;

    protected $id;
    protected $idpacote; // Variável para armazenar o ID
    public $data = [];
    private $ListBundles = [];
    public $updatedRows = [];
    public $erroRows = [];
    

    public function __construct()
    {
        //$this->id = $id;
        // $this->idpacote = $idpacote; // Define o ID recebido no construtor
    }


    // Validação apenas da primeira aba
    public function sheets(): array
    {
        return [
            1 => $this // Apenas a primeira aba será processada
        ];
    }
    
    public function startRow(): int{
        return 2; // Ignora as duas primeiras linhas
    }

    public function collection(Collection $rows)
    {
        // Busca em Todas as linhas
        foreach ($rows as $index => $row) {  
            if($row[0] == "PROJETO"){
                $project = $row[1];
            }

            if($row[0] == "DATA DA RFP"){
                $excelDate = $row[1];
                $date = Date::excelToDateTimeObject($excelDate);
                $rfp_date = $date->format('Y-m-d');
            }

            if($row[0] == "TIME"){
                $project_team = $row[1];
            }
        }

        $this->data[] = [
            'project' => $project,
            'rfp_date' => $rfp_date,
            'project_team' => $project_team,
        ];
    }
    

    public function chunkSize(): int
    {
        return 1000; // Processa 100 linhas por vez
    }

}
