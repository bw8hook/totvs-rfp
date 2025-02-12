<x-app-layout>
    <div class="flex flex-col">
        <div class="py-4" style=" padding-bottom: 130px;">

            <x-title-component :showButton="false">Editar Produto</x-title-component>
            
            <div class="list-form" style="margin-top: 21px;">
                <form method="POST" action="{{ route('bundles.edit_user') }}" style=" margin: auto; width: 40%; min-width: 450px;">
                @csrf

                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Nome')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" value="{{ old('name', $user->bundle) }}" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>


                <div x-data="dropdown()" class="relative w-full max-w-md">
                    <!-- Campo de Seleção -->
                    <div @click="toggle()" class="border rounded-lg p-2 flex flex-wrap items-center gap-2 cursor-pointer bg-white">
                        <template x-for="(item, index) in selectedItems" :key="index">
                            <div class="bg-gray-200 text-gray-700 px-2 py-1 rounded flex items-center">
                                <span x-text="item.name"></span>
                                <button @click.stop="removeItem(index)" class="ml-1 text-gray-500 hover:text-gray-700">✕</button>
                            </div>
                        </template>
                        <input type="text" class="border-none focus:ring-0 flex-grow" placeholder="Selecione..." readonly />
                    </div>

                    <!-- Lista de Opções (Aparece ao Clicar) -->
                    <div x-show="open" @click.away="open = false" class="absolute mt-1 w-full bg-white border rounded-lg shadow-lg z-10">
                        <template x-for="(item, index) in options" :key="index">
                            <div class="flex items-center justify-between px-3 py-2 hover:bg-gray-100 cursor-pointer"
                                @click="toggleItem(item)">
                                <span x-text="item.name"></span>
                                <span x-text="item.count + ' documentos'" class="text-gray-500 text-sm"></span>
                            </div>
                        </template>
                        <div class="p-2 text-blue-600 text-sm cursor-pointer hover:underline">+ Nova base de conhecimento</div>
                    </div>
                </div>





                <!-- Status -->
                <div>
                    <x-input-label for="status" :value="__('Status')" />
                    <div class="flex items-center gap-4 mt-1">
                        <label>
                            <input type="radio" name="status" value="active" {{ old('status', $user->status) === 'active' ? 'checked' : '' }} style="height: 7px; padding: 7px; margin-bottom: 2px;">
                            Ativo
                        </label>

                        <label>
                            <input type="radio" name="status" value="inactive"
                                {{ old('status', $user->status) === 'inactive' ? 'checked' : '' }} style="height: 7px; padding: 7px; margin-bottom: 2px;">
                            Inativo
                        </label>
                    </div>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>



                <input type="hidden" name="bundle_id" value="{{$user->bundle_id}}" />

          
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
    function dropdown() {
        return {
            open: false,
            selectedItems: [],
            options: [
                { id: 1, name: "RFPs API", count: 6103 },
                { id: 2, name: "RFP's Preenchidas", count: 7542 },
            ],

            toggle() {
                this.open = !this.open;
            },

            toggleItem(item) {
                const index = this.selectedItems.findIndex(i => i.id === item.id);
                if (index === -1) {
                    this.selectedItems.push(item);
                } else {
                    this.selectedItems.splice(index, 1);
                }
            },

            removeItem(index) {
                this.selectedItems.splice(index, 1);
            }
        };
    }
</script>


