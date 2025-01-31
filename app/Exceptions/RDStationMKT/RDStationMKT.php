<?php
namespace App\Exceptions\RDStationMKT; //https://developers.rdstation.com/pt-BR/overview
//https://crmsupport.rdstation.com.br/hc/pt-br/articles/360018747911-Integra%C3%A7%C3%B5es-via-API-com-outras-plataformas
use Illuminate\Support\Facades\Auth;

use GlobalFuncoes;

use App\Models\Sistema\Conta;
use App\Models\Sistema\Lead;

use App\Models\Sistema\LogURL;

class RDStationMKT
{
    public static function salvaLog($acao='RDStationMKT',$Conta=null,$Lead=null,$msg='',$dados=array()){
        if(!empty($dados)){ $dados=json_decode(json_encode($dados),true); }
        LogURL::novoLog($acao,$Conta,$Lead,$msg,$dados);
    }
    public static function trataErros($Conta=null,$errors=[],$debug=false) {
        if(!empty($errors['errors'])){ $errors=$errors['errors']; }
        //if(!empty($errors['error'])){ $errors=$errors['error']; }
        $salva="NAO";
        if($debug==true){
            //dd("trataErros",$errors);
            //"error":"invalid_token","error_description":"The access token is invalid or has expired"
        }
        foreach ($errors as $error => $info) {
            
            if($error=="invalid_token"||$info=="invalid_token"){ $refreshToken = self::refreshToken(['Conta'=>$Conta],null,$debug); $salva="SIM"; }else{
            
                if(empty($error)){$error=$info;} //"error_description":"The access token is invalid or has expired","error":"invalid_token"
                $error_type = $error;
                if(!empty($error['error'])){
                    if($error['error']=="invalid_token"){ $error_type = $error['error']; $refreshToken = self::refreshToken(['Conta'=>$Conta],null,$debug); $salva="SIM"; }
                    if(empty($error_type)&&strpos($error['error'], 'limit') !== false){ $error_type = "identity_limiter"; $salva="NAO"; sleep(10); }
                }            
                if(!empty($error['error_type'])){ $error_type = $error['error_type']; }
                if($info=="The access token is invalid or has expired"){ $error_type="invalid_token"; $refreshToken = self::refreshToken(['Conta'=>$Conta],null,$debug); $salva="SIM"; }
                
                if($debug==true){
                    //dd("trataErros",$error_type,$error,$info,$errors);
                }            
    
                if($error_type=="EXPIRED_CODE_GRANT"){ $Conta->config->APP->code=""; $salva="SIM"; }
                if(in_array($error_type,["UNAUTHORIZED","invalid_token","The access token is invalid or has expired"])){
                    $refreshToken = self::refreshToken(['Conta'=>$Conta],null,$debug); $salva="SIM";
                    if($debug==true){
                        dd($refreshToken,"trataErros refreshToken",$error_type,$error,$info,$errors);
                    }                  
                }
                if($error_type=="RESOURCE_NOT_FOUND"){ return false; }
                if($error_type=="TOO_SHORT"){  }
                if($salva=="NAO"){
                    $caminho = storage_path('app/RDStationMKT/'); GlobalFuncoes::criarPasta($caminho);
                    GlobalFuncoes::criarPasta(storage_path().'/logs/trataErros/');

                    if(!empty($Conta)){
                        file_put_contents(storage_path().'/logs/trataErros/rdstation_'.preg_replace('/[^a-zA-Z]/s','',$error_type).'.json',json_encode([
                            "error_type"=>$error_type,"conta_id"=>$Conta->id ??"","sigla"=>$Conta->sigla ??"","status"=>$Conta->status ??"","error"=>$error,"errors"=>$errors
                        ]));
                        if(!empty($Conta->sigla)){
                            $caminho = $caminho.$Conta->sigla.'/'; GlobalFuncoes::criarPasta($caminho);
                            $caminho = $caminho.'trataErros/'; GlobalFuncoes::criarPasta($caminho);
                            file_put_contents($caminho.'logs_trataErros_rdstation_'.preg_replace('/[^a-zA-Z]/s','',$error_type).'_'.date("Ymd_H_i").'.json',json_encode([
                                "error_type"=>$error_type,"conta_id"=>$Conta->id,"sigla"=>$Conta->sigla,"status"=>$Conta->status,"error"=>$error,"errors"=>$errors
                            ]));                        
                        }
                    }else{
                        file_put_contents(storage_path().'/logs/trataErros/rdstation_semconta_'.preg_replace('/[^a-zA-Z]/s','',$error_type).'.json',json_encode([
                            "error_type"=>$error_type,"error"=>$error,"errors"=>$errors
                        ]));                        
                    }
                }
            }
            
        }
        if($salva=="SIM"){ if(!empty($Conta)){if(!empty($Conta->id)){  $Conta->save(); } } return true; }else{
            self::salvaLog('trataErros',$Conta,null,'',$errors);
        }

        return false;
    }
    public static function buscarToken($retorno=null,$request=null) { if(!empty($retorno['Conta'])){
        if(!empty($retorno['Conta']->config->APP->client_id)&&!empty($retorno['Conta']->config->APP->client_secret)&&!empty($retorno['Conta']->config->APP->code)){
            $post = [
                'client_id' => $retorno['Conta']->config->APP->client_id,
                'client_secret' => $retorno['Conta']->config->APP->client_secret,
                'code' => $retorno['Conta']->config->APP->code
            ];
            $urlToken = "https://api.rd.services/auth/token";
            //$enviaPOST = GlobalFuncoes::enviaPOST($urlToken,$post); //,$retorno['Conta']
            $enviaPOST = GlobalFuncoes::enviaPOST($urlToken,$post);//,$retorno['Conta'],'POST');
            self::salvaLog('buscarToken',$retorno['Conta'],null,'',$enviaPOST);
            if(!empty($enviaGET['errors'])||!empty($enviaGET['error'])){ return self::trataErros($retorno['Conta'],$enviaPOST); }
            if(!empty($enviaPOST['access_token'])){ $retorno['Conta']->atualizaConfig($enviaPOST); return true; }
        }
        return $retorno;
    } }
    //public static function refreshToken($Conta=null) { if(!empty($Conta)){
    public static function refreshToken($retorno=null,$request=null,$debug=false) { if(!empty($retorno['Conta'])){
        //if(gettype($retorno['Conta'])=="array"){ $retorno['Conta']=json_decode(json_encode($retorno['Conta'])); }
        if(!empty($retorno['Conta']->config->APP->client_id)&&!empty($retorno['Conta']->config->APP->client_secret)&&!empty($retorno['Conta']->config->APP->refresh_token)){
            $post = [
                'client_id' => $retorno['Conta']->config->APP->client_id,
                'client_secret' => $retorno['Conta']->config->APP->client_secret,
                'refresh_token' => $retorno['Conta']->config->APP->refresh_token
            ];
            $urlToken = "https://api.rd.services/auth/token";
            //$enviaPOST = GlobalFuncoes::enviaPOST($urlToken, $post); //,$retorno['Conta']
            $enviaPOST = GlobalFuncoes::enviaPOST($urlToken,$post);//,$retorno['Conta'],'POST');
            if($debug==true){ dd("refreshToken a",$debug,$enviaPOST); }
            //if(!empty($enviaPOST['errors'])||!empty($enviaPOST['error'])){ return self::trataErros($retorno['Conta'],$enviaPOST); }
            self::salvaLog('refreshToken',$retorno['Conta'],null,'',$enviaPOST);
            if(!empty($enviaPOST['access_token'])){ $retorno['Conta']->atualizaConfig($enviaPOST); return true; }
            else{ $buscarToken=self::buscarToken($retorno['Conta']); }
        }else{
            //if($debug==true){ dd(gettype($retorno['Conta']),"refreshToken b",$debug,$retorno); }
        }
        return $retorno;
    } }
    public static function buscarEmail($retorno=null,$request=null,$email=null) {
        if(!empty($retorno['Conta'])){
            
            if(empty($email)&&!empty($request->email)){ $email=$request->email; }
            if(!empty($email)){
                $urlEmail = "https://api.rd.services/platform/contacts/email:".$email;
                $enviaGET = GlobalFuncoes::enviaGET($urlEmail,$retorno['Conta']);
                //dd($enviaGET);
                if(!empty($enviaGET['errors'])||!empty($enviaGET['error'])){ //dd('buscarEmail errors',$urlEmail,$enviaGET,$request->all(),$retorno);
                    $trataErros = self::trataErros($retorno['Conta'],$enviaGET);
                    if($trataErros === true){ return self::buscarEmail($retorno,$request,$email); }
                }else{ //dd('buscarEmail',$urlEmail,$enviaGET,$request->all(),$retorno);
                    if(!empty($enviaGET['uuid'])){ $retorno['Lead'] = Lead::where('uuid',$enviaGET['uuid'])->first();
                        if(empty($retorno['Lead'])) { $retorno['Conta']->novoLead($enviaGET); $retorno['Lead'] = Lead::where('uuid',$enviaGET['uuid'])->first(); }
                        else{ $retorno['Lead']->dados = $enviaGET; $retorno['Lead']->save(); }
                    }else{ $retorno['buscarEmail'] = $enviaGET; }
                    //self::salvaLog('buscarEmail',$retorno['Conta'],$retorno['Lead'],'',$enviaGET);
                }
            }
            
        }
        return $retorno;
    }
    public static function buscarID($retorno=null,$request=null,$uuid=null) {
        if(!empty($retorno['Conta'])){
            
            if(empty($uuid)&&!empty($request->uuid)){ $uuid=$request->uuid; }
            if(!empty($uuid)){
                $urluuid = "https://api.rd.services/platform/contacts/uuid:".$uuid;
                $enviaGET = GlobalFuncoes::enviaGET($urluuid,$retorno['Conta']);
                if(!empty($enviaGET['errors'])||!empty($enviaGET['error'])){ //dd('buscarID errors',$urluuid,$enviaGET,$request->all(),$retorno);
                    $trataErros = self::trataErros($retorno['Conta'],$enviaGET);
                    if($trataErros === true){ return self::buscarID($retorno,$request,$uuid); }
                }else{
                    $retorno['Lead'] = $enviaGET;
                }
            }
            
        }
        return $retorno;
    }    
    public static function buscarFunil($retorno=null,$request=null) {
        if(!empty($retorno['Conta'])&&!empty($request->email)){
            $funil="default"; if(!empty($request->funil)){ $funil=$request->funil; }
            $urlFunil = "https://api.rd.services/platform/contacts/email:".$request->email."/funnels/".$funil;
            $enviaGET = GlobalFuncoes::enviaGET($urlFunil,$retorno['Conta']);
            //self::salvaLog('buscarFunil',$retorno['Conta'],null,'',$enviaGET);
            if(!empty($enviaGET['errors'])||!empty($enviaGET['error'])){ $trataErros = self::trataErros($retorno['Conta'],$enviaGET);
                if($trataErros === true){ return self::buscarFunil($retorno,$request); }
            }else{ $retorno['buscarFunil']=$enviaGET; }
        }
        return $retorno;
    }
    public static function statusLead($retorno=null,$request=null) {
        if(!empty($retorno['Conta'])&&!empty($request->email)){
            $post = array(
                "lifecycle_stage" => $request->lifecycle_stage, "opportunity" => json_decode($request->opportunity), // 'Lead', 'Qualified Lead' and 'Client'.
                "contact_owner_email" => json_decode($request->contact_owner_email) //"fernando.martins@hook.app.br"//"equipe@bw8.com.br"
            );
            $funil="default"; if(!empty($request->funil)){ $funil=$request->funil; }
            $urlStatus = "https://api.rd.services/platform/contacts/email:".$request->email."/funnels/".$funil;
            $enviaPOST = GlobalFuncoes::enviaPOST($urlStatus,$post,$retorno['Conta'],'PUT');
            //self::salvaLog('statusLead',$retorno['Conta'],null,'',$enviaPOST);
            if(!empty($enviaPOST['errors'])||!empty($enviaPOST['error'])){ $trataErros = self::trataErros($retorno['Conta'],$enviaPOST);
                if($trataErros === true){ return self::statusLead($retorno,$request); }
            }else{ $retorno['statusLead']=$enviaPOST; }
        }
        return $retorno;
    }
    public static function eventoLead($retorno=null,$request=null) {
        /* categoria	Fragmento	data_processing ou communications
            tipo	Fragmento	pre_existent_contract, consent, legitimate_interest, judicial_process, vital_interestOupublic_interest
            status	Fragmento	grantedou declined (apenas quando a categoria for comunicações ) */
        if(!empty($retorno['Conta'])&&!empty($request->email)){
            $event_type="OPPORTUNITY"; if(!empty($request->event_type)){ $event_type=$request->event_type; }
            $funil="default"; if(!empty($request->funil)){ $funil=$request->funil; }
            $identifier="Evento_".date('Ymd_H-i'); if(!empty($request->identifier)){ $identifier=$request->identifier; }
            $funil="default"; if(!empty($request->funil)){ $funil=$request->funil; }

            $payload = json_decode(json_encode($retorno['lead']->dados),true);
            $payload["conversion_identifier"] = $identifier; //"email" => $request->email,
            $payload["funnel_name"] = $funil;
            $payload["tags"][] = $identifier; //legal_bases
            $post = array(
                "event_type" => $event_type,
                "event_family" => "CDP",
                "payload" => $payload
            );
            if(!empty($post['payload'])){ foreach($post['payload'] as $campo =>$valor){ if(!is_array($valor)){$post['payload'][$campo]=$valor.""; } } }
            //dd('eventoLead','conversion_identifier',$post,$request->all(),$retorno['lead']->dados);
            $urlEvento = "https://api.rd.services/platform/events";
            $enviaPOST = GlobalFuncoes::enviaPOST($urlEvento,$post,$retorno['Conta'],'POST');
            //dd('eventoLead',$identifier,$enviaPOST,$post,$urlEvento,$request->all(),$retorno['lead']->dados);
            //self::salvaLog('eventoLead',$retorno['Conta'],null,'',$enviaPOST);
            if(!empty($enviaPOST['errors'])||!empty($enviaPOST['error'])){ $trataErros = self::trataErros($retorno['Conta'],$enviaPOST);
                if($trataErros === true){ return self::eventoLead($retorno,$request); }
            }else{ $retorno['eventoLead']=$enviaPOST; }
            GlobalFuncoes::setLogContador("RDStationMKT", $retorno['Conta']->sigla, 'conversao-lead', $payload);
        }
        return $retorno;
    }
    public static function loteEnvento($Conta,$post=[]) {
        $enviaPOST = GlobalFuncoes::enviaPOST("https://api.rd.services/platform/events/batch",$post,$Conta,'POST');
        //dd('loteEnvento',$enviaPOST);
        self::salvaLog('loteEnvento',$Conta,null,'',$enviaPOST);
        if(!empty($enviaPOST['errors'])||!empty($enviaPOST['error'])){ $trataErros = self::trataErros($Conta,$enviaPOST);
            if($trataErros === true){ return self::loteEnvento($Conta,$post); }
        }
        GlobalFuncoes::setLogContador("RDStationMKT", $Conta->sigla, 'conversao-lote', $post);
        return $enviaPOST;
    }
    public static function acaoEnvento($Conta,$post=[],$tentativa=0) {
        if(!empty($post['payload'])){ if(!empty($post['payload']['email'])){
            if(empty($post['payload']["conversion_identifier"])){ $post['payload']["conversion_identifier"]="conversao";}
        } }
        if(!empty($post['payload'])){ foreach($post['payload'] as $campo =>$valor){ if(!is_array($valor)){$post['payload'][$campo]=$valor.""; } } }
        
        $enviaPOST = GlobalFuncoes::enviaPOST("https://api.rd.services/platform/events",$post,$Conta,'POST');
        if($tentativa<2){ $tentativa++;
            if(!empty($enviaPOST['errors'])||!empty($enviaPOST['error'])){ $trataErros = self::trataErros($Conta,$enviaPOST);
                if($trataErros === true){ return self::acaoEnvento($Conta,$post,$tentativa); }
            }
        }
        GlobalFuncoes::setLogContador("RDStationMKT", $Conta->sigla, 'conversao', $post);        
        return $enviaPOST;
    }

    public static function listaEmailMKT($retorno=null,$request=null) {
        if(!empty($retorno['Conta'])){
            $start_date=date("Y-m-d"); $end_date=date("Y-m-d");$campaign_id="";
            if(!empty($request)){ if(gettype($request)=="object"){ $request=json_decode(json_encode($request->all()),true); }
                if(!empty($request['start_date'])){$start_date=$request['start_date'];}
                if(!empty($request['end_date'])){$end_date=$request['end_date'];}
                if(!empty($request['campaign_id'])){$campaign_id="&campaign_id=".$request['campaign_id'];}
            }
            $enviaGET = GlobalFuncoes::enviaGET("https://api.rd.services/platform/analytics/emails?start_date=".$start_date."&end_date=".$end_date.$campaign_id,$retorno['Conta']);
            self::salvaLog('listaEmailMKT',$retorno['Conta'],null,'',$enviaGET);
            if(!empty($enviaGET['errors'])||!empty($enviaGET['error'])){ $trataErros = self::trataErros($retorno['Conta'],$enviaGET); }
            else{ $retorno['EmailMKT']=$enviaGET; }
        }
        return $retorno;
    }
    public static function listaEmailFluxo($retorno=null,$request=null) {
        if(!empty($retorno['Conta'])){
            $start_date=date("Y-m-d"); $end_date=date("Y-m-d");$workflow_id="";
            if(!empty($request)){ if(gettype($request)=="object"){ $request=json_decode(json_encode($request->all()),true); }
                if(!empty($request['start_date'])){$start_date=$request['start_date'];}
                if(!empty($request['end_date'])){$end_date=$request['end_date'];}
                if(!empty($request['workflow_id'])){$workflow_id="&workflow_id=".$request['workflow_id'];}
            }
            $enviaGET = GlobalFuncoes::enviaGET("https://api.rd.services/platform/analytics/workflow_emails?start_date=".$start_date."&end_date=".$end_date.$workflow_id,$retorno['Conta']);
            self::salvaLog('listaEmailFluxo',$retorno['Conta'],null,'',$enviaGET);
            if(!empty($enviaGET['errors'])||!empty($enviaGET['error'])){ $trataErros = self::trataErros($retorno['Conta'],$enviaGET); }
            else{ $retorno['EmailFluxo']=$enviaGET; }
        }
        return $retorno;
    }  
    public static function listaFunilVendas($retorno=null,$request=null) {
        if(!empty($retorno['Conta'])){
            $start_date=date("Y-m-d"); $end_date=date("Y-m-d");$grouped_by="";
            if(!empty($request)){ if(gettype($request)=="object"){ $request=json_decode(json_encode($request->all()),true); }
                if(!empty($request['start_date'])){$start_date=$request['start_date'];}
                if(!empty($request['end_date'])){$end_date=$request['end_date'];}
                if(!empty($request['grouped_by'])){$grouped_by="&grouped_by=".$request['grouped_by'];}
            }
            $enviaGET = GlobalFuncoes::enviaGET("https://api.rd.services/platform/analytics/funnel?start_date=".$start_date."&end_date=".$end_date.$grouped_by,$retorno['Conta']);
            self::salvaLog('listaFunilVendas',$retorno['Conta'],null,'',$enviaGET);
            if(!empty($enviaGET['errors'])||!empty($enviaGET['error'])){ $trataErros = self::trataErros($retorno['Conta'],$enviaGET); }
            else{ $retorno['FunilVendas']=$enviaGET; }
        }
        return $retorno;
    }    
    public static function listaAtivosConversao($retorno=null,$request=null) {
        if(!empty($retorno['Conta'])){
            $start_date=date("Y-m-d"); $end_date=date("Y-m-d");$asset_id="";
            if(!empty($request)){ if(gettype($request)=="object"){ $request=json_decode(json_encode($request->all()),true); }
                if(!empty($request['start_date'])){$start_date=$request['start_date'];}
                if(!empty($request['end_date'])){$end_date=$request['end_date'];}
                if(!empty($request['asset_id'])){$asset_id="&asset_id=".$request['asset_id'];}
            }
            $enviaGET = GlobalFuncoes::enviaGET("https://api.rd.services/platform/analytics/conversions?assets_type[]=LandingPage&start_date=".$start_date."&end_date=".$end_date.$asset_id,$retorno['Conta']);
            self::salvaLog('listaAtivosConversao',$retorno['Conta'],null,'',$enviaGET);
            if(!empty($enviaGET['errors'])||!empty($enviaGET['error'])){ $trataErros = self::trataErros($retorno['Conta'],$enviaGET); }
            else{ $retorno['AtivosConversao']=$enviaGET; }
        }
        return $retorno;
    }
    public static function listaWorkflows($retorno=null,$request=null) {
        if(!empty($retorno['Conta'])){
            $enviaGET = GlobalFuncoes::enviaGET("https://api.rd.services/platform/workflows",$retorno['Conta']);
            self::salvaLog('listaWorkflows',$retorno['Conta'],null,'',$enviaGET);
            if(!empty($enviaGET['errors'])||!empty($enviaGET['error'])){ $trataErros = self::trataErros($retorno['Conta'],$enviaGET); }
            else{ if(!empty($enviaGET['workflows'])){ $retorno['Workflows']=$enviaGET['workflows']; }else{ $retorno['Workflows']=$enviaGET; } }
        }
        return $retorno;
    }  
    public static function listaLandingPages($retorno=null,$request=null) {
        if(!empty($retorno['Conta'])){
            //&search=teste //&ids=dsfsdfdsfdsf,645654 &order=title
            $enviaGET = GlobalFuncoes::enviaGET("https://api.rd.services/platform/landing_pages?order=title&page_size=50&page=1",$retorno['Conta']);
            self::salvaLog('listaLandingPages',$retorno['Conta'],null,'',$enviaGET);
            if(!empty($enviaGET['errors'])||!empty($enviaGET['error'])){ $trataErros = self::trataErros($retorno['Conta'],$enviaGET); }
            else{  $retorno['LandingPages']=$enviaGET; }
        }
        return $retorno;
    }    
    public static function listaWebhooks($retorno=null,$request=null) {
        if(!empty($retorno['Conta'])){
            $enviaGET = GlobalFuncoes::enviaGET("https://api.rd.services/integrations/webhooks",$retorno['Conta']);
            self::salvaLog('listaWebhooks',$retorno['Conta'],null,'',$enviaGET);
            if(!empty($enviaGET['errors'])||!empty($enviaGET['error'])){ $trataErros = self::trataErros($retorno['Conta'],$enviaGET); }
            else{ $retorno['Webhooks']=$enviaGET; }
        }
        return $retorno;
    } 

    public static function criarWebhookOportunidade($webhookURL="",$Conta=null) { 
        $retorno = [];
        if(!empty($webhookURL) && !empty($Conta) ){
            $webhooks = GlobalFuncoes::enviaGET("https://api.rd.services/integrations/webhooks",$Conta);
            $tem="NAO";
            if(!empty($webhooks['webhooks'])){ foreach ($webhooks['webhooks'] as $webhook) {
                if($webhook['url']==$webhookURL){ $tem="SIM"; $retorno=$webhook; }
            } }
            if($tem=="NAO"){
                $post=[
                    "url" => $webhookURL,
                    "event_type" => "WEBHOOK.MARKED_OPPORTUNITY",
                    "entity_type" => "CONTACT",
                    "http_method" => "POST",
                    "include_relations" => [ "COMPANY", "CONTACT_FUNNEL" ]                    
                ];
                $retorno = GlobalFuncoes::enviaPOST("https://api.rd.services/integrations/webhooks",$post,$Conta,'POST');
            }
        }
        return $retorno;
    }

    public static function emailmkt($retorno=null,$request=null) {
        if(!empty($request)){ if(gettype($request)=="object"){ $request=json_decode(json_encode($request->all()),true); } }
        if(!empty($retorno['Conta'])&&!empty($request['email_id'])){
            $enviaGET = GlobalFuncoes::enviaGET("https://api.rd.services/platform/emails/".$request['email_id'],$retorno['Conta']);
            /*
            "id" => 11017797
  "name" => "modelo1_teste1_hook"
  "component_template_id" => "9564117"
  "created_at" => "10 de Novembro de 2022, 09:16 (GMT-03:00)"
  "updated_at" => "10 de Novembro de 2022, 09:16 (GMT-03:00)"
  "type" => "email_model"
            self::salvaLog('listaWebhooks',$retorno['Conta'],null,'',$enviaGET);
            if(!empty($enviaGET['errors'])||!empty($enviaGET['error'])){ $trataErros = self::trataErros($retorno['Conta'],$enviaGET); }
            else{ $retorno['Webhooks']=$enviaGET; }
            */
        }
        return $retorno;
    }    
    
    public static function listaSegmentos($retorno=null,$request=null) {
        if(!empty($retorno['Conta'])){
            $enviaGET = GlobalFuncoes::enviaGET("https://api.rd.services/platform/segmentations",$retorno['Conta']);
            self::salvaLog('listaSegmentos',$retorno['Conta'],null,'',$enviaGET);
            if(!empty($enviaGET['errors'])||!empty($enviaGET['error'])){ $trataErros = self::trataErros($retorno['Conta'],$enviaGET); }
            else{ $retorno['Segmentos']=$enviaGET; }
        }
        return $retorno;
    }    
    public static function buscaSegmento($Conta=null,$id=null) {
        $retorno=[];
        if(!empty($Conta)){
            $enviaGET = GlobalFuncoes::enviaGET("https://api.rd.services/platform/segmentations/".$id."/contacts",$Conta);
            self::salvaLog('buscaSegmento',$Conta,null,'',$enviaGET);
            if(!empty($enviaGET['errors'])||!empty($enviaGET['error'])){ $trataErros = self::trataErros($Conta,$enviaGET); }
            else{ $retorno=$enviaGET; }
        }
        return $retorno;
    }    
    
    public static function consultarEventos($Conta=null,$uuid=null) {
        $retorno=[];
        if(!empty($Conta)){
            $enviaGET = GlobalFuncoes::enviaGET("https://api.rd.services/platform/contacts/".$uuid."/events?event_type=CONVERSION",$Conta);
            self::salvaLog('consultarEventos',$Conta,null,'',$enviaGET);
            if(!empty($enviaGET['errors'])||!empty($enviaGET['error'])){ $trataErros = self::trataErros($Conta,$enviaGET); }
            else{ $retorno=$enviaGET; }
        }
        return $retorno;
    }     
    public static function camposPersonalizados($retorno=null,$request=null,$debug=false) {
        if(!empty($retorno['Conta'])) {
            $urlPersonalizados = "https://api.rd.services/platform/contacts/fields";
            $enviaGET = GlobalFuncoes::enviaGET($urlPersonalizados,$retorno['Conta']);
            if($debug==true){
                //dd($enviaGET);
            }
            //self::salvaLog('camposPersonalizados',$retorno['Conta'],null,'',$enviaGET);
            if(!empty($enviaGET['errors'])||!empty($enviaGET['error'])){ $trataErros = self::trataErros($retorno['Conta'],$enviaGET); }
            else{
                $fields=array(
                    array("label" => array("pt-BR"=>"Campanha / Primeira Conversão"), "api_identifier" => "first_conversion@source"),
                    array("label" => array("pt-BR"=>"Origem / Primeira Conversão"), "api_identifier" => "first_conversion@conversion_origin@source"),
                    array("label" => array("pt-BR"=>"Identificador / Primeira Conversão"), "api_identifier" => "first_conversion@content@identificador"),
                    array("label" => array("pt-BR"=>"Canal de Origem / Primeira Conversão"), "api_identifier" => "first_conversion@conversion_origin@channel"),

                    array("label" => array("pt-BR"=>"Campanha / Ultima Conversão"), "api_identifier" => "last_conversion@source"),
                    array("label" => array("pt-BR"=>"Origem / Ultima Conversão"), "api_identifier" => "last_conversion@conversion_origin@source"),
                    array("label" => array("pt-BR"=>"Identificador / Ultima Conversão"), "api_identifier" => "last_conversion@content@identificador"),
                    array("label" => array("pt-BR"=>"Canal de Origem / Ultima Conversão"), "api_identifier" => "last_conversion@conversion_origin@channel"),


                    array("label" => array("pt-BR"=>"Data Inclusão na Integracão"), "api_identifier" => "date_now"),
                    array("label" => array("pt-BR"=>"Padrão2: Criado EM"), "api_identifier" => "created_at"),
                    array("label" => array("pt-BR"=>"Padrão2: Empresa/Cliente"), "api_identifier" => "company"),
                    array("label" => array("pt-BR"=>"Padrão2: Dono do Lead"), "api_identifier" => "user"),
                    array("label" => array("pt-BR"=>"Padrão2: URL pública do RD"), "api_identifier" => "public_url"),

                    array("label" => array("pt-BR"=>"Padrão2: Primeira Conversão (Evento/event_identifier)"), "api_identifier" => "first_conversion@content@event_identifier"),
                    array("label" => array("pt-BR"=>"Padrão2: Primeira Conversão (Origem/source)"), "api_identifier" => "first_conversion@source"),
                    array("label" => array("pt-BR"=>"Padrão2: Primeira Conversão (Data/created_at)"), "api_identifier" => "first_conversion@created_at"),
                    array("label" => array("pt-BR"=>"Padrão2: Primeira Conversão (Identidade/identificador)"), "api_identifier" => "first_conversion@content@identificador"),
                    array("label" => array("pt-BR"=>"Padrão2: Primeira Conversão (Origem Conversão/conversion_origin)"), "api_identifier" => "first_conversion@conversion_origin@source"),
                    array("label" => array("pt-BR"=>"Padrão2: Primeira Conversão (Campanha Conversão/campaign)"), "api_identifier" => "first_conversion@conversion_origin@campaign"),
                    array("label" => array("pt-BR"=>"Padrão2: Primeira Conversão (Canal de Origem Conversão/conversion_origin)"), "api_identifier" => "first_conversion@conversion_origin@channel"),
                    
                    array("label" => array("pt-BR"=>"Padrão2: Ultima Conversão (Evento/event_identifier)"), "api_identifier" => "last_conversion@content@event_identifier"),
                    array("label" => array("pt-BR"=>"Padrão2: Ultima Conversão (Origem/source)"), "api_identifier" => "last_conversion@source"),
                    array("label" => array("pt-BR"=>"Padrão2: Ultima Conversão (Data/created_at)"), "api_identifier" => "last_conversion@created_at"),
                    array("label" => array("pt-BR"=>"Padrão2: Ultima Conversão (Identidade/identificador)"), "api_identifier" => "last_conversion@content@identificador"),
                    array("label" => array("pt-BR"=>"Padrão2: Ultima Conversão (Origem Conversão/conversion_origin)"), "api_identifier" => "last_conversion@conversion_origin@source"),
                    array("label" => array("pt-BR"=>"Padrão2: Ultima Conversão (Campanha Conversão/campaign)"), "api_identifier" => "last_conversion@conversion_origin@campaign"),
                    array("label" => array("pt-BR"=>"Padrão2: Ultima Conversão (Canal de Origem Conversão/conversion_origin)"), "api_identifier" => "last_conversion@conversion_origin@channel")
                    
                );
                $retorno['camposPersonalizados']['fields'] = $fields;
                if(!empty($enviaGET['fields'])){
                    $retorno['Conta']->atualizaCampo($enviaGET['fields']);
                    //if(Auth::user()->id==1) {
                    for ($i=0; $i < count($enviaGET['fields']); $i++) { 
                        $enviaGET['fields'][$i]["label"]["pt-BR"]="Padrão1: ".$enviaGET['fields'][$i]["label"]["pt-BR"];
                    }
                    //}
                    $retorno['camposPersonalizados']['fields'] = array_merge($fields,$enviaGET['fields']);
                }
            }
            
            if(!empty($retorno['camposPersonalizados'])) { if(!empty($retorno['camposPersonalizados']['fields'])) {
                //if(Auth::user()->id==1) {
                    //dd($retorno['camposPersonalizados']);
                    //$retorno['Lead'] = Lead::find(1);
                    //dd($retorno['Lead']->dados,$retorno['camposPersonalizados']);
                //}
            } }

        }
        return $retorno;
    }
    public static function criarCampo($Conta=null,$campo="") {
        if(empty($Conta)||empty($campo)){ return false; }
        $api_identifier="cf_".str_replace(" ","_",strtolower($campo));
        $label = ucwords(str_replace("_"," ",$campo));
        $camposPersonalizados = self::camposPersonalizados(['Conta'=>$Conta]);
        //dd($label,$api_identifier,$campo,$camposPersonalizados,$Conta);
        if(!empty($camposPersonalizados['camposPersonalizados']) && !empty($camposPersonalizados['camposPersonalizados']['fields'])) {
            $tem = array_search($api_identifier, array_column($camposPersonalizados['camposPersonalizados']['fields'], 'api_identifier'));
            if(!empty($tem)){ $tem=true; }else{
                $post = [
                    "name" => ['pt-BR'=> $label], "label" => ['pt-BR'=> $label], 
                    "presentation_type" => 'TEXT_AREA', "api_identifier" => $api_identifier, "data_type"=> 'STRING' 
                ];                
                $enviaPOST = GlobalFuncoes::enviaPOST("https://api.rd.services/platform/contacts/fields",$post,$Conta);
                if(!empty($enviaPOST['uuid'])){ $tem=true; }
                //dd($enviaPOST,$tem,$label,$api_identifier,$campo,$camposPersonalizados,$Conta);
            }
            //dd($tem,$label,$api_identifier,$campo,$camposPersonalizados,$Conta);
            return $tem;
        }
    }  

    /*
    public static function adicionaTags($Conta=null,$uuid=null,$tags=array()) { if(!empty($Conta)&&!empty($uuid)){
        $post = [ 'tags' => $tags ];
        $urlTags = "https://api.rd.services/platform/contacts/".$uuid;
        $enviaPOST = GlobalFuncoes::enviaGET($urlTags,$post,$Conta);

        if(!empty($enviaPOST['errors'])||!empty($enviaPOST['error'])){ dd('adicionaTags',$enviaPOST); $trataErros = self::trataErros($Conta,$enviaPOST);
            if($trataErros === true){ return self::adicionaTags($Conta,$uuid,$tags); }
        }else{
            dd('adicionaTags',$enviaPOST); return $enviaPOST;
        }
        return false;
    } }
    public static function atualizarFunil($Conta=null,$email=null,$funil="default") { if(!empty($Conta)&&!empty($email)){
        //if($Conta->sigla=="c2"){ }
        $post = array(
            "lifecycle_stage" => "Client", // 'Lead', 'Qualified Lead' and 'Client'.
            "opportunity" => true,
            "contact_owner_email" => null//"fernando.martins@hook.app.br"//"equipe@bw8.com.br" //fernando.martins@hook.app.br
        );
        $urlFunil = "https://api.rd.services/platform/contacts/email:".$email."/funnels/".$funil;
        $enviaPOST = GlobalFuncoes::enviaPOST($urlFunil,$post,$Conta,'PUT');
        if(!empty($enviaPOST['errors'])||!empty($enviaPOST['error'])){ $trataErros = self::trataErros($Conta,$enviaPOST);
            if($trataErros === true){ return self::buscarFunil($Conta,$email,$funil); }
        }else{ $Lead = Lead::where('email',$email)->first(); $Lead->atualizaFunil($enviaPOST,$funil); return $enviaPOST; }
        return false;
    } }
    public static function oportunidadeFunil($Conta=null,$email=null,$funil="default") { if(!empty($Conta)&&!empty($email)){
        $post = array(
            "event_type" => "OPPORTUNITY",
            "event_family" => "CDP",
            "payload" => array(
                "email" => $email,
                "funnel_name" => $funil
            )
        );
        $urlEvento = "https://api.rd.services/platform/events";//"https://api.rd.services/platform/contacts/email:".$email."/funnels/".$funil;
        $enviaPOST = GlobalFuncoes::enviaPOST($urlEvento,$post,$Conta,'PUT');
        if(!empty($enviaPOST['errors'])||!empty($enviaPOST['error'])){ $trataErros = self::trataErros($Conta,$enviaPOST);
            if($trataErros === true){ return self::buscarFunil($Conta,$email,$funil); }
        }else{ //$Lead = Lead::where('email',$email)->first(); $Lead->atualizaFunil($enviaPOST,$funil);
            return $enviaPOST; }
        return false;
    } }

    public static function camposPersonalizados($Conta=null) { if(!empty($Conta)){
        $urlPersonalizados = "https://api.rd.services/platform/contacts/fields";
        $enviaGET = GlobalFuncoes::enviaGET($urlPersonalizados,$Conta);

        if(!empty($enviaGET['errors'])||!empty($enviaGET['error'])){ $trataErros = self::trataErros($Conta,$enviaGET);
            //if($trataErros === true){ return self::buscarFunil($Conta,$email,$funil); }
        }else{
            if(!empty($enviaGET['fields'])){ $Conta->atualizaCampo($enviaGET['fields']); }
            return $enviaGET;
        }
        return false;
    } }
    */
}
