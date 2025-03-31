<?php

namespace App\Console\Commands;

use App\Imports\DownloadAnsweredProjectImport;
use App\Models\ProjectFiles;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel;

class CreateAnsweredFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-answered-file';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('Iniciando o processamento da base de conhecimento');
        try {
            $ProjectFiles = ProjectFiles::where('status', "concluído")
            ->with('bundles')
            ->get();

            foreach ($ProjectFiles as $ProjectFile) {
                try {
                    // Baixar arquivo do S3 para processamento local
                    $tempInputFile = tempnam(sys_get_temp_dir(), 'excel');
                    $contents = Storage::disk('s3')->get($ProjectFile->filepath);
                    file_put_contents($tempInputFile, $contents);
        
                     // Criar instância de importação
                    $import = new DownloadAnsweredProjectImport($ProjectFile->id);

                    // Importar arquivo
                    Excel::import($import, $tempInputFile, null, \Maatwebsite\Excel\Excel::XLSX);

                    // Salvar arquivo processado
                    $tempOutputFile = tempnam(sys_get_temp_dir(), 'excel') . '.xlsx';
                    
                    // Usar IOFactory para criar writer
                    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($import->getSpreadsheet(), 'Xlsx');
                    $writer->save($tempOutputFile);

                    // Enviar arquivo processado para S3
                    $FileName = time() . '_' . uniqid() . '.xlsx';
                    $outputFilePath = 'cdn/projects_answereds/'.$FileName;
                    Storage::disk('s3')->put(
                        $outputFilePath, 
                        file_get_contents($tempOutputFile)
                    );
            
                    // Gerar URL completa
                    $urlCompleta = Storage::disk('s3')->url($outputFilePath);
                    $ProjectFile->answered_file = $urlCompleta;
                    $ProjectFile->save();

                    // Opcional: Excluir arquivo temporário
                    if (file_exists($tempOutputFile)) {
                        unlink($tempOutputFile);
                    }

                    // Preparar download
                    // return response()->download(
                    //     $tempOutputFile, 
                    //     $FileName,
                    //     [
                    //         'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    //         'Content-Disposition' => 'attachment; filename="arquivo_processado.xlsx"'
                    //     ]
                    // )->deleteFileAfterSend(true);

                } catch (\Exception $e) {

                    dd($e->getMessage());
                    Log::error('Erro no processamento do Excel', [
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);

                    return 'Erro no processamento';
                }        
            }
        } catch (\Exception $e) {
            Log::error("Erro: " . $e->getMessage());
        }
          
        Log::info('Finalizando o processamento da base de conhecimento'); // Adiciona log aqui

    }
}
