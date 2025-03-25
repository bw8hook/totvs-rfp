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
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\MultipartStream;



class UploadKnowledgeBase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:upload-knowledge-base';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function handle()
    {
        Log::info('Iniciando o processamento da base de conhecimento'); // Adiciona log aqui
        
        $client = new Client();
        $Bundles = RfpBundle::with('agent')->get();
        $RDStationMentoria = new RDStationMentoria();
        $RDStationMentoria->classBases();
        $UpdateBase = false;

        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

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

                        $fileContent = Storage::disk('s3')->get($item->filepath);
                        $localFileName = uniqid('file-') . '.csv';
                        Storage::disk('local')->put('temp/' . $item->filename, $fileContent);
                        $localFilePath = storage_path('app/temp/' . $item->filename);

                        try {
                            $url = "https://totvs-ia.hook.app.br/v1/datasets/".$bundle->agent->knowledge_id_hook."/document/create-by-file";
                
                            // Preparar os dados multipart
                            $multipart = [
                                [
                                    'name' => 'data',
                                    'contents' => json_encode([
                                        "indexing_technique" => "high_quality",
                                        "process_rule" => [
                                            "rules" => [
                                                "pre_processing_rules" => [
                                                    ["id" => "remove_extra_spaces", "enabled" => true],
                                                    ["id" => "remove_urls_emails", "enabled" => true]
                                                ],
                                                "segmentation" => [
                                                    "separator" => "###",
                                                    "max_tokens" => 500
                                                ]
                                            ],
                                            "mode" => "custom"
                                        ]
                                    ]),
                                    'headers' => [
                                        'Content-Type' => 'text/plain'
                                    ]
                                ],
                                [
                                    'name' => 'file',
                                    'contents' => fopen($localFilePath, 'r'),
                                    'filename' => basename($localFilePath),
                                    'headers' => [
                                        'Content-Type' => mime_content_type($localFilePath)
                                    ]
                                ]
                            ];

                            // Criar o stream multipart
                            $multipartStream = new MultipartStream($multipart);

                            // Fazer a requisição
                            $response = $client->request('POST', $url, [
                                'headers' => [
                                    'Authorization' => "Bearer dataset-XSIGaQdZXZdDLux237SDv7s9",
                                    'Content-Type' => 'multipart/form-data; boundary=' . $multipartStream->getBoundary()
                                ],
                                'body' => $multipartStream
                            ]);

                            $statusCode = $response->getStatusCode();
                            $body = $response->getBody()->getContents();
                        
                        } catch (GuzzleException $e) {
                            //echo "Erro: " . $e->getMessage();
                        }

                        $item->status = "exportado";
                        $item->save();
                    } 
        
                    if($UpdateBase){
                        $updatedBase = $RDStationMentoria->Bases->updateBase($gBaseById['id'], $gBaseById);
                        $UpdateBase = false;
                        
                    }
                }
            }
        }
       
        Log::info('Finalizando o processamento da base de conhecimento'); // Adiciona log aqui
    }
}