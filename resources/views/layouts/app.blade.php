<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'RFPs') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

        <link rel="icon" href="{{ asset('/storage/favicon.png') }}" type="image/png">
        <link rel="icon" href="{{ asset('/storage/favicon.svg') }}" type="image/svg+xml">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased flex" style="overflow: hidden;">
    
    @if (session('success'))
        <div id="success-alert" class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md" role="alert" style="position: absolute; top: 10px; right: 10px; z-index:9;">
            <div class="flex">
                <div class="py-1"><svg class="fill-current h-6 w-6 text-teal-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg></div>
                <div>
                <p class="font-bold">Sucesso!</p>
                <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div id="error-alert" class="bg-red-100 border-t-4 border-red-500 rounded-b text-red-900 px-4 py-3 shadow-md" role="alert" style="position: absolute; top: 10px; right: 10px; z-index:9;">
            <div class="flex">
                <div class="py-1"><svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg></div>
                <div>
                <p class="fonet-bold">Ooops!</p>
                <p class="text-sm"> {{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    @include('layouts.navigation')

    <div class="h-screen w-full" style="background-color: #F8F8F8; overflow:auto;">
        @if (isset($header))
            <header>

             <!-- <header class="bg-white shadow"> -->
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8" style="display: flex; justify-content: flex-end; align-items: center; width: 100%; margin-right: 30px;">
                    <!-- {{ $header }} -->
                    
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <img src="{{ asset('icons/bell.svg') }}"/>
                        <img src="{{ asset('icons/moon.svg') }}"/>
                        <img src="{{ asset('icons/info.svg') }}"/>
                        <span style="color: #333;">{{ Auth::user()->name }}</span>
                        <div style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden; border: 1px solid #ccc;">
                            <img src="{{ asset('icons/default-photo.png') }}"/>
                        </div>
                        <img src="{{ asset('icons/logout.svg') }}"/>
                    </div>


                </div>
            </header>
        @endif
        <div class="h-screen w-full">
            {{ $slot }}
        </div>

    </div>
    
    <script>
    // Espera 5 segundos (5000 milissegundos) antes de ocultar a mensagem de sucesso
    document.addEventListener('DOMContentLoaded', function() {
        const successAlert = document.getElementById('success-alert');
        const errorAlert = document.getElementById('error-alert');

        if (successAlert) {
            setTimeout(() => {
                successAlert.style.transition = 'opacity 0.5s';
                successAlert.style.opacity = 0;
                setTimeout(() => successAlert.remove(), 500); // Remove o elemento após a transição
            }, 5000); // 5000 milissegundos = 5 segundos
        }

        if (errorAlert) {
            setTimeout(() => {
                errorAlert.style.transition = 'opacity 0.5s';
                errorAlert.style.opacity = 0;
                setTimeout(() => errorAlert.remove(), 500);
            }, 5000);
        }
    });
</script>


</body>
</html>