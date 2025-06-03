<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    @include('layouts.asesi-sertifikasi-menu')
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <h4 class="inline-block  text-gray-800 dark:text-white px-4 py-2 rounded-lg transition">
            Praasesmen {{ $sertification->skema->nama_skema }}</h4>
        {!! html_entity_decode($sertification->rincian_praasesmen) !!}
    </div>
</x-app-layout>
