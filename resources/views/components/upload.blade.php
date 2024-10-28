<script>
    function upload(files) {
        const formData = new FormData();
        formData.append('file', files[0]);

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
    }

    document.addEventListener('DOMContentLoaded', () => {
        const fileInput = document.getElementById('fileInput');
        const dropZone = document.getElementById('dropZone');

        dropZone.addEventListener('click', () => {
            fileInput.click();
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0 && fileInput.files[0].type === "text/xml") {
                upload(fileInput.files);
            } else {
                alert("Please select an XML file.");
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

            console.log(e.dataTransfer, e)
            const files = e.dataTransfer.files;
            if (files.length > 0 && files[0].type === "text/xml") {
                upload(files);
            } else {
                alert("Please drop an XML file bruh");
            }
        });
    });
</script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="bg-white rounded-lg shadow-md p-8 w-full" style="height: 64vh;">
    <div class="flex flex-column items-center justify-center">
        <div class="mb-6" style="width: 37%; height: 86px;">
            <label for="project-name" class="block text-sm font-medium text-gray-700 mb-2">Nome do Projeto</label>
            <input type="text" id="project-name"
                class="w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Digite o nome do projeto">
        </div>
        <div id="dropZone"
            class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center mb-6 cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-2" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16l4-4 4 4m0-4l4 4m0 0l4-4m-4 4V4" />
            </svg>
            <p class="text-gray-500 font-medium">Drop it like a pro!</p>
            <p class="text-gray-400 text-sm">Click or drag .xml files here to upload</p>
        </div>
        <input type="file" id="fileInput" style="display: none;" accept=".xml">
    </div>
</div>