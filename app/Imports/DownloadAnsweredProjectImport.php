<?php

namespace App\Imports;

use App\Models\ProjectAnswer;
use App\Models\ProjectRecord;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DownloadAnsweredProjectImport implements  ToCollection, WithHeadingRow
{   
     private $spreadsheet;
    private $processedData = [];
    protected $id;

    protected $ProjectRecords;

    public function __construct($id, $ProjectRecords = [])
    {
        $this->id = $id;
        $this->ProjectRecords = $ProjectRecords;
        $this->spreadsheet = new Spreadsheet();
    }
    
    public function collection(Collection $rows)
    {
        // Preparar planilha de destino
        $worksheet = $this->spreadsheet->getActiveSheet();

        // Definir cabeçalhos originais
        $headers = [
            'PROCESSO', 
            'SUBPROCESSO', 
            'DESCRIÇÃO DO REQUISITO',
            'RESPOSTA',
            'MÓDULOS',
            'PRODUTO PRINCIPAL',
            'OBSERVAÇÕES',
            'PRODUTOS ADICIONAIS'
        ];

         // Adicionar cabeçalhos
    foreach ($headers as $col => $header) {
        $worksheet->setCellValueByColumnAndRow($col + 1, 1, $header);
    }

        // Estilizar linha de cabeçalho
        $headerRange = 'A1:' . $worksheet->getHighestColumn() . '1';
        
        $worksheet->getStyle($headerRange)->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => '4F81BD'] // Azul corporativo
            ],
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'], // Texto branco
                'size' => 12
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // // Adicionar cabeçalhos
        // foreach ($headers as $colIndex => $header) {
        //     $worksheet->setCellValueByColumnAndRow($colIndex + 1, 1, $header);
        // }

        // Ajustar largura das colunas automaticamente
        foreach (range('A', $worksheet->getHighestColumn()) as $columnID) {
            $worksheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Adicionar dados
        $rowNumber = 2; // Começar da segunda linha


        // Processar linhas
        foreach ($rows as $index => $row) {
            // Valores originais
            $processo = $row['processo'] ?? '';
            $subprocesso = $row['subprocesso'] ?? '';
            $descricaoRequisito = $row['descricao_do_requisito'] ?? '';

            if(!empty($processo) && !empty($subprocesso) && !empty($descricaoRequisito)){

                // Buscar informações adicionais
                $BuscaResposta = $this->buscarResposta($processo, $subprocesso, $descricaoRequisito);
                $resposta = $BuscaResposta->aderencia_na_mesma_linha;
                $modulos = $BuscaResposta->modulo;
                $produtoPrincipal = $BuscaResposta->linha_produto;
                $observacoes = $BuscaResposta->observacao;
                $produtosAdicionais = '';

                // Adicionar linha processada
                $worksheet->setCellValueByColumnAndRow(1, $index + 2, $processo);
                $worksheet->setCellValueByColumnAndRow(2, $index + 2, $subprocesso);
                $worksheet->setCellValueByColumnAndRow(3, $index + 2, $descricaoRequisito);
                $worksheet->setCellValueByColumnAndRow(4, $index + 2, $resposta);
                $worksheet->setCellValueByColumnAndRow(5, $index + 2, $modulos);
                $worksheet->setCellValueByColumnAndRow(6, $index + 2, $produtoPrincipal);
                $worksheet->setCellValueByColumnAndRow(7, $index + 2, $observacoes);
                $worksheet->setCellValueByColumnAndRow(8, $index + 2, $produtosAdicionais);
            }
        }
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function(BeforeImport $event) {
                Log::info('Iniciando importação do Excel');
            },
            AfterImport::class => function(AfterImport $event) {
                Log::info('Importação do Excel concluída');
            }
        ];
    }

    // Métodos de busca (implementar conforme necessidade)
    private function buscarResposta($processo, $subprocesso, $descricaoRequisito)
    {
        $Records = ProjectRecord::where('project_records.project_file_id', $this->id)
        ->where('project_records.processo', $processo)
        ->where('project_records.subprocesso', $subprocesso)
        ->where('project_records.requisito', $descricaoRequisito)
        ->first();

        if(isset($Records->project_answer_id)){
            $ProjectAnswer = ProjectAnswer::where('project_answer.id', $Records->project_answer_id)->first();
            return $ProjectAnswer;
        }else{
            $RecordsFilter = ProjectRecord::where('project_records.project_file_id', $this->id)
            ->where('project_records.subprocesso', $subprocesso)
            ->where('project_records.requisito', $descricaoRequisito)
            ->first();

            if(isset($RecordsFilter->project_answer_id)){
                $ProjectAnswer = ProjectAnswer::where('project_answer.id', $RecordsFilter->project_answer_id)->first();
                return $ProjectAnswer;
            }
        }
    
    }

    // Método para salvar e preparar download
    public function getSpreadsheet()
    {
        return $this->spreadsheet;
    }
}