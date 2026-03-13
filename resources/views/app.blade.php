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
    <script>
    // Tangkap pesan telepati dari Service Worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.addEventListener('message', function(event) {
            // Cek apakah pesannya adalah perintah pindah halaman
            if (event.data && event.data.action === 'NAVIGATE_FROM_NOTIF') {
                console.log('Dapat perintah pindah URL dari SW:', event.data.url);
                // Eksekusi pindah halaman dari sisi Front-End!
                window.location.href = event.data.url;
            }
        });
    }
</script>
</body>

</html>
