<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class FileController extends Controller
{
        
    public function getFile($bundle, $folder, $filename = null)
    {
        if($bundle != "user"){
            if (Storage::exists("app/rfps/".$bundle."/".$folder)) {
                return response()->file(storage_path("app/rfps/".$bundle."/".$folder));
            } else {
                 // Public folder
                 if (Storage::exists("/rfps/".$bundle."/".$folder)) {
                    return response()->file(storage_path("app/rfps/".$bundle."/".$folder));
                }else{
                    return response()->json('Arquivo não econtrado 1');
                }
              // die();
            } 
        }else{
            if (Storage::exists("app/rfps/".$bundle."/".$folder."/".$filename)) {
                return response()->file(storage_path("app/rfps/".$bundle."/".$folder."/".$filename));
            } else {
                return response()->json('Arquivo não econtrado 2');
            } 

           
        }
       
    }

}
