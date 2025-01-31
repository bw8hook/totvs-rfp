<?php

namespace App\Exceptions\MagicWriteAPI;

use Illuminate\Support\Facades\Http;

use GlobalFuncoes;
use RDStationMKT;
use App\Models\Sistema\APIConfig;

use App\Models\Sistema\Conta;
use App\Models\Sistema\Lead;

use App\Exceptions\MagicWriteAPI\Agents;
use App\Exceptions\MagicWriteAPI\Ingestions;
use App\Exceptions\MagicWriteAPI\Completions;

// https://magic-write.readme.io/reference/mutationingestionsstart

class MagicWriteAPI
{
    private $apiUrl = 'https://api.magicwrite.ai/';
    //private $apiKey = '123456'; // Substitua pela sua chave de API
    private $apiKey = 'eyJhbGciOiJSUzUxMiIsInR5cCI6IkpXVCJ9.eyJ0eXBlIjoiYXBpIiwicHJvcGVydGllcyI6eyJrZXlJRCI6ImtleV8wMUg0NzJGV1ZDWjZCQTEyRlgySFZYM0VEMiIsImNvbXBhbnlJRCI6ImNvXzAxSDQ0WTJCUUpRV1NCN1dETUhHN05DTU5WIiwidXNlcklEIjoidXNlcl9hcGkiLCJyb2xlIjoiYWRtaW4ifSwiaWF0IjoxNjg4MTU5NjQ2fQ.WnXFYxf-Ot9WA2EY3yO88RUDZw-sWVQH_WtiYC8BVoo-SiwSytfB3AkCElOoe_SfM1q3grzbXCM4PWypTNh5glGkHaGUTKoOiASOTfDnb4W17vShe5qChdvGUAtiAhUgknJfxnW1ABBuJiwMXyQ7xSzzhHvpP8ra6KWgJXYPiMNm8RWNJutpQkwvK5O9HAeVtMqV6Pyud5ht15RpaKHnXELhZR2R40PlxA7g3Slv865SOy-sFgSipePNVVA4lvmSDwg5UACVV_Ee_1EFiXWcgTVziuhVed-y-B2uvUwUQVtAA07PvqlrnWX5goM-A063X1sb2on49_nyaJTjv02lmQ'; // Substitua pela sua chave de API    

    private $apiKey_nova = "eyJhbGciOiJSUzUxMiIsInR5cCI6IkpXVCJ9.eyJ0eXBlIjoiYXBpIiwicHJvcGVydGllcyI6eyJrZXlJZCI6ImtleV8wMUhGU0szTkRWN0JCREFDVEFaWEZHMUo5UCIsIndvcmtzcGFjZUlkIjoid3BjXzAxSDQ0WTJCUUpRV1NCN1dETUhHN05DTU5WIiwidXNlcklkIjoidXNlcl9hcGkifSwiaWF0IjoxNzAwNTkyMjExfQ.oFCUsOE8t-pPyUM0EgWCYS4gY50-3kVjMo6O76WE87rUKS8lG_mRycsDFuspxw1gi55_MWNWEZ65iKWus4YAOuL8dhAvsaLxSd27A8f2UNBwcuouHRus0c9wcwDnGnLlKoOuFp8fVQpXDEfnNlr2WhULqzT92ejrzzqahyCcRPi4v8GujkeG8DZGDFuBZTkI3QddRBm-Slh5wbBnqOkfLwfoJWixxcJjVdcGPq77cClhI_pgCQbMrsm90nUwzIurbYNaq8UqI2nr9mFWNdHuk9yn3ObmeRcI8TdHz5Gk-AmYp-rCAH50ZuJyBWBJf92KUd6JO6rWQTr32miL9f92Dg";
    private $apiKey_antiga = "eyJhbGciOiJSUzUxMiIsInR5cCI6IkpXVCJ9.eyJ0eXBlIjoiYXBpIiwicHJvcGVydGllcyI6eyJrZXlJRCI6ImtleV8wMUg0NzJGV1ZDWjZCQTEyRlgySFZYM0VEMiIsImNvbXBhbnlJRCI6ImNvXzAxSDQ0WTJCUUpRV1NCN1dETUhHN05DTU5WIiwidXNlcklEIjoidXNlcl9hcGkiLCJyb2xlIjoiYWRtaW4ifSwiaWF0IjoxNjg4MTU5NjQ2fQ.WnXFYxf-Ot9WA2EY3yO88RUDZw-sWVQH_WtiYC8BVoo-SiwSytfB3AkCElOoe_SfM1q3grzbXCM4PWypTNh5glGkHaGUTKoOiASOTfDnb4W17vShe5qChdvGUAtiAhUgknJfxnW1ABBuJiwMXyQ7xSzzhHvpP8ra6KWgJXYPiMNm8RWNJutpQkwvK5O9HAeVtMqV6Pyud5ht15RpaKHnXELhZR2R40PlxA7g3Slv865SOy-sFgSipePNVVA4lvmSDwg5UACVV_Ee_1EFiXWcgTVziuhVed-y-B2uvUwUQVtAA07PvqlrnWX5goM-A063X1sb2on49_nyaJTjv02lmQ";
    
    
    public $caminho;
    public $retorno=[];

    public function __construct($sigla="",$versao="1",$teste=false) {
        if(!empty($sigla)){ $this->setConta(null,$sigla);}
        $this->caminho = app_path("Exceptions/MagicWriteAPI/");
        if($versao!="1"){
            $this->apiUrl = "https://api.meuassistente.rdstationmentoria.com.br/";
            $this->apiKey = "eyJhbGciOiJSUzUxMiIsInR5cCI6IkpXVCJ9.eyJ0eXBlIjoiYXBpIiwicHJvcGVydGllcyI6eyJrZXlJZCI6ImtleV8wMUhGU0szTkRWN0JCREFDVEFaWEZHMUo5UCIsIndvcmtzcGFjZUlkIjoid3BjXzAxSDQ0WTJCUUpRV1NCN1dETUhHN05DTU5WIiwidXNlcklkIjoidXNlcl9hcGkifSwiaWF0IjoxNzAwNTkyMjExfQ.oFCUsOE8t-pPyUM0EgWCYS4gY50-3kVjMo6O76WE87rUKS8lG_mRycsDFuspxw1gi55_MWNWEZ65iKWus4YAOuL8dhAvsaLxSd27A8f2UNBwcuouHRus0c9wcwDnGnLlKoOuFp8fVQpXDEfnNlr2WhULqzT92ejrzzqahyCcRPi4v8GujkeG8DZGDFuBZTkI3QddRBm-Slh5wbBnqOkfLwfoJWixxcJjVdcGPq77cClhI_pgCQbMrsm90nUwzIurbYNaq8UqI2nr9mFWNdHuk9yn3ObmeRcI8TdHz5Gk-AmYp-rCAH50ZuJyBWBJf92KUd6JO6rWQTr32miL9f92Dg";
        }
        if($teste==false){
            //$this->apiKey = $this->apiKey_antiga;
        }        
    }
    public function setConta($conta_id=null,$sigla=null){
        if(!empty($conta_id)){ $Conta = Conta::find($conta_id); }else{
        if(!empty($sigla)){ $Conta = Conta::where('sigla',$sigla)->first(); } }
        if(!empty($Conta)){ if($Conta->status=='Ativo') { $this->Conta = $Conta; } }
        return $this->Conta;
    }
    /////////////////////////////////////
    public function request($method="GET", $path="", $parameters = []) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if(!empty($this->requestTimeout)){
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->requestTimeout);
        }
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
        if(gettype($response)=="string"){
            $response= $this->toJson($response);
        }
        if ($response === false) {
            $this->retorno= 'Erro na chamada da API: ' . curl_error($ch);
            return $response;
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

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
    public function getSession() {
        return $this->request('GET', 'rest/session');
    }
    ///////////////////////////////////////////////////
    public function getTreinamento($Treinamento = "", $atualizar = "NAO") {
        $TREINO = []; $listIngestions = [];
        $arquivo = $this->caminho . "Ingestions.json";
        
        if (!empty($Treinamento)) {
            if (file_exists($arquivo)) { $listIngestions = json_decode(file_get_contents($arquivo), true); }

            if (empty($listIngestions) || empty($listIngestions['data'])) { $atualizar = "SIM";}
            else {
                $filteredIngestions = array_filter($listIngestions['data'], function ($ingestion) use ($Treinamento) {
                    return $ingestion['search'] === ':' . $Treinamento;
                });
                if (empty($filteredIngestions)) { $atualizar = "SIM";}
            }

            if ($atualizar == "SIM") {
                if (empty($this->Ingestions)) { $this->MagicWriteIngestions(); }
                $listIngestions = $this->Ingestions->listIngestions($Treinamento);
                if(!empty($listIngestions['data'])) { file_put_contents($arquivo, json_encode($listIngestions)); }
            }
            
            if(!empty($listIngestions['data'])) {
                $filteredIngestions = array_filter($listIngestions['data'], function ($ingestion) use ($Treinamento) {
                    return $ingestion['search'] === ':' . $Treinamento;
                });
                if (!empty($filteredIngestions)) { $TREINO = reset($filteredIngestions); }
            }
        }

        return $TREINO;
    }
    
    ///////////////////////////////////////////////////
    public function MagicWriteCompletions() {
        $this->Completions = new Completions($this);
        return $this->Completions;
    }
    public function MagicWriteAgents() {
        $this->Agents = new Agents($this);
        return $this->Agents;
    }
    public function MagicWriteIngestions() {
        $this->Ingestions = new Ingestions($this);
        return $this->Ingestions;
    }        
   
    
}



