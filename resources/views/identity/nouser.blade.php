<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class="font-sans text-gray-900 antialiased backgroundLogin">
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div class="w-full max-w-2xl mt-6 px-10 py-6 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <div class="flex flex-col sm:justify-center items-center">
            <a href="/">
                <x-icon-status type="attention"></x-icon-status>
            </a>
        </div>

        <div class="flex flex-col sm:justify-center items-center">
            <p class="font-bold text-4xl mt-4">Atenção!</p>

            <p class="text-center text-xl mt-4" style="color: #525B75;">
                Notamos que você não possui acesso ao sistema SmartRFP.</p>
{{--            <p class="text-center text-xl" style="color: #525B75;">Solicite o acesso clicando no botão:</p>--}}
        </div>
    </div>
</div>
</body>
</html>
