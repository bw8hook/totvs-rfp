<?php

namespace App\Http\Controllers;
use App\Models\KnowledgeBase;
use App\Models\RfpBundle;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KnowledgeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $AllFiles = KnowledgeBase::where('user_id', Auth::id())->get();
        $ListFiles = array();

        foreach ($AllFiles as $key => $File) {
                $ListFile = array();
                $ListFile['knowledge_base_id'] = $File->knowledge_base_id;
                $ListFile['bundle'] = RfpBundle::firstWhere('bundle_id', $File->bundle_id);
                $ListFile['filepath'] = $File->filepath;
                $ListFile['filename_original'] = $File->filename_original;
                $ListFile['filename'] = $File->filename;
                $ListFile['file_extension'] = $File->file_extension;
                $ListFile['status'] = $File->status;
                $ListFile['created_at'] = date("d/m/Y", strtotime($File->created_at));;
    

                $ListFiles[] = $ListFile;
          }

          $data = array(
              'title' => 'Todos Arquivos',
              'ListFiles' => $ListFiles,
          );
  
          //return view('auth.register')->with($data);

        return view('knowledge.list')->with($data);

    
        //return view('knowledge.list')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

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

        //return view('auth.register')->with($data);
       
        $userId = auth()->user()->id;
        $data = [
            'userId' => $userId,
            'ListBundles' => $ListBundles
        ];
    
        return view('knowledge.create')->with($data);
    
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Encontrar o usuário pelo ID
        $Arquivo = KnowledgeBase::where('knowledge_base_id', $id)->first();
        if ($Arquivo['user_id'] == Auth::id() || Auth::user()->account_type == "admin"){
            if (Storage::exists($Arquivo->filepath)){
                if (Storage::delete($Arquivo->filepath)){
                    KnowledgeBase::where('knowledge_base_id', $id)->delete();// Exclui o usuário do banco de dados
                    return redirect()->back()->with('success', 'Arquivo excluído com sucesso.');
                }else{
                    return redirect()->back()->with('error', 'Erro ao excluir arquivo.');
                }
            }else{
                return redirect()->back()->with('error', 'Arquivo não encontrado.');
            }
        } else {
            return redirect()->back()->with('error', 'Usuário sem permissão para excluir.');
        }
    }
}
