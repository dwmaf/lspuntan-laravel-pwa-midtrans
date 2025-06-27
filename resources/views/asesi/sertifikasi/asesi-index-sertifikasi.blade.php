<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            @foreach ($sertifications as $sert)
                @php
                    $filteredAsesi = $asesi->get($sert->id);
                    $sudahDaftar = !is_null($filteredAsesi);
                    $pendaftaranDitutup = \Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($sert->tgl_apply_ditutup));
                    $pendaftaranDibuka = \Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($sert->tgl_apply_dibuka));
                @endphp

                <div class="bg-white p-6 rounded-lg dark:bg-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200 flex items-center">
                        {{ $sert->skema->nama_skema }}
                        @if (!$sudahDaftar && $pendaftaranDitutup)
                            <span
                                class="ml-3 px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-700 dark:bg-red-700 dark:text-red-100">
                                Ditutup
                            </span>
                        @elseif (!$sudahDaftar && $pendaftaranDibuka && !$pendaftaranDitutup)
                            <span
                                class="ml-3 px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-700 dark:bg-green-700 dark:text-green-100">
                                Dibuka
                            </span>
                        @endif
                    </h3>

                    <div class="flex items-center mt-4">
                        <x-bxs-calendar class="w-4 text-gray-700 dark:text-gray-200" />
                        <p class="ml-2 text-gray-600 text-sm dark:text-gray-200">
                            Pendaftaran:
                            {{ \Carbon\Carbon::parse($sert->tgl_apply_dibuka)->format('d M Y') }}
                            &ndash;
                            {{ \Carbon\Carbon::parse($sert->tgl_apply_ditutup)->format('d M Y') }}
                        </p>
                    </div>
                    {{-- <div class="text-gray-600 text-sm dark:text-gray-200">
                        {{ $pendaftaranDibuka }}
                        Today:
                        {{ \Carbon\Carbon::now() }}
                    </div> --}}
                    <div class="flex items-center mt-4">
                        <x-tni-money class="w-4 text-gray-700 dark:text-gray-200" />
                        <p class="ml-2 text-gray-600 text-sm dark:text-gray-200">
                            Biaya: Rp{{ number_format($sert->harga, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="mt-4">
                        @if ($sudahDaftar)
                            <a href="{{ route('asesi.applied.show', [$sert->id, $filteredAsesi->id]) }}"
                                class="self-start font-medium bg-blue-500 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-700 cursor-pointer">
                                Lihat Status
                            </a>
                        @elseif ($pendaftaranDitutup)
                            <span
                                class="inline-block font-medium bg-gray-400 text-white px-4 py-2 rounded-lg cursor-not-allowed opacity-70">
                                Pendaftaran Ditutup
                            </span>
                        @elseif (!$pendaftaranDibuka)
                            <span
                                class="inline-block font-medium bg-yellow-400 text-white px-4 py-2 rounded-lg cursor-not-allowed opacity-70">
                                Belum Dibuka
                            </span>
                        @else
                            <a href="{{ route('asesi.certifications.apply.create', $sert->id) }}"
                                class="self-start font-medium bg-blue-500 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-700 cursor-pointer">
                                Daftar
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
