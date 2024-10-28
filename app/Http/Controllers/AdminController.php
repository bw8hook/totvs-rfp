<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Users;
use App\Models\User;


class AdminController extends Controller
{


    public function dashboard(){

      // Pega todos os arquivos da PASTA
      $userId = Auth::id();
      $AgenteId = Auth::user()->agent;
  
      $directory =  'public/uploads/'.$userId."/";
      $storage = Storage::allFiles($directory);
      

      $Files = array();
      foreach ($storage as $key => $file) {
        // Limpa o Nome do Arquivo
        $NomeArquivo = str_replace($directory, "", $file);
        $NomeArquivo = str_replace("document/", "", $NomeArquivo);
        $NomeArquivo = str_replace("image/", "", $NomeArquivo);

        // Alteração para o link de Download
        $DownloadLink = str_replace("public", "storage", $file);

        //Adiciona arquivos no ARRAY
        if($NomeArquivo != 'diretriz.txt' && $NomeArquivo != '.DS_Store'){
          $Files[$key]['id'] = $key-1;
          $Files[$key]['titulo'] = $NomeArquivo;
          $Files[$key]['link'] = $file;
          $Files[$key]['link_download'] = $DownloadLink;
        }
      }

      $users = User::all();
      $stringUsers = '';
      foreach ($users as $user) {
        $stringUsers = $stringUsers . "-" . $user->id . ',' . $user->name;
      }

      $data = array(
        'files' => $Files,
        'AgenteId' => $AgenteId,
        'userId' => $userId,
        'stringUsers' => $stringUsers
      );

      return view('admin.dashboard')->with($data);
    }




    public function delete($Path, $folder, $userid, $type, $doc){
      // Pega o Caminho do Arquivo
      $Doc = $Path.'/'.$folder.'/'.$userid.'/'.$type.'/'.$doc;

      // Verifica se o Arquivo existe
      if (Storage::exists($Doc)){
        try {
          // Deleta o Arquivo
          Storage::delete($Doc);
        } catch (\Throwable $th) {
          dd($th);
        }
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
        $FileClean = str_replace("document/", "", $FileClean);
        $FileClean = str_replace("image/", "", $FileClean);


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

      if(empty($UserId)){
          $UserId = Auth::id();
      }

      $authorization = "Authorization: Bearer ".md5("1_".date("Y-m-d"));

      $curl = curl_init();

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
