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

    @if ($selectedAsesi)
        {{-- ============================================= --}}
        {{-- ============ TAMPILAN DETAIL ASESI ============ --}}
        {{-- ============================================= --}}
        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Rincian Pendaftar: {{ $selectedAsesi->student->user->name }}
                </h3>
                <button wire:click="backToList"
                    class="text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">&larr;
                    Kembali ke Daftar</button>
            </div>

            {{-- Tampilan Detail Utama (muncul saat tidak edit sertifikat) --}}
            <div x-show="!$wire.isEditingCertificate" x-transition.opacity>
                <div>
                    <h3 class="text-md font-semibold dark:text-gray-300 mb-2 border-b pb-1 border-gray-700">A. Data
                        Pribadi
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                        <div>
                            <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Nama Lengkap (Sesuai
                                KTP)</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $asesi->student?->user->name ?: 'Tidak diisi' }}
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
                                <ul class="">
                                    @foreach ($asesi?->makulnilais as $makulnilai)
                                        <li>
                                            {{ $makulnilai->nama_makul }} : {{ $makulnilai->nilai_makul }}
                                        </li>
                                    @endforeach
                                </ul>
                            </dd>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-md font-semibold dark:text-gray-300 mb-2 border-b pb-1 border-gray-700 mt-6">D.
                        Bukti
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
                            <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Scan Kartu Hasil
                                Studi
                            </dt>
                            <dd class="mt-1 text-sm">
                                @php
                                    $kartuHasilStudiFiles = $asesi->student->studentattachmentfile->where(
                                        'type',
                                        'kartu_hasil_studi',
                                    );
                                @endphp
                                @if ($kartuHasilStudiFiles->isNotEmpty())
                                    <ul class="">
                                        @foreach ($kartuHasilStudiFiles as $attachment)
                                            <li>
                                                <a href="{{ asset('storage/' . $attachment->path_file) }}"
                                                    target="_blank"
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
                                    <ul class="">
                                        @foreach ($suratMagangAttachmentFiles as $attachment)
                                            <li>
                                                <a href="{{ asset('storage/' . $attachment->path_file) }}"
                                                    target="_blank"
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
                                    <ul class="">
                                        @foreach ($sertifPelatihanAttachmentFiles as $attachment)
                                            <li>
                                                <a href="{{ asset('storage/' . $attachment->path_file) }}"
                                                    target="_blank"
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
                                    <ul class="">
                                        @foreach ($dokPendukungAttachmentFiles as $attachment)
                                            <li>
                                                <a href="{{ asset('storage/' . $attachment->path_file) }}"
                                                    target="_blank"
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
                {{-- Contoh Bagian E. Status --}}
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
                            @elseif($asesi->status == 'lulus_sertifikasi')
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                    Lulus Sertifikasi
                                </span>
                            @else
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200">
                                    {{ $asesi->status ?? 'N/A' }}
                                </span>
                            @endif
                            <button type="button" wire:click="openStatusAsesiModal"
                                class="inline-flex items-center px-2 py-1 text-sm ...">
                                <x-bxs-edit class="w-3 h-3 mr-2" /> Ubah Status
                            </button>
                        </dd>
                    </div>
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Status Pembayaran</dt>
                        @php $latestTransaction = $selectedAsesi->transaction->sortByDesc('created_at')->first(); @endphp
                        <dd @class([
                            'mt-1',
                            'text-sm',
                            'rounded-lg',
                            'p-2',
                            'border', // Kelas dasar
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
                                        <a href="{{ asset('storage/' . $latestTransaction->bukti_bayar) }}"
                                            target="_blank"
                                            class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">Lihat
                                            File</a>
                                    </div>
                                @elseif($latestTransaction?->tipe == 'manual' && $latestTransaction?->status == 'bukti_pembayaran_terverifikasi')
                                    <div class="space-x-1">
                                        <span
                                            class="px-1 inline-flex text-xs leading-5 font-semibold rounded-sm bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                            Pembayaran Terverifikasi
                                        </span>
                                        <a href="{{ asset('storage/' . $latestTransaction->bukti_bayar) }}"
                                            target="_blank"
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
                                        <a href="{{ asset('storage/' . $latestTransaction->bukti_bayar) }}"
                                            target="_blank"
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
                                    <button type="button"
                                        wire:click="openStatusPembayaranModal({{ $latestTransaction->id }})"
                                        class="inline-flex items-center px-2 py-1 text-sm ...">
                                        <x-bxs-edit class="w-3 h-3 mr-2" /> Ubah Status
                                    </button>
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
                            @if ($selectedAsesi->sertifikat)
                                <button type="button" wire:click="enterCertificateEditMode"
                                    class="inline-flex items-center ...">
                                    <x-bxs-edit class="w-3 h-3 mr-2" /> Ubah Data
                                </button>
                            @elseif($selectedAsesi->status == 'lulus_sertifikasi')
                                <button type="button" wire:click="enterCertificateEditMode"
                                    class="inline-flex items-center ...">
                                    <x-fas-upload class="w-3 h-3 mr-2" /> Upload Sertifikat
                                </button>
                            @endif
                        </dd>
                    </div>
                </div>

            </div>

            {{-- Tampilan Form Edit Sertifikat --}}
            <div x-show="isEditingCertificate" x-transition.opacity style="display: none;">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        @if ($asesi->sertifikat)
                            Ubah Data Sertifikat
                        @else
                            Upload Sertifikat
                        @endif
                    </h3>
                    {{-- PERBAIKAN 3: Tombol Batal untuk kembali ke mode view --}}
                    <button type="button" @click="isEditingCertificate = false"
                        class="text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                        &times; Batal
                    </button>
                </div>
                <p class="my-1 text-sm text-gray-600 dark:text-gray-400">Untuk: <span
                        class="font-semibold">{{ $asesi->student->user->name }}</span></p>

                <form
                    action="{{ route('admin.sertifikasi.pendaftar.upload-certificate.update', [$asesi->id, $sertification->id]) }}"
                    method="POST" enctype="multipart/form-data" class="space-y-6">
                    @method('PATCH')
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nomor_seri"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor
                                Seri</label>
                            <input required type="text" name="nomor_seri" id="nomor_seri"
                                value="{{ old('nomor_seri', $asesi->sertifikat?->nomor_seri) }}"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="nomor_sertifikat"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor
                                Sertifikat</label>
                            <input required type="text" name="nomor_sertifikat" id="nomor_sertifikat"
                                value="{{ old('nomor_sertifikat', $asesi->sertifikat?->nomor_sertifikat) }}"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="nomor_registrasi"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor
                                Registrasi</label>
                            <input required type="text" name="nomor_registrasi" id="nomor_registrasi"
                                value="{{ old('nomor_registrasi', $asesi->sertifikat?->nomor_registrasi) }}"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-md shadow-sm">
                        </div>
                    </div>

                    {{-- Input untuk Tanggal --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="tanggal_terbit"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal
                                Terbit</label>
                            <input required type="date" name="tanggal_terbit" id="tanggal_terbit"
                                value="{{ old('tanggal_terbit', $asesi->sertifikat?->tanggal_terbit) }}" required
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="berlaku_hingga"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Berlaku
                                Hingga</label>
                            <input required type="date" name="berlaku_hingga" id="berlaku_hingga"
                                value="{{ old('berlaku_hingga', $asesi->sertifikat?->berlaku_hingga) }}" required
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-md shadow-sm">
                        </div>
                    </div>
                    {{-- Input untuk File --}}
                    <div>
                        <label for="sertifikat_asesi"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">File Sertifikat
                            (PDF, JPG, PNG)</label>
                        <input type="file" name="sertifikat_asesi" id="sertifikat_asesi"
                            @if (!$asesi->sertifikat) required @endif accept=".pdf,.jpg,.jpeg,.png,.webp"
                            class="cursor-pointer w-full px-3 py-2 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-hidden dark:bg-gray-900 focus-ring-2 focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">
                        @if ($asesi->sertifikat)
                            <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah file.</p>
                        @endif
                        @error('sertifikat_asesi')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="showUploadCertificateModal = false"
                            class="cursor-pointer px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">Batal</button>
                        <button type="submit"
                            class="cursor-pointer px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">Simpan
                            Sertifikat</button>
                    </div>
                </form>

            </div>

            <!-- Modal Konfirmasi Ubah status asesi -->
            <div x-show="showConfirmEditStatusModal" x-data="{ showCatatanStatus: false }"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 " style="display: none;">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-4 w-[280px]">
                    <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-6">Konfirmasi Ubah Status Asesi
                    </h3>

                    <form :action="editStatusAsesiUrl" method="POST">
                        @csrf
                        @method('PATCH')
                        <div id="ubah status asesi">
                            <label for="status asesi"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih status</label>
                            <select required name="status"
                                @change="showCatatanStatus = ($event.target.value === 'perlu_perbaikan_berkas')"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="" disabled selected>--Silahkan pilih status--</option>
                                <option class="" value="ditolak">
                                    Ditolak
                                </option>
                                <option class="" value="perlu_perbaikan_berkas">
                                    Perlu Perbaikan Berkas
                                </option>
                                <option class="" value="dilanjutkan_asesmen">
                                    Dilanjutkan ke asesmen
                                </option>
                                <option class="" value="lulus_sertifikasi">
                                    Lulus Sertifikasi
                                </option>
                            </select>
                            <div x-show="showCatatanStatus">
                                <label for="catatan_status"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mt-4">Catatan</label>
                                <textarea name="catatan_status" id="catatan_status" rows="5"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
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

            <!-- Modal Konfirmasi Ubah status pembayaran -->
            <div x-show="showConfirmUpdateStatusPembayaranModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 " style="display: none;">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-4 w-[280px]">
                    <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-6">Konfirmasi Ubah Status
                        Pembayaran
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
                                <option class="" value="bukti_pembayaran_ditolak">
                                    Bukti Pembayaran Ditolak
                                </option>
                                <option class="" value="bukti_pembayaran_terverifikasi">
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
    @else
        {{-- ============================================= --}}
        {{-- ============ TAMPILAN DAFTAR ASESI ============ --}}
        {{-- ============================================= --}}
        <div class="p-6 bg-white dark:bg-gray-800 shadow-xl rounded-lg ">
            <div class="px-6 py-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                    Daftar Pendaftar Skema: {{ $sertification->skema->nama_skema ?? 'Tidak Diketahui' }}
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full ">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                No
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Nama Asesi
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status Asesi
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status Pembayaran
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Aksi
                            </th>

                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 ">
                        @forelse ($sertification->asesi as $asesi)
                            <tr class="">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $loop->iteration }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $asesi->student->user->name ?? 'Nama Tidak Tersedia' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if ($asesi->status == 'daftar')
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100">
                                            Daftar
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
                                    @elseif($asesi->status == 'lulus_sertifikasi')
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                            Lulus Sertifikasi
                                        </span>
                                    @else
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200">
                                            {{ $asesi->status ?? 'N/A' }}
                                        </span>
                                    @endif
                                </td>
                                @php
                                    $latestTransaction = $asesi->transaction->sortByDesc('created_at')->first();
                                @endphp
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if ($latestTransaction)
                                        @if ($latestTransaction?->status == 'belum bayar')
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100">
                                                Belum Bayar
                                            </span>
                                        @elseif(
                                            $latestTransaction?->tipe == 'manual' &&
                                                $latestTransaction?->bukti_bayar &&
                                                $latestTransaction?->status == 'pending')
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100">
                                                Menunggu Verifikasi
                                            </span>
                                        @elseif($latestTransaction?->tipe == 'manual' && $latestTransaction?->status == 'bukti_pembayaran_terverifikasi')
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                                Pembayaran Terverifikasi
                                            </span>
                                        @elseif($latestTransaction?->tipe == 'manual' && $latestTransaction?->status == 'bukti_pembayaran_ditolak')
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">
                                                Bukti Pembayaran Ditolak
                                            </span>
                                        @endif
                                    @else
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100">
                                            Belum Submit Bukti Pembayaran
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    {{-- PERBAIKAN: Gunakan wire:click untuk memilih asesi --}}
                                    <button wire:click="selectAsesi({{ $asesi->id }})"
                                        class="cursor-pointer px-2 py-1 text-sm font-medium text-white bg-indigo-500 ...">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4"
                                    class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                    Tidak ada data pendaftar untuk skema ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
