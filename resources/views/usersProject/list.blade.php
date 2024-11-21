<x-app-layout>
    <div class="py-12" style=" padding-bottom: 130px;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-title-component :showButton="true" textButton="Adicionar Usuário" urlButton="new-user" > {{ __('Usuários Cadastrados') }} </x-title-component>

            <div id="BlocoLista" class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">

                <div class="tabela">
                    <div class="header_tabela">
                        <div style="width:30%;">Nome do usuário</div>
                        <div style="width:10%; text-align:center;">Tipo da Conta</div>
                        <div style="width:15%; text-align:center;">Email Verificado</div>
                        <div style="width:20%; text-align:center;">Cargo</div>
                        <div style="width:20%; text-align:center;">Data do Cadastro</div>
                        <div style="width:10%; text-align:center;">Ações</div>           
                    </div>
                    <div>
                        @foreach($ListUsers as $User)
                        <div class="listaTabela">
                            <div style="width:30%;">{{$User['nome']}}</div>
                            
                            <div style="width:10%; text-align:center;">
                                @if ($User['account_type'] == "admin")
                                    <div style="font-size: 11px;  margin:auto; background: #2196F3; line-height: 30px; width: 70px; text-align: center; color: #FFF; border-radius: 6px; letter-spacing: 1px; font-weight: bold;">ADMIN</div>
                                @else
                                    <div style="font-size: 11px;  margin:auto; background: #607d8b5c; line-height: 30px; width: 70px; text-align: center; color: #FFF; border-radius: 6px; letter-spacing: 1px; font-weight: bold;">PADRÃO</div>
                                @endif
                            </div>
                            <div style="width:15%; text-align:center;">
                                @if (isset($User['email_verified_at']))
                                    <div><x-ei-check /></div>
                                @else
                                    <div style=" width: 21px; margin:auto; color: #E91E63;"> <x-simpleline-close /></div>
                                @endif
                            </div>
                            <div style="width:20%; text-align:center;">{{$User['position']->position}}</div>
                            <div style="width:20%; text-align:center;">{{$User['created_at']}}</div>
                            <div style="width:10%; text-align:center; position:relative;">

                            <x-dropdown class="text-gray-500">
                                <x-slot name="trigger">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 cursor-pointer" style="margin:auto;" fill="none" viewBox="0 0 24 24" stroke="currentColor" onclick="toggleMenu()">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6h.01M12 12h.01M12 18h.01" />
                                    </svg>
                                </x-slot>
                                <div style="    background-color: #f6f6f6;
    width: 140px;
    position: absolute;
    padding: 9px;
    right: 66px;
    bottom: -17px; border-radius: 10px;">
                                    <a href="/edit-user/{{$User['id']}}" style="width: 100%; text-align: center; text-transform: uppercase; font-weight: bold; font-size: 13px; margin: 6px;">Editar</a>
                                    <!-- <a href="/remove-user/{{$User['id']}}" style="width: 100%; text-align: center; text-transform: uppercase; font-weight: bold; font-size: 13px; margin: 6px;">Excluir</a> -->

                                    <form action="{{ route('userproject.remove', $User['id']) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');" style="margin: 0px;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="width: 100%; text-align: center; text-transform: uppercase; font-weight: bold; font-size: 13px; margin: 6px;">Excluir</button>
                                    </form>


                                    <a href="/new-password/{{$User['id']}}" style="width: 100%; text-align: center; text-transform: uppercase; font-weight: bold; font-size: 13px; margin: 6px;">Trocar Senha</a>
                                </div>
                            </x-dropdown>



                
                                
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function toggleMenu() {
        const dropdownMenu = document.getElementById("dropdownMenu");
        dropdownMenu.classList.toggle("hidden");
    }

    document.addEventListener("click", function (event) {
        const dropdownMenu = document.getElementById("dropdownMenu");
        const isClickInside = event.target.closest('.relative');
        if (!isClickInside && !dropdownMenu.classList.contains("hidden")) {
            dropdownMenu.classList.add("hidden");
        }
    });
</script>