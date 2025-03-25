<x-app-layout>
    <div class="flex flex-col">
        <div class="py-4" style=" padding-bottom: 130px;">

            <x-title-component :showButton="false">Editar Produto</x-title-component>
            
            <div class="list-form" style="margin-top: 21px;">
                <form method="POST" id="myForm" action="{{ route('bundles.register') }}" style=" margin: auto; width: 40%; min-width: 450px;">
                @csrf

                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Nome')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>


                <div class="mt-4">
                    <x-input-label for="categories" :value="__('Selecione a Categoria')" />
                    <select name="categories"  class="form-control">
                        @foreach ($categories as $index => $item)
                            <option value="{{ $item['id'] }}"  >{{ $item['name'] }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('categories')" class="mt-2" />
                </div>


                <div class="mt-4">
                    <x-input-label for="types" :value="__('Selecione o Tipo')" />
                    <select name="types"  class="form-control">
                        @foreach ($types as $index => $item)
                            <option value="{{ $item['id'] }}"  >{{ $item['name'] }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('types')" class="mt-2" />
                </div>
            
                <!-- Linha de Produtos -->
                <div>
                    <x-input-label for="lineofproducts" :value="__('Selecione Linha de Produto(s)')" />
                    <div id="dropdown-wrapper-line" class="relative w-full">
                        <div id="dropdown-toggle-line" class="border rounded-lg p-2 flex items-center gap-2 cursor-pointer bg-white flex-wrap" style="min-height: 60px;">
                            @if (!empty($LinesSelected))
                                <div id="selected-items-line" class="flex flex-wrap gap-2">
                                    @foreach ($LinesSelected as $index => $LineSelected)
                                        <div class="selected-item flex items-center bg-gray-200 text-gray-700 px-2 py-1 rounded">
                                            <span>{{$LineSelected->name}}</span>
                                            <button class="ml-1 text-gray-500 hover:text-gray-700" data-id="{{$LineSelected->id}}">✕</button>
                                        </div>
                                    @endforeach
                                </div>
                                <span id="placeholder-text-line" class="text-gray-500" hidden>Selecione...</span>
                            @else
                                <div id="selected-items-line" class="flex flex-wrap gap-2"></div>
                                <span id="placeholder-text-line" class="text-gray-500">Selecione...</span>
                            @endif    
                        </div>
                        <div id="dropdown-menu-line" class="hidden absolute mt-1 w-full bg-white border rounded-lg shadow-lg z-10">
                            <div id="dropdown-options-line">
                                @foreach ($lineofproducts as $index => $item)
                                    <div class="option-item flex items-center justify-between px-3 py-2 hover:bg-gray-100 cursor-pointer" data-id="{{ $item['id'] }}" data-name="{{ $item['name'] }}" data-documents="{{ $item['document_count'] }}">
                                        <span>{{ $item['name'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Segmentos -->
                <div>
                    <x-input-label for="segments" :value="__('Selecione Segmento(s)')" />
                    <div id="dropdown-wrapper-segment" class="relative w-full">
                        <div id="dropdown-toggle-segment" class="border rounded-lg p-2 flex items-center gap-2 cursor-pointer bg-white flex-wrap" style="min-height: 60px;">
                            @if (!empty($SegmentsSelected))
                                <div id="selected-items-segment" class="flex flex-wrap gap-2">
                                    @foreach ($SegmentsSelected as $index => $SegmentSelected)
                                        <div class="selected-item flex items-center bg-gray-200 text-gray-700 px-2 py-1 rounded">
                                            <span>{{$SegmentSelected->name}}</span>
                                            <button class="ml-1 text-gray-500 hover:text-gray-700" data-id="{{$SegmentSelected->id}}">✕</button>
                                        </div>
                                    @endforeach
                                </div>
                                <span id="placeholder-text-segment" class="text-gray-500" hidden>Selecione...</span>
                            @else
                                <div id="selected-items-segment" class="flex flex-wrap gap-2"></div>
                                <span id="placeholder-text-segment" class="text-gray-500">Selecione...</span>
                            @endif    
                        </div>
                        <div id="dropdown-menu-segment" class="hidden absolute mt-1 w-full bg-white border rounded-lg shadow-lg z-10">
                            <div id="dropdown-options-segment">
                                @foreach ($segments as $index => $item)
                                    <div class="option-item flex items-center justify-between px-3 py-2 hover:bg-gray-100 cursor-pointer" data-id="{{ $item['id'] }}" data-name="{{ $item['name'] }}" data-documents="{{ $item['document_count'] }}">
                                        <span>{{ $item['name'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <x-input-label for="agents" :value="__('Selecione o Agente')" />
                    <select name="agents"  class="form-control">
                        @foreach ($agents as $index => $item)
                            <option value="{{ $item['id'] }}"  >{{ $item['agent_name'] }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('agents')" class="mt-2" />
                </div>


                <!-- Módulos -->
                <div>
                    <x-input-label for="modules" :value="__('Selecione Módulo(s)')" />
                    <div id="dropdown-wrapper-module" class="relative w-full">
                        <div id="dropdown-toggle-module" class="border rounded-lg p-2 flex items-center gap-2 cursor-pointer bg-white flex-wrap" style="min-height: 60px;">
                            @if (!empty($ModulesSelected))
                                <div id="selected-items-module" class="flex flex-wrap gap-2">
                                    @foreach ($ModulesSelected as $index => $ModuleSelected)
                                        <div class="selected-item flex items-center bg-gray-200 text-gray-700 px-2 py-1 rounded">
                                            <span>{{$ModuleSelected->name}}</span>
                                            <button class="ml-1 text-gray-500 hover:text-gray-700" data-id="{{$ModuleSelected->id}}">✕</button>
                                        </div>
                                    @endforeach
                                </div>
                                <span id="placeholder-text-module" class="text-gray-500" hidden>Selecione...</span>
                            @else
                                <div id="selected-items-module" class="flex flex-wrap gap-2"></div>
                                <span id="placeholder-text-module" class="text-gray-500">Selecione...</span>
                            @endif    
                        </div>
                        <div id="dropdown-menu-module" class="hidden absolute mt-1 w-full bg-white border rounded-lg shadow-lg z-10">
                            <div id="dropdown-options-module">
                                @foreach ($modules as $index => $item)
                                    <div class="option-item flex items-center justify-between px-3 py-2 hover:bg-gray-100 cursor-pointer" data-id="{{ $item['id'] }}" data-name="{{ $item['name'] }}" data-documents="{{ $item['document_count'] }}">
                                        <span>{{ $item['name'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Processos -->



               





                <div>
                    <x-input-label for="process" :value="__('Selecione Processo(s)')" />
                    <div id="dropdown-wrapper-process" class="relative w-full">
                        <div id="dropdown-toggle-process" class="border rounded-lg p-2 flex items-center gap-2 cursor-pointer bg-white flex-wrap" style="min-height: 60px;">
                            @if (!empty($ProcessSelected))
                                <div id="selected-items-process" class="flex flex-wrap gap-2">
                                    @foreach ($ProcessSelected as $index => $ProcesSelected)   
                                        <div class="selected-item flex items-center bg-gray-200 text-gray-700 px-2 py-1 rounded">
                                            <span>{{$ProcesSelected->process}}</span>
                                            <button class="ml-1 text-gray-500 hover:text-gray-700" data-id="{{$ProcesSelected->id}}">✕</button>
                                        </div>
                                    @endforeach
                                </div>
                                <span id="placeholder-text-process" class="text-gray-500" hidden>Selecione...</span>
                            @else
                                <div id="selected-items-process" class="flex flex-wrap gap-2"></div>
                                <span id="placeholder-text-process" class="text-gray-500">Selecione...</span>
                            @endif    
                        </div>
                        <div id="dropdown-menu-process" class="hidden absolute mt-1 w-full bg-white border rounded-lg shadow-lg z-10">
                            <div id="dropdown-options-process">
                                @foreach ($process as $index => $item)
                                    <div class="option-item flex items-center justify-between px-3 py-2 hover:bg-gray-100 cursor-pointer" data-id="{{ $item['id'] }}" data-name="{{ $item['process'] }}" data-documents="{{ $item['document_count'] }}">
                                        <span>{{ $item['process'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                 <!-- Status -->
                 <div class="mt-4">
                    <x-input-label for="status_totvs" :value="__('Status Totvs')" />
                    <select name="status_totvs"  class="form-control">
                        <option value="ativo" >Ativo</option>
                       <option value="descontinuado" >Descontinuado</option>
                    </select>
                    <x-input-error :messages="$errors->get('status_totvs')" class="mt-2" />
                </div>


                <!-- Status -->
                <div class="mt-4">
                    <x-input-label for="status" :value="__('Status')" />
                    <select name="status"  class="form-control">
                        <option value="active" >Ativo</option>
                       <option value="inactive" >Inativo</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>


             

          
                <div class="flex items-center justify-end mt-4">

                    <x-primary-button class="ms-4 btn_enviar">
                        {{ __('Atualizar Produto') }}
                    </x-primary-button>
                </div>
            </form>
        </div>


        
        </div>
    </div>
</x-app-layout>


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

            if (isMultiple) {
                if (selectedItems.includes(agentId)) {
                    selectedItems = selectedItems.filter(id => id !== agentId);
                    $(this).removeClass("bg-gray-200");
                    removeSelectedItemBlock(agentId);
                } else {
                    selectedItems.push(agentId);
                    $(this).addClass("bg-gray-200");
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
    initializeDropdown('line');
    initializeDropdown('segment');
    initializeDropdown('module');
    initializeDropdown('process');
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
});





    </script>