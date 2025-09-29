{{-- filepath: d:\Laravel-App\lsp-untan-laravel-pwa\resources\views\vendor\laravelpwa\offline.blade.php --}}
@extends($layout)

@section('content')
<div class="flex flex-col items-center justify-center h-full text-center text-gray-700 dark:text-gray-300">
    <svg class="w-16 h-16 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m-12.728 0a9 9 0 010-12.728m12.728 0L5.636 18.364m12.728 0L5.636 5.636"></path></svg>
    <h1 class="text-2xl font-bold mb-2">Anda Sedang Offline</h1>
    <p class="text-lg">Koneksi internet tidak tersedia. Halaman ini tidak dapat dimuat.</p>
    <p class="mt-2 text-sm">Silakan periksa koneksi Anda dan coba lagi.</p>
</div>
@endsection

{{-- Untuk layout admin/app yang memiliki header, kita bisa menyuntikkan judul --}}
@isset($header)
    @section('header')
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Offline
        </h2>
    @endsection
@endisset