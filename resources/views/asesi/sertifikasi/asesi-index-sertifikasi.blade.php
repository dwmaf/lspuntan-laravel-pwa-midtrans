<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($sertifications as $sert)
                @php
                    $filteredAsesi = $asesi->get($sert->id);
                    $sudahDaftar = !is_null($filteredAsesi);
                @endphp

                <div class="bg-white p-6 rounded-lg dark:bg-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">{{ $sert->skema->nama_skema }}
                    </h3>

                    <div class="flex items-center mt-4">
                        <x-bxs-calendar class="w-4 text-gray-700 dark:text-gray-200" />
                        <p class="ml-2 text-gray-600 text-sm dark:text-gray-200">Pendaftaran Dibuka:
                            {{ $sert->tgl_apply_dibuka }}</p>
                    </div>
                    <div class="flex items-center mt-2">
                        <x-bxs-calendar-event class="w-4 text-gray-700 dark:text-gray-200" />
                        <p class="ml-2 text-gray-600 text-sm dark:text-gray-200">Ditutup: {{ $sert->tgl_apply_ditutup }}
                        </p>
                    </div>

                    <div class="mt-4">
                        @if ($sudahDaftar)
                            <a href="{{ route('show_applied_sertifikasi', [$sert->id, $filteredAsesi->id]) }}"
                                class="inline-block px-4 py-2 bg-blue-800 text-white rounded-md hover:bg-blue-900">Lihat
                                Status</a>
                        @else
                            <a href="{{ route('apply_sertifikasi', $sert->id) }}"
                                class="inline-block px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Daftar</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
