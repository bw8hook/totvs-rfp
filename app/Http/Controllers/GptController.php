<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Controllers\Users;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class GptController extends Controller
{
    public function index($file) {
      $userId = Auth::id();
      $AgenteId = Auth::user()->agent;
  
      $directory =  'public/uploads/'.$userId."/";
      $storage = Storage::allFiles($directory);

      $Files = array();
      foreach ($storage as $key => $file) {
        $NomeArquivo = str_replace($directory, "", $file);
        $NomeArquivo = str_replace("document/", "", $NomeArquivo);
        $NomeArquivo = str_replace("image/", "", $NomeArquivo);

        $DownloadLink = str_replace("public", "storage", $file);

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
        'stringUsers' => $stringUsers
      );

      return view('test')->with($data);
    }

    public function uploadFile($url, $curlFile, $hash) {
        $ch = curl_init();
    
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'file' => $curlFile,
            'chatbotId' => $hash
        ]);
      
        $response = curl_exec($ch);
      
        // if (curl_errno($ch)) {
        //     echo 'cURL error: ' . curl_error($ch) . "\n";
        // } else {
        //     echo 'Response from ' . $url . ': ' . $response . "\n";
        // }
      
        curl_close($ch);
    }

    public function upload($file) {
      if (!$file->isValid()) {
        die('Invalid file upload.');
      }

      $filePath = $file->getRealPath();
      $fileName = $file->getClientOriginalName();
      $fileMimeType = $file->getClientMimeType();

      if (!is_readable($filePath)) {
          die('Cannot access file.');
      }

      $curlFile = new \CURLFile($filePath, $fileMimeType, $fileName);

      $userId = Auth::id();
      $hash = hash('sha256', $userId);

      $this->uploadFile('https://dev-ia2.hook.app.br/api/chatbotia/upload', $curlFile, $hash);

      $this->uploadFile('https://dev-ia2.hook.app.br/api/ia/upload', $curlFile, $hash);
    }
        
    public function indexGpt() {
      dd("test");
    }
}