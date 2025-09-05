<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- <title>{{ config('app.name', 'Laravel') }}</title> --}}

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col bg-blue-100">
            {{-- Header --}}
            @include('layouts.headerNews')
        
            {{-- Top Menu --}}
            @if (request()->routeIs('index'))
                @include('layouts.top-menuNews')
            @endif
        
            {{-- Main Content --}}
            <main class="flex-grow p-4 space-y-4">
                @yield('content')
            </main>
        
            {{-- Bottom Nav --}}
            @include('layouts.bottom-navNews')

            <footer class="bg-white shadow mt-6">
                
            </footer>
        </div>
    </body>
</html>
