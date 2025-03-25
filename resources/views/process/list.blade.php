<x-app-layout>
    <div class="py-4" style=" padding-bottom: 130px;">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-title-component :showButton="true" $componentType = "list" titleDescription="" textButton="Adicionar Processo" urlButton="/process/new" > {{ __('Processos Cadastrados') }} </x-title-component>

            <div id="BlocoLista" class="p-4 sm:p-8">
                <div class="bloco_info_details_header">
                    <div id="bloco_info_found">
                        <div class="info_details_total">
                            Total Encontrados: <span>{{$TotalFound}}</span>
                        </div>
                    </div>

                    <form id="filterForm">
                       
                        <select name="sort_order" style="width:190px;">
                            <option value="id_asc" selected>Ordem crescente</option>
                            <option value="id_desc">Ordem decrescente</option>
                            <option value="bundle_asc">De A a Z</option>
                            <option value="bundle_desc">De Z a A</option>
                           
                        </select>
                        <!-- <button type="submit">Filtrar</button> -->
                    </form> 

                    
                </div>
               

                <table id="userTable" class="tabela">
                    <thead>
                        <tr>
                            <th style="width:11%; text-align:center;">ID</th>
                            <th style="width:60%;">Nome do Processo</th>
                            <th  style="width:13%; text-align:center;">Data do Cadastro</th>
                            <th  style="width:15%; text-align:center;">Ação</th>
                        </tr>    
                    </thead>
                    <tbody class="body_table">
                    
                    </tbody>
                </table>

                <nav id="paginationLinks"></nav>

            </div>
        </div>
    </div>
</x-app-layout>

<script>
    $(document).ready(function () {
        function fetchUsers(url = "{{ route('process.filter') }}") {
            $.ajax({
                url: url,
                method: 'GET',
                data: $('#filterForm').serialize(),
                success: function (response) {
                    // Atualizar tabela
                    let rows = '';
                    response.data.forEach(process => {
                        const date = new Date(process.created_at);
                        const day = String(date.getDate()).padStart(2, '0');
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const year = String(date.getFullYear()).slice(-2);
                        const formattedDate = `${day}/${month}/${year}`;

                        rows += `
                            <tr class="listaTabela">
                              <td style="width:10%;">#${process.id}</td>
                                <td style="width:60%; text-align:left;">${process.process}</td>
                                <td style="width:20%; text-align:center;">${formattedDate}</td>
                                <td style="width:10%; text-align:center; position:relative; display:flex;">
                                    <a class="btn_edit_row" href="/process/edit/${process.id}" style="margin: -6px 10px;;">
                                        <button type="submit" style="width: 17px; text-align: center; text-transform: uppercase; font-weight: bold; font-size: 13px; margin: 3px;">
                                            <svg width="17" height="17" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g clip-path="url(#clip0_329_10365)"><path d="M15.6775 8.33333C15.935 8.33333 16.1783 8.21417 16.3358 8.01083C16.4933 7.8075 16.5483 7.5425 16.485 7.29333C16.2258 6.27917 15.6975 5.3525 14.9575 4.6125L12.0533 1.70833C10.9517 0.606667 9.48667 0 7.92833 0H4.16583C1.86917 0 0 1.86917 0 4.16667V15.8333C0 18.1308 1.86917 20 4.16667 20H6.66667C7.12667 20 7.5 19.6267 7.5 19.1667C7.5 18.7067 7.12667 18.3333 6.66667 18.3333H4.16667C2.78833 18.3333 1.66667 17.2117 1.66667 15.8333V4.16667C1.66667 2.78833 2.78833 1.66667 4.16667 1.66667H7.92917C8.065 1.66667 8.2 1.67333 8.33333 1.68583V5.83333C8.33333 7.21167 9.455 8.33333 10.8333 8.33333H15.6775ZM10 5.83333V2.21583C10.3158 2.3975 10.61 2.6225 10.875 2.8875L13.7792 5.79167C14.0408 6.05333 14.265 6.34833 14.4483 6.66667H10.8333C10.3742 6.66667 10 6.2925 10 5.83333ZM19.2683 9.89917C18.3233 8.95417 16.6767 8.95417 15.7325 9.89917L10.1433 15.4883C9.51417 16.1175 9.16667 16.955 9.16667 17.8458V19.1675C9.16667 19.6275 9.54 20.0008 10 20.0008H11.3217C12.2125 20.0008 13.0492 19.6533 13.6783 19.0242L19.2675 13.435C19.74 12.9625 20 12.335 20 11.6667C20 10.9983 19.74 10.3708 19.2683 9.89917ZM18.0892 12.2558L12.4992 17.845C12.185 18.16 11.7667 18.3333 11.3208 18.3333H10.8325V17.845C10.8325 17.4 11.0058 16.9817 11.3208 16.6667L16.9108 11.0775C17.225 10.7625 17.7742 10.7625 18.0892 11.0775C18.2467 11.2342 18.3333 11.4433 18.3333 11.6667C18.3333 11.89 18.2467 12.0983 18.0892 12.2558Z" fill="#8A94AD"/></g>
                                                <clipPath id="clip0_329_10365"><rect width="20" height="20" fill="white"/></clipPath>
                                            </svg>
                                        </button>
                                    </a>
                                    <div class="btn_delete_row">
                                        <form action="process/remove/${process.id}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este produto?');" style="margin: 0px;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit">
                                                <svg width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M5.70786 1.62227L5.45714 2.125H2.11429C1.49795 2.125 1 2.62773 1 3.25C1 3.87227 1.49795 4.375 2.11429 4.375H15.4857C16.1021 4.375 16.6 3.87227 16.6 3.25C16.6 2.62773 16.1021 2.125 15.4857 2.125H12.1429L11.8921 1.62227C11.7041 1.23906 11.3176 1 10.8963 1H6.70375C6.28241 1 5.89589 1.23906 5.70786 1.62227ZM15.4857 5.5H2.11429L2.8525 17.418C2.90821 18.3074 3.63946 19 4.52045 19H13.0796C13.9605 19 14.6918 18.3074 14.7475 17.418L15.4857 5.5Z" fill="#C8C8C8"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>

                                </td>
                            </tr>
                        `;
                    });

                    $('#userTable .body_table').html(rows);

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

        // Submeter filtros
        $('#filterForm').on('change', function () {
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
</body>
</html>
