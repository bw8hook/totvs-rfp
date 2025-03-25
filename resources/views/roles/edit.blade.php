<x-app-layout>
    <div class="flex items-center justify-center  flex-col">
        <div class="w-full justify-center items-center flex flex-column" style="width: 100%; max-width: 80%;">

            <x-title-component :showButton="false">Editar Perfil</x-title-component>
            
            <div class="list-form" style="margin-top: 21px;">
                <form action="{{ route('roles.update', $role->id) }}" method="POST" style=" margin: auto; width: 40%; min-width: 450px;">
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
                    @foreach($groupedPermissions as $groupName => $permissions)
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="form-check">
                                <input type="checkbox" 
                                style="height: 6px; padding: 8px; margin-bottom: 5px;" 
                                    class="form-check-input group-selector" 
                                    id="group_{{ $groupName }}"
                                    data-group="{{ $groupName }}">
                                        <label class="form-check-label" for="group_{{ $groupName }}">
                                            @switch($groupName)
                                                @case('knowledge')
                                                    Base de Conhecimento
                                                    @break
                                                @case('projects_all')
                                                    Todos os Projetos
                                                    @break
                                                @case('projects_my')
                                                    Projetos Próprios
                                                    @break
                                                @case('users')
                                                    Usuários
                                                    @break
                                                @case('config')
                                                    Configurações
                                                    @break
                                                @default
                                                    {{ ucfirst($groupName) }}
                                            @endswitch
                                        </label>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @foreach($permissions as $permission)
                                        <div class="form-check">
                                            <input type="checkbox" 
                                                class="form-check-input permission-checkbox"
                                                name="permissions[]" 
                                                value="{{ $permission->name }}"
                                                data-group="{{ $groupName }}"
                                                style="height: 6px; padding: 8px; margin-bottom: 5px;" 
                                                {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}
                                                id="perm_{{ $permission->name }}">
                                            <label class="form-check-label" for="perm_{{ $permission->name }}">
                                                {{ $permission->show_name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-end mt-4">

                    <x-primary-button class="ms-4 btn_enviar">
                        {{ __('Editar Perfil') }}
                    </x-primary-button>
                </div>



            </form>
        </div>
           
        </div>
    </div>
</x-app-layout>






<script>
document.addEventListener('DOMContentLoaded', function() {
    // Função para verificar se todas as permissions de um grupo estão selecionadas
    function checkGroupState(groupName) {
        const groupPermissions = document.querySelectorAll(`.permission-checkbox[data-group="${groupName}"]`);
        const groupCheckbox = document.querySelector(`.group-selector[data-group="${groupName}"]`);
        const allChecked = Array.from(groupPermissions).every(checkbox => checkbox.checked);
        groupCheckbox.checked = allChecked;
    }

    // Inicializar estado dos checkboxes de grupo
    document.querySelectorAll('.group-selector').forEach(function(checkbox) {
        const group = checkbox.dataset.group;
        checkGroupState(group);
    });

    // Manipular checkbox do grupo
    document.querySelectorAll('.group-selector').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const group = this.dataset.group;
            const isChecked = this.checked;
            
            document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`).forEach(function(permissionCheckbox) {
                permissionCheckbox.checked = isChecked;
            });
        });
    });

    // Atualizar checkbox do grupo quando permissions individuais são alteradas
    document.querySelectorAll('.permission-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const group = this.dataset.group;
            checkGroupState(group);
        });
    });
});
</script>

<style>
.card {
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-bottom: 1rem;
    padding: 0px !important;
}

.card-header {
    background-color: #f8f9fa;
    padding: 0.75rem 1.25rem;
    padding-bottom: 0px;
}

.card-body {
    padding: 1.25rem;
}

.form-check {
    margin-bottom: 0.5rem;
}
</style>