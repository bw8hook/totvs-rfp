<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __($title) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" style="width: 100%; max-width: 780px; margin: auto;">
            <div class="col-12">

                        <div class="card ">
                            <div class="card-body" style="height: 80vh; width: 100%; max-width: 780px;">                    

                                <iframe src=" https://widget.meuassistente.rdstationmentoria.com.br/wpc_01H44Y2BQJQWSB7WDMHG7NCMNV/{{$Idagente}}" width="100%" height="100%">
                                    <p>Não suportado pelo seu navegador</p>
                                </iframe>
    
                            </div>
                        </div>
                        </div>



            </div>
        </div>
    </div>
</x-app-layout>
