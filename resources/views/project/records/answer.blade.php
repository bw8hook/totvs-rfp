<x-app-layout>
    <div class="" style=" padding-bottom: 130px;">
        <div class="max-w-full mx-auto">

            <div id="titleComponent_KnowledgeBase" style=" padding-top: 20px; min-height: 100px; height: auto; justify-content: space-between; align-items: flex-start;" class="text-lg font-bold flex items-center justify-between w-full px-4 space-x-2 relative" >  
                <div class="block" style="width: 50%;">    
                    <div class="flex" style="width: 100%;">
                    <img src="{{ asset('icons/base_conhecimento.svg') }}" alt="Upload Icon" style="height: 33%; padding-right: 18px;">
                        <span>{{$Project->name}}</span>
                    </div>
                    <div class="relative block items-center" style="padding-bottom: 12px; padding-left:7px;">        
                        <div class="info_details" style="color:#3A57E8"> {{$ProjectFile->filename_original}} </div>
                        <div class="info_details"> Requisitos:<span> {{$CountCountRecordsResultado}}</span></div>
                        <div class="info_details"> Produto:<span> {{$ProjectFile->rfp_bundles->bundle}}</span></div>
                        <div class="info_details"> Responsável:<span> {{$Project->user->name}}</span></div>
                    </div>
                </div>
            </div>


            <div id="titleComponent_KnowledgeBase" style=" padding-top: 20px; min-height: 100px; height: auto; justify-content: space-around; align-items: flex-start;" class="text-lg font-bold flex items-center justify-between w-full px-4 space-x-2 relative" >  
                <div style="width: 480px; height: 300px; border: 1px solid #CCC; border-radius: 8px; margin-top: 23px; padding:20px;">
                    <div style="display: flex; align-items: center; width:100%;">
                        <div style="display: flex; align-items: center; width: 190px; height: 250px;">
                            <canvas id="requisitoChart" width="50" height="50"></canvas>
                        </div>
                        <div style="margin-left: 20px; color: #8A94AD;">
                            <div style="margin-bottom:10px; font-size: 15px;"><div style=" width: 13px; height: 13px; background: #D2E4FF; border-radius:20px; float:left; margin:14px 10px 14px 0px;"></div>Total de Requisitos <br><span style="color:#141824; font-size:20px;">{{$CountCountRecordsResultado}}</span></div>
                            <div style="margin-bottom:10px;font-size: 15px;"><div style=" width: 13px; height: 13px; background: #3A57E8; border-radius:20px; float:left; margin:14px 10px 14px 0px;"></div>Total de Respostas IA <br><span style="color:#141824; font-size:20px;">{{$CountAnswerIA}}</span></di>
                            <div style="font-size: 15px;"><div style=" width: 13px; height: 13px; background: #E5780C; border-radius:20px; float:left; margin:14px 10px 14px 0px;"></div>Respondidas por você <br><span style="color:#141824; font-size:20px;">{{$CountAnswerUser}}</span></div></div>
                        </div>
                    </div>
                </div>     
                
                <div style="width: 50%; height: 300px; border: 1px solid #CCC; border-radius: 8px; margin-top: 23px; padding:20px;">
    
                    <div style="display: flex; align-items: center; width:100%;">
                        <div style=" display: flex; justify-content: center; align-items: center; margin: 15px 20px;">
                            <div class="progress-container">
                                <div class="circular-progress" style="background: conic-gradient(#007bff 0% {{ $progress }}%, #e0e0e0 {{ $progress }}% 100%);">
                                    <div class="inner-circle">
                                        <span class="progress-value">{{ round($progress) }}%</span>
                                    </div>
                                </div>
                                <p class="progress-text">Perguntas sem resposta da IA</p>
                            </div>
                        </div>
                        <div style="margin-left: 20px; color: #525B75; font-size: 20px; font-weight: 100;">
                            <span style=" margin-bottom: 20px; width: 100%; display: block;">Do total de requisitos solicitados neste arquivo, {{$registrosSemResposta}} perguntas ficaram sem resposta. </span>

                            <span>Identifique os requisitos de acordo com a classificação, resposta, produto ou qualidade da resposta para facilitar o processo de análise.</span>
                        </div>
                    </div>
                </div>  


            </div>

            <div id="BlocoLista">

                <div class="bloco_info_filter_records">
                    <div>
                        <h2>Análise e Confirmação de Requisitos</h2>
                        <h4>Analise a resposta de cada requisito de acordo com a sua acuracidade e edite as respostas se necessário.</h4>
                    </div>
                        
                    <form id="filterForm">
                        @csrf    
                        <div class="inputField">
                            <label>Palavra Chave:</label>
                            <input type="text" id="keyWord" name="keyWord">
                        </div>

                        <div class="inputField" style="width: 300px;">
                            <label>Modulo:</label>
                            <select name="modulo">
                                <option value="null" selected>Selecione</option>
                                @foreach($ListClassificacao as $Classificacao)
                                    <option value="{{$Classificacao}}">{{$Classificacao}}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <button type="submit">FILTRAR</button>
                        <button id="btnLimpar" style=" border: 2px solid #CBD0DD; background: #FFF; color: #5E6470;" type="button">LIMPAR</button>
                    </form> 

                    <span style="font-size: 13px; color: #818181;">*Os filtros são combinados, e o campo de palavra-chave aplica-se aos campos (Classificação 1, Descrição, Resposta 1, Resposta 2, Produto/Linha e Observações).</span>
                </div>
            
                <table id="TableExcel" class="tabela">
                    <thead>
                        <tr>
                            <th style="width:7.4%;">Modulo</th>
                            <th style="width:21%;">Descrição do Requisito</th>
                            <th style="width:11%;">Resposta</th>
                            <th style="width:23%;">Observações</th>
                            <th style="width:16%;">Referências</th>
                            <th style="width:10%;">Acuracidade</th>
                            <th style="width:22%;">Produto</th>
                            <th style="width:5%;"></th>
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
                        <div class="btn_finishSend" data-id="{{$ProjectFile->id}}" data-href="{{ route('project.recordsErrors', $ProjectFile->id) }}">
                            <div class="alignCenter">
                                <span>Concluir edições</span>
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14.5 17.8337L20.5781 12.7685C21.0579 12.3688 21.0579 11.6319 20.5781 11.2321L14.5 6.16699" stroke="white" stroke-width="2.08333" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M2 12L14 12" stroke="white" stroke-width="2.08333" stroke-linecap="round"/>
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
    const ListAnswers = @json($AllAnswers);
</script>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    var totalRequisitos = {{$CountCountRecordsResultado}};
    var totalRespostasIA = {{$CountAnswerIA}};
    var respostasUsuario = {{$CountAnswerUser}};
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    var ctx = document.getElementById("requisitoChart").getContext("2d");

    new Chart(ctx, {
        type: "doughnut",
        data: {
            datasets: [
                {
                    data: [totalRespostasIA, (totalRequisitos- totalRespostasIA)], 
                    backgroundColor: ["#3A57E8", "#D2E4FF"], // Anel externo: azul claro e cinza
                    borderWidth: 3, 
                    hoverOffset: 0,
                    borderRadius: 5
                },
                {
                    data: [respostasUsuario, (totalRequisitos - respostasUsuario)], 
                    backgroundColor: [ "#D97706", "#D2E4FF"], // Anel interno: azul escuro, laranja, cinza
                    borderWidth: 3, 
                    borderRadius: 5,
                    hoverOffset: 0
                }
            ]
        },
        options: {
            cutout: "80%", // Controla a espessura dos anéis
            plugins: {
                legend: { display: false }, // Esconde legenda padrão
                tooltip: { enabled: false } // Oculta tooltip para manter o design limpo
            }
        }
    });
});
</script>



<script>
    $(document).ready(function () {
        $(".side_menu_big").addClass("menu_hidden").removeClass("menu_visible");
        $(".side_menu_small").addClass("menu_visible").removeClass("menu_hidden");

        function fetchUsers(url = "{{ route('project.recordsFilterAnswer', $idProjectFile) }}", append = false) {
            $.ajax({
                url: url,
                method: 'GET',
                data: $('#filterForm').serialize(),
                success: function (response) {
                    // Atualizar tabela
                    let rows = '';
                    response.data.forEach(record => {

                        console.log(record);


                        // Verifica se record.resposta está presente em ListRespostas
                        let existsInList = ListAnswers.some(resposta => resposta.anwser === record.answers.aderencia_na_mesma_linha);
                        let AnwserOptions = !existsInList  ? `<option disabled selected>${record.answers.aderencia_na_mesma_linha ? record.answers.aderencia_na_mesma_linha : ''} </option>` : '';
                        let highlighted_error = !existsInList  ? false : true;
                        console.log(existsInList);
                        ListAnswers.forEach(resposta => {
                            AnwserOptions += `<option value="${resposta.id}" ${resposta.anwser === record.answers.aderencia_na_mesma_linha ? 'selected' : ''}>${resposta.anwser}</option>`;
                        });
                        
                        rows += `
                            <tr class="listaTabela ${highlighted_error ? '' : 'highlighted_error'}" data-id="${record.id}" style="min-height:60px; max-height: 100%;">                                
                                <td style="width:15%; display: flex; align-items: center; word-wrap: break-word; white-space:normal; overflow:visible; text-align: left; margin-right: 10px;"> ${record.answers.modulo ? record.answers.modulo : ''} </td>
                                <td style="width:38%; display: flex; align-items: center; word-wrap: break-word; white-space:normal; overflow:visible; text-align: left; margin-right: 10px;"> ${record.requisito} </td>
                                <td style="width:20%; display: flex; align-items: center;">
                                    <select name="classificacao_id"  style="border-radius: 8px; width:100%">
                                      ${AnwserOptions}
                                    </select>
                                </td>
                                 <td style="width:42%; display: flex; align-items: center; word-wrap: break-word; white-space:normal; overflow:visible; text-align: left; margin-right: 10px;"> ${record.answers.resposta ? record.answers.resposta : ''} </td>
                                <td style="width:30%; font-size:12px; display: flex; align-items: center; word-wrap: break-word; white-space:normal; overflow:visible; text-align: left; margin-right: 10px;"> ${record.answers.referencia ? record.answers.referencia : ''} </td>
                                <td style="width:12%; display: flex; align-items: center; word-wrap: break-word; white-space:normal; overflow:visible; text-align: left; margin-right: 10px;"> <span style=" width: 80%; background: #D2E4FF; text-align: center; margin: auto; padding: 5px; border-radius: 8px; color: #0E2ECF;"> ${record.answers.acuracidade_porcentagem ? record.answers.acuracidade_porcentagem : ''} </span> </td>
                                <td style="width:20%; display: flex; align-items: center; word-wrap: break-word; white-space:normal; overflow:visible; text-align: left; margin-right: 10px;"> <span style=" width: 80%; background: #C7EBFF; text-align: center; margin: auto; padding: 5px; border-radius: 8px; color: #141824;"> ${record.rfp_bundles.bundle ? record.rfp_bundles.bundle : ''} </span></td>
                                <td style="width:5%;  display: flex; align-items: center;">
                                    <div class="btnEditRecord" style="margin: 0px; float:left; cursor:pointer;">
                                        <button type="submit" class="records_edit">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M15.6775 8.33333C15.935 8.33333 16.1783 8.21417 16.3358 8.01083C16.4933 7.8075 16.5483 7.5425 16.485 7.29333C16.2258 6.27917 15.6975 5.3525 14.9575 4.6125L12.0533 1.70833C10.9517 0.606667 9.48667 0 7.92833 0H4.16583C1.86917 0 0 1.86917 0 4.16667V15.8333C0 18.1308 1.86917 20 4.16667 20H6.66667C7.12667 20 7.5 19.6267 7.5 19.1667C7.5 18.7067 7.12667 18.3333 6.66667 18.3333H4.16667C2.78833 18.3333 1.66667 17.2117 1.66667 15.8333V4.16667C1.66667 2.78833 2.78833 1.66667 4.16667 1.66667H7.92917C8.065 1.66667 8.2 1.67333 8.33333 1.68583V5.83333C8.33333 7.21167 9.455 8.33333 10.8333 8.33333H15.6775ZM10 5.83333V2.21583C10.3158 2.3975 10.61 2.6225 10.875 2.8875L13.7792 5.79167C14.0408 6.05333 14.265 6.34833 14.4483 6.66667H10.8333C10.3742 6.66667 10 6.2925 10 5.83333ZM19.2683 9.89917C18.3233 8.95417 16.6767 8.95417 15.7325 9.89917L10.1433 15.4883C9.51417 16.1175 9.16667 16.955 9.16667 17.8458V19.1675C9.16667 19.6275 9.54 20.0008 10 20.0008H11.3217C12.2125 20.0008 13.0492 19.6533 13.6783 19.0242L19.2675 13.435C19.74 12.9625 20 12.335 20 11.6667C20 10.9983 19.74 10.3708 19.2683 9.89917ZM18.0892 12.2558L12.4992 17.845C12.185 18.16 11.7667 18.3333 11.3208 18.3333H10.8325V17.845C10.8325 17.4 11.0058 16.9817 11.3208 16.6667L16.9108 11.0775C17.225 10.7625 17.7742 10.7625 18.0892 11.0775C18.2467 11.2342 18.3333 11.4433 18.3333 11.6667C18.3333 11.89 18.2467 12.0983 18.0892 12.2558Z" fill="#8A94AD"/>
                                                <clipPath id="clip0_329_10365"><rect width="20" height="20" fill="white"/> </clipPath>            
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
                        pagination = response.links
                            .filter(link => !["&laquo; Anterior", "Próximo &raquo;"].includes(link.label)) // Remove "Anterior" e "Próximo"
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

        $(document).on('change', 'select[name="classificacao_id"]', function () {
            const Record = $(this);
            const IdRecord = $(this).closest('tr').data('id'); // Obtém o ID do registro da linha da tabela
            if (IdRecord) {
                let url = `{{ route('project.records.update', ':id') }}`.replace(':id', IdRecord);
                console.log(url); // Verifica a URL gerada no console

                // Opcional: Enviar automaticamente a alteração via AJAX para salvar no banco
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        classificacao_id: $(this).val(), // Valor selecionado no select
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
                let url = `{{ route('project.recordsFilterRemove', ':id') }}`.replace(':id', IdRecord);
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
                
                    let urlFiltro = `{{ route('project.recordsFilterErrors', ':id') }}`.replace(':id', IdRecord);
            
                    // Envia ajax para validar se todos os campos estão preenchidos, caso contrario, direciona para a página de "error"
                    $.ajax({
                        url: urlFiltro,
                        method: 'GET',
                        success: function(response) {
                            console.log(response);
                            if (response.data.length === 0 && response.next_page_url === null) {
                                let urlSuccess = `{{ route('project.records.processing', ':id') }}`.replace(':id', IdRecord);
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
      const url = "{{ route('project.updateInfos', $ProjectFile) }}"
        
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


    document.getElementById('btnLimpar').addEventListener('click', function () {
        document.getElementById('filterForm').reset();
    });



</script>

