<x-app-layout>
    <div class="flex flex-col">
        <div class="py-4" style=" padding-bottom: 130px;">

        <div id="titleComponent_KnowledgeBase" class="text-lg font-bold flex items-center justify-between w-full px-4 space-x-2 relative" style="height:100px;">
                <div class="AlignTitleLeft" style="width: 80%;">
                    <div class="flex" style="width: 100%;">
                        <svg width="39" height="22" viewBox="0 0 29 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14.6235 11.2867C15.314 10.6889 15.8679 9.94969 16.2475 9.11904C16.6271 8.2884 16.8235 7.38581 16.8235 6.47254C16.8235 4.75643 16.1418 3.11062 14.9283 1.89714C13.7149 0.683674 12.069 0.00195336 10.3529 0.00195336C8.63683 0.00195336 6.99101 0.683674 5.77754 1.89714C4.56407 3.11062 3.88235 4.75643 3.88235 6.47254C3.88234 7.38581 4.0788 8.2884 4.4584 9.11904C4.83799 9.94969 5.39184 10.6889 6.08235 11.2867C4.27077 12.107 2.73378 13.4317 1.65515 15.1024C0.576523 16.7731 0.00190475 18.7192 0 20.7078C0 21.0511 0.136344 21.3802 0.379038 21.6229C0.621732 21.8656 0.950896 22.002 1.29412 22.002C1.63734 22.002 1.9665 21.8656 2.2092 21.6229C2.45189 21.3802 2.58824 21.0511 2.58824 20.7078C2.58824 18.6485 3.4063 16.6735 4.86246 15.2174C6.31863 13.7612 8.29361 12.9431 10.3529 12.9431C12.4123 12.9431 14.3873 13.7612 15.8434 15.2174C17.2996 16.6735 18.1176 18.6485 18.1176 20.7078C18.1176 21.0511 18.254 21.3802 18.4967 21.6229C18.7394 21.8656 19.0685 22.002 19.4118 22.002C19.755 22.002 20.0842 21.8656 20.3268 21.6229C20.5695 21.3802 20.7059 21.0511 20.7059 20.7078C20.704 18.7192 20.1294 16.7731 19.0507 15.1024C17.9721 13.4317 16.4351 12.107 14.6235 11.2867ZM10.3529 10.3549C9.58508 10.3549 8.83447 10.1272 8.19602 9.7006C7.55757 9.274 7.05996 8.66766 6.76611 7.95825C6.47227 7.24885 6.39538 6.46824 6.54519 5.71513C6.69499 4.96203 7.06475 4.27026 7.6077 3.7273C8.15066 3.18435 8.84243 2.81459 9.59553 2.66479C10.3486 2.51499 11.1292 2.59187 11.8387 2.88572C12.5481 3.17956 13.1544 3.67717 13.581 4.31562C14.0076 4.95407 14.2353 5.70468 14.2353 6.47254C14.2353 7.50221 13.8263 8.4897 13.0982 9.21778C12.3701 9.94586 11.3826 10.3549 10.3529 10.3549ZM22.9576 10.769C23.7858 9.83638 24.3268 8.68426 24.5155 7.45133C24.7042 6.21841 24.5325 4.95723 24.0211 3.8196C23.5097 2.68198 22.6804 1.71639 21.6331 1.03909C20.5857 0.361783 19.3649 0.00161976 18.1176 0.00195336C17.7744 0.00195336 17.4453 0.138297 17.2026 0.380992C16.9599 0.623686 16.8235 0.95285 16.8235 1.29607C16.8235 1.63929 16.9599 1.96846 17.2026 2.21115C17.4453 2.45384 17.7744 2.59019 18.1176 2.59019C19.1473 2.59019 20.1348 2.99922 20.8629 3.7273C21.591 4.45539 22 5.44288 22 6.47254C21.9982 7.15226 21.8179 7.81959 21.4773 8.40779C21.1366 8.99599 20.6475 9.48445 20.0588 9.82431C19.867 9.93498 19.7067 10.093 19.5934 10.2834C19.4801 10.4737 19.4176 10.6899 19.4118 10.9114C19.4063 11.1311 19.457 11.3485 19.5589 11.5432C19.6607 11.7379 19.8105 11.9035 19.9941 12.0243L20.4988 12.3608L20.6671 12.4514C22.227 13.1912 23.543 14.3615 24.4601 15.8242C25.3772 17.287 25.8572 18.9814 25.8435 20.7078C25.8435 21.0511 25.9799 21.3802 26.2226 21.6229C26.4653 21.8656 26.7944 22.002 27.1376 22.002C27.4809 22.002 27.81 21.8656 28.0527 21.6229C28.2954 21.3802 28.4318 21.0511 28.4318 20.7078C28.4423 18.7219 27.9449 16.7663 26.9868 15.0267C26.0287 13.2872 24.6417 11.8215 22.9576 10.769Z" fill="#5570F1"/>
                        </svg>
                        <span style=" margin-left: 6px;margin-top: 1px;">Usuários Cadastrados</span>
                    </div>
                </div>
               
                <a href="{{route('users.register')}}" type="button" class="btn flex items-center justify-center  py-3 rounded-lg font-semibold transition mb-6 bg-#5570F1" style="box-shadow: 0px 19px 34px -20px #43BBED; background-color: #5570F1; color: white; padding: 0px 24px; height: 45px; font-size: 15px; text-transform: uppercase; letter-spacing: 0px; margin-top: 28px; border-radius: 8px;">
                    <img src="{{ asset('icons/btn_nova_base.svg') }}" alt="Upload Icon" style="height: 22px; padding-right: 18px;">    
                    Criar Novo Usuário
                </a>
            </div>

            <div id="BlocoLista" class="p-4 sm:p-8">
                <!-- <div class="bloco_info_details_header">
                    <form id="filterForm">
                        <span>Filtragem rápida:</span>
                        <select name="sort_by" style="height: 40px; padding: 0px 14px;">
                            <option value="bundle">Pacote</option>
                            <option value="bundle_id">Id do Pacote</option>
                            <option value="created_at">Data de Cadastro</option>
                        </select>
                        <select name="sort_order" style="height: 40px; padding: 0px 14px;">
                            <option value="asc">Ascendente</option>
                            <option value="desc">Descendente</option>
                        </select>
                        <button type="submit">Filtrar</button>
                    </form> 

                    <div id="bloco_info_found">
                        <div class="info_details_total">
                            Total Encontrados: <span>0</span>
                        </div>
                    </div>
                </div> -->
               

                <table id="userTable" class="tabela">
                    <thead>
                        <tr>
                            <th style="width:12%;">Nome:</th>
                            <th style="width:11%; text-align:center;" >ID TOTVS:</th>
                            <th style="width:12%; text-align:center;">Setor</th>
                            <!-- <th style="width:12%">Produtos/Skills</th> -->
                            <th style="width:20%; text-align:center;">Contato</th>
                            <th style="width:10%; text-align:center;">Data do Cadastro</th>
                            <th style="width:15%; text-align:center;">Tipo de Cadastro</th>
                            <th style="width:10%; text-align:center;">Ações</th>
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
        function fetchUsers(url = "{{ route('users.filter') }}") {
            $.ajax({
                url: url,
                method: 'GET',
                data: $('#filterForm').serialize(),
                success: function (response) {
                    // Atualizar tabela
                    let rows = '';
                    const QtdTotal = Object.keys(response.data).length
                    response.data.forEach(user => {
                        console.log(user);
                        const date = new Date(user.created_at);
                        const day = String(date.getDate()).padStart(2, '0'); // Adiciona zero à esquerda, se necessário
                        const month = String(date.getMonth() + 1).padStart(2, '0'); // `getMonth()` retorna 0 para janeiro
                        const year = date.getFullYear();
                        const formattedDate = `${day}/${month}/${year}`;
                        rows += `
                            <tr class="listaTabela">
                                <td style="width: 28%; text-align: left;">${user.name}</td>
                                <td style="width: 24%; text-align: center;">${user.idtotvs ? user.idtotvs : '-'}</td>
                                <td style="width: 32%; text-align: center;">${user.departament.departament}</td>
                                <td style="width: 42%; text-align: center;">${user.email}</td>
                                <td style="width: 32%; text-align: center;">${formattedDate}</td>
                                <td style="width: 30%; text-align: center;"> <div class="tipo_cadastro" style="width:auto;">${user.role.name}</div></td>
                                <td style="width:12%; margin-left:3%; text-align:center; position:relative; display:flex;">
                                    <a href="/users/edit/${user.id}" class="btn_edit_row">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g clip-path="url(#clip0_126_4845)">
                                                <path d="M15.6775 8.33333C15.935 8.33333 16.1783 8.21417 16.3358 8.01083C16.4933 7.8075 16.5483 7.5425 16.485 7.29333C16.2258 6.27917 15.6975 5.3525 14.9575 4.6125L12.0533 1.70833C10.9517 0.606667 9.48667 0 7.92833 0H4.16583C1.86917 0 0 1.86917 0 4.16667V15.8333C0 18.1308 1.86917 20 4.16667 20H6.66667C7.12667 20 7.5 19.6267 7.5 19.1667C7.5 18.7067 7.12667 18.3333 6.66667 18.3333H4.16667C2.78833 18.3333 1.66667 17.2117 1.66667 15.8333V4.16667C1.66667 2.78833 2.78833 1.66667 4.16667 1.66667H7.92917C8.065 1.66667 8.2 1.67333 8.33333 1.68583V5.83333C8.33333 7.21167 9.455 8.33333 10.8333 8.33333H15.6775ZM10 5.83333V2.21583C10.3158 2.3975 10.61 2.6225 10.875 2.8875L13.7792 5.79167C14.0408 6.05333 14.265 6.34833 14.4483 6.66667H10.8333C10.3742 6.66667 10 6.2925 10 5.83333ZM19.2683 9.89917C18.3233 8.95417 16.6767 8.95417 15.7325 9.89917L10.1433 15.4883C9.51417 16.1175 9.16667 16.955 9.16667 17.8458V19.1675C9.16667 19.6275 9.54 20.0008 10 20.0008H11.3217C12.2125 20.0008 13.0492 19.6533 13.6783 19.0242L19.2675 13.435C19.74 12.9625 20 12.335 20 11.6667C20 10.9983 19.74 10.3708 19.2683 9.89917ZM18.0892 12.2558L12.4992 17.845C12.185 18.16 11.7667 18.3333 11.3208 18.3333H10.8325V17.845C10.8325 17.4 11.0058 16.9817 11.3208 16.6667L16.9108 11.0775C17.225 10.7625 17.7742 10.7625 18.0892 11.0775C18.2467 11.2342 18.3333 11.4433 18.3333 11.6667C18.3333 11.89 18.2467 12.0983 18.0892 12.2558Z" fill="#C8C8C8"/>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_126_4845">
                                                    <rect width="20" height="20" fill="white"/>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </a>

                                    <div class="btn_delete_row">
                                        <form action="users/remove/${user.id}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este produto?');" style="margin: 0px;">
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

                    
                    $('.info_details_total span').html(QtdTotal);
                    $('#userTable .body_table').html(rows);

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
</body>
</html>
