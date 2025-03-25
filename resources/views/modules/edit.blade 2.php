<x-app-layout>
    <div style=" padding-bottom: 130px;">
        <div class="max-w-7xl mx-auto space-y-6">
    

            <div class="list-form" style="padding-top:0px;">
                  
                <div id="titleComponentForm" class=" flex items-center justify-between px-4 space-x-2 relative">
                    <div class="flex items-center space-x-2" style="display: block; margin-left: 30px;">
                
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline; margin-top: 3px; width: 27px; margin-right: 1px;">
                            <rect x="3" y="3" width="7" height="7" style="color: #5570F1;"></rect>
                            <rect x="14" y="3" width="7" height="7" style="color: #5570F1;"></rect>
                            <rect x="14" y="14" width="7" height="7" style="color: #5570F1;"></rect>
                            <rect x="3" y="14" width="7" height="7" style="color: #5570F1;"></rect>
                        </svg>


                        <div style="display: inline-grid; width: 94%;">
                            <span style="color: #141824; font-size: 22px; font-weight: 600; line-height: 28.6px; text-align: left;">Editar Módulo</span>
                            <div style="color:#8A94AD; font-size: 16px; font-weight: 400; line-height: 20px; text-align: left; margin-top: 8px; ">Preencha o formulário abaixo para editar um Módulo já cadastrado. Certifique-se de fornecer todas as informações necessárias.</div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('modules.update', $module->id) }}" enctype="multipart/form-data" style=" margin: auto; width: 65%; min-width: 450px; background: #FFF; box-shadow: 0px 4px 28px rgba(0, 0, 0, 0.1); border-radius: 15px; padding: 50px 15%;">
                @csrf
                
                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Nome')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" value="" required autofocus autocomplete="name"  value="{{ old('name', $module->name) }}" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>               

  
               <!-- TIPO DE CONTA -->
               <div class="mt-4">
                    <x-input-label for="status" :value="__('Status')" />
                    <select name="status"  class="form-control">
                        <option value="ativo"  {{ $module->status === 'ativo' ? 'selected' : '' }} >Ativo</option>
                       <option value="inativo"  {{ $module->status === 'inativo' ? 'selected' : '' }} >Inativo</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>
          
                <div class="flex items-center justify-end mt-3">
                    <button type="submit" class=" inline-flex items-center rounded-md font-semibold text-xs text-white btn_enviar" style="height: 46px; display: inline;">
                        <span>Atualizar</span>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: inline;">
                            <path d="M13.5817 6.90667L14.7517 8.09417L9.92417 12.85C9.60167 13.1725 9.1775 13.3333 8.75167 13.3333C8.32583 13.3333 7.8975 13.1708 7.57167 12.8458L5.25333 10.5992L6.41417 9.40167L8.74167 11.6575L13.5817 6.90667ZM20 10C20 15.5142 15.5142 20 10 20C4.48583 20 0 15.5142 0 10C0 4.48583 4.48583 0 10 0C15.5142 0 20 4.48583 20 10ZM18.3333 10C18.3333 5.405 14.595 1.66667 10 1.66667C5.405 1.66667 1.66667 5.405 1.66667 10C1.66667 14.595 5.405 18.3333 10 18.3333C14.595 18.3333 18.3333 14.595 18.3333 10Z" fill="white"/>
                        </svg>

                    </button>
                </div>

                <a href="" class="btn_voltar">Voltar</a>

            </form>
        </div>


           
        </div>
    </div>
</x-app-layout>
