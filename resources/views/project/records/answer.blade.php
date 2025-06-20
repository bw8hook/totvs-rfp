<x-app-layout>
    <div class="" style=" padding-bottom: 130px;">
        <div class="max-w-full mx-auto">

            <div id="titleComponent_KnowledgeBase" style=" padding-top: 20px; min-height: 100px; height: auto; justify-content: space-between; align-items: flex-start;" class="text-lg font-bold flex items-center justify-between w-full px-4 space-x-2 relative" >  
                <div class="block" style="width: 100%;">    
                    <div class="flex" style="width: 100%;">
                    <img src="{{ asset('icons/base_conhecimento.svg') }}" alt="Upload Icon" style="height: 33%; padding-right: 18px;">
                        <span>{{$Project->name}}</span>
                    </div>
                    <div class="relative block items-center" style="padding-bottom: 12px; padding-left:7px;">        
                        <div class="info_details" style="color:#3A57E8"> {{$ProjectFile->filename_original}} </div>
                        <div class="info_details"> Requisitos:<span> {{$CountCountRecordsResultado}}</span></div>
                        <div class="info_details" style="width: 98%;"> Produto:<span>
                            @isset($ProjectFile->rfp_bundles->bundle)
                                {{$ProjectFile->rfp_bundles->bundle}}
                            @else
                                @foreach ($ProjectFile->bundles as $bundleProject)
                                    <span class="produto_answer" >{{$bundleProject->bundle}}</span>
                                @endforeach
                            @endisset
                        </span></div>

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
                            <label>Processo:</label>
                            <select name="process">
                                <option value="null" selected>Selecione</option>
                                @foreach($ListClassificacao as $Classificacao)
                                    <option value="{{$Classificacao}}">{{$Classificacao}}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="inputField" style="width: 300px;">
                            <label>Resposta:</label>
                            <select name="answer">
                                <option value="null" selected>Selecione</option>
                                @foreach($ListRespostaRecebidas as $ListRespostaRecebida)
                                    <option value="{{$ListRespostaRecebida}}">{{$ListRespostaRecebida}}</option>
                                @endforeach
                                <option value="Não Processado">Não Processado</option>
                            </select>
                        </div>
                        

                        <div class="inputField" style="width: 300px;">
                            <label>Produto:</label>
                            <select name="bundle">
                                <option value="null" selected>Selecione</option>
                                @foreach($ListProdutosRecebidos as $ListProdutoRecebido)
                                    <option value="{{$ListProdutoRecebido}}">{{$ListProdutoRecebido}}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="inputField" style="width: 300px;">
                            <label>Acuracidade:</label>
                            <div class="btn" id="acertividade-btn" data-bs-toggle="popover" data-trigger="focus" data-bs-placement="bottom" data-bs-html="true">
                                <span id="min-value">0%</span> - <span id="max-value">100%</span>
                            </div>
                            <input type="hidden" name="min_percent" id="min_percent" value="0">
                            <input type="hidden" name="max_percent" id="max_percent" value="100">
                        </div>





                        
                        <button type="submit">FILTRAR</button>
                        <button id="btnLimpar" style=" border: 2px solid #CBD0DD; background: #FFF; color: #5E6470;" type="button">LIMPAR</button>
                    </form> 

                    <span style="font-size: 13px; color: #818181;">*Os filtros são combinados, e o campo de palavra-chave aplica-se aos campos (Classificação 1, Descrição, Resposta 1, Resposta 2, Produto/Linha e Observações).</span>
                </div>
            
                <table id="TableExcel" class="tabela">
                    <thead>
                        <tr>
                            <th style="width:7.4%;">Processo</th>
                            <th style="width:8.4%;">Subprocesso</th>
                            <th style="width:20%;">Descrição do Requisito</th>
                            <th style="width:11%;">Resposta</th>
                            <th style="width:10%;">Módulo</th>
                            <th style="width:21%;">Observações</th>
                            <th style="width:8%;">Acuracidade</th>
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
                        <div class="btn_finishSend" data-id="{{$ProjectFile->id}}" data-href="{{ route('project.answer.errors', $ProjectFile->id) }}">
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

                <div id="ModalEdit">
                    <div class="content">

                        <div class="loading" style="background: #ffffffcf; position: relative; width: 100%; height: 100%; top: 0px; left: 0px;">
                            <div id="lottie-container" style="width: 100px; height:100px; position: absolute; top: 50%; left: 50%; transform: translate(-75px, -35px);"></div>
                        </div>


                        <div class="ListaHistorico">

                        </div>

                        <form id="UserEdit" method="post" action="" style="display:none;">
                            @csrf
                            <div class="Title">
                                <h2>Edite o conteúdo sugerido pela IA</h2>
                                <div class="btnHistorico"> Ver histórico </div>
                            </div>
                            
                            <div class="inputField">
                                <label>Resposta:</label>
                                <select id="resposta" name="resposta">
                                    <option value="null" disabled>Selecione</option>
                                    @foreach($AllAnswers as $Answer)
                                        <option value="{{$Answer->id}}">{{$Answer->anwser}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="inputField"> 
                                <label>Módulo:</label>
                                <input type="text" id="modulo" name="modulo">
                            </div>

                            <div class="inputField">
                                <label>Observações:</label>
                                <input type="text" id="observacao" name="observacao">
                            </div>

                            <div class="inputField">
                                <label>Linha/Produto:</label>
                                <select id="produto" name="produto">
                                    <option value="null" disabled>Selecione</option>
                                    @foreach($AllBundles as $bundle)
                                        <option value="{{$bundle->bundle_id}}">{{$bundle->bundle}}</option>
                                    @endforeach
                                </select>
                            </div>

                        <div class="BtnConfirmEdit">Concluir Edições</div>
                        </form>
                    </div>
                </div>

                <div id="ModalReferencia">
                    <div class="content">
                        <div class="loading" style="background: #ffffffcf; position: absolute; width: 100%; height: 100%; top: 0px; left: 0px;">
                            <div id="lottie-container2" style="width: 100px; height:100px; position: absolute; top: 50%; left: 50%; transform: translate(-50px, -50px);"></div>
                        </div>

                        <div class="Title">
                            <svg width="23" height="23" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.6931 7.46553H15.3069V10.0793H12.6931V7.46553ZM14 20.5345C14.7188 20.5345 15.3069 19.9464 15.3069 19.2276V14C15.3069 13.2812 14.7188 12.6931 14 12.6931C13.2812 12.6931 12.6931 13.2812 12.6931 14V19.2276C12.6931 19.9464 13.2812 20.5345 14 20.5345ZM14 0.93103C6.78594 0.93103 0.93103 6.78594 0.93103 14C0.93103 21.2141 6.78594 27.069 14 27.069C21.2141 27.069 27.069 21.2141 27.069 14C27.069 6.78594 21.2141 0.93103 14 0.93103ZM14 24.4552C8.2366 24.4552 3.54483 19.7635 3.54483 14C3.54483 8.2366 8.2366 3.54483 14 3.54483C19.7635 3.54483 24.4552 8.2366 24.4552 14C24.4552 19.7635 19.7635 24.4552 14 24.4552Z" fill="#0097EB"/>
                            </svg>
                            <h2>Referências selecionadas pela IA para respostas do requisito:</h2>
                        </div>
                        <div class="ListaReferencia">
                            <div class="listAll">

                            </div>
                        </div>
                    </div>
                </div>




                <div id="ModalRetry">
                    <div class="content">
                        <div class="loading" style="background: #ffffffcf; position: absolute; width: 100%; height: 100%; top: 0px; left: 0px;">
                            <div id="lottie-container3" style="width: 100px; height:100px; position: absolute; top: 50%; left: 50%; transform: translate(-50px, -50px);"></div>
                        </div>

                        <div class="Title">
                            <svg width="23" height="23" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.6931 7.46553H15.3069V10.0793H12.6931V7.46553ZM14 20.5345C14.7188 20.5345 15.3069 19.9464 15.3069 19.2276V14C15.3069 13.2812 14.7188 12.6931 14 12.6931C13.2812 12.6931 12.6931 13.2812 12.6931 14V19.2276C12.6931 19.9464 13.2812 20.5345 14 20.5345ZM14 0.93103C6.78594 0.93103 0.93103 6.78594 0.93103 14C0.93103 21.2141 6.78594 27.069 14 27.069C21.2141 27.069 27.069 21.2141 27.069 14C27.069 6.78594 21.2141 0.93103 14 0.93103ZM14 24.4552C8.2366 24.4552 3.54483 19.7635 3.54483 14C3.54483 8.2366 8.2366 3.54483 14 3.54483C19.7635 3.54483 24.4552 8.2366 24.4552 14C24.4552 19.7635 19.7635 24.4552 14 24.4552Z" fill="#0097EB"/>
                            </svg>
                            <h2>Referências selecionadas pela IA para respostas do requisito:</h2>
                        </div>
                        <div class="ListaRetry">
                            <div class="listAll">

                            </div>
                        </div>
                    </div>
                </div>





                <!-- Template para o conteúdo do Popover -->
                <div id="popover-content" style="display: none;">
                    <div id="slider" class="mb-3"></div>
                    <div class="d-flex justify-content-between">
                        <span id="slider-value-min" class="badge bg-secondary"></span>
                        <span id="slider-value-max" class="badge bg-secondary"></span>
                    </div>
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
    var UserType = {{$InfoUser}};
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

                        let highlighted_error = true;
                        let AnwserOptions = '';

                        // Verifica se record.resposta está presente em ListRespostas
                        if (record.answers && record.answers.aderencia_na_mesma_linha) {                       
                            let existsInList = ListAnswers.some(resposta => resposta.anwser === record.answers.aderencia_na_mesma_linha);
                            AnwserOptions = !existsInList  ? `<option disabled selected>${record.answers.aderencia_na_mesma_linha ? record.answers.aderencia_na_mesma_linha : ''} </option>` : '';
                            highlighted_error = !existsInList  ? false : true;
                            ListAnswers.forEach(resposta => {
                                AnwserOptions += `<option value="${resposta.id}" ${resposta.anwser === record.answers.aderencia_na_mesma_linha ? 'selected' : ''}>${resposta.anwser}</option>`;
                            });
                        }else{
                            highlighted_error = false;
                             // Adiciona as opções da lista
                            ListAnswers.forEach(resposta => {
                                AnwserOptions += `<option disabled selected > Não Processado </option>`;
                            });

                            console.log("highlighted_error");
                            console.log(highlighted_error);
                        }

                        let user_edit_record =  record.status == "user edit" ? false : true;


                        if (record.status === 'user edit') {
                            btnEdit = `<div class="btnEditRecord" style="margin: 0px; float:left; cursor:pointer;width:50%;">
                                        <button type="submit" class="records_edit_user">
                                             <svg width="23" height="23" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g clip-path="url(#clip0_1034_1300)">
                                                <path d="M22.813 10C23.122 10 23.414 9.857 23.603 9.613C23.792 9.369 23.858 9.051 23.782 8.752C23.471 7.535 22.837 6.423 21.949 5.535L18.464 2.05C17.142 0.728 15.384 0 13.514 0H8.999C6.243 0 4 2.243 4 5V16.5C4 19.257 5 22 8.999 23.5H10.5H12C12.5 23.5 13 23.552 13 23C13 22.448 12.552 22 12 22H9C7.346 22 6 20.654 6 19V5C6 3.346 7.346 2 9 2H13.515C13.678 2 13.84 2.008 14 2.023V7C14 8.654 15.346 10 17 10H22.813ZM16 7V2.659C16.379 2.877 16.732 3.147 17.05 3.465L20.535 6.95C20.849 7.264 21.118 7.618 21.338 8H17C16.449 8 16 7.551 16 7ZM27.122 11.879C25.988 10.745 24.012 10.745 22.879 11.879L16.172 18.586C15.417 19.341 15 20.346 15 21.415V23.001C15 23.553 15.448 24.001 16 24.001H17.586C18.655 24.001 19.659 23.584 20.414 22.829L27.121 16.122C27.688 15.555 28 14.802 28 14C28 13.198 27.688 12.445 27.122 11.879ZM25.707 14.707L18.999 21.414C18.622 21.792 18.12 22 17.585 22H16.999V21.414C16.999 20.88 17.207 20.378 17.585 20L24.293 13.293C24.67 12.915 25.329 12.915 25.707 13.293C25.896 13.481 26 13.732 26 14C26 14.268 25.896 14.518 25.707 14.707Z" fill="#8A94AD"/>
                                                <g clip-path="url(#clip1_1034_1300)">
                                                <path d="M14 21C14 24.8599 10.8599 28 7 28C3.14008 28 0 24.8599 0 21C0 17.1401 3.14008 14 7 14C10.8599 14 14 17.1401 14 21Z" fill="#2EE400"/>
                                                <path d="M10.4671 19.9222L9.64807 19.0909L6.26007 22.4165L4.63082 20.8374L3.81824 21.6757L5.44107 23.2484C5.66915 23.4759 5.96899 23.5896 6.26707 23.5896C6.56515 23.5896 6.86207 23.477 7.08782 23.2513L10.4671 19.9222Z" fill="white"/>
                                                </g>
                                                </g>
                                                <defs>
                                                <clipPath id="clip0_1034_1300">
                                                <rect width="28" height="28" fill="white"/>
                                                </clipPath>
                                                <clipPath id="clip1_1034_1300">
                                                <rect width="14" height="14" fill="white" transform="translate(0 14)"/>
                                                </clipPath>
                                                </defs>
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="btnInfoRecord" style="margin: 0px; float:left; cursor:pointer; width:50%;">
                                        <button type="submit" class="records_info">
                                            <svg width="23" height="23" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12.6931 7.46553H15.3069V10.0793H12.6931V7.46553ZM14 20.5345C14.7188 20.5345 15.3069 19.9464 15.3069 19.2276V14C15.3069 13.2812 14.7188 12.6931 14 12.6931C13.2812 12.6931 12.6931 13.2812 12.6931 14V19.2276C12.6931 19.9464 13.2812 20.5345 14 20.5345ZM14 0.93103C6.78594 0.93103 0.93103 6.78594 0.93103 14C0.93103 21.2141 6.78594 27.069 14 27.069C21.2141 27.069 27.069 21.2141 27.069 14C27.069 6.78594 21.2141 0.93103 14 0.93103ZM14 24.4552C8.2366 24.4552 3.54483 19.7635 3.54483 14C3.54483 8.2366 8.2366 3.54483 14 3.54483C19.7635 3.54483 24.4552 8.2366 24.4552 14C24.4552 19.7635 19.7635 24.4552 14 24.4552Z" fill="#0097EB"/>
                                            </svg>
                                        </button>
                                    </div>`;
                        } else {
                            btnEdit = `<div class="btnEditRecord" style="margin: 0px; float:left; cursor:pointer;width:50%;">
                                        <button type="submit" class="records_edit">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M15.6775 8.33333C15.935 8.33333 16.1783 8.21417 16.3358 8.01083C16.4933 7.8075 16.5483 7.5425 16.485 7.29333C16.2258 6.27917 15.6975 5.3525 14.9575 4.6125L12.0533 1.70833C10.9517 0.606667 9.48667 0 7.92833 0H4.16583C1.86917 0 0 1.86917 0 4.16667V15.8333C0 18.1308 1.86917 20 4.16667 20H6.66667C7.12667 20 7.5 19.6267 7.5 19.1667C7.5 18.7067 7.12667 18.3333 6.66667 18.3333H4.16667C2.78833 18.3333 1.66667 17.2117 1.66667 15.8333V4.16667C1.66667 2.78833 2.78833 1.66667 4.16667 1.66667H7.92917C8.065 1.66667 8.2 1.67333 8.33333 1.68583V5.83333C8.33333 7.21167 9.455 8.33333 10.8333 8.33333H15.6775ZM10 5.83333V2.21583C10.3158 2.3975 10.61 2.6225 10.875 2.8875L13.7792 5.79167C14.0408 6.05333 14.265 6.34833 14.4483 6.66667H10.8333C10.3742 6.66667 10 6.2925 10 5.83333ZM19.2683 9.89917C18.3233 8.95417 16.6767 8.95417 15.7325 9.89917L10.1433 15.4883C9.51417 16.1175 9.16667 16.955 9.16667 17.8458V19.1675C9.16667 19.6275 9.54 20.0008 10 20.0008H11.3217C12.2125 20.0008 13.0492 19.6533 13.6783 19.0242L19.2675 13.435C19.74 12.9625 20 12.335 20 11.6667C20 10.9983 19.74 10.3708 19.2683 9.89917ZM18.0892 12.2558L12.4992 17.845C12.185 18.16 11.7667 18.3333 11.3208 18.3333H10.8325V17.845C10.8325 17.4 11.0058 16.9817 11.3208 16.6667L16.9108 11.0775C17.225 10.7625 17.7742 10.7625 18.0892 11.0775C18.2467 11.2342 18.3333 11.4433 18.3333 11.6667C18.3333 11.89 18.2467 12.0983 18.0892 12.2558Z" fill="#8A94AD"/>
                                                <clipPath id="clip0_329_10365"><rect width="20" height="20" fill="white"/> </clipPath>            
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="btnInfoRecord" style="margin: 0px; float:left; cursor:pointer; width:50%;">
                                        <button type="submit" class="records_info">
                                            <svg width="23" height="23" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12.6931 7.46553H15.3069V10.0793H12.6931V7.46553ZM14 20.5345C14.7188 20.5345 15.3069 19.9464 15.3069 19.2276V14C15.3069 13.2812 14.7188 12.6931 14 12.6931C13.2812 12.6931 12.6931 13.2812 12.6931 14V19.2276C12.6931 19.9464 13.2812 20.5345 14 20.5345ZM14 0.93103C6.78594 0.93103 0.93103 6.78594 0.93103 14C0.93103 21.2141 6.78594 27.069 14 27.069C21.2141 27.069 27.069 21.2141 27.069 14C27.069 6.78594 21.2141 0.93103 14 0.93103ZM14 24.4552C8.2366 24.4552 3.54483 19.7635 3.54483 14C3.54483 8.2366 8.2366 3.54483 14 3.54483C19.7635 3.54483 24.4552 8.2366 24.4552 14C24.4552 19.7635 19.7635 24.4552 14 24.4552Z" fill="#0097EB"/>
                                            </svg>
                                        </button>
                                    </div>`;
                        }




                        if(UserType == 1){
                            btnRetry = `
                                    <div class="btnInfoRetry" style="margin: 0px; float:left; cursor:pointer; width:50%; margin-left: 10px; margin-top: 12px;">
                                        <button type="submit" class="records_info">                      
                                            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill="#000000" d="M14.9547098,7.98576084 L15.0711,7.99552 C15.6179,8.07328 15.9981,8.57957 15.9204,9.12636 C15.6826,10.7983 14.9218,12.3522 13.747,13.5654 C12.5721,14.7785 11.0435,15.5888 9.37999,15.8801 C7.7165,16.1714 6.00349,15.9288 4.48631,15.187 C3.77335,14.8385 3.12082,14.3881 2.5472,13.8537 L1.70711,14.6938 C1.07714,15.3238 3.55271368e-15,14.8776 3.55271368e-15,13.9867 L3.55271368e-15,9.99998 L3.98673,9.99998 C4.87763,9.99998 5.3238,11.0771 4.69383,11.7071 L3.9626,12.4383 C4.38006,12.8181 4.85153,13.1394 5.36475,13.3903 C6.50264,13.9466 7.78739,14.1285 9.03501,13.9101 C10.2826,13.6916 11.4291,13.0839 12.3102,12.174 C13.1914,11.2641 13.762,10.0988 13.9403,8.84476 C14.0181,8.29798 14.5244,7.91776 15.0711,7.99552 L14.9547098,7.98576084 Z M11.5137,0.812976 C12.2279,1.16215 12.8814,1.61349 13.4558,2.14905 L14.2929,1.31193 C14.9229,0.681961 16,1.12813 16,2.01904 L16,6.00001 L12.019,6.00001 C11.1281,6.00001 10.6819,4.92287 11.3119,4.29291 L12.0404,3.56441 C11.6222,3.18346 11.1497,2.86125 10.6353,2.60973 C9.49736,2.05342 8.21261,1.87146 6.96499,2.08994 C5.71737,2.30841 4.57089,2.91611 3.68976,3.82599 C2.80862,4.73586 2.23802,5.90125 2.05969,7.15524 C1.98193,7.70202 1.47564,8.08224 0.928858,8.00448 C0.382075,7.92672 0.00185585,7.42043 0.0796146,6.87364 C0.31739,5.20166 1.07818,3.64782 2.25303,2.43465 C3.42788,1.22148 4.95652,0.411217 6.62001,0.119916 C8.2835,-0.171384 9.99651,0.0712178 11.5137,0.812976 Z"/>
                                            </svg>
                                        </button>
                                    </div>`;
                        } else {
                            btnRetry = ``;
                        }



                        rows += `
                            <tr class="listaTabela ${highlighted_error ? '' : 'highlighted_error'} ${user_edit_record ? '' : 'user_edit_record'}" data-id="${record.id}" style="min-height:60px; max-height: 100%;">                                
                                <td style="width:15%; display: flex; align-items: center; word-wrap: break-word; white-space:normal; overflow:visible; text-align: left; margin-right: 10px;"> ${record.processo ? record.processo : ''} </td>
                                <td style="width:15%; display: flex; align-items: center; word-wrap: break-word; white-space:normal; overflow:visible; text-align: left; margin-right: 10px;"> ${record.subprocesso ? record.subprocesso : ''} </td>
                                <td style="width:38%; display: flex; align-items: center; word-wrap: break-word; white-space:normal; overflow:visible; text-align: left; margin-right: 10px;"> ${record.requisito} </td>
                                <td style="width:20%; display: flex; align-items: center;">
                                    <select name="classificacao_id"  style="border-radius: 8px; width:100%" disabled>
                                      ${AnwserOptions}
                                    </select>
                                </td>
                                <td style="width:20%; display: flex; align-items: center; word-wrap: break-word; white-space:normal; overflow:visible; text-align: left; margin-right: 10px;"> ${record.answers?.modulo ? record.answers.modulo : ''} </td>
                                <td style="width:42%; display: flex; align-items: center; word-wrap: break-word; white-space:normal; overflow:visible; text-align: left; margin-right: 10px;"> ${record.answers?.resposta || ''} 
${!record.answers?.resposta && record.answers?.aderencia_na_mesma_linha === "Desconhecido" 
  ? record.answers?.acuracidade_explicacao 
  : ""} </td>
                                <td style="width:12%; display: flex; align-items: center; word-wrap: break-word; white-space:normal; overflow:visible; text-align: left; margin-right: 10px;"> <span style=" width: 80%; background: #D2E4FF; text-align: center; margin: auto; padding: 5px; border-radius: 8px; color: #0E2ECF;"> ${record.answers?.acuracidade_porcentagem ? record.answers.acuracidade_porcentagem : '0%'} </span> </td>
                                <td style="width:20%; display: flex; align-items: center; word-wrap: break-word; white-space:normal; overflow:visible; text-align: left; margin-right: 10px;"> <span style=" width: 80%; background: #C7EBFF; text-align: center; margin: auto; padding: 5px; border-radius: 8px; color: #141824;"> ${record.answers?.linha_produto ? record.answers.linha_produto : ' Produto não encontrado'}   </span></td>
                                
                                ${UserType == 1 ? '<td style="width:13%;  display: flex; align-items: center;">' : '<td style="width:10%;  display: flex; align-items: center;">'}
                                    ${btnEdit}

                                    ${btnRetry}

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

        $(document).on('click', '.btnHistorico', function () {
            $('#ModalEdit form').hide();
            $('.ListaHistorico').show();
        })


        $(document).on('click', '.btnHistoricoBack', function () {
            $('.ListaHistorico').hide();
            $('#ModalEdit form').show();
        })


        $(document).on('click', '.btnEditRecord', function () {
            const IdRecord = $(this).parent().parent().data('id');
            $('#ModalEdit form').hide();
            $('.ListaHistorico').hide();
            $('#ModalEdit .loading').show();
            if (IdRecord) {
                let url = `{{ route('project.records.detail', ':id') }}`.replace(':id', IdRecord);
                let urlForm = `{{ route('project.records.history.update', ':id') }}`.replace(':id', IdRecord);
                $("#ModalEdit form").attr('action', urlForm);
                $("#ModalEdit form").attr('data-id', IdRecord);
                $("#ModalEdit").show();

                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function (response) {
                        console.log(response.ProjectAnswer);

                        $('#ModalEdit .loading').hide();
                        $('#ModalEdit form').show();

                        console.log(response.ProjectAnswer.aderencia_na_mesma_linha);

                        $('#resposta').val(response.ProjectAnswer.answer_id);
                        $('#modulo').val(response.ProjectAnswer.modulo);
                        $('#observacao').val(response.ProjectAnswer.observacao);
                        $('#produto').val(response.ProjectAnswer.bundle_id);



                        // Atualizar tabela
                        let rows = '';
                        response.ProjectHistory.forEach(record => {
                            console.log(record);
                            const data = new Date(record.created_at);
    
                            // Formatando HH:MM
                            const horas = String(data.getHours()).padStart(2, '0');
                            const minutos = String(data.getMinutes()).padStart(2, '0');
                            const horaFormatada = `${horas}:${minutos}`;
                            
                            // Formatando DD/MM/AAAA
                            const dia = String(data.getDate()).padStart(2, '0');
                            const mes = String(data.getMonth() + 1).padStart(2, '0'); // getMonth() retorna 0-11
                            const ano = data.getFullYear();
                            const dataFormatada = `${dia}/${mes}/${ano}`;


                            rows += `
                                    <div class="Referencia">
                                        <h3>Responsável: <span class="idrequisito">${record.user.name}</span></h3>
                                        <h4>Histórico de Edição:</h3>
                                        <h2>Data: <span class="idrequisito">${dataFormatada}</span></h2>
                                        <h2>Hora: <span class="idrequisito">${horaFormatada}</span></h2>
                                        <div class="list">
                                            <div class="processoList">
                                                <div class="labelProcesso">Resposta</div>
                                                <div class="textoProcesso">${record.new_answer} </div>
                                            </div>
                                            <div class="processoList">
                                                <div class="labelProcesso">Módulo</div>
                                                <div class="textoProcesso">${record.new_module} </div>
                                            </div>
                                            <div class="processoList">
                                                <div class="labelProcesso">Observações</div>
                                                <div class="textoProcesso">${record.new_observation}</div>
                                            </div>
                                            <div class="processoList">
                                                <div class="labelProcesso">Linha/Produto</div>
                                                <div class="textoProcesso">${record.new_bundle}</div>
                                            </div>
                                        </div>
                                    </div>`;
                        });

                        if(rows){
                            $('.btnHistorico').show();
                        }
                        // Adiciona ou substitui os itens da tabela
                        $('.ListaHistorico').html(rows);
                        
                    }
                })


                console.log(url);// Passa "true" para adicionar itens ao invés de substituir
            }
        });

        // Fechar MODAL
        $(document).on('click', '#ModalEdit', function (event) { if (event.target === this) { $(this).hide(); }});
        $(document).on('click', '.btnCancelDelete', function (event) {$('#ModalEdit').hide(); });

        function getBarColor(score) {
            if (score <= 20) return '#8B0000'; // vermelho escuro
            if (score <= 40) return '#FF0000'; // vermelho
            if (score <= 60) return '#FFA500'; // laranja
            if (score <= 80) return '#228B22'; // verde escuro
            return '#45bc4a'; // verde
        }        

        $(document).on('click', '.btnInfoRetry', function () {
            const IdRecord = $(this).parent().parent().data('id');
            const $Record = $(this).parent().parent();
            $('.ListaRetry .listAll').html('');
            $('#ModalRetry .loading').fadeIn();
            if (IdRecord) {
                let url = `{{ route('project.answer.reprocessing', ':id') }}`.replace(':id', IdRecord);
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function (response) {


                        let $tds = $Record.find('td');
                        let valorResposta;

                        if(response.aderencia_na_mesma_linha == 'Atende'){
                            valorResposta = 1;
                        }else if(response.aderencia_na_mesma_linha == 'Atende Parcial'){
                            valorResposta = 2;
                        }else if(response.aderencia_na_mesma_linha == 'Não Atende'){
                            valorResposta = 4;
                        }else if(response.aderencia_na_mesma_linha == 'Customizável'){
                            valorResposta = 4;
                        }

                        $tds.eq(3).find('select[name="classificacao_id"]').val(valorResposta); 
                        $tds.eq(4).text(response.modulo);
                        $tds.eq(5).text(response.resposta);
                        $tds.eq(6).find('span').text(response.acuracidade_porcentagem);
                        $tds.eq(7).find('span').text('Produto atualizado');
            

                        console.log(response);
                        console.log($Record);

                        $('#ModalRetry .loading').fadeOut();

                        // Atualizar tabela
                        let rows = '';
                        //response.ReferenciasBanco.forEach(record => {
                            let urlKnowledge = `{{ route('knowledge.records', ['id' => ':id', 'record_id' => ':record_id']) }}`.replace(':id', response.knowledge_base_id).replace(':record_id', response.id_record);

                            console.log(response);

                            rows += `
                                    <div class="Referencia">
                                        <h3 style="font-size: 12px; font-weight: bold; margin-bottom: 10px;">A similaridade entre o requisito e a referência foi de: ${response.acuracidade_porcentagem}% </h3>
                                        <div class="barra-container" style=" width: 100%; background-color: #e0e0e0; border-radius: 20px; margin-bottom:20px; overflow: hidden; height: 7px;">
                                            <div class="barra-preenchida" title="A similaridade entre o requisito e a referência foi de: ${response.acuracidade_porcentagem}%" style="height: 100%; width: ${response.acuracidade_porcentagem}%; background-color: ${getBarColor(response.acuracidade_porcentagem)}; text-align: center; color: white; line-height: 25px; transition: width 0.5s ease;"></div>
                                        </div>

                                        <div class="list">
                                            <div class="processoList">
                                                <div class="labelProcesso">Descrição do Requisito</div>
                                                <div class="textoProcesso">${response.resposta}</div>
                                            </div>
                                            <div class="processoList">
                                                <div class="labelProcesso">Resposta</div>
                                                <div class="textoProcesso">${response.aderencia_na_mesma_linha}</div>
                                            </div>
                                            <div class="processoList">
                                                <div class="labelProcesso">Módulo</div>
                                                <div class="textoProcesso">${response.modulo ? response.modulo : '-'}</div>
                                            </div>
                                            <div class="processoList">
                                                <div class="labelProcesso">Observações</div>
                                                <div class="textoProcesso">${response.observacao ? response.observacao : '-'}</div>
                                            </div>
                                            <div class="processoList">
                                                <div class="labelProcesso">Produto</div>
                                                <div class="textoProcesso">${response.linha_produto ? response.linha_produto : '-'} </div>
                                            </div> 

                                            <div class="processoList">
                                                <div class="labelProcesso">Acuracidade</div>
                                                <div class="textoProcesso">${response.acuracidade_porcentagem ? response.acuracidade_porcentagem+'%' : '-'} </div>
                                            </div> 

                                            <div class="processoList">
                                                <div class="labelProcesso">Explicação</div>
                                                <div class="textoProcesso">${response.acuracidade_explicacao ? response.acuracidade_explicacao : '-'} </div>
                                            </div>

                                            <div class="processoList">
                                                <div class="labelProcesso">Referência</div>
                                                <div class="textoProcesso">${response.referencia ? response.referencia : '-'} </div>
                                            </div> 

                                        </div>
                                    </div>`;
                        //});

                        // Adiciona ou substitui os itens da tabela
                        $('.ListaRetry .listAll').html(rows);
                        
                    }
                })

                $("#ModalRetry form").attr('action', url);
                $("#ModalDelete form").attr('data-id', IdRecord);
                $("#ModalRetry").show();
               
                console.log(url);// Passa "true" para adicionar itens ao invés de substituir

            }
            
        });






        $(document).on('click', '.btnInfoRecord', function () {
            const IdRecord = $(this).parent().parent().data('id');
            $('.ListaReferencia .listAll').html('');
            $('#ModalReferencia .loading').fadeIn();
            console.log('answer');
            if (IdRecord) {
                let url = `{{ route('project.records.references', ':id') }}`.replace(':id', IdRecord);
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function (response) {
                        console.log(response);

                        $('#ModalReferencia .loading').fadeOut();

                        // Atualizar tabela
                        let rows = '';
                        response.ReferenciasBanco.forEach(record => {
                            let urlKnowledge = `{{ route('knowledge.records', ['id' => ':id', 'record_id' => ':record_id']) }}`.replace(':id', record.knowledge_base_id).replace(':record_id', record.id_record);


                            console.log(record);
                            rows += `
                                    <div class="Referencia">

                                       ${record.coverage ? `<h3 style="font-size: 14px; margin-bottom: 20px;color: #86939d;background: #eaf0f5;padding: 10px;border-radius: 6px;"> <b>Explicação: </b>${record.coverage} </h3>` : ''}
                                        
                                        <div class="barra-container" style=" width: 100%; background-color: #e0e0e0; border-radius: 20px; margin-bottom:10px; overflow: hidden; height: 7px;">
                                            <div class="barra-preenchida" title="A similaridade entre o requisito e a referência foi de: ${record.score}%" style="height: 100%; width: ${record.score}%; background-color: ${getBarColor(record.score)}; text-align: center; color: white; line-height: 25px; transition: width 0.5s ease;"></div>
                                        </div>
                                         <h3 style="font-size: 12px; font-weight: bold; margin-bottom: 20px;">A similaridade entre o requisito e a referência foi de: ${record.score}% </h3>


                                        <h2><a href="${urlKnowledge}" target="_blank" class="idrequisito">${record.id_record} - VER REQUISITO DE REFERÊNCIA</a></h2>
                                        <div class="list">
                                            <div class="processoList">
                                                <div class="labelProcesso">Processo</div>
                                                <div class="textoProcesso">${record.processo} </div>
                                            </div>
                                            <div class="processoList">
                                                <div class="labelProcesso">Subprocesso</div>
                                                 <div class="textoProcesso">${record.subprocesso ? record.subprocesso : '-'}</div>
                                            </div>
                                            <div class="processoList">
                                                <div class="labelProcesso">Descrição do Requisito</div>
                                                <div class="textoProcesso">${record.requisito}</div>
                                            </div>
                                            <div class="processoList">
                                                <div class="labelProcesso">Resposta</div>
                                                <div class="textoProcesso">${record.resposta}</div>
                                            </div>
                                            <div class="processoList">
                                                <div class="labelProcesso">Módulo</div>
                                                <div class="textoProcesso">${record.modulo ? record.modulo : '-'}</div>
                                            </div>
                                            <div class="processoList">
                                                <div class="labelProcesso">Observações</div>
                                                <div class="textoProcesso">${record.observacao ? record.observacao : '-'}</div>
                                            </div>
                                            <div class="processoList">
                                                <div class="labelProcesso">Produto</div>
                                                <div class="textoProcesso">${record.bundles[0].bundle}</div>
                                            </div> 
                                        </div>
                                    </div>`;
                        });

                        // Adiciona ou substitui os itens da tabela
                        $('.ListaReferencia .listAll').html(rows);
                        
                    }
                })

                $("#ModalReferencia form").attr('action', url);
                $("#ModalDelete form").attr('data-id', IdRecord);
                $("#ModalReferencia").show();
               
                console.log(url);// Passa "true" para adicionar itens ao invés de substituir

            }
            
        });

        $(document).on('click', '#ModalRetry', function (event) { if (event.target === this) { $(this).hide(); }});
        $(document).on('click', '.btnCancelRetry', function (event) {$('#ModalRetry').hide(); });



        $(document).on('click', '#ModalReferencia', function (event) { if (event.target === this) { $(this).hide(); }});
        $(document).on('click', '.btnCancelReferencia', function (event) {$('#ModalReferencia').hide(); });
    
        
        $(document).on('click', '.BtnConfirmEdit', function () {
            $.ajax({
                url: $('#ModalEdit form').attr('action'),
                method: 'POST',
                data: $('#ModalEdit form').serialize(),
                success: function (response) {
                    showAlertBootstrap("success", "Alterado com Sucesso.");
                    console.log(response);
                    $('#ModalEdit').fadeOut();
                    
                }
            })
        });


        // BTN Concluir e Enviar
        $(document).on('click', '.btn_finishSend', function () {
            const url = $(this).data('href'); // Obtém o valor do atributo data-href
            const IdRecord = $(this).data('id');
            console.log(url);
            if (url) {
                let isValid = true;
                let firstError = null;
                let alertContainer = $("#alert-container");
                $(".border-red").removeClass("border-red"); // Remove bordas vermelhas anteriores
                    let urlFiltro = `{{ route('project.answer.filter.errors', ':id') }}`.replace(':id', IdRecord);
                    // Envia ajax para validar se todos os campos estão preenchidos, caso contrario, direciona para a página de "error"
                    $.ajax({
                        url: urlFiltro,
                        method: 'GET',
                        success: function(response) {
                            console.log(response);
                            if (response.data.length === 0 && response.next_page_url === null) {
                                let urlSuccess = `{{ route('project.answer.processing', ':id') }}`.replace(':id', IdRecord);
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


<script>
document.addEventListener('DOMContentLoaded', function() {
    
    var minValue = document.getElementById('min-value');
    var maxValue = document.getElementById('max-value');
    var minPercentInput = document.getElementById('min_percent');
    var maxPercentInput = document.getElementById('max_percent');
    var acertividadeBtn = document.getElementById('acertividade-btn');

    var popover = new bootstrap.Popover(acertividadeBtn, {
        container: 'body',
        content: document.getElementById('popover-content').innerHTML,
        html: true,
        sanitize: false,
        trigger: 'manual' // Mudamos para 'manual' para controlar a exibição/ocultação
    });

    // Função para mostrar o popover
    function showPopover() {
        popover.show();
        setTimeout(() => {
            initializeSlider();
            document.addEventListener('click', closePopoverOutside);
        }, 0);
    }

    // Função para fechar o popover
    function closePopover() {
        popover.hide();
        document.removeEventListener('click', closePopoverOutside);
    }

    // Função para fechar o popover quando clicar fora
    function closePopoverOutside(event) {
        var popoverElement = document.querySelector('.popover');
        if (popoverElement && !popoverElement.contains(event.target) && event.target !== acertividadeBtn) {
            closePopover();
        }
    }

    // Mostrar popover ao clicar no botão
    acertividadeBtn.addEventListener('click', function(event) {
        event.stopPropagation();
        if (document.querySelector('.popover')) {
            closePopover();
        } else {
            showPopover();
        }
    });

    function initializeSlider() {
        var popoverBody = document.querySelector('.popover-body');
        var slider = popoverBody.querySelector('#slider');
        
        if (slider.noUiSlider) {
            slider.noUiSlider.destroy();
        }

        noUiSlider.create(slider, {
            start: [Number(minPercentInput.value), Number(maxPercentInput.value)],
            connect: true,
            step: 1,
            range: {
                'min': 0,
                'max': 100
            },
            format: {
                to: function (value) {
                    return Math.round(value) + '%';
                },
                from: function (value) {
                    return Number(value.replace('%', ''));
                }
            }
        });

        var sliderValueMin = popoverBody.querySelector('#slider-value-min');
        var sliderValueMax = popoverBody.querySelector('#slider-value-max');

        slider.noUiSlider.on('update', function(values, handle) {
            var value = values[handle];
            if (handle) {
                sliderValueMax.innerHTML = value;
                maxPercentInput.value = value.replace('%', '');
                maxValue.innerHTML = value;
            } else {
                sliderValueMin.innerHTML = value;
                minPercentInput.value = value.replace('%', '');
                minValue.innerHTML = value;
            }
        });
    }

});
</script>


