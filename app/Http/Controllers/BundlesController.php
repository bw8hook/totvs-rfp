<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\User;
use App\Models\RfpBundle;
use App\Models\Agent;
use App\Models\Category;
use App\Models\LineOfProduct;
use App\Models\Module;
use App\Models\Segments;
use App\Models\RfpProcess;
use App\Models\Type;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;


class BundlesController extends Controller
{
    public function filter(Request $request)
    {
        // Aplicar ordenação
        $orderBy = $request->get('sort_order', 'id_desc'); // Padrão: mais recente primeiro

        $query = RfpBundle::query();

        switch ($orderBy) {
            case 'id_asc':
                $query->orderBy('bundle_id', 'asc');
                break;
            case 'id_desc':
                $query->orderBy('bundle_id', 'desc');
                break;
            case 'bundle_asc':
                $query->orderBy('bundle', 'asc');
                break;
            case 'bundle_desc':
                $query->orderBy('bundle', 'desc');
               
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

          $AllBundles = RfpBundle::orderBy('bundle', 'asc')->get();
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

        return view('bundles.list', ['user' => $request->user(), ])->with($data);
    }




    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $lineofproducts = LineOfProduct::all();
        $segments = Segments::all();
        $agents = Agent::all();
        $modules = Module::all();
        $process = RfpProcess::all();
        $categories = Category::all();
        $types = Type::all();
  

        $data = array(
            'lineofproducts' => $lineofproducts,
            'segments' => $segments,
            'agents' => $agents,
            'categories' => $categories,
            'modules' => $modules,
            'types' => $types,
            'process' => $process,
        );

       return view('bundles.create')->with($data);
    }



      /**
     * Display the registration view.
     */
    public function edit($id): View
    {
        if (Auth::user()->hasAnyPermission(['bundles.manage'])) {
            $Bundle = RfpBundle::where('bundle_id', $id)->firstOrFail();
            
            $lineofproducts = LineOfProduct::all();
            $LinesSelected = $Bundle->lineOfProduct()->get();

            $segments = Segments::all();
            $SegmentsSelected = $Bundle->segments()->get();

            $agents = Agent::all();
            $AgentSelected = Agent::where('id', $Bundle->agent_id)->first();

            $modules = Module::all();
            $ModulesSelected = $Bundle->modules()->get();

            $process = RfpProcess::all();
            $ProcessSelected = $Bundle->rfpProcesses()->get();

            $categories = Category::all();
            $CategorieSelected = Category::where('id', $Bundle->category_id)->first();

            $types = Type::all();
            $typesSelected = Type::where('id', $Bundle->type_id)->first();

            if ($Bundle && $agents) {
                $data = array(
                    'bundle' => $Bundle,
                    'lineofproducts' => $lineofproducts,
                    'segments' => $segments,
                    'agents' => $agents,
                    'modules' => $modules,
                    'process' => $process,
                    'categories' => $categories,
                    'types' => $types,
                    'id' => $id,
                    'LinesSelected' => $LinesSelected,
                    'SegmentsSelected' => $SegmentsSelected,
                    'AgentSelected' => $AgentSelected,
                    'ModulesSelected' => $ModulesSelected,
                    'ProcessSelected' => $ProcessSelected,
                    'CategorieSelected' => $CategorieSelected,
                    'typesSelected' => $typesSelected
                );

                return view('bundles.edit')->with($data);;
            } else {
                return redirect()->back()->with('error', 'Produto não encontrado.');
            }
        }else{
            return redirect()->back()->with('error', 'Usuário sem permissão para editar.');
        }
    }

      /**
     * Display the registration view.
     */
    public function remove($id)
    {
        if (Auth::user()->hasAnyPermission(['bundles.manage'])) {
            // Encontrar o usuário pelo ID
            $user = RfpBundle::find($id);

            if ($user) {
                $user->delete(); // Exclui o usuário do banco de dados
                return redirect()->back()->with('success', 'Produto excluído com sucesso.');
            } else {
                return redirect()->back()->with('error', 'Produto não encontrado.');
            }
        }else{
            return redirect()->back()->with('error', 'Usuário sem permissão para excluir.');
        }
    }


         /**
     * Display the registration view.
     */
    public function register(Request $request)
    {
        if (Auth::user()->hasAnyPermission(['bundles.manage'])) {
            // Validação dos dados
            $validatedData = $request->validate([
                'name' => 'required|string|max:255'
            ]);

            // Criar o novo registro no banco de dados
            $post = RfpBundle::create([
                'bundle' => $validatedData['name'],
                'status' => $request->status,
                'status_totvs' => $request->status_totvs,
                'type_id' => intval($request->types),
                'category_id' => intval($request->categories),
                'agent_id' => intval($request->agents),
            ]);

            if ($post) {
                if($request->selected_line){
                    $post->lineOfProduct()->sync($request->selected_line);
                }

                if($request->selected_segment){
                    $post->segments()->sync($request->selected_segment);
                }
                if($request->selected_module){
                    $post->modules()->sync($request->selected_module);
                }
                if($request->selected_process){
                    $post->rfpProcesses()->sync($request->selected_process);
                }


                //return response()->json(['message' => 'Produto criado com sucesso!', 'data' => $post], 201);
                return redirect()->back()->with('success', 'Produto criado com sucesso.');
            } else {
                return redirect()->back()->with('error', 'Produto não encontrado.');
            }
        }else{
            return redirect()->back()->with('error', 'Usuário sem permissão para excluir.');
        }
    }

        /**
     * Display the registration view.
     */
    public function update(Request $request)
    {
        if (Auth::user()->hasAnyPermission(['bundles.manage'])) {
            // Validação dos dados
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'bundle_id' => 'required'
            ]);

          

            $produto = RfpBundle::where('bundle_id', $validatedData['bundle_id'])->firstOrFail(); // Encontra o registro pelo ID
        
            if ($produto) {
                $produto->bundle =  $validatedData['name'];
                $produto->status =  $request->status;
                $produto->status_totvs =  $request->status_totvs;
                $produto->type_id = intval($request->types); 
                $produto->category_id = intval($request->categories); 
                $produto->agent_id = intval($request->agents); 

                $produto->save(); // Salva as alterações no banco

                if($request->selected_line){
                    $produto->lineOfProduct()->sync($request->selected_line);
                }

                if($request->selected_segment){
                    $produto->segments()->sync($request->selected_segment);
                }
                if($request->selected_module){
                    $produto->modules()->sync($request->selected_module);
                }
                if($request->selected_process){
                    $produto->rfpProcesses()->sync($request->selected_process);
                }

               
                
                return redirect()->route('bundles.list')->with('success', 'Produto editado com sucesso.');
                //return response()->json(['message' => 'Produto criado com sucesso!', 'data' => $post], 201);
               
            } else {
                return redirect()->back()->with('error', 'Produto não encontrado.');
            }
        }else{
            return redirect()->back()->with('error', 'Usuário sem permissão para excluir.');
        }
    }


}
