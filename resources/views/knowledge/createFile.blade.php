<x-app-layout>
    <div class="flex flex-col">
        <div class="profile-bar">
            <x-profile-bar></x-profile-bar>
        </div>

        <script>
            const userId = @json($userId);
            document.addEventListener('DOMContentLoaded', () => {
                console.log(userId);
            });
        </script>


<script>
    let fileListGlobal = [];

    function addFileItem(file) {
        const fileItem = document.createElement('div');
        fileItem.style.display = 'flex';
        fileItem.style.alignItems = 'center';
        fileItem.style.justifyContent = 'space-between';
        fileItem.style.width = '90%';
        fileItem.style.maxWidth = '500px';
        fileItem.style.padding = '8px 12px';
        fileItem.style.border = '1px solid #ccc';
        fileItem.style.borderRadius = '5px';
        fileItem.style.backgroundColor = '#fff';
        fileItem.style.marginBottom = '8px';

        const fileName = document.createElement('span');
        fileName.textContent = file.name;
        fileName.style.color = '#666';
        fileName.style.fontSize = '14px';

        const deleteIcon = document.createElement('div');
        deleteIcon.style.display = 'flex';
        deleteIcon.style.alignItems = 'center';
        deleteIcon.style.justifyContent = 'center';
        deleteIcon.style.cursor = 'pointer';
        deleteIcon.innerHTML = '<img src="{{ asset('icons/trashbin.svg') }}" alt="Upload Icon">';
        deleteIcon.onclick = () => {
            console.log(fileListGlobal, file)
            fileListGlobal = fileListGlobal.filter(element => element != file.name)
            fileItem.remove();
            console.log(fileListGlobal, file)
        };

        fileItem.appendChild(fileName);
        fileItem.appendChild(deleteIcon);

        const fileList = document.getElementById('fileList');
        fileList.appendChild(fileItem);
    }

    function updateFileListDisplay() {
        const fileListDisplay = document.getElementById('fileList');
        fileListDisplay.innerHTML = '';
        fileListGlobal.forEach((file, index) => {
            const exampleFile = { name: file.name };
            addFileItem(exampleFile);
        });
    }

    function uploadFiles() {
        fileListGlobal.forEach(file => {
            $('.loading').fadeIn(500);
            const formData = new FormData();
            formData.append('file', file);
            fetch('/upload-file', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    console.log('sucesso cheogu');

                    if(data.success){
                        console.log('sucesso entrou');
                        window.location.replace(data.redirectUrl);
                    }else{
                        console.log('erro entrou');
                        window.location.replace(data.redirectUrl);
                    }

                    console.log('sucesso passou ');
                    fileListGlobal = [];
                    $('#fileList').empty();
                    console.log('Upload successful:', data);
                    $("#sucesso_message").show().delay(3200).fadeOut(300);
                })
                .catch(error => {
                    console.log('erro entrou');
                    //window.location.replace(error.redirectUrl);
                    ///$("#erro_message").show().delay(3200).fadeOut(300);
                    console.log(error);
                });
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const fileInput = document.getElementById('fileInput');
        const dropZone = document.getElementById('dropZone');
        const uploadButton = document.getElementById('uploadButton');

        dropZone.addEventListener('click', () => {
            fileInput.click();
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                if(fileListGlobal.length < 1){
                    for (let file of fileInput.files) {
                        if (file.type === "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" || file.type === "application/vnd.ms-excel" || file.type === "application/vnd.openxmlformats-officedocument.spreadsheetml.template") {
                            //"text/csv"
                            fileListGlobal.push(file);
                        } else {
                            alert("Apenas arquivo no formato XLS ou XLXS");
                        }
                    } 
                    updateFileListDisplay();
                }else{
                    alert("Neste modelo é possível subir apenas um arquivo por vez!.");
                }
            }
        });

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-blue-500');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-blue-500');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-blue-500');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                if(fileListGlobal.length < 1){
                    for (let file of files) {
                        console.log(file);
                        if (file.type === "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" || file.type === "application/vnd.ms-excel" || file.type === "application/vnd.openxmlformats-officedocument.spreadsheetml.template") {
                            //"text/csv"
                            fileListGlobal.push(file);
                        } else {
                            alert("Apenas arquivo no formato XLS ou XLXS");
                        }
                    }
                    updateFileListDisplay();
                }else{
                    alert("Neste modelo é possível subir apenas um arquivo por vez!.");
                }
            }
        });

        uploadButton.addEventListener('click', () => {
            if (fileListGlobal.length > 0) {
                uploadFiles();
                
                updateFileListDisplay()
            } else {
                alert("Nenhum arquivo enviado.");
            }
        });



    });
</script>

        
        <div class="w-full max-w-md mx-auto justify-center items-center flex flex-column" style="width: 85vw; min-height: 73vh; max-width: 90%;">

                <x-title-component :showButton="false"> Enviar RFP Preparada </x-title-component>

                <div style="padding: 2px;"></div>
            
                <meta name="csrf-token" content="{{ csrf_token() }}">

                <div class="bg-white rounded-lg shadow-md p-8 w-full flex flex-col" style=" margin-bottom: 100px; position:relative;">

                    <div class="loading" style="display:none; background: #ffffffcf; position: absolute; width: 100%; height: 100%; top: 0px; left: 0px;">
                        <img src="{{ asset('icons/loading.gif') }}" alt="Upload Icon" style="position: absolute; top: 50%; left: 50%; transform: translate(-75px, -35px);">
                    </div>


                    <div class="flex flex-column items-center justify-center">
                        <div style="padding-top: 10px;"></div>
                        
                        <div class="mb-6" style="">
                            <div class="mb-6" style="align-self: baseline; padding-bottom: 20px; width: 550px; text-align: center;">
                                <p style="color: #9a9aa9; font-size: 14px; margin-bottom: -30px;">Utilize a planilha de modelo para garantir que os dados estejam no formato correto e sejam importados sem erros. Certifique-se de preencher o modelo antes de realizar o envio.</p>
                            </div>

                            <div class="mb-6" style="align-self: end; padding-bottom: 20px; display:flex;">
                                <a href="/storage/Planilha Modelo - Carga de RFP's.xlsx" style="background-color: #9a9aa9; border-radius: 10px; color: white; box-shadow: 0px 19px 34px -20px #43BBED; padding: 15px 25px; text-transform: uppercase; font-size: 14px; font-weight: bolder; letter-spacing: 1px; margin:auto;" download> Baixar Planilha MODELO</a>
                                <a href="/storage/lista-pacotes.xlsx" style="background-color: #5570f1;border-radius: 10px; color: white;box-shadow: 0px 19px 34px -20px #43BBED; padding: 15px 25px; text-transform: uppercase; font-size: 14px; font-weight: bolder; letter-spacing: 1px; margin: auto; margin-left: 20px;" download> Baixar Lista de Pacotes</a>
                            </div>
                        </div>

                        <div style="padding-top: 10px"></div>
                        <div id="dropZone" class="border-2 border-gray-300 rounded-lg p-8 text-center mb-6 cursor-pointer" style="border-style: dashed; width: 48%; box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25); padding: 30px 0px;">
                            <img src="{{ asset('icons/upload-cloud.svg') }}" alt="Upload Icon"
                                class="h-12 w-12 mx-auto text-gray-400 mb-2">
                            <p class="text-gray-500 font-medium">Drop it like a pro!</p>
                            <p class="text-gray-400 text-sm">Carregue arquivos .xls ou .xlxs soltando-os nesta janela</p>
                        </div>
                        <ul id="fileList" style="width: 100%;" class="flex flex-column text-sm text-gray-600 mb-4 items-center justify-center"></ul>
                        <button id="uploadButton" class="px-4 py-2" style="background-color: #5570F1; width: 406px; height: 58px; border-radius: 10px; color: white; box-shadow: 0px 19px 34px -20px #43BBED;">Upload</button>
                        <input type="file" id="fileInput" style="display: none;"/>
                    </div>
                </div>

        </div>
    </div>
</x-app-layout>