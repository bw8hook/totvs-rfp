<x-app-layout>
    <div class="py-4" style=" padding-bottom: 130px;">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">

            <div id="titleComponent_KnowledgeBase" style=" display: inherit; padding-top: 30px; display: inherit; min-height: 97px; height: auto; margin-bottom: -16px !important;" class="text-lg font-bold flex items-center justify-between w-full px-4 space-x-2 relative" >
                <div class="AlignTitleLeft" style="width: 80%;">
                    <div class="flex" style="width: 100%;">
                        <img src="{{ asset('icons/file-ai.svg') }}" alt="Upload Icon" style="height: 33%; width:52px; padding-right: 18px;">
                        <span>Novo Projeto</span>
                    </div>
                    <div class="relative block items-center" style=" padding-bottom: 12px; margin-left: 8px; width: 90%;">        
                        <div class="info_details" style="color:#8A94AD;"> Vamos começar um novo projeto!<br/>
                        Dê um nome para a RFP e posteriormente vincule arquivos.</div>
                    </div>
                </div>

            </div>

                <form method="post" id="formID" action="{{ route('project.create') }}" class="bg-white rounded-lg shadow-md p-8 w-full flex flex-col" style=" margin-bottom: 100px; position:relative;">
                    @csrf

                    <div class="loading" style="display:none; background: #ffffffcf; position: absolute; width: 100%; height: 100%; top: 0px; left: 0px;  z-index: 9;">
                        <div id="lottie-container" style="width: 100px; height:100px; position: absolute; top: 50%; left: 50%; transform: translate(-75px, -35px);"></div>
                    </div>

   
                    <div class="flex flex-column items-center justify-center">

                        <div style="padding-top: 10px"></div>

                        <!-- Name -->
                        <div style="width: 35%; margin-bottom: 25px;">
                            <x-input-label for="name" :value="__('*Nome:')" />
                            <x-text-input id="name" class="formAddKnowledge" type="text" name="name" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            <p id="nameError" class="mt-2"></p> <!-- Exibe erro do JS -->
                        </div>

                        <!-- Name -->
                        <div style="width: 35%; margin-bottom: 40px;">
                            <x-input-label for="responsavel" :value="__('Responsável:')" />
                            <x-text-input id="responsavel" class="formAddKnowledge" type="text" name="responsavel" value="{{Auth::user()->name}}" required autofocus autocomplete="name" disabled/>
                            <x-input-error :messages="$errors->get('responsavel')" class="mt-2" />
                        </div>

                        <!-- Name -->
                        <div style="width: 35%; margin-bottom: 25px;">
                            <x-input-label for="data" :value="__('Data:')" />
                            <x-text-input id="data" class="formAddKnowledge" type="text" name="data" value="{{ now()->format('d/m/Y') }}" required autofocus autocomplete="name" disabled/>
                        </div>

                        <button type="submit" id="uploadButton" class="px-4 py-2" style="background-color: #5570F1; width: 300px; height: 58px; border-radius: 10px; color: white; font-weight:bold; font-size:22px;">Salvar Projeto</button>
                        <a href="{{route('project.list')}}" id="uploadButton" class="px-4 py-2" style="background-color: #E0E0E0; width: 160; height: 58px; border-radius: 10px; color:#525B75; margin-top:20px; box-shadow: 0px 19px 34px -20px #E0E0E0; font-weight:bold; font-size:22px; text-align: center;line-height: 44px;">Voltar</a>
                    </div>
                </div>

        </div>
    </div>
</x-app-layout>

<script>
    document.getElementById("formID").addEventListener("submit", function (event) {
        let nameField = document.getElementById("name");
        let nameError = document.getElementById("nameError");

        if (nameField.value.trim() === "") {
            event.preventDefault(); // Impede o envio do formulário
            nameError.innerText = "O nome é obrigatório!";
            nameError.style.color = "red";
        } else if (nameField.value.length < 3) {
            event.preventDefault();
            nameError.innerText = "O nome deve ter pelo menos 3 caracteres!";
            nameError.style.color = "red";
        } else {
            nameError.innerText = ""; // Limpa a mensagem de erro se estiver válido
        }
    });
</script>