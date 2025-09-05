<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    @include('layouts.admin-sertifikasi-menu')
    <div class="p-6 bg-white dark:bg-gray-800 shadow-xl rounded-lg ">
        @if ($asesi->asesiasesmenfiles && $asesi->asesiasesmenfiles->isNotEmpty())
            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">{{ $asesi->student->user->name }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2">
                @foreach ($asesi->asesiasesmenfiles as $attachment)
                    <div id="file-{{ $attachment->id }}"
                        class="flex flex-row py-1 px-2 border border-gray-300 dark:border-gray-600 rounded-md items-center justify-between">
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
