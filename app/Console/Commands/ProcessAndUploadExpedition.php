<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

use App\Exports\KnowledgeCorrectionExport;
use App\Models\ProjectAnswer;
use App\Models\ProjectRecord;
use App\Models\KnowledgeBaseExported;
use App\Models\RfpBundle;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ProcessAndUploadExpedition extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-and-upload-expedition';

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
            $Bundles = RfpBundle::with('agent')->get();

            foreach ($Bundles as $bundle) {
                $Records = ProjectRecord::whereNotNull('project_records.bundle_id')
                    ->where('project_records.bundle_id', $bundle->bundle_id)
                    ->where('project_records.status', 'user edit')
                    ->whereNull('project_records.retroalimentacao')
                    ->get();

                if (!$Records->isEmpty()) { 
                    $filenamePrev = 'retroalimentacao_'.$bundle->bundle_id.'_'.uniqid();
                    $fileName = preg_replace('/[^\w\-_\.]/', '', $filenamePrev);
                    $fileName = trim($fileName, '_');
                    $fileName = Str::slug($fileName).'.csv';

                    $BundleName = preg_replace('/[^\w\-_\.]/', '', $bundle->bundle);
                    $BundleName = trim($BundleName, '_');
                    $BundleName = Str::slug($BundleName);

                    $filePath = 'cdn/knowledge/base_exported_corrections/'.$BundleName.'/'.$fileName;

                    // Cria um Registro de Arquivo Exportado
                    $KnowledgeBaseExported = new KnowledgeBaseExported();
                    // Por ser um campo obrigatório, deixamos fixo com o ID 1
                    $KnowledgeBaseExported->user_id = 1;
                    $KnowledgeBaseExported->bundle_id = 11;
                    //$KnowledgeBaseExported->default_base_id = $item->id;
                    $KnowledgeBaseExported->save();
                    $KnowledgeBaseExportedid = $KnowledgeBaseExported->id;
                    $KnowledgeBaseExported->filepath = $filePath;
                    $KnowledgeBaseExported->filename = $fileName;

                    $RecordsData = [];
                    foreach ($Records as $key => $record) {
                        $ProjectAnswer = ProjectAnswer::where('requisito_id', $record->id)->first();
                        $RecordData = [];
                        $RecordData['id_record'] = $record->id;
                        $RecordData['processo'] = $record->processo;
                        $RecordData['subprocesso'] = $record->subprocesso;
                        $RecordData['requisito'] = $ProjectAnswer->requisito;
                        //$RecordData['Resposta'] = ;
                        $RecordData['modulo'] = $ProjectAnswer->modulo;
                        $RecordData['observações'] = $ProjectAnswer->observacao;
                        $RecordData['produto'] = $ProjectAnswer->linha_produto;
                        $RecordsData[] = $RecordData;
                    }

                    // Envia os arquivos para a S3 e Pega a URL
                    $export = new KnowledgeCorrectionExport(  1, $RecordsData, $KnowledgeBaseExportedid); 
                    $Exports = Excel::store($export, $filePath, 's3');
                    $fileUrl = Storage::disk('s3')->url($filePath);
        
                    if (!empty($fileUrl)) {
                        // Atualiza com a URL
                        $KnowledgeBaseExported->file_url = $fileUrl;
                        $KnowledgeBaseExported->save();
                        //$item->status = 'processado';
                        //$item->save();
                    }
                } else {
                    Log::error("Erro ao processar a base de conhecimento");
                }
            }
        } catch (\Exception $e) {
            Log::error("Erro: " . $e->getMessage());
        }
          
        Log::info('Finalizando o processamento da base de conhecimento'); // Adiciona log aqui
    }
}
