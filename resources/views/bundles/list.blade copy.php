<x-app-layout>
    <div class="py-12" style=" padding-bottom: 130px;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-title-component :showButton="true" textButton="Adicionar Linha/Produto" urlButton="new-bundles" > {{ __('Linhas/Produtos Cadastrados') }} </x-title-component>

            <div id="BlocoLista" class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">

                <div class="tabela">
                    <div class="header_tabela">
                        <div style="width:50%;">Nome da Linha/Produto</div>
                        <div style="width:20%; text-align:center;">Data do Cadastro</div>
                        <div style="width:10%; text-align:center;">Ações</div>           
                    </div>
                    <div>
                        @foreach($ListBundles as $Bundle)
                        <div class="listaTabela">
                            <div style="width:50%;">{{$Bundle['nome']}}</div>
                            <div style="width:20%; text-align:center;">{{$Bundle['created_at']}}</div>
                            <div style="width:10%; text-align:center; position:relative;">

                            <x-dropdown class="text-gray-500">
                                <x-slot name="trigger">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 cursor-pointer" style="margin:auto;" fill="none" viewBox="0 0 24 24" stroke="currentColor" onclick="toggleMenu()">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6h.01M12 12h.01M12 18h.01" />
                                    </svg>
                                </x-slot>
                                <div style="background-color: #f6f6f6; width: 140px; position: absolute; padding: 9px; right: 66px; bottom: -17px; border-radius: 10px;">
                                    <a href="/edit-bundle/{{$Bundle['id']}}" style="width: 100%; text-align: center; text-transform: uppercase; font-weight: bold; font-size: 13px; margin: 6px;">Editar</a>
                                
                                    <form action="{{ route('bundles.remove', $Bundle['id']) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este produto?');" style="margin: 0px;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="width: 100%; text-align: center; text-transform: uppercase; font-weight: bold; font-size: 13px; margin: 6px;">Excluir</button>
                                    </form>
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





