<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UsersDepartaments;
use App\Models\UserRole;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use App\Mail\NewUserEmail;
use Illuminate\Support\Facades\Mail;




class UserProjectController extends Controller
{
    public function filter(Request $request)
    {
        // Iniciar a query
         $query = User::query()->with(['departament']);

        // Aplicar filtros
        if ($request->has('nome') && !empty($request->nome)) {
            $query->where('name', 'like', '%' . $request->nome . '%');
        }

        if ($request->has('id_totvs') && !empty($request->id_totvs)) {
            $query->where('idtotvs', 'like', '%' . $request->id_totvs . '%');
        }

        if ($request->has('position') && !empty($request->position)) {
            $query->where('position', 'like', '%' . $request->position . '%');
        }

        if ($request->has('departament') && $request->departament != "null") {
            $query->where('departament_id', 'like', '%' . $request->departament . '%');
        }

        $query->where('status', 'like', '%' . $request->user_active . '%');
        
        if ($request->has('role') && $request->role != "null") {

            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->role . '%');
            });
        }


        // Paginação e execução da query
        $users = $query->paginate(40);

        // Adicionar role_names após a paginação
        $users->through(function ($user) {
            $user->role_names = $user->getRoleNames();
            return $user;
        });

        // Retornar dados em JSON
        return response()->json($users);
    }


    public function listUsers(Request $request): View
    {
          $AllUsers = User::all();
          $ListUsers = array();
          $permissionNames = Auth::user()->getPermissionsViaRoles();
          $AllRoles = Role::with('permissions')->get();
          $AllDepartaments = UsersDepartaments::all();


          foreach ($AllUsers as $key => $User) {
                $user = User::find($User->id);
                $roles = $user->getRoleNames();

                $ListUser = array();
                $ListUser['id'] = $User->id;
                $ListUser['nome'] = $User->name;
                $ListUser['email'] = $User->email;
                $ListUser['idtotvs'] = $User->idtotvs;
                $ListUser['departamento'] = $user->departament->departament;;
                $ListUser['status'] = $User->status;
                $ListUser['roles'] = $roles;
                $ListUser['updated_at'] = $User->updated_at;
                $ListUser['created_at'] = date("d/m/Y", strtotime($User->created_at));

                $ListUsers[] = $ListUser;
          }

          $data = array(
              'ListUsers' => $ListUsers,
              'AllRoles' => $AllRoles,
              'AllDepartaments' => $AllDepartaments
          );
  
          //return view('auth.register')->with($data);

        return view('usersProject.list', ['user' => $request->user(), ])->with($data);
    }




    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $userDepartaments = UsersDepartaments::all();
        $roles = Role::with('permissions')->get();

        $data = array(
            'userDepartaments' => $userDepartaments,
            'roles' => $roles,
        );
            
        return view('usersProject.register')->with($data);
            
    }


    /**
     * Display the registration view.
     */
    public function edit($id): View|RedirectResponse
    {
        $userDepartaments = UsersDepartaments::all();
        $roles = Role::with('permissions')->get();

        //if(Auth::user()->can('users.manage')){
            $user = User::findOrFail($id);
            $role = $user->getRoleNames();

            if ($user) {
                $data = array(
                    'user' => $user,
                    'userDepartaments' => $userDepartaments,
                    'roles' => $roles,
                    'user_role' => $role,
                );
                return view('usersProject.edit')->with($data);
                //return view('usersProject.edit')->with($user);
            } else {
                return redirect(route('users.list', absolute: false))->with('error', 'Usuário sem permissão para editar.');
            }
        //}else{
        //    return redirect(route('users.list', absolute: false))->with('error', 'Usuário sem permissão para editar.');
        //}
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
            $filePath = 'cdn/profile';
            $UploadedFile = Storage::disk('s3')->put($filePath, $image);
            $relativePath = Storage::disk('s3')->url($UploadedFile);

            $userUpdate = ([
                'profile_picture' => $relativePath,
                'name' => $request->name,
                'email' => $request->email,
                'idtotvs' => $request->idtotvs,
                'position' => $request->position,
                'departament' => $request->departament[0],
                //'password' => Hash::make($request->password),
            ]);
        }else{
            $userUpdate = ([
                'profile_picture' => null,
                'name' => $request->name,
                'email' => $request->email,
                'idtotvs' => $request->idtotvs,
                'departament' => $request->departament[0],
                'position' => $request->position,
                //'password' => Hash::make($request->password),
            ]);
        }

        $user->syncRoles([$request->account_type[0]]);
        // Atualize os dados do registro

        $user->update($userUpdate);
       
        // event(new Registered($user));
        // Auth::login($user);

        if($user){
            return redirect(route('users.list', absolute: false))->with('success', 'Usuário Editado com sucesso.');
        } else {
            return redirect()->back()->with('error', 'Erro ao criar usuário.');
        }
        
       
    }


      /**
     * Display the registration view.
     */
    public function remove($id)
    {

        if (Auth::user()->hasRole('Administrador')) {
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
        ]);

        $HashPassword = Str::random(12);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($HashPassword),
            'idtotvs' => $request->idtotvs,
            'departament_id' => $request->departament[0],
            'user_role_id' => 1,
        ]);

        $user->syncRoles([$request->account_type[0]]);


        if($user){
            $data = [
                'name' => $request->name,
                'message' => 'Sua senha de acesso é '.$HashPassword
            ];
        
            Mail::to($request->email)->send(new NewUserEmail($data));

            return redirect(route('users.list', absolute: false));
        }

        

       // event(new Registered($user));
       // Auth::login($user);

       
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
