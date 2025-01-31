<x-app-layout>
    <div class="flex flex-col">
        <div class="py-4" style=" padding-bottom: 130px;">

            <div id="titleComponent_KnowledgeBase" class="text-lg font-bold flex items-center justify-between w-full px-4 space-x-2 relative">
                <div class="AlignTitleLeft" style="width: 80%;">
                    <div class="flex" style="width: 100%;">
                        <img src="{{ asset('icons/base_conhecimento.svg') }}" alt="Upload Icon" style="height: 33%; padding-right: 18px;">
                        <span>Base de Conhecimento</span>
                    </div>
                    <div class="relative block items-center" style=" padding-bottom: 12px; margin-left: 8px; width: 90%;">        
                        <div class="info_details" style="color:#8A94AD;"> Os arquivos exibidos nesta seção foram enviados por você e por outros analistas, e servirão como base de conhecimento. Eles serão utilizados para que a inteligência artificial possa responder de forma mais precisa às novas RFPs recebidas. Sua contribuição ajudará a melhorar as respostas fornecidas.</div>
                    </div>
                </div>
               
                <a href="{{route('knowledge.addFile')}}" type="button" class="btn flex items-center justify-center  py-3 rounded-lg font-semibold transition mb-6 bg-#5570F1" style="box-shadow: 0px 19px 34px -20px #43BBED; background-color: #5570F1; color: white; padding: 0px 24px; height: 45px; font-size: 15px; text-transform: uppercase; letter-spacing: 0px; margin-top: 28px; border-radius: 60px;">
                    <img src="{{ asset('icons/btn_nova_base.svg') }}" alt="Upload Icon" style="height: 22px; padding-right: 18px;">    
                    Enviar nova base
                </a>
            </div>

            <div class="w-full">
                @if(!empty($ListFiles))
                    <div class="bloco_info_filter_records">            
                        <div style="width: 100%; display:flex; justify-content: space-between; margin:20px 0px 40px;">
                            <div>
                                <div class="bloco_importacao_topo">
                                    <img src="{{ asset('icons/document.svg') }}" alt="Upload Icon" style="height: 22px; margin-bottom: 12px;">    
                                    <div>Total de RFPs</div>
                                    <h6> {{$CountRFPs}} </h6>
                                </div>
                                <div class="bloco_importacao_topo">
                                    <img src="{{ asset('icons/list-check.svg') }}" alt="Upload Icon" style="height: 22px; margin-bottom: 12px;">    
                                    <div>Total de Requisitos</div>
                                    <h6> {{$CountRequisitos}} </h6>
                                </div>
                            </div>

                            @if(isset($lastUpdated))
                                <div class="ultimaAtualizacao" style="border: 1px solid #CCC; border-radius: 10px; width: 362px; height:60px; display: flex;">
                                    <img src="{{ asset('icons/calendar-lines.svg') }}" alt="Upload Icon" style="height: 22px; padding-right: 18px; margin-top: 17px; margin-left: 15px;">    
                                    <div style="display: flex;" title="o último arquivo atualizado foi o {{$lastUpdated->name}}.{{$lastUpdated->file_extension}} no dia {{$lastUpdatedDate}} as {{$lastUpdatedTime}} ">
                                        <span style="color: #525B75; font-size:16px;line-height: 55px;">Última Atualização:</span>
                                        <h2 style="color: #141824; font-size:16px; line-height: 55px; margin-left:5px; font-weight:400;">{{$lastUpdatedTime}}</h2>
                                        <div style="color: #525B75; font-size:16px; line-height: 55px; margin-left:5px;">|</div>
                                        <h3 style="color: #141824; font-size:16px; line-height: 55px; margin-left:5px;">{{$lastUpdatedDate}}</h3>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                
                @endif

                <div id="BlocoLista" class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">

                    @if(!empty($ListFiles))
                        <table id="TableExcel" class="tabela">
                            <thead>
                                <tr>
                                    <th style="width:28%; text-align:left;">Nome do Arquivo:</th>
                                    <th style="width:10%; text-align: center;">Requisitos:</th>
                                    <th style="width:15%; text-align: center;">Última Atualização:</th>
                                    <th style="width:15%; text-align: center;">Responsável:</th>
                                    <th style="width:15%; text-align: center;">Status:</th>
                                    <th style="width:10%;"></th>
                                </tr>    
                            </thead>
                                <tbody class="body_table">
                                    <!-- CARREGA VIA AJAX -->
                                </tbody>
                        </table>

                        <nav id="paginationLinks"></nav>
                    @else
                    <svg width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin: auto; margin-top:15px;">
                    <g clip-path="url(#clip0_255_2742)">
                    <path d="M11.6667 23.3333C11.6667 24.5408 10.6867 25.5208 9.47917 25.5208C8.27167 25.5208 7.29167 24.5408 7.29167 23.3333C7.29167 22.1258 8.27167 21.1458 9.47917 21.1458C10.6867 21.1458 11.6667 22.1258 11.6667 23.3333ZM9.47917 6.5625C8.27167 6.5625 7.29167 7.5425 7.29167 8.75C7.29167 9.9575 8.27167 10.9375 9.47917 10.9375C10.6867 10.9375 11.6667 9.9575 11.6667 8.75C11.6667 7.5425 10.6867 6.5625 9.47917 6.5625ZM9.47917 13.8542C8.27167 13.8542 7.29167 14.8342 7.29167 16.0417C7.29167 17.2492 8.27167 18.2292 9.47917 18.2292C10.6867 18.2292 11.6667 17.2492 11.6667 16.0417C11.6667 14.8342 10.6867 13.8542 9.47917 13.8542ZM27.7083 0H7.29167C3.27104 0 0 3.27104 0 7.29167V26.25C0 30.2706 3.27104 33.5417 7.29167 33.5417H11.6667C12.4717 33.5417 13.125 32.8898 13.125 32.0833C13.125 31.2769 12.4717 30.625 11.6667 30.625H7.29167C4.87958 30.625 2.91667 28.6621 2.91667 26.25V7.29167C2.91667 4.87958 4.87958 2.91667 7.29167 2.91667H27.7083C30.1204 2.91667 32.0833 4.87958 32.0833 7.29167V20.4167C32.0833 21.2231 32.7367 21.875 33.5417 21.875C34.3467 21.875 35 21.2231 35 20.4167V7.29167C35 3.27104 31.729 0 27.7083 0ZM16.0417 10.2083H26.25C27.055 10.2083 27.7083 9.555 27.7083 8.75C27.7083 7.945 27.055 7.29167 26.25 7.29167H16.0417C15.2367 7.29167 14.5833 7.945 14.5833 8.75C14.5833 9.555 15.2367 10.2083 16.0417 10.2083ZM16.0417 17.5H26.25C27.055 17.5 27.7083 16.8467 27.7083 16.0417C27.7083 15.2367 27.055 14.5833 26.25 14.5833H16.0417C15.2367 14.5833 14.5833 15.2367 14.5833 16.0417C14.5833 16.8467 15.2367 17.5 16.0417 17.5ZM34.5698 27.0506C35.14 27.8935 35.14 28.9815 34.5698 29.8244C33.2631 31.7567 30.2546 35 24.7917 35C19.3288 35 16.3202 31.7567 15.0121 29.8244C14.4419 28.98 14.4419 27.8921 15.0121 27.0506C16.3188 25.1183 19.3258 21.875 24.7902 21.875C30.2546 21.875 33.2631 25.1183 34.5698 27.0506ZM31.9813 28.4375C30.9167 26.9704 28.681 24.7917 24.7917 24.7917C20.9023 24.7917 18.6652 26.9719 17.6021 28.4375C18.6652 29.9046 20.9023 32.0833 24.7917 32.0833C28.681 32.0833 30.9167 29.9046 31.9813 28.4375ZM24.7917 26.25C23.5842 26.25 22.6042 27.23 22.6042 28.4375C22.6042 29.645 23.5842 30.625 24.7917 30.625C25.9992 30.625 26.9792 29.645 26.9792 28.4375C26.9792 27.23 25.9992 26.25 24.7917 26.25Z" fill="#8A94AD"/>
                    </g>
                    <defs>
                    <clipPath id="clip0_255_2742">
                    <rect width="35" height="35" fill="white"/>
                    </clipPath>
                    </defs>
                    </svg>

                        <span style="margin: auto; height: 50px; display: block; width: 100%; text-align: center; justify-items: center; line-height: 50px; color: #8A94AD;">Sua lista de arquivos aparecerá aqui!</span>
                    @endif

                </div>

            </div>
        </div>
    </div>
</x-app-layout>

<script>
    $(document).ready(function () {
        function fetchUsers(url = "{{route('knowledge.filter')}}") {
            $.ajax({
                url: url,
                method: 'GET',
                data: $('#filterForm').serialize(),
                success: function (response) {
                    // Atualizar tabela
                    let rows = '';
                    response.data.forEach(record => {
                        console.log(record);
                        // Converter a data para um objeto Date
                        const date = new Date(record.created_at);
                        const day = String(date.getDate()).padStart(2, '0');
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const year = String(date.getFullYear()).slice(-2);
                        const formattedDate = `${day}/${month}/${year}`;

                        // VALIDA O STATUS DO REQUISITO
                        if (record.status === 'não enviado') {
                            status = '<div style="background: #FFEFCA; border: 1px solid #0000000D; border-radius: 8px; font-weight: 600; color: #E5780C; width: auto; padding: 5px 20px; position: relative; display: inline-block;">'+record.status+'</div>';
                            btnEdit = `<a href="knowledge/records/${record.id}" style="margin: 0px; float:left;">
                                            <button type="submit" style="width: 17px; text-align: center; text-transform: uppercase; font-weight: bold; font-size: 13px; margin: 8px;">
                                                <img src="{{ asset('icons/file-edit 1.svg') }}" alt="Edit Icon">
                                            </button>
                                        </a>`;
                        } else   if (record.status === 'processando') {
                            status = '<div style="background: #FFEFCA; border: 1px solid #0000000D; border-radius: 8px; font-weight: 600; color: #E5780C; width: auto; padding: 5px 20px; position: relative; display: inline-block;">'+record.status+'</div>';
                            btnEdit = `<a href="knowledge/records/${record.id}" style="margin: 0px; float:left;">
                                            <button type="submit" style="width: 17px; text-align: center; text-transform: uppercase; font-weight: bold; font-size: 13px; margin: 8px;">
                                                <img src="{{ asset('icons/file-edit 1.svg') }}" alt="Edit Icon">
                                            </button>
                                        </a>`;

                        } else   if (record.status === 'concluído') {
                            btnEdit = `<a href="knowledge/records/${record.id}" style="margin: 0px; float:left;">
                                            <button type="submit" style="width: 17px; text-align: center; text-transform: uppercase; font-weight: bold; font-size: 13px; margin: 8px;">
                                                <img src="{{ asset('icons/eye.svg') }}" alt="Edit Icon">
                                            </button>
                                        </a>

                                        <a href="knowledge/records/${record.id}" style="margin: 0px; float:left;">
                                            <button type="submit" style="width: 17px; text-align: center; text-transform: uppercase; font-weight: bold; font-size: 13px; margin: 8px;">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="#8A94AD" xmlns="http://www.w3.org/2000/svg">
                                                <g clip-path="url(#clip0_485_4256)">
                                                <path d="M10.7812 17.0091V7.03125H9.21872V17.0091L7.97985 15.7702C7.67476 15.4651 7.1801 15.4651 6.87499 15.7701C6.56987 16.0752 6.56986 16.5699 6.87497 16.875L9.99997 20L13.125 16.875C13.4301 16.5699 13.4301 16.0752 13.125 15.7701C12.8198 15.4651 12.3252 15.4651 12.0201 15.7702L10.7812 17.0091Z" fill="#8A94AD"/>
                                                <path d="M16.8598 4.90602C16.2368 2.07324 13.7393 0 10.7812 0C8.49715 0 6.43188 1.24695 5.33586 3.21422C3.69297 3.53738 2.41145 4.88215 2.21379 6.57777C0.816094 7.1893 0 8.59406 0 10.1562C0 12.3129 1.59051 14.0625 3.75 14.0625H6.875C7.30647 14.0625 7.65625 13.7127 7.65625 13.2812C7.65625 12.8498 7.30647 12.5 6.875 12.5H3.75C2.50293 12.5 1.5625 11.4924 1.5625 10.1562C1.5625 9.03242 2.19453 8.145 3.17266 7.89543L3.78137 7.74012C3.75828 7.1357 3.76566 7.10984 3.75023 6.99691C3.76734 5.78777 4.6891 4.80762 5.90773 4.71008L6.35922 4.67395L6.55219 4.26418C7.32516 2.62297 8.9852 1.5625 10.7812 1.5625C13.1118 1.5625 15.0991 3.31621 15.4039 5.64176L15.4777 6.20566L16.037 6.30863C17.4729 6.57289 18.4375 7.8052 18.4375 9.375C18.4375 11.1565 17.1612 12.5 15.4688 12.5H13.125C12.6935 12.5 12.3438 12.8498 12.3438 13.2812C12.3438 13.7127 12.6935 14.0625 13.125 14.0625H15.4688C18.0096 14.0625 20 12.0035 20 9.375C20 7.26086 18.734 5.49688 16.8598 4.90602Z" fill="#8A94AD"/>
                                                </g>
                                               
                                                </svg>
                                            </button>
                                        </a>`;

                            status = '<div style="background: #25B102; border: 1px solid #0000000D; border-radius: 8px; font-weight: 600; color: #FFF; width: auto; padding: 5px 20px; position: relative; display: inline-block;">'+record.status+'</div>';
                        }

                        // <form action="knowledge/records/${record.id}"" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este arquivo?');" style="margin: 0px; float:left;">
                        //                     @csrf
                        //                     @method('DELETE')
                        //                     <button type="submit" style="width: 20px; text-align: center; text-transform: uppercase; font-weight: bold; font-size: 13px; margin: 6px;">
                        //                         <img src="{{ asset('icons/eye.svg') }}" alt="Eye Icon">
                        //                     </button>
                        //                 </form>
                        
                        // DADOS DE CADA LINHA
                        rows += `
                            <tr class="listaTabela" style="min-height:60px; max-height: 100%;">
                                    <td style="width:30%; text-align:left; line-height: 33px;" title="${record.filename_original}">${record.name}.${record.file_extension}</td>
                                    <td style="width:10%; line-height: 33px;"> ${record.knowledge_records_count} </td>
                                    <td style="width:15%; line-height: 33px;">${formattedDate}</td>
                                    <td style="width:17%;"> 
                                        <div style="background: #E0E0E0; border: 1px solid #0000000D; border-radius: 8px; font-weight: 600; color: #141824; width: auto; padding: 5px 20px; position: relative; display: inline-block;">${record.user.name}</div>
                                    </td>
                                    <td style="width:15%;">${status}</td>
                                    <td style="width:6%; margin-left:2%;"> 
                                        ${btnEdit}
                                    </td>
                                </tr>
                        `;
                    });

                    $('#TableExcel .body_table').html(rows);

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

        // Submeter filtros
        $('#filterForm').on('submit', function (e) {
            e.preventDefault();
            fetchUsers();
        });

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

        // Carregar lista inicial
        fetchUsers();
    });
</script>