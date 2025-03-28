<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\KnowledgeBase;
use App\Models\KnowledgeBaseExported;
use App\Models\KnowledgeRecord;
use App\Models\RfpBundle;
use App\Exports\KnowledgeBaseExport;
use App\Exceptions\RDStationMentoria\RDStationMentoria;
use Illuminate\Support\Str;

class ProcessKnowledgeBase extends Command
{
    protected $signature = 'knowledgebase:process';

    protected $description = 'Processa a base de conhecimento';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Log::info('Iniciando o processamento da base de conhecimento'); // Adiciona log aqui
        $KnowledgeBase = KnowledgeBase::where('status', "processando")->get();
        if ($KnowledgeBase->count() > 0) {
            foreach ($KnowledgeBase as $item) {
                try {
                    $Bundles = RfpBundle::with('agent')->get();

                    foreach ($Bundles as $bundle) {
                        
                        $Records = KnowledgeRecord::where('knowledge_base_id', $item->id)
                        ->where('knowledge_records.status', 'aguardando')
                        ->join('knowledge_records_bundles', 'knowledge_records.id_record', '=', 'knowledge_records_bundles.knowledge_record_id')
                        ->where('knowledge_records_bundles.bundle_id', $bundle->bundle_id) // Filtrar pelo bundle específico
                        ->select(
                            'knowledge_records.id_record',
                            'knowledge_records.processo',
                            'knowledge_records.subprocesso',
                            'knowledge_records.requisito',
                            'knowledge_records.resposta',
                            'knowledge_records.modulo',
                            DB::raw('(
                                SELECT rb.bundle 
                                FROM knowledge_records_bundles krb
                                JOIN rfp_bundles rb ON krb.bundle_id = rb.bundle_id
                                WHERE krb.knowledge_record_id = knowledge_records.id_record
                                AND krb.bundle_status = "principal"
                                LIMIT 1
                            ) as produto_principal'),
                            'knowledge_records.observacao',
                            DB::raw('(
                                SELECT GROUP_CONCAT(
                                    COALESCE(
                                        rb.bundle,
                                        krb.old_bundle
                                    )
                                )
                                FROM knowledge_records_bundles krb
                                LEFT JOIN rfp_bundles rb ON krb.bundle_id = rb.bundle_id
                                WHERE krb.knowledge_record_id = knowledge_records.id_record
                                AND krb.bundle_status = "adicional"
                            ) as produtos_adicionais')
                        )
                        ->get();

                        if (!$Records->isEmpty()) {
                            $filenamePrev = $item->id.'_'.$item->name.'_'.uniqid();
                            $fileName = preg_replace('/[^\w\-_\.]/', '', $filenamePrev);
                            $fileName = trim($fileName, '_');
                            $fileName = Str::slug($fileName).'.csv';

                            $BundleName = preg_replace('/[^\w\-_\.]/', '', $bundle->bundle);
                            $BundleName = trim($BundleName, '_');
                            $BundleName = Str::slug($BundleName);

                            $filePath = 'cdn/knowledge/base_exported/'.$BundleName.'/'.$fileName;

                            // Cria um Registro de Arquivo Exportado
                            $KnowledgeBaseExported = new KnowledgeBaseExported();
                            $KnowledgeBaseExported->user_id = $item->user_id;
                            $KnowledgeBaseExported->bundle_id = $bundle->bundle_id;
                            $KnowledgeBaseExported->default_base_id = $item->id;
                            $KnowledgeBaseExported->save();
                            $KnowledgeBaseExportedid = $KnowledgeBaseExported->id;
                            $KnowledgeBaseExported->filepath = $filePath;
                            $KnowledgeBaseExported->filename = $fileName;
                            
                            // Envia os arquivos para a S3 e Pega a URL
                            $export = new KnowledgeBaseExport($item->id, $Records, $KnowledgeBaseExportedid);
                            Excel::store($export, $filePath, 's3');
                            $fileUrl = Storage::disk('s3')->url($filePath);

                            if (!empty($fileUrl)) {
                                // Atualiza com a URL
                                $KnowledgeBaseExported->file_url = $fileUrl;
                                $KnowledgeBaseExported->save();
                                $item->status = 'processado';
                                $item->save();
                            }
                        } else {
                           Log::error("Erro ao processar a base de conhecimento");
                        }
                    }
                } catch (\Exception $e) {
                    Log::error("Erro: " . $e->getMessage());
                }
            }
        } else {
            Log::info('Nenhuma base de conhecimento com status "processando" encontrada');
        }

        Log::info('Finalizando o processamento da base de conhecimento'); // Adiciona log aqui
    }
}
