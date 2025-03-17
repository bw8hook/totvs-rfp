<x-app-layout>
    <div class="flex items-center justify-center  flex-col">
        <div class="w-full justify-center items-center flex flex-column" style="width: 100%; max-width: 80%;">

            <x-title-component :showButton="false">Novo Perfil</x-title-component>
            
            <div class="list-form" style="margin-top: 21px;">
                <form method="POST" action="{{ route('roles.store') }}" style=" margin: auto; width: 40%; min-width: 450px;">
                @csrf

                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Nome')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')"  value="{{ old('name', $role->name) }}"  required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>


                <div class="form-group">
                    <label>Permissões</label>
                    <div class="row">
                        @foreach($permissions as $permission)
                            <div class="col-md-12">
                                <div class="custom-control custom-checkbox" style="width: 100%;">
                                    <input type="checkbox" style="height: 6px; padding: 8px; margin-bottom: 5px;" class="custom-control-input" id="permission_{{ $permission->id }}" name="permissions[]" value="{{ $permission->name }}">
                                    <label class="custom-control-label" for="permission_{{ $permission->id }}"> {{ $permission->show_name }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-end mt-4">

                    <x-primary-button class="ms-4 btn_enviar">
                        {{ __('Adicionar novo Perfil') }}
                    </x-primary-button>
                </div>



            </form>
        </div>
           
        </div>
    </div>
</x-app-layout>




