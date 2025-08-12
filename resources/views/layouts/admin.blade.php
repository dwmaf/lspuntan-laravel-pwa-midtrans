<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LSP UNTAN') }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="{{ asset('logo-lsp.png') }}" type="image/png">
    <style>
        ul {
            list-style-type: disc;
            /* dari 'list-disc' */
            list-style-position: outside;
            /* dari 'list-outside' */
            padding-left: 2.5rem;
            /* dari 'pl-10' */
            padding-top: 1rem;
            /* dari 'py-4' */
            padding-bottom: 1rem;
            /* dari 'py-4' */
        }
    </style>
</head>

<body class="font-sans antialiased">
    <div class="flex min-h-screen bg-gray-100 dark:bg-gray-900">
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
                    <a href="{{-- route ke halaman notifikasi --}}" class="relative">
                        <span>Notifikasi</span>
                        @if (isset($unreadNotifications) && $unreadNotifications->count() > 0)
                            <span
                                class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                                {{ $unreadNotifications->count() }}
                            </span>
                        @endif
                    </a>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="p-2">
                {{ $slot }}
            </main>
        </div>

    </div>
    @stack('scripts')
    @stack('scripts-asesmen')
</body>

</html>
