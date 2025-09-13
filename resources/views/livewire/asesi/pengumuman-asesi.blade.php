<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Rincian Sertifikasi') }}
        </h2>
    </x-slot>
    @include('layouts.asesi-sertifikasi-menu')
    <div>
        
        @if ($pengumumans->isEmpty())
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                Belum ada pengumuman apapun.
            </p>
        @else
            @foreach ($pengumumans as $pengumuman)
                <div class="py-3 px-5 bg-white dark:bg-gray-800 rounded-lg shadow-md mb-2">
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
                                @if ($pengumuman->pembuatpengumuman->asesor)
                                    {{-- Jika pembuatnya adalah seorang asesor, tampilkan nama dari tabel asesor --}}
                                    {{ $pengumuman->pembuatpengumuman->asesor->name }}
                                @else
                                    {{-- Fallback jika karena suatu hal data pembuat tidak ada --}}
                                    Admin
                                @endif
                            </h5>
                            <div class="text-xs text-gray-400">
                                @if (\Carbon\Carbon::parse($pengumuman->rincian_pengumuman_asesmen_dibuat_pada)->isToday())
                                    {{-- Jika hari ini, tampilkan jam --}}
                                    {{ \Carbon\Carbon::parse($pengumuman->rincian_pengumuman_asesmen_dibuat_pada)->format('H:i') }}
                                @else
                                    {{-- Jika sudah lewat, tampilkan tanggal --}}
                                    {{ \Carbon\Carbon::parse($pengumuman->rincian_pengumuman_asesmen_dibuat_pada)->format('d M Y') }}
                                @endif
                            </div>
                        </div>
                    </div>
                    <h6 class="font-medium text-sm text-gray-800 dark:text-gray-100">{!! $pengumuman?->rincian_pengumuman_asesmen !!}</h6>
                </div>
            @endforeach
        @endif
    </div>
</div>