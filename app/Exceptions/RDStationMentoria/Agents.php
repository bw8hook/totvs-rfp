<?php

namespace App\Exceptions\RDStationMentoria;

class Agents
{
    private $RDStationMentoria;
    private $ref="meuassistente";

    public function __construct($RDStationMentoria)
    {
        $this->RDStationMentoria = $RDStationMentoria;
        $this->RDStationMentoria->setUrl($this->ref);
    }

    public function listAgents($page = 1, $size = 20, $search = null)
    {
        $path = '/agents';
        $queryParams = ['page' => $page, 'size' => $size]; //, 'search' => $search
        if(!empty($search)){
            $queryParams["search"] = $search;//urlencode($search);
        }

        if ($queryParams) {
            $path .= '?' . http_build_query($queryParams);
        }        
        return $this->RDStationMentoria->request('GET', $path);
    }

    public function createAgent($data)
    {
        $path = '/agents';
        return $this->RDStationMentoria->request('POST', $path, $data);
    }

    public function getAgentById($id)
    {
        $path = '/agents/' . urlencode($id);
        return $this->RDStationMentoria->request('GET', $path);
    }

    public function updateAgent($id, $data)
    {
        $path = '/agents/' . urlencode($id);
        return $this->RDStationMentoria->request('PUT', $path, $data);
    }

    public function removeAgent($id)
    {
        $path = '/agents/' . urlencode($id);
        return $this->RDStationMentoria->request('DELETE', $path);
    }

    public function getChannelsByAgentId($id)
    {
        $path = '/agents/' . urlencode($id).'/channels/';
        return $this->RDStationMentoria->request('GET', $path);
    }

    ///////////////////////////////////

    public function getCurrentWorkspace()
    {
        $path = '/workspaces/current';
        return $this->RDStationMentoria->request('GET', $path);
    }
    public function startConversation($workspaceId, $agentId, $title)
    {
        $this->RDStationMentoria->setUrl('chat');

        $path = '/widget/' . urlencode($workspaceId) . '/' . urlencode($agentId);
        $data=["message"=> ["content"=>$title,"role"=>"user", "metadata" =>new \stdClass()] ];
        $request = $this->RDStationMentoria->request('POST', $path, $data);
        $temp = $request;
        if(!empty($request['content'])){
            $request = $this->jsonData($request);
        }elseif(!empty($request[0])){
            foreach ($request as $r => $req) {
                $request[$r] = $this->jsonData($request[$r]);
            }
        }

        $this->RDStationMentoria->setUrl('meuassistente');

        if(!empty($_GET['teste'])){
            //dd('76 startConversation', $path, $data,  $request, $temp);
        }          
        return $request;
        //dd($request, $data, $path, $this->RDStationMentoria);
        /*
        chat.meuassistente.rdstationmentoria.com.br/widget/<id do workspace>/<id do assistente>
        $path = '/public/agents/' . urlencode($workspaceId) . '/' . urlencode($agentId);
        */
    }

    public function jsonData($request=[]){
        if(!empty($request['content'])){
            $content = $request['content'];
            /*
            if(!empty($_GET['teste']) && strpos($content, 'json') !== false){
                $content1 = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
                $content2 = htmlspecialchars_decode($content1, ENT_QUOTES);
                //https://bw8.hook.app.br/nentoriamarajo/api/listaveiculos/MRJ/telefone1?ENTRADA=Estou%20a%20procura%20de%20op%C3%A7oces%20hatch%202020?teste=123
                dd(['content'=>$content,'content1'=>$content1,'content2'=>$content2]);
            }
            */
            if (mb_substr($content, 0, 1) === "{" && json_decode($content, true)) {
                $jsonString = $content;
            }else{
                $startPos = strpos($content, '```json');
                $endPos = strpos($content, '```', $startPos + 1);
                if ($startPos !== false && $endPos !== false) {
                    $jsonString = substr($content, $startPos + 7, $endPos - $startPos - 7);
                }
            }    
            if(!empty($_GET['teste']) && empty($request['jsonData']) && strpos($content, 'json') !== false){
                //dd("agents 107",['content'=>$content,'startPos'=>$startPos,'endPos'=>$endPos,'request'=>$request]);
            }

            if(!empty($jsonString)){

                $request['jsonData'] = json_decode($jsonString, true);
                if (!empty($startPos) && $startPos !== false && !empty($endPos) &&  $endPos !== false) {
                    $request['antesTexto'] = trim( str_replace(["\r", "\n",'""','  '], '', substr($content, 0, $startPos)) );
                    $request['depoisTexto'] = trim( str_replace(["\r", "\n",'""','  '], '', substr($content, $endPos + 3)) );
                }
            }
            if(!empty($_GET['teste']) && empty($request['jsonData']) && strpos($content, 'json') !== false){
                //dd("agents 118",['content'=>$content,'startPos'=>$startPos,'endPos'=>$endPos,'request'=>$request]);
            }
        }
        if(!empty($_GET['teste'])){
            //dd("agents 111",$request);
        }

        return $this->escolherResquest( $request );
    }
    public function escolherResquest($request=[]){
        if(!empty($request) && empty($request['content']) && !empty($request[0]) ){
            $keyValida = "NAO";
            for ($c=count($request); $c > 0 ; $c--) {
            //for ($c=0; $c < count($request); $c++) { 
                if($keyValida == "NAO" && !empty($request[$c]) && !empty($request[$c]['content']) ){
                    $request = $request[$c];
                    $keyValida = "SIM";
                    break;
                }
            }
        }
        return $request;     
    }
    public function askInConversation($workspaceId, $agentId, $conversationId="", $content="")
    {
        $this->RDStationMentoria->setUrl('chat');

        $path = '/widget/' . urlencode($workspaceId) . '/' . urlencode($agentId);
        $data=["message"=> ["content"=>$content,"role"=>"user", "metadata" =>new \stdClass()] ];
        if(!empty($conversationId)){ $data['id']=$conversationId; }

        return json_encode($data);

        $request = $this->RDStationMentoria->request('POST', $path, $data); //json_decode(json_encode(''),true);

       

        if(!empty($_GET['teste'])){
           // dd('157 askInConversation', $path, $data,  $request);
        }        
        /*
        [
        "createdAt" => "2024-04-10T13:51:15.000Z",
        "metadata" => [],
        "role" => "assistant",
        "conversationId" => "convo_01HV43ZPCBQPX3A7ANC9VHC1WD",
        "id" => "msg_01HV44DV368KN26VSKAT7XNJBF",
        "intent" => null,
        "content" => '"""
          Sim, encontramos informações similares na nossa base de conhecimento relacionadas a relatórios para conferência da contabilização. Aqui está um resumo no formato solicitado: 
          
          ```json
          {
            "encontrou_similar": "SIM",
            "aderencia_na_mesma_linha": "Atende",
            "observacao_na_mesma_linha": "O Relatório de Conferência de IRRF do TOTVS RH Linha RM 12.1.2310 compara os valores de IRRF na Folha com os valores devolvidos pelo evento S-5002, oferecendo uma visão clara das diferenças e simplificando a gestão financeira."
          }
          ```
          
          Espero que isso ajude! Precisa de mais alguma informação sobre este tópico ou tem outra questão em mente?
          """',
        "updatedAt" => "2024-04-10T13:51:15.000Z"
        ];
        */
        if(!empty($request['content'])){
            $request = $this->jsonData($request);
        }elseif(!empty($request[0])){
            foreach ($request as $r => $req) {
                $request[$r] = $this->jsonData($request[$r]);
            }
        }
        

        $this->RDStationMentoria->setUrl('meuassistente');
        
      

        return $this->escolherResquest( $request );

        //$path = '/public/agents/' . urlencode($workspaceId) . '/' . urlencode($agentId) . '/' . urlencode($conversationId);
        //return $this->RDStationMentoria->request('POST', $path, $data);
    }

    public function getAgentInWorkspace($workspaceId, $id)
    {
        $path = '/public/agents/' . urlencode($workspaceId) . '/' . urlencode($id);
        return $this->RDStationMentoria->request('GET', $path);
    }
  
}
