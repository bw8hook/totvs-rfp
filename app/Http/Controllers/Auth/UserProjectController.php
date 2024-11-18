<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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
        // // BUSCA A LISTA DE AGENTS
        // $CountAgentsPage = 1;
        // $RetornoAgent = $this->SendCurl("GET", 'https://bw8.hook.app.br/arquivos-base-mentoria/api/agents?page='.$CountAgentsPage, null, 1);
        // $ListaAgents = array();

        // foreach ($RetornoAgent->Agents as $key => $Agent) {
        //     $ListaAgents[$Agent->agent_id] =  $Agent;
        // }
  
        // if($RetornoAgent->total > count($RetornoAgent->Agents)){
        //     $QtdPagesAgents = intval(round($RetornoAgent->total/20));  
        //     $CountAgentsPage++;

        //     while ($CountAgentsPage < $QtdPagesAgents) {
        //       $RetornoAgentWhile = $this->SendCurl("GET", 'https://bw8.hook.app.br/arquivos-base-mentoria/api/agents?page='.$CountAgentsPage, null, 1);
        //       foreach ($RetornoAgentWhile->Agents as $key => $Agent) {
        //         $ListaBases[$Agent->agent_id] =  $Agent;
        //       }
        //       $CountAgentsPage++;
        //     }
        // }
  
  
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

        $data = array(
            'title' => 'Todos Arquivos',
            // 'listaAgents' => $ListaAgents,
            // 'listaBases' => $ListaBases
        );

        return view('usersProject.register')->with($data);
    }


      /**
     * Display the registration view.
     */
    public function edit($id): View
    {

        $user = User::findOrFail($id);
       
        // Recupera o usuário com base no ID
        //$user = User::find($id)->toArray();

        if ($user) {
            return view('usersProject.edit', compact('user'));
            //return view('usersProject.edit')->with($user);
        } else {
            return redirect()->back()->with('error', 'Usuário não encontrado.');
        }


        // $data = array(
        //     'title' => 'Todos Arquivos',
        //     // 'listaAgents' => $ListaAgents,
        //     // 'listaBases' => $ListaBases
        // );

        // return view('usersProject.edit')->with($data);
    }

      /**
     * Display the registration view.
     */
    public function remove(): View
    {

        $data = array(
            'title' => 'Todos Arquivos',
            // 'listaAgents' => $ListaAgents,
            // 'listaBases' => $ListaBases
        );

        return view('usersProject.remove')->with($data);
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

        return redirect(route('newproject', absolute: false));
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
