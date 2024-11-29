<x-app-layout>
    <div class="flex flex-col">
   
        <div class="profile-bar">
            <x-profile-bar></x-profile-bar>
        </div>
        <div class="py-12" style=" padding-bottom: 130px;">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-title-component :showButton="true" textButton="Enviar Outra Base" urlButton="add-knowledge" > {{ __('Dados Enviados') }} </x-title-component>

            <div id="BlocoLista" class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">

                <div class="bloco_importacao_sucesso">
                    <img src="{{ asset('icons/success-filled-svgrepo-com.svg') }}" width="50" />
                    <h5>Base enviada com Sucesso!</h5>
                </div>
                
                <div class="bloco_importacao_topo">
                    <div>Total de Requisitos</div>
                    <h6> {{$CountResultado}} </h6>
                </div>

                <div class="bloco_importacao_topo">
                    <div>Qtd Pacotes</div>
                    <h6> {{$CountPacotes}} </h6>
                </div>



                 @if(!empty($ListImports))
                    <div style="background: #f8f8f8; padding: 30px; border-radius: 12px; margin-left: 10px; margin-top: 20px;">
                        <h6 style="margin-bottom: 20px; font-size: 16px; color: #696969; padding: 10px; text-align: justify;">Abaixo, apresentamos a quantidade de requisitos que foram enviados, organizados por pacotes específicos. Cada pacote representa um conjunto agrupado para facilitar a visualização dos dados. Assim, você pode identificar rapidamente o volume de requisitos por categoria</h6>

                        <div class="listaTabela" style="    background: #e9e9e9; padding: 6px; padding-left: 13px; border-color: #e9e9e9; margin-bottom: 3px; text-transform: uppercase; font-size: 13px; font-weight: bolder; border-radius: 4px;">
                            <div style="width:90px; text-align:center;">Quantidade</div>
                            <div style="width:calc(100% - 100px); margin-left: 20px; text-align:left;">Nome do Pacote</div>
                        </div>
                        @foreach($ListImports as $Import)
                        <div class="listaTabela" style="background: #FFF;">
                            <div style="width:100px; text-align:center;">{{$Import->total}}</div>
                            <div style="width:calc(100% - 100px); margin-left: 20px; text-align:left;">{{$Import->bundle}}</div>
                            
                        </div>
                        @endforeach
                    </div>

                @endif
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