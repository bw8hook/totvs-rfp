<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\User;
use App\Models\RfpBundle;
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
        $query = RfpBundle::query();

        // Aplicar filtros
        if ($request->has('filter')) {
            foreach ($request->filter as $field => $value) {
                if (!empty($value)) {
                    $query->where($field, 'like', '%' . $value . '%');
                }
            }
        }

        // Aplicar ordenação
        if ($request->has('sort_by') && $request->has('sort_order')) {
            $sortBy = $request->sort_by;
            $sortOrder = $request->sort_order;

            if (in_array($sortBy, ['bundle', 'bundle_id', 'created_at']) && in_array($sortOrder, ['asc', 'desc'])) {
                $query->orderBy($sortBy, $sortOrder);
            }
        }

        dd($query);
        // Paginação
        $users = $query->paginate(40);

        // Retornar dados em JSON
        return response()->json($users);
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
                $ListBundle['color'] = $Bundle->bundle_color;
                if(isset($Bundle->created_at)){
                    $ListBundle['created_at'] = date("d/m/Y", strtotime($Bundle->created_at));
                }else{
                    $ListBundle['created_at'] = " - ";
                }
               
                $ListBundles[] = $ListBundle;
          }


          $data = array(
              'ListBundles' => $ListBundles,
          );
  
          //return view('auth.register')->with($data);

        return view('bundles.list', ['user' => $request->user(), ])->with($data);
    }




    /**
     * Display the registration view.
     */
    public function create(): View
    {

        //$AgentId = Auth::user()->id;

        $data = array(
            'title' => 'Todos Arquivos',
        );


        //return view('auth.register')->with($data);

       return view('bundles.create')->with($data);
    }



      /**
     * Display the registration view.
     */
    public function edit($id): View
    {
        if(Auth::user()->role->role_priority >= 90){
            $user = RfpBundle::where('bundle_id', $id)->firstOrFail();

            if ($user) {
                return view('bundles.edit', compact('user'));
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
        if(Auth::user()->role->role_priority >= 90){
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
        if(Auth::user()->role->role_priority >= 90){
            // Validação dos dados
            $validatedData = $request->validate([
                'name' => 'required|string|max:255'
            ]);

            // Criar o novo registro no banco de dados
            $post = RfpBundle::create([
                'bundle' => $validatedData['name'],
            ]);

            if ($post) {
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
    public function edit_user(Request $request)
    {
        if(Auth::user()->role->role_priority >= 90){
            // Validação dos dados
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'bundle_id' => 'required'
            ]);

            $produto = RfpBundle::where('bundle_id', $validatedData['bundle_id'])->firstOrFail(); // Encontra o registro pelo ID
           
            if ($produto) {
                $produto->bundle =  $validatedData['name'];
                $produto->save(); // Salva as alterações no banco
                
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
