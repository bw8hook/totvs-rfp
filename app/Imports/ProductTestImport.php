<?php

namespace App\Imports;

use App\Models\Agent;
use App\Models\Category;
use App\Models\KnowledgeBase;
use App\Models\LineOfProduct;
use App\Models\RfpBundle;
use App\Models\RfpProcess;
use App\Models\KnowledgeRecord;
use App\Models\KnowledgeError;
use App\Models\ServiceGroup;
use App\Models\Type;
use DB;
use Exception;
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
use Maatwebsite\Excel\Validators\Failure;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ProductTestImport implements ToCollection, WithStartRow, WithEvents, WithMultipleSheets, SkipsOnFailure, WithChunkReading
{
    use RegistersEventListeners;
    use Importable;

    protected $idpacote; // Variável para armazenar o ID
    public $Erros = [];
    private $ListBundles = [];
    public $updatedRows = [];
    public $erroRows = [];
    
    private $rowCount = 0;
    private $successCount = 0;
    private $failedCount = 0;

    public function __construct()
    {

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
        $instance->ListProcess = RfpProcess::all()->pluck('id', 'process')->toArray();
    }
    
    public function startRow(): int{
        return 1; // Ignora as duas primeiras linhas
    }

    public function collection(Collection $rows)
    {
        try {
            // Ignorar as primeiras duas linhas (cabeçalho)
            $rows = $rows->slice(2);
    
            // Log do total de linhas
            Log::info('Total de linhas a processar: ' . $rows->count());
    
            // Processamento em lote para melhor performance
            $rows->each(function($row, $index) {
                try {
                    // Incrementar contador de linhas
                    $this->rowCount++;

                    Log::info('Processando Linha: ' .$this->rowCount);

                    // Buscar registros relacionados
                    //$Type = Type::where('name', $row[0])->first();
                    //$Category = Category::where('name', $row[3])->first();
                    //$ServiceGroup = ServiceGroup::where('name', $row[4])->first();   
                    //$Agent = Agent::where('agent_name', $row[5])->first();

                    // $Type = Type::firstOrCreate(
                    //     ['name' => $row[0]], 
                    //     ['status' => 'ativo' ]
                    // );
                    
                    // $Category = Category::firstOrCreate(
                    //     ['name' => $row[5]], 
                    //     ['status' => 'ativo' ]
                    // );

                    // if(!empty($row[6])){
                    //     $ServiceGroup = ServiceGroup::firstOrCreate(
                    //         ['name' => $row[6]], 
                    //         ['status' => 'ativo' ]
                    //     );
                    // }else{
                    //     $ServiceGroup = null;
                    // }
                
                    $Agent = Agent::firstOrCreate(
                        ['agent_name' => $row[13]], 
                        ['status' => 'ativo' ]
                    );

                    // // Criar ou encontrar Bundle
                    // $Bundle = RfpBundle::firstOrCreate(
                    //     [
                    //         'bundle' => $row[1],
                    //     ],
                    //     [
                    //         'agent_id' => $Agent->id,
                    //     ]
                    // );


                    $Bundle = RfpBundle::where('bundle', $row[1])->first();

                    

                    if ($Bundle->id > 660) {

                        dd($Bundle);
                        
                        $Bundle->agent_id = $Agent->id;
                        $Bundle->save();
                    }



                    //$Bundle = RfpBundle::where('bundle', $row[1])->update(['agent_id' => $Agent->id]);

                   

                    //$Bundle = RfpBundle::where('bundle', $row[1])->first();


                    // Verificar se Bundle foi criado/encontrado
                    // if ($Bundle->bundle_id) {
                        
                    //     // Associar Linha de Produto
                    //     $this->attachLineOfProduct($Bundle);
    
                    //     // Associar Segmentos
                    //     $this->attachSegments($Bundle, $row[4]);
    
                    //     $this->successCount++;

                    //     Log::info('Linha: Criada' .$this->rowCount);
                    // } else {
                    //     Log::warning('Não foi possível criar/encontrar Bundle', [
                    //         'row' => $row,                        ]);
                    // }
    
                } catch (\Exception $e) {
                    Log::error('Erro ao processar linha', [
                        'index' => $index,
                        'row' => $row,
                        'error' => $e->getMessage()
                    ]);
                    $this->failedCount++;
                }
            });
    
            // Log de resumo
            Log::info('Importação concluída', [
                'total_rows' => $this->rowCount,
                'successful' => $this->successCount,
                'failed' => $this->failedCount
            ]);
    
        } catch (\Exception $e) {
            Log::error('Erro geral na importação', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }


    // Método para associar Linha de Produto
private function attachLineOfProduct($Bundle)
{
    try {
        $lineOfProduct = LineOfProduct::find(5);
        if ($lineOfProduct) {
            $Bundle->lineOfProduct()->syncWithoutDetaching(5);
        }
    } catch (\Exception $e) {
        Log::error('Erro ao associar Linha de Produto', [
            'bundle_id' => $Bundle->id,
            'error' => $e->getMessage()
        ]);
    }
}

// Método para associar Segmentos
private function attachSegments($Bundle, $segmentString)
{
    $segmentsMap = [
        'Agro' => 14,
        'Construção' => 15,
        'Distribuição' => 16,
        'Educacional' => 17,
        'Financial Services' => 18,
        'Hotelaria' => 19,
        'Jurídico' => 20,
        'Manufatura' => 21,
        'Logística' => 22,
        'Prestadores de Serviços' => 23,
        'Saúde' => 24,
        'Varejo' => 25
    ];

    $segmentsToAttach = [];

    foreach ($segmentsMap as $segment => $id) {
        if (stripos($segmentString, $segment) !== false || stripos($segmentString, 'CROSS') !== false) {
            $segmentsToAttach[] = $id;
        }
    }

    if (!empty($segmentsToAttach)) {
        try {
            $Bundle->segments()->syncWithoutDetaching($segmentsToAttach);
        } catch (\Exception $e) {
            Log::error('Erro ao associar Segmentos', [
                'bundle_id' => $Bundle->id,
                'segments' => $segmentsToAttach,
                'error' => $e->getMessage()
            ]);
        }
    }
}



    public static function afterImport(AfterImport $event)
    {

    }


    public function chunkSize(): int
    {
        return 200; // Tamanho do chunk
    }


    public function getRowCount()
    {
        return $this->rowCount;
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }

    public function getFailedCount()
    {
        return $this->failedCount;
    }



    public function onFailure(Failure ...$failures)
    {
        // Lidar com falhas específicas
        foreach ($failures as $failure) {
            Log::error('Falha na importação', [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors()
            ]);
        }
    }
    


}
