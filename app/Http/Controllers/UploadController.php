<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Controllers\Users;
use App\Models\User;
use App\Models\KnowledgeBase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class UploadController extends Controller
{
    public function index($file) {
      $userId = Auth::id();

      $directory =  'public/uploads/'.$userId."/";
      $storage = Storage::allFiles($directory);

      $Files = array();
      foreach ($storage as $key => $file) {
        $NomeArquivo = str_replace($directory, "", $file);
        $NomeArquivo = str_replace("document/", "", $NomeArquivo);
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

    public function upload(Request $request)
    {

        // $request->validate([
        //     'file' => 'required|file|mimes:xml|max:2048',
        // ]);

        // pega o caminho da pasta do ERP selecionado
        $UrlFiles = 'rfps/'.$request->totvs_erp;    
        // Joga o arquivo na pasta do ERP selecionado
        $File = $request->file('file');
        $filePath = $File->store($UrlFiles);

        $KnowledgeBaseData = new KnowledgeBase();
        $KnowledgeBaseData->user_id = Auth::id();
        $KnowledgeBaseData->bundle_id = $request->totvs_erp;
        $KnowledgeBaseData->filename_original = $File->getClientOriginalName();
        $KnowledgeBaseData->filepath = $File->store($UrlFiles);
        $KnowledgeBaseData->filename = $File->hashName();
        $KnowledgeBaseData->file_extension = $File->extension();
        $KnowledgeBaseData->save();
      
        // Retorna o JSON
        return response()->json(['filePath' => $filePath], 200);
    }
}