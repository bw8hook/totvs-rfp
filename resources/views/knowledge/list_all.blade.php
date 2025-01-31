<x-app-layout>
    <div class="flex flex-col">
   
        <div class="profile-bar">
            <x-profile-bar></x-profile-bar>
        </div>
        <div class="py-12" style=" padding-bottom: 130px;">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <div id="titleComponent" class="text-lg font-bold flex items-center justify-between w-full px-4 space-x-2 relative" >
                    <div class="flex items-center space-x-2">
                        <img src="{{ asset('icons/new-item.svg') }}" alt="Upload Icon" style="height: 33%; padding-right: 18px;">
                        <span>Todas Bases de Conhecimento Enviadas</span>
                    </div>
                </div>


                <canvas id="barChart" width="400" height="200"></canvas>


            @if(!empty($ListFiles))
                <div class="bloco_importacao_topo">
                    <div>Total de RFPs</div>
                    <h6> {{$CountRFPs}} </h6>
                </div>

                <div class="bloco_importacao_topo">
                    <div>Total de Requisitos</div>
                    <h6> {{$CountResultado}} </h6>
                </div>

                <div class="bloco_importacao_topo">
                    <div>Qtd Pacotes</div>
                    <h6> {{$CountPacotes}} </h6>
                </div>
            @endif


            <div id="BlocoLista" class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">

            @if(!empty($ListFiles))
                <div class="tabela">
                    <div class="header_tabela">
                        <div style="width:30%;">Nome do Arquivo</div>
                        <div style="width:30%;  text-align:center;">Usuário</div>
                        <div style="width:100px; text-align:center;">Pacote</div>
                        <div style="width:100px; text-align:center;">Requisitos</div>
                        <div style="width:100px;  text-align:center;">Data de Envio</div>       
                    </div>
                    <div>
                        @foreach($ListFiles as $File)
                        <div class="listaTabela">
                            <div style="width:30%;">
                                @if ($File['filename_original'])
                                    {{$File['filename_original']}}
                                @else
                                    {{$File['filename']}}
                                @endif
                            </div>

                           
                            <div style="width:30%; text-align:center; ">
                                <div style="background: #9a9aa9; font-size: 11px;  margin:auto; line-height: 30px; display: inline-block; padding: 0px 10px; text-align: center; color: #FFF; border-radius: 6px; letter-spacing: 1px; font-weight: bold; text-transform:uppercase;">{{$File['username']}}</div>
                            </div>
                            


                            <div style="width:100px; text-align:center; ">
                                @if (isset($File['bundle']->bundle))
                                    <div style="background: #9a9aa9; font-size: 11px;  margin:auto; line-height: 30px; display: inline-block; padding: 0px 10px; text-align: center; color: #FFF; border-radius: 6px; letter-spacing: 1px; font-weight: bold; text-transform:uppercase;">{{$File['bundle']->bundle}}</div>
                                @else
                                <div style=" font-size: 11px;  margin:auto; line-height: 30px; display: inline-block; padding: 0px 10px; text-align: center;  letter-spacing: 1px; font-weight: bold; text-transform:uppercase;"> - </div>
                                @endif
                            </div>

                            <div style="width:100px; text-align:center;">{{$File['QtdRecordsBase']}}</div>

                            <div style="width:100px; text-align:center;">{{$File['created_at']}}</div>

                        </div>
                        @endforeach
                    </div>
                </div>
                @else

                    <div style=" width: 550px; margin: auto; text-align: center; font-size: 20px; color: #30344d;"> 
                        <svg style="width:30px; margin:20px auto;" fill="#30344d" width="800px" height="30px" viewBox="0 0 56 56" xmlns="http://www.w3.org/2000/svg"><path d="M 7.7148 48.0039 L 48.2852 48.0039 C 53.1836 48.0039 55.6446 45.5664 55.6446 40.7383 L 55.6446 27.8008 C 55.6446 25.6914 55.2928 24.7070 54.3088 23.3477 L 46.1992 12.4023 C 43.6446 8.9102 42.2382 7.9961 38.0898 7.9961 L 17.9101 7.9961 C 13.7617 7.9961 12.3554 8.9102 9.8007 12.4023 L 1.6913 23.3477 C .7070 24.7070 .3554 25.6914 .3554 27.8008 L .3554 40.7383 C .3554 45.5898 2.8398 48.0039 7.7148 48.0039 Z M 27.9882 34.4336 C 24.4726 34.4336 22.2226 31.3867 22.2226 28.5039 L 22.2226 28.4336 C 22.2226 27.3789 21.5898 26.3945 20.3007 26.3945 L 5.3476 26.3945 C 4.5741 26.3945 4.4101 25.7383 4.7851 25.2227 L 13.4570 13.3164 C 14.5585 11.8164 15.9413 11.2774 17.6523 11.2774 L 38.3476 11.2774 C 40.0585 11.2774 41.4413 11.8164 42.5430 13.3164 L 51.1912 25.2227 C 51.5665 25.7383 51.4024 26.3945 50.6291 26.3945 L 35.6992 26.3945 C 34.4101 26.3945 33.7773 27.3789 33.7773 28.4336 L 33.7773 28.5039 C 33.7773 31.3867 31.5273 34.4336 27.9882 34.4336 Z"/></svg>
                        <h3 style="line-height:30px;"> Ainda não há arquivos nesta lista.</h3>
                    
                    </div>

                @endif
            </div>
        </div>
    </div>
    </div>
</x-app-layout>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>
        const ctx = document.getElementById('barChart').getContext('2d');
        const barChart = new Chart(ctx, {
            type: 'bar', // Tipo do gráfico
            data: {
                labels: @json($ListBundles), // Labels (meses)
                datasets: [{
                    label: 'Requisitos por Linha/Produto',
                    data: @json($ListBundlesQtds), // Dados (valores)

                    borderWidth: 2 // Largura da borda
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true // Começar do zero
                    }
                }
            }
        });
    </script>

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