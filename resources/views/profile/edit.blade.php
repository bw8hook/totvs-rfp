<x-app-layout>
    <div class="py-4" style=" padding-bottom: 130px;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <x-title-component :showButton="false" componentType="edit" titleDescription="Confirme as informações:">Editar minhas informações</x-title-component>
            
            <div class="list-form" style="margin-top: 21px;">
                <form method="POST" action="{{ route('user.edit') }}" enctype="multipart/form-data" style=" margin: auto; width: 50%; min-width: 450px;">
                @csrf

                <x-text-input id="id" type="hidden" name="id" value="{{ old('name', $user->id) }}" required />

                <div class="img_profile">
                    <input type="file" id="photo-upload" name="profile_picture" accept="image/*">

                    <svg width="70px" height="70px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 17V9C21 7.89543 20.1046 7 19 7H16.5C15.9477 7 15.5 6.55228 15.5 6C15.5 5.44772 15.0523 5 14.5 5H9.5C8.94772 5 8.5 5.44772 8.5 6C8.5 6.55228 8.05228 7 7.5 7H5C3.89543 7 3 7.89543 3 9V17C3 18.1046 3.89543 19 5 19H19C20.1046 19 21 18.1046 21 17Z" stroke="#000000" stroke-width="1.5"/>
                        <path d="M15 13C15 14.6569 13.6569 16 12 16C10.3431 16 9 14.6569 9 13C9 11.3431 10.3431 10 12 10C13.6569 10 15 11.3431 15 13Z" stroke="#000000" stroke-width="1.5"/>
                    </svg>

                    <div class="preview">
                        <img src="{{ asset('storage/' .$user->profile_picture) }}" alt="">
                    </div>
                </div>
                


                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Nome')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email Address -->
                <div class="mt-4">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"  value="{{ old('name', $user->email) }}" required  />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                
                 <!-- ID TOTVS -->
                 <div class="mt-4">
                    <x-input-label for="idtotvs" :value="__('ID TOTVS')" />
                    <x-text-input id="idtotvs" class="block mt-1 w-full" type="text" name="idtotvs"  value="{{ old('ID TOTVS', $user->idtotvs) }}" />
                    <x-input-error :messages="$errors->get('idtotvs')" class="mt-2" />
                </div>

                <!-- SETOR -->
                <div class="mt-4">
                    <x-input-label for="departament_id" :value="__('Departamento')" />
                    <select name="departament[]"  class="form-control">
                        <option selected disabled>Selecione um Setor</option>
                        @foreach($userDepartaments as $userDepartament)
                            @if ($userDepartament->id == $user->departament->id)
                                <option selected value="{{$userDepartament->id}}"> {{$userDepartament->departament}}</option>
                            @else
                                <option value="{{$userDepartament->id}}" >{{$userDepartament->departament}}</option>
                            @endif 
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('departament')" class="mt-2" />
                </div>
                
               

                <!-- SKILLS -->
                <div class="mt-4">
                    <x-input-label for="idtotvs" :value="__('Skills')" />
                    <div class="dropdown">
                        <label class="dropdown-label">Selecione as skills</label>
                
                        <div class="dropdown-list">
                            <div class="checkbox">
                                <input type="checkbox" name="dropdown-group" class="check checkbox-custom" id="checkbox-custom_01"/>
                                <label for="checkbox-custom_01" class="checkbox-custom-label">Protheus</label>
                            </div>
                            
                            <div class="checkbox">
                                <input type="checkbox" name="dropdown-group" class="check checkbox-custom" id="checkbox-custom_02"/>
                                <label for="checkbox-custom_02" class="checkbox-custom-label">Whintor</label>
                            </div>
                            
                            <div class="checkbox">
                                <input type="checkbox" name="dropdown-group" class="check checkbox-custom" id="checkbox-custom_03"/>
                                <label for="checkbox-custom_03" class="checkbox-custom-label">Datasul</label>
                            </div>
                        </div>
                    </div>
                </div>
  
               <!-- TIPO DE CONTA -->
               <div class="mt-4">
                    <x-input-label for="account_type" :value="__('Tipo de Conta')" />
                    <select name="account_type[]"  class="form-control">
                        @if (empty($user->account_type))
                            <option selected disabled>Tipo de Conta</option>
                        @else
                            <option disabled>Tipo de Conta</option>
                        @endif 
                        

                        @if ($user->account_type == "admin")
                            <option value="admin" selected>Administrador Master</option>
                        @else
                            <option value="admin">Administrador Master</option>
                        @endif 

                        @if ($user->account_type == "gestor")
                            <option value="gestor" selected>Gestor</option>
                        @else
                            <option value="gestor">Gestor</option>
                        @endif 

                        @if ($user->account_type == "especialista")
                            <option value="especialista" selected>Especialista Totvs</option>
                        @else
                            <option value="especialista">Especialista Totvs</option>
                        @endif 

                        @if ($user->account_type == "executivo")
                            <option value="executivo" selected>Executivo de Vendas</option>
                        @else
                            <option value="executivo">Executivo de Vendas</option>
                        @endif 

                    </select>
                    <x-input-error :messages="$errors->get('account_type')" class="mt-2" />
                </div>
          
                <div class="flex items-center justify-end mt-4">

                    <x-primary-button class="ms-4 btn_enviar">
                        {{ __('Confirmar alterações') }}
                    </x-primary-button>
                </div>



            </form>
        </div>


           
        </div>
    </div>
</x-app-layout>

<script>

function checkboxDropdown(el) {
  var $el = $(el)

  function updateStatus(label, result) {
    if(!result.length) {
      label.html('Select Options');
    }
  };
  
  $el.each(function(i, element) {
    var $list = $(this).find('.dropdown-list'),
      $label = $(this).find('.dropdown-label'),
      $checkAll = $(this).find('.check-all'),
      $inputs = $(this).find('.check'),
      defaultChecked = $(this).find('input[type=checkbox]:checked'),
      result = [];
    
    updateStatus($label, result);
    if(defaultChecked.length) {
      defaultChecked.each(function () {
        result.push($(this).next().text());
        $label.html(result.join(", "));
      });
    }
    
    $label.on('click', ()=> {
      $(this).toggleClass('open');
    });

    $checkAll.on('change', function() {
      var checked = $(this).is(':checked');
      var checkedText = $(this).next().text();
      result = [];
      if(checked) {
        result.push(checkedText);
        $label.html(result);
        $inputs.prop('checked', false);
      }else{
        $label.html(result);
      }
        updateStatus($label, result);
    });

    $inputs.on('change', function() {
      var checked = $(this).is(':checked');
      var checkedText = $(this).next().text();
      if($checkAll.is(':checked')) {
        result = [];
      }
      if(checked) {
        result.push(checkedText);
        $label.html(result.join(", "));
        $checkAll.prop('checked', false);
      }else{
        let index = result.indexOf(checkedText);
        if (index >= 0) {
          result.splice(index, 1);
        }
        $label.html(result.join(", "));
      }
      updateStatus($label, result);
    });

    $(document).on('click touchstart', e => {
      if(!$(e.target).closest($(this)).length) {
        $(this).removeClass('open');
      }
    });
  });
};

checkboxDropdown('.dropdown');


</script>


<script>
        const fileInput = document.getElementById('photo-upload');
        const previewContainer = document.querySelector('.preview');
        const previewText = previewContainer.querySelector('span');

        fileInput.addEventListener('change', function() {
            const file = this.files[0];

            if (file) {
                const reader = new FileReader();

                reader.addEventListener('load', function() {
                    const imgElement = document.createElement('img');
                    imgElement.src = this.result;

                    // Limpa o preview anterior e adiciona a nova imagem
                    previewContainer.innerHTML = '';
                    previewContainer.appendChild(imgElement);
                });

                reader.readAsDataURL(file);
            } else {
                // Volta ao estado inicial se nenhum arquivo for selecionado
                previewContainer.innerHTML = '';
            }
        });
    </script>