<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    @include('layouts.admin-sertifikasi-menu')
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md" x-data="{ showConfirmEditStatusModal: false, showConfirmUpdateStatusPembayaranModal: false, editStatusAsesiUrl: '', editStatusPembayaranUrl: '' }">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 sm:mb-0">
            Rincian Pendaftar
        </h3>

        <div class="space-y-6">
            <div>
                <h3 class="text-md font-semibold dark:text-gray-300 mb-2 border-b pb-1 border-gray-700">A. Data Pribadi
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Nama Lengkap (Sesuai
                            KTP)</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $asesi->student?->name ?: 'Tidak diisi' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">No. KTP</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $asesi->student?->nik ?: 'Tidak diisi' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Tempat Lahir</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $asesi->student?->tmpt_lhr ?: 'Tidak diisi' }}</dd>
                    </div>
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Tanggal Lahir</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $asesi->student?->tgl_lhr ? \Carbon\Carbon::parse($asesi->student->tgl_lhr)->isoFormat('D MMMM YYYY') : 'Tidak diisi' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Jenis Kelamin</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $asesi->student?->kelamin ?: 'Tidak diisi' }}</dd>
                    </div>
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Kebangsaan</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $asesi->student?->kebangsaan ?: 'Tidak diisi' }}</dd>
                    </div>
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">No. Tlp HP(WA)</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $asesi->student?->no_tlp_hp ?: 'Tidak diisi' }}</dd>
                    </div>
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">No. Tlp Rumah</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $asesi->student?->no_tlp_rmh ?: 'Tidak diisi' }}</dd>
                    </div>
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">No. Tlp Kantor</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $asesi->student?->no_tlp_kntr ?: 'Tidak diisi' }}</dd>
                    </div>
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Kualifikasi
                            Pendidikan</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $asesi->student?->kualifikasi_pendidikan ?: 'Tidak diisi' }}</dd>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-md font-semibold dark:text-gray-300 mb-2 border-b pb-1 border-gray-700 mt-6">B. Data
                    Pekerjaan Sekarang</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Nama
                            Institusi/Perusahaan</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $asesi->student?->nama_institusi ?: 'Tidak diisi' }}</dd>
                    </div>
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Jabatan</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $asesi->student?->jabatan ?: 'Tidak diisi' }}</dd>
                    </div>
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Alamat Kantor</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $asesi->student?->alamat_kantor ?: 'Tidak diisi' }}</dd>
                    </div>
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">No. Tlp/Email/Fax
                            Kantor
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $asesi->student?->no_tlp_email_fax ?: 'Tidak diisi' }}</dd>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-md font-semibold dark:text-gray-300 mb-2 border-b pb-1 border-gray-700 mt-6">C. Data
                    Sertifikasi</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Tujuan
                            Sertifikasi</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $asesi?->tujuan_sert ?: 'Tidak diisi' }}</dd>
                    </div>
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Mata Kuliah terkait
                            Skema Sertifikasi dan Nilai</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $asesi?->makul_nilai ?: 'Tidak diisi' }}</dd>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-md font-semibold dark:text-gray-300 mb-2 border-b pb-1 border-gray-700 mt-6">D. Bukti
                    Kelengkapan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Form APL.01</dt>
                        <dd class="mt-1 text-sm">
                            @if ($asesi->apl_1)
                                <a href="{{ asset('storage/' . $asesi->apl_1) }}" target="_blank"
                                    class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">Lihat
                                    File APL.01</a>
                            @else
                                <span class="text-gray-900 dark:text-gray-100">Tidak ada file.</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Form APL.02</dt>
                        <dd class="mt-1 text-sm">
                            @if ($asesi->apl_2)
                                <a href="{{ asset('storage/' . $asesi->apl_2) }}" target="_blank"
                                    class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">Lihat
                                    File APL.02</a>
                            @else
                                <span class="text-gray-900 dark:text-gray-100">Tidak ada file.</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Scan KTP</dt>
                        <dd class="mt-1 text-sm">
                            @if ($asesi->student->foto_ktp)
                                <a href="{{ asset('storage/' . $asesi->student->foto_ktp) }}" target="_blank"
                                    class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">Lihat
                                    KTP</a>
                            @else
                                <span class="text-gray-900 dark:text-gray-100">Tidak ada file.</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Scan KTM</dt>
                        <dd class="mt-1 text-sm">
                            @if ($asesi->student->foto_ktm)
                                <a href="{{ asset('storage/' . $asesi->student->foto_ktm) }}" target="_blank"
                                    class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">Lihat
                                    KTM</a>
                            @else
                                <span class="text-gray-900 dark:text-gray-100">Tidak ada file.</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Scan Kartu Hasil Studi
                        </dt>
                        <dd class="mt-1 text-sm">
                            @if ($asesi->student->foto_khs)
                                <a href="{{ asset('storage/' . $asesi->student->foto_khs) }}" target="_blank"
                                    class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">Lihat
                                    KHS</a>
                            @else
                                <span class="text-gray-900 dark:text-gray-100">Tidak ada file.</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Pasfoto</dt>
                        <dd class="mt-1 text-sm">
                            @if ($asesi->student->pas_foto)
                                <a href="{{ asset('storage/' . $asesi->student->pas_foto) }}" target="_blank"
                                    class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">Lihat
                                    Pasfoto</a>
                            @else
                                <span class="text-gray-900 dark:text-gray-100">Tidak ada file.</span>
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Scan Surat Keterangan
                            Magang/PKL/MBKM</dt>
                        <dd class="mt-1 text-sm">
                            @php
                                $suratMagangAttachmentFiles = $asesi->asesiattachmentfiles->where(
                                    'type',
                                    'surat_ket_magang',
                                );
                            @endphp
                            @if ($suratMagangAttachmentFiles->isNotEmpty())
                                <ul class="list-disc list-inside">
                                    @foreach ($suratMagangAttachmentFiles as $attachment)
                                        <li>
                                            <a href="{{ asset('storage/' . $attachment->path_file) }}" target="_blank"
                                                class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">Lihat</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-gray-900 dark:text-gray-100">Tidak ada file.</span>
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Scan Sertifikat
                            Pelatihan</dt>
                        <dd class="mt-1 text-sm">
                            @php
                                $sertifPelatihanAttachmentFiles = $asesi->asesiattachmentfiles->where(
                                    'type',
                                    'sertif_pelatihan',
                                );
                            @endphp
                            @if ($sertifPelatihanAttachmentFiles->isNotEmpty())
                                <ul class="list-disc list-inside">
                                    @foreach ($sertifPelatihanAttachmentFiles as $attachment)
                                        <li>
                                            <a href="{{ asset('storage/' . $attachment->path_file) }}" target="_blank"
                                                class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">Lihat</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-gray-900 dark:text-gray-100">Tidak ada file.</span>
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Dokumen Pendukung
                            Lainnya</dt>
                        <dd class="mt-1 text-sm">
                            @php
                                $dokPendukungAttachmentFiles = $asesi->asesiattachmentfiles->where(
                                    'type',
                                    'dok_pendukung_lain',
                                );
                            @endphp
                            @if ($dokPendukungAttachmentFiles->isNotEmpty())
                                <ul class="list-disc list-inside">
                                    @foreach ($dokPendukungAttachmentFiles as $attachment)
                                        <li>
                                            <a href="{{ asset('storage/' . $attachment->path_file) }}" target="_blank"
                                                class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">Lihat</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-gray-900 dark:text-gray-100">Tidak ada file.</span>
                            @endif
                        </dd>
                    </div>
                </div>
            </div>
            <div>
                <h3 class="text-md font-semibold dark:text-gray-300 mb-2 border-b pb-1 border-gray-700 mt-6">E. Status
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Status Asesi</dt>
                        <dd class="mt-1 text-sm mr-1">
                            @if ($asesi->status == 'daftar')
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100">
                                    {{ $asesi->status }}
                                </span>
                            @elseif($asesi->status == 'perlu_perbaikan_berkas')
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100">
                                    Perlu perbaikan berkas
                                </span>
                            @elseif($asesi->status == 'ditolak')
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">
                                    Ditolak
                                </span>
                            @elseif($asesi->status == 'dilanjutkan_asesmen')
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                    Dilanjutkan ke asesmen
                                </span>
                            @else
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200">
                                    {{ $asesi->status ?? 'N/A' }}
                                </span>
                            @endif
                            <button type="button"
                                @click="showConfirmEditStatusModal = true; editStatusAsesiUrl = '{{ route('admin.applicants.update-status', [$asesi->id, $asesi->sertification_id]) }}'"
                                class="inline-flex items-center px-2 py-1 text-sm font-medium text-white bg-yellow-500 hover:bg-yellow-600 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-700 transition ease-in-out duration-150 cursor-pointer">
                                <x-bxs-edit class="w-3 h-3 mr-2" />
                                Ubah Status
                            </button>
                        </dd>
                    </div>

                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Status Pembayaran</dt>
                        <dd class="mt-1 text-sm mr-1">
                            @php
                                $latestTransaction = $asesi->transaction->sortByDesc('created_at')->first();
                            @endphp
                            @if ($latestTransaction)
                                @if ($latestTransaction?->status == 'belum bayar')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100">
                                        belum bayar
                                    </span>
                                    {{-- pembayaran manual dan belum diverifikasi admin --}}
                                @elseif(
                                    $latestTransaction?->tipe == 'manual' &&
                                        $latestTransaction?->bukti_pembayaran &&
                                        $latestTransaction?->status == 'pending')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100">
                                        pending
                                    </span>
                                    {{-- pembayaran manual dan diterima --}}
                                @elseif($latestTransaction?->tipe == 'manual' && $latestTransaction?->status == 'bukti pembayaran terverifikasi')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100">
                                        sudah bayar
                                    </span>
                                    {{-- pembayaran manual dan ditolak --}}
                                @elseif($latestTransaction?->tipe == 'manual' && $latestTransaction?->status == 'bukti pembayaran ditolak')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100">
                                        bukti pembayaran ditolak
                                    </span>
                                @elseif($asesi->status == 'dilanjutkan asesmen')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                        sudah bayar
                                    </span>
                                @endif
                            @else
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100">
                                    Belum submit bukti pembayaran
                                </span>
                            @endif
                            <button type="button" @if (!$latestTransaction) disabled @endif
                                @click="showConfirmUpdateStatusPembayaranModal = true; editStatusPembayaranUrl = '{{ route('admin.applicants.update-payment-status', $asesi->id) }}'"
                                class="inline-flex items-center px-2 py-1 text-sm font-medium text-white bg-yellow-500 hover:bg-yellow-600 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-700 transition ease-in-out duration-150 cursor-pointer disabled:opacity-50 disabled:cursor-default">
                                <x-bxs-edit class="w-3 h-3 mr-2" />
                                Ubah Status
                            </button>
                        </dd>
                    </div>
                </div>
                {{-- Bagian Aksi Keputusan Untuk Pendaftar Baru --}}
                {{-- @if ($asesi->status == 'daftar')
                    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-3">Tindakan Keputusan:
                        </h4>
                        <div class="flex items-center justify-start space-x-3">
                            <form class="inline-block"
                                action="{{ route('admin.applicants.update-status', [$asesi->id, $asesi->sertification_id]) }}"
                                method="POST"
                                onsubmit="return confirm('Anda yakin ingin melanjutkan asesi ini ke tahap asesmen?');">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="dilanjutkan asesmen">
                                <button type="submit"
                                    class="flex items-center gap-2 cursor-pointer px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-700">
                                    <!-- Ikon Centang -->
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Lanjutkan Asesmen
                                </button>
                            </form>

                            <form class="inline-block"
                                action="{{ route('admin.applicants.update-payment-status', [$asesi->id, $asesi->sertification_id]) }}"
                                method="POST"
                                onsubmit="return confirm('Anda yakin ingin menolak asesi ini? Tindakan ini tidak dapat diurungkan.');">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="tidak bisa dilanjutkan">
                                <button type="submit"
                                    class="flex items-center gap-2 cursor-pointer px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-red-700 dark:hover:bg-red-800 dark:focus:ring-red-800">
                                    <!-- Ikon Silang -->
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Tolak (Tidak bisa lanjut ke asesmen)
                                </button>
                            </form>
                        </div>
                    </div>
                @endif --}}
                {{-- Bagian Aksi Keputusan Untuk Asesi yang sudah melakukan pembayaran secara manual dan menyertakan bukti pembayaran --}}
                {{-- @if ($latestTransaction?->tipe == 'manual' && $latestTransaction?->bukti_pembayaran && $latestTransaction?->status == 'pending')
                    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-3">Tindakan Keputusan:
                        </h4>
                        <div class="flex items-center justify-start space-x-3">
                            
                            <form class="inline-block"
                                action="{{ route('admin.applicants.update-payment-status', $asesi->id) }}"
                                method="POST"
                                onsubmit="return confirm('Anda yakin ingin melanjutkan asesi ini ke tahap asesmen?');">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="bukti pembayaran terverifikasi">
                                <button type="submit"
                                    class="flex items-center gap-2 cursor-pointer px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-700">
                                    <!-- Ikon Centang -->
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Bukti Pembayaran Terverifikasi
                                </button>
                            </form>

                            <form class="inline-block"
                                action="{{ route('admin.applicants.update-payment-status', $asesi->id) }}"
                                method="POST"
                                onsubmit="return confirm('Anda yakin ingin menolak asesi ini? Tindakan ini tidak dapat diurungkan.');">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="bukti pembayaran ditolak">
                                <button type="submit"
                                    class="flex items-center gap-2 cursor-pointer px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-red-700 dark:hover:bg-red-800 dark:focus:ring-red-800">
                                    <!-- Ikon Silang -->
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Bukti Pembayaran Ditolak
                                </button>
                            </form>
                        </div>
                    </div>
                @endif --}}
            </div>
        </div>
        <!-- Modal Konfirmasi Ubah status asesi -->
        <div x-show="showConfirmEditStatusModal" x-data="{ showCatatanStatus: false}"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 " style="display: none;">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-4 w-[280px]">
                <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-6">Konfirmasi Ubah Status Asesi</h3>

                <form :action="editStatusAsesiUrl" method="POST">
                    @csrf
                    @method('PATCH')
                    <div id="ubah status asesi">
                        <label for="status asesi"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih status</label>
                        <select required name="status"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option value="" disabled selected>--Silahkan pilih status--</option>
                            <option class="" value="ditolak">
                                Ditolak
                            </option>
                            <option @click="showCatatanStatus = true" class="" value="perlu_perbaikan_berkas">
                                Perlu Perbaikan Berkas
                            </option>
                            <option class="" value="dilanjutkan_asesmen">
                                Dilanjutkan ke asesmen
                            </option>
                        </select>
                        <div x-show="showCatatanStatus">
                            <label for="catatan"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan</label>
                            <textarea name="catatan_status" id="" cols="30" rows="10"></textarea>
                        </div>
                        @error('status')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mt-4 flex justify-end space-x-2">
                        <button type="button" @click="showConfirmEditStatusModal = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md cursor-pointer">
                            Ubah
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Modal Konfirmasi Ubah status pembayaran asesi -->
        <div x-show="showConfirmUpdateStatusPembayaranModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 " style="display: none;">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-4 w-[280px]">
                <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-6">Konfirmasi Ubah Status Pembayaran
                </h3>
                <form :action="editStatusPembayaranUrl" method="POST">
                    @csrf
                    @method('PATCH')
                    <div id="ubah status asesi">
                        <label for="skema_asesor"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih status</label>
                        <select required name="status"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option value="" disabled selected>--Silahkan pilih status--</option>
                            <option class="" value="ditolak">
                                Bukti Pembayaran Ditolak
                            </option>
                            <option class="" value="paid">
                                Bukti Pembayaran Diterima
                            </option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mt-4 flex justify-end space-x-2">
                        <button type="button" @click="showConfirmUpdateStatusPembayaranModal = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md cursor-pointer">
                            Ubah
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
