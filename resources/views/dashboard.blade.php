<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>

        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Agente') }}
        </h2>

    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!DOCTYPE html>
                    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

                    <head>
                        <meta charset="utf-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1">
                        <title>File uploader</title>
                        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
                            rel="stylesheet"
                            integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65"
                            crossorigin="anonymous">
                    </head>

                    <body class="antialiased">
                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    @if ($message = Session::get('success'))
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @endif
                                    @if (count($errors) > 0)
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                            <ul class="mb-0 p-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-12 py-5">
                                    <div class="card my-5">
                                        <div class="card-header">
                                            <h3>Laravel File Uploader</h3>
                                        </div>
                                        <div class="card-body">
                                            <form method="POST" action="{{ route('files.store')  }}"
                                                enctype="multipart/form-data">
                                                @method('POST')
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="formFileLg" class="form-label">File input
                                                        example</label>
                                                    <input name="file" class="form-control form-control-lg"
                                                        id="formFileLg" type="file">
                                                </div>
                                                <div class="mb-3">
                                                    <button type="submit" value="submit"
                                                        class="btn btn-primary">Upload</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
                            integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
                            crossorigin="anonymous"></script>
                    </body>

                    </html>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>