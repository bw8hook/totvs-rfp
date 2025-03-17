<x-app-layout>
    <div style=" padding-bottom: 130px;">
        <div class="max-w-7xl mx-auto space-y-6">
    

            <div class="list-form" style="padding-top:0px;">
                  
                <div id="titleComponentForm" class=" flex items-center justify-between px-4 space-x-2 relative">
                    <div class="flex items-center space-x-2" style="display: block; margin-left: 30px;">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: inline; margin-top: 3px; width: 27px; margin-right: 1px;">
                            <path d="M30.4707 1.53065C29.5642 0.62558 28.3356 0.117233 27.0547 0.117233C25.7737 0.117233 24.5451 0.62558 23.6387 1.53065L1.95335 23.216C1.33236 23.8335 0.83997 24.568 0.504684 25.377C0.169399 26.1861 -0.00213064 27.0536 1.9975e-05 27.9293V30.6666C1.9975e-05 31.0203 0.140496 31.3594 0.390544 31.6095C0.640593 31.8595 0.979731 32 1.33335 32H4.07069C4.94637 32.0025 5.81385 31.8312 6.62289 31.4961C7.43194 31.1611 8.16649 30.6689 8.78402 30.048L30.4707 8.36132C31.3754 7.45491 31.8834 6.2266 31.8834 4.94598C31.8834 3.66536 31.3754 2.43706 30.4707 1.53065ZM6.89869 28.1626C6.14669 28.9096 5.13064 29.3303 4.07069 29.3333H2.66669V27.9293C2.66534 27.4039 2.76823 26.8833 2.9694 26.3979C3.17058 25.9125 3.46604 25.4718 3.83869 25.1013L20.296 8.64398L23.3627 11.7107L6.89869 28.1626ZM28.584 6.47598L25.2427 9.81865L22.176 6.75865L25.5187 3.41598C25.72 3.21506 25.959 3.05577 26.2219 2.9472C26.4849 2.83863 26.7666 2.7829 27.051 2.78321C27.3355 2.78352 27.6171 2.83986 27.8798 2.949C28.1425 3.05814 28.3811 3.21796 28.582 3.41932C28.7829 3.62068 28.9422 3.85964 29.0508 4.12256C29.1594 4.38549 29.2151 4.66722 29.2148 4.95167C29.2145 5.23613 29.1581 5.51774 29.049 5.78043C28.9399 6.04311 28.78 6.28173 28.5787 6.48265L28.584 6.47598Z" fill="#5570F1"/>
                        </svg>

                        <div style="display: inline-grid;">
                            <span style="color: #141824; font-size: 22px; font-weight: 600; line-height: 28.6px; text-align: left;">Edição de dados do usuário</span>
                            <div style="color:#8A94AD; font-size: 16px; font-weight: 400; line-height: 28.6px; text-align: left;">Edite o tipo de cadastro do usuário e confirme as alterações para concluir.</div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('user.edit') }}" enctype="multipart/form-data" style=" margin: auto; width: 65%; min-width: 450px; background: #FFF; box-shadow: 0px 4px 28px rgba(0, 0, 0, 0.1); border-radius: 15px; padding: 50px 15%;">
                @csrf

                <x-text-input id="id" type="hidden" name="id" value="{{ old('name', $user->id) }}" required />

                <div class="img_profile">
                    <input type="file" id="photo-upload" name="profile_picture" accept="image/*">

                    <svg width="70px" height="70px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 17V9C21 7.89543 20.1046 7 19 7H16.5C15.9477 7 15.5 6.55228 15.5 6C15.5 5.44772 15.0523 5 14.5 5H9.5C8.94772 5 8.5 5.44772 8.5 6C8.5 6.55228 8.05228 7 7.5 7H5C3.89543 7 3 7.89543 3 9V17C3 18.1046 3.89543 19 5 19H19C20.1046 19 21 18.1046 21 17Z" stroke="#000000" stroke-width="1.5"/>
                        <path d="M15 13C15 14.6569 13.6569 16 12 16C10.3431 16 9 14.6569 9 13C9 11.3431 10.3431 10 12 10C13.6569 10 15 11.3431 15 13Z" stroke="#000000" stroke-width="1.5"/>
                    </svg>

                    <div class="preview">
                        <img src="{{$user->profile_picture}}" alt="">
                    </div>

                    <span class="preview_chamada">Editar imagem</span>
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


                <!-- ID TOTVS -->
                <div class="mt-4">
                    <x-input-label for="position" :value="__('Cargo')" />
                    <x-text-input id="position" class="block mt-1 w-full" type="text" name="position"  value="{{ old('Cargo', $user->position) }}" />
                    <x-input-error :messages="$errors->get('position')" class="mt-2" />
                </div>

                <!-- SETOR -->
                <div class="mt-4">
                    <x-input-label for="departament_id" :value="__('Setor')" />
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
                
               

  
               <!-- TIPO DE CONTA -->
               <div class="mt-4">
                    <x-input-label for="account_type" :value="__('Tipo de Conta')" />
                    <select name="account_type[]"  class="form-control">
                        @foreach($roles as $role)
                         
                            @if ($role->name == $user_role[0])
                                <option selected value="{{$role->name}}"> {{$role->name}}</option>
                            @else
                                <option value="{{$role->name}}" >{{$role->name}}</option>
                            @endif 
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('account_type')" class="mt-2" />
                </div>
          
                @if ($user->status == "ativo")
                  <div class="flex items-center justify-end mt-4">
                      <div class="ms-4 btn_desativar" data-id="{{ $user->id }}">
                          Desativar acesso
                      </div>
                  </div>
                @else
                  <div class="flex items-center justify-end mt-4">
                      <div class="ms-4 btn_ativar" data-id="{{ $user->id }}">
                          Ativar acesso
                      </div>
                  </div>
                @endif

                
                <div class="flex items-center justify-end mt-3">
                    <button type="submit" class=" inline-flex items-center rounded-md font-semibold text-xs text-white btn_enviar" style="height: 46px; display: inline;">
                        <span>Confirmar alterações</span>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: inline;">
                            <path d="M13.5817 6.90667L14.7517 8.09417L9.92417 12.85C9.60167 13.1725 9.1775 13.3333 8.75167 13.3333C8.32583 13.3333 7.8975 13.1708 7.57167 12.8458L5.25333 10.5992L6.41417 9.40167L8.74167 11.6575L13.5817 6.90667ZM20 10C20 15.5142 15.5142 20 10 20C4.48583 20 0 15.5142 0 10C0 4.48583 4.48583 0 10 0C15.5142 0 20 4.48583 20 10ZM18.3333 10C18.3333 5.405 14.595 1.66667 10 1.66667C5.405 1.66667 1.66667 5.405 1.66667 10C1.66667 14.595 5.405 18.3333 10 18.3333C14.595 18.3333 18.3333 14.595 18.3333 10Z" fill="white"/>
                        </svg>

                    </button>
                </div>

                <a href="{{ route('users.list') }}" class="btn_voltar">Voltar</a>

            </form>
        </div>



      <!-- Popup -->
      <div id="overlay" style="display:none;"></div>
      <div id="confirmPopup" style="display:none;">
          @if ($user->status == "ativo")
            <p>Tem certeza que deseja desativar o acesso desse usuário?</p>
            <button id="confirmDesativar">Sim, desativar</button>
          @else
            <p>Tem certeza que deseja ativar o acesso desse usuário?</p>
            <button id="confirmDesativar">Sim, ativar</button>
          @endif
          <button id="cancelDesativar">Não, cancelar</button>
      </div>

      <!-- Formulário oculto -->
      <form id="desativarForm" method="POST" action="{{ route('users.status', $user->id) }}" style="display:none;">
          @csrf
          @if ($user->status == "ativo")
            <input type="hidden" name="status" value="inativo">
          @else
            <input type="hidden" name="status" value="ativo">
          @endif
         
      </form>



           
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


$(document).ready(function() {
    $('.btn_desativar , .btn_ativar').click(function() {
        $('#overlay, #confirmPopup').show();
    });

    $('#confirmDesativar').click(function() {
        $('#desativarForm').submit();
    });

    $('#cancelDesativar, #overlay').click(function() {
        $('#overlay, #confirmPopup').hide();
    });
});




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