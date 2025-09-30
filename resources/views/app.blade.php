<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LSP UNTAN') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @routes
    @laravelPWA
    @inertiaHead
</head>
<style>
    .min-h-12 {
        min-height: 3rem;
    }
</style>

<body class="font-sans antialiased">
    @inertia
</body>

</html>
