<x-app-layout>
    <div class="flex min-w-full flex-col">
        <div style="display: flex; padding-top: 10px; align-self: end;">
            <x-profile-bar></x-profile-bar>
        </div>
    </div>
    <div class="flex items-center justify-center  flex-col">
        <div class="w-full justify-center items-center flex flex-column" style="width: 100%; max-width: 80%;">

            <x-title-component :showButton="false">Novo Produto/Linha</x-title-component>
            
            <div class="list-form" style="margin-top: 21px;">
                <form method="POST" action="{{ route('bundles.register') }}" style=" margin: auto; width: 40%; min-width: 450px;">
                @csrf

                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Nome')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-4">

                    <x-primary-button class="ms-4 btn_enviar">
                        {{ __('Adicionar novo Produto') }}
                    </x-primary-button>
                </div>



            </form>
        </div>
           
        </div>
    </div>
</x-app-layout>




