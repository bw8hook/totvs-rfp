<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __($title) }}
        </h2>

        <!-- Trigger for Modal -->
        <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModal" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150" style="background: #3F51B5; position: relative; float: right; top: -30px;">Dicas para dar Diretrizes</button>
      
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" style="width: 100%; max-width: 780px; margin: auto;">
                <div class="col-12">
                    <div class="card ">
                        <div class="card-body" style="width: 100%; max-width: 780px;">                    

                    
                        <form method="post" action="{{ route('diretriz') }}" class="mt-6 space-y-6" id="CardForm" style="padding: 30px; padding-top: 0px; margin-top: -10px; overflow: hidden;">
                            @csrf
                           
                            <input type="hidden" name="agente" value="{{$Idagente}}" />

                                <div id="ListInputs">
                                    @foreach($Diretrizes as $key=>$Diretriz)
                                        <div class="diretriz">
                                            <x-input-label for="name" value="Diretriz {{$key+1}}:" /> 
                                            <textarea id="inputDescriptionEs" name="Diretriz[{{$Diretriz['id']}}]"  class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" name="description_es" required>{{$Diretriz['diretriz']}}</textarea>
                                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                        </div>
                                    @endforeach
                                </div>
                                
                                 <div class="sticky-top" style=" width: 100%; height: 39px;">
                                    <button id="rowAdder" class="btn btn-success" style="float:left; position:relative;">Adicionar Nova Diretriz</button>
                                    <button type="submit" class="btn btn-primary" style="float:right; position:relative;">Salvar</button>
                                </div>

                            </form>



                                
        
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

   <!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="exampleModalLabel"> <strong>Diretrizes para uma inteligência artificial (IA) envolve fornecer instruções claras e específicas sobre o que você deseja que a IA faça e como ela deve fazer isso. Aqui estão algumas etapas para dar diretrizes eficazes para uma IA:</strong></h3>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>

            <li style="margin:10px;"> Responda sempre com respostas curtas, com no máximo 1 parágrafo pequeno.</li>
            <li style="margin:10px;"> Mantenha o tom profissional e amigável.</li>
            <li style="margin:10px;"> Tenha um engajamento ativo.</li>
            <li style="margin:10px;"> Você é um assistente da empresa HOOK IA e sabe responder todas as perguntas sobre a empresa. </li>
            <li style="margin:10px;"> Faça perguntas para o usuário para ele se sentir engajado na conversa. Não repita perguntas que já foram feitas anteriormente.</li>
            <li style="margin:10px;"> Sempre responda primeiro com uma frase curta e objetiva, depois complemente com uma resposta longa e completa. </li>
            <li style="margin:10px;"> No final de todas as respostas, pergunte se conseguiu ajudar e se ficou clara a resposta que você enviou.</li>
            <li style="margin:10px;"> Use listas no formato de bullet points para organizar os itens. </li>
            <li style="margin:10px;"> Se o visitante perguntar sobre planos ou valores, envie esse link: https://www.hook.app.br/.</li>
        </p>
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Entendi</button>
      </div>
    </div>
  </div>
</div>







</x-app-layout>

