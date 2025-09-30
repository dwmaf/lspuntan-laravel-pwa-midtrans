<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Sertifikasi
        </h2>
    </x-slot>
    <div x-data="{ show: false, message: '' }"
        x-on:notify.window="message=$event.detail.message;show=true;setTimeout(()=>show=false,3000)" x-show="show"
        x-transition x-text="message" class="fixed top-20 right-4 text-xs px-3 py-2 rounded bg-green-600 text-white z-50"
        style="display:none">
    </div>
    @include('layouts.admin-sertifikasi-menu')
    <div class="max-w-7xl mx-auto">
        <div x-show="$wire.isEditing" class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <form wire:submit.prevent="update" class="space-y-6">
                <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Edit Sertifikasi</h2>
                <div id="asesor dan skema">
                    <x-input-label>Pilih Skema dan Asesor:</x-input-label>
                    <select wire:model.defer="asesor_skema" required name="asesor_skema"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="" disabled>--Silahkan pilih asesor dan skema--</option>
                        @foreach ($asesors as $asesor)
                            @foreach ($asesor->skemas as $skema)
                                <option value="{{ $asesor->id . ',' . $skema->id }}"
                                    @if ($asesor_skema == $asesor->id . ',' . $skema->id) selected @endif>
                                    {{ $asesor->user->name }} - {{ $skema->nama_skema }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('asesor_skema')" />
                </div>
                <div id="tanggal_apply_dibuka">
                    <x-input-label>Tanggal Daftar Dibuka</x-input-label>
                    <x-text-input wire:model.defer="tgl_apply_dibuka" type="date" class="mt-1 block w-full" />
                    <x-input-error class="mt-2" :messages="$errors->get('tgl_apply_dibuka')" />
                </div>
                <div id="tanggal_apply_ditutup">
                    <x-input-label>Tanggal Daftar Ditutup</x-input-label>
                    <x-text-input wire:model.defer="tgl_apply_ditutup" type="date" class="mt-1 block w-full" />
                    <x-input-error class="mt-2" :messages="$errors->get('tgl_apply_ditutup')" />
                </div>
                <div id="tanggal_bayar_ditutup">
                    <x-input-label>Tanggal Bayar Ditutup</x-input-label>
                    <x-text-input wire:model.defer="tgl_bayar_ditutup" type="datetime-local"
                        class="mt-1 block w-full" />
                    <x-input-error class="mt-2" :messages="$errors->get('tgl_bayar_ditutup')" />
                </div>
                <div x-data="{ hargaValue: @entangle('harga') }" id="biaya_sertifikasi">
                    <x-input-label>Biaya Sertifikasi</x-input-label>
                    <p x-show="hargaValue" class="text-sm font-medium text-gray-800 dark:text-gray-200 mt-1"
                        style="display: none;">
                        <span
                            x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(hargaValue)"></span>
                    </p>
                    <x-text-input wire:model.defer="harga" x-model="hargaValue" type="number" min="0"
                        class="mt-1 block w-full" />
                    <x-input-error class="mt-2" :messages="$errors->get('harga')" />
                </div>
                <div id="tuk">
                    <x-input-label>Tempat Uji Sertifikasi</x-input-label>
                    <x-text-input wire:model.defer="tuk" type="text" class="mt-1 block w-full" />
                    <x-input-error class="mt-2" :messages="$errors->get('tuk')" />
                </div>
                <div id="status" class="mb-2">
                    <x-input-label>Status</x-input-label>
                    <div class="flex gap-4">
                        <label class="inline-flex items-center cursor-pointer">
                            <input wire:model.defer="status" type="radio" value="berlangsung"
                                class="cursor-pointer dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                required>
                            <span class="ml-2 dark:text-gray-300">Sedang Berlangsung</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" wire:model.defer="status" value="selesai"
                                class="cursor-pointer dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                required>
                            <span class="ml-2 dark:text-gray-300">Selesai</span>
                        </label>
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('status')" />
                </div>

                <div class="flex items-center gap-4">
                    <span wire:loading wire:target="update" class="flex items-center">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </span>
                    <x-primary-button wire:loading.attr="disabled">Update</x-primary-button>
                    <x-secondary-button @click="$wire.set('isEditing', false)">Batal</x-secondary-button>
                </div>
            </form>
        </div>
        {{-- @if ($isEditing)
        @else
        @endif --}}
        <div x-show="!$wire.isEditing" class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 sm:mb-0">
                    Detail Sertifikasi
                </h3>
                {{-- {{ $sertification }} --}}
                <div class="flex items-center space-x-3">
                    <x-edit-button type="button" @click="$wire.set('isEditing', true)">
                        Edit
                    </x-edit-button>

                    @if ($sertification->status == 'berlangsung')
                        <form class="inline-block"
                            action="{{ route('admin.kelolasertifikasi.destroy', $sertification->id) }}"
                            method="post"
                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus data sertifikasi ini? Ini tidak akan menghapus skema atau asesor terkait, hanya jadwal sertifikasi ini.');">
                            @method('delete')
                            @csrf
                            <x-delete-button type="submit">Hapus</x-delete-button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Skema</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ $sertification->skema->nama_skema ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Asesor</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ $sertification->asesor->user->name ?? 'N/A' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Pendaftaran Dibuka</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ \Carbon\Carbon::parse($sertification->tgl_apply_dibuka)->isoFormat('D MMMM YYYY') ?? 'N/A' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Pendaftaran Ditutup
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ \Carbon\Carbon::parse($sertification->tgl_apply_ditutup)->isoFormat('D MMMM YYYY') ?? 'N/A' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Batas Akhir Pembayaran</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ \Carbon\Carbon::parse($sertification->tgl_bayar_ditutup)->isoFormat('D MMMM YYYY') ?? 'N/A' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Biaya Sertifikasi</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">Rp
                        {{ number_format($sertification->harga, 0, ',', '.') ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">TUK</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ $sertification->tuk ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                    <dd class="mt-1 text-sm">
                        @if ($sertification->status == 'berlangsung')
                            <span
                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                Sedang Berlangsung
                            </span>
                        @else
                            <span
                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100">
                                Selesai
                            </span>
                        @endif
                    </dd>
                </div>
            </div>
        </div>
    </div>
</div>
