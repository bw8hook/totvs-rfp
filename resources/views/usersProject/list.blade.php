<x-app-layout>
    <div class="flex flex-col">
        <div class="py-4" style=" padding-bottom: 130px;">

        <div id="titleComponent_KnowledgeBase" class="text-lg font-bold flex items-center justify-between w-full px-4 space-x-2 relative" style="height:100px; background:#F9F9F9; box-shadow: 0px 4px 44px 0px #0000000D;">
                <div class="AlignTitleLeft" style="width: 80%;">
                    <div class="flex" style="width: 100%;">
                        <svg width="39" height="22" viewBox="0 0 29 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14.6235 11.2867C15.314 10.6889 15.8679 9.94969 16.2475 9.11904C16.6271 8.2884 16.8235 7.38581 16.8235 6.47254C16.8235 4.75643 16.1418 3.11062 14.9283 1.89714C13.7149 0.683674 12.069 0.00195336 10.3529 0.00195336C8.63683 0.00195336 6.99101 0.683674 5.77754 1.89714C4.56407 3.11062 3.88235 4.75643 3.88235 6.47254C3.88234 7.38581 4.0788 8.2884 4.4584 9.11904C4.83799 9.94969 5.39184 10.6889 6.08235 11.2867C4.27077 12.107 2.73378 13.4317 1.65515 15.1024C0.576523 16.7731 0.00190475 18.7192 0 20.7078C0 21.0511 0.136344 21.3802 0.379038 21.6229C0.621732 21.8656 0.950896 22.002 1.29412 22.002C1.63734 22.002 1.9665 21.8656 2.2092 21.6229C2.45189 21.3802 2.58824 21.0511 2.58824 20.7078C2.58824 18.6485 3.4063 16.6735 4.86246 15.2174C6.31863 13.7612 8.29361 12.9431 10.3529 12.9431C12.4123 12.9431 14.3873 13.7612 15.8434 15.2174C17.2996 16.6735 18.1176 18.6485 18.1176 20.7078C18.1176 21.0511 18.254 21.3802 18.4967 21.6229C18.7394 21.8656 19.0685 22.002 19.4118 22.002C19.755 22.002 20.0842 21.8656 20.3268 21.6229C20.5695 21.3802 20.7059 21.0511 20.7059 20.7078C20.704 18.7192 20.1294 16.7731 19.0507 15.1024C17.9721 13.4317 16.4351 12.107 14.6235 11.2867ZM10.3529 10.3549C9.58508 10.3549 8.83447 10.1272 8.19602 9.7006C7.55757 9.274 7.05996 8.66766 6.76611 7.95825C6.47227 7.24885 6.39538 6.46824 6.54519 5.71513C6.69499 4.96203 7.06475 4.27026 7.6077 3.7273C8.15066 3.18435 8.84243 2.81459 9.59553 2.66479C10.3486 2.51499 11.1292 2.59187 11.8387 2.88572C12.5481 3.17956 13.1544 3.67717 13.581 4.31562C14.0076 4.95407 14.2353 5.70468 14.2353 6.47254C14.2353 7.50221 13.8263 8.4897 13.0982 9.21778C12.3701 9.94586 11.3826 10.3549 10.3529 10.3549ZM22.9576 10.769C23.7858 9.83638 24.3268 8.68426 24.5155 7.45133C24.7042 6.21841 24.5325 4.95723 24.0211 3.8196C23.5097 2.68198 22.6804 1.71639 21.6331 1.03909C20.5857 0.361783 19.3649 0.00161976 18.1176 0.00195336C17.7744 0.00195336 17.4453 0.138297 17.2026 0.380992C16.9599 0.623686 16.8235 0.95285 16.8235 1.29607C16.8235 1.63929 16.9599 1.96846 17.2026 2.21115C17.4453 2.45384 17.7744 2.59019 18.1176 2.59019C19.1473 2.59019 20.1348 2.99922 20.8629 3.7273C21.591 4.45539 22 5.44288 22 6.47254C21.9982 7.15226 21.8179 7.81959 21.4773 8.40779C21.1366 8.99599 20.6475 9.48445 20.0588 9.82431C19.867 9.93498 19.7067 10.093 19.5934 10.2834C19.4801 10.4737 19.4176 10.6899 19.4118 10.9114C19.4063 11.1311 19.457 11.3485 19.5589 11.5432C19.6607 11.7379 19.8105 11.9035 19.9941 12.0243L20.4988 12.3608L20.6671 12.4514C22.227 13.1912 23.543 14.3615 24.4601 15.8242C25.3772 17.287 25.8572 18.9814 25.8435 20.7078C25.8435 21.0511 25.9799 21.3802 26.2226 21.6229C26.4653 21.8656 26.7944 22.002 27.1376 22.002C27.4809 22.002 27.81 21.8656 28.0527 21.6229C28.2954 21.3802 28.4318 21.0511 28.4318 20.7078C28.4423 18.7219 27.9449 16.7663 26.9868 15.0267C26.0287 13.2872 24.6417 11.8215 22.9576 10.769Z" fill="#5570F1"/>
                        </svg>
                        <span style=" margin-left: 6px;margin-top: 1px;">Usuários Cadastrados</span>
                    </div>
                </div>
               
                <a href="{{route('users.register')}}" type="button" class="btn flex items-center justify-center  py-3 rounded-lg font-semibold transition mb-6 bg-#5570F1" style="box-shadow: 0px 19px 34px -20px #43BBED; background-color: #5570F1; color: white; padding: 0px 24px; height: 45px; font-size: 15px; text-transform: uppercase; letter-spacing: 0px; margin-top: 28px; border-radius: 8px;">
                    Cadastrar Usuário
                </a>
            </div>

            <div id="BlocoLista" class="p-4 sm:p-8" style="background:#FFF;">
                <div class="bloco_info_details_header" style="height: 80px; margin-top: 20px;">
                    <form id="filterFormList" style="display: grid;">
                        <span style="font-style: normal; font-weight: 700; font-size: 12px; line-height: 150%; color: #5E6470;">Filtragem rápida:</span>
                        <div style="display: inline-flex ;">
                            <div class="inputField">
                                <input type="text" id="nome" name="nome" placeholder="Nome">
                            </div>

                            <div class="inputField">
                                <input type="text" id="id_totvs" name="id_totvs" placeholder="Código Totvs">
                            </div>

                            <div class="inputField">
                                <input type="text" id="position" name="position" placeholder="Cargo">
                            </div>

                            <div class="inputField">
                                <select name="departament">
                                    <option value="null" selected>Setor</option>
                                    @foreach ($AllDepartaments as $departament)
                                    <option value="{{ $departament->id }}">{{ $departament->departament }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="inputField">
                                <select name="role">
                                    <option value="null" selected>Perfil</option>
                                    @foreach ($AllRoles as $Role)
                                        <option value="{{$Role->name}}">{{ $Role->name }}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="toggle-container">
                                <span class="toggle-label">Usuário ativo?</span>
                                <div class="toggle-wrapper">
                                    <span class="toggle-option">Não</span>
                                    <label class="toggle-switch">
                                    <input type="checkbox" id="toggleSwitch" checked>
                                    <span class="slider"></span>
                                    </label>
                                    <span class="toggle-option">Sim</span>
                                </div>
                            </div>

                                                    
                           

                            <button type="submit">Filtrar</button>

                        </div>

                    </form> 
                </div>
               

                <table id="userTable" class="tabela">
                    <thead>
                        <tr>
                            <th style="width:12%;">Nome:</th>
                            <th style="width:11%; text-align:center;" >ID TOTVS:</th>
                            <th style="width:11%; text-align:center;">Cargo</th>
                            <th style="width:12%; text-align:center;">Setor</th>
                            <!-- <th style="width:12%">Produtos/Skills</th> -->
                            <th style="width:20%; text-align:center;">Contato</th>
                            <th style="width:10%; text-align:center;">Data do Cadastro</th>
                            <th style="width:15%; text-align:center;">Perfil</th>
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
            var formData = $("#filterFormList").serialize();
            var isActive = $('#toggleSwitch').is(':checked') ? 'ativo' : 'inativo';
            formData += '&user_active=' + isActive;
            
            $.ajax({
                url: url,
                method: 'GET',
                data: formData,
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

                        let statusPerfil;
                        if (user.status === "ativo") {
                            statusPerfil = `<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.25 9.43332C7.44899 9.43332 6.66596 9.19579 5.99994 8.75077C5.33392 8.30575 4.81482 7.67322 4.50829 6.93318C4.20175 6.19314 4.12155 5.37882 4.27782 4.5932C4.43409 3.80758 4.81981 3.08593 5.38622 2.51953C5.95262 1.95313 6.67426 1.5674 7.45988 1.41113C8.24551 1.25486 9.05983 1.33507 9.79987 1.6416C10.5399 1.94814 11.1724 2.46724 11.6175 3.13326C12.0625 3.79927 12.3 4.5823 12.3 5.38332C12.2988 6.45708 11.8717 7.48652 11.1125 8.24578C10.3532 9.00505 9.32376 9.43213 8.25 9.43332ZM8.25 3.13332C7.80499 3.13332 7.36998 3.26528 6.99997 3.51251C6.62996 3.75974 6.34157 4.11114 6.17127 4.52228C6.00097 4.93341 5.95642 5.38581 6.04323 5.82227C6.13005 6.25873 6.34434 6.65964 6.65901 6.97431C6.97368 7.28897 7.37459 7.50327 7.81105 7.59008C8.2475 7.6769 8.6999 7.63234 9.11104 7.46204C9.52217 7.29175 9.87357 7.00336 10.1208 6.63335C10.368 6.26334 10.5 5.82832 10.5 5.38332C10.5 4.78658 10.2629 4.21428 9.84099 3.79233C9.41903 3.37037 8.84674 3.13332 8.25 3.13332ZM15 18.4333V17.9833C15 16.1931 14.2888 14.4762 13.023 13.2103C11.7571 11.9445 10.0402 11.2333 8.25 11.2333C6.45979 11.2333 4.7429 11.9445 3.47703 13.2103C2.21116 14.4762 1.5 16.1931 1.5 17.9833L1.5 18.4333C1.5 18.672 1.59482 18.9009 1.7636 19.0697C1.93239 19.2385 2.16131 19.3333 2.4 19.3333C2.63869 19.3333 2.86761 19.2385 3.0364 19.0697C3.20518 18.9009 3.3 18.672 3.3 18.4333V17.9833C3.3 16.6705 3.82152 15.4114 4.74982 14.4831C5.67813 13.5548 6.93718 13.0333 8.25 13.0333C9.56282 13.0333 10.8219 13.5548 11.7502 14.4831C12.6785 15.4114 13.2 16.6705 13.2 17.9833V18.4333C13.2 18.672 13.2948 18.9009 13.4636 19.0697C13.6324 19.2385 13.8613 19.3333 14.1 19.3333C14.3387 19.3333 14.5676 19.2385 14.7364 19.0697C14.9052 18.9009 15 18.672 15 18.4333Z" fill="#0097EB"/>
                                <path d="M19 15.5C19 17.9814 16.9814 20 14.5 20C12.0186 20 10 17.9814 10 15.5C10 13.0186 12.0186 11 14.5 11C16.9814 11 19 13.0186 19 15.5Z" fill="#0097EB"/>
                                <path d="M12 16L14 17.5L17 14" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>`;
                        } else {
                            statusPerfil = `<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.25 9.43332C7.44899 9.43332 6.66596 9.19579 5.99994 8.75077C5.33392 8.30575 4.81482 7.67322 4.50829 6.93318C4.20175 6.19314 4.12155 5.37882 4.27782 4.5932C4.43409 3.80758 4.81981 3.08593 5.38622 2.51953C5.95262 1.95313 6.67426 1.5674 7.45988 1.41113C8.24551 1.25486 9.05983 1.33507 9.79987 1.6416C10.5399 1.94814 11.1724 2.46724 11.6175 3.13326C12.0625 3.79927 12.3 4.5823 12.3 5.38332C12.2988 6.45708 11.8717 7.48652 11.1125 8.24578C10.3532 9.00505 9.32376 9.43213 8.25 9.43332ZM8.25 3.13332C7.80499 3.13332 7.36998 3.26528 6.99997 3.51251C6.62996 3.75974 6.34157 4.11114 6.17127 4.52228C6.00097 4.93341 5.95642 5.38581 6.04323 5.82227C6.13005 6.25873 6.34434 6.65964 6.65901 6.97431C6.97368 7.28897 7.37459 7.50327 7.81105 7.59008C8.2475 7.6769 8.6999 7.63234 9.11104 7.46204C9.52217 7.29175 9.87357 7.00336 10.1208 6.63335C10.368 6.26334 10.5 5.82832 10.5 5.38332C10.5 4.78658 10.2629 4.21428 9.84099 3.79233C9.41903 3.37037 8.84674 3.13332 8.25 3.13332ZM15 18.4333V17.9833C15 16.1931 14.2888 14.4762 13.023 13.2103C11.7571 11.9445 10.0402 11.2333 8.25 11.2333C6.45979 11.2333 4.7429 11.9445 3.47703 13.2103C2.21116 14.4762 1.5 16.1931 1.5 17.9833L1.5 18.4333C1.5 18.672 1.59482 18.9009 1.7636 19.0697C1.93239 19.2385 2.16131 19.3333 2.4 19.3333C2.63869 19.3333 2.86761 19.2385 3.0364 19.0697C3.20518 18.9009 3.3 18.672 3.3 18.4333V17.9833C3.3 16.6705 3.82152 15.4114 4.74982 14.4831C5.67813 13.5548 6.93718 13.0333 8.25 13.0333C9.56282 13.0333 10.8219 13.5548 11.7502 14.4831C12.6785 15.4114 13.2 16.6705 13.2 17.9833V18.4333C13.2 18.672 13.2948 18.9009 13.4636 19.0697C13.6324 19.2385 13.8613 19.3333 14.1 19.3333C14.3387 19.3333 14.5676 19.2385 14.7364 19.0697C14.9052 18.9009 15 18.672 15 18.4333Z" fill="#C8C8C8"/>
                                <path d="M19 15.5C19 17.9814 16.9814 20 14.5 20C12.0186 20 10 17.9814 10 15.5C10 13.0186 12.0186 11 14.5 11C16.9814 11 19 13.0186 19 15.5Z" fill="#C8C8C8"/>
                                <path d="M15.2069 15.5L16.3534 14.3536C16.5488 14.1581 16.5488 13.8421 16.3534 13.6466C16.1579 13.4511 15.8419 13.4511 15.6464 13.6466L14.5 14.793L13.3535 13.6466C13.1581 13.4511 12.8421 13.4511 12.6466 13.6466C12.4511 13.8421 12.4511 14.1581 12.6466 14.3536L13.793 15.5L12.6466 16.6464C12.4511 16.8419 12.4511 17.1579 12.6466 17.3534C12.7441 17.4509 12.8721 17.4999 13.0001 17.4999C13.1281 17.4999 13.256 17.4509 13.3535 17.3534L14.5 16.207L15.6464 17.3534C15.7439 17.4509 15.8719 17.4999 15.9999 17.4999C16.1279 17.4999 16.2559 17.4509 16.3534 17.3534C16.5488 17.1579 16.5488 16.8419 16.3534 16.6464L15.2069 15.5Z" fill="white"/>
                            </svg>`;
                        }


                        rows += `
                            <tr class="listaTabela">
                                <td style="width: 28%; text-align: left;">${user.name}</td>
                                <td style="width: 24%; text-align: center;">${user.idtotvs ? user.idtotvs : '-'}</td>
                                 <td style="width: 32%; text-align: center;">${user.position ? user.position : '-'}</td>
                                <td style="width: 32%; text-align: center;">${user.departament.departament}</td>
                                <td style="width: 42%; text-align: center;">${user.email}</td>
                                <td style="width: 32%; text-align: center;">${formattedDate}</td>
                                <td style="width: 30%; text-align: center;"> <div class="tipo_cadastro" style="width:auto;">${user.role_names}</div></td>
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
                                    <div class="btn_edit_row">${statusPerfil}</div>
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
        $('#filterFormList').on('submit', function (e) {
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
