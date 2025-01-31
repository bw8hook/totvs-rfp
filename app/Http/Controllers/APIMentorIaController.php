<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class APIMentorIaController extends Controller
{
   
    public function webhooks($agente=null,$treinamento=null,$event=null){
        
        $MODE="MentorIATreino";
        $DADOS=[ 'status'=>false ];
        $agora=date("Ymd_H_i_s");
        $posfixo=GlobalFuncoes::limpaNome(strtolower($agente??""))."_".GlobalFuncoes::limpaNome(strtolower($treinamento??""));
        
        $log="NAO";
        if ($_SERVER['REQUEST_METHOD'] == 'POST'&&empty($_POST)) { $_POST=file_get_contents("php://input");
            if(gettype($_POST)=="string"){ $_POST = json_decode($_POST,true); }
        }
        
        $caminho = storage_path('app/'.$MODE.'/'); GlobalFuncoes::criarPasta($caminho); 
        $arquivo=$agora."-".$posfixo.".json";
        
        // dd($treinamento,$agente,$arquivo,$posfixo,$event);

        if(!empty($agente)){

            $magicWrite = new MagicWriteAPI('',2);
            $magicWrite->MagicWriteAgents();
            $listAgents = $magicWrite->Agents->listAgents();
            if(!empty($listAgents["data"])){
                $agentsPorNome = array_filter($listAgents["data"], function ($agents) use ($agente) {
                    return preg_replace('/[^A-Za-z0-9]/', '', GlobalFuncoes::limpaNome(strtolower($agents["name"]))) === preg_replace('/[^A-Za-z0-9]/', '', GlobalFuncoes::limpaNome(strtolower($agente))); 
                });
                if(!empty($agentsPorNome)&& count($agentsPorNome)==1 ){
                    $agentsPorNome=$agentsPorNome[0];

                    dd($agentsPorNome,$treinamento,$agente,$arquivo,$posfixo,$event);

                    $magicWrite1 = new MagicWriteAPI('',1);
                    $magicWrite1->MagicWriteIngestions();
                    $allIngestions =[];
                    do {
                        $listIngestions = $magicWrite1->Ingestions->listIngestions(10);
                        if (!empty($listIngestions["data"])) {
                            foreach($listIngestions["data"] as $Ingestion){
                                
                            }
                            $allIngestions = array_merge($allIngestions,$listIngestions["data"]);
                        }else{ break; }
                        if (empty($listIngestions["cursor"])) { break; }

                    } while (true);                        

                    // https://bw8.hook.app.br/treino-mentoria/api/assistentetotvsrhlinharm/dadosimportadoviaapi
                    dd($listIngestions,$agentsPorNome,$listAgents);
                }
            }
            dd($treinamento,$agente,$listAgents);


        }else{ $DADOS['error'] = "Sem nome agente"; }


        if($log=="SIM"&&!empty($_POST)){ 
            file_put_contents($caminho.$arquivo, json_encode([ "agente"=>$agente, "treinamento"=>$treinamento, "get"=>$_GET,"post"=>$_POST ]));
        }  
        
        GlobalFuncoes::logAPI(ucfirst("MentorIA_Treinamento"));
        
        header("Content-Type:application/json;");
        header('HTTP/1.0 200 OK');
        return json_encode($DADOS);        
        
    } 
}


            /*
            $listIngestions = $magicWrite->Ingestions->listIngestions(10,"arena8"); //50,"arena8"
            dd($listIngestions,$treinamento,$agente,$magicWrite);

            dd( 
                array_column(array_filter($listIngestions['data'] ?? [], function($item) {
                    return isset($item['description']);
                }), 'description'),

                $listIngestions,
                $magicWrite
            );

            if (!empty($listIngestions["data"])) {

                $filtroPorNome = array_filter($listIngestions["data"], function ($ingestion) use ($treinamento) { return $ingestion["search"] === ":" . $treinamento; });

                if (!empty($filtroPorNome)) {

                    $filtroMaisRecente = array_reduce($filtroPorNome, function ($carry, $ingestion) {
                        $mostRecentTime = max($ingestion["createdAt"], $ingestion["updatedAt"]);
                        if ($carry === null || $mostRecentTime > $carry["time"]) {
                            return ["time" => $mostRecentTime, "ingestion" => $ingestion];
                        }
                        return $carry;
                    });
                    if (!empty($filtroMaisRecente["ingestion"])) {

                        $TREINO = $filtroMaisRecente["ingestion"];

                        if(!empty($_GET) && !empty($_GET['teste'])) {
                            dd($TREINO,$listIngestions);
                        }
                        if(!empty($TREINO['ingestionID'])){

                            $url='https://hook.app.br/treino_arquivo1.pdf';
                            $title="Arquivo teste 1";
                            $ext="pdf";
            
                            $TREINO['sources'][]=[
                                'type' => 'file',
                                'file' => [
                                    'url' => $url, 'title' => $title, 'ext' => $ext,
                                    'description' =>"Recebido via API ".date("Y_m_d_H_i"),
                                ],
                            ];              

                            $createIngestion = $magicWrite->Ingestions->createIngestion($TREINO);
                            if(!empty($createIngestion['ingestionID'])){

                                $ingestionID = $createIngestion['ingestionID'];
                                $filteredDelete = array_filter($listIngestions["data"], function ($ingestion) use ($treinamento, $ingestionID) {
                                    return $ingestion["search"] === ":" . $treinamento && $ingestion["ingestionID"] !== $ingestionID;
                                });

                                $deleteIngestion=[];
                                if(!empty($filteredDelete)){
                                    $ingestionIDs = array_column($filteredDelete, "ingestionID");
                                    foreach ($ingestionIDs as $ID) {
                                        $deleteIngestion[] = $magicWrite->Ingestions->deleteIngestion($ID);
                                    }
                                }
                                $updateAgents=[]; $agentsPorNome=[];
                                if(!empty($agente)){
                                    $magicWrite->MagicWriteAgents();
                                    $listAgents = $magicWrite->Agents->listAgents();
                                    $agentsPorNome = array_filter($listAgents["data"], function ($agents) use ($agente) { 
                                        return preg_replace('/[^A-Za-z0-9\-_]/', '', GlobalFuncoes::tirarAcentos(strtolower($agents["name"]))) === preg_replace('/[^A-Za-z0-9\-_]/', '', GlobalFuncoes::tirarAcentos(strtolower($agente))); 
                                    });
                                    if(!empty($agentsPorNome)&& count($agentsPorNome)==1 ){ $agentsPorNome=$agentsPorNome[0];}
                                    if(!empty($agentsPorNome['agentID']) && !in_array($ingestionID, $agentsPorNome['ingestions'])){ 
                                        $agentsPorNome['ingestions'][]=$ingestionID; 
                                        $updateAgents = $magicWrite->Agents->updateAgents($agentsPorNome['agentID'],$agentsPorNome);
                                    }
                                }

                                $DADOS['status'] = true;
                                $DADOS['ingestionID'] = $ingestionID;
                                $DADOS['createIngestion'] = $createIngestion;
                                $DADOS['updateAgents'] = $updateAgents;
                                $DADOS['deleteIngestion'] = $deleteIngestion;
                                $DADOS['agent'] = $agentsPorNome;

                                $log="NAO";


                            }else{ $DADOS['error'] = "ERRO ao criar o treinamento"; $DADOS['createIngestion']=$createIngestion; }

                        }else{ $DADOS['error'] = "Nenhum treinamento encontrado"; }                         
                        
                    }else{ $DADOS['error'] = "Nenhum treinamento mais recente encontrado"; }
                    
                }else{ $DADOS['error'] = "Nenhum treinamento por nome encontrado"; }

            }else{ $DADOS['error'] = "Treinamentos estão vazio"; }
            */

/*
            $ingestionID = "ingest_01HDMBHEH5TZ0JVABZ4JJG30VG"; // Substitua pelo valor desejado
            $filteredDelete = array_filter($listIngestions["data"], function ($ingestion) use ($treinamento, $ingestionID) {
                return $ingestion["search"] === ":" . $treinamento && $ingestion["ingestionID"] !== $ingestionID;
            });
            if(!empty($filteredDelete)){
                $ingestionIDs = array_column($filteredDelete, "ingestionID");
                dd($ingestionIDs,$filteredDelete, $listIngestions);
            }
            dd($filteredDelete, $listIngestions);
            
            $TREINO = $magicWrite->getTreinamento($treinamento);
            if(!empty($TREINO['ingestionID'])){
                $url='https://hook.app.br/treino_arquivo1.pdf';
                $title="Arquivo teste 1";
                $ext="pdf";

                $TREINO['sources'][]=[
                    'type' => 'file',
                    'file' => [
                        'url' => $url,
                        'title' => $title,
                        'ext' => $ext,
                        'description' =>"Recebido via API ".date("Y_m_d_H_i"),
                    ],
                ];              
                
                $ingestions = $magicWrite->MagicWriteIngestions();
                $createIngestion = $ingestions->createIngestion($TREINO);

                dd($createIngestion, $TREINO);

            }else{ $DADOS['error'] = "Nenhum treinamento encontrado"; }            
*/

                /*

array:3 [▼
  "ingestionID" => "ingest_01HDMAGJPS3NZ8R3VZ0SHHXXPY"
  "companyID" => "co_01H44Y2BQJQWSB7WDMHG7NCMNV"
  "sources" => array:1 [▼
    0 => array:2 [▼
      "type" => "file"
      "file" => array:4 [▼
        "url" => "https://hook.app.br/treino_arquivo1.pdf"
        "title" => "Arquivo teste 1"
        "ext" => "pdf"
        "description" => "Recebido via API"
      ]
    ]
  ]
]

                $ingestions = $magicWrite->MagicWriteIngestions();
                $create = $ingestions->createIngestion('file', $url, $title, "Recebido via API ", "pdf");

                dd($create,'file', $url, $title, "Recebido via API ", "pdf",$TREINO);
                */
                


                /*
                $fileTreino = $caminho."treino_arquivo1.pdf";
                if(file_exists($fileTreino)){
                    createIngestion($type='file', $url="", $title
                }                
                dd(file_exists($fileTreino),$TREINO,$treinamento,$caminho);
                */

            /*
            

            $listIngestions = $ingestions->listIngestions($treinamento);
            if(!empty($listIngestions['data'])){
                $filteredIngestions = array_filter($listIngestions['data'], function ($ingestion) use ($treinamento) {
                    return $ingestion['search'] === ':' . $treinamento;
                });
                $TREINO = reset($filteredIngestions);

                if(!empty($TREINO)){
                
                    dd($TREINO,$treinamento,$listIngestions);

                }else{ $DADOS['error'] = "Nenhum elemento encontrado"; }
            }else{ $DADOS['error'] = "Treinamentos estão vazio"; }
            /*
            $completions = $magicWrite->MagicWriteCompletions();
            $agents = $magicWrite->MagicWriteAgents();
            $AGENTES = $agents->listAgents();        
            $TREINOS = [];
            if(!empty($listIngestions['data'])){
                $TREINOS = array_map(fn($item) => ["description" => $item["description"], "search" => $item["search"], "ingestionID" => $item["ingestionID"]], $listIngestions['data']);
            }

            dd($TREINOS,$AGENTES,$listIngestions,$sigla,$event,$arquivo,$caminho);
            */