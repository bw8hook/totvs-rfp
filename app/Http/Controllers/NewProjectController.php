<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Controllers\Users;
use App\Models\User;
use App\Models\KnowledgeBase;
use App\Models\RfpProject;
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
use App\Imports\NewProjectImport;
use App\Exports\NewProjectExport;
use Maatwebsite\Excel\Validators\ValidationException;
use App\Exceptions\RDStationMentoria\RDStationMentoria;


use App\Models\RfpBundle;

class NewProjectController extends Controller
{
    protected $RDStationMentoria = [];

    public function index() {
        $userId = auth()->user()->id;

        $rfpBundles = RfpBundle::all();
        $ListBundles = array();

        //$AgentId = Auth::user()->id;

        foreach ($rfpBundles as $key => $User) {
              $ListBundle = array();
              $ListBundle['id'] = $User->bundle_id;
              $ListBundle['bundle'] = $User->bundle;
              $ListBundle['type'] = $User->type_bundle;
              $ListBundles[] = $ListBundle;
        }

        $data = [
            'userId' => $userId,
            'ListBundles' => $ListBundles
        ];

        return view('project.newproject')->with($data);
    }





  public function uploadMentoria(){
  
    $Data = array();
    $Data['agent_id'] = "agt_01JEY70XQPHBDMBJ6XP2VHCJBX";
    $Data['base_id'] = "kb_01JEY61V19GCT1XRXT54T72XQ3";

    $this->RDStationMentoria = new RDStationMentoria();
    $this->RDStationMentoria->classBases();
    
    $gBaseById = $this->RDStationMentoria->Bases->getBaseById( $Data['base_id']);

    //dd($gBaseById);

    if (!empty($gBaseById['id'])) {
        $antes = $gBaseById['sources'];
        $gBaseById['sources'] = [];

        $rfpBundles = KnowledgeBase::all();
        $ListBundles = array();

        foreach ($rfpBundles as $key => $Bundle) {
                $FileClean = str_replace("rfps/", "", $Bundle->filepath);
                $arquivoPath = 'https://bw8.com.br/fotos/EV-6261-Requisitos%20T%C3%A9cnicos.xlsx';

                $Arquivo = array();
                $Arquivo['url'] = $arquivoPath;
                $Arquivo['title'] = $Bundle->filename_original;
                $Arquivo['img'] = null;
                $Arquivo['description'] = $Bundle->filename_original;
                $Arquivo['key'] = $Bundle->filename;
                //$ext = pathinfo($file, PATHINFO_EXTENSION);
                //$Arquivo['size'] = Storage::size($file);;

                if($Bundle->file_extension == "xlsx"){
                    $Arquivo['ext'] = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
                }else if($Bundle->file_extension == "xls"){
                    $Arquivo['ext'] = "application/vnd.ms-excel";
                }else{
                    $Arquivo['ext'] = "text/csv";
                }
                $gBaseById['sources'][] = ["type" => "file", "file" => $Arquivo];
        }

        if (count($gBaseById['sources']) > 1) {
            $updateBase = $this->RDStationMentoria->Bases->updateBase($gBaseById['id'], $gBaseById);
            if (!empty($updateBase['id'])) {
                dd('atualizdo');
            }
            dd($updateBase);
        }

    } else {
        dd("erro");
    }

    //$directory =  'rfps';
    //$storage = Storage::allFiles($directory);

  }




  public function result($id)
  {
      $ListFiles = array();

      $AllFiles = RfpProject::find($id);
      
      if( isset($AllFiles->user_id) && ($AllFiles->user_id == Auth::id() || Auth::user()->account_type === "admin")){

         // Calcula a porcentagem
        $percentage = ($AllFiles['answered'] / ($AllFiles['answered'] + $AllFiles['unanswered'])) * 100;
        $PercentageAnswered = round($percentage, 2);

        $PercentageUnAnswered = 100 - $PercentageAnswered;

            $data = [
                "title" =>  $AllFiles['title'],
                "description" => $AllFiles['description'],
                "status" => $AllFiles['status'],
                "answered" => $AllFiles['answered'],
                "unanswered" => $AllFiles['unanswered'],
                "filename_original" => $AllFiles['filename_original'],
                "filepath" => $AllFiles['filepath'],
                "filename" => $AllFiles['filename'],
                "file_extension" => $AllFiles['file_extension'],
                "PercentageAnswered" => $PercentageAnswered,
                "PercentageUnAnswered" => $PercentageUnAnswered,
            ];


          return view('project.result')->with($data);

      }else{
          return redirect()->route('knowledge.list')->with('error', 'Usuário sem permissão para visualizar.');
      }


  }

  





}