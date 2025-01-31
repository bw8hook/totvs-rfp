<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Estilo básico */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }

        #paginationLinks{
            width: auto;
            display: table;
            margin: 30px auto 10px;
        }

        .pagination-link {
            text-decoration: none;
            margin: 5px;
            text-align: center;
            line-height: 38px;
            border: 1px solid #999999;
            border-radius: 3px;
            color: #999999;
            width: 40px;
            height: 40px;
            border-radius: 40px;
            display: inline-grid;
        }
        
        .pagination-link:hover {
            background-color: #3A57E8;
            color: #FFF;
            border-color: #3A57E8;
        }

        .pagination-link.active {
            color: #3A57E8;
            background-color: transparent;
            border-color: #3A57E8;
        }
    </style>

<x-app-layout>
    <div class="py-12" style=" padding-bottom: 130px;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-title-component :showButton="true" textButton="Adicionar Linha/Produto" urlButton="new-bundles" > {{ __('Linhas/Produtos Cadastrados') }} </x-title-component>

            <div id="BlocoLista" class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">

                <div id="bloco_info_details">
                    <div class="info_details_total">
                        Total Encontrados: <span>15</span>
                    </div>

                </div>
                <form id="filterForm">
                    <select name="sort_by">
                        <option value="bundle">Pacote</option>
                        <option value="bundle_id">Id do Pacote</option>
                        <option value="created_at">Data de Cadastro</option>
                    </select>
                    <select name="sort_order">
                        <option value="asc">Ascendente</option>
                        <option value="desc">Descendente</option>
                    </select>
                    <button type="submit">Filtrar</button>
                </form>

                <div id="userTable" class="tabela">
                    <div class="header_tabela">
                        <div style="width:50%;">Nome da Linha/Produto</div>
                        <div style="width:20%; text-align:center;">Data do Cadastro</div>
                        <div style="width:10%; text-align:center;">Ações</div>           
                    </div>
                    <div class="body_table">
       
                    </div>
                </div>

                <nav id="paginationLinks"></nav>

            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function toggleMenu() {
        const dropdownMenu = document.getElementById("dropdownMenu");
        dropdownMenu.classList.toggle("hidden");
    }

    document.addEventListener("click", function (event) {
        const dropdownMenu = document.getElementById("dropdownMenu");
        const isClickInside = event.target.closest('.relative');
        if (!isClickInside && !dropdownMenu.classList.contains("hidden")) {
            dropdownMenu.classList.add("hidden");
        }
    });
</script>


    <script>
        $(document).ready(function () {
            function fetchUsers(url = "{{ route('bundles.filter') }}") {
                $.ajax({
                    url: url,
                    method: 'GET',
                    data: $('#filterForm').serialize(),
                    success: function (response) {
                        // Atualizar tabela
                        let rows = '';
                        response.data.forEach(bundle => {
                            rows += `
                                <div class="listaTabela">
                                    <div style="width:50%;">${bundle.bundle}</div>
                                    <div style="width:20%; text-align:center;">${bundle.created_at}</div>
                                    <div style="width:10%; text-align:center; position:relative;">

                                    <x-dropdown class="text-gray-500">
                                        <x-slot name="trigger">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 cursor-pointer" style="margin:auto;" fill="none" viewBox="0 0 24 24" stroke="currentColor" onclick="toggleMenu()">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6h.01M12 12h.01M12 18h.01" />
                                            </svg>
                                        </x-slot>
                                        <div style="background-color: #f6f6f6; width: 140px; position: absolute; padding: 9px; right: 66px; bottom: -17px; border-radius: 10px;">
                                            <a href="/edit-bundle/${bundle.bundle_id}" style="width: 100%; text-align: center; text-transform: uppercase; font-weight: bold; font-size: 13px; margin: 6px;">Editar</a>
                                        
                                            <form action="bundles/remove/${bundle.bundle_id}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este produto?');" style="margin: 0px;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="width: 100%; text-align: center; text-transform: uppercase; font-weight: bold; font-size: 13px; margin: 6px;">Excluir</button>
                                            </form>
                                        </div>
                                    </x-dropdown>
                                    </div>
                                </div>
                            `;
                        });
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
                }
            });

            // Carregar lista inicial
            fetchUsers();
        });
    </script>
</body>
</html>
