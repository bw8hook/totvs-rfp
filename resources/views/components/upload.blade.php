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

            var select = document.getElementById('totvs-erp').value;
            formData.append('totvs_erp', select);

            fetch('/upload-file', {
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
                    if(data.success){
                        console.log('sucesso entrou');
                        window.location.replace(data.redirectUrl);
                    }else{
                        console.log('erro entrou');
                        window.location.replace(data.redirectUrl);
                    }


                    console.log('Upload successful:', data);
                    //$("#sucesso_message").show().delay(3200).fadeOut(300);
                })
                .catch(error => {
                    //$("#erro_message").show().delay(3200).fadeOut(300);
                    console.log(error);
                    window.location.replace(data.redirectUrl);
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
                for (let file of fileInput.files) {
                    //if (file.type === "text/xml") {
                        fileListGlobal.push(file);
                    // } else {
                    //     alert("Please select an XML file.");
                    // }
                }
                updateFileListDisplay();
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
                for (let file of files) {
                    //if (file.type === "text/xml") {
                        fileListGlobal.push(file);
                    //} else {
                    //    alert("Please drop an XML file");
                    //}
                }
                updateFileListDisplay();
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

<div id="sucesso_message" class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md" role="alert" style="display:none; position: absolute; top: 10px; right: 10px;">
  <div class="flex">
    <div class="py-1"><svg class="fill-current h-6 w-6 text-teal-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg></div>
    <div>
      <p class="font-bold">Upload bem-sucedido!</p>
      <p class="text-sm">O seus arquivos foram enviado com sucesso.</p>
    </div>
  </div>
</div>

<div id="erro_message" class="bg-red-100 border-t-4 border-red-500 rounded-b text-red-900 px-4 py-3 shadow-md" role="alert" style="display:none; position: absolute; top: 10px; right: 10px;">
  <div class="flex">
    <div class="py-1"><svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg></div>
    <div>
      <p class="fonet-bold">Erro no Upload!</p>
      <p class="text-sm">Não foi possível ao subir seus arquivos, tente novamente.</p>
    </div>
  </div>
</div>


<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="bg-white rounded-lg shadow-md p-8 w-full" style=" margin-bottom: 100px; position:relative;">

    <div class="loading" style="display:none; background: #ffffffcf; position: absolute; width: 100%; height: 100%; top: 0px; left: 0px;">
        <img src="{{ asset('icons/loading.gif') }}" alt="Upload Icon" style="position: absolute; top: 50%; left: 50%; transform: translate(-75px, -35px);">
    </div>


    <div class="flex flex-column items-center justify-center">
        <div style="padding-top: 5%"></div>
        <div class="mb-6" style="width: 37%; height: 86px;">
            <label for="totvs-erp" class="block text-sm font-medium text-gray-700 mb-2">Selecione o Pacote</label>
            <select id="totvs-erp" name="totvs-erp">
                @foreach($ListBundles as $ListBundle)
                    <!-- Código que será executado para cada item -->
                    <option value="{{ $ListBundle['id'] }}">{{ $ListBundle['bundle'] }}</option>
                @endforeach
            </select>


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