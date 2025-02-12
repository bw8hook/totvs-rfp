<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use Carbon\Carbon;
use App\Models\KnowledgeBase;
use App\Models\KnowledgeRecord;
use App\Models\RfpBundle;
use App\Models\RfpAnswer;
use App\Models\UsersDepartaments;
use App\Imports\KnowledgeBaseImport;
use App\Imports\KnowledgeBaseInfoImport;
use App\Exports\KnowledgeBaseExport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use App\Exceptions\RDStationMentoria\RDStationMentoria;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Schedule::call(function () {
    $KnowledgeBase = KnowledgeBase::where('status', "processando")->get();

        if ($KnowledgeBase->count() > 0) {
            foreach ($KnowledgeBase as $item) {
                try {

                    $Bundles = RfpBundle::with('agent')->get();

                    foreach ($Bundles as $bundle) {
                        $Records = KnowledgeRecord::whereNotNull('knowledge_records.bundle_id') // Garante que bundle_id está preenchido
                        ->where('knowledge_base_id', $item->id) // Filtra apenas os registros da base específica
                        ->where('knowledge_records.bundle_id', $bundle->bundle_id) // Aplica o filtro com os bundle_ids selecionados
                        ->join('rfp_bundles', 'knowledge_records.bundle_id', '=', 'rfp_bundles.bundle_id') // Faz o JOIN corretamente
                        ->select(
                            'knowledge_records.id_record',
                            'knowledge_records.classificacao',
                            'knowledge_records.classificacao2',
                            'knowledge_records.requisito',
                            'knowledge_records.resposta',
                            'knowledge_records.resposta2',
                            'knowledge_records.observacao',
                            'rfp_bundles.bundle'
                        )
                        ->get();
                            
                        if (!$Records->isEmpty()) {
                            //Ajusta o nome do Arquivo
                            $filenamePrev = $item->id.'_'.$item->name.'_'.uniqid();
                            $fileName = preg_replace('/[^\w\-_\.]/', '', $filenamePrev); // Substitui caracteres não permitidos por "_"
                            $fileName = trim($fileName, '_');
                            $fileName = Str::slug($fileName).'.csv';

                            // Ajusta o nome do Produto
                            $BundleName = preg_replace('/[^\w\-_\.]/', '', $bundle->bundle); // Substitui caracteres não permitidos por "_"
                            $BundleName = trim($BundleName, '_');
                            $BundleName = Str::slug($BundleName);

                            // Ajusta o Path do S3
                            $filePath = 'cdn/knowledge/base_exported/'.$BundleName.'/'.$fileName;

                            dd($filePath);
    
                            // Chama a exportação do EXCEL
                            $export = new KnowledgeBaseExport($item->id, $Records);
                            Excel::store($export, $filePath, 's3');

                       

                            //$UploadedFile = Storage::disk('s3')->put($filePath, file_get_contents($File));
                            $TempFile = Storage::disk('s3')->temporaryUrl($filePath,now()->addMinutes(60));                      
                          
                            $Data = array();
                            $Data['agent_id'] = $bundle->agent->agent_id;
                            $Data['base_id'] = $bundle->agent->knowledge_id;
                
                            $RDStationMentoria = new RDStationMentoria();
                            $RDStationMentoria->classBases();
                            
                            $gBaseById = $RDStationMentoria->Bases->getBaseById( $Data['base_id']);
                            dd($gBaseById);
                            $gBaseById['sources'] = [];

                            if (!empty($gBaseById['id'])) {
                                $antes = $gBaseById['sources'];
                                

                                    $Arquivo = array();
                                    $Arquivo['url'] = $TempFile;
                                    $Arquivo['title'] = $fileName;
                                    $Arquivo['img'] = null;
                                    $Arquivo['description'] = $fileName;
                                    $Arquivo['key'] = $fileName;
                                    $Arquivo['ext'] = "text/csv";
                               
                                    $gBaseById['sources'][] = ["type" => "file", "file" => $Arquivo];                               
                                  
                                    $updateBase = $RDStationMentoria->Bases->updateBase($gBaseById['id'], $gBaseById);
                                    if (!empty($updateBase['id'])) {
                                        dd($filePath);
                                    }else{
                                        dd($updateBase);
                                    }

                                   
                                 

                            } else {
                                dd("erro");
                            }
                        }
                    }

                    // Excel::store(new NewProjectExport($updatedData), $filePath, 'local');        
                } catch (\Exception $e) {
                    dd($e);
                }
            }
        } 
})->everyMinute();