<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\ProfileUpdateUserRequest;
use App\Http\Controllers\Users;
use App\Models\User;
use App\Models\UsersDepartaments;
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
        $userDepartaments = UsersDepartaments::all();

            $user = User::findOrFail(Auth::user()->id);

            if ($user) {
                $data = array(
                    'user' => $user,
                    'userDepartaments' => $userDepartaments,
                );

                return view('profile.edit', ['user' => $request->user(), ])->with($data);
                //return view('usersProject.edit')->with($user);
            } else {
                return redirect()->back()->with('error', 'Usuário não encontrado.');
            }
       


        //   // BUSCA A LISTA DE AGENTS
        //   $CountAgentsPage = 1;
        //   $RetornoAgent = $this->SendCurl("GET", 'https://bw8.hook.app.br/arquivos-base-mentoria/api/agents?page='.$CountAgentsPage, null, 1);
        //   $ListaAgents = array();
          
        //   foreach ($RetornoAgent->Agents as $key => $Agent) {
        //       $ListaAgents[] =  $Agent;
        //   }
  
        //   if($RetornoAgent->total > count($RetornoAgent->Agents)){
        //       $QtdPagesAgents = intval(round($RetornoAgent->total/20));         
        //       $CountAgentsPage++;

        //       while ($CountAgentsPage <= $QtdPagesAgents) {
        //           $RetornoAgentWhile = $this->SendCurl("GET", 'https://bw8.hook.app.br/arquivos-base-mentoria/api/agents?page='.$CountAgentsPage, null, 1);
        //           foreach ($RetornoAgentWhile->Agents as $key => $Agent) {
        //               $ListaBases[] =  $Agent;
        //           }
        //           $CountAgentsPage++;
        //       }
        //   }
  
        //   // BUSCA A LISTA DE BASES
  
        //   $CountBasesPage = 1;
        //   $RetornoBase = $this->SendCurl("GET", 'https://bw8.hook.app.br/arquivos-base-mentoria/api/bases?page='.$CountBasesPage, null, 1);
        //   $ListaBases = array();
          
        //   foreach ($RetornoBase->Bases as $key => $Base) {
        //       if($Base->status == "succeeded"){
        //          $ListaBases[] =  $Base;
        //      }
        //   }
  
        //   if($RetornoBase->total > count($RetornoBase->Bases)){
        //       $QtdPagesBases = intval(round($RetornoBase->total/20));         
        //       $CountBasesPage++;

        //       while ($CountBasesPage <= $QtdPagesBases) {
        //           $RetornoBaseWhile = $this->SendCurl("GET", 'https://bw8.hook.app.br/arquivos-base-mentoria/api/bases?page='.$CountBasesPage, null, 1);
        //           foreach ($RetornoBaseWhile->Bases as $key => $Base) {
        //               if($Base->status == "succeeded"){
        //                  $ListaBases[] =  $Base;
        //               }
        //           }
        //           $CountBasesPage++;
        //       }
        //   }
  
        //   $AgentId = Auth::user()->agent;
        //   $BaseId = Auth::user()->base;
        //   $userId = Auth::user()->id;

        //   $data = array(
        //       'title' => 'Todos Arquivos',
        //       'listaAgents' => $ListaAgents,
        //       'listaBases' => $ListaBases,
        //       'AgentId' => $AgentId,
        //       'BaseId' => $BaseId,
        //       'userId' => $userId
        //   );
  
          //return view('auth.register')->with($data);



       
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

        if(!empty($request->agent)){
            $request->user()->agent = $request->agent;
        }

        if(!empty($request->base)){
            $request->user()->base = $request->base;
        }

       
      

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }


     /**
     * Update the user's profile information.
     */
    public function updateUser(ProfileUpdateUserRequest $request): RedirectResponse
    {

        // dd('teste');

        $request->user()->fill($request->validated());

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
        $RetornoAgent = $this->SendCurl("GET", 'https://bw8.hook.app.br/arquivos-base-mentoria/api/agents?page='.$CountAgentsPage, null, 1);
        $ListaAgents = array();

        foreach ($RetornoAgent->Agents as $key => $Agent) {
            $ListaAgents[$Agent->agent_id] =  $Agent;
        }
  
        if($RetornoAgent->total > count($RetornoAgent->Agents)){
            $QtdPagesAgents = intval(round($RetornoAgent->total/20));  
            $CountAgentsPage++;

            while ($CountAgentsPage < $QtdPagesAgents) {
              $RetornoAgentWhile = $this->SendCurl("GET", 'https://bw8.hook.app.br/arquivos-base-mentoria/api/agents?page='.$CountAgentsPage, null, 1);
              foreach ($RetornoAgentWhile->Agents as $key => $Agent) {
                $ListaBases[$Agent->agent_id] =  $Agent;
              }
              $CountAgentsPage++;
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
                $ListUser['agente'] = isset($ListaAgents[$User->agent]->name) ? $ListaAgents[$User->agent]->name : '';
                $ListUser['agente_status'] = isset($ListaAgents[$User->agent]->status) ? $ListaAgents[$User->agent]->status : '';
                //$ListUser['base'] = $ListaBases[$User->base]->description;
                //$ListUser['base_status'] = $ListaBases[$User->base]->status;

                $ListUsers[] = $ListUser;
          }


          $data = array(
              'ListUsers' => $ListUsers,
          );
  
          //return view('auth.register')->with($data);

        return view('profile.list', ['user' => $request->user(), ])->with($data);
    }



    public function editUser(Request $request, $Id = null): View
    {
          // BUSCA A LISTA DE AGENTS
          $ListUser = User::find($Id);
          //$users = User::where('id', $Id)->get();

          $CountAgentsPage = 1;
          $RetornoAgent = $this->SendCurl("GET", 'https://bw8.hook.app.br/arquivos-base-mentoria/api/agents?page='.$CountAgentsPage, null, 1);
          $ListaAgents = array();
          
          foreach ($RetornoAgent->Agents as $key => $Agent) {
              $ListaAgents[] =  $Agent;
          }
  
          if($RetornoAgent->total > count($RetornoAgent->Agents)){
              $QtdPagesAgents = intval(round($RetornoAgent->total/20));         
              $CountAgentsPage++;

              while ($CountAgentsPage < $QtdPagesAgents) {
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

              while ($CountBasesPage < $QtdPagesBases) {
                  $RetornoBaseWhile = $this->SendCurl("GET", 'https://bw8.hook.app.br/arquivos-base-mentoria/api/bases?page='.$CountBasesPage, null, 1);
                  foreach ($RetornoBaseWhile->Bases as $key => $Base) {
                      if($Base->status == "succeeded"){
                         $ListaBases[] =  $Base;
                      }
                  }
                  $CountBasesPage++;
              }
          }
  
          $AgentId = Auth::user()->agent;
          $BaseId = Auth::user()->base;

          

          $data = array(
              'Name' => $ListUser->name,
              'Email' => $ListUser->email,
              'AgentId' => $ListUser->agent,
              'BaseId' => $ListUser->base,
              'Role' => $ListUser->role,
              'listaAgents' => $ListaAgents,
              'listaBases' => $ListaBases
          );
  
          //return view('auth.register')->with($data);



        return view('users.edit', ['user' => $request->user(), ])->with($data);
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
