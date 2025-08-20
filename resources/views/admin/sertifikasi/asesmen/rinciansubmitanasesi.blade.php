<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    @include('layouts.admin-sertifikasi-menu')
    {{-- Daftar mahasiswa yg dilanjutkan ke asesmen dan pembayarannya sudah terverifikasi --}}
    <div class="p-6 bg-white dark:bg-gray-800 shadow-xl rounded-lg ">
        @if ($asesi->asesiasesmenfiles && $asesi->asesiasesmenfiles->isNotEmpty())
            <div class="flex-wrap mb-2">
                @foreach ($asesi->asesiasesmenfiles as $attachment)
                    <div id="file-{{ $attachment->id }}"
                        class="flex flex-row py-1 px-2 border border-gray-300 dark:border-gray-600 rounded-md items-center justify-between mb-1">
                        @php
                            $basename = basename($attachment->path_file);
                            $short = strlen($basename) > 24 ? substr($basename, 0, 24) . '...' : $basename;
                        @endphp
                        <a href="{{ asset('storage/' . $attachment->path_file) }}" target="_blank"
                            class="font-medium text-sm text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 no-underline">{{ basename($short) }}</a>
                    </div>
                @endforeach
            </div>
        @else
            <span class="text-xs text-gray-900 dark:text-gray-100">Belum ada file.</span>
        @endif
    </div>
</x-admin-layout>
