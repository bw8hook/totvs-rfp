<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Models\KnowledgeBase;
use App\Models\RfpBundle;
use App\Models\KnowledgeRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ImportController extends Controller
{
    public function listRecords($id)
    {
        $ListFiles = array();

        $AllFiles = KnowledgeRecord::where('knowledge_base_id', $id)->first();
        
        if( isset($AllFiles->user_id) && ($AllFiles->user_id == Auth::id() || Auth::user()->account_type === "admin")){
            $resultados = DB::table(table: 'knowledge_records')
            ->leftJoin('rfp_bundles', 'knowledge_records.bundle_id', '=', 'rfp_bundles.bundle_id') // INNER JOIN
            ->select('knowledge_records.bundle_id', 'rfp_bundles.bundle',  DB::raw('COUNT(*) as total'))
            ->where('knowledge_records.knowledge_base_id', $id) // Filtra pelo ID do usuário
            ->groupBy('knowledge_records.bundle_id') // Agrupa pelo ID do bundle
            ->get();
        
            $CountResultado = 0;
            $CountPacotes = 0;
            //Exibindo o resultado
            foreach ($resultados as $resultado) {
                $CountPacotes++;
                $CountResultado = $CountResultado + $resultado->total;
            }
        

            $data = array(
                'title' => 'Todos Arquivos',
                'ListImports' => $resultados,
                'CountResultado' => $CountResultado,
                'CountPacotes' => $CountPacotes 
            );
  
            return view('import.list')->with($data);

        }else{
            return redirect()->route('knowledge.list')->with('error', 'Usuário sem permissão para visualizar.');
        }


    }



    public function listErroRecords($id)
    {
       
        $ListFiles = array();

        $resultados = DB::table(table: 'knowledge_errors')
            ->select('knowledge_errors.error_code', 'knowledge_errors.error_message', 'knowledge_errors.error_data', 'knowledge_errors.created_at')
            ->where('knowledge_errors.id',  $id) // Filtra pelo ID do usuário
            ->first();

         

          $data = array(
              'title' => 'Todos Arquivos',
              'error_code' => $resultados->error_code,
              'error_message' => $resultados->error_message,
              'error_data' => json_decode($resultados->error_data),
              'created_at' => $resultados->created_at,
          );  

            return view('import.listError')->with($data);


    }


    
    
}