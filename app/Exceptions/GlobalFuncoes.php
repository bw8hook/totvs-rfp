<?php
namespace App\Exceptions;

use \App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use App\Models\Sistema\Conta;
use App\Models\Sistema\Lead;
use App\Models\Sistema\LogURL;
use App\Models\Sistema\Contador;

use RDStationMKT;

class GlobalFuncoes
{
    public static function host() {
        return 'https://'.$_SERVER[ 'HTTP_HOST' ].'/';
    }
    public static function hostCRM() {
        return 'crm.rdstation.com'; //'plugcrm.net'; //'crm.rdstation.com';
    }    
    public static function apiQRCode($chave=null) { if($chave!=null) {
        return GlobalFuncoes::host()."qr/api/".$chave;
    } }  
    
    public static function apiRDStation($sigla=null) { if($sigla!=null) {
        return GlobalFuncoes::host()."rdstation/api/".$sigla;
    } }
    public static function arquivosRetorno($sigla=null) { if($sigla!=null) {
        $caminho = public_path() . '/retorno/';
        if (!file_exists($caminho."index.php")) { file_put_contents($caminho."index.php", ""); }
        return $caminho.$sigla.'/';
    } }
    public static function arquivosVideo($video=null) { if($video!=null) {
        $caminho = public_path() . '/video/';
        if (!file_exists($caminho."index.php")) { file_put_contents($caminho."index.php", ""); }
        return $caminho.$video.'.mp4';
    } }
    public static function arquivosQR($sigla=null) { if($sigla!=null) {
        $caminho = public_path() .'/qrcode/'. $sigla.'/';
        if (!file_exists($caminho)){ mkdir($caminho, 0755, true); }
        if (!file_exists($caminho."index.php")) { file_put_contents($caminho."index.php", ""); }
        return $caminho;
    } }
    public static function criarPasta($pasta="",$index=true) {
        if (!file_exists($pasta)){ mkdir($pasta, 0755, true); }
        if (!file_exists($pasta.'/'."index.php")&&$index==true) { file_put_contents($pasta.'/'."index.php", ""); }
        return $pasta;
    }
    public static function limparPasta($diretorio="") {
        if(!empty($diretorio)){ if(is_dir($diretorio)){
            $itens = glob($diretorio . '/*');
            if (empty($itens)) { rmdir($diretorio); return; }
            foreach ($itens as $item) { if (is_dir($item)) { GlobalFuncoes::limparPasta($item); } }
            $itens = glob($diretorio . '/*');
            if (empty($itens)) {rmdir($diretorio);}
        } }
    }
    public static function detalhePasta($directory = "", $detalhado=false, $limiteTamanhoMB = 100, $limiteArquivos = 100000) {
        $detalhePasta = ['pasta'=>$directory,'pastas'=>[],'tamanhoTotal'=>0];
        if (!empty($directory) && is_dir($directory)) {

            $detalhePasta['time'] = filemtime($directory);
            $detalhePasta['data'] = date("Y-m-d H:i", $detalhePasta['time']);

            $folders = glob($directory . '/*', GLOB_ONLYDIR);
            foreach ($folders as $folder) {
                $pasta = [
                    'pasta' => $folder,
                    'data' => date("Y-m-d H:i", filemtime($folder)),
                    'time' => filemtime($folder),
                ];

                if ($detalhado==true) {
                    $pasta['tamanho'] = self::tamanhoPasta($folder, $limiteTamanhoMB, $limiteArquivos);
                    $pasta['bytes'] = self::formatBytes($pasta['tamanho']);
                    $pasta = array_merge($pasta, self::arquivoRecenteAntigo($folder));

                    $detalhePasta['tamanhoTotal'] += $pasta['tamanho'];
                }

                $detalhePasta['pastas'][] = $pasta;
            }
            
            if ($detalhado==true) {

                if(empty($folders)){
                    $detalhePasta['tamanhoTotal'] += self::tamanhoPasta($directory, $limiteTamanhoMB, $limiteArquivos);
                }

                if($detalhePasta['tamanhoTotal']>61850){
                    //dd($detalhePasta,$folders);
                }

                //$detalhePasta['tamanhoTotal'] += filesize($directory) ?? 0;
                $detalhePasta['tamanhoTotalBytes'] = self::formatBytes($detalhePasta['tamanhoTotal']);
                $detalhePasta = array_merge($detalhePasta, self::arquivoRecenteAntigo($directory));                
            }

        }
    
        return $detalhePasta;
    }
    
    public static function getConta($sigla="",$nome="") {
        if(!empty($sigla)){ return  Conta::where('sigla',$sigla)->first(); }
        if(!empty($nome)){ return  Conta::where('nome',$nome)->first(); }
        return null;
    }

    public static function tamanhoPasta($diretorio = "", $limiteTamanhoMB = 100, $limiteArquivos = 10000) {
        $tamanho = 0;
        $arquivosContados = 0;
    
        if (!empty($diretorio) && is_dir($diretorio)) {

            $arquivos = File::allFiles($diretorio);
            foreach ($arquivos as $arquivo) {
                $tamanho += $arquivo->getSize();
                $arquivosContados++;
                if (($limiteTamanhoMB !== null && $tamanho > $limiteTamanhoMB * 1024 * 1024) || ($limiteArquivos !== null && $arquivosContados > $limiteArquivos)) {
                    break;
                }
            }
    
            $subpastas = File::directories($diretorio);
            foreach ($subpastas as $subpasta) {
                if (($limiteTamanhoMB !== null && $tamanho > $limiteTamanhoMB * 1024 * 1024) || ($limiteArquivos !== null && $arquivosContados > $limiteArquivos)) {
                    break;
                }
                $tamanho += self::tamanhoPasta($subpasta, $limiteTamanhoMB, $limiteArquivos);
            }
        }
    
        return $tamanho;
    }
    
    public static function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    } 
    public static function arquivoRecenteAntigo($directory) {
        $files = glob($directory . '/*');
        $mostRecent = null;
        $mostRecentTime = 0;
        $oldest = null;
        $oldestTime = PHP_INT_MAX;
        
        foreach ($files as $file) {
            $fileTime = filemtime($file);
            
            if ($fileTime > $mostRecentTime) {
                $mostRecent = $file;
                $mostRecentTime = $fileTime;
            }
            
            if ($fileTime < $oldestTime) {
                $oldest = $file;
                $oldestTime = $fileTime;
            }
        }
        
        return [
            'recenteFile' => $mostRecent,
            'recenteTime' => $mostRecentTime,
            'recenteData' => date("Y-m-d H:i",$mostRecentTime),
            'antigoFile' => $oldest,
            'antigoTime' => $oldestTime,
            'antigoData' => date("Y-m-d H:i",$oldestTime),
        ];
    }  
    public static function copiaImgURL($imageUrl=null,$destino="") {
        if($imageUrl!=null) {
            $ch = curl_init();
            curl_setopt ($ch, CURLOPT_URL, $imageUrl);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($ch);
            curl_close($ch);
            $file = fopen($destino, 'w+');
            fputs($file, $data);
            fclose($file);
        }
        return (file_exists($destino));
    }
    ///////////////////////////////////

    public static function requestLambda($endpoint, $data = [], $method = 'GET', $header = [])
    {
        $lambdaEndpoint = "https://otugl7mdygouv5sfahfgsw4oya0ipbud.lambda-url.us-east-1.on.aws/";
        $dataToSend = [
            'endpoint' => $endpoint,
            'data' => $data,
            'method' => $method
        ];     
           
        if(!empty($header)){
            $dataToSend['header'] = $header;
        }

        if(!empty($_GET['teste'])){
            // dd( $dataToSend, json_encode($dataToSend) );
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $lambdaEndpoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataToSend));
        curl_setopt($ch, CURLOPT_HTTPHEADER,  ['Content-Type: application/json', 'Accept: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        // Defina o tempo limite da conexão em segundos (ajuste conforme necessário)
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        set_time_limit(20);  // Define o limite de tempo de execução para 60 segundos
        // Defina o tempo limite total da solicitação em segundos (ajuste conforme necessário)
        curl_setopt($ch, CURLOPT_TIMEOUT, 20); // Define o tempo limite da solicitação para 30 segundos

        $response = curl_exec($ch);
    
        if (curl_errno($ch) ) {
            dd('Erro cURL: ' . curl_error($ch));
        }
    
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    
        $responseData = json_decode($response, true);
    
        if ($httpCode >= 400) {
            dd('Erro na solicitação: ' . $httpCode);
        }
    
        return $responseData;
    }

    ///////////////////////////////////

    public static function debugcodigo($debugcodigo='',$dados=[]) {
        
        if (defined('debugcodigo')&&!empty($debugcodigo)) { if($debugcodigo==debugcodigo){
            if(empty($dados)){ return true; }else{ dd($debugcodigo,$dados); }
        } }
        
        if (defined('debugLog')){ LogURL::novoLog($debugcodigo,null,null,$msg='',$dados); }
        
        return false;
    }
    public static function contadorFILA_inicio($Fila=[]) {
        $contadorURL=[];
        $caminho = storage_path('logs/contadorURL/'); GlobalFuncoes::criarPasta($caminho);

        $Fila=json_decode(json_encode($Fila),true);
        if(!empty($Fila['id'])){
            $FILA_ID = $Fila['id'];
            $ACAO=""; if(!empty($Fila['get'])){ $ACAO = array_key_first(json_decode($Fila['get'],true)); }
            $Conta = Conta::find($Fila['conta_id']);
            if(!empty($Conta->sigla)){
                $SIGLA=$Conta->sigla;
                $caminho = storage_path('logs/contadorURL/'.$SIGLA.'/'); GlobalFuncoes::criarPasta($caminho); //.$ACAO.'/'
                $arquivo=date("Ymd_H_i_s").".json"; //$arquivo=$FILA_ID.".json";
                if (!defined('contadorArquivo')){ define('contadorArquivo',$caminho.$arquivo); }
                if(!file_exists(contadorArquivo)){
                    $contadorURL=[
                        'contadorInicio'=>date('Y-m-d H:i:s'),
                        'contador'=>0,'intevalo'=>0,
                        'totalFila'=>1,'limite'=>1,
                        "requisicao"=>[]
                    ];
                    file_put_contents(contadorArquivo, json_encode($contadorURL));
                }
                if(file_exists(contadorArquivo)){
                    $contadorURL=json_decode(file_get_contents(contadorArquivo),true);
                    $contadorURL[$FILA_ID]=['SIGLA'=>$SIGLA,'FILA_ID'=>$FILA_ID,'ACAO'=>$ACAO,'conta_id'=>$Fila['conta_id']];
                    $contadorURL['totalFila']++;
                    file_put_contents(contadorArquivo, json_encode($contadorURL));
                }
            }
        }
        return $contadorURL;
    }
   
    public static function contadorURL($contador=0,$FUNCAO="",$URL="") {
        $contadorURL=[];
        if (defined('contadorArquivo')){ if(file_exists(contadorArquivo)){
            $contadorURL=json_decode(file_get_contents(contadorArquivo),true);
            //////////////////////////////////////////////
            $contadorURL['contador'] = $contadorURL['contador']+$contador;
            $contadorURL['contadorFinal'] = date('Y-m-d H:i:s');
            
            if(!empty($FUNCAO)&&!empty($URL)){
                $contadorURL["requisicao"][]=['FUNCAO'=>$FUNCAO,'URL'=>$URL,"TEMPO"=> (strtotime($contadorURL['contadorFinal'])-strtotime($contadorURL['contadorInicio'])) ];
            }       
            
            $contadorURL['segundos'] = (strtotime($contadorURL['contadorFinal'])-strtotime($contadorURL['contadorInicio']))/ $contadorURL['limite'];
            $contadorURL['minutos']  = round(($contadorURL['segundos']*$contadorURL['totalFila'])/60,2);
            $contadorURL['horas']  = round($contadorURL['minutos']/60,2);
            
            //////////////////////////////////////////////
            file_put_contents(contadorArquivo, json_encode($contadorURL));
        } }
        return $contadorURL;
    }
    ///////////////////////////////////    
    public static function enviaPOST($url,$post,$Conta=null,$metodo="",$tentativa=0) { //,$token_usuario=""
            GlobalFuncoes::contadorURL(1,"enviaPOST",$url);
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $headers = array('Content-Type: application/json','Accept: application/json');
            if(!empty($Conta)){ if(!empty($Conta->config->APP->access_token)){
                $headers[]='Authorization: Bearer ' . $Conta->config->APP->access_token;
            } }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post) ); //http_build_query($post)
            if(!empty($metodo)){
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  $metodo);
            }
            //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1200);
            
            $retorno = curl_exec($ch);
            if(!curl_errno($ch)){
                $info = curl_getinfo($ch);
                if($info['total_time']>5){
                    GlobalFuncoes::criarPasta(storage_path('logs/enviaPOST/'));
                    $caminho = storage_path('logs/enviaPOST/');
                    file_put_contents($caminho.date("Ymd_His").".json",json_encode(["info"=>$info,"post"=>$post]));
                    //echo 'Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url'];
                }
            }
            curl_close($ch);
            
            if(gettype($retorno)=="string"){
                
                $retorno=json_decode($retorno,true);
                
                if(!empty($retorno['erros'])){ LogURL::novoLog('enviaPOST erros',null,null,$msg='',$retorno['erros']);  }
                
                if(!empty($retorno['message'])){ if($retorno['message']=="API rate limit exceeded"){
                    $caminho = storage_path('logs/APIlimit/');GlobalFuncoes::criarPasta($caminho);
                    file_put_contents($caminho."espera.json","SIM");
                    $arquivo = date("Ymd_His").".json";
                    if(!empty($post['name'])){$arquivo = str_replace(' ','_',GlobalFuncoes::tirarAcentos(strtolower($post['name']))).".json"; }
                    file_put_contents($caminho.$arquivo,json_encode(["tentativa"=>$tentativa,"Conta"=>$Conta,"retorno"=>$retorno,"post"=>$post]));     
                    sleep(10);
                    /*
                    $tentativa++; sleep((20*$tentativa)); 
                    if($tentativa<3){
                        $caminho = storage_path('logs/APIlimit/');GlobalFuncoes::criarPasta($caminho);
                        $arquivo = date("Ymd_His").".json";
                        if(!empty($post['name'])){$arquivo = str_replace(' ','_',GlobalFuncoes::tirarAcentos(strtolower($post['name']))).".json"; }
                        file_put_contents($caminho.$arquivo,json_encode(["tentativa"=>$tentativa,"Conta"=>$Conta,"retorno"=>$retorno,"post"=>$post]));
                        $retorno = GlobalFuncoes::enviaPOST($url,$post,$Conta,$metodo,$tentativa);
                    }
                    */
                } }

                if(!empty($Conta) && !empty($Conta->config->APP->access_token) && gettype($retorno)=="array" && strpos(json_encode($retorno), 'invalid_token') !== false && strpos($url, 'api.rd.services') !== false && class_exists('RDStationMKT') ){
                    $refreshToken = RDStationMKT::refreshToken(['Conta'=>$Conta]);
                    //if(!empty($_GET) && !empty($_GET['teste']) && $_GET['teste'] == 123){ dd($refreshToken, "enviaGET",json_encode($retorno) , $url); }
                }                
                
            }
            
            
        return $retorno;
    }
    public static function enviaPOST_teste($url,$post,$Conta=null,$metodo="",$token_usuario="") {
            $ch = curl_init($url); 
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
             //,'Content-Length: ' . strlen($post)////'Content-Type: application/x-www-form-urlencoded',//'Content-Type: multipart/form-data',
             $headers=array();
             $headers = array('Content-Type: application/json','Accept: application/json','x-access-token', $token_usuario);//'x-access-token', 'YOUR-TOKEN-HERE');
            //curl_setopt($ch, CURLOPT_POST, true); 'query' => ['token' => $this->token],
            if(!empty($Conta)){ if(!empty($Conta->config->APP->access_token)){
                $headers[]='Authorization: Bearer ' . $Conta->config->APP->access_token;
                //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Bearer ' . $Conta->config->APP->access_token));
            } }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post ); //http_build_query($post)
            curl_setopt($ch, CURLOPT_POST, true);
            if(!empty($metodo)){ //'PUT' //'DELETE'
                //if ($metodo == 'PUT') { curl_setopt($ch, CURLOPT_PUT, true); }
                //else if ($metodo == 'POST') { curl_setopt($ch, CURLOPT_POST, true); }
                //else { curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  $metodo); }
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  $metodo);
            }

            $retorno = curl_exec($ch);
            //dd($retorno);
            curl_close($ch);
            if(gettype($retorno)=="string"){ $retorno=json_decode($retorno,true); }
        return $retorno;
    }
    public static function enviaGET($url,$Conta=null) {

            GlobalFuncoes::contadorURL(1,"enviaGET",$url);
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $headers = array('Content-Type: application/json');
            if(!empty($Conta)){ if(!empty($Conta->config->APP->access_token)){
                $headers[]='Authorization: Bearer ' . $Conta->config->APP->access_token;
                //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Bearer ' . $Conta->config->APP->access_token));
            } }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
            $retorno = curl_exec($ch);
            curl_close($ch);
            if(gettype($retorno)=="string"){$retorno=json_decode($retorno,true); }

            if(!empty($Conta) && !empty($Conta->config->APP->access_token) && gettype($retorno)=="array" && strpos(json_encode($retorno), 'invalid_token') !== false && strpos($url, 'api.rd.services') !== false && class_exists('RDStationMKT') ){
                $refreshToken = RDStationMKT::refreshToken(['Conta'=>$Conta]);
                //if(!empty($_GET) && !empty($_GET['teste']) && $_GET['teste'] == 123){ dd($refreshToken, "enviaGET",json_encode($retorno) , $url); }
            }

            return $retorno;

    }

    public static function enviaDELETE($url, $Conta = null) {
        GlobalFuncoes::contadorURL(1, "enviaDELETE", $url);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $headers = array('Accept: application/json');
        if (!empty($Conta) && !empty($Conta->config->APP->access_token)) {
            $headers[] = 'Authorization: Bearer ' . $Conta->config->APP->access_token;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $retorno = curl_exec($ch);
        curl_close($ch);
        
        if (gettype($retorno) == "string") {
            $retorno = json_decode($retorno, true);
        }
    
        if (!empty($Conta) && !empty($Conta->config->APP->access_token) && gettype($retorno) == "array" && strpos(json_encode($retorno), 'invalid_token') !== false && strpos($url, 'api.rd.services') !== false && class_exists('RDStationMKT')) {
            $refreshToken = RDStationMKT::refreshToken(['Conta' => $Conta]);
        }
    
        return $retorno;
    }
    
    
    ///////////////////////////////////
    ///////////////////////////////////
    public static function acaoPadrao() {
        header('Location: https://hook.app.br/');
        exit;
    }
    public static function codigo($Conta=null,$id=null) { if(!empty($Conta)&&!empty($id)){
        return $Conta->sigla.$id;
    } }
    public static function chave($codigo=null) { if(!empty($codigo)){
        return md5( $codigo );
    } }
    public static function categorias() {
        return array('EMBALAGEM');
    }
    public static function QRlib($Chave=null) {
        $QRlib = new QRlib($Chave);
        return $QRlib->saida();
    }
    public static function dadosQRcode($Chave=null) {
        $QRlib = new QRlib($Chave);
        return $QRlib->dadosQRcode();
    }
    ///////////////////////////////////
    public static function RedirecionarURL($URL=null) { if(!empty($URL)){
        //if(GlobalFuncoes::checkURL($URL)) {
        //dd( 'Location: '.$URL );
        header('Location: '.$URL);
        exit;
        //}else{ GlobalFuncoes::acaoPadrao(); } 
        }else{ GlobalFuncoes::acaoPadrao(); }
    }
    public static function executaAcao($Acao=null) { if(!empty($Acao)){ if(!empty($Acao->operacao)){
        //dd( 'executaAcao: '.$Acao->operacao." == ".$Acao->valor );
        GlobalFuncoes::{$Acao->operacao}($Acao->valor);
    }else{ GlobalFuncoes::acaoPadrao(); } }else{ GlobalFuncoes::acaoPadrao(); } }
    ///////////////////////////////////

    public static function menuSistema($menu) {
        $GlobalFuncoes = new GlobalFuncoes();
        $labels=array();
        if(!empty($menu['route'])){
            if($menu['route']=='cliente.cadastro.index') {
                $Clientes = Cliente::where('status','NOVO');
                if(Auth::user()->tipo!="Atendimento") { $qtd = $Clientes->count(); }
                else{ $qtd = $Clientes->where('user_id',Auth::user()->id)->count(); }
                $labels = array( array('label'=> $qtd,'color'=>'success') );
            }
            if($menu['route']=='cliente.atendimento.contato.index') {
                $Clientes = Cliente::where('status','NOVO');
                if(Auth::user()->tipo!="Atendimento") { $qtd = $Clientes->count(); }
                else{ $qtd = $Clientes->where('user_id',Auth::user()->id)->count(); }
                $labels = array( array('label'=> $qtd,'color'=>'info') );
            }
            if($menu['route']=='cliente.atendimento.revisa.index') {
                $Clientes = Cliente::where('status','REVISA');
                if(Auth::user()->tipo!="Atendimento") { $qtd = $Clientes->count(); }
                else{ $qtd = $Clientes->where('user_id',Auth::user()->id)->count(); }
                $labels = array( array('label'=> $qtd,'color'=>'danger') );
            }
            if($menu['route']=='cliente.atendimento.retorno.index') {
                $Clientes = Cliente::where('status','RETORNO');
                if(Auth::user()->tipo!="Atendimento") { $qtd = $Clientes->count(); }
                else{ $qtd = $Clientes->where('user_id',Auth::user()->id)->count(); }
                $labels = array( array('label'=> $qtd,'color'=>'warning') );
            }
            if($menu['route']=='cliente.atendimento.rejeitado.index') {
                $Clientes = Cliente::where('status','REJEITADO');
                if(Auth::user()->tipo!="Atendimento") { $qtd = $Clientes->count(); }
                else{ $qtd = $Clientes->where('user_id',Auth::user()->id)->count(); }
                $labels = array( array('label'=> $qtd,'color'=>'dark') );
            }
            if($menu['route']=='cliente.aplicativo.index') {

                $Clientes = Cliente::where('status','APLICATIVO');
                if(Auth::user()->tipo!="Atendimento") { $qtd = $Clientes->count(); }
                else{ $qtd = $Clientes->where('user_id',Auth::user()->id)->count(); }

                $Aplicativos = clone $Clientes->with('Aplicativo')->whereHas('Aplicativo',
                    function($query) { $query->where('status', 'ENVIADO'); }
                );
                $aplicativosRejeitadas = clone $Clientes->with('Aplicativo')->whereHas('Aplicativo',
                    function($query) { $query->where('status', 'REJEITADO'); }
                );

                if(Auth::user()->tipo!="Atendimento") { $qtd2 = $Aplicativos->count();$qtd3 = $aplicativosRejeitadas->count(); }
                else{ $qtd2 = $Aplicativos->where('user_id',Auth::user()->id)->count();$qtd3 = $aplicativosRejeitadas->where('user_id',Auth::user()->id)->count(); }
                $labels = array( array('label'=> $qtd,'color'=>'primary'),array('label'=> $qtd2,'color'=>'dark'),array('label'=> $qtd3,'color'=>'danger') );
            }
            if($menu['route']=='cliente.proposta.index') {

                $Clientes = Cliente::where('status','PROPOSTA');
                if(Auth::user()->tipo!="Atendimento") { $qtd = $Clientes->count(); }
                else{ $qtd = $Clientes->where('user_id',Auth::user()->id)->count(); }

                $Propostas = clone $Clientes;
                $Propostas = $Propostas->with('Proposta')->whereHas('Proposta',
                    function($query) { $query->where('status', 'ENVIADA'); }
                );
                $propostasRejeitadas = clone $Clientes;
                $propostasRejeitadas = $propostasRejeitadas->with('Proposta')->whereHas('Proposta',
                    function($query) { $query->where('status', 'REJEITADA'); }
                );
                if(Auth::user()->id==1) {
                    //dd($propostasRejeitadas->count(),$propostasRejeitadas);
                }
                if(Auth::user()->tipo!="Atendimento") { $qtd2 = $Propostas->count();$qtd3 = $propostasRejeitadas->count();  }
                else{ $qtd2 = $Propostas->where('user_id',Auth::user()->id)->count();$qtd3 = $propostasRejeitadas->where('user_id',Auth::user()->id)->count(); }
                $labels = array( array('label'=> $qtd,'color'=>'primary'),array('label'=> $qtd2,'color'=>'dark'),array('label'=> $qtd3,'color'=>'danger') );
            }

            if($menu['route']=='cliente.aprovado.index') {
                $Clientes = Cliente::where('status','APROVADO');
                if(Auth::user()->tipo!="Atendimento") { $qtd = $Clientes->count(); }
                else{ $qtd = $Clientes->where('user_id',Auth::user()->id)->count(); }
                $labels = array( array('label'=> $qtd,'color'=>'warning') );
            }
            //////////////////////////////////////////////////////////////////////
            if($menu['route']=='encaminhado.contato.index') {
                $labels = array( array('label'=> Contato::count(),'color'=>'dark') );
            }
            if($menu['route']=='encaminhado.formulario.index') {
                $labels = array( array('label'=> Formulario::count(),'color'=>'dark') );
            }
            //////////////////////////////////////////////////////////////////////

        }

        if(!empty($menu['submenu'])){
            foreach ($menu['submenu'] as $key => $submenu) {
                $menu['submenu'][$key] = $GlobalFuncoes->menuSistema($menu['submenu'][$key]);
            }
        }
        $menu['labels']=$labels;

        return $menu;
    }
    ///////////////////////////////////
    public static function logAPI($className="",$sigla="",$event="") {
        $pathCSV = storage_path().'/csv/outros/'; GlobalFuncoes::criarPasta($pathCSV);
        $hoje=date('Y-m-d');$ontem = date('Y-m-d',strtotime($hoje . "-1 day"));
        $arquivo = $hoje.'.csv';
        if(!file_exists($pathCSV.$arquivo)){ file_put_contents($pathCSV.$arquivo,json_encode([])); }
        $conteudo = json_decode( file_get_contents($pathCSV.$arquivo), true);
        if(empty($conteudo[$className])){ $conteudo[$className]=0; }
        $conteudo[$className]++;
        file_put_contents($pathCSV.$arquivo,json_encode($conteudo));
    }
    public static function setLogContador($className = "", $sigla = "", $event = "", $dados = [], $lead = false, $limite = 1000) {

        $Contador = Contador::setContador($className, $sigla, $event);

        $saveDados = function ($path) use ($dados, $limite) {
            $path = $path . 'Log/';
            self::criarPasta($path);
            $files = glob($path . '*.json');
            if (!empty($dados)) {
                usort($files, function ($a, $b) {
                    return filemtime($b) - filemtime($a);
                });
                if (count($files) > $limite) {
                    $filesToRemove = array_slice($files, $limite);
                    foreach ($filesToRemove as $file) {
                        if (file_exists($file)) {
                            try {unlink($file);}
                            catch (Exception $e) {
                                // $e->getMessage();
                            }
                        }
                    }
                }
                file_put_contents($path . date('Y_m_d_H_i_s') . ".json", json_encode($dados));
            }
            return count(glob($path . '*.json'));
        };

        $pathDir = storage_path('HookContador/');
        if (!empty($className)) {
            $classPath = $pathDir . str_replace(["Class", "class", " "], "_", $className) . '/';
            if (empty($sigla)) {
                $saveDados($classPath);
            } else {
                $siglaPath = $classPath . str_replace(" ", "_", $sigla) . '/';
                if (empty($event)) {
                    $saveDados($siglaPath);
                } else {
                    $eventPath = $siglaPath . str_replace(" ", "_", $event) . '/';
                    $saveDados($eventPath);
                }
            }
        }

         
        return $Contador;

    }    
    /*
    public static function setLogContador($className = "", $sigla = "", $event = "", $dados = [], $lead = false, $limite = 1000) {
        if (!empty($className) && $className != "TESTE") { 
            //GlobalFuncoes::logAPI($className);  
        }
    
        $pathDir = storage_path('HookContador/'); 
        GlobalFuncoes::criarPasta($pathDir);
        $className = str_replace(" ", "_", $className);
        $sigla = str_replace(" ", "_", $sigla);
        $event = str_replace(" ", "_", $event);
    
        $updateContador = function($path, $name) use ($lead, $limite) {
            GlobalFuncoes::criarPasta($path);
    
            // Contador geral
            $fileContador = $path . $name . "_contador.json";
            $contentContador = ["total" => 0, "lead" => 0, "limite" => $limite];
            if (file_exists($fileContador)) { 
                $contentContador = json_decode(file_get_contents($fileContador), true); 
            }
            $contentContador["total"]++; 
            $contentContador["limite"] = $limite;
            if ($lead == true) { 
                $contentContador["lead"]++; 
            }
            file_put_contents($fileContador, json_encode($contentContador));
    
            // Contador por ano, mês e dia
            $ano = date('Y'); 
            $mes = date('Y-m'); 
            $dia = date('Y-m-d');
            $fileAno = $path . $name . "_".$ano.".json";
            $contentAno = [$ano => [$mes => [$dia => 0]]];
            if (file_exists($fileAno)) { 
                $contentAno = json_decode(file_get_contents($fileAno), true); 
            }
            if (empty($contentAno[$ano])) { 
                $contentAno[$ano] = []; 
            }
            if (empty($contentAno[$ano][$mes])) { 
                $contentAno[$ano][$mes] = []; 
            }
            if (empty($contentAno[$ano][$mes][$dia])) { 
                $contentAno[$ano][$mes][$dia] = 0; 
            }
            $contentAno[$ano][$mes][$dia]++;
            file_put_contents($fileAno, json_encode($contentAno));
            
            $contentContador[ 'contador' ] = [ $ano => $contentAno[$ano] ];

            return $contentContador;
        };
    
        $saveDados = function($path) use ($dados, $limite) {
            $path = $path . 'Log/'; 
            GlobalFuncoes::criarPasta($path);
            $files = glob($path . '*.json');
            if (!empty($dados)) {
                usort($files, function($a, $b) { return filemtime($b) - filemtime($a); });
                if (count($files) > $limite) {
                    $filesToRemove = array_slice($files, $limite);
                    foreach ($filesToRemove as $file) { unlink($file); }
                }
                file_put_contents($path . date('Y_m_d_H_i_s') . ".json", json_encode($dados));
            }
            return count(glob($path . '*.json'));
        };
    
        if (!empty($className)) {
            $classPath = $pathDir . $className . '/';
            $conteudoClass = $updateContador($classPath, $className);    
            if (empty($sigla)) {
                $conteudoClass['logs'] = $saveDados($classPath);
                return [$className => $conteudoClass];
            } else {
                $siglaPath = $classPath . $sigla . '/';
                $conteudoSigla = $updateContador($siglaPath, $sigla);
    
                if (empty($event)) {
                    $conteudoSigla['logs'] = $saveDados($siglaPath);
                    return [$sigla => [$className => $conteudoSigla]];
                } else {
                    $eventPath = $siglaPath . $event . '/';
                    $conteudoEvent = $updateContador($eventPath, $event);
                    $conteudoEvent['logs'] = $saveDados($eventPath);
                    return [$sigla => [$className => [$event => $conteudoEvent]]];
                }
            }
        }
    
        return [];
    }
    
    public static function getLogContador($className = "", $sigla = "", $event = "", $dataInicial = "", $dataFinal = "") {
        $pathDir = storage_path('HookContador/');
        $className = str_replace(" ", "_", $className);
        $sigla = str_replace(" ", "_", $sigla);
        $event = str_replace(" ", "_", $event);
        if(empty($dataInicial)){ $dataInicial = "2024-06-22"; }
        if(empty($dataFinal)){ $dataFinal = date("Y-m-d"); }

        $readContador = function($path, $name, $dataInicial, $dataFinal) {
            $result = [];
            $dataInicialTimestamp = strtotime($dataInicial);
            $dataFinalTimestamp = strtotime($dataFinal);
    
            // Contador geral
            $filePath = $path . $name . "_contador.json";
            if (file_exists($filePath)) {
                $result = json_decode(file_get_contents($filePath), true);
            }
    
            // Contadores por ano, mês e dia
            for ($timestamp = $dataInicialTimestamp; $timestamp <= $dataFinalTimestamp; $timestamp = strtotime('+1 day', $timestamp)) {
                $ano = date('Y', $timestamp);
                $mes = date('m', $timestamp);
                $dia = date('d', $timestamp);
    
                $fileAnoPath = $path . $name . "_" . $ano . ".json";
                if (file_exists($fileAnoPath)) {
                    $data = json_decode(file_get_contents($fileAnoPath), true);
                    if (isset($data[$ano][$mes][$dia])) {
                        if (!isset($result[$ano])) $result[$ano] = [];
                        if (!isset($result[$ano][$mes])) $result[$ano][$mes] = [];
                        if (!isset($result[$ano][$mes][$dia])) $result[$ano][$mes][$dia] = 0;
                        $result[$ano][$mes][$dia] += $data[$ano][$mes][$dia];
                    }
                }
            }
    
            return $result;
        };
    
        $getLogs = function($path, $dataInicial, $dataFinal) {
            $path = $path . 'Log/';
            if (!is_dir($path)) { return []; }
            $files = glob($path . '*.json');
            usort($files, function($a, $b) { return filemtime($b) - filemtime($a); });
            $logs = [];
            $dataInicialTimestamp = strtotime($dataInicial);
            $dataFinalTimestamp = strtotime($dataFinal);
            
            foreach ($files as $file) {
                $fileTimestamp = filemtime($file);
                if ($fileTimestamp >= $dataInicialTimestamp && $fileTimestamp <= $dataFinalTimestamp) {
                    $logs[] = json_decode(file_get_contents($file), true);
                }
            }
            return $logs;
        };
    
        $result = [];
        if (!empty($className)) {
            $classPath = $pathDir . $className . '/';
            $conteudoClass = $readContador($classPath, $className, $dataInicial, $dataFinal);
            $conteudoClass['logs'] = $getLogs($classPath, $dataInicial, $dataFinal);
            $result = [$className => $conteudoClass];
            if (empty($sigla)) {
                $pathsSigla = glob($classPath . '*', GLOB_ONLYDIR);
                $result[$className]['siglas'] = array_map(function($dir) { return basename($dir); }, $pathsSigla);
            }
            if (!empty($sigla)) {
                $siglaPath = $classPath . $sigla . '/';
                $conteudoSigla = $readContador($siglaPath, $sigla, $dataInicial, $dataFinal);
                $conteudoSigla['logs'] = $getLogs($siglaPath, $dataInicial, $dataFinal);
                $result = [$sigla => [$className => $conteudoSigla]];
    
                if (!empty($event)) {
                    $eventPath = $siglaPath . $event . '/';
                    $conteudoEvent = $readContador($eventPath, $event, $dataInicial, $dataFinal);
                    $conteudoEvent['logs'] = $getLogs($eventPath, $dataInicial, $dataFinal);
                    $result = [$sigla => [$className => [$event => $conteudoEvent]]];
                }
            }
        } elseif (!empty($sigla)) {
            $result = [$sigla => []];
            $paths = glob($pathDir . '*', GLOB_ONLYDIR);
            $classNames = array_map(function($dir) { return basename($dir); }, $paths);
            foreach ($classNames as $className) {
                if ($className != "Log") {
                    $LogContadorClass = self::getLogContador($className, $sigla, "", $dataInicial, $dataFinal);
                    if (!empty($LogContadorClass[$sigla]) && !empty($LogContadorClass[$sigla][$className])) {
                        $result[$sigla][$className] = $LogContadorClass[$sigla][$className];
                    }
                    $siglaPath = $pathDir . $className . '/' . $sigla . '/';
                    $eventPaths = glob($siglaPath . '*', GLOB_ONLYDIR);
                    $events = array_map(function($dir) { return basename($dir); }, $eventPaths);
                    foreach ($events as $event) {
                        if ($event != "Log") {
                            $LogContadorEvent = self::getLogContador($className, $sigla, $event, $dataInicial, $dataFinal);
                            if (!empty($LogContadorEvent[$sigla]) && !empty($LogContadorEvent[$sigla][$className])) {
                                $result[$sigla][$className][$event] = $LogContadorEvent[$sigla][$className][$event];
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }
    */
    
    

    
    public static function excutarSomente($className="",$sigla="",$get=[],$post=[],$parametro1=[],$parametro2=[],$parametro3=[]) {
        // https://bw8.hook.app.br/rdstation/api/BW8?teste=123&excutarSomente=2d8h00as18h00
        $segue="SIM";  $excutarsomente=""; $dataFuturaInicial = ""; $dataFuturaFinal = "";$caminhoData="";$arquivo="";
        $caminho = storage_path().'/AGUARDANDO/'; GlobalFuncoes::criarPasta($caminho,false);
        
        foreach(array_keys($get) as $campo){
            if('executarsomente' == strtolower($campo)){$excutarsomente=$get[$campo];}
            if('executar_somente' == strtolower($campo)){$excutarsomente=$get[$campo];}
            if('excutarsomente' == strtolower($campo)){$excutarsomente=$get[$campo];}
            if('excutar_somente' == strtolower($campo)){$excutarsomente=$get[$campo];}
            if('excutasomente' == strtolower($campo)){$excutarsomente=$get[$campo];}
            if('excuta_somente' == strtolower($campo)){$excutarsomente=$get[$campo];}
        }
        if(!empty($get['teste'])){
            //dd(array_keys($get),$className,$sigla,$get,$post,$excutarsomente);
        }
        if(!empty($excutarsomente)){
            
            $dias = 0; $horaInicial="00"; $minutoInicial="00"; $horaFinal="24"; $minutoFinal="00";
            
            $startDia = strpos($excutarsomente, "d");
            if ($startDia === false) { $startDia = strpos($excutarsomente, "D");}
            $dias = ($startDia !== false) ? substr($excutarsomente, 0, $startDia) : 0;
            if ($startDia !== false) { $excutarsomente = substr($excutarsomente, $startDia + 1); }
            
            $temp = [];
            $separadores = ["ate", "as", "t"];
            foreach ($separadores as $separador) {if (strpos($excutarsomente, $separador) !== false && empty($temp)) {
                $temp = explode($separador, $excutarsomente); break;
            } }
            if(!empty($temp)){ $hInicial = $temp[0]??''; $hFinal = $temp[1]??''; }

            $separadores = ["h", ":"];
            foreach ($separadores as $separador) {
                if (strpos($hInicial, $separador) !== false) {
                    $tempInicial = explode($separador, $hInicial);
                    $horaInicial = $tempInicial[0]??''; $minutoInicial = $tempInicial[1]??'';
                }
                if (strpos($hFinal, $separador) !== false) {
                    $tempFinal = explode($separador, $hFinal);
                    $horaFinal = $tempFinal[0]??''; $minutoFinal = $tempFinal[1]??'';
                }
            }
            
            $dataFuturaInicial = date('Y-m-d H:i',strtotime( date('Y-m-d '.$horaInicial.':'.$minutoInicial, strtotime("+$dias days")) ));
            $dataFuturaFinal = date('Y-m-d H:i',strtotime( date('Y-m-d '.$horaFinal.':'.$minutoFinal, strtotime("+$dias days")) ));
            $agora = date('Y-m-d H:i');
            
            if (strtotime($dataFuturaInicial) <= strtotime($agora) && strtotime($dataFuturaFinal) >= strtotime($agora)) {
                $segue="SIM";
            }else{
                $segue="NAO";
                
                if(!empty($get['teste'])){
                    //dd($segue,array_keys($get),$className,$sigla,$get,$post,$excutarsomente,$dataFuturaInicial,$dataFuturaFinal,$agora);
                }                
                file_put_contents($caminho.$dataFuturaInicial."-".$dataFuturaFinal."-".date('Y-m-d H:i').".json",json_encode([$dataFuturaInicial,$dataFuturaFinal,date('Y-m-d H:i')]));
                
                $caminhoData = $caminho.date('Y-m-d',strtotime($dataFuturaInicial))."/"; GlobalFuncoes::criarPasta($caminhoData,false);
                if(!empty($className)){ $caminhoData.= $className."/"; GlobalFuncoes::criarPasta($caminhoData,false); }
                if(!empty($sigla)){ $caminhoData.=$sigla."/"; GlobalFuncoes::criarPasta($caminhoData,false); }
                $caminhoData.= str_replace(":","h",date('H:i',strtotime($dataFuturaInicial)))."_".str_replace(":","h",date('H:i',strtotime($dataFuturaFinal)))."/";GlobalFuncoes::criarPasta($caminhoData,false);
                $arquivo = count( glob($caminhoData . '*.json') )."_".date("Ymd_H_i_s").".json";
                file_put_contents($caminhoData.$arquivo,json_encode([ "sigla"=>$sigla,"GET"=>$get,"POST"=>$post,"parametro1"=>$parametro1,"parametro2"=>$parametro2,"parametro3"=>$parametro3 ]));
                
                if(!empty($_GET)) { if(!empty($_GET['teste'])) {
                    //dd($segue,$dataFuturaInicial,$dataFuturaFinal,$horaInicial??'',$minutoInicial??'',$horaFinal??'',$minutoFinal??'',$dias??'',$matches??[],$excutarsomente,"excutarSomente",$className,$sigla,$get,$post,$caminho);
                } }
                
            }
            //dd($segue,$dataFuturaInicial,$dataFuturaFinal,$horaInicial??'',$minutoInicial??'',$horaFinal??'',$minutoFinal??'',$dias??'',$matches??[],$excutarsomente,"excutarSomente",$className,$sigla,$get,$post,$caminho);
        }
        if(!empty($_GET)) { if(!empty($_GET['teste'])) {
            //dd($arquivo,$caminhoData,$segue,$dataFuturaInicial,$dataFuturaFinal,$excutarsomente,"excutarSomente",$className,$sigla,$get,$post,$caminho);
        } }
        return $segue;
    }    
    ///////////////////////////////////
    public static function paginacao($busca=[],$request=null,$retorno=[],$class=null){
        $retorno['paginacao']['pageNumber']=1;
        if(!empty($request->page_on)){ $retorno['paginacao']['pageNumber']=$request->page_on; }
        else{ if(!empty($_GET['page'])){ $retorno['paginacao']['pageNumber'] = $_GET['page']; } }
        foreach ($busca as $campo) {
            if(!empty($request->{$campo})){  $retorno[$campo] = $request->{$campo}; }
            if(!empty($_GET[$campo])){
                $retorno[$campo] = $_GET[$campo];
                if($request!=null){ if(empty($request->{$campo})){ 
                    $request->merge([$campo => $_GET[$campo]]);
                } }
            }
            
        }
        if(!empty($class)){
            $retorno = GlobalFuncoes::funcoesRota($class,$request);
        }
        return $retorno;
    }
    public static function funcoesJS($request,$retorno){
        if(!empty($request->mostraJanela)){ $retorno['funcoesJS']['mostraJanela'] = $request->mostraJanela; }
        return $retorno;
    }
    public static function funcoesRota($class=null,$request=null){ if(!empty($class)){
        $filtro="funcao_";
        $funcoes = array_filter(get_class_methods($class), function ($item) use ( $filtro ) {
            if (stripos($item, $filtro ) !== false) { return true; } return false;
        });
        foreach ($funcoes as $funcao) {
            $fn=str_replace($filtro,"",$funcao);
            if(!empty($request->{$fn})){$class->$funcao($request); }
        }
        return $class->retorno;
    } }
    ///////////////////////////////////    
    /*
    public static function md5Pasta($pasta=null) { if(!empty($pasta)){
        return preg_replace( '/[^a-zA-Z0-9]+/', '-', strtolower( md5( $pasta) ) );
    } }
    public static function pastaUsuario($id=null,$local='public') { if(!empty($id)){
        $GlobalFuncoes = new GlobalFuncoes();
        if($local=='public'){ return public_path() . "/User/" . $GlobalFuncoes->md5Pasta('Usuario' . $id) .'/'; }
        if($local=='url'){ return asset( "User/". $GlobalFuncoes->md5Pasta('Usuario' . $id) ) .'/'; }
    } }
    public static function pastaApp($codigo=null,$local='public') { if(!empty($codigo)){
        $GlobalFuncoes = new GlobalFuncoes();
        if($local=='public'){ return public_path(). "/APP/".$GlobalFuncoes->md5Pasta('APP' . $codigo).'/'; }
        if($local=='url'){ return asset( "APP/".$GlobalFuncoes->md5Pasta('APP' . $codigo)).'/'; }
    } }
    */
    public static function csv2array($file="",$delimiter=";")
    {
        if (($handle = fopen($file, "r")) === false) {die("can't open the file.");}
        $csv_json = array();
        while ($row = fgetcsv($handle, 4000, $delimiter)) { $csv_json[] = $row; }
        fclose($handle);
        return $csv_json;
    }    
    public static function conteudoCSV($arquivoCSV="",$separador="",$conteudo=""){
        $linhas = [];
        if(file_exists($arquivoCSV)){
            $conteudo = file_get_contents($arquivoCSV);
        }
        if(!empty($conteudo)){
            $f = $conteudo;
            /*****/ GlobalFuncoes::debugcodigo('conteudoCSV_type',[ 'conteudoCSV_type', gettype($f), mb_detect_encoding($f), 'separador'=>$separador, 'arquivoCSV'=>$arquivoCSV ]); /*****/ 
            if(mb_detect_encoding($f)!="UTF-8"){
                $f = mb_convert_encoding($f, 'UTF8', 'UTF-16LE'); 
            }
            $f = preg_split("/\R/", $f); 
            $f = array_map('str_getcsv', $f);

            foreach($f as $line => $record){ if(isset($record[0])){
                $record[0]=preg_replace('/[\x80-\xFF]/', '', $record[0]);
                if(empty($separador)){
                    if(strpos($record[0], ",") !== false){ $separador=","; }
                    if(strpos($record[0], ";") !== false){ $separador=";"; }
                    if(strpos($record[0], "\t") !== false){ $separador="\t"; }
                }
                /*****/ GlobalFuncoes::debugcodigo('conteudoCSV',[ 'conteudoCSV', $record,'line'=>$line, 'record[0]'=>$record[0], 'separador'=>$separador, 'arquivoCSV'=>$arquivoCSV ]); /*****/ 

                // Escapar caracteres especiais na expressão regular
                $escapedSeparador = preg_quote($separador, '/'); //$linhas[] = preg_split('/[' . $escapedSeparador . ']/', $record[0]);                //

                $linhas[] = preg_split('/['.  preg_quote($separador, '/') .']/',$record[0]);

            } }
        }
        return $linhas;
    }
    private static function limpaArrayRecursivo(&$array, $fieldNames)
    {
        foreach ($fieldNames as $fieldName) {
            unset($array[$fieldName]);
        }
        foreach ($array as &$value) {
            if (is_array($value)) {
                // Chama a função recursivamente
                self::limpaArrayRecursivo($value, $fieldNames);
            }
        }
    }

    public static function limpaArray($data = [], $fieldNames = [])
    {
        // Clona os dados e aplica a função
        $dataClone = $data;
        self::limpaArrayRecursivo($dataClone, $fieldNames);

        return $dataClone;
    }

    public static function inverte($dados=array(),$separador="-"){
        $retorno=$dados;
        $tipo=gettype($dados);
        if($tipo=="string"){
            $dados = str_replace(array('-','/',' ','_','\\'), $separador, $dados);
            $dados = explode($separador, $dados);
        }
        if(gettype($dados)=="array"){
            $retorno = array_reverse($dados);
        }
        if($tipo=="string"){
            $retorno = implode($separador, $retorno);
        }
        return $retorno;
    }
    public static function soNumero($str=""){
        return preg_replace("/[^0-9]/", "",$str);
    }
    public static function numero($number="0,00", int $decimal = 2) {
        $number = str_replace(' ', '', $number);
        $number = str_replace(',', '.', $number);
        if (strpos($number, '.')) {
            $groups = explode('.', str_replace(',', '.', $number));
            $lastGroup = array_pop($groups);
            $number = implode('', $groups) . '.' . $lastGroup;
        }
        return bcadd($number, 0, $decimal);//$number;//number_format($number+0, $decimal);//
    }
    public static function soma($numeros=array()) {
        $retorno=0;
        if(gettype($numeros)=='array'){
            for ($n=0; $n < count($numeros); $n++) {
                $retorno+=GlobalFuncoes::numero($numeros[$n]);
            }
        }
        return number_format($retorno, 2,',', '.');
    }
    public static function Mask($mask="cep",$str=""){
        $str=GlobalFuncoes::soNumero($str);
        $limite=0;
        switch ($mask) {
            case 'cep': $mask="#####-###"; $limite=8; break;
            case 'cpf': $mask="###.###.###-##"; break;
            case 'cnpj': $mask="##.###.###/####-##"; break;
            case 'telefone': $mask="(##)####-####"; break;
            case 'dataBR': $mask="##/##/####"; break;
        }
        $str = str_replace(" ","",$str);
        if(strlen($str)==$limite){
            for($i=0;$i<strlen($str);$i++){
                $mask[strpos($mask,"#")] = $str[$i];
            }
            $str=$mask;
        }
        return $str;
    }
    public static function legenda($texto){
        switch ($texto) {
            case 'EmProducao'   : $texto="Em Produção"; break;
            case 'CartaodeCredito'  : $texto="Cartão de Crédito"; break;
            case 'PROPOSTA'  : $texto="Proposta Comercial"; break;
            case 'CONTRATO'  : $texto="Contrato Fornecedor"; break;
            case 'TERMODEUSO'  : $texto="Termo de Uso"; break;
            case 'ANDROIDNOVOAPP'  : $texto="Etapas: Novo APP/Android"; break;
            case 'ANDROIDATUALIZACAO'  : $texto="Etapas: Atualizar APP/Android"; break;
            case 'CODIGOFONTE'  : $texto="CODIGOFONTE"; break;
        }
        return $texto;
    }
    public static function plural($valor=0,$texto='Imagem',$ordem=1){
        if($valor>1){ switch ($texto) {
            case 'Imagem'   : $texto="Imagens";break;
            case 'imagem'   : $texto="imagens";break;
            case 'Item' : $texto="Itens";break;
            case 'item' : $texto="itens";break;
            case 'Foto' : $texto="Fotos";break;
            case 'foto' : $texto="fotos";break;
            case 'Enviada'  : $texto="Enviadas";break;
            case 'Copia'    : $texto="Copias";break;
            case 'PEDIDO'    : $texto="PEDIDOS";break;
            case 'Ativo'    : $texto="Ativos";break;
            case 'OPÇÃO'    : $texto="OPÇÕES";break;
            case 'dia'    : $texto="dias";break;
        } }
        if($ordem==1){return $valor." ".$texto;}
        else{return $texto." ".$valor;}
    }
    public static function aplicaZero($valor=0,$zero=1){
        $GlobalFuncoes = new GlobalFuncoes();
        if(strlen($valor)<$zero){
            $valor=$GlobalFuncoes->aplicaZero("0".$valor,$zero);
        }
        return $valor;
    }
    public static function formataMoeda($valor="1,00",$copia=1){
        if(gettype($valor)=="string"){ $valor=str_replace(",",".",$valor)+0; }
        //return number_format($valor*$copia, 2);
        return number_format($valor*$copia, 2, ',', '.');
    }
    public static function tirarAcentos( $texto ){
        $comAcentos = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú');
        $semAcentos = array('a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', '0', 'U', 'U', 'U');
        return str_replace($comAcentos, $semAcentos, $texto); //preg_replace
    }
    public static function limpaNome($nome,$space=true) {     
        if($space==true){
            return preg_replace('/[^a-z0-9-_]/', '', strtolower(str_replace(' ', '', self::tirarAcentos($nome))));
        }
        return preg_replace('/[^a-z0-9-_]/', '', strtolower(str_replace(' ', '_', self::tirarAcentos($nome))));
    }     
    public static function build_http_query( $query=[] ){
        $query_array = [];
        foreach( $query as $key => $key_value ){$query_array[] = urlencode( $key ) . '=' . urlencode( $key_value );}
        return implode( '&', $query_array );
    }    
    public static function utf8_converter($obj) {
        if ( is_object($obj) ) {
            $newObj = json_decode(json_encode($obj));//new stdClass();
            foreach ($obj as $property => $value) {
                //$newProperty = $closure($property);
                if(gettype($value)=="string"){
                    if(!mb_detect_encoding($value, 'utf-8', true)){ $value = utf8_encode($value); }
                }
                $newValue = self::utf8_converter($value);
                $newObj->$property = $newValue;
            }
            return $newObj;
        } else if ( is_array($obj) ) {
            $newArray = array();
            foreach ($obj as $key => $value) {
                //$key = $closure($key);
                if(gettype($value)=="string"){
                    if(!mb_detect_encoding($value, 'utf-8', true)){ $value = utf8_encode($value); }
                }
                $newArray[$key] = self::utf8_converter($value);
            }
            return $newArray;
        } else {
            return $obj;
        }
    }
    public static function json_acento_encode($arrayJson){
        $arrayJson = self::utf8_converter($arrayJson);
        $var = json_encode($arrayJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return utf8_decode($var);
    }
    public static function json_acento_decode($textJson){
        $textJson = utf8_encode($textJson);
        //$arrayJson = self::utf8_converter($arrayJson);
        return json_decode($textJson,true);
    }
    
    public static function buscaNoLead($Campo=[],$Lead=[]){
        $valor="";
        if(!empty($Campo)&&!empty($Lead)){
            if(gettype($Lead)=="array"){ $Lead=json_decode(json_encode($Lead)); }
            if(empty($valor)&&!empty($Lead->{$Campo})){ $valor=$Lead->{$Campo}; }
            if(empty($valor)&&!empty($Lead->custom_fields->{$Campo})){ $valor=$Lead->custom_fields->{$Campo}; }
            /*****/ GlobalFuncoes::debugcodigo('buscaNoLead',[ 'buscaNoLead', 'valor'=>$valor, 'Campo'=>$Campo, 'Lead'=>$Lead ]); /*****/
        }
        return $valor;
    }
    public static function buscaNoPersonalizados($Campo=[],$Lead=[],$Conta=[]){
        $valor="";
        if(!empty($Campo)&&!empty($Conta)){
            $camposPersonalizados = $Conta->camposPersonalizados();
            if(!empty($camposPersonalizados)){
                $camposKey = array_keys(array_column($camposPersonalizados, 'api_identifier'), $Campo);
                foreach($camposKey as $key){                  
                    if(empty($valor)&&!empty($camposPersonalizados[$key])){
                        if(!empty($camposPersonalizados[$key]["name"])){ if(!empty($camposPersonalizados[$key]["name"]["default"])){
                            $valor=GlobalFuncoes::buscaNoLead($camposPersonalizados[$key]["name"]["default"],$Lead);
                        } }
                    }
                }
            }
        }
        return $valor;
    }     
    public static function pegaNivelLead($Campo=[],$Lead=[]){
        $valor="";

        $Lead=json_decode(json_encode($Lead),true);
        
        $Campo=explode('@',$Campo);
        if(count($Campo)==1){ if(!empty($Lead[ $Campo[0] ])) {
            $valor = $Lead[ $Campo[0] ];
        } }

        if(count($Campo)==2){ if(!empty($Lead[ $Campo[0] ])) {
            if(!empty($Lead[ $Campo[0] ][0])){ if(!empty($Lead[ $Campo[0] ][0][ $Campo[1] ])){ $valor=$Lead[ $Campo[0] ][0][ $Campo[1] ]; } }
            if(!empty($Lead[ $Campo[0] ])){ if(!empty($Lead[ $Campo[0] ][ $Campo[1] ])){ $valor=$Lead[ $Campo[0] ][ $Campo[1] ]; } }
        } }

        if(count($Campo)==3){ if(!empty($Lead[ $Campo[0] ])) {
            if(!empty($Lead[ $Campo[0] ][0])){ if(!empty($Lead[ $Campo[0] ][0][ $Campo[1] ])){
                
                if(!empty($Lead[ $Campo[0] ][0][ $Campo[1] ][0])){
                    if(!empty($Lead[ $Campo[0] ][0][ $Campo[1] ][0][ $Campo[2] ])){
                        $valor=$Lead[ $Campo[0] ][0][ $Campo[1] ][0][ $Campo[2] ];
                    }
                }
                if(!empty($Lead[ $Campo[0] ][0][ $Campo[1] ][ $Campo[2] ])){
                    $valor=$Lead[ $Campo[0] ][0][ $Campo[1] ][ $Campo[2] ];
                }
                
            } }
            if(!empty($Lead[ $Campo[0] ])){ if(!empty($Lead[ $Campo[0] ][ $Campo[1] ])){
                
                if(!empty($Lead[ $Campo[0] ][ $Campo[1] ][0])){
                    if(!empty($Lead[ $Campo[0] ][ $Campo[1] ][0][ $Campo[2] ])){
                        $valor=$Lead[ $Campo[0] ][ $Campo[1] ][0][ $Campo[2] ];
                    }
                }
                if(!empty($Lead[ $Campo[0] ][ $Campo[1] ][ $Campo[2] ])){
                    $valor=$Lead[ $Campo[0] ][ $Campo[1] ][ $Campo[2] ];
                }
                
            } }
        } }


        return $valor;
    }     
    public static function noLead($Campo="",$Lead=[],$Conta=[]){
        $valor="";
        if(empty($valor)&&!empty($Campo)){ $valor=GlobalFuncoes::buscaNoLead($Campo,$Lead); }
        /*****/ GlobalFuncoes::debugcodigo('noLead_1',[ 'noLead_1', 'valor'=>$valor, 'Campo'=>$Campo, 'Lead'=>$Lead , 'Conta'=>$Conta ]); /*****/
        if(empty($valor)&&!empty($Campo)&&!empty($Conta)){ $valor=GlobalFuncoes::buscaNoPersonalizados($Campo,$Lead,$Conta); }
        /*****/ GlobalFuncoes::debugcodigo('noLead_2',[ 'noLead_2', 'valor'=>$valor, 'Campo'=>$Campo, 'Lead'=>$Lead , 'Conta'=>$Conta ]); /*****/
        if(empty($valor)&&!empty($Campo)){ $valor=GlobalFuncoes::pegaNivelLead($Campo,$Lead); }
        /*****/ GlobalFuncoes::debugcodigo('noLead_3',[ 'noLead_3', 'valor'=>$valor, 'Campo'=>$Campo, 'Lead'=>$Lead , 'Conta'=>$Conta ]); /*****/
        return $valor;
    }     
    
    public static function diaUtil($data,$tipo='volta'){
        $GlobalFuncoes = new GlobalFuncoes();
        $diaSemana=date('w', strtotime($data));
        if($diaSemana==0||$diaSemana==6){
            $acao=' + 1 days';
            if($tipo=='volta'){ $acao=' - 1 days'; }
            $data=$GlobalFuncoes::diaUtil(date('Ymd', strtotime($data. $acao)),$tipo);
        }
        return $data;
    }
    public static function addDias($date = '',$dias=1,$formato='Y-m-d',$operador="+")
    {
        if(empty($date)){ $date=date($formato); }
        return date($formato, strtotime($date . ' '.$operador.$dias.' day'));
    }
    public static function addMes($date = '',$mes=1,$formato='Y-m-d',$operador="+")
    {
        if(empty($date)){ $date=date($formato); }
        return date($formato, strtotime($date . ' '.$operador.$mes.' months'));
        //return date($formato, strtotime(date($formato, strtotime($date)) . ' '.$operador.$mes." months");
    }
    public static function dataBR($data,$formato='d/m/Y H'){
        $retorno="";
        if(empty($data)){ $data=date("Y-m-d"); }
        $temp = explode(" ", $data);if(!empty($temp[1])){$data=$temp[0];}
        //if (stripos($data, "-") !== false) { $data=str_replace("-","",$data); }
        $data=GlobalFuncoes::inverte($data);

        $tempH = explode(" ", $formato);if(!empty($tempH[1])){
            $data=date($tempH[0], strtotime($data));
            if(!empty($temp[1])){$data=$data." ".$temp[1];}
        }else{
            $data=date($formato, strtotime($data));
        }

        return $data;
    }
    public static function diasemana($data,$padrao=0){
        if(empty($data)){ $data=date("Y-m-d"); }
        $semana = array('Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sabado');
        $abreviado = array('Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab');
        $sigla = array('D', 'S', 'T', 'Q', 'Q', 'S', 'S');
        if($padrao==2) { return $sigla[ date('w', strtotime($data)) ]; }
        if($padrao==1) { return $abreviado[ date('w', strtotime($data)) ]; }
        return $semana[ date('w', strtotime($data)) ];
    }
    public static function mes($mes){
        if(empty($mes)){ $mes=date("m")-1;}
        $meses = array('Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
        return $meses[ $mes ];
    }    
    public static function mesesano($data,$padrao=0){
        if(empty($data)){ $data=date("Y-m-d"); }
        $meses = array('Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
        $abreviado = array('Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez');
        $sigla = array('J', 'F', 'M', 'A', 'M', 'J', 'J', 'A', 'S', 'O', 'N', 'D');
        if($padrao==2) { return $sigla[ (date('m', strtotime($data))-1) ]; }
        if($padrao==1) { return $abreviado[ (date('m', strtotime($data))-1) ]; }
        return $meses[ date('M', strtotime($data))+0 ];
    }
    public static function mesingles($mes){
        if(empty($mes)){ $mes=date("M"); }
        $meses = array('January'=>'Janeiro','February'=>'Fevereiro','March'=>'Março','April'=>'Abril','May'=>'Maio','June'=>'Junho','July'=>'Julho','August'=>'Agosto','September'=>'Setembro','October'=>'Outubro','November'=>'Novembro','December'=>'Dezembro');
        return $meses[ $mes ];
    }
    public static function ultimodiames($date = '',$formato='Y-m-d')
    {
        if(empty($date)){ $date=date($formato); }
        $month  = date('m', strtotime($date));
        $year   = date('Y', strtotime($date));
        $result = strtotime($year."-".$month."-01");
        $result = strtotime('-1 second', strtotime('+1 month', $result));

        return date($formato, $result);
    }
    public static function diferencaDias($data1,$data2="",$tipo='',$separador='',$div='dia'){
        $retorno="";
        if(empty($data2)){ $data2=date("Ymd H:i:s"); }
        $temp1 = explode(" ", $data1);
        $temp2 = explode(" ", $data2);
        if(empty($tipo)){
            $data1=str_replace("-","",$data1);
            if($div=="dia"){
                if(!empty($temp1[1])){$data1=$temp1[0];}
                if(!empty($temp2[1])){$data2=$temp2[0];}
                $retorno=round( (strtotime($data2) - strtotime($data1) )/60/60/24 );
            }
            if($div=="hora"){
                $retorno=round( (strtotime($data2) - strtotime($data1) )/60/60 );
            }
            if($div=="minuto"){
                $retorno=round( (strtotime($data2) - strtotime($data1) )/60 );
            }
        }else{
            $data1_hora=0;$data1_minuto=0;$data1_second=0;
            $data2_hora=0;$data2_minuto=0;$data2_second=0;
            if(strstr($data1,":")){
                $dh1 = explode(" ", $data1);
                $data1 = explode($separador, $dh1[0]);
                $data1_h = explode(":", $dh1[1]);

                $data1_hora=$data1_h[0];
                $data1_minuto=$data1_h[1];
                $data1_second=$data1_h[2];
            }else{
                if(!empty($separador)){
                    $data1 = explode($separador, $data1);
                    $data1_ano=$data1[0];
                    $data1_mes=$data1[1];
                    $data1_dia=$data1[2];
                }else{
                    $data1_ano=substr($data1,0,4);
                    $data1_mes=substr($data1,4,2);
                    $data1_dia=substr($data1,6,2);
                }
            }
            if(strstr($data2,":")){
                $dh2 = explode(" ", $data2);
                $data2 = explode($separador, $dh2[0]);
                $data2_h = explode(":", $dh2[1]);

                $data2_hora=$data2_h[0];
                $data2_minuto=$data2_h[1];
                $data2_second=$data2_h[2];
            }else{
                if(!empty($separador)){
                    $data2 = explode($separador, $data2);
                    $data2_ano=$data2[0];
                    $data2_mes=$data2[1];
                    $data2_dia=$data2[2];
                }else{
                    $data2_ano=substr($data2,0,4);
                    $data2_mes=substr($data2,4,2);
                    $data2_dia=substr($data2,6,2);
                }
            }
            switch ($tipo){
                case "A":$X = 31104000;	break;
                case "M":$X = 2592000;	break;
                case "D":$X = 86400;	break;
                case "H":$X = 3600;		break;
                case "MI":$X = 60;		break;
                default:$X = 1;
            }
            //mktime (hora,minuto,second,mes,dia,ano,$is_dst)
            $retorno=(
                ((mktime($data1_hora,$data1_minuto,$data1_second,$data1_mes,$data1_dia,$data1_ano)-mktime($data2_hora,$data2_minuto,$data2_second,$data2_mes,$data2_dia,$data2_ano))/$X)
            );
        }
        return $retorno;
    }
    public static function geraColor(){
		$rcolor = '#';
		for($i=0;$i<6;$i++){
		    $rNumber = rand(0,15);
		    switch ($rNumber) {
		        case 10:$rNumber='A';break;
		        case 11:$rNumber='B';break;
		        case 12:$rNumber='C';break;
		        case 13:$rNumber='D';break;
		        case 14:$rNumber='E';break;
		        case 15:$rNumber='F';break;
		    }
		    $rcolor .= $rNumber;
		}
		return $rcolor;
	}    
    public static function geraCodigo($tamanho=8,$MODEL=null,$campo="") {
        $GlobalFuncoes = new GlobalFuncoes();
        $codigo = strtoupper(substr(md5(uniqid(rand(1,6))), 0, $tamanho));
        if($MODEL!=null&&$campo!=""){ if($MODEL::where($campo,$codigo)->count()>0){
            $codigo=$GlobalFuncoes::geraCodigo($tamanho,$MODEL,$campo);
        } }
        return $codigo;
    }
    
    public static function csvtoarray($file="",$delimiter=";",$badchar=[],$inicio=0,$limite=100000)
    {
        $badchar=array(
            chr(0), chr(1), chr(2), chr(3), chr(4), chr(5), chr(6), chr(7), chr(8), chr(9), chr(10),
            chr(11), chr(12), chr(13), chr(14), chr(15), chr(16), chr(17), chr(18), chr(19), chr(20),
            chr(21), chr(22), chr(23), chr(24), chr(25), chr(26), chr(27), chr(28), chr(29), chr(30),
            chr(31),
            chr(127)
        );        
    	$l=0;
        if (($handle = fopen($file, "r")) === false) {die("can't open the file.");}
        $csv_json = array();
    	//echo "<hr>buscando ". $inicio. " ate ".$limite."<hr>";
        while ($row = fgetcsv($handle, 4000, $delimiter)) { if($l>=$inicio){ 
    		$csv_json[] = str_replace($badchar, '',  $row); 
    		if($l>=$limite){ fclose($handle); return $csv_json; } //echo "<hr>acho ". $l. " ate ".$limite."<hr>";
    	} $l++; }
        fclose($handle);
        return $csv_json;
    }
    public static function arrayToXml($array = [], $xml = null) {
        if ($xml === null) {
            $xml = new \SimpleXMLElement('<root/>');
        }

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                // Chama a função recursivamente para arrays
                self::arrayToXml($value, $xml->addChild($key));
            } elseif (is_object($value)) {
                // Converte objetos para array e chama a função recursivamente
                self::arrayToXml(json_decode(json_encode($value), true), $xml->addChild($key));
            } else {
                // Trata strings e outros tipos
                $xml->addChild($key, htmlspecialchars((string)$value));
            }
        }

        return $xml->asXML();
    }  

    public static function  remove_emoji($text){
        $text = preg_replace('/\x{1F3F4}\x{E0067}\x{E0062}(?:\x{E0077}\x{E006C}\x{E0073}|\x{E0073}\x{E0063}\x{E0074}|\x{E0065}\x{E006E}\x{E0067})\x{E007F}|(?:\x{1F9D1}\x{1F3FF}\x{200D}\x{2764}(?:\x{FE0F}\x{200D}(?:\x{1F48B}\x{200D})?|\x{200D}(?:\x{1F48B}\x{200D})?)\x{1F9D1}|\x{1F469}\x{1F3FF}\x{200D}\x{1F91D}\x{200D}[\x{1F468}\x{1F469}]|\x{1FAF1}\x{1F3FF}\x{200D}\x{1FAF2})[\x{1F3FB}-\x{1F3FE}]|(?:\x{1F9D1}\x{1F3FE}\x{200D}\x{2764}(?:\x{FE0F}\x{200D}(?:\x{1F48B}\x{200D})?|\x{200D}(?:\x{1F48B}\x{200D})?)\x{1F9D1}|\x{1F469}\x{1F3FE}\x{200D}\x{1F91D}\x{200D}[\x{1F468}\x{1F469}]|\x{1FAF1}\x{1F3FE}\x{200D}\x{1FAF2})[\x{1F3FB}-\x{1F3FD}\x{1F3FF}]|(?:\x{1F9D1}\x{1F3FD}\x{200D}\x{2764}(?:\x{FE0F}\x{200D}(?:\x{1F48B}\x{200D})?|\x{200D}(?:\x{1F48B}\x{200D})?)\x{1F9D1}|\x{1F469}\x{1F3FD}\x{200D}\x{1F91D}\x{200D}[\x{1F468}\x{1F469}]|\x{1FAF1}\x{1F3FD}\x{200D}\x{1FAF2})[\x{1F3FB}\x{1F3FC}\x{1F3FE}\x{1F3FF}]|(?:\x{1F9D1}\x{1F3FC}\x{200D}\x{2764}(?:\x{FE0F}\x{200D}(?:\x{1F48B}\x{200D})?|\x{200D}(?:\x{1F48B}\x{200D})?)\x{1F9D1}|\x{1F469}\x{1F3FC}\x{200D}\x{1F91D}\x{200D}[\x{1F468}\x{1F469}]|\x{1FAF1}\x{1F3FC}\x{200D}\x{1FAF2})[\x{1F3FB}\x{1F3FD}-\x{1F3FF}]|(?:\x{1F9D1}\x{1F3FB}\x{200D}\x{2764}(?:\x{FE0F}\x{200D}(?:\x{1F48B}\x{200D})?|\x{200D}(?:\x{1F48B}\x{200D})?)\x{1F9D1}|\x{1F469}\x{1F3FB}\x{200D}\x{1F91D}\x{200D}[\x{1F468}\x{1F469}]|\x{1FAF1}\x{1F3FB}\x{200D}\x{1FAF2})[\x{1F3FC}-\x{1F3FF}]|\x{1F468}(?:\x{1F3FB}(?:\x{200D}(?:\x{2764}(?:\x{FE0F}\x{200D}(?:\x{1F48B}\x{200D}\x{1F468}[\x{1F3FB}-\x{1F3FF}]|\x{1F468}[\x{1F3FB}-\x{1F3FF}])|\x{200D}(?:\x{1F48B}\x{200D}\x{1F468}[\x{1F3FB}-\x{1F3FF}]|\x{1F468}[\x{1F3FB}-\x{1F3FF}]))|\x{1F91D}\x{200D}\x{1F468}[\x{1F3FC}-\x{1F3FF}]|[\x{2695}\x{2696}\x{2708}]\x{FE0F}|[\x{2695}\x{2696}\x{2708}]|[\x{1F33E}\x{1F373}\x{1F37C}\x{1F393}\x{1F3A4}\x{1F3A8}\x{1F3EB}\x{1F3ED}\x{1F4BB}\x{1F4BC}\x{1F527}\x{1F52C}\x{1F680}\x{1F692}\x{1F9AF}-\x{1F9B3}\x{1F9BC}\x{1F9BD}]))?|[\x{1F3FC}-\x{1F3FF}]\x{200D}\x{2764}(?:\x{FE0F}\x{200D}(?:\x{1F48B}\x{200D}\x{1F468}[\x{1F3FB}-\x{1F3FF}]|\x{1F468}[\x{1F3FB}-\x{1F3FF}])|\x{200D}(?:\x{1F48B}\x{200D}\x{1F468}[\x{1F3FB}-\x{1F3FF}]|\x{1F468}[\x{1F3FB}-\x{1F3FF}]))|\x{200D}(?:\x{2764}(?:\x{FE0F}\x{200D}(?:\x{1F48B}\x{200D})?|\x{200D}(?:\x{1F48B}\x{200D})?)\x{1F468}|[\x{1F468}\x{1F469}]\x{200D}(?:\x{1F466}\x{200D}\x{1F466}|\x{1F467}\x{200D}[\x{1F466}\x{1F467}])|\x{1F466}\x{200D}\x{1F466}|\x{1F467}\x{200D}[\x{1F466}\x{1F467}]|[\x{1F33E}\x{1F373}\x{1F37C}\x{1F393}\x{1F3A4}\x{1F3A8}\x{1F3EB}\x{1F3ED}\x{1F4BB}\x{1F4BC}\x{1F527}\x{1F52C}\x{1F680}\x{1F692}\x{1F9AF}-\x{1F9B3}\x{1F9BC}\x{1F9BD}])|\x{1F3FF}\x{200D}(?:\x{1F91D}\x{200D}\x{1F468}[\x{1F3FB}-\x{1F3FE}]|[\x{1F33E}\x{1F373}\x{1F37C}\x{1F393}\x{1F3A4}\x{1F3A8}\x{1F3EB}\x{1F3ED}\x{1F4BB}\x{1F4BC}\x{1F527}\x{1F52C}\x{1F680}\x{1F692}\x{1F9AF}-\x{1F9B3}\x{1F9BC}\x{1F9BD}])|\x{1F3FE}\x{200D}(?:\x{1F91D}\x{200D}\x{1F468}[\x{1F3FB}-\x{1F3FD}\x{1F3FF}]|[\x{1F33E}\x{1F373}\x{1F37C}\x{1F393}\x{1F3A4}\x{1F3A8}\x{1F3EB}\x{1F3ED}\x{1F4BB}\x{1F4BC}\x{1F527}\x{1F52C}\x{1F680}\x{1F692}\x{1F9AF}-\x{1F9B3}\x{1F9BC}\x{1F9BD}])|\x{1F3FD}\x{200D}(?:\x{1F91D}\x{200D}\x{1F468}[\x{1F3FB}\x{1F3FC}\x{1F3FE}\x{1F3FF}]|[\x{1F33E}\x{1F373}\x{1F37C}\x{1F393}\x{1F3A4}\x{1F3A8}\x{1F3EB}\x{1F3ED}\x{1F4BB}\x{1F4BC}\x{1F527}\x{1F52C}\x{1F680}\x{1F692}\x{1F9AF}-\x{1F9B3}\x{1F9BC}\x{1F9BD}])|\x{1F3FC}\x{200D}(?:\x{1F91D}\x{200D}\x{1F468}[\x{1F3FB}\x{1F3FD}-\x{1F3FF}]|[\x{1F33E}\x{1F373}\x{1F37C}\x{1F393}\x{1F3A4}\x{1F3A8}\x{1F3EB}\x{1F3ED}\x{1F4BB}\x{1F4BC}\x{1F527}\x{1F52C}\x{1F680}\x{1F692}\x{1F9AF}-\x{1F9B3}\x{1F9BC}\x{1F9BD}])|(?:\x{1F3FF}\x{200D}[\x{2695}\x{2696}\x{2708}]|\x{1F3FE}\x{200D}[\x{2695}\x{2696}\x{2708}]|\x{1F3FD}\x{200D}[\x{2695}\x{2696}\x{2708}]|\x{1F3FC}\x{200D}[\x{2695}\x{2696}\x{2708}]|\x{200D}[\x{2695}\x{2696}\x{2708}])\x{FE0F}|\x{200D}(?:[\x{1F468}\x{1F469}]\x{200D}[\x{1F466}\x{1F467}]|[\x{1F466}\x{1F467}])|\x{1F3FF}\x{200D}[\x{2695}\x{2696}\x{2708}]|\x{1F3FE}\x{200D}[\x{2695}\x{2696}\x{2708}]|\x{1F3FD}\x{200D}[\x{2695}\x{2696}\x{2708}]|\x{1F3FC}\x{200D}[\x{2695}\x{2696}\x{2708}]|\x{1F3FF}|\x{1F3FE}|\x{1F3FD}|\x{1F3FC}|\x{200D}[\x{2695}\x{2696}\x{2708}])?|(?:\x{1F469}(?:\x{1F3FB}\x{200D}\x{2764}(?:\x{FE0F}\x{200D}(?:\x{1F48B}\x{200D}[\x{1F468}\x{1F469}]|[\x{1F468}\x{1F469}])|\x{200D}(?:\x{1F48B}\x{200D}[\x{1F468}\x{1F469}]|[\x{1F468}\x{1F469}]))|[\x{1F3FC}-\x{1F3FF}]\x{200D}\x{2764}(?:\x{FE0F}\x{200D}(?:\x{1F48B}\x{200D}[\x{1F468}\x{1F469}]|[\x{1F468}\x{1F469}])|\x{200D}(?:\x{1F48B}\x{200D}[\x{1F468}\x{1F469}]|[\x{1F468}\x{1F469}])))|\x{1F9D1}[\x{1F3FB}-\x{1F3FF}]\x{200D}\x{1F91D}\x{200D}\x{1F9D1})[\x{1F3FB}-\x{1F3FF}]|\x{1F469}\x{200D}\x{1F469}\x{200D}(?:\x{1F466}\x{200D}\x{1F466}|\x{1F467}\x{200D}[\x{1F466}\x{1F467}])|\x{1F469}(?:\x{200D}(?:\x{2764}(?:\x{FE0F}\x{200D}(?:\x{1F48B}\x{200D}[\x{1F468}\x{1F469}]|[\x{1F468}\x{1F469}])|\x{200D}(?:\x{1F48B}\x{200D}[\x{1F468}\x{1F469}]|[\x{1F468}\x{1F469}]))|[\x{1F33E}\x{1F373}\x{1F37C}\x{1F393}\x{1F3A4}\x{1F3A8}\x{1F3EB}\x{1F3ED}\x{1F4BB}\x{1F4BC}\x{1F527}\x{1F52C}\x{1F680}\x{1F692}\x{1F9AF}-\x{1F9B3}\x{1F9BC}\x{1F9BD}])|\x{1F3FF}\x{200D}[\x{1F33E}\x{1F373}\x{1F37C}\x{1F393}\x{1F3A4}\x{1F3A8}\x{1F3EB}\x{1F3ED}\x{1F4BB}\x{1F4BC}\x{1F527}\x{1F52C}\x{1F680}\x{1F692}\x{1F9AF}-\x{1F9B3}\x{1F9BC}\x{1F9BD}]|\x{1F3FE}\x{200D}[\x{1F33E}\x{1F373}\x{1F37C}\x{1F393}\x{1F3A4}\x{1F3A8}\x{1F3EB}\x{1F3ED}\x{1F4BB}\x{1F4BC}\x{1F527}\x{1F52C}\x{1F680}\x{1F692}\x{1F9AF}-\x{1F9B3}\x{1F9BC}\x{1F9BD}]|\x{1F3FD}\x{200D}[\x{1F33E}\x{1F373}\x{1F37C}\x{1F393}\x{1F3A4}\x{1F3A8}\x{1F3EB}\x{1F3ED}\x{1F4BB}\x{1F4BC}\x{1F527}\x{1F52C}\x{1F680}\x{1F692}\x{1F9AF}-\x{1F9B3}\x{1F9BC}\x{1F9BD}]|\x{1F3FC}\x{200D}[\x{1F33E}\x{1F373}\x{1F37C}\x{1F393}\x{1F3A4}\x{1F3A8}\x{1F3EB}\x{1F3ED}\x{1F4BB}\x{1F4BC}\x{1F527}\x{1F52C}\x{1F680}\x{1F692}\x{1F9AF}-\x{1F9B3}\x{1F9BC}\x{1F9BD}]|\x{1F3FB}\x{200D}[\x{1F33E}\x{1F373}\x{1F37C}\x{1F393}\x{1F3A4}\x{1F3A8}\x{1F3EB}\x{1F3ED}\x{1F4BB}\x{1F4BC}\x{1F527}\x{1F52C}\x{1F680}\x{1F692}\x{1F9AF}-\x{1F9B3}\x{1F9BC}\x{1F9BD}])|\x{1F9D1}(?:\x{200D}(?:\x{1F91D}\x{200D}\x{1F9D1}|[\x{1F33E}\x{1F373}\x{1F37C}\x{1F384}\x{1F393}\x{1F3A4}\x{1F3A8}\x{1F3EB}\x{1F3ED}\x{1F4BB}\x{1F4BC}\x{1F527}\x{1F52C}\x{1F680}\x{1F692}\x{1F9AF}-\x{1F9B3}\x{1F9BC}\x{1F9BD}])|\x{1F3FF}\x{200D}[\x{1F33E}\x{1F373}\x{1F37C}\x{1F384}\x{1F393}\x{1F3A4}\x{1F3A8}\x{1F3EB}\x{1F3ED}\x{1F4BB}\x{1F4BC}\x{1F527}\x{1F52C}\x{1F680}\x{1F692}\x{1F9AF}-\x{1F9B3}\x{1F9BC}\x{1F9BD}]|\x{1F3FE}\x{200D}[\x{1F33E}\x{1F373}\x{1F37C}\x{1F384}\x{1F393}\x{1F3A4}\x{1F3A8}\x{1F3EB}\x{1F3ED}\x{1F4BB}\x{1F4BC}\x{1F527}\x{1F52C}\x{1F680}\x{1F692}\x{1F9AF}-\x{1F9B3}\x{1F9BC}\x{1F9BD}]|\x{1F3FD}\x{200D}[\x{1F33E}\x{1F373}\x{1F37C}\x{1F384}\x{1F393}\x{1F3A4}\x{1F3A8}\x{1F3EB}\x{1F3ED}\x{1F4BB}\x{1F4BC}\x{1F527}\x{1F52C}\x{1F680}\x{1F692}\x{1F9AF}-\x{1F9B3}\x{1F9BC}\x{1F9BD}]|\x{1F3FC}\x{200D}[\x{1F33E}\x{1F373}\x{1F37C}\x{1F384}\x{1F393}\x{1F3A4}\x{1F3A8}\x{1F3EB}\x{1F3ED}\x{1F4BB}\x{1F4BC}\x{1F527}\x{1F52C}\x{1F680}\x{1F692}\x{1F9AF}-\x{1F9B3}\x{1F9BC}\x{1F9BD}]|\x{1F3FB}\x{200D}[\x{1F33E}\x{1F373}\x{1F37C}\x{1F384}\x{1F393}\x{1F3A4}\x{1F3A8}\x{1F3EB}\x{1F3ED}\x{1F4BB}\x{1F4BC}\x{1F527}\x{1F52C}\x{1F680}\x{1F692}\x{1F9AF}-\x{1F9B3}\x{1F9BC}\x{1F9BD}])|\x{1F469}\x{200D}\x{1F466}\x{200D}\x{1F466}|\x{1F469}\x{200D}\x{1F469}\x{200D}[\x{1F466}\x{1F467}]|\x{1F469}\x{200D}\x{1F467}\x{200D}[\x{1F466}\x{1F467}]|(?:\x{1F441}\x{FE0F}?\x{200D}\x{1F5E8}|\x{1F9D1}(?:\x{1F3FF}\x{200D}[\x{2695}\x{2696}\x{2708}]|\x{1F3FE}\x{200D}[\x{2695}\x{2696}\x{2708}]|\x{1F3FD}\x{200D}[\x{2695}\x{2696}\x{2708}]|\x{1F3FC}\x{200D}[\x{2695}\x{2696}\x{2708}]|\x{1F3FB}\x{200D}[\x{2695}\x{2696}\x{2708}]|\x{200D}[\x{2695}\x{2696}\x{2708}])|\x{1F469}(?:\x{1F3FF}\x{200D}[\x{2695}\x{2696}\x{2708}]|\x{1F3FE}\x{200D}[\x{2695}\x{2696}\x{2708}]|\x{1F3FD}\x{200D}[\x{2695}\x{2696}\x{2708}]|\x{1F3FC}\x{200D}[\x{2695}\x{2696}\x{2708}]|\x{1F3FB}\x{200D}[\x{2695}\x{2696}\x{2708}]|\x{200D}[\x{2695}\x{2696}\x{2708}])|\x{1F636}\x{200D}\x{1F32B}|\x{1F3F3}\x{FE0F}?\x{200D}\x{26A7}|\x{1F43B}\x{200D}\x{2744}|(?:[\x{1F3C3}\x{1F3C4}\x{1F3CA}\x{1F46E}\x{1F470}\x{1F471}\x{1F473}\x{1F477}\x{1F481}\x{1F482}\x{1F486}\x{1F487}\x{1F645}-\x{1F647}\x{1F64B}\x{1F64D}\x{1F64E}\x{1F6A3}\x{1F6B4}-\x{1F6B6}\x{1F926}\x{1F935}\x{1F937}-\x{1F939}\x{1F93D}\x{1F93E}\x{1F9B8}\x{1F9B9}\x{1F9CD}-\x{1F9CF}\x{1F9D4}\x{1F9D6}-\x{1F9DD}][\x{1F3FB}-\x{1F3FF}]|[\x{1F46F}\x{1F9DE}\x{1F9DF}])\x{200D}[\x{2640}\x{2642}]|[\x{26F9}\x{1F3CB}\x{1F3CC}\x{1F575}](?:[\x{FE0F}\x{1F3FB}-\x{1F3FF}]\x{200D}[\x{2640}\x{2642}]|\x{200D}[\x{2640}\x{2642}])|\x{1F3F4}\x{200D}\x{2620}|[\x{1F3C3}\x{1F3C4}\x{1F3CA}\x{1F46E}\x{1F470}\x{1F471}\x{1F473}\x{1F477}\x{1F481}\x{1F482}\x{1F486}\x{1F487}\x{1F645}-\x{1F647}\x{1F64B}\x{1F64D}\x{1F64E}\x{1F6A3}\x{1F6B4}-\x{1F6B6}\x{1F926}\x{1F935}\x{1F937}-\x{1F939}\x{1F93C}-\x{1F93E}\x{1F9B8}\x{1F9B9}\x{1F9CD}-\x{1F9CF}\x{1F9D4}\x{1F9D6}-\x{1F9DD}]\x{200D}[\x{2640}\x{2642}]|[\xA9\xAE\x{203C}\x{2049}\x{2122}\x{2139}\x{2194}-\x{2199}\x{21A9}\x{21AA}\x{231A}\x{231B}\x{2328}\x{23CF}\x{23ED}-\x{23EF}\x{23F1}\x{23F2}\x{23F8}-\x{23FA}\x{24C2}\x{25AA}\x{25AB}\x{25B6}\x{25C0}\x{25FB}\x{25FC}\x{25FE}\x{2600}-\x{2604}\x{260E}\x{2611}\x{2614}\x{2615}\x{2618}\x{2620}\x{2622}\x{2623}\x{2626}\x{262A}\x{262E}\x{262F}\x{2638}-\x{263A}\x{2640}\x{2642}\x{2648}-\x{2653}\x{265F}\x{2660}\x{2663}\x{2665}\x{2666}\x{2668}\x{267B}\x{267E}\x{267F}\x{2692}\x{2694}-\x{2697}\x{2699}\x{269B}\x{269C}\x{26A0}\x{26A7}\x{26AA}\x{26B0}\x{26B1}\x{26BD}\x{26BE}\x{26C4}\x{26C8}\x{26CF}\x{26D1}\x{26D3}\x{26E9}\x{26F0}-\x{26F5}\x{26F7}\x{26F8}\x{26FA}\x{2702}\x{2708}\x{2709}\x{270F}\x{2712}\x{2714}\x{2716}\x{271D}\x{2721}\x{2733}\x{2734}\x{2744}\x{2747}\x{2763}\x{27A1}\x{2934}\x{2935}\x{2B05}-\x{2B07}\x{2B1B}\x{2B1C}\x{2B55}\x{3030}\x{303D}\x{3297}\x{3299}\x{1F004}\x{1F170}\x{1F171}\x{1F17E}\x{1F17F}\x{1F202}\x{1F237}\x{1F321}\x{1F324}-\x{1F32C}\x{1F336}\x{1F37D}\x{1F396}\x{1F397}\x{1F399}-\x{1F39B}\x{1F39E}\x{1F39F}\x{1F3CD}\x{1F3CE}\x{1F3D4}-\x{1F3DF}\x{1F3F5}\x{1F3F7}\x{1F43F}\x{1F4FD}\x{1F549}\x{1F54A}\x{1F56F}\x{1F570}\x{1F573}\x{1F576}-\x{1F579}\x{1F587}\x{1F58A}-\x{1F58D}\x{1F5A5}\x{1F5A8}\x{1F5B1}\x{1F5B2}\x{1F5BC}\x{1F5C2}-\x{1F5C4}\x{1F5D1}-\x{1F5D3}\x{1F5DC}-\x{1F5DE}\x{1F5E1}\x{1F5E3}\x{1F5E8}\x{1F5EF}\x{1F5F3}\x{1F5FA}\x{1F6CB}\x{1F6CD}-\x{1F6CF}\x{1F6E0}-\x{1F6E5}\x{1F6E9}\x{1F6F0}\x{1F6F3}])\x{FE0F}|\x{1F441}\x{FE0F}?\x{200D}\x{1F5E8}|\x{1F9D1}(?:\x{1F3FF}\x{200D}[\x{2695}\x{2696}\x{2708}]|\x{1F3FE}\x{200D}[\x{2695}\x{2696}\x{2708}]|\x{1F3FD}\x{200D}[\x{2695}\x{2696}\x{2708}]|\x{1F3FC}\x{200D}[\x{2695}\x{2696}\x{2708}]|\x{1F3FB}\x{200D}[\x{2695}\x{2696}\x{2708}]|\x{200D}[\x{2695}\x{2696}\x{2708}])|\x{1F469}(?:\x{1F3FF}\x{200D}[\x{2695}\x{2696}\x{2708}]|\x{1F3FE}\x{200D}[\x{2695}\x{2696}\x{2708}]|\x{1F3FD}\x{200D}[\x{2695}\x{2696}\x{2708}]|\x{1F3FC}\x{200D}[\x{2695}\x{2696}\x{2708}]|\x{1F3FB}\x{200D}[\x{2695}\x{2696}\x{2708}]|\x{200D}[\x{2695}\x{2696}\x{2708}])|\x{1F3F3}\x{FE0F}?\x{200D}\x{1F308}|\x{1F469}\x{200D}\x{1F467}|\x{1F469}\x{200D}\x{1F466}|\x{1F636}\x{200D}\x{1F32B}|\x{1F3F3}\x{FE0F}?\x{200D}\x{26A7}|\x{1F635}\x{200D}\x{1F4AB}|\x{1F62E}\x{200D}\x{1F4A8}|\x{1F415}\x{200D}\x{1F9BA}|\x{1FAF1}(?:\x{1F3FF}|\x{1F3FE}|\x{1F3FD}|\x{1F3FC}|\x{1F3FB})?|\x{1F9D1}(?:\x{1F3FF}|\x{1F3FE}|\x{1F3FD}|\x{1F3FC}|\x{1F3FB})?|\x{1F469}(?:\x{1F3FF}|\x{1F3FE}|\x{1F3FD}|\x{1F3FC}|\x{1F3FB})?|\x{1F43B}\x{200D}\x{2744}|(?:[\x{1F3C3}\x{1F3C4}\x{1F3CA}\x{1F46E}\x{1F470}\x{1F471}\x{1F473}\x{1F477}\x{1F481}\x{1F482}\x{1F486}\x{1F487}\x{1F645}-\x{1F647}\x{1F64B}\x{1F64D}\x{1F64E}\x{1F6A3}\x{1F6B4}-\x{1F6B6}\x{1F926}\x{1F935}\x{1F937}-\x{1F939}\x{1F93D}\x{1F93E}\x{1F9B8}\x{1F9B9}\x{1F9CD}-\x{1F9CF}\x{1F9D4}\x{1F9D6}-\x{1F9DD}][\x{1F3FB}-\x{1F3FF}]|[\x{1F46F}\x{1F9DE}\x{1F9DF}])\x{200D}[\x{2640}\x{2642}]|[\x{26F9}\x{1F3CB}\x{1F3CC}\x{1F575}](?:[\x{FE0F}\x{1F3FB}-\x{1F3FF}]\x{200D}[\x{2640}\x{2642}]|\x{200D}[\x{2640}\x{2642}])|\x{1F3F4}\x{200D}\x{2620}|\x{1F1FD}\x{1F1F0}|\x{1F1F6}\x{1F1E6}|\x{1F1F4}\x{1F1F2}|\x{1F408}\x{200D}\x{2B1B}|\x{2764}(?:\x{FE0F}\x{200D}[\x{1F525}\x{1FA79}]|\x{200D}[\x{1F525}\x{1FA79}])|\x{1F441}\x{FE0F}?|\x{1F3F3}\x{FE0F}?|[\x{1F3C3}\x{1F3C4}\x{1F3CA}\x{1F46E}\x{1F470}\x{1F471}\x{1F473}\x{1F477}\x{1F481}\x{1F482}\x{1F486}\x{1F487}\x{1F645}-\x{1F647}\x{1F64B}\x{1F64D}\x{1F64E}\x{1F6A3}\x{1F6B4}-\x{1F6B6}\x{1F926}\x{1F935}\x{1F937}-\x{1F939}\x{1F93C}-\x{1F93E}\x{1F9B8}\x{1F9B9}\x{1F9CD}-\x{1F9CF}\x{1F9D4}\x{1F9D6}-\x{1F9DD}]\x{200D}[\x{2640}\x{2642}]|\x{1F1FF}[\x{1F1E6}\x{1F1F2}\x{1F1FC}]|\x{1F1FE}[\x{1F1EA}\x{1F1F9}]|\x{1F1FC}[\x{1F1EB}\x{1F1F8}]|\x{1F1FB}[\x{1F1E6}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1EE}\x{1F1F3}\x{1F1FA}]|\x{1F1FA}[\x{1F1E6}\x{1F1EC}\x{1F1F2}\x{1F1F3}\x{1F1F8}\x{1F1FE}\x{1F1FF}]|\x{1F1F9}[\x{1F1E6}\x{1F1E8}\x{1F1E9}\x{1F1EB}-\x{1F1ED}\x{1F1EF}-\x{1F1F4}\x{1F1F7}\x{1F1F9}\x{1F1FB}\x{1F1FC}\x{1F1FF}]|\x{1F1F8}[\x{1F1E6}-\x{1F1EA}\x{1F1EC}-\x{1F1F4}\x{1F1F7}-\x{1F1F9}\x{1F1FB}\x{1F1FD}-\x{1F1FF}]|\x{1F1F7}[\x{1F1EA}\x{1F1F4}\x{1F1F8}\x{1F1FA}\x{1F1FC}]|\x{1F1F5}[\x{1F1E6}\x{1F1EA}-\x{1F1ED}\x{1F1F0}-\x{1F1F3}\x{1F1F7}-\x{1F1F9}\x{1F1FC}\x{1F1FE}]|\x{1F1F3}[\x{1F1E6}\x{1F1E8}\x{1F1EA}-\x{1F1EC}\x{1F1EE}\x{1F1F1}\x{1F1F4}\x{1F1F5}\x{1F1F7}\x{1F1FA}\x{1F1FF}]|\x{1F1F2}[\x{1F1E6}\x{1F1E8}-\x{1F1ED}\x{1F1F0}-\x{1F1FF}]|\x{1F1F1}[\x{1F1E6}-\x{1F1E8}\x{1F1EE}\x{1F1F0}\x{1F1F7}-\x{1F1FB}\x{1F1FE}]|\x{1F1F0}[\x{1F1EA}\x{1F1EC}-\x{1F1EE}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F7}\x{1F1FC}\x{1F1FE}\x{1F1FF}]|\x{1F1EF}[\x{1F1EA}\x{1F1F2}\x{1F1F4}\x{1F1F5}]|\x{1F1EE}[\x{1F1E8}-\x{1F1EA}\x{1F1F1}-\x{1F1F4}\x{1F1F6}-\x{1F1F9}]|\x{1F1ED}[\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F7}\x{1F1F9}\x{1F1FA}]|\x{1F1EC}[\x{1F1E6}\x{1F1E7}\x{1F1E9}-\x{1F1EE}\x{1F1F1}-\x{1F1F3}\x{1F1F5}-\x{1F1FA}\x{1F1FC}\x{1F1FE}]|\x{1F1EB}[\x{1F1EE}-\x{1F1F0}\x{1F1F2}\x{1F1F4}\x{1F1F7}]|\x{1F1EA}[\x{1F1E6}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1ED}\x{1F1F7}-\x{1F1FA}]|\x{1F1E9}[\x{1F1EA}\x{1F1EC}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F4}\x{1F1FF}]|\x{1F1E8}[\x{1F1E6}\x{1F1E8}\x{1F1E9}\x{1F1EB}-\x{1F1EE}\x{1F1F0}-\x{1F1F5}\x{1F1F7}\x{1F1FA}-\x{1F1FF}]|\x{1F1E7}[\x{1F1E6}\x{1F1E7}\x{1F1E9}-\x{1F1EF}\x{1F1F1}-\x{1F1F4}\x{1F1F6}-\x{1F1F9}\x{1F1FB}\x{1F1FC}\x{1F1FE}\x{1F1FF}]|\x{1F1E6}[\x{1F1E8}-\x{1F1EC}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F4}\x{1F1F6}-\x{1F1FA}\x{1F1FC}\x{1F1FD}\x{1F1FF}]|[#\*0-9]\x{FE0F}?\x{20E3}|\x{1F93C}[\x{1F3FB}-\x{1F3FF}]|\x{2764}\x{FE0F}?|[\x{1F3C3}\x{1F3C4}\x{1F3CA}\x{1F46E}\x{1F470}\x{1F471}\x{1F473}\x{1F477}\x{1F481}\x{1F482}\x{1F486}\x{1F487}\x{1F645}-\x{1F647}\x{1F64B}\x{1F64D}\x{1F64E}\x{1F6A3}\x{1F6B4}-\x{1F6B6}\x{1F926}\x{1F935}\x{1F937}-\x{1F939}\x{1F93D}\x{1F93E}\x{1F9B8}\x{1F9B9}\x{1F9CD}-\x{1F9CF}\x{1F9D4}\x{1F9D6}-\x{1F9DD}][\x{1F3FB}-\x{1F3FF}]|[\x{26F9}\x{1F3CB}\x{1F3CC}\x{1F575}][\x{FE0F}\x{1F3FB}-\x{1F3FF}]?|\x{1F3F4}|[\x{270A}\x{270B}\x{1F385}\x{1F3C2}\x{1F3C7}\x{1F442}\x{1F443}\x{1F446}-\x{1F450}\x{1F466}\x{1F467}\x{1F46B}-\x{1F46D}\x{1F472}\x{1F474}-\x{1F476}\x{1F478}\x{1F47C}\x{1F483}\x{1F485}\x{1F48F}\x{1F491}\x{1F4AA}\x{1F57A}\x{1F595}\x{1F596}\x{1F64C}\x{1F64F}\x{1F6C0}\x{1F6CC}\x{1F90C}\x{1F90F}\x{1F918}-\x{1F91F}\x{1F930}-\x{1F934}\x{1F936}\x{1F977}\x{1F9B5}\x{1F9B6}\x{1F9BB}\x{1F9D2}\x{1F9D3}\x{1F9D5}\x{1FAC3}-\x{1FAC5}\x{1FAF0}\x{1FAF2}-\x{1FAF6}][\x{1F3FB}-\x{1F3FF}]|[\x{261D}\x{270C}\x{270D}\x{1F574}\x{1F590}][\x{FE0F}\x{1F3FB}-\x{1F3FF}]|[\x{261D}\x{270A}-\x{270D}\x{1F385}\x{1F3C2}\x{1F3C7}\x{1F408}\x{1F415}\x{1F43B}\x{1F442}\x{1F443}\x{1F446}-\x{1F450}\x{1F466}\x{1F467}\x{1F46B}-\x{1F46D}\x{1F472}\x{1F474}-\x{1F476}\x{1F478}\x{1F47C}\x{1F483}\x{1F485}\x{1F48F}\x{1F491}\x{1F4AA}\x{1F574}\x{1F57A}\x{1F590}\x{1F595}\x{1F596}\x{1F62E}\x{1F635}\x{1F636}\x{1F64C}\x{1F64F}\x{1F6C0}\x{1F6CC}\x{1F90C}\x{1F90F}\x{1F918}-\x{1F91F}\x{1F930}-\x{1F934}\x{1F936}\x{1F93C}\x{1F977}\x{1F9B5}\x{1F9B6}\x{1F9BB}\x{1F9D2}\x{1F9D3}\x{1F9D5}\x{1FAC3}-\x{1FAC5}\x{1FAF0}\x{1FAF2}-\x{1FAF6}]|[\x{1F3C3}\x{1F3C4}\x{1F3CA}\x{1F46E}\x{1F470}\x{1F471}\x{1F473}\x{1F477}\x{1F481}\x{1F482}\x{1F486}\x{1F487}\x{1F645}-\x{1F647}\x{1F64B}\x{1F64D}\x{1F64E}\x{1F6A3}\x{1F6B4}-\x{1F6B6}\x{1F926}\x{1F935}\x{1F937}-\x{1F939}\x{1F93D}\x{1F93E}\x{1F9B8}\x{1F9B9}\x{1F9CD}-\x{1F9CF}\x{1F9D4}\x{1F9D6}-\x{1F9DD}]|[\x{1F46F}\x{1F9DE}\x{1F9DF}]|[\xA9\xAE\x{203C}\x{2049}\x{2122}\x{2139}\x{2194}-\x{2199}\x{21A9}\x{21AA}\x{231A}\x{231B}\x{2328}\x{23CF}\x{23ED}-\x{23EF}\x{23F1}\x{23F2}\x{23F8}-\x{23FA}\x{24C2}\x{25AA}\x{25AB}\x{25B6}\x{25C0}\x{25FB}\x{25FC}\x{25FE}\x{2600}-\x{2604}\x{260E}\x{2611}\x{2614}\x{2615}\x{2618}\x{2620}\x{2622}\x{2623}\x{2626}\x{262A}\x{262E}\x{262F}\x{2638}-\x{263A}\x{2640}\x{2642}\x{2648}-\x{2653}\x{265F}\x{2660}\x{2663}\x{2665}\x{2666}\x{2668}\x{267B}\x{267E}\x{267F}\x{2692}\x{2694}-\x{2697}\x{2699}\x{269B}\x{269C}\x{26A0}\x{26A7}\x{26AA}\x{26B0}\x{26B1}\x{26BD}\x{26BE}\x{26C4}\x{26C8}\x{26CF}\x{26D1}\x{26D3}\x{26E9}\x{26F0}-\x{26F5}\x{26F7}\x{26F8}\x{26FA}\x{2702}\x{2708}\x{2709}\x{270F}\x{2712}\x{2714}\x{2716}\x{271D}\x{2721}\x{2733}\x{2734}\x{2744}\x{2747}\x{2763}\x{27A1}\x{2934}\x{2935}\x{2B05}-\x{2B07}\x{2B1B}\x{2B1C}\x{2B55}\x{3030}\x{303D}\x{3297}\x{3299}\x{1F004}\x{1F170}\x{1F171}\x{1F17E}\x{1F17F}\x{1F202}\x{1F237}\x{1F321}\x{1F324}-\x{1F32C}\x{1F336}\x{1F37D}\x{1F396}\x{1F397}\x{1F399}-\x{1F39B}\x{1F39E}\x{1F39F}\x{1F3CD}\x{1F3CE}\x{1F3D4}-\x{1F3DF}\x{1F3F5}\x{1F3F7}\x{1F43F}\x{1F4FD}\x{1F549}\x{1F54A}\x{1F56F}\x{1F570}\x{1F573}\x{1F576}-\x{1F579}\x{1F587}\x{1F58A}-\x{1F58D}\x{1F5A5}\x{1F5A8}\x{1F5B1}\x{1F5B2}\x{1F5BC}\x{1F5C2}-\x{1F5C4}\x{1F5D1}-\x{1F5D3}\x{1F5DC}-\x{1F5DE}\x{1F5E1}\x{1F5E3}\x{1F5E8}\x{1F5EF}\x{1F5F3}\x{1F5FA}\x{1F6CB}\x{1F6CD}-\x{1F6CF}\x{1F6E0}-\x{1F6E5}\x{1F6E9}\x{1F6F0}\x{1F6F3}]|[\x{23E9}-\x{23EC}\x{23F0}\x{23F3}\x{25FD}\x{2693}\x{26A1}\x{26AB}\x{26C5}\x{26CE}\x{26D4}\x{26EA}\x{26FD}\x{2705}\x{2728}\x{274C}\x{274E}\x{2753}-\x{2755}\x{2757}\x{2795}-\x{2797}\x{27B0}\x{27BF}\x{2B50}\x{1F0CF}\x{1F18E}\x{1F191}-\x{1F19A}\x{1F201}\x{1F21A}\x{1F22F}\x{1F232}-\x{1F236}\x{1F238}-\x{1F23A}\x{1F250}\x{1F251}\x{1F300}-\x{1F320}\x{1F32D}-\x{1F335}\x{1F337}-\x{1F37C}\x{1F37E}-\x{1F384}\x{1F386}-\x{1F393}\x{1F3A0}-\x{1F3C1}\x{1F3C5}\x{1F3C6}\x{1F3C8}\x{1F3C9}\x{1F3CF}-\x{1F3D3}\x{1F3E0}-\x{1F3F0}\x{1F3F8}-\x{1F407}\x{1F409}-\x{1F414}\x{1F416}-\x{1F43A}\x{1F43C}-\x{1F43E}\x{1F440}\x{1F444}\x{1F445}\x{1F451}-\x{1F465}\x{1F46A}\x{1F479}-\x{1F47B}\x{1F47D}-\x{1F480}\x{1F484}\x{1F488}-\x{1F48E}\x{1F490}\x{1F492}-\x{1F4A9}\x{1F4AB}-\x{1F4FC}\x{1F4FF}-\x{1F53D}\x{1F54B}-\x{1F54E}\x{1F550}-\x{1F567}\x{1F5A4}\x{1F5FB}-\x{1F62D}\x{1F62F}-\x{1F634}\x{1F637}-\x{1F644}\x{1F648}-\x{1F64A}\x{1F680}-\x{1F6A2}\x{1F6A4}-\x{1F6B3}\x{1F6B7}-\x{1F6BF}\x{1F6C1}-\x{1F6C5}\x{1F6D0}-\x{1F6D2}\x{1F6D5}-\x{1F6D7}\x{1F6DD}-\x{1F6DF}\x{1F6EB}\x{1F6EC}\x{1F6F4}-\x{1F6FC}\x{1F7E0}-\x{1F7EB}\x{1F7F0}\x{1F90D}\x{1F90E}\x{1F910}-\x{1F917}\x{1F920}-\x{1F925}\x{1F927}-\x{1F92F}\x{1F93A}\x{1F93F}-\x{1F945}\x{1F947}-\x{1F976}\x{1F978}-\x{1F9B4}\x{1F9B7}\x{1F9BA}\x{1F9BC}-\x{1F9CC}\x{1F9D0}\x{1F9E0}-\x{1F9FF}\x{1FA70}-\x{1FA74}\x{1FA78}-\x{1FA7C}\x{1FA80}-\x{1FA86}\x{1FA90}-\x{1FAAC}\x{1FAB0}-\x{1FABA}\x{1FAC0}-\x{1FAC2}\x{1FAD0}-\x{1FAD9}\x{1FAE0}-\x{1FAE7}]/u', '', $text);
        $text = preg_replace('/\s+/', '', $text);
        return $text;
    }    

    public static function buscaModelo($tipo='Mobile'){
        $modelos=array(
            'Mobile' => array('android' => 'Android', 'iphone' => 'iPhone', 'ipod' => 'iPod', 'ipad' => 'iPad',  'blackberry' => 'BlackBerry', 'webos' => 'Mobile'),
            'Sistema' => array('linux' =>  'Linux', 'windows nt 10.0' => 'Windows 10','windows nt 6.2' => 'Windows 8','windows nt 6.1' =>  'Windows 7','windows nt 6.0' =>  'Windows Vista','windows nt 5.2' =>  'Windows Server 2003/XP x64','windows nt 5.1' =>  'Windows XP','windows xp' =>  'Windows XP','windows nt 5.0' =>  'Windows 2000','windows me' =>  'Windows ME','win98' =>  'Windows 98','win95' =>  'Windows 95','win16' =>  'Windows 3.11','macintosh|mac os x' =>  'Mac OS X','mac_powerpc' =>  'Mac OS 9','ubuntu' =>  'Ubuntu','iPhone' =>  'iPhone','ipod' =>  'iPod','ipad' =>  'iPad','android' =>  'Android','blackberry' =>  'BlackBerry','webos' =>  'Mobile','windows' => 'Windows','iPhone' =>  'iPhone'),
            'Navegador' => array('chrome' =>  'Chrome','msie' =>  'Internet Explorer','firefox' =>  'Firefox','safari' =>  'Safari','opera' =>  'Opera','netscape' =>  'Netscape','maxthon' =>  'Maxthon','konqueror' =>  'Konqueror','mobile' =>  'Handheld Browser','Mac OS' =>  'Mac OS','Android' =>  'Android','chrome'=>'chrome')
        );
        if(!empty($modelos[$tipo])){ if(isset($_SERVER['HTTP_USER_AGENT']) and !empty($_SERVER['HTTP_USER_AGENT'])) {
            foreach($modelos[$tipo] as $busca => $nome) {
                if(strpos(preg_replace('/[^A-Za-z0-9\-]/', ' ',strtolower($_SERVER['HTTP_USER_AGENT'])),strtolower($busca)) !== false ){ return $nome; }
                if(preg_match(strtolower('/(Mobile|Android|Tablet|GoBrowser|[0-9]x[0-9]*|uZardWeb\/|Mini|Doris\/|Skyfire\/|iPhone|Fennec\/|Maemo|Iris\/|CLDC\-|Mobi\/)/i'),strtolower($_SERVER['HTTP_USER_AGENT']))){
                    return 'MobileDevice';
                };
            }
        } }
        return false;
    }
    public static function dadosHardware($tipo='Mobile'){
        $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
        $IP = GlobalFuncoes::getIp();
        if($REMOTE_ADDR=="127.0.0.1"||$IP=="127.0.0.1") {
            exec('ipconfig | findstr /R /C:"IPv4.*"', $output); //exec('ipconfig | findstr /R /C:"IPv6.*"', $IPv6)
            preg_match('/\d+\.\d+\.\d+\.\d+/', $output[0], $matches);
            if($IP=="127.0.0.1") { $IP=$matches[0]; }
            if($REMOTE_ADDR=="127.0.0.1") { $REMOTE_ADDR=$matches[0]; }
        }
        return array(
            "REMOTE_ADDR"=>$REMOTE_ADDR,
            'IP' => $IP,
            'Mobile' => GlobalFuncoes::buscaModelo('Mobile'),
            'Sistema' => GlobalFuncoes::buscaModelo('Sistema'),
            'Navegador' => GlobalFuncoes::buscaModelo('Navegador')
        );
    }
    public static function get_dpi($filename){
        $a = fopen($filename,'r');
        $string = fread($a,20);
        fclose($a);

        $data = bin2hex(substr($string,14,4));
        $x = substr($data,0,4);
        $y = substr($data,4,4);

        return array(hexdec($x),hexdec($y));
    }
    public static function getIp() {

        // Check for shared Internet/ISP IP
        if (!empty($_SERVER['HTTP_CLIENT_IP']) && validate_ip($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }

        // Check for IP addresses passing through proxies
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {

            // Check if multiple IP addresses exist in var
            if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
                $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                foreach ($iplist as $ip) {
                    if (validate_ip($ip))
                        return $ip;
                }
            }
            else {
                if (validate_ip($_SERVER['HTTP_X_FORWARDED_FOR']))
                    return $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED']) && validate_ip($_SERVER['HTTP_X_FORWARDED']))
            return $_SERVER['HTTP_X_FORWARDED'];
        if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && validate_ip($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
            return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && validate_ip($_SERVER['HTTP_FORWARDED_FOR']))
            return $_SERVER['HTTP_FORWARDED_FOR'];
        if (!empty($_SERVER['HTTP_FORWARDED']) && validate_ip($_SERVER['HTTP_FORWARDED']))
            return $_SERVER['HTTP_FORWARDED'];

        // Return unreliable IP address since all else failed
        return $_SERVER['REMOTE_ADDR'];
    }
    public static function validate_ip($ip) {

        if (strtolower($ip) === 'unknown')
            return false;

        // Generate IPv4 network address
        $ip = ip2long($ip);

        // If the IP address is set and not equivalent to 255.255.255.255
        if ($ip !== false && $ip !== -1) {
            // Make sure to get unsigned long representation of IP address
            // due to discrepancies between 32 and 64 bit OSes and
            // signed numbers (ints default to signed in PHP)
            $ip = sprintf('%u', $ip);

            // Do private network range checking
            if ($ip >= 0 && $ip <= 50331647)
                return false;
            if ($ip >= 167772160 && $ip <= 184549375)
                return false;
            if ($ip >= 2130706432 && $ip <= 2147483647)
                return false;
            if ($ip >= 2851995648 && $ip <= 2852061183)
                return false;
            if ($ip >= 2886729728 && $ip <= 2887778303)
                return false;
            if ($ip >= 3221225984 && $ip <= 3221226239)
                return false;
            if ($ip >= 3232235520 && $ip <= 3232301055)
                return false;
            if ($ip >= 4294967040)
                return false;
        }
        return true;
    }
    public static function checkURL($url, array $options = array()) {
        if (empty($url)) {
            throw new Exception('URL is empty');
        }
        $httpStatusCodes = array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status',
            208 => 'Already Reported',
            226 => 'IM Used',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => 'Switch Proxy',
            307 => 'Temporary Redirect',
            308 => 'Permanent Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Payload Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            418 => 'I\'m a teapot',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            425 => 'Unordered Collection',
            426 => 'Upgrade Required',
            428 => 'Precondition Required',
            429 => 'Too Many Requests',
            431 => 'Request Header Fields Too Large',
            449 => 'Retry With',
            450 => 'Blocked by Windows Parental Controls',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            508 => 'Loop Detected',
            509 => 'Bandwidth Limit Exceeded',
            510 => 'Not Extended',
            511 => 'Network Authentication Required',
            599 => 'Network Connect Timeout Error'
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        if (isset($options['timeout'])) {
            $timeout = (int) $options['timeout'];
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        }

        curl_exec($ch);
        $returnedStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //dd($url, 'returnedStatusCode: '.$returnedStatusCode, var_dump( ($returnedStatusCode >= 200 && $returnedStatusCode <= 301) ? true : false ) );
        return ($returnedStatusCode == 200 ) ? true : false;
        //return ($returnedStatusCode >= 200 && $returnedStatusCode <= 301) ? true : false; dd('returnedStatusCode', $returnedStatusCode );
        curl_close($ch);
        /*
        if (array_key_exists($returnedStatusCode, $httpStatusCodes)) {
            return "URL: '{$url}' - Error code: {$returnedStatusCode} - Definition: {$httpStatusCodes[$returnedStatusCode]}";
        } else {
            return "'{$url}' does not exist";
        }
        */
    }

    public static function consultarCep($cep) {
        $cep = preg_replace('/[^0-9]/', '', $cep);
        $url = "https://viacep.com.br/ws/{$cep}/json/";
        $response = @file_get_contents($url);
        if ($response === FALSE) {
            return "Erro ao consultar o CEP";
        }
        $endereco = json_decode($response, true);
        if (isset($endereco['erro']) && $endereco['erro'] === true) {
            return "CEP inválido";
        }
        return $endereco;
    }
    public static function consultarEstados() {
        $conteudo = [];
        $arquivoIbge = app_path("/Exceptions/ibge_" . date("Y") . ".json");
        if (file_exists($arquivoIbge)) {
            $conteudo = json_decode(file_get_contents($arquivoIbge), true);
        }
        if (!empty($conteudo['estados'])) {return $conteudo['estados']; }

        $url = "https://servicodados.ibge.gov.br/api/v1/localidades/estados";
        $response = @file_get_contents($url);
        if ($response === false) {
            return ["error" => "Erro ao consultar estados"];
        }
    
        $conteudo['estados'] = json_decode($response, true);
        file_put_contents($arquivoIbge, json_encode($conteudo));
        return $conteudo['estados'];
    }
    
    
    public static function consultarCidades() {
        $conteudo = [];
        $arquivoIbge = app_path("/Exceptions/ibge_" . date("Y") . ".json");
        if (file_exists($arquivoIbge)) {
            $conteudo = json_decode(file_get_contents($arquivoIbge), true);
        }    
        if (!empty($conteudo['cidades'])) { return $conteudo['cidades']; }
    
        $url = "https://servicodados.ibge.gov.br/api/v1/localidades/municipios";
        $response = @file_get_contents($url);
        if ($response === false) {
            return ["error" => "Erro ao consultar cidades"];
        }
    
        $conteudo['cidades'] = json_decode($response, true);
        file_put_contents($arquivoIbge, json_encode($conteudo));
        return $conteudo['cidades'];
    }

    
    public static function consultarCidadeEstado($estadoId) {
        if (empty($estadoId)) { return []; }
        $url = "https://servicodados.ibge.gov.br/api/v1/localidades/estados/{$estadoId}/municipios";
        $response = @file_get_contents($url);
        if ($response === false) {
            return ["error" => "Erro ao consultar cidades para o estado {$estadoId}"];
        }
        return json_decode($response, true);
    }
 
    

    /*
    public static function validaUrl($url){
        if(!$url || !is_string($url)){ return false; }
        if( ! preg_match('/^http(s)?:\/\/[a-z0-9-]+(\.[a-z0-9-]+)*(:[0-9]+)?(\/.*)?$/i', $url) ){ return false; }
        if(GlobalFuncoes::getHttpResponseCode_using_curl($url) != 200){ //      if(getHttpResponseCode_using_getheaders($url) != 200){  // use this one if you cant use curl
            return false;
        }
        return true;
    }

    public static function getHttpResponseCode_using_curl($url, $followredirects = true){
        if(! $url || ! is_string($url)){ return false; }
        $ch = @curl_init($url);
        if($ch === false){ return false; }
        @curl_setopt($ch, CURLOPT_HEADER         ,true);    // we want headers
        @curl_setopt($ch, CURLOPT_NOBODY         ,true);    // dont need body
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER ,true);    // catch output (do NOT print!)
        if($followredirects){
            @curl_setopt($ch, CURLOPT_FOLLOWLOCATION ,true);
            @curl_setopt($ch, CURLOPT_MAXREDIRS      ,10);  // fairly random number, but could prevent unwanted endless redirects with followlocation=true
        }else{
            @curl_setopt($ch, CURLOPT_FOLLOWLOCATION ,false);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        @curl_exec($ch);
        $code = @curl_getinfo($ch, CURLINFO_HTTP_CODE);
        dd('code', $code );
        if(@curl_errno($ch)){   // should be 0
            dd('curl_errno ',$ch  );
            @curl_close($ch);
            return false;
        }
         // note: php.net documentation shows this returns a string, but really it returns an int
        @curl_close($ch);
        return $code;
    }

    public static function getHttpResponseCode_using_getheaders($url, $followredirects = true){
        if(! $url || ! is_string($url)){ return false; }
        $headers = @get_headers($url);
        if($headers && is_array($headers)){
            if($followredirects){ $headers = array_reverse($headers); }
            foreach($headers as $hline){
                */
                //if(preg_match('/^HTTP\/\S+\s+([1-9][0-9][0-9])\s+.*/', $hline, $matches) ){ $code = $matches[1]; return $code; }
                /*
            }
            return false;
        }
        return false;
    }
    public static function validaUrl_($Url=''){
        if (empty($Url)){ return false; }
        $headers = @get_headers($Url);
        $ch = curl_init($Url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        $data = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        //dd($Url, 'headers: '.$headers, 'httpcode: '.$httpcode, var_dump( ($httpcode >= 200 && $httpcode <= 301) ? true : false ) );
        return ($httpcode >= 200 && $httpcode <= 301) ? true : false;
    }
    public static function get_ip_address(){
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
            if (array_key_exists($key, $_SERVER) === true){
                foreach (explode(',', $_SERVER[$key]) as $ip){
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                        return $ip;
                    }
                }
            }
        }
    }
    */
    /*
    public static function salvaImagem($image=null, $destino=null, $modelObjeto=null, $campo='',$antinga="",$reduzir="NAO") {
        $retorno = array(); if(!empty($image)) {
            if ($image->getClientOriginalName()) {
                $name = time() . rand() . '.' . $image->getClientOriginalExtension(); $image->move($destino, $name);
                if (file_exists($destino . $name)) {
                    $retorno['name']=$name;
                } else { $retorno['msg']['error']='Falha ao atualizar a Imagem...'; }

            }
        }
        return $retorno;
    }
    public static function Documento($Documento="",$dados1=array(),$dados2=array()) {
        $retorno = "";
        if(!empty($Documento)){

            $Documento = Documento::where('documento',$Documento)->first();
            if(!empty($Documento->conteudo)){ $retorno =$Documento->conteudo; }
            if(gettype($dados1)=="object") { $dados1 = $dados1->toArray(); }
            if(gettype($dados2)=="object") { $dados2 = $dados2->toArray(); }
            if(gettype($dados1)=="array") { //if(Auth::user()->id==1){ dd("xxx",gettype($dados1),$dados1); }
                foreach ($dados1 as $campo => $valor) { $retorno = str_replace('<'.strtolower($campo).'>',$valor,$retorno); }
            }//else{if(Auth::user()->id==1){dd('aqui1');}}
            if(gettype($dados2)=="array") {
                foreach ($dados2 as $campo => $valor) { $retorno = str_replace('<'.strtolower($campo).'>',$valor,$retorno); }
            }//else{if(Auth::user()->id==1){dd('aqui2');}}

        }
        return $retorno;
    }
    public static function enviaEmail($dados=array()) {
        if(!empty($dados['template'])&&!empty($dados['emailDestino'])&&!empty($dados['nomeDestino'])) {

            Mail::send(['html' => 'templates.emails.'.$dados['template'] ], array('contactusbody' =>$dados['conteudo']), function($message) use ($dados) {
                $message->to($dados['emailDestino'],  $dados['nomeDestino']);
                $message->from($dados['emailOrigem'], $dados['nomeOrigem']);
                $message->bcc('comercial@assineapp.com.br','Sistema');
                $message->subject($dados['titulo']);
            });

        }
    }
    public static function valor_por_extenso( $v ){
        $GlobalFuncoes = new GlobalFuncoes();
        $v = $GlobalFuncoes->numero($v,0);

        $v = filter_var($v, FILTER_SANITIZE_NUMBER_INT);

            $sin = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
            $plu = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões","quatrilhões");

            $c = array("", "cem", "duzentos", "trezentos", "quatrocentos","quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
            $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta","sessenta", "setenta", "oitenta", "noventa");
            $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze","dezesseis", "dezesete", "dezoito", "dezenove");
            $u = array("", "um", "dois", "três", "quatro", "cinco", "seis","sete", "oito", "nove");

            $z = 0;

            $v = number_format( $v, 2, ".", "." );
            $int = explode( ".", $v );

            for ( $i = 0; $i < count( $int ); $i++ )
            {
                for ( $ii = mb_strlen( $int[$i] ); $ii < 3; $ii++ )
                {
                    $int[$i] = "0" . $int[$i];
                }
            }

            $rt = null;
            $fim = count( $int ) - ($int[count( $int ) - 1] > 0 ? 1 : 2);
            for ( $i = 0; $i < count( $int ); $i++ )
            {
                $v = $int[$i];
                $rc = (($v > 100) && ($v < 200)) ? "cento" : $c[$v[0]];
                $rd = ($v[1] < 2) ? "" : $d[$v[1]];
                $ru = ($v > 0) ? (($v[1] == 1) ? $d10[$v[2]] : $u[$v[2]]) : "";

                $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd && $ru) ? " e " : "") . $ru;
                $t = count( $int ) - 1 - $i;
                $r .= $r ? " " . ($v > 1 ? $plu[$t] : $sin[$t]) : "";
                if ( $v == "000")
                    $z++;
                elseif ( $z > 0 )
                    $z--;

                if ( ($t == 1) && ($z > 0) && ($int[0] > 0) )
                    $r .= ( ($z > 1) ? " de " : "") . $plu[$t];

                if ( $r )
                    $rt = $rt . ((($i > 0) && ($i <= $fim) && ($int[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
            }
            $rt = mb_substr( $rt, 1 );
            return($rt ? trim( $rt ) : "zero");
    }


    public static function filtroRetorno(
            $root = 'Clientes', $retorno=array(), $request=null,
            $filtro=array('Estado'=>'estado','Cidade'=>'cidade','Atendimento'=>'user_id')
        ) {
        if(!empty($retorno[$root])){ foreach ($filtro as $nome => $campo) { if(!empty($request->$nome)){
            $retorno[$nome]= $request->$nome; $retorno[$root]=$retorno[$root]->where($campo,$retorno[$nome]);
        } } }
        return $retorno;
    }
    public static function grupoModel($Model=null,$campo='') {
        if(!empty($Model)&&!empty($campo)) {
            $Model = $Model->groupBy($campo)->selectRaw('count(*) as total, '.$campo)->get();
        }
        return $Model;
    }
    public static function listaEmailMKT($Cliente='',$tipo='',$status='') {
        if(!empty($Cliente)){
            if(!empty($tipo)){
                return EmailMKT::where('cliente_id',$Cliente->id)->where('documento', $tipo)->orderBy('created_at','desc')->get();
            }
            return EmailMKT::where('cliente_id',$Cliente->id)->orderBy('created_at','desc')->get();
        }else{
            $retorno = array('APRESENTACAO');
            if($status=="APLICATIVO"||$status=="PROPOSTA") { $retorno[] = 'AGRADECIMENTO'; }
            return $retorno;
        }
    }
    public static function enviaEmailMKT(
        $Cliente='',
        $emailDestino='comercial@assineapp.com.br',
        $nomeDestino='APRESENTACAO',
        $tipo=''
        ) {
        //$retorno=array('APRESENTACAO','ESPERAR_APP');
        if(!empty($Cliente)&&!empty($tipo)){
            $chave=bcrypt($tipo.'_'.$Cliente->id);
            $dados = array(
                'template'=>"emailMKT",
                'conteudo'=>GlobalFuncoes::Documento($tipo,array(
                    'cliente' => $Cliente->cliente, 'chave' => $chave
                )),
                'titulo'=>'APLICATIVO - '.$Cliente->cliente, //$tipo.
                'emailDestino'=>$emailDestino,//'comercial@assineapp.com.br',//'comercial@assineapp.com.br',//$request->emailEmailMKT,//
                'nomeDestino'=>$nomeDestino,//$request->contatoEmailMKT,//$this->retorno['Cliente']->contato,
                'emailOrigem'=>"comercial@assineapp.com.br",
                'nomeOrigem'=>"AssineAPP"
            );
            GlobalFuncoes::enviaEmail($dados);
            $retorno = EmailMKT::create([
                'cliente_id' =>  $Cliente->id,'user_id' =>  Auth::user()->id, 'documento' => $tipo,
                'contato' =>  $nomeDestino,'email' =>  $emailDestino,
                'chave' => $chave,
                'contador' => 0, 'dt_envio' => date("Y-m-d H:i"),
                'obs' =>  array(), 'status' =>  'Novo'
            ]);
            return $retorno;
        }
    }
    */
}

$GlobalFuncoes = new GlobalFuncoes();

