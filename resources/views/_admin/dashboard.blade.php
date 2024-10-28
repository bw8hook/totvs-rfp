<x-app-layout>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

    <x-slot name="header">
        <div style="display: flow-root;">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight" style="margin-top: 4px; float: left; font-size: 25px;">
                {{ __('Arquivos') }}
            </h2>


            

            <a href="/update-ia" >
                <button type="submit" value="submit" class="btn btn-primary" style="float: right;">Atualizar Base de Conhecimento</button>
            </a>

            <a href="https://widget.meuassistente.rdstationmentoria.com.br/wpc_01H44Y2BQJQWSB7WDMHG7NCMNV/{{$AgenteId}}" target="_blank">
                <button type="submit" value="submit" class="btn btn-success" style="float: right; margin-right:20px;">Abrir Agente</button>
            </a>
           
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                <div class="col-12">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            <strong>{{ $message }}</strong>
                        </div>
                    @endif

                    @if ($message = Session::get('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            <strong>{{ $message }}</strong>
                        </div>
                    @endif
                    
                    @if (count($errors) > 0)
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            <ul class="mb-0 p-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="col-12">
                    <div class="card my-5">
                    
                        <div class="card-body">
                            <form method="POST" action="{{ route('files.store')  }}" enctype="multipart/form-data">
                                @method('POST')
                                @csrf
                                <div class="mb-3">
                                    <label for="formFileLg" class="form-label">Enviar Arquivo</label>
                                    <input name="file" class="form-control form-control-lg" id="formFileLg"
                                           type="file">
                                </div>
                                <div class="mb-3">
                                    <button type="submit" value="submit" class="btn btn-primary">Upload</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>




                
                    <div class="col-12">
                        <div class="card ">
                            <div class="card-body">

                            <table class="table">
                                <thead>
                                    <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Arquivo</th>
    
                                    <th scope="col">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($files as $file)
                                        <tr>
                                            <th scope="row">{{$file['id']}}</th>
                                            <td>{{$file['titulo']}}</td>
                                            <td>
                                                <a href="/delete-file/{{$file['link']}}" style="margin-left:8px">
                                                    <button type="button" class="btn btn-outline-danger">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                                            <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"></path>
                                                        </svg>
                                                    </button></a>

                                                <a href="{{$file['link_download']}}" download="" style="margin-left:8px">
                                                <button type="button" class="btn btn-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
                                                        <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"></path>
                                                        <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"></path>
                                                    </svg>
                                                </button>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>




                                
                            </div>
                        </div>
                        </div>
            
       



                </div>
            </div>
        </div>
    </div>
</x-app-layout>
