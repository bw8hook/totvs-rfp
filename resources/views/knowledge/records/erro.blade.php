<x-app-layout>
    <div class="" style=" padding-bottom: 130px;">
        <div class="max-w-full mx-auto">

            <div id="titleComponent_KnowledgeBase" style=" padding-top: 20px; min-height: 100px; height: auto; justify-content: space-between; align-items: flex-start;" class="text-lg font-bold flex items-center justify-between w-full px-4 space-x-2 relative" >  
                <div class="block" style="width: 50%;">    
                    <div class="flex" style="width: 100%;">
                    <img src="{{ asset('icons/base_conhecimento.svg') }}" alt="Upload Icon" style="height: 33%; padding-right: 18px;">
                        <span>{{$KnowledgeBase->name}}</span>
                    </div>
                    <div class="relative block items-center" style="padding-bottom: 12px; padding-left:7px;">        
                        <div class="info_details" style="color:#3A57E8"> {{$KnowledgeBase->filename_original}} </div>
                        <div class="info_details"> Requisitos:<span> {{$CountCountRecordsResultado}}</span></div>
                    </div>
                </div>

                <div class="flex" style="width: 40%;">    
                    <form id="InfosKnoledgeBase">
                        @csrf    
                        <div class="form-group" style="width:100%;">
                            <label for="escopo">*Escopo da RFP</label>
                            <input type="text" id="escopo" name="project" value="{{$KnowledgeBase->project}}" placeholder="Digite o escopo aqui" onblur="handleInput('escopo')">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="24" height="24" rx="4" fill="white"/><path d="M21.0885 3.83905C20.5504 3.30177 19.821 3 19.0606 3C18.3002 3 17.5709 3.30177 17.0328 3.83905L4.15958 16.7122C3.79093 17.0788 3.49864 17.5148 3.2996 17.9951C3.10056 18.4754 2.99874 18.9904 3.00001 19.5102V21.1352C3.00001 21.3451 3.0834 21.5465 3.23184 21.6949C3.38028 21.8433 3.5816 21.9267 3.79153 21.9267H5.4165C5.93634 21.9282 6.4513 21.8265 6.93158 21.6276C7.41186 21.4287 7.84792 21.1365 8.2145 20.7679L21.0885 7.89398C21.6255 7.3559 21.9271 6.62674 21.9271 5.86652C21.9271 5.10629 21.6255 4.37713 21.0885 3.83905ZM7.0953 19.6487C6.64889 20.0922 6.04573 20.3419 5.4165 20.3437H4.58304V19.5102C4.58224 19.1983 4.64332 18.8893 4.76274 18.6011C4.88217 18.313 5.05756 18.0514 5.27878 17.8314L15.0484 8.06178L16.8689 9.88226L7.0953 19.6487ZM19.9685 6.77478L17.9849 8.7591L16.1645 6.94258L18.1488 4.95825C18.2683 4.83898 18.4102 4.74442 18.5663 4.67997C18.7223 4.61551 18.8896 4.58244 19.0584 4.58262C19.2273 4.5828 19.3945 4.61625 19.5504 4.68104C19.7064 4.74583 19.848 4.8407 19.9673 4.96023C20.0866 5.07977 20.1811 5.22162 20.2456 5.3777C20.31 5.53378 20.3431 5.70103 20.3429 5.86989C20.3427 6.03876 20.3093 6.20593 20.2445 6.36187C20.1797 6.51781 20.0848 6.65946 19.9653 6.77874L19.9685 6.77478Z" fill="#8A94AD"/></svg>
                        </div>
                        <div class="form-group"  style="width:50%;">
                            <label for="time">*Time Responsável</label>
                            <select id="time" name="project_team" value="{{$KnowledgeBase->project_team}}" placeholder="Selecione o time responsável"  onblur="handleInput('time')" style="  width: 107%; border: none; font-size: 14px; font-weight: 100; color: #6f778c;">
                                <option value="null" selected disabled>Selecione</opt>
                                    @foreach($UsersDepartaments as $Departament)
                                        @if ($KnowledgeBase->project_team == $Departament->id)
                                            <option value="{{$Departament->id}}" selected>{{$Departament->departament}}</option>
                                        @else
                                            <option value="{{$Departament->id}}">{{$Departament->departament}}</option>
                                        @endif
                                    @endforeach
                            </select>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="24" height="24" rx="4" fill="white"/><path d="M21.0885 3.83905C20.5504 3.30177 19.821 3 19.0606 3C18.3002 3 17.5709 3.30177 17.0328 3.83905L4.15958 16.7122C3.79093 17.0788 3.49864 17.5148 3.2996 17.9951C3.10056 18.4754 2.99874 18.9904 3.00001 19.5102V21.1352C3.00001 21.3451 3.0834 21.5465 3.23184 21.6949C3.38028 21.8433 3.5816 21.9267 3.79153 21.9267H5.4165C5.93634 21.9282 6.4513 21.8265 6.93158 21.6276C7.41186 21.4287 7.84792 21.1365 8.2145 20.7679L21.0885 7.89398C21.6255 7.3559 21.9271 6.62674 21.9271 5.86652C21.9271 5.10629 21.6255 4.37713 21.0885 3.83905ZM7.0953 19.6487C6.64889 20.0922 6.04573 20.3419 5.4165 20.3437H4.58304V19.5102C4.58224 19.1983 4.64332 18.8893 4.76274 18.6011C4.88217 18.313 5.05756 18.0514 5.27878 17.8314L15.0484 8.06178L16.8689 9.88226L7.0953 19.6487ZM19.9685 6.77478L17.9849 8.7591L16.1645 6.94258L18.1488 4.95825C18.2683 4.83898 18.4102 4.74442 18.5663 4.67997C18.7223 4.61551 18.8896 4.58244 19.0584 4.58262C19.2273 4.5828 19.3945 4.61625 19.5504 4.68104C19.7064 4.74583 19.848 4.8407 19.9673 4.96023C20.0866 5.07977 20.1811 5.22162 20.2456 5.3777C20.31 5.53378 20.3431 5.70103 20.3429 5.86989C20.3427 6.03876 20.3093 6.20593 20.2445 6.36187C20.1797 6.51781 20.0848 6.65946 19.9653 6.77874L19.9685 6.77478Z" fill="#8A94AD"/></svg>
                        </div>
                        <div class="form-group" style="width:30%;">
                            <label for="data">*Data da RFP</label>
                            <input type="text" id="data" name="rfp_date" class="form-control" placeholder="Selecione uma data" value="@if($KnowledgeBase->rfp_date) {{ date('d/m/Y', strtotime($KnowledgeBase->rfp_date)) }} @endif" onblur="handleInput('data')">           
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="24" height="24" rx="4" fill="white"/><path d="M21.0885 3.83905C20.5504 3.30177 19.821 3 19.0606 3C18.3002 3 17.5709 3.30177 17.0328 3.83905L4.15958 16.7122C3.79093 17.0788 3.49864 17.5148 3.2996 17.9951C3.10056 18.4754 2.99874 18.9904 3.00001 19.5102V21.1352C3.00001 21.3451 3.0834 21.5465 3.23184 21.6949C3.38028 21.8433 3.5816 21.9267 3.79153 21.9267H5.4165C5.93634 21.9282 6.4513 21.8265 6.93158 21.6276C7.41186 21.4287 7.84792 21.1365 8.2145 20.7679L21.0885 7.89398C21.6255 7.3559 21.9271 6.62674 21.9271 5.86652C21.9271 5.10629 21.6255 4.37713 21.0885 3.83905ZM7.0953 19.6487C6.64889 20.0922 6.04573 20.3419 5.4165 20.3437H4.58304V19.5102C4.58224 19.1983 4.64332 18.8893 4.76274 18.6011C4.88217 18.313 5.05756 18.0514 5.27878 17.8314L15.0484 8.06178L16.8689 9.88226L7.0953 19.6487ZM19.9685 6.77478L17.9849 8.7591L16.1645 6.94258L18.1488 4.95825C18.2683 4.83898 18.4102 4.74442 18.5663 4.67997C18.7223 4.61551 18.8896 4.58244 19.0584 4.58262C19.2273 4.5828 19.3945 4.61625 19.5504 4.68104C19.7064 4.74583 19.848 4.8407 19.9673 4.96023C20.0866 5.07977 20.1811 5.22162 20.2456 5.3777C20.31 5.53378 20.3431 5.70103 20.3429 5.86989C20.3427 6.03876 20.3093 6.20593 20.2445 6.36187C20.1797 6.51781 20.0848 6.65946 19.9653 6.77874L19.9685 6.77478Z" fill="#8A94AD"/></svg>
                        </div>
                    </form>
                </div>
            </div>

            <div id="BlocoLista">

            <div class="bloco_info_filter_records" style="background:#D2E4FF;">
                    <div>
                        <h2 style="color:#3A57E8;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style=" float: left; margin-right: 9px;">
                                <g clip-path="url(#clip0_332_2687)">
                                    <path d="M24 15.8V22.2C24 22.642 23.642 23 23.2 23C22.758 23 22.4 22.642 22.4 22.2V21H21C19.895 21 19 20.105 19 19V15.8C19 15.358 19.358 15 19.8 15C20.242 15 20.6 15.358 20.6 15.8V19.2C20.6 19.311 20.69 19.4 20.8 19.4H22.401V15.799C22.401 15.357 22.759 14.999 23.201 14.999C23.643 14.999 24.001 15.357 24.001 15.799L24 15.8ZM11.2 15C10.758 15 10.4 15.358 10.4 15.8V19.401H8.799C8.688 19.401 8.599 19.311 8.599 19.201V15.801C8.599 15.359 8.241 15.001 7.799 15.001C7.357 15.001 6.999 15.359 6.999 15.801V19.001C6.999 20.106 7.894 21.001 8.999 21.001H10.399V22.201C10.399 22.643 10.757 23.001 11.199 23.001C11.641 23.001 11.999 22.643 11.999 22.201V15.801C11.999 15.359 11.641 15.001 11.199 15.001L11.2 15ZM18 17.5V20.5C18 21.878 16.878 23 15.5 23C14.122 23 13 21.878 13 20.5V17.5C13 16.122 14.122 15 15.5 15C16.878 15 18 16.122 18 17.5ZM16.4 17.5C16.4 17.004 15.996 16.6 15.5 16.6C15.004 16.6 14.6 17.004 14.6 17.5V20.5C14.6 20.996 15.004 21.4 15.5 21.4C15.996 21.4 16.4 20.996 16.4 20.5V17.5ZM4.5 4C3.672 4 3 4.672 3 5.5C3 6.328 3.672 7 4.5 7C5.328 7 6 6.328 6 5.5C6 4.672 5.328 4 4.5 4ZM8.5 4C7.672 4 7 4.672 7 5.5C7 6.328 7.672 7 8.5 7C9.328 7 10 6.328 10 5.5C10 4.672 9.328 4 8.5 4ZM24 6V12C24 12.552 23.552 13 23 13C22.448 13 22 12.552 22 12V10H2V18C2 19.654 3.346 21 5 21C5.552 21 6 21.448 6 22C6 22.552 5.552 23 5 23C2.243 23 0 20.757 0 18V6C0 3.243 2.243 1 5 1H19C21.757 1 24 3.243 24 6ZM22 8V6C22 4.346 20.654 3 19 3H5C3.346 3 2 4.346 2 6V8H22Z" fill="#3A57E8"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0_332_2687"><rect width="24" height="24" fill="white"/></clipPath>
                                </defs>
                            </svg>


                        Atenção
                        </h2>
                        <div style="display:flex; justify-content: space-between;">
                            <h4>Existem requisitos com valores inválidos em campos obrigatórios. Verifique os requisitos antes de enviá-los à base de dados.</h4>
                            <div style=" color: #5c6681; text-transform: uppercase; font-size: 13px;">Total Com Erros:<span> {{$CountCountRecordsEmpty}}</span></div>
                        </div>
                    </div>
                </div>
            
                <table id="TableExcel" class="tabela">
                    <thead>
                        <tr>
                            <th style="width:3%;"></th>
                            <th style="width:11%;">Processo</th>
                            <th style="width:11%;">Subprocesso</th>
                            <th style="width:19%;">Descrição do Requisito</th>
                            <th style="width:9.5%;">Resposta</th>
                            <th style="width:11.5%;">Módulo</th>
                            <th style="width:12.5%;">Produto</th>
                            <th style="width:19.5%;">Observações</th>
                            <th style="width:12%;"></th>
                        </tr>    
                    </thead>
                        <tbody class="body_table">
                          
                        </tbody>
                </table>

                <nav id="paginationLinks"></nav>

                <div id="loadMore" style="display: none;" data-next-page="">
                    <div class="alignCenter">
                        <span style="margin-right: 6px;">Carregar</span>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_443_3072)">
                                <path d="M10 0C8.02219 0 6.08879 0.58649 4.4443 1.6853C2.79981 2.78412 1.51809 4.3459 0.761209 6.17317C0.00433286 8.00043 -0.193701 10.0111 0.192152 11.9509C0.578004 13.8907 1.53041 15.6725 2.92894 17.0711C4.32746 18.4696 6.10929 19.422 8.0491 19.8079C9.98891 20.1937 11.9996 19.9957 13.8268 19.2388C15.6541 18.4819 17.2159 17.2002 18.3147 15.5557C19.4135 13.9112 20 11.9778 20 10C19.9971 7.34872 18.9426 4.80684 17.0679 2.9321C15.1932 1.05736 12.6513 0.00286757 10 0ZM10 18.3333C8.35183 18.3333 6.74066 17.8446 5.37025 16.9289C3.99984 16.0132 2.93174 14.7117 2.30101 13.189C1.67028 11.6663 1.50525 9.99076 1.82679 8.37425C2.14834 6.75774 2.94201 5.27288 4.10745 4.10744C5.27289 2.94201 6.75774 2.14833 8.37425 1.82679C9.99076 1.50525 11.6663 1.67027 13.189 2.301C14.7118 2.93173 16.0132 3.99984 16.9289 5.37025C17.8446 6.74066 18.3333 8.35182 18.3333 10C18.3309 12.2094 17.4522 14.3276 15.8899 15.8899C14.3276 17.4522 12.2094 18.3309 10 18.3333ZM14.1667 10C14.1667 10.221 14.0789 10.433 13.9226 10.5893C13.7663 10.7455 13.5544 10.8333 13.3333 10.8333H10.8333V13.3333C10.8333 13.5543 10.7455 13.7663 10.5893 13.9226C10.433 14.0789 10.221 14.1667 10 14.1667C9.77899 14.1667 9.56703 14.0789 9.41075 13.9226C9.25447 13.7663 9.16667 13.5543 9.16667 13.3333V10.8333H6.66667C6.44566 10.8333 6.2337 10.7455 6.07742 10.5893C5.92113 10.433 5.83334 10.221 5.83334 10C5.83334 9.77899 5.92113 9.56703 6.07742 9.41074C6.2337 9.25447 6.44566 9.16667 6.66667 9.16667H9.16667V6.66667C9.16667 6.44565 9.25447 6.23369 9.41075 6.07741C9.56703 5.92113 9.77899 5.83333 10 5.83333C10.221 5.83333 10.433 5.92113 10.5893 6.07741C10.7455 6.23369 10.8333 6.44565 10.8333 6.66667V9.16667H13.3333C13.5544 9.16667 13.7663 9.25447 13.9226 9.41074C14.0789 9.56703 14.1667 9.77899 14.1667 10Z" fill="#525B75"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_443_3072"><rect width="20" height="20" fill="white"/></clipPath>
                            </defs>
                        </svg>
                    </div>
                </div>

                <div class="btns_bottom">
                    <div class="AlignBtns">
                        <div class="btn_finishSend" data-id="{{$KnowledgeBase->id}}" data-href="{{ route('knowledge.recordsErrors', $KnowledgeBase->id) }}">
                            <div class="alignCenter">
                                <span>Concluir edições e enviar</span>
                                <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16.798 8.288L18.202 9.713L12.409 15.42C12.022 15.807 11.513 16 11.002 16C10.491 16 9.977 15.805 9.586 15.415L6.804 12.719L8.197 11.282L10.99 13.989L16.798 8.288ZM24.5 12C24.5 18.617 19.117 24 12.5 24C5.883 24 0.5 18.617 0.5 12C0.5 5.383 5.883 0 12.5 0C19.117 0 24.5 5.383 24.5 12ZM22.5 12C22.5 6.486 18.014 2 12.5 2C6.986 2 2.5 6.486 2.5 12C2.5 17.514 6.986 22 12.5 22C18.014 22 22.5 17.514 22.5 12Z" fill="white"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="ModalDelete">
                    <form method="delete" action="">
                        @csrf
                        <h2>Tem certeza que deseja excluir esse requisito?</h2>
                        <div class="btns_Delete">
                            <div class="BtnConfirmDelete">Sim, excluir agora</div>
                            <div class="btnCancelDelete">Não excluir</div>
                        </div>
                    </form>
                </div>
              

            </div>
        </div>
    </div>
</x-app-layout>

<script>
    const ListProdutos = @json($AllBundles);
    const ListRespostas = @json($AllAnswers);
    const ListProcessos = @json($AllProcess);
</script>

<script>
    $(document).ready(function () {
        $(".side_menu_big").addClass("menu_hidden").removeClass("menu_visible");
        $(".side_menu_small").addClass("menu_visible").removeClass("menu_hidden");

        function fetchUsers(url = "{{ route('knowledge.recordsFilterErrors', $idKnowledgeBase) }}", append = false) {
            $.ajax({
                url: url,
                method: 'GET',
                data: $('#filterForm').serialize(),
                success: function (response) {
                    // Atualizar tabela
                    let rows = '';
                    response.data.forEach(record => {
                        // Verifica se record.bundle_id está presente em ListProdutos
                        let existsInListProducts = ListProdutos.some(produto => produto.bundle_id === record.bundle_id);

                        // Se o bundle_id não existir na lista, ele aparece como desabilitado e selecionado
                        let bundleOptions = !existsInListProducts ? `<option disabled selected>${record.bundle_old}</option>` : '<option disabled selected> - </option>';
                        ListProdutos.forEach(produto => {
                            bundleOptions += `<option value="${produto.bundle_id}" ${produto.bundle_id === record.bundle_id ? 'selected' : ''}>${produto.bundle}</option>`;
                        });

                        // Verifica se record.resposta está presente em ListRespostas
                        let existsInList = ListRespostas.some(resposta => resposta.anwser === record.resposta);
                        let AnwserOptions = !existsInList 
                            ? `<option disabled selected>${record.resposta || '?'}</option>` 
                            : '<option disabled selected>?</option>';
                        console.log(ListRespostas);
                        ListRespostas.forEach(resposta => {
                            AnwserOptions += `<option value="${resposta.anwser}" ${resposta.anwser === record.resposta ? 'selected' : ''}>${resposta.anwser}</option>`;
                        });


                        // Verifica se record.resposta está presente em ListRespostas
                        let existsInListProcess = ListProcessos.some(processo => processo.process === record.processo);
                        let ProcessOptions = !existsInListProcess   ? `<option disabled selected>${record.processo}</option>` : '';
                       
                        ListProcessos.forEach(processo => {
                            ProcessOptions += `<option value="${processo.id}" ${processo.process === record.processo ? 'selected' : ''}>${processo.process}</option>`;
                        });

                        rows += `
                            <tr class="listaTabela ${record.rfp_bundles ? '' : 'highlighted_error'}" data-id="${record.id_record}" style="min-height:60px; max-height: 100%;">
                                <td style="width:3%; display: flex; align-items: center;">#${record.spreadsheet_line}</td>
                                <td style="width:11%; text-align:left; display: flex; align-items: center; word-wrap: break-word; white-space: normal;">
                                     <select name="processo" class="${existsInListProcess ? '' : 'highlighted_error_select'}" style="border-radius: 8px; width:100%">
                                        ${ProcessOptions}
                                    </select>
                                </td>
                                <td style="width:11%; text-align:left; display: flex; align-items: center; word-wrap: break-word; white-space: normal;">${record.subprocesso ? record.subprocesso : '-'}</td>
                                <td style="width:20%; display: flex; align-items: center; word-wrap: break-word; white-space:normal; overflow:visible; text-align: left; margin-right: 10px;"> ${record.requisito ? record.requisito : '-'}</td>
                                <td style="width:10%; display: flex; align-items: center;">
                                    <select name="resposta" class="${existsInList ? '' : 'highlighted_error_select'}""  style="border-radius: 8px; width:100%">
                                        ${AnwserOptions}
                                    </select>
                                </td>
                                <td style="width:11%;  display: flex; align-items: center;  word-wrap: break-word; white-space: normal;overflow: visible; text-align: left;">${record.resposta2 ? record.resposta2 : '-'}</td>
                                <td style="width:13%;  display: flex; align-items: center;">
                                     <select name="bundle" style="border-radius: 8px; width:100%">
                                        ${bundleOptions}
                                    </select>
                                </td>

                                <td style="width:18%; display: flex; align-items: center; word-wrap: break-word; white-space:normal; overflow:visible; text-align: left; margin-right: 10px; font-size:14px;">${record.observacao ? record.observacao : '-'}</td>

                                <td style="width:5%;  display: flex; align-items: center;">
                                    <div class="btnEditRecord" style="margin: 0px; float:left; cursor:pointer;">
                                        <button type="submit" class="records_edit">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M15.6775 8.33333C15.935 8.33333 16.1783 8.21417 16.3358 8.01083C16.4933 7.8075 16.5483 7.5425 16.485 7.29333C16.2258 6.27917 15.6975 5.3525 14.9575 4.6125L12.0533 1.70833C10.9517 0.606667 9.48667 0 7.92833 0H4.16583C1.86917 0 0 1.86917 0 4.16667V15.8333C0 18.1308 1.86917 20 4.16667 20H6.66667C7.12667 20 7.5 19.6267 7.5 19.1667C7.5 18.7067 7.12667 18.3333 6.66667 18.3333H4.16667C2.78833 18.3333 1.66667 17.2117 1.66667 15.8333V4.16667C1.66667 2.78833 2.78833 1.66667 4.16667 1.66667H7.92917C8.065 1.66667 8.2 1.67333 8.33333 1.68583V5.83333C8.33333 7.21167 9.455 8.33333 10.8333 8.33333H15.6775ZM10 5.83333V2.21583C10.3158 2.3975 10.61 2.6225 10.875 2.8875L13.7792 5.79167C14.0408 6.05333 14.265 6.34833 14.4483 6.66667H10.8333C10.3742 6.66667 10 6.2925 10 5.83333ZM19.2683 9.89917C18.3233 8.95417 16.6767 8.95417 15.7325 9.89917L10.1433 15.4883C9.51417 16.1175 9.16667 16.955 9.16667 17.8458V19.1675C9.16667 19.6275 9.54 20.0008 10 20.0008H11.3217C12.2125 20.0008 13.0492 19.6533 13.6783 19.0242L19.2675 13.435C19.74 12.9625 20 12.335 20 11.6667C20 10.9983 19.74 10.3708 19.2683 9.89917ZM18.0892 12.2558L12.4992 17.845C12.185 18.16 11.7667 18.3333 11.3208 18.3333H10.8325V17.845C10.8325 17.4 11.0058 16.9817 11.3208 16.6667L16.9108 11.0775C17.225 10.7625 17.7742 10.7625 18.0892 11.0775C18.2467 11.2342 18.3333 11.4433 18.3333 11.6667C18.3333 11.89 18.2467 12.0983 18.0892 12.2558Z" fill="#8A94AD"/>
                                                <clipPath id="clip0_329_10365"><rect width="20" height="20" fill="white"/> </clipPath>            
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="btnDeleteRecord" style="margin: 0px; float:left;">
                                        <button type="submit" class="records_delete">
                                            <svg width="14" height="16" viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M4.18476 0.553125L3.9619 1H0.990476C0.442619 1 0 1.44687 0 2C0 2.55312 0.442619 3 0.990476 3H12.8762C13.424 3 13.8667 2.55312 13.8667 2C13.8667 1.44687 13.424 1 12.8762 1H9.90476L9.6819 0.553125C9.51476 0.2125 9.17119 0 8.79667 0H5.07C4.69548 0 4.3519 0.2125 4.18476 0.553125ZM12.8762 4H0.990476L1.64667 14.5938C1.69619 15.3844 2.34619 16 3.12929 16H10.7374C11.5205 16 12.1705 15.3844 12.22 14.5938L12.8762 4Z" fill="#CCCED9"/>
                                                <clipPath id="clip0_2032_702"> <rect width="13.8667" height="16" fill="white"/></clipPath>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });

                    // Adiciona ou substitui os itens da tabela
                    if (append) {
                        $('#TableExcel .body_table').append(rows);
                    } else {
                        $('#TableExcel .body_table').html(rows);
                    }

    
                    // Atualizar links de paginação
                    let pagination = '';
                    if (response.links) {
                        console.log(response);
                        pagination = response.links
                             .filter(link => !["&laquo; Anterior", "Próximo &raquo;", "&laquo; Previous" , "Next &raquo;"].includes(link.label)) // Remove "Anterior" e "Próximo"
                            .map(link =>
                                `<a href="${link.url}" class="pagination-link ${link.active ? 'active' : ''}">${link.label}</a>`
                            ).join('');
                    }
                    $('#paginationLinks').html(pagination);
                }
            });
        }


         // Navegar na paginação
         $(document).on('click', '#paginationLinks a', function (e) {
            e.preventDefault();
            const url = $(this).attr('href');
            if (url) {
                fetchUsers(url);
                const div = document.getElementById("contentBody");
                div.scrollTop = 0; 
            }
        });


        $(document).on('change', 'select[name="resposta"]', function () {
            const IdRecord = $(this).closest('tr').data('id'); // Obtém o ID do registro da linha da tabela
            if (IdRecord) {
                let url = `{{ route('knowledge.records.update', ':id') }}`.replace(':id', IdRecord);
                console.log(url); // Verifica a URL gerada no console

                // Opcional: Enviar automaticamente a alteração via AJAX para salvar no banco
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        resposta: $(this).val(), // Valor selecionado no select
                        _token: $('meta[name="csrf-token"]').attr('content') // Se necessário para Laravel
                    },
                    success: function(response) {
                        // Verifica se já existe um alerta visível e fecha ele
                        if ($('#success-alert').length) {
                            $('#success-alert').remove();
                            clearTimeout(alertTimeout); // Limpa o timeout anterior
                        }

                        // Criando o alerta dinamicamente
                        $('body').append(`
                            <div id="success-alert" class="show bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md" role="alert" style="position: absolute; top: 10px; right: 10px; z-index:9;">
                                <div class="flex">
                                    <div class="py-1"><svg class="fill-current h-6 w-6 text-teal-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg></div>
                                    <div>
                                    <p class="font-bold">Salvamento Automático!</p>
                                    <p class="text-sm">Informações salvas com sucesso!</p>
                                    </div>
                                </div>
                            </div>
                        `);

                    // Define um novo temporizador para remover o alerta após 5 segundos
                        alertTimeout = setTimeout(function() {
                            $('#success-alert').remove();
                        }, 5000);

                    },
                    error: function(error) {
                        console.error('Erro ao atualizar:', error);
                    }
                });
            }
        });


        $(document).on('change', 'select[name="processo"]', function () {
            const Record = $(this);
            const IdRecord = $(this).closest('tr').data('id'); // Obtém o ID do registro da linha da tabela
            if (IdRecord) {
                let url = `{{ route('knowledge.records.update', ':id') }}`.replace(':id', IdRecord);
                console.log(url); // Verifica a URL gerada no console

                // Opcional: Enviar automaticamente a alteração via AJAX para salvar no banco
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        processo: $(this).val(), // Valor selecionado no select
                        _token: $('meta[name="csrf-token"]').attr('content') // Se necessário para Laravel
                    },
                    success: function(response) {

                        Record.parent().parent().removeClass("highlighted_error");

                        // Verifica se já existe um alerta visível e fecha ele
                        if ($('#success-alert').length) {
                            $('#success-alert').remove();
                            clearTimeout(alertTimeout); // Limpa o timeout anterior
                        }

                        // Criando o alerta dinamicamente
                        $('body').append(`
                            <div id="success-alert" class="show bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md" role="alert" style="position: absolute; top: 10px; right: 10px; z-index:9;">
                                <div class="flex">
                                    <div class="py-1"><svg class="fill-current h-6 w-6 text-teal-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg></div>
                                    <div>
                                    <p class="font-bold">Salvamento Automático!</p>
                                    <p class="text-sm">Informações salvas com sucesso!</p>
                                    </div>
                                </div>
                            </div>
                        `);

                    // Define um novo temporizador para remover o alerta após 5 segundos
                        alertTimeout = setTimeout(function() {
                            $('#success-alert').remove();
                        }, 5000);

                    },
                    error: function(error) {
                        // Verifica se já existe um alerta visível e fecha ele
                        if ($('#error-alert').length) {
                            $('#error-alert').remove();
                            clearTimeout(alertTimeout); // Limpa o timeout anterior
                        }
                        
                        // Criando o alerta dinamicamente
                        $('body').append(`
                            <div id="error-alert" class="bg-red-100 border-t-4 border-red-500 rounded-b text-red-900 px-4 py-3 shadow-md" role="alert" style="position: absolute; top: 10px; right: 10px; z-index:9;">
                                <div class="flex">
                                    <div class="py-1"><svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg></div>
                                    <div>
                                    <p class="fonet-bold">Erro</p>
                                    <p class="text-sm">Não foi possível atualizar essa informação nesse momento!</p>
                                    </div>
                                </div>
                            </div>
                        `);

                        // Aguardar 5 segundos antes de remover o alerta
                        setTimeout(function() {
                            $('#error-alert').remove();
                        }, 5000);


                        console.error('Erro ao atualizar:', error);
                    }
                });
            }
        });


        $(document).on('change', 'select[name="bundle"]', function () {
            const Record = $(this);
            const IdRecord = $(this).closest('tr').data('id'); // Obtém o ID do registro da linha da tabela
            if (IdRecord) {
                let url = `{{ route('knowledge.records.update', ':id') }}`.replace(':id', IdRecord);
                console.log(url); // Verifica a URL gerada no console

                // Opcional: Enviar automaticamente a alteração via AJAX para salvar no banco
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        bundle: $(this).val(), // Valor selecionado no select
                        _token: $('meta[name="csrf-token"]').attr('content') // Se necessário para Laravel
                    },
                    success: function(response) {

                        Record.parent().parent().removeClass("highlighted_error");

                        // Verifica se já existe um alerta visível e fecha ele
                        if ($('#success-alert').length) {
                            $('#success-alert').remove();
                            clearTimeout(alertTimeout); // Limpa o timeout anterior
                        }

                        // Criando o alerta dinamicamente
                        $('body').append(`
                            <div id="success-alert" class="show bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md" role="alert" style="position: absolute; top: 10px; right: 10px; z-index:9;">
                                <div class="flex">
                                    <div class="py-1"><svg class="fill-current h-6 w-6 text-teal-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg></div>
                                    <div>
                                    <p class="font-bold">Salvamento Automático!</p>
                                    <p class="text-sm">Informações salvas com sucesso!</p>
                                    </div>
                                </div>
                            </div>
                        `);

                    // Define um novo temporizador para remover o alerta após 5 segundos
                        alertTimeout = setTimeout(function() {
                            $('#success-alert').remove();
                        }, 5000);

                    },
                    error: function(error) {
                        // Verifica se já existe um alerta visível e fecha ele
                        if ($('#error-alert').length) {
                            $('#error-alert').remove();
                            clearTimeout(alertTimeout); // Limpa o timeout anterior
                        }
                        
                        // Criando o alerta dinamicamente
                        $('body').append(`
                            <div id="error-alert" class="bg-red-100 border-t-4 border-red-500 rounded-b text-red-900 px-4 py-3 shadow-md" role="alert" style="position: absolute; top: 10px; right: 10px; z-index:9;">
                                <div class="flex">
                                    <div class="py-1"><svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg></div>
                                    <div>
                                    <p class="fonet-bold">Erro</p>
                                    <p class="text-sm">Não foi possível atualizar essa informação nesse momento!</p>
                                    </div>
                                </div>
                            </div>
                        `);

                        // Aguardar 5 segundos antes de remover o alerta
                        setTimeout(function() {
                            $('#error-alert').remove();
                        }, 5000);


                        console.error('Erro ao atualizar:', error);
                    }
                });
            }
        });


        $(document).on('click', '.btnDeleteRecord', function () {
            const IdRecord = $(this).parent().parent().data('id');
            if (IdRecord) {
                let url = `{{ route('knowledge.recordsFilterRemove', ':id') }}`.replace(':id', IdRecord);
                $("#ModalDelete form").attr('action', url);
                $("#ModalDelete form").attr('data-id', IdRecord);
                $("#ModalDelete").show();
                console.log(url);// Passa "true" para adicionar itens ao invés de substituir
            }
        });

        // Fechar MODAL
        $(document).on('click', '#ModalDelete', function (event) { if (event.target === this) { $(this).hide(); }});
        $(document).on('click', '.btnCancelDelete', function (event) {$('#ModalDelete').hide(); });

        
        $(document).on('click', '.BtnConfirmDelete', function () {
            $.ajax({
                url: $('#ModalDelete form').attr('action'),
                method: 'DELETE',
                data: $('#ModalDelete form').serialize(),
                success: function (response) {
                    console.log(response);
                    if(response.status == "success"){
                            const idRecord = $('#ModalDelete form').attr('data-id');
                            console.log(idRecord);
                            $(`.listaTabela[data-id='${idRecord}']`).fadeOut(300, function() {
                            $(this).remove();

                            // Criando o alerta dinamicamente
                            $('body').append(`
                                <div id="error-alert" class="bg-red-100 border-t-4 border-red-500 rounded-b text-red-900 px-4 py-3 shadow-md" role="alert" style="position: absolute; top: 10px; right: 10px; z-index:9;">
                                    <div class="flex">
                                        <div class="py-1"><svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg></div>
                                        <div>
                                        <p class="fonet-bold">Removido</p>
                                        <p class="text-sm">${response.message}</p>
                                        </div>
                                    </div>
                                </div>
                            `);

                            // Aguardar 5 segundos antes de remover o alerta
                            setTimeout(function() {
                                $('#error-alert').remove();
                            }, 5000);
                        });
           
                        $("#ModalDelete").hide();
                    }
                }
            })
        });


        // BTN Concluir e Enviar
        $(document).on('click', '.btn_finishSend', function () {
            const url = $(this).data('href'); // Obtém o valor do atributo data-href
            const IdRecord = $(this).data('id');
            if (url) {
                let escopo = $("#escopo");
                let time = $("#time");
                let data = $("#data");
                let isValid = true;
                let firstError = null;
                let alertContainer = $("#alert-container");

                $(".border-red").removeClass("border-red"); // Remove bordas vermelhas anteriores

                if (escopo.val() && escopo.val().trim() === "") {
                    escopo.addClass("border-red");
                    showAlertBootstrap("error", "o campo 'ESCOPO' é um requisito obrigatório, preencha para continuar.");
                    if (!firstError) firstError = escopo;
                    isValid = false;
                }

                if (time.val() && time.val().trim() === "") {
                    time.addClass("border-red");
                    showAlertBootstrap("error", "o campo 'TIME' é um requisito obrigatório, preencha para continuar.");
                    if (!firstError) firstError = time;
                    isValid = false;
                }

                if (data.val() && data.val().trim() === "") {
                    data.addClass("border-red");
                    showAlertBootstrap("error", "o campo 'DATA' é um requisito obrigatório, preencha para continuar.");
                    if (!firstError) firstError = data;
                    isValid = false;
                }

                if (!isValid) {
                    event.preventDefault(); // Impede o envio do formulário
                    $("#contentBody").animate({
                        scrollTop: firstError.offset().top - 50 // Ajuste para posicionar melhor o campo na tela
                    }, 500);
                }else{
                    
                    let urlFiltro = `{{ route('knowledge.recordsFilterErrors', ':id') }}`.replace(':id', IdRecord);
            
                    // Envia ajax para validar se todos os campos estão preenchidos, caso contrario, direciona para a página de "error"
                    $.ajax({
                        url: urlFiltro,
                        method: 'GET',
                        success: function(response) {
                            console.log(response);
                            if (response.data.length === 0 && response.next_page_url === null) {
                                let urlSuccess = `{{ route('knowledge.records.processing', ':id') }}`.replace(':id', IdRecord);
                                window.location.href = urlSuccess;
                            } else {
                                showAlertBootstrap("error", "Para Concluir é necessário que todos os erros sejam corrigidos.");
                            }
                        },
                        error: function(error) {
                            console.error('Erro ao atualizar:', error);
                        }
                    });
                }
            }
        });




        // Submeter filtros
        $('#filterForm').on('submit', function (e) {
            e.preventDefault();
            fetchUsers();
        });

        // Carregar lista inicial
        fetchUsers();
    });


    function handleInput(fieldId) {
        const input = document.getElementById(fieldId);
        console.log(input.value);
        if(input.value != ""){
            saveToDatabase(fieldId, input.value);
        }
        
    }

    let alertTimeout;

    function saveToDatabase(field, value) {
      // Substitua esta URL pelo endpoint do seu servidor
      const url = "{{ route('knowledge.updateInfos', $idKnowledgeBase) }}"
        
      $.ajax({
        url: url,
        method: 'POST',
        data: $('#InfosKnoledgeBase').serialize(),
        success: function (response) {
          console.log(response);

            // Verifica se já existe um alerta visível e fecha ele
            if ($('#success-alert').length) {
                $('#success-alert').remove();
                clearTimeout(alertTimeout); // Limpa o timeout anterior
            }

            // Criando o alerta dinamicamente
            $('body').append(`
                <div id="success-alert" class="show bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md" role="alert" style="position: absolute; top: 10px; right: 10px; z-index:9;">
                    <div class="flex">
                        <div class="py-1"><svg class="fill-current h-6 w-6 text-teal-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg></div>
                        <div>
                        <p class="font-bold">Salvamento Automático!</p>
                        <p class="text-sm">Informações salvas com sucesso!</p>
                        </div>
                    </div>
                </div>
            `);

           // Define um novo temporizador para remover o alerta após 5 segundos
            alertTimeout = setTimeout(function() {
                $('#success-alert').remove();
            }, 5000);
        }
    })
    }
</script>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        flatpickr("#data", {
            dateFormat: "d/m/Y", // Formato da data (dia/mês/ano)
            enableTime: false,    // Desativa a seleção de horário
            locale: "pt"         // Define para português
        });
    });
</script>
