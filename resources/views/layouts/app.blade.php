<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased flex" style="overflow: hidden;">
    
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
    
</body>
</html>