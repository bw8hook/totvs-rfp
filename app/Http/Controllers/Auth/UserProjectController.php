<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UsersPosition;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class UserProjectController extends Controller
{

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
        
        $UsersPosition = UsersPosition::all();
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

        if(Auth::user()->account_type == "admin"){
            $user = User::findOrFail($id);

            if ($user) {
                return view('usersProject.edit', compact('user'));
                //return view('usersProject.edit')->with($user);
            } else {
                return redirect()->back()->with('error', 'Usuário não encontrado.');
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

        if(Auth::user()->account_type == "admin"){
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
