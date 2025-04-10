<x-app-layout>
    <div class="py-4" style=" padding-bottom: 130px;">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">

            <div id="titleComponent_KnowledgeBase" style=" padding-top: 30px; display: flex; min-height: 97px; height: auto; margin-bottom: -16px !important;" class="text-lg font-bold flex items-center justify-between w-full px-4 space-x-2 relative" >
                <div class="AlignTitleLeft" style="width: 80%;">
                    <div class="flex" style="width: 100%;">
                        <img src="{{ asset('icons/file-ai.svg') }}" alt="Upload Icon" style="height: 33%; width:52px; padding-right: 18px;">
                        <span>Novo Arquivo</span>
                    </div>
                    <div class="relative block items-center" style=" padding-bottom: 12px; margin-left: 8px; width: 90%;">        
                        <div class="info_details" style="color:#8A94AD;"> Preencha os dados abaixo e envie um novo arquivo para a IA responder seus requisitos de acordo com o projeto selecionado anteriormente.
                        Se tiver alguma dúvida referente às informações a serem inseridas no seu arquivo, baixe o arquivo modelo. </div>
                    </div>
                </div>

                <a href="https://docs.google.com/spreadsheets/d/1DMFxJXIKM4up3Tocm_zGWEeKyp4qdCHr809rjhi1qNc/edit?usp=drive_linkArquivo não encontrado" style="background-color: #5570F1; border-radius: 50px; color: white; padding: 8px 21px; font-size: 14px; font-weight: 600; margin: auto; float: right; margin-top: -4px;" download> 
                    <div style="">
                        <img src="{{ asset('icons/download_2.svg') }}" alt="Upload Icon" style="height: 18px; padding-right: 12px; float: left; margin-top: 5px;">
                        <span>Baixar planilha modelo</span>
                    </div>
                </a>
            </div>
            
            <meta name="csrf-token" content="{{ csrf_token() }}">

                <div class="bg-white rounded-lg shadow-md p-8 w-full flex flex-col" style=" margin-bottom: 100px; position:relative;">

                    <div class="loading" style="display:none; background: #ffffffcf; position: absolute; width: 100%; height: 100%; top: 0px; left: 0px; z-index: 9;">
                        <div id="lottie-container" style="width: 100px; height:100px; position: absolute; top: 50%; left: 50%; transform: translate(-75px, -35px);"></div>
                    </div>


                    <div class="flex flex-column items-center justify-center">

                        <div style="padding-top: 10px"></div>

                        <div style="width: 35%; border-bottom: 2px solid #E3EEFF; margin-bottom: 30px;;">
                            <h3 style="font-style: normal; font-weight: 600; font-size: 22px; line-height: 130%; color: #5570F1; margin-bottom: 10px;; ">Identificação</h3>
                        
                            <!-- Name -->
                            <div style="width: 100%; margin-bottom: 25px;">
                                <x-input-label for="name" :value="__('Nome do Projeto:')" />
                                <x-text-input id="name" class="formAddKnowledge" type="text" name="name" value="{{$Project->name}}" required autofocus autocomplete="name" disabled/>
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- User -->
                            <!-- <div style="width: 35%; margin-bottom: 40px;">
                                <x-input-label for="responsavel" :value="__('Responsável:')" /> -->
                                <x-text-input id="responsavel" class="formAddKnowledge" type="hidden" name="responsavel" value="{{$Project->user->name}}" required autofocus autocomplete="name" disabled/>
                                <!-- <x-input-error :messages="$errors->get('responsavel')" class="mt-2" />
                            </div> -->

                            <!-- DATA -->
                            <div style="width: 100%; margin-bottom: 25px;">
                                <x-input-label for="data" :value="__('Data:')" />
                                <x-text-input id="data" class="formAddKnowledge" type="text" name="data" value="{{ date('d/m/Y', strtotime($Project->project_date)) }}" required disabled/>
                            </div>

                        </div>

                        <div style="width: 35%; border-bottom: 2px solid #E3EEFF; margin-bottom: 30px;">
                            <h3 style="font-style: normal; font-weight: 600; font-size: 22px; line-height: 130%; color: #5570F1; margin-bottom: 10px;; ">Cenário</h3>
                            
                            <div style="width: 100%; margin-bottom: 25px;">
                                <label for="line_product">Linha de Produto Principal:</label>
                                <select id="line_product" class="formAddKnowledge" name="line_product" placeholder="Selecione a Linha" style="  width: 100%; border: none; font-size: 14px; font-weight: 100; color: #6f778c;">
                                    <option value="" selected disabled>Selecione</opt>
                                        @foreach($lines as $line)
                                            <option value="{{$line->id}}">{{$line->name}}</option>
                                        @endforeach
                                </select>
                            </div>

                            <!-- Select convertido -->
                            <div class="w-full">
                                <x-input-label for="multi-select" :value="__('Segmentos')" />
                                <div class="mt-1">
                                    <select id="multi-select" 
                                            name="selected_items[]" 
                                            multiple 
                                            placeholder="Selecione as opções..."
                                            class="w-full">
                                        @foreach($segments as $item)
                                            <option value="{{ $item['id'] }}"
                                                    @if(isset($selectedItems) && in_array($item['id'], $selectedItems)) selected @endif>
                                                {{ $item['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Segmentos -->
                            <!-- <div style="margin-bottom: 20px;">
                                <x-input-label for="segments" :value="__('Segmento:')" />
                                <div id="dropdown-wrapper-segment" class="relative w-full">
                                    <div id="dropdown-toggle-segment" class="rounded-lg p-2 flex items-center gap-2 cursor-pointer  flex-wrap" style="min-height: 60px; background-color: #F7F7F7;">
                                        <div id="selected-items-segment" class="flex flex-wrap gap-2"></div>
                                        <span id="placeholder-text-segment" class="text-gray-500">Selecione...</span> 
                                    </div>
                                    <div id="dropdown-menu-segment" class="hidden absolute mt-1 w-full bg-white border rounded-lg shadow-lg z-10">
                                        <div id="dropdown-options-segment" style="max-height: 272px; overflow: scroll;">
                                            @foreach ($segments as $index => $item)
                                                <div class="option-item flex items-center justify-between px-3 py-2 hover:bg-gray-100 cursor-pointer segment-project" data-id="{{ $item['id'] }}" data-name="{{ $item['name'] }}" data-documents="{{ $item['document_count'] }}">
                                                    <input type="checkbox" class="form-check-input segment-checkbox" >
                                                    <span>{{ $item['name'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div> -->

                            <div class="btn_open_filtro">
                                <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_1347_985)">
                                <path d="M16.3045 14.862L12.3252 10.8826C13.4096 9.55637 13.9428 7.86404 13.8144 6.15568C13.6861 4.44733 12.906 2.85366 11.6356 1.70432C10.3652 0.554984 8.70158 -0.0620951 6.98895 -0.0192739C5.27632 0.0235473 3.64566 0.722992 2.43426 1.93439C1.22287 3.14578 0.523425 4.77644 0.480604 6.48907C0.437783 8.2017 1.05486 9.86528 2.2042 11.1357C3.35354 12.4061 4.94721 13.1862 6.65556 13.3145C8.36392 13.4429 10.0563 12.9097 11.3825 11.8253L15.3619 15.8046C15.4876 15.9261 15.656 15.9933 15.8308 15.9918C16.0056 15.9902 16.1728 15.9201 16.2964 15.7965C16.42 15.6729 16.4901 15.5057 16.4916 15.3309C16.4932 15.1561 16.426 14.9877 16.3045 14.862ZM7.16652 12C6.11169 12 5.08054 11.6872 4.20348 11.1011C3.32642 10.5151 2.64283 9.68216 2.23916 8.70762C1.8355 7.73308 1.72988 6.66073 1.93567 5.62616C2.14145 4.5916 2.64941 3.64129 3.39529 2.89541C4.14117 2.14953 5.09147 1.64158 6.12604 1.43579C7.16061 1.23 8.23296 1.33562 9.2075 1.73929C10.182 2.14295 11.015 2.82654 11.601 3.7036C12.1871 4.58066 12.4999 5.61181 12.4999 6.66664C12.4983 8.08064 11.9359 9.43628 10.936 10.4361C9.93615 11.436 8.58052 11.9984 7.16652 12Z" fill="#5570F1"/>
                                </g>
                                <defs>
                                <clipPath id="clip0_1347_985">
                                <rect width="16" height="16" fill="white" transform="translate(0.5)"/>
                                </clipPath>
                                </defs>
                                </svg>
                                <span>Filtros avançados</span>
                            </div>

                            <div class="alert_filtro_custom">*seus filtros foram personalizados</div>

                        </div>


                        <div style="width: 35%;">
                            <h3 style="font-style: normal; font-weight: 600; font-size: 22px; line-height: 130%; color: #5570F1; margin-bottom: 10px;; ">Arquivo</h3>

                            <div id="dropZone" class="border-2 border-gray-300 rounded-lg p-8 text-center mb-6 cursor-pointer" style="border-style: dashed; width: 100%; box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25); padding: 30px 0px;">
                                <img src="{{ asset('icons/upload-cloud.svg') }}" alt="Upload Icon"
                                    class="h-12 w-12 mx-auto text-gray-400 mb-2">
                                <p class="text-gray-500 font-medium">Drop it like a pro!</p>
                                <p class="text-gray-400 text-sm">Carregue seu arquivo .xls ou .xlxs soltando-os nesta janela</p>
                            </div>
                        </div>

                        <ul id="fileList" style="width: 100%;" class="flex flex-column text-sm text-gray-600 mb-4 items-center justify-center"></ul>
                        <button id="uploadButton" class="px-4 py-2" style="background-color: #5570F1; width: 406px; height: 58px; border-radius: 10px; color: white; box-shadow: 0px 19px 34px -20px #43BBED;">Confirmar e Enviar</button>
                        <input type="file" id="fileInput" style="display: none;"/>
                    </div>
                </div>

        </div>
    </div>


    <div id="ModalProdutosOverlay"><div>
    <div id="ModalProdutos">
        <div class="title">
            <h3>Busca avançada</h3>
        </div>

        <div class="btn_close">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18.3402 4.84109L13.1813 10L18.3402 15.1589C19.2199 16.0386 19.2199 17.4606 18.3402 18.3402C17.9015 18.779 17.3255 18.9995 16.7496 18.9995C16.1736 18.9995 15.5977 18.779 15.1589 18.3402L10 13.1813L4.84107 18.3402C4.40235 18.779 3.82639 18.9995 3.25042 18.9995C2.67446 18.9995 2.09849 18.779 1.65977 18.3402C0.780076 17.4606 0.780076 16.0386 1.65977 15.1589L6.8187 10L1.65977 4.84109C0.780076 3.96139 0.780076 2.53948 1.65977 1.65979C2.53947 0.780091 3.96138 0.780091 4.84107 1.65979L10 6.81871L15.1589 1.65979C16.0386 0.780091 17.4605 0.780091 18.3402 1.65979C19.2199 2.53948 19.2199 3.96139 18.3402 4.84109Z" fill="#8A94AD"/>
            </svg>
        </div>

        <div class="alerta_pre_selecao">
            <h4>Pré-seleção</h4>
            <div class="pre-line"> Linha de Produto: <span>Protheus</span> </div>
            <div class="pre-segment"> Segmento: <span>Logiística</span> </div>
        </div>

        <div id="filterAdvancedSearch">
            <form id="filterFormProdutos">
                @csrf    
                <!-- <div class="primeira_linha">
                    <div class="inputField">
                        <input type="text" id="keyWord" name="keyWord" placeholder="Busca por palavra-chave">
                    </div>
                </div> -->
                <div class="segunda_linha">
                    <div class="inputField">
                        <label>Nome do Prodito:</label>
                        <input type="text" id="filter-nome" name="keyWord">
                    </div>

                    <div class="inputField" >
                        <label>Tipos:</label>
                        <select name="processo" id="filter-tipo" class="filter-select">
                            <option value="" selected>Selecione</option>
                            @foreach($types as $Type)
                                <option value="{{$Type->name}}">{{$Type->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="inputField" >
                        <label>Segmentos:</label>
                        <select name="segmento" id="filter-segmento" class="filter-select">
                            <option value="" selected>Selecione</option>
                            @foreach($segments as $segment)
                                <option value="{{$segment->name}}">{{$segment->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="inputField" >
                        <label>Categoria:</label>
                        <select name="categoria" id="filter-categoria" class="filter-select">
                            <option value="" selected>Selecione</option>
                            @foreach($categorys as $category)
                                <option value="{{$category->name}}">{{$category->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="inputField" >
                        <label>Grupo de Atendimento:</label>
                        <select name="grupo" id="filter-grupo" class="filter-select">
                            <option value="" selected>Selecione</option>
                            @foreach($services as $service)
                                <option value="{{$service->name}}">{{$service->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="inputField" >
                        <label>Agente:</label>
                        <select name="agente" id="filter-agente" class="filter-select">
                            <option value="" selected>Selecione</option>
                            @foreach($agents as $agent)
                                <option value="{{$agent->agent_name}}">{{$agent->agent_name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-buttons">
                        <button id="apply-filters" class="btn-filter">Filtrar</button>
                        <!-- <button id="clear-filters" class="btn-clear"  style=" border: 2px solid #CBD0DD; background: #FFF; color: #5E6470;">Limpar</button> -->
                    </div>

                    
                    
                    <!-- <button type="submit">Buscar</button>
                    <button id="btnLimpar" style=" border: 2px solid #CBD0DD; background: #FFF; color: #5E6470;" type="button">LIMPAR</button> -->
                    </div>
                </form> 

                </div>

                <div id="ProductsResults">

                    <div class="resultadosTitle"> Principais resultados: </div>
                    <!-- <div id="results-count" class="results-counter"></div> -->
                    <div class="SearchHeader">
                        <input type="checkbox" class="form-check-input search-all-checkbox" >  
                        <div class="search_nome">NOME DO PRODUTO:</div>
                        <div class="search_tipo">TIPO:</div>
                        <div class="search_segmento">SEGMENTOS:</div>
                        <div class="search_categoria">CATEGORIA:</div>
                        <div class="search_grupo">GRUPO DE ATENDIMENTO:</div>
                        <div class="search_agente">AGENTE:</div>
                    </div>
                    <div class="SearchBody">
                        @foreach($bundles as $bundleItem)        
                            <div class="listItem" data-id="{{ $bundleItem->bundle_id }}" data-line-ids="{{ $bundleItem->lineOfProduct->pluck('id')->join(',') }}" data-segment-ids="{{ $bundleItem->segments->pluck('id')->join(',') }}">
                                <input type="checkbox"  class="form-check-input segment-checkbox" name="selected_bundles[]" value="{{ $bundleItem->bundle_id }}">  
                                <div class="search_nome">{{$bundleItem->bundle}}</div>
                                <div class="search_tipo">
                                    @if(isset($bundleItem->type) && isset($bundleItem->type['name']))
                                        {{ $bundleItem->type['name'] }}
                                    @endif
                                </div>
                                <div class="search_segmento">
                                    @if ($bundleItem->segments)
                                        @foreach ($bundleItem->segments as $segment)
                                            {{ $segment->name }},
                                        @endforeach
                                    @endif
                                </div>
                                <div class="search_categoria">
                                    @if(isset($bundleItem->category) && isset($bundleItem->category['name']))
                                        {{ $bundleItem->category['name'] }}
                                    @endif
                                </div>
                                <div class="search_grupo">
                                    @if(isset($bundleItem->serviceGroup) && isset($bundleItem->serviceGroup->name))
                                        {{ $bundleItem->serviceGroup->name}}
                                    @endif
                                </div>
                                <div class="search_agente">
                                    @if(isset($bundleItem->agent) && isset($bundleItem->agent->agent_name))
                                        {{ $bundleItem->agent->agent_name}}
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="btn_submit_filter">Aplicar filtros e concluir</div>
                </div>
        </div>
    </div>






</x-app-layout>



<script>
    const Id = @json($Project->id);
    const userId = @json($userId);
</script>


        <script>
            let fileListGlobal = [];

            function addFileItem(file) {
                const fileItem = document.createElement('div');
                fileItem.style.display = 'flex';
                fileItem.style.alignItems = 'center';
                fileItem.style.justifyContent = 'space-between';
                fileItem.style.width = '90%';
                fileItem.style.maxWidth = '500px';
                fileItem.style.padding = '8px 12px';
                fileItem.style.border = '1px solid #ccc';
                fileItem.style.borderRadius = '5px';
                fileItem.style.backgroundColor = '#fff';
                fileItem.style.marginBottom = '8px';

                const fileName = document.createElement('span');
                fileName.textContent = file.name;
                fileName.style.color = '#666';
                fileName.style.fontSize = '14px';

                const deleteIcon = document.createElement('div');
                deleteIcon.style.display = 'flex';
                deleteIcon.style.alignItems = 'center';
                deleteIcon.style.justifyContent = 'center';
                deleteIcon.style.cursor = 'pointer';
                deleteIcon.innerHTML = '<img src="{{ asset('icons/trashbin.svg') }}" alt="Upload Icon">';
                deleteIcon.onclick = () => {
                    let index = fileListGlobal.indexOf(file.name);
                    fileListGlobal = fileListGlobal.filter(element => element != file.name)
                    fileListGlobal.splice(0, fileListGlobal.length);
                    file = {};
                    fileItem.remove();
                    const fileInput = document.getElementById("fileInput");
                    fileInput.value = "";                    
                };

                fileItem.appendChild(fileName);
                fileItem.appendChild(deleteIcon);

                const fileList = document.getElementById('fileList');
                fileList.appendChild(fileItem);
            }

            function updateFileListDisplay() {
                const fileListDisplay = document.getElementById('fileList');
                fileListDisplay.innerHTML = '';
                fileListGlobal.forEach((file, index) => {
                    const exampleFile = { name: file.name };
                    addFileItem(exampleFile);
                });
            }

            function uploadFiles() {
                fileListGlobal.forEach(file => {
                    const formData = new FormData();
                    formData.append('name', document.getElementById('name').value);

                    $('.loading').fadeIn(500);
                                      
                   // Pega os bundles selecionados
                    const selectedBundles = $('.segment-checkbox:checked').map(function() {
                        return $(this).val();
                    }).get();

                    // Adiciona os bundles selecionados
                    selectedBundles.forEach((bundleId, index) => {
                        formData.append(`bundles[${index}]`, bundleId);
                    });

                    // Verifica se há itens selecionados
                    if (selectedBundles.length === 0) {
                        alert('Preencha o Produto a ser utilizado para responder!');
                        return false;
                    }

                    formData.append('file', file);
                    fetch('/project/'+Id+'/files', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            console.log('sucesso cheogu');

                            if(data.success){
                                console.log('sucesso entrou');
                                window.location.replace(data.redirectUrl);
                            }else{
                                if(data.error){
                                    $('.loading').fadeOut(500);
                                    showAlertBootstrap("error", data.message, false);
                                }else{
                                    console.log('erro entrou');
                                    window.location.replace(data.redirectUrl);
                                }
                            }


                            console.log('sucesso passou ');
                            fileListGlobal = [];
                            $('#fileList').empty();
                            
                            console.log('Upload successful:', data);
                            $("#sucesso_message").show().delay(3200).fadeOut(300);
                        })
                        .catch(error => {
                            $('.loading').fadeOut(500);
                            console.log('erro entrou');
                            //window.location.replace(error.redirectUrl);
                            ///$("#erro_message").show().delay(3200).fadeOut(300);
                            console.log(error);
                        });
                });
            }

            document.addEventListener('DOMContentLoaded', () => {
                const fileInput = document.getElementById('fileInput');
                const dropZone = document.getElementById('dropZone');
                const uploadButton = document.getElementById('uploadButton');

                dropZone.addEventListener('click', () => {
                    fileInput.click();
                });

                fileInput.addEventListener('change', () => {
                    if (fileInput.files.length > 0) {
                        if(fileListGlobal.length < 1){
                            for (let file of fileInput.files) {
                                if (file.type === "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" || file.type === "application/vnd.ms-excel" || file.type === "application/vnd.openxmlformats-officedocument.spreadsheetml.template") {
                                    //"text/csv"
                                    fileListGlobal.push(file);
                                } else {
                                    alert("Apenas arquivo no formato XLS ou XLXS");
                                }
                            } 
                            updateFileListDisplay();
                        }else{
                            alert("Neste modelo é possível subir apenas um arquivo por vez!.");
                        }
                    }
                });

                dropZone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    dropZone.classList.add('border-blue-500');
                });

                dropZone.addEventListener('dragleave', () => {
                    dropZone.classList.remove('border-blue-500');
                });

                dropZone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropZone.classList.remove('border-blue-500');

                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        if(fileListGlobal.length < 1){
                            for (let file of files) {
                                console.log(file);
                                if (file.type === "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" || file.type === "application/vnd.ms-excel" || file.type === "application/vnd.openxmlformats-officedocument.spreadsheetml.template") {
                                    //"text/csv"
                                    fileListGlobal.push(file);
                                } else {
                                    alert("Apenas arquivo no formato XLS ou XLXS");
                                }
                            }
                            updateFileListDisplay();
                        }else{
                            alert("Neste modelo é possível subir apenas um arquivo por vez!.");
                        }
                    }
                });

                uploadButton.addEventListener('click', () => {
                    if (fileListGlobal.length > 0) {
                        uploadFiles();
                        
                        updateFileListDisplay()
                    } else {
                        alert("Nenhum arquivo enviado.");
                    }
                });



            });
        </script>


<script>
   $(document).ready(function () {
    function initializeDropdown(type) {
        const selectors = {
            wrapper: `#dropdown-wrapper-${type}`,
            toggle: `#dropdown-toggle-${type}`,
            menu: `#dropdown-menu-${type}`,
            selectedItems: `#selected-items-${type}`,
            placeholderText: `#placeholder-text-${type}`,
            options: `#dropdown-options-${type}`
        };

        let isMultiple = true;
        let selectedItems = [];

        // Inicializar selectedItems com itens já selecionados
        $(`${selectors.selectedItems} .selected-item`).each(function() {
            const id = $(this).find('button').data('id');
            selectedItems.push(id);
            $(`${selectors.wrapper} .option-item[data-id="${id}"]`).addClass('bg-gray-200');
        });

        // Toggle dropdown
        $(selectors.toggle).on("click", function () {
            $(selectors.menu).toggleClass("hidden");
        });

        // Select item
        $(`${selectors.wrapper} .option-item`).on("click", function () {
            const agentName = $(this).data("name");
            const agentId = $(this).data("id");
            const item = $(this);

            if (isMultiple) {
                if (selectedItems.includes(agentId)) {
                    selectedItems = selectedItems.filter(id => id !== agentId);
                    $(this).removeClass("bg-gray-200");
                    $(this).find('.segment-checkbox').attr('checked', false);
                    removeSelectedItemBlock(agentId);
                } else {
                    selectedItems.push(agentId);
                    $(this).addClass("bg-gray-200");
                    $(this).find('.segment-checkbox').attr('checked', true);
                    addSelectedItemBlock(agentId, agentName);
                }
            } else {
                selectedItems = [agentId];
                $(selectors.selectedItems).empty();
                addSelectedItemBlock(agentId, agentName);
                $(selectors.menu).addClass("hidden");
            }

            updatePlaceholderText();
            updateHiddenInputs();
        });

        // Função modificada para remover item
        function removeSelectedItemBlock(agentId) {
            $(`${selectors.selectedItems} button[data-id="${agentId}"]`).parent().remove();
            $(`${selectors.wrapper} .option-item[data-id="${agentId}"]`).removeClass("bg-gray-200");
            selectedItems = selectedItems.filter(id => id !== agentId);
            updatePlaceholderText();
            updateHiddenInputs();
        }

        // Adicionar evento de clique para botões de remoção existentes
        $(`${selectors.selectedItems} .selected-item button`).on("click", function(event) {
            event.stopPropagation();
            const idToRemove = $(this).data("id");
            removeSelectedItemBlock(idToRemove);
        });

        function addSelectedItemBlock(agentId, agentName) {
            const itemBlock = $(`
                <div class="selected-item flex items-center bg-gray-200 text-gray-700 px-2 py-1 rounded">
                    <span>${agentName}</span>
                    <button class="ml-1 text-gray-500 hover:text-gray-700" data-id="${agentId}">✕</button>
                </div>
            `);

            itemBlock.find("button").on("click", function (event) {
                event.stopPropagation();
                removeSelectedItemBlock(agentId);
            });

            $(selectors.selectedItems).append(itemBlock);
        }

        function updatePlaceholderText() {
            if (selectedItems.length === 0) {
                $(selectors.placeholderText).removeClass("hidden");
            } else {
                $(selectors.placeholderText).addClass("hidden");
            }
        }

        function updateHiddenInputs() {
            $(`input[name='selected_${type}[]']`).remove();
            selectedItems.forEach(id => {
                const hiddenInput = `<input type="hidden" name="selected_${type}[]" value="${id}">`;
                $("#myForm").append(hiddenInput);
            });
        }

        // Close dropdown when clicking outside
        $(document).on("click", function (event) {
            const $wrapper = $(selectors.wrapper);
            if (!$wrapper.is(event.target) && $wrapper.has(event.target).length === 0) {
                $(selectors.menu).addClass("hidden");
            }
        });
    }

    // Initialize all dropdowns
    initializeDropdown('segment');


    $('.btn_close').on("click", function (event) {
        $('#ModalProdutos').hide();
        $('#ModalProdutosOverlay').hide();
    });

    $('.btn_submit_filter').on("click", function (event) {
        $('#ModalProdutos').hide();
        $('#ModalProdutosOverlay').hide();
    });

    $('.btn_open_filtro').on("click", function (event) {
        $('#ModalProdutos').show();
        $('#ModalProdutosOverlay').show();
    });

    


    $('.listItem').on("click", function (event) {
        $(this).find('.segment-checkbox').prop('checked', !$(this).find('.segment-checkbox').prop('checked'));
        $('.alert_filtro_custom').show();
    });


    $('.segment-checkbox').on('click', function(event) {
        $(this).prop('checked', !$(this).prop('checked'));
   });
    


   $('.search-all-checkbox').on('click', function(event) {
        // Verifica o estado do checkbox clicado
        const isChecked = $(this).prop('checked');

        // Marca ou desmarca todos os checkboxes visíveis com base no estado do checkbox clicado
        $('.listItem:visible').each(function() {
            $(this).find('.segment-checkbox').prop('checked', isChecked);
        });
    });


   

});

    </script>







<script>
$(document).ready(function() {
    function populateSelects() {
        // populateSelect('filter-tipo', 'search_tipo');
        // populateSelect('filter-segmento', 'search_segmento');
        // populateSelect('filter-categoria', 'search_categoria');
        // populateSelect('filter-grupo', 'search_grupo');
        // populateSelect('filter-agente', 'search_agente');
    }

    function populateSelect(selectId, className) {
        const uniqueValues = new Set();
        $(`.${className}`).each(function() {
            const value = $(this).text().trim();
            if (value) {
                // Para segmentos, separar por vírgula e adicionar cada valor
                if (className === 'search_segmento') {
                    value.split(',').forEach(segment => {
                        const trimmedSegment = segment.trim();
                        if (trimmedSegment) uniqueValues.add(trimmedSegment);
                    });
                } else {
                    uniqueValues.add(value);
                }
            }
        });

        const $select = $(`#${selectId}`);
        Array.from(uniqueValues).sort().forEach(value => {
            $select.append(`<option value="${value}">${value}</option>`);
        });
    }

    function filterItems(e) {
        if (e) e.preventDefault();

        const filters = {
            nome: $('#filter-nome').val().toLowerCase(),
            tipo: $('#filter-tipo').val(),
            segmento: $('#filter-segmento').val(),
            categoria: $('#filter-categoria').val(),
            grupo: $('#filter-grupo').val(),
            agente: $('#filter-agente').val()
        };

        let visibleCount = 0;
        const totalItems = $('.listItem').length;

        $('.listItem').each(function() {
            const $item = $(this);
            
            const values = {
                nome: $item.find('.search_nome').text().toLowerCase(),
                tipo: $item.find('.search_tipo').text().trim(),
                segmento: $item.find('.search_segmento').text().trim(),
                categoria: $item.find('.search_categoria').text().trim(),
                grupo: $item.find('.search_grupo').text().trim(),
                agente: $item.find('.search_agente').text().trim()
            };

            let shouldShow = true;

            // Verifica apenas os filtros que têm valor
            if (filters.nome && !values.nome.includes(filters.nome)) shouldShow = false;
            
            if (filters.tipo && filters.tipo !== '' && values.tipo !== filters.tipo) shouldShow = false;
            
            // Busca parcial para segmento
            if (filters.segmento && filters.segmento !== '') {
                if (!values.segmento.includes(filters.segmento)) shouldShow = false;
            }
            
            if (filters.categoria && filters.categoria !== '' && values.categoria !== filters.categoria) shouldShow = false;
            if (filters.grupo && filters.grupo !== '' && values.grupo !== filters.grupo) shouldShow = false;
            if (filters.agente && filters.agente !== '' && values.agente !== filters.agente) shouldShow = false;

            if (shouldShow) {
                $item.show();
                visibleCount++;
            } else {
                $item.hide();
            }
        });

        updateResultsCount(visibleCount, totalItems);
    }

    function updateResultsCount(visible, total) {
        $('#results-count').text(`Mostrando ${visible} de ${total} itens`);
    }

    $('#apply-filters').on('click', function(e) {
        e.preventDefault();
        filterItems(e);
    });

    $('#clear-filters').on('click', function(e) {
        e.preventDefault();
        $('#filter-nome').val('');
        $('.filter-select').each(function() {
            $(this).val('');
        });
        filterItems(e);
    });

    // Inicializa
    populateSelects();
    updateResultsCount($('.listItem').length, $('.listItem').length);
});









$(document).ready(function() {
    let segmentSelect; // Variável para o TomSelect

    // Inicializa o TomSelect com suas configurações personalizadas
    segmentSelect = new TomSelect('#multi-select', {
        plugins: ['remove_button', 'clear_button'],
        maxItems: null,
        valueField: 'id',
        labelField: 'name',
        searchField: ['name'],
        placeholder: 'Selecione as opções...',
        persist: false,
        createOnBlur: false,
        create: false,
        closeAfterSelect: false,

        // Tradução para português
        plugins: {
            'remove_button': {
                title: 'Remover'
            },
            'clear_button': {
                title: 'Limpar todos'
            }
        },

        render: {
            no_results: function(data, escape) {
                return '<div class="no-results">Nenhum resultado encontrado</div>';
            },
            option: function(data, escape) {
                return `<div class="py-2 px-3">
                    <div class="text-sm">${escape(data.name)}</div>
                </div>`;
            },
            item: function(data, escape) {
                return `<div class="flex items-center space-x-1">
                    <span class="block truncate">${escape(data.name)}</span>
                </div>`;
            }
        },
        onChange: function(values) {
            applyFilters();
            updateSelectedTexts();
        }
    });

    // Evento de mudança no select de linha de produto
    $('#line_product').on('change', function() {
        applyFilters();
        updateSelectedTexts();
    });


    // Função para atualizar os textos selecionados
    function updateSelectedTexts() {
        $('.alerta_pre_selecao').show();

        // Pega o texto da linha selecionada
        const selectedLineId = $('#line_product').val();
        const selectedLineName = selectedLineId ? 
            $(`#line_product option[value="${selectedLineId}"]`).text() : '';

        // Pega os textos dos segmentos selecionados
        const selectedSegments = segmentSelect.items.map(id => {
            const option = segmentSelect.options[id];
            return option ? option.name : '';
        }).filter(name => name); // Remove valores vazios

        // Monta o texto final
        let finalTextLine = [];
        let finalTextSegment = [];
        
        if (selectedLineName) {
            finalTextLine.push(`${selectedLineName}`);
            $('.pre-line').show();
            $('.pre-line span').html(finalTextLine.join(' | '));
        }
        
        if (selectedSegments.length > 0) {
            finalTextSegment.push(`${selectedSegments.join(', ')}`);
            $('.pre-segment').show();
            $('.pre-segment span').html(finalTextSegment.join(' | '));
        }       
        
    }

    // Função principal que aplica todos os filtros
    function applyFilters() {
        const selectedLineId = $('#line_product').val();
        const selectedSegmentIds = segmentSelect.getValue();

        let itemsFound = 0;

        $('.listItem').each(function() {
            const $item = $(this);
            
            let itemLineIds = parseIds($item.data('line-ids'));
            let itemSegmentIds = parseIds($item.data('segment-ids'));

            const $checkbox = $item.find('.segment-checkbox');

            // Verifica se atende aos critérios de linha
            const matchesLine = !selectedLineId || 
                itemLineIds.indexOf(parseInt(selectedLineId)) !== -1;

            // Verifica se atende aos critérios de segmento
            const matchesSegments = !selectedSegmentIds.length || 
                selectedSegmentIds.some(id => 
                    itemSegmentIds.indexOf(parseInt(id)) !== -1
                );

            // Aplica os filtros combinados
            if (matchesLine && matchesSegments) {
                $checkbox.prop('checked', true);
                $item.addClass('highlighted');
                itemsFound++;
            } else {
                $checkbox.prop('checked', false);
                $item.removeClass('highlighted');
            }
        });

        showFeedback(itemsFound, selectedLineId, selectedSegmentIds);
    }

    function parseIds(ids) {
        if (typeof ids === 'string') {
            return ids.split(',').map(id => parseInt(id.trim()));
        } else if (typeof ids === 'number') {
            return [ids];
        } else if (!ids) {
            return [];
        }
        return ids;
    }

    function showFeedback(itemsFound, selectedLineId, selectedSegmentIds) {
        let message = [];
        let messageType = itemsFound > 0 ? 'success' : 'warning';

        // Mensagem para linha de produto
        if (selectedLineId) {
            const lineName = $(`#line_product option[value="${selectedLineId}"]`).text();
            message.push(`Linha: ${lineName}`);
        }

        // Mensagem para segmentos
        if (selectedSegmentIds && selectedSegmentIds.length > 0) {
            const segmentCount = selectedSegmentIds.length;
            message.push(`${segmentCount} segmento(s) selecionado(s)`);
        }

        // Monta a mensagem final
        let finalMessage = '';
        if (message.length > 0) {
            finalMessage = `Encontrados ${itemsFound} itens para ${message.join(' e ')}`;
        } else {
            finalMessage = 'Selecione os filtros desejados';
            messageType = 'info';
        }

        // Cria e mostra o elemento de feedback
        const $feedback = $('<div>')
            .addClass(`alert alert-${messageType}`)
            .text(finalMessage)
            .hide();

        $('#feedbackContainer').empty().append($feedback);
        $feedback.fadeIn();
    }

    // Botão para limpar filtros (opcional)
    $('#clear-filters').on('click', function() {
        // Limpa select de linha
        $('#line_product').val('');
        
        // Limpa TomSelect de segmentos
        segmentSelect.clear();
        
        // Limpa checkboxes e highlighting
        $('.segment-checkbox').prop('checked', false);
        $('.listItem').removeClass('highlighted');
        
        // Limpa feedback
        $('#feedbackContainer').empty();
    });
});



</script>



