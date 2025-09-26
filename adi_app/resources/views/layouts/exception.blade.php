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
        
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
            
        <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('images/apple-icon-57x57.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-icon-180x180.png') }}">

        <link rel="manifest" href="{{asset('manifest.json')}}">
        <meta name="theme-color" content="#ffffff">
        <!-- Scripts -->
        @php
            $isProduction = app()->environment('production');
            $manifestPath = $isProduction ? '../public_html/build/manifest.json' : public_path('build/manifest.json');
        @endphp

        @if ($isProduction && file_exists($manifestPath))
            @php
                $manifest = json_decode(file_get_contents($manifestPath), true);
            @endphp
            <link rel="stylesheet" href="{{ config('app.url') }}/build/{{ $manifest['resources/css/app.css']['file'] }}">
            <script type="module" src="{{ config('app.url') }}/build/{{ $manifest['resources/js/app.js']['file'] }}"></script>
        @else
            @viteReactRefresh
            @vite(['resources/js/app.js', 'resources/css/app.css'])
        @endif

    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col bg-blue-100">
            {{-- Header --}}
            @include('layouts.headerNews')
        
            {{-- Main Content --}}
            <main class="flex-grow p-4 space-y-4">
                @yield('content')
            </main>
        
            {{-- Bottom Nav --}}
            @include('layouts.bottom-navNews')

            <footer class="bg-white shadow mt-6">
                <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center text-sm text-gray-500">
                    <span>© {{ date('Y') }} Mycdc. All rights reserved.</span>
                </div>
            </footer>
        </div>
    </body>
</html>