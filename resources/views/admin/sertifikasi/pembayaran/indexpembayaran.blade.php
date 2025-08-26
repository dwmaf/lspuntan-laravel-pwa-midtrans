<x-admin-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    @if (session('success'))
        <div class="my-2 p-4 bg-green-100 dark:bg-green-700 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-100 rounded-md"
            role="alert">
            {{ session('success') }}
        </div>
    @endif
    @include('layouts.admin-sertifikasi-menu')
    @php
        $rincianDefault = 'Silahkan buat rincian pembayaran...';
        $punyaRincian =
            !empty($sertification->rincian_pembayaran) && $sertification->rincian_pembayaran !== $rincianDefault;
    @endphp
    <div x-data="{ editingRincian: {{ $punyaRincian ? 'false' : 'true' }}, punyaRincian: {{ $punyaRincian ? 'true' : 'false' }} }">
        {{-- View utk edit rincian pembayaran (edit mode) --}}
        <div x-show="editingRincian"
            class="p-6 bg-white dark:bg-gray-800 rounded-tr-lg rounded-bl-lg rounded-br-lg shadow-md">
            <div class="flex justify-between items-center mb-2">
                <p class="text-gray-500 dark:text-gray-400 text-xs">Silahkan berikan rincian pembayaran
                    yang harus dilakukan para asesi.</p>
                <button x-show="punyaRincian" type="button" @click="editingRincian = false"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 cursor-pointer">
                    Batal
                </button>
            </div>
            <form action="{{ route('admin.sertifikasi.payment-desc.update', $sertification->id) }}"
                class="mt-4 flex flex-col gap-2" method="POST">
                @csrf
                @method('PATCH')
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1"
                    for="rincian_pembayaran">Rincian
                    Pembayaran</label>
                <p class="text-gray-500 dark:text-gray-400 text-xs">Nominal uang yang perlu dibayarkan asesi sudah
                    otomatis tertera di ui asesi</p>
                <input id="rincian_pembayaran" type="hidden" name="rincian_pembayaran"
                    value="{{ old('rincian_pembayaran', $sertification?->rincian_pembayaran) }}">
                @include('layouts.custom-rich-editor', [
                    'inputName' => 'rincian_pembayaran',
                    'initialValue' => old(
                        'rincian_pembayaran',
                        $sertification?->rincian_pembayaran ?? $rincianDefault),
                ])
                @error('rincian_pembayaran')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                <button type="submit"
                    class="self-start font-medium bg-blue-500 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-700 cursor-pointer">
                    Simpan Perubahan
                </button>
            </form>
        </div>
        {{-- View utk nampilin rincian pembayaran (view mode) --}}
        <div class="py-3 px-5 bg-white dark:bg-gray-800 rounded-lg shadow-md mb-2" x-show="!editingRincian">
            <div class="flex justify-between items-center mb-2">
                {{-- foto profil, nama, dan tanggal rincian pembayaran dibuat --}}
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
                            @if ($sertification->pembuatrincianpembayaran->asesor)
                                {{-- Jika pembuatnya adalah seorang asesor, tampilkan nama dari tabel asesor --}}
                                {{ $sertification->pembuatrincianpembayaran->asesor->name }}
                            @else
                                {{-- Fallback jika karena suatu hal data pembuat tidak ada --}}
                                Admin
                            @endif
                        </h5>
                        <div class="text-xs text-gray-400">
                            @if (\Carbon\Carbon::parse($sertification->rincian_bayar_dibuat_pada)->isToday())
                                {{-- Jika hari ini, tampilkan jam --}}
                                {{ \Carbon\Carbon::parse($sertification->rincian_bayar_dibuat_pada)->format('H:i') }}
                            @else
                                {{-- Jika sudah lewat, tampilkan tanggal --}}
                                {{ \Carbon\Carbon::parse($sertification->rincian_bayar_dibuat_pada)->format('d M Y') }}
                            @endif
                        </div>
                    </div>
                </div>
                <button type="button" @click="editingRincian = true"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-yellow-500 hover:bg-yellow-600 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-700 transition ease-in-out duration-150 cursor-pointer">
                    <x-bxs-edit class="w-4 h-4 mr-2" />
                    Edit
                </button>
            </div>
            <h6 class="font-medium text-sm text-gray-800 dark:text-gray-100 mb-2">{!! $sertification?->rincian_pembayaran !!}</h6>
            <div class="flex mb-2">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mr-1">Biaya Sertifikasi : </dt>
                <dd class="text-sm text-gray-900 dark:text-gray-100">Rp
                    {{ number_format($sertification->harga, 0, ',', '.') ?? 'N/A' }}</dd>
            </div>
            <div class="flex">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mr-1">Batas Akhir Pembayaran : </dt>
                <dd class="text-sm text-gray-900 dark:text-gray-100">
                    {{ \Carbon\Carbon::parse($sertification->tgl_bayar_ditutup)->isoFormat('D MMMM YYYY') ?? 'N/A' }}</dd>
            </div>
        </div>
    </div>

</x-admin-layout>
