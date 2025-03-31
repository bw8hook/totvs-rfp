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
                        <div class="info_details"> Escopo da RFP:<span>  {{$KnowledgeBase->project}}</span></div>
                        <div class="info_details"> Time Responsável:<span> 
                            @foreach($UsersDepartaments as $Departament)
                                @if ($KnowledgeBase->project_team == $Departament->id)
                                    {{$Departament->departament}}
                                @endif
                            @endforeach
                            </span>
                        </div>
                        <div class="info_details"> Data da RFP:<span> @if($KnowledgeBase->rfp_date) {{ date('d/m/Y', strtotime($KnowledgeBase->rfp_date)) }} @endif</span></div>
                       
                    </div>
                </div>
            </div>

            <div id="BlocoLista">

                <div class="bloco_info_filter_records">
                    <div>
                        <h2>Edição e Envio de Requisitos</h2>
                        <h4>Verifique cada item enviado. Para facilitar a sua escolha de edição de requisitos, escolha os filtros abaixo através de palavras-chave, classificação e linha de produto. Ao finalizar a sua edição, conclua a operação com o botão <b>“Concluir e enviar” ou salve para continuar depois.</b></h4>
                    </div>
                        
                    <form id="filterForm">
                        @csrf    

                        <input type="hidden" id="record_id" name="record_id" value="{{$Record_id}}">
                        
                        <div class="inputField">
                            <label>Palavra Chave:</label>
                            <input type="text" id="keyWord" name="keyWord">
                        </div>

                        <div class="inputField" style="width: 300px;">
                            <label>Processo:</label>
                            <select name="processo">
                                <option value="null" selected>Selecione</option>
                                @foreach($ListClassificacao as $Classificacao)
                                    <option value="{{$Classificacao}}">{{$Classificacao}}</option>
                                @endforeach
                            </select>
                        </div>
                        

                        <div class="inputField" style="width: 300px;">
                            <label>Resposta:</label>
                            <select name="resposta">
                                <option value="null" selected>Selecione</option>
                                @foreach($ListResposta as $Resposta)
                                    @if ($Resposta)
                                        <option value="{{$Resposta}}">{{$Resposta}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="inputField">
                            <label>Selecione o Produto:</label>
                            <select name="product">
                                <option value="null" selected>Selecione</opt>
                                    @foreach($ListProdutosRecebidas as $Produtos)
                                        <option value="{{$Produtos}}">{{$Produtos}}</option>
                                    @endforeach
                            </select>
                        </div>

                        <button type="submit">FILTRAR</button>
                        <button id="btnLimpar" style=" border: 2px solid #CBD0DD; background: #FFF; color: #5E6470;" type="button">LIMPAR</button>
                    </form> 

                    <span style="font-size: 13px; color: #818181;">*Os filtros são combinados e o campo de palavra-chave busca em todos os campos.</span>
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

                <!-- <div class="btns_bottom">
                    <div class="AlignBtns">
                        <div class="btn_finishSend" data-id="{{$KnowledgeBase->id}}" data-href="{{ route('knowledge.recordsErrors', $KnowledgeBase->id) }}">
                            <div class="alignCenter">
                                <span>Concluir e enviar</span>
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14.5 17.8337L20.5781 12.7685C21.0579 12.3688 21.0579 11.6319 20.5781 11.2321L14.5 6.16699" stroke="white" stroke-width="2.08333" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M2 12L14 12" stroke="white" stroke-width="2.08333" stroke-linecap="round"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div> -->

    
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    const ListProdutos = @json($AllBundles);
    const ListRespostas = @json($AllAnswers);
    const ListProcessos = @json($AllProcess);
    const Record_id = @json($Record_id);
</script>

<script>
    $(document).ready(function () {
        $(".side_menu_big").addClass("menu_hidden").removeClass("menu_visible");
        $(".side_menu_small").addClass("menu_visible").removeClass("menu_hidden");

        function fetchUsers(url = "{{ route('knowledge.recordsFilter', $idKnowledgeBase) }}", append = false) {
            $.ajax({
                url: url,
                method: 'GET',
                data: $('#filterForm').serialize(),
                success: function (data) {
                    // Atualizar tabela
                    let response = data.response;
                    let rows = '';
                    response.data.forEach(record => {

                        // Verifica se record.bundle_id está presente em ListProdutos
                           let existsInListProducts = ListProdutos.some(produto => 
                            produto.bundle_id === record.bundles?.principais?.[0]?.id
                        );
                        
                        // Se o bundle_id não existir na lista, ele aparece como desabilitado e selecionado
                        let bundleOptions = !existsInListProducts ? `<option disabled selected>${record.bundle_old}</option>` : '';
                        ListProdutos.forEach(produto => {
                            bundleOptions += `<option value="${produto.bundle_id}" ${produto.bundle_id === record.bundles?.principais?.[0]?.id ? 'selected' : ''}>${produto.bundle}</option>`;
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
                             <tr class="listaTabela ${existsInListProducts ? '' : 'highlighted_error'} ${record.id_record == Record_id ? 'highlighted_record' : ''}"  data-id="${record.id_record}" style="min-height:60px; max-height: 100%;">
                                <td style="width:3%; display: flex; align-items: center;">#${record.spreadsheet_line}</td>
                                <td style="width:11%; text-align:left; display: flex; align-items: center; word-wrap: break-word; white-space: normal;">${record.processo ? record.processo : '-'}</td>
                                <td style="width:11%; text-align:left; display: flex; align-items: center; word-wrap: break-word; white-space: normal;">${record.subprocesso ? record.subprocesso : '-'}</td>
                                <td style="width:20%; display: flex; align-items: center; word-wrap: break-word; white-space:normal; overflow:visible; text-align: left; margin-right: 10px;"> ${record.requisito ? record.requisito : '-'}</td>
                                <td style="width:10%; display: flex; align-items: center;">
                                    <select name="resposta" class="${existsInList ? '' : 'highlighted_error_select'}""  style="border-radius: 8px; width:100%" disabled>
                                        ${AnwserOptions}
                                    </select>
                                </td>
                                <td style="width:11%;  display: flex; align-items: center;  word-wrap: break-word; white-space: normal;overflow: visible; text-align: left;">${record.modulo ? record.modulo : '-'}</td>
                                <td style="width:13%;  display: flex; align-items: center;">
                                     <select name="bundle" style="border-radius: 8px; width:100%" disabled>
                                        ${bundleOptions}
                                    </select>
                                </td>

                                <td style="width:18%; display: flex; align-items: center; word-wrap: break-word; white-space:normal; overflow:visible; text-align: left; margin-right: 10px; font-size:14px;">${record.observacao ? record.observacao : '-'}</td>

                                <td style="width:5%;  display: flex; align-items: center;">
                                    
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

                    if(Record_id){
                        scrollToHighlightedRecord();
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

        

        $(document).on('click', '#loadMore', function () {
            const nextPage = $(this).data('next-page');
            if (nextPage) {
                fetchUsers(nextPage, true); // Passa "true" para adicionar itens ao invés de substituir
            }
        });


        $(document).on('change', 'select[name="resposta"]', function () {
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
                        resposta: $(this).val(), // Valor selecionado no select
                        _token: $('meta[name="csrf-token"]').attr('content') // Se necessário para Laravel
                    },
                    success: function(response) {
                        Record.removeClass("highlighted_error_select");

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

                        Record.removeClass("highlighted_error_select");

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
            console.log(url);
            if (url) {
                let escopo = $("#escopo");
                let time = $("#time");
                let data = $("#data");
                let isValid = true;
                let firstError = null;
                let alertContainer = $("#alert-container");

                $(".border-red").removeClass("border-red"); // Remove bordas vermelhas anteriores

                if (!escopo.val() || escopo.val().trim() === "") {
                    escopo.addClass("border-red");
                    showAlertBootstrap("error", "o campo 'ESCOPO' é um requisito obrigatório, preencha para continuar.");
                    if (!firstError) firstError = escopo;
                    isValid = false;
                    console.log('escopo');
                }

                if (!time.val() || time.val().trim() === "") {
                    time.addClass("border-red");
                    showAlertBootstrap("error", "o campo 'TIME' é um requisito obrigatório, preencha para continuar.");
                    if (!firstError) firstError = time;
                    isValid = false;
                }

                if (!data.val() || data.val().trim() === "") {
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
                                window.location.href = url;
                            }
                        },
                        error: function(error) {
                            console.error('Erro ao atualizar:', error);
                        }
                    });
                }
            }
        });


        $('#btnLimpar').on('click', function (e) {
            document.getElementById('filterForm').reset();

            fetchUsers();
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

    document.addEventListener("DOMContentLoaded", function() {
        flatpickr("#data", {
            dateFormat: "d/m/Y", // Formato da data (dia/mês/ano)
            enableTime: false,    // Desativa a seleção de horário
            locale: "pt"         // Define para português
        });
    });



    function scrollToHighlightedRecord() {
        // Espera um curto período para garantir que o DOM foi atualizado
        setTimeout(() => {
            const highlightedElement = document.querySelector('.highlighted_record');
            if (highlightedElement) {
                highlightedElement.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center'
                });
            }
        }, 100);
    }



</script>

