<div>
    {{-- Notifikasi (copy dari komponen Asesmen) --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    <div x-data="{ show: false, message: '' }"
        x-on:notify.window="message=$event.detail.message;show=true;setTimeout(()=>show=false,2500)" x-show="show"
        x-transition x-text="message" class="text-xs px-3 py-2 rounded bg-green-600 text-white inline-block"
        style="display:none">
    </div>
    @include('layouts.admin-sertifikasi-menu')

    {{-- Mode Tampilan --}}
    @if (!$editingRincian)
        <div class="py-3 px-5 bg-white dark:bg-gray-800 rounded-lg shadow-md mb-2">
            <div class="flex justify-between items-center mb-2">
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
                            @if ($sertification->pembuatrincianpembayaran && $sertification->pembuatrincianpembayaran->asesor)
                                {{ $sertification->pembuatrincianpembayaran->asesor->user->name }}
                            @else
                                Admin
                            @endif
                        </h5>
                        <div class="text-xs text-gray-400">
                            {{ $sertification->tanggal_rincian_bayar_dibuat_formatted }}
                            @if ($sertification->rincianbayar_updatedat)
                                (Diedit)
                            @endif
                        </div>
                    </div>
                </div>
                <x-edit-button type="button" wire:click="edit">
                    <x-loading-spinner wire:target="edit" wire:loading></x-loading-spinner>
                    <span>
                        Edit
                    </span>
                </x-edit-button>

            </div>

            <div class="font-medium text-sm text-gray-800 dark:text-gray-100 mb-2">
                {!! nl2br(e($sertification->rincian_pembayaran)) !!}
            </div>
            <div class="flex mb-2">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mr-1">Biaya Sertifikasi : </dt>
                <dd class="text-sm text-gray-900 dark:text-gray-100">Rp
                    {{ number_format($sertification->harga, 0, ',', '.') ?? 'N/A' }}</dd>
            </div>
            <div class="flex">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mr-1">Batas Akhir Pembayaran : </dt>
                <dd class="text-sm text-gray-900 dark:text-gray-100">
                    {{ $sertification->tgl_bayar_ditutup?->isoFormat('D MMMM YYYY, HH:mm') ?? 'N/A' }}
                </dd>
            </div>
        </div>
    @endif

    {{-- Mode Edit --}}
    @if ($editingRincian)
        <div x-data="{ harga: @entangle('harga') }" class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md flex flex-col gap-4">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Edit Rincian Instruksi Pembayaran</h3>

            </div>
            <form wire:submit.prevent="save" class="flex flex-col gap-4">
                <div>
                    <x-input-label>Rincian Pembayaran</x-input-label>
                    <textarea wire:model.defer="rincian_pembayaran" rows="8"
                        class="mt-1 w-full text-sm p-3 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100"></textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('rincian_pembayaran')" />
                </div>
                <div>
                    <x-input-label>Biaya Sertifikasi</x-input-label>
                    <p x-show="harga" class="text-sm font-medium text-gray-800 dark:text-gray-400"
                        style="display: none;">
                        <span
                            x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(harga)"></span>
                    </p>
                    <x-text-input id="harga" wire:model.defer="harga" x-model="harga" type="number" min="0"
                        class="mt-1 block w-full" required />
                    <x-input-error class="mt-2" :messages="$errors->get('harga')" />
                </div>
                <div>
                    <x-input-label>Batas Akhir Pembayaran</x-input-label>
                    <x-text-input id="tgl_bayar_ditutup" wire:model.defer="tgl_bayar_ditutup" type="datetime-local"
                        class="mt-1 block w-full" required />
                    <x-input-error class="mt-2" :messages="$errors->get('tgl_bayar_ditutup')" />
                </div>
                <div class="flex gap-2 items-center">
                    <x-loading-spinner wire:loading wire:target="save"></x-loading-spinner>
                    <x-primary-button wire:loading.attr="disabled">Simpan</x-primary-button>
                    <x-secondary-button wire:click="$set('editingRincian', false)">Batal</x-secondary-button>
                </div>
            </form>
        </div>
    @endif
</div>
