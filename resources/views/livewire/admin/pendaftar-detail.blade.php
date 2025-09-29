<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>

    {{-- Notifikasi Global --}}
    <div x-data="{ show: false, message: '' }"
        x-on:notify.window="message=$event.detail.message;show=true;setTimeout(()=>show=false,3000)" x-show="show"
        x-transition x-text="message" class="fixed top-20 right-4 text-xs px-3 py-2 rounded bg-green-600 text-white z-50"
        style="display:none">
    </div>
    @include('layouts.admin-sertifikasi-menu')
    <div x-show="!$wire.isEditingCertificate" class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
        {{-- Tampilan Detail Utama (muncul saat tidak edit sertifikat) --}}
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                Rincian Pendaftar
            </h3>
            <a href="{{ route('admin.sertifikasi.pendaftar.index', $sertification->id) }}" wire:navigate
                class="text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 cursor-pointer">&larr;Kembali</a>
        </div>
        <div x-transition.opacity>
            @include('livewire.admin.partials.pendaftar-detail-data-statis')
            <h3 class="text-md font-semibold dark:text-gray-300 mb-2 border-b pb-1 border-gray-700 mt-6">E. Status
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                <div>
                    <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Status Asesi</dt>
                    <dd class="mt-1 text-sm mr-1">
                        @if ($asesi->status == 'daftar')
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-sm bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100">
                                {{ $asesi->status }}
                            </span>
                        @elseif($asesi->status == 'perlu_perbaikan_berkas')
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-sm bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100">
                                Perlu perbaikan berkas
                            </span>
                        @elseif($asesi->status == 'ditolak')
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-sm bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">
                                Ditolak
                            </span>
                        @elseif($asesi->status == 'dilanjutkan_asesmen')
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-sm bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                Dilanjutkan ke asesmen
                            </span>
                        @elseif($asesi->status == 'lulus_sertifikasi')
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-sm bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                Lulus Sertifikasi
                            </span>
                        @else
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-sm bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200">
                                {{ $asesi->status ?? 'N/A' }}
                            </span>
                        @endif
                        <x-edit-button type="button" @click="$wire.set('showConfirmEditStatusModal', true)">Ubah
                            Status</x-edit-button>
                    </dd>
                </div>
                <div>
                    <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Status Pembayaran</dt>
                    @php $latestTransaction = $asesi->transaction->sortByDesc('created_at')->first(); @endphp
                    <dd @class([
                        'mt-1',
                        'text-sm',
                        'rounded-lg',
                        'p-2',
                        'border',
                        'border-green-300 dark:border-green-700' =>
                            $latestTransaction?->status == 'bukti_pembayaran_terverifikasi',
                        'border-red-300 dark:border-red-700' =>
                            $latestTransaction?->status == 'bukti_pembayaran_ditolak',
                        'border-yellow-300 dark:border-yellow-700' =>
                            $latestTransaction?->status == 'pending',
                        'border-gray-300 dark:border-gray-600' =>
                            !$latestTransaction || $latestTransaction?->status == 'belum bayar',
                    ])>

                        @if ($latestTransaction)
                            @if ($latestTransaction?->status == 'belum bayar')
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-sm bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100">
                                    belum bayar
                                </span>
                                {{-- pembayaran manual dan belum diverifikasi admin --}}
                            @elseif(
                                $latestTransaction?->tipe == 'manual' &&
                                    $latestTransaction?->bukti_bayar &&
                                    $latestTransaction?->status == 'pending')
                                <div class="space-x-1">
                                    <span
                                        class="px-1 inline-flex text-xs leading-5 font-semibold rounded-sm bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100">
                                        Menunggu Verifikasi
                                    </span>
                                    <a href="{{ asset('storage/' . $latestTransaction->bukti_bayar) }}" target="_blank"
                                        class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">Lihat
                                        File</a>
                                </div>
                            @elseif($latestTransaction?->tipe == 'manual' && $latestTransaction?->status == 'bukti_pembayaran_terverifikasi')
                                <div class="space-x-1">
                                    <span
                                        class="px-1 inline-flex text-xs leading-5 font-semibold rounded-sm bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                        Pembayaran Terverifikasi
                                    </span>
                                    <a href="{{ asset('storage/' . $latestTransaction->bukti_bayar) }}" target="_blank"
                                        class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">Lihat
                                        File</a>
                                </div>
                                {{-- pembayaran manual dan ditolak --}}
                            @elseif($latestTransaction?->tipe == 'manual' && $latestTransaction?->status == 'bukti_pembayaran_ditolak')
                                <div class="space-x-1">
                                    <span
                                        class="px-1 inline-flex text-xs leading-5 font-semibold rounded-sm bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">
                                        Bukti Pembayaran Ditolak
                                    </span>
                                    <a href="{{ asset('storage/' . $latestTransaction->bukti_bayar) }}" target="_blank"
                                        class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">Lihat
                                        File</a>
                                </div>
                            @endif
                        @else
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-sm bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100">
                                Belum submit bukti pembayaran
                            </span>
                        @endif
                        @if ($latestTransaction)
                            <div class="flex justify-end">
                                <x-edit-button @click="$wire.set('showConfirmUpdateStatusPembayaranModal', true)">Ubah
                                    Status</x-edit-button>
                            </div>
                        @endif
                    </dd>
                </div>
            </div>

            {{-- Contoh Bagian F. Sertifikat --}}
            <h3 class="text-md font-semibold dark:text-gray-300 mb-2 border-b pb-1 border-gray-700 mt-6">F.
                Sertifikat</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                <div>
                    <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Sertifikat Asesi</dt>
                    <dd class="mt-1 text-sm mr-1 space-y-2">
                        @if ($asesi->sertifikat)
                            <x-edit-button @click="$wire.set('isEditingCertificate', true)" >
                                <x-bxs-edit class="w-3 h-3 mr-2" />
                                <span>
                                    Ubah Data Sertifikat
                                </span>
                                <x-loading-spinner wire:loading
                                    wire:target="enterCertificateEditMode"></x-loading-spinner>
                            </x-edit-button>
                        @elseif($asesi->status == 'lulus_sertifikasi')
                            <x-see-button wire:click="enterCertificateEditMode">
                                <x-fas-upload class="w-3 h-3" />
                                <span>
                                    Upload Sertifikat
                                </span>
                                <x-loading-spinner wire:loading
                                    wire:target="enterCertificateEditMode"></x-loading-spinner>
                            </x-see-button>
                        @endif
                    </dd>
                </div>
            </div>

        </div>



        <!-- Modal Konfirmasi Ubah status asesi -->
        <div x-data="{ showCatatanStatus: false }" x-show="$wire.showConfirmEditStatusModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 " style="display: none;">
            <div @click.away="$wire.set('showConfirmEditStatusModal', false)"
                class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-4 w-[280px]">
                <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-6">Konfirmasi Ubah Status Asesi
                </h3>
                <form wire:submit.prevent="updateStatusAsesi">
                    <div id="ubah status asesi">
                        <label for="status asesi"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih status</label>
                        <select required wire:model.defer="newStatusAsesi"
                            @change="showCatatanStatus = ($event.target.value === 'perlu_perbaikan_berkas')"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option value="daftar">Daftar</option>
                            <option value="ditolak">
                                Ditolak
                            </option>
                            <option value="perlu_perbaikan_berkas">
                                Perlu Perbaikan Berkas
                            </option>
                            <option value="dilanjutkan_asesmen">
                                Dilanjutkan ke asesmen
                            </option>
                            <option value="lulus_sertifikasi">
                                Lulus Sertifikasi
                            </option>
                        </select>
                        <div x-show="showCatatanStatus">
                            <label for="catatan_status"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mt-4">Catatan</label>
                            <textarea wire:model.defer="catatanStatus" id="catatan_status" rows="5"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
                        </div>
                        @error('status')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mt-4 flex items-center justify-end space-x-3">
                        <x-secondary-button
                            @click="$wire.set('showConfirmEditStatusModal', false)">Batal</x-secondary-button>
                        <x-primary-button>Ubah</x-primary-button>
                        <div wire:loading wire:target="updateStatusAsesi">
                            <x-loading-spinner></x-loading-spinner>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Konfirmasi Ubah status pembayaran -->
        <div x-show="$wire.showConfirmUpdateStatusPembayaranModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 " style="display: none;">
            <div @click.away="$wire.set('showConfirmUpdateStatusPembayaranModal', false)"
                class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-4 w-[280px]">
                <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-6">Konfirmasi Ubah Status
                    Pembayaran
                </h3>
                <form wire:submit.prevent="updateStatusPembayaran">
                    <div id="ubah status asesi">
                        <label for="skema_asesor"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih status</label>
                        <select required wire:model.defer="newStatusPembayaran"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option value="bukti_pembayaran_ditolak">
                                Bukti Pembayaran Ditolak
                            </option>
                            <option value="pending">Menunggu Verifikasi</option>
                            <option value="perlu_perbaikan_berkas">
                                Perlu Perbaikan Berkas
                            </option>
                            <option class="" value="bukti_pembayaran_terverifikasi">
                                Bukti Pembayaran Diterima
                            </option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mt-4 flex items-center justify-end space-x-3">
                        <x-secondary-button
                            @click="$wire.set('showConfirmUpdateStatusPembayaranModal', false)">Batal</x-secondary-button>
                        <x-primary-button>Ubah</x-primary-button>
                        <div wire:loading wire:target="updateStatusPembayaran">
                            <x-loading-spinner></x-loading-spinner>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div x-show="$wire.isEditingCertificate" x-transition.opacity style="display: none;"
        class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
        {{-- Tampilan Detail Utama (muncul saat tidak edit sertifikat) --}}
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                @if ($asesi->sertifikat)
                    Ubah Data Sertifikat
                @else
                    Upload Sertifikat
                @endif
            </h3>
        </div>
        <p class="my-1 text-sm text-gray-600 dark:text-gray-400">Untuk: <span
                class="font-semibold">{{ $asesi->student->user->name }}</span></p>

        <form wire:submit.prevent="saveCertificate" class="flex flex-col gap-4">
            <div>
                <x-input-label>Nomor Seri</x-input-label>
                <x-text-input wire:model.defer="nomor_seri" type="text" class="mt-1 block w-full" required />
                <x-input-error class="mt-2" :messages="$errors->get('nomor_seri')" />
            </div>
            <div>
                <x-input-label>Nomor Sertifikat</x-input-label>
                <x-text-input wire:model.defer="nomor_sertifikat" type="text" class="mt-1 block w-full"
                    required />
                <x-input-error class="mt-2" :messages="$errors->get('nomor_sertifikat')" />
            </div>
            <div>
                <x-input-label>Nomor Registrasi</x-input-label>
                <x-text-input wire:model.defer="nomor_registrasi" type="text" class="mt-1 block w-full"
                    required />
                <x-input-error class="mt-2" :messages="$errors->get('nomor_registrasi')" />
            </div>
            <div>
                <x-input-label>Tanggal Terbit</x-input-label>
                <x-text-input wire:model.defer="tanggal_terbit" type="date" class="mt-1 block w-full" required />
                <x-input-error class="mt-2" :messages="$errors->get('tanggal_terbit')" />
            </div>
            <div>
                <x-input-label>Berlaku Hingga</x-input-label>
                <x-text-input wire:model.defer="berlaku_hingga" type="date" class="mt-1 block w-full" required />
                <x-input-error class="mt-2" :messages="$errors->get('berlaku_hingga')" />
            </div>
            <div>
                <x-input-label>File Sertifikat (PDF, JPG, PNG)</x-input-label>
                @if ($asesi->sertifikat)
                    <p class="text-sm text-gray-500 mb-1">File sudah ada:
                        <a href="{{ asset('storage/' . $asesi->sertifikat->file_path) }}"
                            class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">Lihat
                            Sertifikat</a>
                    </p>
                @endif
                <x-file-input type="file" wire:model.defer="sertifikat_asesi" class="mt-1" />
                @if ($asesi->sertifikat)
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah file.</p>
                @endif
                <x-input-error class="mt-2" :messages="$errors->get('sertifikat_asesi')" />
            </div>
            <div class="flex gap-2 items-center">
                <x-loading-spinner wire:loading wire:target="saveCertificate"></x-loading-spinner>
                <x-primary-button wire:loading.attr="disabled">Upload</x-primary-button>
                <x-secondary-button wire:click="$set('isEditingCertificate', false)">Batal</x-secondary-button>
            </div>
        </form>


    </div>
</div>
