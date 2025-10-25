<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <style>
        body { font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"; background-color: #f3f4f6; color: #1f2937; }
        .dark body { background-color: #111827; color: #d1d5db; }
        .container { min-height: 100vh; display: flex; flex-direction: column; justify-content: center; align-items: center; padding-top: 1.5rem; }
        .card { width: 100%; max-width: 28rem; margin-top: 1.5rem; padding: 1.5rem; background-color: #fff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); overflow: hidden; border-radius: 0.5rem; }
        .dark .card { background-color: #1f2937; }
        .content { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; text-align: center; }
        .icon { width: 4rem; height: 4rem; margin-bottom: 1rem; color: #9ca3af; }
        h1 { font-size: 1.5rem; line-height: 2rem; font-weight: 700; margin-bottom: 0.5rem; }
        p { font-size: 1.125rem; line-height: 1.75rem; }
        .small-text { margin-top: 0.5rem; font-size: 0.875rem; line-height: 1.25rem; }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="content">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18.364 5.636a9 9 0 010 12.728m-12.728 0a9 9 0 010-12.728m12.728 0L5.636 18.364m12.728 0L5.636 5.636">
                    </path>
                </svg>
                <h1>Anda Sedang Offline</h1>
                <p>Koneksi internet tidak tersedia.</p>
                <p class="small-text">Silakan periksa koneksi Anda dan coba lagi.</p>
            </div>
        </div>
    </div>

</body>

</html>
