<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    @include('layouts.admin-sertifikasi-menu')
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <div class="flex gap-3">
            <a class="inline-flex items-center px-2 py-2 bg-gray-800 dark:bg-gray-200 rounded-md text-white dark:text-gray-800 hover:bg-gray-700 dark:hover:bg-white cursor-pointer"
                href="/sertification/{{ $sertification[0]->id }}/edit">
                <x-fas-edit class="w-5" /></a>
            <form action="/sertification/{{ $sertification[0]->id }}" method="post">
                @method('delete')
                @csrf
                <button
                    class="inline-flex items-center px-2 py-2 bg-gray-800 dark:bg-gray-200 rounded-md text-white dark:text-gray-800 hover:bg-gray-700 dark:hover:bg-white cursor-pointer"
                    onclick="return confirm('Hapus data {{ $sertification[0]->id }}?')"><x-bxs-trash class="w-5" /></button>
            </form>
        </div>
        <h5>{{ $sertification[0]->skema->nama_skema }}</h5>
    </div>
</x-admin-layout>
