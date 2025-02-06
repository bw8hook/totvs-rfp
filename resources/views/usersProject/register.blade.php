<x-app-layout>
    <div class="flex min-w-full flex-col">
        <div style="display: flex; padding-top: 10px; align-self: end;">
            <x-profile-bar></x-profile-bar>
        </div>
    </div>
    <div class="flex items-center justify-center  flex-col">
        <div class="w-full justify-center items-center flex flex-column" style="width: 100%; max-width: 80%;">

            <x-title-component :showButton="false">Novo Usuário</x-title-component>
            
            <div class="list-form" style="margin-top: 21px;">
                <form method="POST" action="{{ route('register') }}" style=" margin: auto; width: 40%; min-width: 450px;">
                @csrf

                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Nome')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email Address -->
                <div class="mt-4">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                 <!-- Name -->
                 <div>
                    <x-input-label for="idtotvs" :value="__('ID Totvs')" />
                    <x-text-input id="idtotvs" class="block mt-1 w-full" type="text" name="idtotvs" :value="old('idtotvs')" required autofocus autocomplete="idtotvs" />
                    <x-input-error :messages="$errors->get('idtotvs')" class="mt-2" />
                </div>


                <!-- USER POSITION -->
                <div class="mt-4" style="height: 86px;">
                    <label for="user_position" class="block text-sm font-medium text-gray-700 mb-2">Selecione o tipo da conta</label>
                    <select id="totvs-erp" name="user_position" style="background-color: #f1f1f1;">
                        <option value="default" selected>Padrão</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>

                <!-- USER POSITION -->
                <div class="mt-4" style="height: 86px;">
                    <label for="user_position" class="block text-sm font-medium text-gray-700 mb-2">Selecione o Cargo</label>
                    <select id="totvs-erp" name="user_position" style="background-color: #f1f1f1;">
                        @foreach($ListPositions as $ListPosition)
                            <!-- Código que será executado para cada item -->
                            <option value="{{ $ListPosition['id'] }}">{{ $ListPosition['position'] }}</option>
                        @endforeach
                    </select>
                </div>


                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Senha')" />

                    <x-text-input id="password" class="block mt-1 w-full"
                                    type="password"
                                    name="password"
                                    required autocomplete="new-password" />

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>    
                
                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Confirme a Senha')" />

                    <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                    type="password"
                                    name="password_confirmation" required autocomplete="new-password" />

                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
                
                <div class="flex items-center justify-end mt-4">

                    <x-primary-button class="ms-4 btn_enviar">
                        {{ __('Adicionar novo Usuário') }}
                    </x-primary-button>
                </div>



            </form>
        </div>
           
        </div>
    </div>
</x-app-layout>




