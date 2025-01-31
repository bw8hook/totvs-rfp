<?php

namespace App\Exceptions\RDStationMentoria;

use Illuminate\Support\Facades\Http;

use GlobalFuncoes;
use RDStationMKT;
use App\Models\Sistema\APIConfig;

use App\Models\Sistema\Conta;

use App\Exceptions\RDStationMentoria\Agents;
use App\Exceptions\RDStationMentoria\Bases;

use App\Exceptions\RDStationMentoria\Accounts;
use App\Exceptions\RDStationMentoria\User;



class RDStationMentoria
{
    private $apiUrl = 'https://api.meuassistente.rdstationmentoria.com.br/rest';
    private $apiKey = "eyJhbGciOiJSUzUxMiIsInR5cCI6IkpXVCJ9.eyJ0eXBlIjoiYXBpIiwicHJvcGVydGllcyI6eyJrZXlJZCI6ImtleV8wMUpGMEhQWEc1SkRSNTlBUzU2OFgwVjk3WSIsIndvcmtzcGFjZUlkIjoid3BjXzAxSjVCSFdCNTRFSldDRE42QVFZMlg2NUo3IiwidXNlcklkIjoidXNlcl9hcGkifSwiaWF0IjoxNzM0MTExNjIyfQ.cLjsIB85bybra-rQOTAI-GLuIQKeQP95HLdXu-JG1yxMbrdzHwjqLGl8xzo3aVwz94uD3mWaOhajdqync0CCusVM_VF3dEsg2bRd9OM02HMD-rxil360HClB--5zYKOW7NZUPKmj0Q8rl-1v-aE4lFes6U7-_zB1gJWiGTLR9HLuZd3E5EsSqxu_mS49ss5tAFHQYrVotns6Ug5OGmxSgJ-IqlluVMPRbI8dtSb0ZsiYHe_xYtaERhTInevjaqgHbhZwLzyHg50R7MMoJLsGlw8CD3KRfcitxj8NZmilKK4vCkjm4dN5QYTuRxSqpgnGRtCCk-3fR0q8N3GwYF4oWQ";

    public $caminho;
    public $arqJson=['contas'=>[],'conteudo'=>[],'bases'=>[],'meuassistente'=>[]];
    public $retorno=[];

    public function __construct($sigla="",$apiKey="") {
        if(!empty($sigla)){ $this->setConta($sigla);}
        if(!empty($apiKey)){ $this->apiKey = $apiKey; }
        $this->caminho = app_path("Exceptions/RDStationMentoria/");

        foreach ($this->arqJson as $arquivo => $conteudo) { if(file_exists($this->caminho.$arquivo.".json")){
            $this->arqJson[$arquivo] = json_decode(file_get_contents($this->caminho.$arquivo.".json"),true);
            unset($this->arqJson[$arquivo]['openapi']);unset($this->arqJson[$arquivo]['info']);unset($this->arqJson[$arquivo]['components']);
        } }
    }

    public function getBaseJson($arq="meuassistente",$ref="agents",$inicio=0,$limite=4) {
        $retorno = [];
        if(empty($arq)){ $retorno = $this->arqJson;}
        elseif(!empty($this->arqJson[$arq])){ foreach ($this->arqJson[$arq]['paths'] as $name => $dados) {
            if(strpos(strtolower($name),strtolower($ref) ) !== false ) { $retorno[ $name ] = $dados; }
        } }
        $this->setUrl($arq);
        return $retorno;
    }    
    /////////////////////////////////////
    public function setUrl($arq="meuassistente"){
        if($arq=="chat"){ $this->apiUrl = "chat.meuassistente.rdstationmentoria.com.br"; }
        elseif(!empty($arq) && !empty($this->arqJson[$arq]) && !empty($this->arqJson[$arq]["servers"])){
            $this->apiUrl = $this->arqJson[$arq]["servers"][0]['url'];
        }
    }
    public function request($method="GET", $path="", $parameters = []) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . $path);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if(!empty($this->requestTimeout)){
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->requestTimeout);
        }

        // Adicione esta linha para seguir redirecionamentos
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 180); // 30 segundos de tempo de espera

                
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json',
                'Accept: application/json',
            ]);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
        } elseif ($method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }
        $response = curl_exec($ch);
        $temp = $response;
        if(gettype($response)=="string"){
            $response= $this->toJson($response);
        }
        if ($response === false) {
            $this->retorno= 'Erro na chamada da API: ' . curl_error($ch);
            return $response;
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if(
             (is_null($response) || !empty($response['message']) && !empty($response['code']) )
            && !empty($_GET) && !empty($_GET['teste']) 
        ){

            //if($path == '/public/agents'){
            // "https://api.meuassistente.rdstationmentoria.com.br/rest/public/agents/wpc_01H44Y2BQJQWSB7WDMHG7NCMNV/agt_01HTFG3DKGQA46REM5EZRCMZMR"
            // "https://api.meuassistente.rdstationmentoria.com.br/rest
                dd($temp,$method, $this->apiUrl . $path, $httpCode, $response,[
                    'Authorization: Bearer '. $this->apiKey,
                    'Content-Type: application/json',
                    'Accept: application/json',
                ],$parameters);
            //}

        }

        if ($httpCode !== 200) {
            $this->retorno= 'Erro na chamada da API. Código HTTP: ' . $httpCode;
            return $response;
        }

        return $response;
    }
    public function toJson($string) {
        if (empty($string)) {
            return null;
        }

        $parsed = json_decode($string, true);

        if ($parsed === null && json_last_error() === JSON_ERROR_SYNTAX) {
            $string = '[' . str_replace("}\n{", '},{', $string) . ']';
            $parsed = json_decode($string, true);
        }

        return $parsed;
    }
    ///////////////////////////////////////////////////

    ///////////////////////////////////////////////////

    public function classAgents() {
        $this->Agents = new Agents($this);
        return $this->Agents;
    }     
    public function classBases() {
        $this->Bases = new Bases($this);
        return $this->Bases;
    }

    public function classAccounts() {
        $this->Accounts = new Accounts($this);
        return $this->Accounts;
    }
    public function classUser() {
        $this->User = new User($this);
        return $this->User;
    }    
    
}



