<?php

namespace App\Console\Commands;
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


class UploadKnowledgeBasecopy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:upload-knowledge-bas2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function handle()
    {
        Log::info('Iniciando o processamento da base de conhecimento'); // Adiciona log aqui
        
        $Bundles = RfpBundle::with('agent')->get();
        $RDStationMentoria = new RDStationMentoria();
        $RDStationMentoria->classBases();
        $UpdateBase = false;
        foreach ($Bundles as $bundle) {
            $gBaseById = $RDStationMentoria->Bases->getBaseById($bundle->agent->knowledge_id);
            
            if (!empty($gBaseById['id'])) {
                $KnowledgeBaseExported = KnowledgeBaseExported::where('status', "aguardando")->where('bundle_id', $bundle->bundle_id)->get();
                if ($KnowledgeBaseExported->count() > 0) {
                    foreach ($KnowledgeBaseExported as $item) {
                        $UpdateBase = true;
                        $Arquivo = array();
                        $Arquivo['url'] = $item->file_url;
                        $Arquivo['title'] = $item->filename;;
                        $Arquivo['img'] = null;
                        $Arquivo['description'] = $item->filename;
                        $Arquivo['key'] = $item->filename;
                        $Arquivo['ext'] = "text/csv";

                        $gBaseById['sources'][] = ["type" => "file", "file" => $Arquivo];

                        $item->status = "exportado";
                        $item->save();
                    } 
        
                if($UpdateBase){
                    $updatedBase = $RDStationMentoria->Bases->updateBase($gBaseById['id'], $gBaseById);
                    $UpdateBase = false;
                    
                }
            }

        }
       
        Log::info('Finalizando o processamento da base de conhecimento'); // Adiciona log aqui
    }
    }
}