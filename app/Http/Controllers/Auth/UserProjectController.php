<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UsersDepartaments;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;


class UserProjectController extends Controller
{

    public function filter(Request $request)
    {
        $query = User::query()->with('departament');

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

            if (in_array($sortBy, ['name', 'id', 'gestor','email', 'account_type', 'status', 'created_at']) && in_array($sortOrder, ['asc', 'desc'])) {
                $query->orderBy($sortBy, $sortOrder);
            }
        }

        // Paginação
        $users = $query->paginate(40);
        
        // Retornar dados em JSON
        return response()->json($users);
    }


    public function listUsers(Request $request): View
    {
          $AllUsers = User::all();
          $ListUsers = array();

          foreach ($AllUsers as $key => $User) {

                $user = User::find($User->id);
                $position = $user->userPosition;
            
                $ListUser = array();
                $ListUser['id'] = $User->id;
                $ListUser['nome'] = $User->name;
                $ListUser['email'] = $User->email;
                $ListUser['perfil'] = $User->role;
                $ListUser['position'] = $position;
                $ListUser['account_type'] = $User->account_type;
                $ListUser['updated_at'] = $User->updated_at;
                $ListUser['created_at'] = date("d/m/Y", strtotime($User->created_at));
                $ListUsers[] = $ListUser;
          }


          $data = array(
              'ListUsers' => $ListUsers,
          );
  
          //return view('auth.register')->with($data);

        return view('usersProject.list', ['user' => $request->user(), ])->with($data);
    }




    /**
     * Display the registration view.
     */
    public function create(): View
    {
        
        $UsersPosition = UsersDepartaments::all();
        $ListPositions = array();

        //$AgentId = Auth::user()->id;

        foreach ($UsersPosition as $key => $User) {
              $ListPosition = array();
              $ListPosition['id'] = $User->user_position_id;
              $ListPosition['position'] = $User->position;
              $ListPosition['type'] = $User->type_position;
              $ListPositions[] = $ListPosition;
        }

        $data = array(
            'title' => 'Todos Arquivos',
            'ListPositions' => $ListPositions,
        );


        //return view('auth.register')->with($data);

       return view('usersProject.register')->with($data);
    }


    /**
     * Display the registration view.
     */
    public function edit($id): View
    {
        $userDepartaments = UsersDepartaments::all();

        if(Auth::user()->role->role_priority >= 90 || Auth::user()->id == $id){
            $user = User::findOrFail($id);

            if ($user) {
                $data = array(
                    'user' => $user,
                    'userDepartaments' => $userDepartaments,
                );

                return view('usersProject.edit')->with($data);
                //return view('usersProject.edit')->with($user);
            } else {
                return redirect()->back()->with('error', 'Usuário não encontrado.');
            }
        }else{
            return redirect()->back()->with('error', 'Usuário sem permissão para editar.');
        }
    }
    

        /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request): RedirectResponse
    {
        $id= $request->id;

        // Encontre o registro existente
        $user = User::findOrFail($id);

        // Valide os dados do formulário
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                \Illuminate\Validation\Rule::unique('users')->ignore($user->id),
            ]
        ]);

        // Validação da imagem
        $request->validate([
            'profile_picture' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Verifica se o arquivo foi enviado
        if ($request->hasFile('profile_picture')) {
            $image = $request->file('profile_picture');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('public/uploads/profile', $filename);
            $relativePath = str_replace('public/', '', $path);

            $userUpdate = ([
                'profile_picture' => $relativePath,
                'name' => $request->name,
                'email' => $request->email,
                'idtotvs' => $request->idtotvs,
                'departament' => $request->departament[0],
                //'password' => Hash::make($request->password),
                'status' => "ativo",
                'account_type' => $request->account_type[0],
            ]);
        }else{
            $userUpdate = ([
                'profile_picture' => null,
                'name' => $request->name,
                'email' => $request->email,
                'idtotvs' => $request->idtotvs,
                'departament' => $request->departament[0],
                //'password' => Hash::make($request->password),
                'status' => "ativo",
                'account_type' => $request->account_type[0],
            ]);
        }


        

        // Atualize os dados do registro
        $user->update($userUpdate);
       
        // event(new Registered($user));
        // Auth::login($user);

        if($user){
            return redirect(route('listUsers', absolute: false))->with('success', 'Usuário Editado com sucesso.');
        } else {
            return redirect()->back()->with('error', 'Erro ao criar usuário.');
        }
        
       
    }


      /**
     * Display the registration view.
     */
    public function remove($id)
    {

        if(Auth::user()->role->role_priority >= 90){
            // Encontrar o usuário pelo ID
            $user = User::find($id);

            if ($user) {
                $user->delete(); // Exclui o usuário do banco de dados
                return redirect()->back()->with('success', 'Usuário excluído com sucesso.');
            } else {
                return redirect()->back()->with('error', 'Usuário não encontrado.');
            }
        }else{
            return redirect()->back()->with('error', 'Usuário sem permissão para editar.');
        }
    }


    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'agent' => $request->agent,
            'base' => $request->base,
            'role' => "user",
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('knowledge.list', absolute: false));
    }

    public function store2(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => "user",
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('login', absolute: false));
    }


   
    private function SendCurl($Type = "GET", $URL, $Data = null, $UserId){
        $curl = curl_init();

        if(empty($UserId)){
            $UserId = Auth::id();
        }
       
        $authorization = "Authorization: Bearer ".md5($UserId."_".date("Y-m-d"));

        curl_setopt_array($curl, array(
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $Type,
            CURLOPT_HTTPHEADER => array('Content-Type: application/json', $authorization),
        ));

        $response = curl_exec($curl);


        curl_close($curl);

        return json_decode($response);
    }
}
