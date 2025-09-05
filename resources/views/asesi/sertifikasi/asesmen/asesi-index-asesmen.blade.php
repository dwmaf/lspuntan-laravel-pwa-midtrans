<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 dark:bg-green-700 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-100 rounded-md"
            role="alert">
            {{ session('success') }}
        </div>
    @endif
    @include('layouts.asesi-sertifikasi-menu')
    <div class="py-3 px-5 bg-white dark:bg-gray-800 rounded-lg shadow-md mb-2">
        <div class="text-gray-900 dark:text-gray-100">
            @if ($sertification->rincian_tugas_asesmen)
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex-shrink-0">
                        <svg class="h-10 w-10 text-gray-400 dark:text-gray-600 rounded-full bg-gray-200 dark:bg-gray-700 p-1"
                            fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <h5 class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                            @if ($sertification->pembuatrinciantugasasesmen->asesor)
                                {{-- Jika pembuatnya adalah seorang asesor, tampilkan nama dari tabel asesor --}}
                                {{ $sertification->pembuatrinciantugasasesmen->asesor->user->name }}
                            @else
                                {{-- Fallback jika karena suatu hal data pembuat tidak ada --}}
                                Admin
                            @endif
                        </h5>
                        <div class="text-xs text-gray-400">
                            @if (\Carbon\Carbon::parse($sertification->rincian_tugasasesmen_dibuat_pada)->isToday())
                                {{-- Jika hari ini, tampilkan jam --}}
                                {{ \Carbon\Carbon::parse($sertification->rincian_tugasasesmen_dibuat_pada)->format('H:i') }}
                            @else
                                {{-- Jika sudah lewat, tampilkan tanggal --}}
                                {{ \Carbon\Carbon::parse($sertification->rincian_tugasasesmen_dibuat_pada)->format('d M Y') }}
                            @endif
                        </div>
                    </div>
                </div>
                <h6 class="font-medium text-sm text-gray-800 dark:text-gray-100 mb-2">{!! $sertification?->rincian_tugas_asesmen !!}</h6>
                <div id="asesiasesmenfiles">
                    <form class="border border-gray-300 dark:border-gray-600 rounded-md p-3 mt-10"
                        action="{{ route('asesi.assessmen.update', [$sertification->id, $asesi->id]) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <label for=""
                            class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Tugas
                            Asesmen Anda (maks 5, ukuran file maksimal 3 MB)
                        </label>
                        {{-- <p class="text-sm">Jumlah file lama: <span x-text="$store.asesmen.jumlahFileLama"></span></p> --}}
                        @if ($asesi->asesiasesmenfiles && $asesi->asesiasesmenfiles->isNotEmpty())
                            <div class="flex-wrap mb-2">
                                @foreach ($asesi->asesiasesmenfiles as $attachment)
                                    <div id="file-{{ $attachment->id }}"
                                        class="flex flex-row py-1 px-2 border border-gray-300 dark:border-gray-600 rounded-md items-center justify-between mb-1">
                                        @php
                                            $basename = basename($attachment->path_file);
                                            $short =
                                                strlen($basename) > 24 ? substr($basename, 0, 24) . '...' : $basename;
                                        @endphp
                                        <a href="{{ asset('storage/' . $attachment->path_file) }}" target="_blank"
                                            class="font-medium text-sm text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 no-underline">{{ basename($short) }}</a>
                                        <button type="button"
                                            class="font-medium text-sm ml-2 text-red-600 cursor-pointer flex items-center gap-1"
                                            onclick="hapusFileAjax({{ $attachment->id }}, this)">
                                            Hapus
                                            <span class="hidden ml-1" id="spinner-{{ $attachment->id }}">
                                                <svg class="animate-spin h-4 w-4 text-red-600"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                                </svg>
                                            </span>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <span class="text-xs text-gray-900 dark:text-gray-100">Belum ada file.</span>
                        @endif
                        <input type="file" name="asesiasesmenfiles[]" multiple
                            class="w-full px-3 py-2 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-hidden dark:bg-gray-900 focus-ring-2 focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">
                        @error('asesiasesmenfiles.*')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        @error('asesiasesmenfiles')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <button type="submit"
                            class="mt-6 self-start font-medium bg-blue-500 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-700 cursor-pointer">Submit</button>
                    </form>
                </div>
            @else
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Asesor belum mengunggah deskripsi tugas untuk asesmen. Silakan periksa kembali nanti.
                </p>
            @endif
        </div>
    </div>
</x-app-layout>
