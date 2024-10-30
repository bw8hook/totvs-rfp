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
            const formData = new FormData();
            formData.append('file', file);

            fetch('/upload', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    console.log('Upload successful:', data);
                })
                .catch(error => {
                    console.error('Error:', error);
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
                    if (file.type === "text/xml") {
                        fileListGlobal.push(file);
                    } else {
                        alert("Please select an XML file.");
                    }
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
                    if (file.type === "text/xml") {
                        fileListGlobal.push(file);
                    } else {
                        alert("Please drop an XML file");
                    }
                }
                updateFileListDisplay();
            }
        });

        uploadButton.addEventListener('click', () => {
            if (fileListGlobal.length > 0) {
                uploadFiles();
                fileListGlobal = []
                updateFileListDisplay()
            } else {
                alert("No files to upload.");
            }
        });
    });
</script>

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="bg-white rounded-lg shadow-md p-8 w-full" style="height: 64vh;">
    <div class="flex flex-column items-center justify-center" style="">
        <div style="padding-top: 5%"></div>
        <div class="mb-6" style="width: 37%; height: 86px;">
            <label for="project-name" class="block text-sm font-medium text-gray-700 mb-2">Nome do Projeto</label>
            <input type="text" id="project-name"
                class="w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Digite o nome do projeto">
        </div>
        <div style="padding-top: 10px"></div>
        <div id="dropZone" class="border-2 border-gray-300 rounded-lg p-8 text-center mb-6 cursor-pointer"
            style="border-style: dashed; width: 48%; box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);">
            <img src="{{ asset('icons/upload-cloud.svg') }}" alt="Upload Icon"
                class="h-12 w-12 mx-auto text-gray-400 mb-2">
            <p class="text-gray-500 font-medium">Drop it like a pro!</p>
            <p class="text-gray-400 text-sm">Carregue arquivos .xml soltando-os nesta janela</p>
        </div>
        <ul id="fileList" style="width: 100%;" class="flex flex-column text-sm text-gray-600 mb-4 items-center justify-center"></ul>
        <button id="uploadButton" class="px-4 py-2" style="background-color: #5570F1; width: 406px; height: 58px; border-radius: 10px; color: white; box-shadow: 0px 19px 34px -20px #43BBED;">Upload</button>
        <input type="file" id="fileInput" style="display: none;" accept=".xml">
    </div>
</div>