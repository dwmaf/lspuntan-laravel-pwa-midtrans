<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LSP UNTAN') }}</title>

        {{-- <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script> --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <link rel="icon" href="{{ asset('logo-lsp.png') }}" type="image/png">
        {{-- @vite(['resources/js/app.js']) --}}
    </head>
    <style>
        .min-h-12{
            min-height: 3rem;
        }
    </style>
    <body class="font-sans antialiased">
        <div class="flex min-h-screen bg-gray-100 dark:bg-gray-900">
            {{-- Sidebar untuk Desktop (md dan lebih besar) --}}
            <div class="hidden md:flex">
                @include('layouts.navigation')
            </div>

            <div class="flex-1 flex flex-col">
                <div class="md:hidden mb-2">
                    @include('layouts.top-navigation')
                </div>
                @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow-sm p-4 m-2">
                    {{ $header }}
                </header>
                @endisset

                <main class="p-2">
                    {{ $slot }}
                </main>
            </div>

        </div>
        
    </body>
</html>