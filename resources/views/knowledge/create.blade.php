<x-app-layout>
    <div class="py-4" style=" padding-bottom: 130px;">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">

            <div id="titleComponent_KnowledgeBase" style=" display: inherit; padding-top: 30px; display: inherit; min-height: 97px; height: auto; margin-bottom: -16px !important;" class="text-lg font-bold flex items-center justify-between w-full px-4 space-x-2 relative" >
                <div class="flex" style="width: 80%; float:left;">
                    <img src="{{ asset('icons/base_conhecimento.svg') }}" alt="Upload Icon" style="height: 33%; padding-right: 18px;">
                    <span style="margin-top:3px;">Nova Base de Conhecimento</span>
                </div>
                
                <a href="https://bw8-dev-totvs-rfp.s3.us-east-1.amazonaws.com/cdn/storage/modelo-base_conhecimento.xlsx" style="background-color: #5570F1; border-radius: 50px; color: white; padding: 8px 21px; font-size: 14px; font-weight: 600; margin: auto; float: right; margin-top: -4px;" download> 
                    <div style="">
                        <img src="{{ asset('icons/download_2.svg') }}" alt="Upload Icon" style="height: 18px; padding-right: 12px; float: left; margin-top: 5px;">
                        <span>Baixar planilha modelo</span>
                    </div>
                </a>

            </div>
            
            <meta name="csrf-token" content="{{ csrf_token() }}">

                <div class="bg-white rounded-lg shadow-md p-8 w-full flex flex-col" style=" margin-bottom: 100px; position:relative;">

                    <div class="loading" style="display:none; background: #ffffffcf; position: absolute; width: 100%; height: 100%; top: 0px; left: 0px;  z-index: 9;">
                        <div id="lottie-container" style="width: 100px; height:100px; position: absolute; top: 50%; left: 50%; transform: translate(-75px, -35px);"></div>

                    </div>


                    <div class="flex flex-column items-center justify-center">

                        <div style="padding-top: 10px"></div>

                        <!-- Name -->
                        <div style="width: 35%; margin-bottom: 25px;">
                            <x-input-label for="name" :value="__('Nome:')" />
                            <x-text-input id="name" class="formAddKnowledge" type="text" name="name" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Name -->
                        <div style="width: 35%; margin-bottom: 25px;">
                            <x-input-label for="data" :value="__('Data de Upload:')" />
                            <x-text-input id="data" class="formAddKnowledge" type="text" name="data" value="{{ now()->format('d/m/Y') }}" required autofocus autocomplete="name" disabled/>
                        </div>

                        <!-- Name -->
                        <div style="width: 35%; margin-bottom: 40px;">
                            <x-input-label for="responsavel" :value="__('Responsável:')" />
                            <x-text-input id="responsavel" class="formAddKnowledge" type="text" name="responsavel" value="{{Auth::user()->name}}" required autofocus autocomplete="name" disabled/>
                            <x-input-error :messages="$errors->get('responsavel')" class="mt-2" />
                        </div>



                        <div id="dropZone" class="border-2 border-gray-300 rounded-lg p-8 text-center mb-6 cursor-pointer" style="border-style: dashed; width: 48%; box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25); padding: 30px 0px;">
                            <img src="{{ asset('icons/upload-cloud.svg') }}" alt="Upload Icon"
                                class="h-12 w-12 mx-auto text-gray-400 mb-2">
                            <p class="text-gray-500 font-medium">Drop it like a pro!</p>
                            <p class="text-gray-400 text-sm">Carregue seu arquivo .xls ou .xlxs soltando-os nesta janela</p>
                        </div>
                        <ul id="fileList" style="width: 100%;" class="flex flex-column text-sm text-gray-600 mb-4 items-center justify-center"></ul>
                        <button id="uploadButton" class="px-4 py-2" style="background-color: #5570F1; width: 406px; height: 58px; border-radius: 10px; color: white; box-shadow: 0px 19px 34px -20px #43BBED;">Upload</button>
                        <input type="file" id="fileInput" style="display: none;"/>
                    </div>
                </div>

        </div>
    </div>
</x-app-layout>



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
                    let index = fileListGlobal.indexOf(file.name);
                    fileListGlobal = fileListGlobal.filter(element => element != file.name)
                    fileListGlobal.splice(0, fileListGlobal.length);
                    file = {};
                    fileItem.remove();
                    const fileInput = document.getElementById("fileInput");
                    fileInput.value = "";                    
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
                    if(!document.getElementById('name').value){
                        alert('Preencha o nome da Base de Conhecimento');
                        $("#name").css({
                            'border': '1px solid red',
                            'background': '#fdf4ef'
                        });
                       
                        return false;
                    }
                    
                    $('.loading').fadeIn(500);
                    
                    const formData = new FormData();
                    formData.append('name', document.getElementById('name').value);
                    formData.append('file', file);
                    fetch('/knowledge/add', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            fileListGlobal = [];
                            $('#fileList').empty();
                            $('#fileInput').val('');

                            if(data.success){
                                console.log('sucesso entrou');
                                window.location.replace(data.redirectUrl);
                            }else{
                                if(data.error){
                                    $('.loading').fadeOut(500);
                                    showAlertBootstrap("error", data.message, false);
                                }else{
                                    console.log('erro entrou');
                                    window.location.replace(data.redirectUrl);
                                }
                            }

                            console.log('Upload successful:', data);
                            
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