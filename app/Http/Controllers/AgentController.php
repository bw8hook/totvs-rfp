<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\Models\Diretrizes;

class AgentController extends Controller
{
    public function index(){

       // BUSCA A LISTA DE AGENTS
       $CountAgentsPage = 1;
       $RetornoAgent = $this->SendCurl("GET", 'https://bw8.hook.app.br/arquivos-base-mentoria/api/agents?page='.$CountAgentsPage,null, 1);
       $ListaAgents = array();

       foreach ($RetornoAgent->Agents as $key => $Agent) {
           $ListaAgents[$Agent->agent_id] =  $Agent;
       }
 
       if($RetornoAgent->total > count($RetornoAgent->Agents)){
           $QtdPagesAgents = intval(round($RetornoAgent->total/20));         
           while ($CountAgentsPage < $QtdPagesAgents) {
             $CountAgentsPage++;
             $RetornoAgentWhile = $this->SendCurl("GET", 'https://bw8.hook.app.br/arquivos-base-mentoria/api/agents?page='.$CountAgentsPage,null, 1);
             foreach ($RetornoAgentWhile->Agents as $key => $Agent) {
               $ListaBases[$Agent->agent_id] =  $Agent;
             }
           }
       }
 
        return view('agent.dashboard')->with($data);

      }

      public function gerenciar($Idagente){

        $AllDiretrizes = Diretrizes::where('agente', Auth::user()->agent)->get();

        $Diretrizes = array();

        foreach ($AllDiretrizes as $key => $DadosDiretriz) {
          $Diretriz = array();
          $Diretriz['id'] = $DadosDiretriz->id;
          $Diretriz['diretriz'] = $DadosDiretriz->diretriz;
          $Diretriz['usuario'] = $DadosDiretriz->idusuario;
          $Diretriz['status'] = $DadosDiretriz->status;
          $Diretrizes[] = $Diretriz;
        }


        $data = array(
          'title' => 'Gerenciar Minha IA',
          'Idagente' => $Idagente,
          'Diretrizes' => $Diretrizes,
        );

        
        //$directories = Storage::disk('public');

        // $userId = Auth::id();
        // $AgenteId = Auth::user()->agent;
        
        // $directory =  'public/uploads/'.$userId."/diretrizes/diretriz_db.txt";
        // $fileDownload = str_replace("public", "storage", $directory);
        // $fh = fopen($fileDownload,'r');
        
        
        // while ($line = fgets($fh)) {
        //   echo($line);
        //   echo('<br/>');
        // }
        // fclose($fh);



        return view('agent.gerenciar')->with($data);

     }


    public function diretriz(Request $request){
        $Campos = $request->request->all();

        if(!empty($Campos['Diretriz'])){
          foreach ($Campos['Diretriz'] as $keyDiretriz => $Diretriz) {
            $DiretrizBd = Diretrizes::find($keyDiretriz);
            $DiretrizBd->diretriz = $Diretriz;
            $DiretrizBd->save();
          }
        }

        if(!empty($Campos['NovaDiretriz'])){
          foreach ($Campos['NovaDiretriz'] as $keyNova => $NovaDiretriz) {
            $AddDiretriz = new Diretrizes;
            $AddDiretriz->diretriz = $NovaDiretriz;
            $AddDiretriz->agente = $Campos['agente'];
            $AddDiretriz->idusuario = Auth::user()->id;
            $AddDiretriz->status = "ativo";
            $AddDiretriz->save();
          }
        }


        $AllDiretrizes = Diretrizes::where('agente', $Campos['agente'])->get();

        $ListaDiretrizes = array();

        $TextoDiretrizes = '';

        foreach ($AllDiretrizes as $key => $DadosDiretriz) {
          $TextoDiretrizes .= ' - '.$DadosDiretriz->diretriz;
          $TextoDiretrizes .= "\r";
        }

        $NewDirectory =  'storage/uploads/'.Auth::user()->id."/diretriz.txt";
        $BaseDirectory =  'storage/diretriz_base.txt';

        $fileContents = file_get_contents($BaseDirectory);

        $CopyFile = copy($BaseDirectory, $NewDirectory);
        
        $fileHandle = fopen($NewDirectory, "r+");
        fputs($fileHandle, $fileContents);
        fclose($fileHandle);

       // Vamos garantir que o arquivo existe e pode ser escrito
      if (is_writable($NewDirectory)) {
        if (!$fp = fopen($NewDirectory, 'a')) {
            echo "Erro ao abrir o ($NewDirectory)";
            exit;
        }
        if (fwrite($fp, $TextoDiretrizes) === FALSE) {
            echo "Erro ao escrever no arquivo ($NewDirectory)";
            exit;
        }
        fclose($fp);

      } else {
        echo "O arquivo não permite escrita";
      }

      $Data = array();
      $Data['agent_id'] = Auth::user()->agent;
      $Data['base_id'] = Auth::user()->base;

      $userId = Auth::id();
      $directory =  'public/uploads/'.$userId."/";
      $storage = Storage::allFiles($directory);
      
      $Files = array();
      foreach ($storage as $key => $file) {
        $FileClean = str_replace($directory, "", $file);
        $FileClean = str_replace("document/", "", $FileClean);
        $FileClean = str_replace("image/", "", $FileClean);

        $ExtUrl = str_replace("public", "storage", $file);

        $ext = pathinfo($file, PATHINFO_EXTENSION);

        $Arquivo['url'] = 'https://ia.hook.app.br/'.$ExtUrl;
        $Arquivo['ext'] = $ext;
        $Arquivo['size'] = Storage::size($file);;
        $Arquivo['title'] = $FileClean;
        $Arquivo['description'] = $FileClean;

        $Files[] = $Arquivo;

      }

      $Data['arquivos'] = $Files;

      $UpdateDataAgent = $this->SendCurl("POST", 'https://bw8.hook.app.br/arquivos-base-mentoria/api/base-file', $Data, Auth::user()->id);



      // $GetDataAgent = $this->SendCurl("GET", 'https://bw8.hook.app.br/arquivos-base-mentoria/api/agent/'.$Campos['agente'],null, Auth::user()->id);     

      // $UpdateAgent = array();
      // $UpdateAgent['name'] = $GetDataAgent->Agent->name;
      // //$UpdateAgent['description'] = $GetDataAgent->Agent->description;
      // $UpdateAgent['instructions'] = $TextoDiretrizes;
      // $UpdateAgent['status'] = "online";
      // $UpdateAgent['behavior_type'] = "custom";
      // $UpdateAgent['bases'][] = Auth::user()->base;

      // $UpdateDataAgent = $this->SendCurl("POST", 'https://bw8.hook.app.br/arquivos-base-mentoria/api/agent-update/'.$Campos['agente'], $UpdateAgent, Auth::user()->id);
      
      return redirect()->back()->with('success','Upload realizado com sucesso.');
    }



      public function assistente($Idagente){
          $userId = Auth::id();
          $hash = hash('sha256', $userId);
          $url = 'https://dev-ia2.hook.app.br/api/chatbotia/chatbots/chatbots/user/' . $hash;

          $ch = curl_init();

          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

          $response = curl_exec($ch);

          if (curl_errno($ch)) {
              dump('Error:' . curl_error($ch));
          }

          curl_close($ch);

          $dataArray = json_decode($response, true);

          if (!isset($dataArray['error']) && json_last_error() === JSON_ERROR_NONE) {
              $firstItem = $dataArray[0];
              $userIdHash = $firstItem['userId'];

              $data = array(
                'hash' => $hash,
                'userId' => $userId
              );

              return view('gpt')->with($data);
          }
          else {
              $data = array(
                'title' => 'Minha IA',
                'Idagente' => $Idagente
              );
              return view('agent.assistente')->with($data);
          }
          return view('agent.assistente')->with($data);
       }

      public function edit($idAgente){

        // BUSCA A LISTA DE AGENTS
        $CountAgentsPage = 1;
        $RetornoAgent = $this->SendCurl("GET", 'https://bw8.hook.app.br/arquivos-base-mentoria/api/agent/'.$idAgente,null, 1);
        
        $DadosAgente = $RetornoAgent->Agent;

        $data = array(
          'nome' => $DadosAgente->name,
          'descricao' => $DadosAgente->description,
          'avatar' => $DadosAgente->avatar,
          'cor' => $DadosAgente->color,
          'bemvindos' => $DadosAgente->welcome,
          'instrucoes' => $DadosAgente->instructions,
          'status' => $DadosAgente->status,
        );


         return view('agent.edit')->with($data);
       }



      private function SendCurl($Type = "GET", $URL, $Data = null, $UserId){
        $curl = curl_init();

        if(empty($UserId)){
            $UserId = Auth::id();
        }

        $UserId = 1;
 
       
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

        if(!empty($Data)){
          curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
          curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($Data));
        }

        
        
        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }

}
