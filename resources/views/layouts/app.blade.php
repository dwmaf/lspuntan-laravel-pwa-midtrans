<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LSP UNTAN') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        {{-- <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script> --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        {{-- @vite(['resources/js/app.js']) --}}
    </head>
    <style>
        .min-h-12{
            min-height: 3rem;
        }
    </style>
    <body class="font-sans antialiased">
        <div class="flex min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <div class="flex-1 flex flex-col p-2">

                @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow-sm p-4 mb-2 ml-2">
                    {{ $header }}
                    
                </header>
                @endisset

                <main class="ml-2">
                    {{ $slot }}
                </main>
            </div>

        </div>
    </body>
</html>


{{-- <!DOCTYPE html>
<!-- Claude Version -->
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        @include('layouts.claudenavigation')

        
        <div class="lg:ml-64 transition-all duration-300">
            
            @isset($header)
            <header class="bg-white dark:bg-gray-800 shadow-sm p-6 mb-6">
                {{ $header }}
            </header>
            @endisset

            
            <main class="p-6">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html> --}}