<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    @include('layouts.asesi-sertifikasi-menu')
    <div class="p-6 bg-white dark:bg-gray-800 rounded-tr-lg rounded-bl-lg rounded-br-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 sm:mb-0">
            Asesmen {{ $sertification->skema->nama_skema }}
        </h3>
        <div class="text-gray-900 dark:text-gray-100 mt-4">
            @if ($sertification->deskripsi_tugas_asesmen)
                {!! html_entity_decode($sertification->deskripsi_tugas_asesmen) !!}
            @else
                <p class="text-gray-500 dark:text-gray-400">
                    Asesor belum mengunggah deskripsi tugas untuk asesmen. Silakan periksa kembali nanti.
                </p>
            @endif
        </div>
    </div>
</x-app-layout>
