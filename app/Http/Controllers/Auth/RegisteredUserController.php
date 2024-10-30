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

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {

        // BUSCA A LISTA DE AGENTS

        $CountAgentsPage = 1;
        $RetornoAgent = $this->SendCurl("GET", 'https://bw8.hook.app.br/arquivos-base-mentoria/api/agents?page='.$CountAgentsPage, null, 1);
        $ListaAgents = array();
        
        foreach ($RetornoAgent->Agents as $key => $Agent) {
            $ListaAgents[] =  $Agent;
        }

        if($RetornoAgent->total > count($RetornoAgent->Agents)){
            $QtdPagesAgents = intval(round($RetornoAgent->total/20));
            $CountAgentsPage++;
            
            while ($CountAgentsPage <= $QtdPagesAgents) {
                $RetornoAgentWhile = $this->SendCurl("GET", 'https://bw8.hook.app.br/arquivos-base-mentoria/api/agents?page='.$CountAgentsPage, null, 1);
    
                foreach ($RetornoAgentWhile->Agents as $key => $Agent) {
                    $ListaBases[] =  $Agent;
                }
                $CountAgentsPage++;
            }
        }

        // BUSCA A LISTA DE BASES

        $CountBasesPage = 1;
        $RetornoBase = $this->SendCurl("GET", 'https://bw8.hook.app.br/arquivos-base-mentoria/api/bases?page='.$CountBasesPage, null, 1);
        $ListaBases = array();
        
        foreach ($RetornoBase->Bases as $key => $Base) {
            if($Base->status == "succeeded"){
               $ListaBases[] =  $Base;
           }
        }

        if($RetornoBase->total > count($RetornoBase->Bases)){
            $QtdPagesBases = intval(round($RetornoBase->total/20));    
            $CountBasesPage++;

            while ($CountBasesPage <= $QtdPagesBases) {
                $RetornoBaseWhile = $this->SendCurl("GET", 'https://bw8.hook.app.br/arquivos-base-mentoria/api/bases?page='.$CountBasesPage, null, 1);
                foreach ($RetornoBaseWhile->Bases as $key => $Base) {
                    if($Base->status == "succeeded"){
                       $ListaBases[] =  $Base;
                    }
                }
                $CountBasesPage++;
            }
        }


        $data = array(
            'title' => 'Todos Arquivos',
            'listaAgents' => $ListaAgents,
            'listaBases' => $ListaBases
        );

        return view('auth.register')->with($data);
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
