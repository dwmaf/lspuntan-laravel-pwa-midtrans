<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Aplikasi berbasis web untuk Lembaga Sertifikasi Profesi (LSP) Universitas Tanjungpura yang melayani pendaftaran, manajemen data asesi, dan informasi jadwal sertifikasi.">
    <title>{{ config('app.name', 'LSP UNTAN') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @routes
    @laravelPWA
    @inertiaHead
</head>
<style>
    body {
        background-color: #f3f4f6; /* bg-gray-100 Tailwind */
    }
    @media (prefers-color-scheme: dark) {
        body {
            background-color: #111827; /* dark:bg-gray-900 Tailwind */
        }
        .app-loader .spinner {
            border-color: #374151 !important; /* dark:border-gray-700 */
            border-top-color: #3b82f6 !important; /* dark:border-blue-500 */
        }
    }

    /* CSS murni untuk Preloader Spinner sebelum Vue aktif */
    .app-loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        background-color: inherit;
        transition: opacity 0.5s ease, visibility 0.5s ease;
    }
    .app-loader .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #e5e7eb; /* border-gray-200 */
        border-top: 4px solid #2563eb; /* border-blue-600 */
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .min-h-12 {
        min-height: 3rem;
    }
</style>

<body class="font-sans antialiased">
    <div class="app-loader" id="preloader">
        <div class="spinner"></div>
    </div>
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

    window.addEventListener('load', function () {
        const loader = document.getElementById('preloader');
        if (loader) {
            loader.style.opacity = '0';
            loader.style.visibility = 'hidden';
            setTimeout(() => loader.remove(), 500); // hilangkan dari html setelah transisi
        }
    });
</script>
</body>

</html>
