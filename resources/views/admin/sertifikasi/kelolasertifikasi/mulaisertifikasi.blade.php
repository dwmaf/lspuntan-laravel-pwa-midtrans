<x-admin-layout>
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
    <div x-data="{ tab: 'mulai', hargaValue: '{{ old('harga') }}' }" class="max-w-7xl mx-auto mb-2">
        <!-- Tombol Tab -->
        <nav class="flex flex-wrap space-x-4 mt-1" aria-label="Tabs">
            <div>
                <button @click="tab = 'mulai'"
                    class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase  hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md
        dark:text-white text-gray-600 cursor-pointer">
                    Mulai Sertifikasi
                </button>
                <div style="margin-top: -4px"
                    :class="{ 'w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md': tab === 'mulai', '': tab !== 'mulai' }">
                </div>
            </div>
            <div>
                <button @click="tab = 'berlangsung'"
                    class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase  hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md
        dark:text-white text-gray-600 cursor-pointer">
                    Sertifikasi Berlangsung
                </button>
                <div style="margin-top: -4px"
                    :class="{ 'w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md': tab === 'berlangsung', '': tab !== 'berlangsung' }">
                </div>
            </div>
            <div>
                <button @click="tab = 'selesai'"
                    class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase  hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md
        dark:text-white text-gray-600 cursor-pointer">
                    Riwayat Sertifikasi
                </button>
                <div style="margin-top: -4px"
                    :class="{ 'w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md': tab === 'selesai', '': tab !== 'selesai' }">
                </div>
            </div>
        </nav>
        <hr class=" border-gray-200 dark:border-gray-700 mb-2">

        <!-- Konten Tab -->
        <div class="">
            {{-- Konten untuk Tab Berlangsung --}}
            <div x-show="tab === 'berlangsung'">
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
                                <a href="{{ route('admin.kelolasertifikasi.show', $sert->id) }}"
                                    class="self-start font-medium bg-blue-500 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-700 cursor-pointer">Lihat</a>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 col-span-2">Tidak ada sertifikasi yang sedang
                            berlangsung.</p>
                    @endforelse
                </div>
            </div>

            {{-- Konten untuk Tab Selesai --}}
            <div x-show="tab === 'selesai'" style="display: none;" x-data="{
                showFilter: false,
                selectedFilter: 'semua',
                loading: false,
                filteredData: @js($sertifications_selesai),
            
                async filterData(filter) {
                    this.selectedFilter = filter;
                    this.loading = true;
                    this.showFilter = false;
            
                    try {
                        const response = await fetch('/admin/sertification/filter-riwayat_sertifikasi', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                            },
                            body: JSON.stringify({ filter: filter })
                        });
            
                        const data = await response.json();
                        this.filteredData = data.sertifications;
                    } catch (error) {
                        console.error('Error filtering data:', error);
                    } finally {
                        this.loading = false;
                    }
                }
            }">
                <!-- Filter Section -->
                <div class="flex justify-end items-center mb-4">
                    <div class="relative">
                        <!-- Filter Button -->
                        <button @click="showFilter = !showFilter"
                            class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z">
                                </path>
                            </svg>
                            Filter
                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': showFilter }"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="showFilter" x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95" @click.outside="showFilter = false"
                            class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-md shadow-lg z-10 border border-gray-200 dark:border-gray-600">
                            <div class="py-1">
                                <button @click="filterData('semua')"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600"
                                    :class="{ 'bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-300': selectedFilter === 'semua' }">
                                    Semua Sertifikasi
                                </button>
                                <button @click="filterData('bulan_ini')"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600"
                                    :class="{ 'bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-300': selectedFilter === 'bulan_ini' }">
                                    Bulan Ini
                                </button>
                                <button @click="filterData('3_bulan')"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600"
                                    :class="{ 'bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-300': selectedFilter === '3_bulan' }">
                                    3 Bulan Terakhir
                                </button>
                                <button @click="filterData('tahun_ini')"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600"
                                    :class="{ 'bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-300': selectedFilter === 'tahun_ini' }">
                                    Tahun Ini
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loading Indicator -->
                <div x-show="loading" class="flex justify-center items-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                    <span class="ml-2 text-gray-600 dark:text-gray-400">Memuat data...</span>
                </div>

                <div x-show="!loading" class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <template x-for="sert in filteredData" :key="sert.id">
                        <div class="bg-white p-6 rounded-lg dark:bg-gray-800 opacity-70">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200"
                                x-text="sert.skema.nama_skema"></h3>
                            <div class="flex items-center mt-4">
                                <x-bxs-calendar class="w-4 text-gray-700 dark:text-gray-200" />
                                <p class="ml-2 text-gray-600 text-sm dark:text-gray-200">
                                    Pendaftaran: <span
                                        x-text="new Date(sert.tgl_apply_dibuka).toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric'})"></span>
                                    &ndash;
                                    <span
                                        x-text="new Date(sert.tgl_apply_ditutup).toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric'})"></span>
                                </p>
                            </div>
                            <div class="flex items-center mt-4">
                                <x-tni-money class="w-4 text-gray-700 dark:text-gray-200" />
                                <p class="ml-2 text-gray-600 text-sm dark:text-gray-200">
                                    Biaya: Rp <span x-text="new Intl.NumberFormat('id-ID').format(sert.harga)"></span>
                                </p>
                            </div>
                            <div class="mt-4">
                                <a :href="`/admin/kelolasertifikasi/${sert.id}`"
                                    class="self-start font-medium bg-blue-500 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-700 cursor-pointer">Lihat</a>
                            </div>
                        </div>
                    </template>

                    <!-- Empty State -->
                    <div x-show="filteredData.length === 0" class="col-span-2">
                        <p class="text-gray-500 dark:text-gray-400">Tidak ada riwayat sertifikasi untuk filter yang
                            dipilih.</p>
                    </div>
                </div>
            </div>
            {{-- Konten untuk mulai sertifikasi --}}
            <div x-show="tab === 'mulai'" class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">

                <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Mulai Sertifikasi</h2>
                <form action="{{ route('admin.kelolasertifikasi.store') }}" class="mt-4 flex flex-col gap-2"
                    method="POST">
                    @csrf
                    <input type="text" hidden name="status" value="berlangsung">
                    <div id="asesor dan skema">
                        <label for="skema_asesor"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih
                            Skema
                            dan Asesor:</label>
                        <select required name="asesor_skema"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option value="" disabled selected>--Silahkan pilih asesor dan skema--</option>
                            @foreach ($asesors as $asesor)
                                @foreach ($asesor->skemas as $skema)
                                    <option class="" value="{{ $asesor->id . ',' . $skema->id }}">
                                        {{ $asesor->user->name }} - {{ $skema->nama_skema }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('asesor_skema')" />
                        
                    </div>
                    <div id="tanggal_apply_dibuka">
                        <label for=""
                            class="block text-sm font-medium text-gray-600 dark:text-gray-300">Tanggal
                            Daftar
                            Dibuka
                        </label>
                        <x-text-input id="tgl_apply_dibuka" name="tgl_apply_dibuka" type="date"
                            class="mt-1 block w-full" :value="old('tgl_apply_dibuka')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('tgl_apply_dibuka')" />
                    </div>
                    <div id="tanggal_apply_ditutup">
                        <label for=""
                            class="block text-sm font-medium text-gray-600 dark:text-gray-300">Tanggal
                            Daftar
                            Ditutup
                        </label>
                        <x-text-input id="tgl_apply_ditutup" name="tgl_apply_ditutup" type="date"
                            class="mt-1 block w-full" :value="old('tgl_apply_ditutup')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('tgl_apply_ditutup')" />
                    </div>
                    <div id="tanggal_bayar_ditutup">
                        <label for=""
                            class="block text-sm font-medium text-gray-600 dark:text-gray-300">Tanggal Bayar
                            Ditutup
                        </label>
                        <x-text-input id="tgl_bayar_ditutup" name="tgl_bayar_ditutup" type="datetime-local"
                            class="mt-1 block w-full" :value="old('tgl_bayar_ditutup')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('tgl_bayar_ditutup')" />
                    </div>
                    <div id="biaya_sertifikasi">
                        <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Biaya
                            Sertifikasi
                        </label>
                        <p x-show="hargaValue" class="text-sm font-medium text-gray-800 dark:text-gray-200 mt-1
                            style="display: none;">
                            <span
                                x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(hargaValue)"></span>
                        </p>
                        <x-text-input id="harga" name="harga" type="number" min="0"
                            class="mt-1 block w-full" x-model="hargaValue" required />
                        <x-input-error class="mt-2" :messages="$errors->get('harga')" />
                    </div>
                    <div id="tuk">
                        <label for=""
                            class="block text-sm font-medium text-gray-600 dark:text-gray-300">Tempat Uji
                            Sertifikasi
                        </label>
                        <x-text-input id="tuk" name="tuk" type="text" class="mt-1 block w-full"
                            :value="old('tuk')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('tuk')" />
                    </div>
                    <button type="submit"
                        class="self-start font-medium bg-blue-500 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-700 cursor-pointer">Mulai</button>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
