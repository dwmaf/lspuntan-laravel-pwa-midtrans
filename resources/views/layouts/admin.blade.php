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
    {{-- kustom styling buat trix --}}
    <style>
        trix-editor h1 {
            font-size: 2em !important;
            font-weight: bold !important;
            display: block !important;
            margin-top: 0.67em !important;
            margin-bottom: 0.67em !important;
        }

        trix-editor ul {
            list-style-type: disc !important;
            display: block !important;
            margin-left: 40px !important;
        }

        trix-editor li {
            display: list-item !important;
        }

        .trix-container {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background-color: white;
        }

        trix-toolbar {
            border-bottom: 1px solid #d1d5db !important;
            padding-top: 8px !important;
            padding-left: 4px !important;
            padding-right: 4px !important;
        }

        .trix-button-group {
            border: none !important;
        }

        .trix-button {
            display: inline-block;
            height: 24px !important;
            width: 28px !important;
            padding: 3px 5px !important;
            border: none !important;
            cursor: pointer;
            background-color: white;
        }


        trix-editor {
            border: none !important;
            font-family: "Gill Sans", sans-serif;
            padding: 12px !important;
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
