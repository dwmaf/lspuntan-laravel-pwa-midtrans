<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    
    <div class="max-w-7xl mx-auto mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($sertifications as $sert)
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-gray-900">{{ $sert->skema->nama_skema }}</h3>
                <p class="text-gray-600 mt-2">Tanggal Pendaftaran Dibuka : {{ $sert->tgl_apply_dibuka }}</p>
                <a href="{{ route('apply_sertifikasi',$sert->id) }}">Daftar</a>
            </div>
            @endforeach
        </div>
    </div>
    
    



</x-app-layout>
