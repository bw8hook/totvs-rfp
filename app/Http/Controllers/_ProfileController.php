<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Controllers\Users;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
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
  
              while ($CountAgentsPage < $QtdPagesAgents) {
                  $CountAgentsPage++;
                  $RetornoAgentWhile = $this->SendCurl("GET", 'https://bw8.hook.app.br/arquivos-base-mentoria/api/agents?page='.$CountAgentsPage, null, 1);
                  foreach ($RetornoAgentWhile->Agents as $key => $Agent) {
                      $ListaBases[] =  $Agent;
                  }
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
  
              while ($CountBasesPage < $QtdPagesBases) {
                  $CountBasesPage++;
                  $RetornoBaseWhile = $this->SendCurl("GET", 'https://bw8.hook.app.br/arquivos-base-mentoria/api/bases?page='.$CountBasesPage, null, 1);
                  foreach ($RetornoBaseWhile->Bases as $key => $Base) {
                      if($Base->status == "succeeded"){
                         $ListaBases[] =  $Base;
                      }
                  }
              }
          }
  
          $AgentId = Auth::user()->agent;
          $BaseId = Auth::user()->base;

          $data = array(
              'title' => 'Todos Arquivos',
              'listaAgents' => $ListaAgents,
              'listaBases' => $ListaBases,
              'AgentId' => $AgentId,
              'BaseId' => $BaseId
          );
  
          //return view('auth.register')->with($data);



        return view('profile.edit', ['user' => $request->user(), ])->with($data);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->agent = $request->agent;
        $request->user()->base = $request->base;

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }



    public function listUsers(Request $request): View
    {
        // BUSCA A LISTA DE AGENTS
        $CountAgentsPage = 1;
        $RetornoAgent = $this->SendCurl("GET", 'https://bw8.hook.app.br/arquivos-base-mentoria/api/agents?page='.$CountAgentsPage);
        $ListaAgents = array();

        foreach ($RetornoAgent->Agents as $key => $Agent) {
            $ListaAgents[$Agent->agent_id] =  $Agent;
        }
  
        if($RetornoAgent->total > count($RetornoAgent->Agents)){
            $QtdPagesAgents = intval(round($RetornoAgent->total/20));         
            while ($CountAgentsPage < $QtdPagesAgents) {
              $CountAgentsPage++;
              $RetornoAgentWhile = $this->SendCurl("GET", 'https://bw8.hook.app.br/arquivos-base-mentoria/api/agents?page='.$CountAgentsPage);
              foreach ($RetornoAgentWhile->Agents as $key => $Agent) {
                $ListaBases[$Agent->agent_id] =  $Agent;
              }
            }
        }
  
        // BUSCA A LISTA DE BASES
        $CountBasesPage = 1;
        $RetornoBase = $this->SendCurl("GET", 'https://bw8.hook.app.br/arquivos-base-mentoria/api/bases?page='.$CountBasesPage);
        $ListaBases = array();
          
        foreach ($RetornoBase->Bases as $key => $Base) {
            if($Base->status == "succeeded"){
               $ListaBases[$Base->base_id] =  $Base;
            }
        }
  
        if($RetornoBase->total > count($RetornoBase->Bases)){
            $QtdPagesBases = intval(round($RetornoBase->total/20));         
            while ($CountBasesPage < $QtdPagesBases) {
                $CountBasesPage++;
                $RetornoBaseWhile = $this->SendCurl("GET", 'https://bw8.hook.app.br/arquivos-base-mentoria/api/bases?page='.$CountBasesPage);
                foreach ($RetornoBaseWhile->Bases as $key => $Base) {
                    if($Base->status == "succeeded"){
                       $ListaBases[$Base->base_id] =  $Base;
                    }
                }
            }
          }
  
          $AllUsers = User::all();
          $ListUsers = array();

          foreach ($AllUsers as $key => $User) {
                
                //dd($ListaBases[$User->base]);

                //$key = array_search('green', $ListaAgents); // $key = 2;
                $ListUser = array();
                $ListUser['id'] = $User->id;
                $ListUser['nome'] = $User->name;
                $ListUser['email'] = $User->email;
                $ListUser['perfil'] = $User->role;
                $ListUser['agente'] = $ListaAgents[$User->agent]->name;
                $ListUser['agente_status'] = $ListaAgents[$User->agent]->status;
                $ListUser['base'] = $ListaBases[$User->base]->description;
                $ListUser['base_status'] = $ListaBases[$User->base]->status;

                $ListUsers[] = $ListUser;
          }

        

          $data = array(
              'title' => 'Todos Arquivos',
              'ListUsers' => $ListUsers,
          );
  
          //return view('auth.register')->with($data);

        return view('users.list', ['user' => $request->user(), ])->with($data);
    }




    function _isCurl(){
        return function_exists('curl_version');
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
