<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;


class AdminController extends Controller
{


    public function dashboard(){


      $directories = Storage::disk('public');

      $userId = Auth::id();
      $AgenteId = Auth::user()->agent;
      
      $directory =  'public/uploads/'.$userId."/";
      //$directories = Storage::directories($directory);
 
      //$directories = Storage::allDirectories($directory);
      $storage = Storage::allFiles($directory);
      

      $Files = array();
      foreach ($storage as $key => $file) {
        $FileClean = str_replace($directory, "", $file);

        $FileClean = str_replace("documentos/", "", $FileClean);
        $FileClean = str_replace("imagens/", "", $FileClean);
        $FileClean = str_replace("audio/", "", $FileClean);
        $FileClean = str_replace("video/", "", $FileClean);
        $FileClean = str_replace("arquivos/", "", $FileClean);
        $FileClean = str_replace("fontes/", "", $FileClean);
        $FileClean = str_replace("historico/", "", $FileClean);
        $FileClean = str_replace("planilhas/", "", $FileClean);

        $fileDownload = str_replace("public", "storage", $file);

        // $ext = pathinfo($file, PATHINFO_EXTENSION);

        // $Arquivo['url'] = 'https://arquivos.bw8.com.br/'.$ExtUrl;

        
        $Files[$key]['id'] = $key+1;
        $Files[$key]['titulo'] = $FileClean;
        $Files[$key]['link'] = $file;
        $Files[$key]['link_download'] = $fileDownload;
        
      }

      $data = array(
        'title' => 'Todos Arquivos',
        'files' => $Files,
        'AgenteId' => $AgenteId
      );


      //return view('inc.file')->with($data);

      
      
      return view('admin.dashboard')->with($data);
    }

    public function delete($public_folder, $folder, $userid, $type, $doc){
      $Doc = $public_folder.'/'.$folder.'/'.$userid.'/'.$type.'/'.$doc;

      if (Storage::exists($Doc)){
        Storage::delete($Doc);
      }

      return redirect()->back();
    }


    public function update(){
  
      $Data = array();
      $Data['agent_id'] = Auth::user()->agent;
      $Data['base_id'] = Auth::user()->base;

      $userId = Auth::id();
      $directory =  'public/uploads/'.$userId."/";
      $storage = Storage::allFiles($directory);
      
      $Files = array();
      foreach ($storage as $key => $file) {
        $FileClean = str_replace($directory, "", $file);

        $FileClean = str_replace("documentos/", "", $FileClean);
        $FileClean = str_replace("imagens/", "", $FileClean);


        $ExtUrl = str_replace("public", "storage", $file);

        $ext = pathinfo($file, PATHINFO_EXTENSION);

        $Arquivo['url'] = 'https://ia.hook.app.br/'.$ExtUrl;
        $Arquivo['ext'] = $ext;
        $Arquivo['size'] = Storage::size($file);;
        $Arquivo['title'] = $FileClean;
        $Arquivo['description'] = $FileClean;

        $Files[] = $Arquivo;

      }

      $Data['arquivos'] = $Files;

      $curl = curl_init();

      $authorization = "Authorization: Bearer ".md5("10_".date("Y-m-d"));

      curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://bw8.hook.app.br/arquivos-base-mentoria/api/base-file',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($Data),
        CURLOPT_HTTPHEADER => array(
          'Content-Type: application/json',
          $authorization
        ),
      ));

      $response = curl_exec($curl);
      
      curl_close($curl);
    
      $RespostaDecoded = json_decode($response);
      if($RespostaDecoded->status == true){
        return redirect()->back();
      }else{
        return redirect()->back();
      }



    }




}
