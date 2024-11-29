<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Controllers\Users;
use App\Models\User;
use App\Models\KnowledgeBase;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Models\KnowledgeError;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\KnowledgeBaseImport;
use Maatwebsite\Excel\Validators\ValidationException;

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

    public function uploadFile(Request $request) {
          // pega o caminho da pasta do ERP selecionado
          if(isset($request->totvs_erp)){
            $UrlFiles = 'rfps/'.$request->totvs_erp;
            $TotvsERP = $request->totvs_erp;
          }else{
            $UrlFiles = 'rfps/user/'.Auth::id();
            $TotvsERP = 0;
          }
           
          // Joga o arquivo na pasta do ERP selecionado
          $File = $request->file('file');
          $filePath = $File->store($UrlFiles);
   
          $KnowledgeBaseData = new KnowledgeBase();
          $KnowledgeBaseData->user_id = Auth::id();
          if(isset($request->totvs_erp)){
            $KnowledgeBaseData->bundle_id = $request->totvs_erp;
          }
          $KnowledgeBaseData->filename_original = $File->getClientOriginalName();
          $KnowledgeBaseData->filepath = $File->store($UrlFiles);
          $KnowledgeBaseData->filename = $File->hashName();
          $KnowledgeBaseData->file_extension = $File->extension();
          $KnowledgeBaseData->save();

          $KnowledgeBaseDataid = $KnowledgeBaseData->id;

          try {
              $import = new KnowledgeBaseImport($KnowledgeBaseDataid,   $TotvsERP);

              // Executa a importação
              $Excel = Excel::import($import, $filePath);
              
               // Acessar a URL gerada dentro da classe de importação
              $MensagemErro = $import->Erros;

              return response()->json(['success' => true, 'redirectUrl' => '/import/'.$KnowledgeBaseDataid]);
            
          } catch (ValidationException $e) {
              // Captura exceções de validação específicas do Maatwebsite Excel
              $failures = $e->failures();
      
              return response()->json([
                  'message' => 'Erros durante a validação!',
                  'failures' => $failures, // Detalhes das linhas com falhas
              ], 422);

          } catch (\Exception $e) {
              $CatchError = json_decode($e->getMessage());

              dd($e);

              $InsertError = KnowledgeError::create([
                  'error_code' => 'ERR003',
                  'error_message' => $CatchError->error_message,
                  'error_data' => json_encode(value: $CatchError->error_data),
                  'user_id' => Auth::id(), // Associar ao usuário logado, se necessário
              ]);

              $InsertErrorID = $InsertError->id;
      
              // Remove a Base Enviada
              if ($KnowledgeBaseDataid) {
                  DB::table('knowledge_base')->where('knowledge_base_id', $KnowledgeBaseDataid)->delete();
              }

              // Captura quaisquer outras exceções
              return response()->json([
                  'message' => 'Erro durante a importação!',
                  'redirectUrl' => '/import/erro/'.$InsertErrorID
              ], 500);
          }



    
    }

    public function upload(Request $request)
    {
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