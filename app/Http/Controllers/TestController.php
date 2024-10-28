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

class TestController extends Controller
{
    public function index() {
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

    public function indexGpt() {
      dd("test");
    }
}