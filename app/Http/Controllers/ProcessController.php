<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\User;
use App\Models\RfpProcess;
use App\Models\Agent;
use App\Models\RfpBundle;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;


class ProcessController extends Controller
{
    public function filter(Request $request)
    {
        // Aplicar ordenação
        $orderBy = $request->get('sort_order', 'id_desc'); // Padrão: mais recente primeiro

        $query = RfpProcess::query();

        switch ($orderBy) {
            case 'id_asc':
                $query->orderBy('id', 'asc');
                break;
            case 'id_desc':
                $query->orderBy('id', 'desc');
                break;
            case 'bundle_asc':
                $query->orderBy('process', 'asc');
                break;
            case 'bundle_desc':
                $query->orderBy('process', 'desc');
               
                break;
            }

        // Paginação
        $bundles = $query->paginate(40);


        // Retornar dados em JSON
        return response()->json(data: $bundles);
    }
    
    public function list(Request $request): View
    {
          //$AllBundles = RfpBundle::all();

          $AllBundles = RfpProcess::orderBy('process', 'asc')->get();
          $ListBundles = array();

          foreach ($AllBundles as $key => $Bundle) {   
                $ListBundle = array();
                $ListBundle['id'] = $Bundle->bundle_id;
                $ListBundle['nome'] = $Bundle->bundle;
                if(isset($Bundle->created_at)){
                    $ListBundle['created_at'] = date("d/m/Y", strtotime($Bundle->created_at));
                }else{
                    $ListBundle['created_at'] = " - ";
                }
               
                $ListBundles[] = $ListBundle;
          }


          $data = array(
              'ListBundles' => $ListBundles,
              'TotalFound' => $AllBundles->count(),
          );

  
          //return view('auth.register')->with($data);

        return view('process.list', ['user' => $request->user(), ])->with($data);
    }




    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $Bundles = RfpBundle::all();
        $data = array( 'bundles' => $Bundles);    

        return view('process.create')->with($data);
    }



      /**
     * Display the registration view.
     */
    public function edit($id): View
    {
            $Process = RfpProcess::find($id);
            $Bundles = RfpBundle::all();

            $ProcessBundles = RfpProcess::with('rfpBundles')->find($id)->toArray();
            $processWithBundles = RfpProcess::with('rfpBundles')->find($id);
            $ProcessBundles = $processWithBundles->rfpBundles->toArray();;


            if ($Process && $Bundles) {
                $data = array(
                    'process' => $Process,
                    'bundles' => $Bundles,
                    'id' => $id,
                    'ProcessBundles' => $ProcessBundles
                );    

                
                return view('process.edit')->with($data);;
            } else {
                return redirect()->back()->with('error', 'Produto não encontrado.');
            }

    }

    /**
     * Display the registration view.
     */
    public function remove($id)
    {
       
            // Encontrar o usuário pelo ID
            $Process = RfpProcess::find($id);

            if ($Process) {
                $Process->rfpBundles()->detach();
                $Process->delete(); // Exclui o usuário do banco de dados
                return redirect()->back()->with('success', 'Produto excluído com sucesso.');
            } else {
                return redirect()->back()->with('error', 'Produto não encontrado.');
            }
    }


         /**
     * Display the registration view.
     */
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'process' => 'required',
            'status' => 'required',
        ]);
        
        try {
            $Id = RfpProcess::create($validatedData);

            return redirect()->route('process.list')->with('success', 'Processo criado com sucesso.');
        } catch (\Throwable $th) {
            return redirect()->route('process.list')->with('error', 'Erro ao Salvar Processo.');
        }
    }

        /**
     * Display the registration view.
     */
    public function update(Request $request, $id)
    {
       
       

            // Validação dos dados
            $validatedData = $request->validate([
                'process' => 'required|string|max:255',
            ]);

            $produto = RfpProcess::where('id', $id)->firstOrFail(); // Encontra o registro pelo ID

           
            
            if ($produto) {
                $produto->process =  $validatedData['process'];
                $produto->status =  $request->status;     
                
                $produto->save(); // Salva as alterações no banco
                
                return redirect()->route('process.list')->with('success', 'Processo editado com sucesso.');
                //return response()->json(['message' => 'Produto criado com sucesso!', 'data' => $post], 201);
               
            } else {
                return redirect()->back()->with('error', 'Produto não encontrado.');
            }
    }


}
