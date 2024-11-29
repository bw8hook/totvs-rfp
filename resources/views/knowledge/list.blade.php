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
                        <span>Bases de Conhecimento Enviadas</span>
                    </div>
                    <div class="relative flex items-center">

                        <button type="button" class="btn flex items-center justify-center w-full py-3 rounded-lg font-semibold transition mb-6 bg-#5570F1" style="box-shadow: 0px 19px 34px -20px #43BBED; background-color: #5570F1; color: white; padding: 0px 24px; height: 45px; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; margin-top: 28px;" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            Enviar nova base
                        </button>
                    </div>
                </div>


                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Enviar Nova Base de Conhecimento</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                    <span  style="width: 100%; font-size: 14px; text-align: justify; float: left; margin-bottom: 15px; color: #7a7a7a;"> Para enviar novas bases de conhecimento, existem duas opções disponíveis. A primeira consiste em enviar um ou mais arquivos de um pacote específico. A segunda opção envolve o envio de um único arquivo que deve conter um campo denominado 'LINHA/PRODUTO'.</span>

                                    <a href="add-knowledge" class="flex items-center justify-center w-full py-3 rounded-lg font-semibold transition mb-6 bg-#5570F1" style="box-shadow: 0px 19px 34px -20px #43BBED; background-color: #5570F1; color: white; padding: 0px 24px; height: 45px; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; margin-top: 28px;">
                                        Enviar Nova Base Escolhendo o Pacote
                                    </a>

                                    <span style="width: 100%; font-size: 14px; text-align: center; float: left; margin-bottom: 15px; color: #7a7a7a;">OU</span>

                                    <a href="add-knowledge-file" class="flex items-center justify-center w-full py-3 rounded-lg font-semibold transition mb-6 bg-#5570F1" style="box-shadow: 0px 19px 34px -20px #43BBED; background-color: #5570F1; color: white; padding: 0px 24px; height: 45px; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; margin-top: 28px;">
                                        Enviar Nova Base contendo vários pacotes
                                    </a>
                            </div>
                        </div>
                    </div>
                </div>

            
            <div id="BlocoLista" class="p-4 sm:p-8 bg-white shadow sm:rounded-lg" style=" color: #929292; text-transform: uppercase; font-size: 13px; font-weight: bolder; text-align: center;">
                Os arquivos exibidos nesta seção foram enviados por você e servirão como base de conhecimento. </br> Eles serão utilizados para que a inteligência artificial possa responder de forma mais precisa às novas RFPs recebidas. </br>Sua contribuição ajudará a melhorar as respostas fornecidas.
            </div>

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
                        <div style="width:30%; text-align:center;">Pacote</div>
                        <div style="width:100px;  text-align:center;">Data de Envio</div>
                        <div style="width:100px; text-align:center;">Extensão</div>
                        <div style="width:10%; text-align:center;">Ações</div>           
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
                                @if (isset($File['bundle']->bundle))
                                    <div style="background: #9a9aa9; font-size: 11px;  margin:auto; line-height: 30px; display: inline-block; padding: 0px 10px; text-align: center; color: #FFF; border-radius: 6px; letter-spacing: 1px; font-weight: bold; text-transform:uppercase;">{{$File['bundle']->bundle}}</div>
                                @endif
                           
                            </div>

                            <div style="width:100px; text-align:center;">{{$File['created_at']}}</div>


                            <div style="width:100px; text-align:center;">
                                @if ($File['file_extension'] == "xls" || $File['file_extension'] == "xlsx")
                                    <div style="font-size: 11px;  margin:auto; background: #0e8d13; line-height: 30px; width: 70px; text-align: center; color: #FFF; border-radius: 6px; letter-spacing: 1px; font-weight: bold; text-transform:uppercase;">.{{$File['file_extension']}}</div>
                                @else
                                    <div style="font-size: 11px;  margin:auto; background: #607d8b5c; line-height: 30px; width: 70px; text-align: center; color: #FFF; border-radius: 6px; letter-spacing: 1px; font-weight: bold; text-transform:uppercase;">.{{$File['file_extension']}}</div>
                                @endif
                            </div>
                            
                            
                            <div style="width:10%; text-align:center; position:relative;">

                                <div class="text-gray-500">
                                    <form action="{{ route('knowledge.remove', $File['knowledge_base_id']) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este arquivo?');" style="margin: 0px;">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" style="width: 14px; text-align: center; text-transform: uppercase; font-weight: bold; font-size: 13px; margin: 6px;">
                                            <img src="{{ asset('icons/trashbin.svg') }}" alt="Delete Icon">
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else

                    <div style=" width: 550px; margin: auto; text-align: center; font-size: 20px; color: #30344d;"> 
                        <svg style="width:30px; margin:20px auto;" fill="#30344d" width="800px" height="30px" viewBox="0 0 56 56" xmlns="http://www.w3.org/2000/svg"><path d="M 7.7148 48.0039 L 48.2852 48.0039 C 53.1836 48.0039 55.6446 45.5664 55.6446 40.7383 L 55.6446 27.8008 C 55.6446 25.6914 55.2928 24.7070 54.3088 23.3477 L 46.1992 12.4023 C 43.6446 8.9102 42.2382 7.9961 38.0898 7.9961 L 17.9101 7.9961 C 13.7617 7.9961 12.3554 8.9102 9.8007 12.4023 L 1.6913 23.3477 C .7070 24.7070 .3554 25.6914 .3554 27.8008 L .3554 40.7383 C .3554 45.5898 2.8398 48.0039 7.7148 48.0039 Z M 27.9882 34.4336 C 24.4726 34.4336 22.2226 31.3867 22.2226 28.5039 L 22.2226 28.4336 C 22.2226 27.3789 21.5898 26.3945 20.3007 26.3945 L 5.3476 26.3945 C 4.5741 26.3945 4.4101 25.7383 4.7851 25.2227 L 13.4570 13.3164 C 14.5585 11.8164 15.9413 11.2774 17.6523 11.2774 L 38.3476 11.2774 C 40.0585 11.2774 41.4413 11.8164 42.5430 13.3164 L 51.1912 25.2227 C 51.5665 25.7383 51.4024 26.3945 50.6291 26.3945 L 35.6992 26.3945 C 34.4101 26.3945 33.7773 27.3789 33.7773 28.4336 L 33.7773 28.5039 C 33.7773 31.3867 31.5273 34.4336 27.9882 34.4336 Z"/></svg>
                        <h3 style="line-height:30px;"> Ainda não há arquivos nesta lista. <br/> Que tal começar agora? <br/> Envie o primeiro arquivo e dê o pontapé inicial!</h3>
                        
                        <button type="button" class="btn flex items-center justify-center w-full py-3 rounded-lg font-semibold transition mb-6 bg-#5570F1" style="box-shadow: 0px 19px 34px -20px #43BBED; background-color: #5570F1; color: white; padding: 0px 24px; height: 45px; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; margin-top: 28px;" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            Enviar minha primeira base
                        </button>


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