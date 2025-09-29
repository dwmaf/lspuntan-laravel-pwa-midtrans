<div class="max-w-7xl mx-auto mb-2">
    {{-- Notifikasi --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    <div x-data="{ show: false, message: '' }"
        x-on:notify.window="message=$event.detail.message;show=true;setTimeout(()=>show=false,3000)" x-show="show"
        x-transition x-text="message" class="fixed top-20 right-4 text-xs px-3 py-2 rounded bg-green-600 text-white z-50"
        style="display:none">
    </div>

    <nav class="flex flex-wrap space-x-4 mt-1" aria-label="Tabs">
        <div>
            <button @click="$wire.set('tab', 'mulai')"
                class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md dark:text-white text-gray-600 cursor-pointer">
                Mulai Sertifikasi
            </button>
            <div style="margin-top: -4px" x-show="$wire.tab === 'mulai'"
                class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
        </div>
        <div>
            <button @click="$wire.set('tab', 'berlangsung')"
                class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md dark:text-white text-gray-600 cursor-pointer">
                Sertifikasi Berlangsung
            </button>
            <div style="margin-top: -4px" x-show="$wire.tab === 'berlangsung'"
                class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md">
            </div>
        </div>
        <div>
            <button @click="$wire.set('tab', 'selesai')"
                class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md dark:text-white text-gray-600 cursor-pointer">
                Riwayat Sertifikasi
            </button>
            <div style="margin-top: -4px" x-show="$wire.tab === 'selesai'"
                class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
        </div>
    </nav>
    <hr class="border-gray-200 dark:border-gray-700 mb-2">

    <!-- Konten Tab -->
    <div>
        {{-- Konten untuk Tab Berlangsung --}}
        <div x-show="$wire.tab === 'berlangsung'">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                @forelse ($sertifications_berlangsung as $sert)
                    <div class="bg-white p-6 rounded-lg dark:bg-gray-800">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                            {{ $sert->skema->nama_skema }}</h3>
                        <div class="flex items-center mt-4">
                            <x-bxs-calendar class="w-4 text-gray-700 dark:text-gray-200" />
                            <p class="ml-2 text-gray-600 text-sm dark:text-gray-200">
                                Pendaftaran:
                                {{ \Carbon\Carbon::parse($sert->tgl_apply_dibuka)->format('d M Y') }}
                                &ndash;
                                {{ \Carbon\Carbon::parse($sert->tgl_apply_ditutup)->format('d M Y') }}
                            </p>
                        </div>
                        <div class="flex items-center mt-4">
                            <x-tni-money class="w-4 text-gray-700 dark:text-gray-200" />
                            <p class="ml-2 text-gray-600 text-sm dark:text-gray-200">
                                Biaya:
                                Rp {{ number_format($sert->harga, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="mt-4">
                            <x-primary-link-button :href="route('admin.kelolasertifikasi.show', $sert->id)" wire:navigate>detail</x-primary-link-button>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400 col-span-2">Tidak ada sertifikasi yang sedang
                        berlangsung.</p>
                @endforelse
            </div>
        </div>

        {{-- Konten untuk Tab Selesai (Disederhanakan dengan Livewire) --}}
        <div x-show="$wire.tab === 'selesai'" x-data="{ showFilter: false }">
            <div class="flex justify-end items-center mb-4">
                <div class="relative">
                    <button @click="showFilter = !showFilter"
                        class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z">
                            </path>
                        </svg>
                        Filter
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': showFilter }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div x-show="showFilter" @click.outside="showFilter = false"
                        class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-md shadow-lg z-10 border border-gray-200 dark:border-gray-600">
                        <div class="py-1">
                            {{-- Ganti @click dengan wire:click --}}
                            <button wire:click="filterRiwayat('semua')" @click="showFilter = false"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ $selectedFilter === 'semua' ? 'bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : '' }}">Semua</button>
                            <button wire:click="filterRiwayat('bulan_ini')" @click="showFilter = false"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ $selectedFilter === 'bulan_ini' ? 'bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : '' }}">Bulan
                                Ini</button>
                            <button wire:click="filterRiwayat('3_bulan')" @click="showFilter = false"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ $selectedFilter === '3_bulan' ? 'bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : '' }}">3
                                Bulan Terakhir</button>
                            <button wire:click="filterRiwayat('tahun_ini')" @click="showFilter = false"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ $selectedFilter === 'tahun_ini' ? 'bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : '' }}">Tahun
                                Ini</button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Loading Indicator Livewire --}}
            <div wire:loading.flex wire:target="filterRiwayat" class="justify-center items-center py-8">
                <x-loading-spinner />
            </div>

            {{-- Tampilkan data setelah loading selesai --}}
            <div wire:loading.remove wire:target="filterRiwayat" class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                @forelse ($sertifications_selesai as $sert)
                    <div class="bg-white p-6 rounded-lg dark:bg-gray-800 opacity-70">
                        <div class="bg-white p-6 rounded-lg dark:bg-gray-800 opacity-70">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">{{ $sert->nama_skema }}
                            </h3>
                            <div class="flex items-center mt-4">
                                <x-bxs-calendar class="w-4 text-gray-700 dark:text-gray-200" />
                                <p class="ml-2 text-gray-600 text-sm dark:text-gray-200">
                                    Pendaftaran:
                                    <span>{{ \Carbon\Carbon::parse($sert->tgl_apply_dibuka)->format('d M Y') }}</span>
                                    &ndash;
                                    <span
                                        {{ \Carbon\Carbon::parse($sert->tgl_apply_ditutup)->format('d M Y') }}></span>
                                </p>
                            </div>
                            <div class="flex items-center mt-4">
                                <x-tni-money class="w-4 text-gray-700 dark:text-gray-200" />
                                <p class="ml-2 text-gray-600 text-sm dark:text-gray-200">
                                    Biaya: Rp <span>Rp {{ number_format($sert->harga, 0, ',', '.') }}</span>
                                </p>
                            </div>
                            <div class="mt-4">

                                <x-primary-link-button :href="route('admin.kelolasertifikasi.show', $sert->id)" wire:navigate>detail</x-primary-link-button>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400 col-span-2">Tidak ada riwayat sertifikasi untuk filter
                        yang dipilih.</p>
                @endforelse
            </div>
        </div>

        {{-- Konten untuk mulai sertifikasi (Ganti dengan wire:submit) --}}
        <div x-show="$wire.tab === 'mulai'" class=" p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Mulai Sertifikasi</h2>
            <form x-data="{ harga: @entangle('harga') }" wire:submit.prevent="save" class="mt-4 flex flex-col gap-4">
                {{-- Ganti semua input dengan wire:model --}}
                <div>
                    <x-input-label>Pilih Skema dan Asesor:</x-input-label>
                    <select wire:model="asesor_skema"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="" disabled>--Silahkan pilih asesor dan skema--</option>
                        @foreach ($asesors as $asesor)
                            @foreach ($asesor->skemas as $skema)
                                <option value="{{ $asesor->id . ',' . $skema->id }}">{{ $asesor->user->name }} -
                                    {{ $skema->nama_skema }}</option>
                            @endforeach
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('asesor_skema')" />
                </div>
                <div>
                    <x-input-label>Tanggal Daftar Dibuka</x-input-label>
                    <x-text-input wire:model="tgl_apply_dibuka" type="date" class="mt-1 block w-full" />
                    <x-input-error class="mt-2" :messages="$errors->get('tgl_apply_dibuka')" />
                </div>
                <div id="tanggal_apply_ditutup">
                    <x-input-label>Tanggal Daftar Ditutup</x-input-label>
                    <x-text-input wire:model="tgl_apply_ditutup" type="date" class="mt-1 block w-full" />
                    <x-input-error class="mt-2" :messages="$errors->get('tgl_apply_ditutup')" />
                </div>
                <div id="tanggal_bayar_ditutup">
                    <x-input-label>Tanggal Bayar Ditutup</x-input-label>
                    <x-text-input wire:model="tgl_bayar_ditutup" type="datetime-local" class="mt-1 block w-full" />
                    <x-input-error class="mt-2" :messages="$errors->get('tgl_bayar_ditutup')" />
                </div>
                <div id="biaya_sertifikasi">
                    <x-input-label>Biaya Sertifikasi</x-input-label>
                    <p x-show="harga" class="text-sm font-medium text-gray-800 dark:text-gray-400"
                        style="display: none;">
                        <span
                            x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(harga)"></span>
                    </p>
                    <x-text-input wire:model="harga" type="number" x-model="harga" min="0"
                        class="mt-1 block w-full" />
                    <x-input-error class="mt-2" :messages="$errors->get('harga')" />
                </div>
                <div id="tuk">
                    <x-input-label>Tempat Uji Sertifikasi</x-input-label>
                    <x-text-input id="tuk" wire:model="tuk" type="text" class="mt-1 block w-full" />
                    <x-input-error class="mt-2" :messages="$errors->get('tuk')" />
                </div>
                <div class="flex items-center gap-4 pt-2">
                    <x-primary-button class="">
                        Simpan
                    </x-primary-button>
                    <div wire:loading wire:target="save">
                        <x-loading-spinner />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
