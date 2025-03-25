<x-app-layout>
    <div class="flex flex-col">
        <div class="py-4" style=" padding-bottom: 130px;">

            <x-title-component :showButton="false">Editar Produto</x-title-component>
            
            <div class="list-form" style="margin-top: 21px;">
                <form method="POST" id="myForm" action="{{ route('bundles.update', $id) }}" style=" margin: auto; width: 40%; min-width: 450px;">
                @csrf

                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Nome')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" value="{{ old('name', $bundle->bundle) }}" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>


                <div>
                    <x-input-label for="lineofproducts" :value="__('Selecione Linha de Produto(s)')" />
                    <div id="dropdown-wrapper" class="relative w-full">
                        <!-- Campo de Seleção -->
                        <div id="dropdown-toggle" class="border rounded-lg p-2 flex items-center gap-2 cursor-pointer bg-white flex-wrap" style="height: 60px;">
                            @if (!empty($LinesSelected))
                                <div id="selected-items" class="flex flex-wrap gap-2"><div class="selected-item flex items-center bg-gray-200 text-gray-700 px-2 py-1 rounded">
                                        <span>{{$LinesSelected->name}}</span>
                                        <button class="ml-1 text-gray-500 hover:text-gray-700" data-id="{{$LinesSelected->id}}">✕</button>
                                    </div>
                                </div>

                                <span id="placeholder-text" class="text-gray-500" hidden>Selecione...</span>
                            @else
                                <div id="selected-items" class="flex flex-wrap gap-2"></div>
                                <span id="placeholder-text" class="text-gray-500">Selecione...</span>
                            @endif    
                        </div>

                        <!-- Lista de Opções -->
                        <div id="dropdown-menu" class="hidden absolute mt-1 w-full bg-white border rounded-lg shadow-lg z-10">
                            <div id="dropdown-options">
                                @foreach ($lineofproducts as $index => $item)
                                    <div class="option-item flex items-center justify-between px-3 py-2 hover:bg-gray-100 cursor-pointer" data-id="{{ $item['id'] }}" data-name="{{ $item['name'] }}" data-documents="{{ $item['document_count'] }}">
                                        <span>{{ $item['name'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>


                <div>
                    <x-input-label for="segments" :value="__('Selecione Segmento(s)')" />
                    <div id="dropdown-wrapper" class="relative w-full">
                        <!-- Campo de Seleção -->
                        <div id="dropdown-toggle" class="border rounded-lg p-2 flex items-center gap-2 cursor-pointer bg-white flex-wrap" style="height: 60px;">
                            @if (!empty($SegmentsSelected))
                                <div id="selected-items" class="flex flex-wrap gap-2"><div class="selected-item flex items-center bg-gray-200 text-gray-700 px-2 py-1 rounded">
                                        <span>{{$SegmentsSelected->name}}</span>
                                        <button class="ml-1 text-gray-500 hover:text-gray-700" data-id="{{$SegmentsSelected->id}}">✕</button>
                                    </div>
                                </div>

                                <span id="placeholder-text" class="text-gray-500" hidden>Selecione...</span>
                            @else
                                <div id="selected-items" class="flex flex-wrap gap-2"></div>
                                <span id="placeholder-text" class="text-gray-500">Selecione...</span>
                            @endif    
                        </div>

                        <!-- Lista de Opções -->
                        <div id="dropdown-menu" class="hidden absolute mt-1 w-full bg-white border rounded-lg shadow-lg z-10">
                            <div id="dropdown-options">
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



                <div>
                    <x-input-label for="modules" :value="__('Selecione Módulo(s)')" />
                    <div id="dropdown-wrapper" class="relative w-full">
                        <!-- Campo de Seleção -->
                        <div id="dropdown-toggle" class="border rounded-lg p-2 flex items-center gap-2 cursor-pointer bg-white flex-wrap" style="height: 60px;">
                            @if (!empty($ModulesSelected))
                                <div id="selected-items" class="flex flex-wrap gap-2"><div class="selected-item flex items-center bg-gray-200 text-gray-700 px-2 py-1 rounded">
                                        <span>{{$ModulesSelected->name}}</span>
                                        <button class="ml-1 text-gray-500 hover:text-gray-700" data-id="{{$ModulesSelected->id}}">✕</button>
                                    </div>
                                </div>

                                <span id="placeholder-text" class="text-gray-500" hidden>Selecione...</span>
                            @else
                                <div id="selected-items" class="flex flex-wrap gap-2"></div>
                                <span id="placeholder-text" class="text-gray-500">Selecione...</span>
                            @endif    
                        </div>

                        <!-- Lista de Opções -->
                        <div id="dropdown-menu" class="hidden absolute mt-1 w-full bg-white border rounded-lg shadow-lg z-10">
                            <div id="dropdown-options">
                                @foreach ($modules as $index => $item)
                                    <div class="option-item flex items-center justify-between px-3 py-2 hover:bg-gray-100 cursor-pointer" data-id="{{ $item['id'] }}" data-name="{{ $item['name'] }}" data-documents="{{ $item['document_count'] }}">
                                        <span>{{ $item['name'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>


                <div>
                    <x-input-label for="process" :value="__('Selecione Processo(s)')" />
                    <div id="dropdown-wrapper" class="relative w-full">
                        <!-- Campo de Seleção -->
                        <div id="dropdown-toggle" class="border rounded-lg p-2 flex items-center gap-2 cursor-pointer bg-white flex-wrap" style="height: 60px;">
                            @if (!empty($ProcessSelected))
                                <div id="selected-items" class="flex flex-wrap gap-2"><div class="selected-item flex items-center bg-gray-200 text-gray-700 px-2 py-1 rounded">
                                        <span>{{$ProcessSelected->process}}</span>
                                        <button class="ml-1 text-gray-500 hover:text-gray-700" data-id="{{$ProcessSelected->id}}">✕</button>
                                    </div>
                                </div>

                                <span id="placeholder-text" class="text-gray-500" hidden>Selecione...</span>
                            @else
                                <div id="selected-items" class="flex flex-wrap gap-2"></div>
                                <span id="placeholder-text" class="text-gray-500">Selecione...</span>
                            @endif    
                        </div>

                        <!-- Lista de Opções -->
                        <div id="dropdown-menu" class="hidden absolute mt-1 w-full bg-white border rounded-lg shadow-lg z-10">
                            <div id="dropdown-options">
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
                    <x-input-label for="status" :value="__('Status')" />
                    <select name="status"  class="form-control">
                        <option value="ativo"  {{ $bundle->status === 'active' ? 'selected' : '' }} >Ativo</option>
                       <option value="inativo"  {{ $bundle->status === 'inactive' ? 'selected' : '' }} >Inativo</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>


                <input type="hidden" name="bundle_id" value="{{$bundle->bundle_id}}" />

          
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
            let isMultiple = false; // Flag para permitir seleção múltipla

            let $wrapper = $("#dropdown-wrapper");
            let $toggle = $("#dropdown-toggle");
            let $menu = $("#dropdown-menu");
            let $selectedItemsContainer = $("#selected-items");
            let $placeholderText = $("#placeholder-text");

            let selectedItems = [];

            // Mostrar/ocultar dropdown ao clicar no campo de seleção
            $toggle.on("click", function () {
                $menu.toggleClass("hidden");
            });

            // Selecionar um item ao clicar
            $(".option-item").on("click", function () {
                let agentName = $(this).data("name");
                let agentId = $(this).data("id");

                if (isMultiple) {
                    if (selectedItems.includes(agentId)) {
                        // Remover item se já estiver selecionado
                        selectedItems = selectedItems.filter(id => id !== agentId);
                        $(this).removeClass("bg-gray-200");
                        removeSelectedItemBlock(agentId);
                    } else {
                        // Adicionar item à seleção
                        selectedItems.push(agentId);
                        $(this).addClass("bg-gray-200");
                        addSelectedItemBlock(agentId, agentName);
                    }
                } else {
                    // Seleção única
                    selectedItems = [agentId];
                    $selectedItemsContainer.empty();
                    addSelectedItemBlock(agentId, agentName);
                    $menu.addClass("hidden");
                }

                // Esconder o menu após a seleção
                $menu.addClass("hidden");

                updatePlaceholderText();
                updateHiddenInputs();
            });

            // Adicionar bloco de item selecionado
            function addSelectedItemBlock(agentId, agentName) {
                let itemBlock = $(`
                    <div class="selected-item flex items-center bg-gray-200 text-gray-700 px-2 py-1 rounded">
                        <span>${agentName}</span>
                        <button class="ml-1 text-gray-500 hover:text-gray-700" data-id="${agentId}">✕</button>
                    </div>
                `);

                // Adicionar evento de clique para remover o item
                itemBlock.find("button").on("click", function (event) {
                    event.stopPropagation();
                    let idToRemove = $(this).data("id");
                    selectedItems = selectedItems.filter(id => id !== idToRemove);
                    removeSelectedItemBlock(idToRemove);
                    $(".option-item[data-id=" + idToRemove + "]").removeClass("bg-gray-200");
                    updatePlaceholderText();
                    updateHiddenInputs();
                });

                $selectedItemsContainer.append(itemBlock);
            }

            // Remover bloco de item selecionado
            function removeSelectedItemBlock(agentId) {
                $selectedItemsContainer.find("button[data-id=" + agentId + "]").parent().remove();
            }

            // Atualizar texto do placeholder
            function updatePlaceholderText() {
                if (selectedItems.length === 0) {
                    $placeholderText.removeClass("hidden");
                } else {
                    $placeholderText.addClass("hidden");
                }
            }

            // Atualizar campos ocultos
            function updateHiddenInputs() {
                // Remove todos os campos ocultos existentes
                $("input[name='selected_agents[]']").remove();

                // Adiciona novos campos ocultos para cada item selecionado
                selectedItems.forEach(id => {
                    let hiddenInput = `<input type="hidden" name="selected_agents[]" value="${id}">`;
                    $("#myForm").append(hiddenInput);
                });
            }

            // Fechar dropdown ao clicar fora
            $(document).on("click", function (event) {
                if (!$wrapper.is(event.target) && $wrapper.has(event.target).length === 0) {
                    $menu.addClass("hidden");
                }
            });
        });
    </script>