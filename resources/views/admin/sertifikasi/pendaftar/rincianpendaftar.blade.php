<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    @include('layouts.admin-sertifikasi-menu')
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
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
                        <dd class="mt-1 text-sm">
                            @if ($asesi->status == 'daftar')
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100">
                                    {{ $asesi->status }}
                                </span>
                            @elseif($asesi->status == 'tidak bisa dilanjutkan')
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">
                                    {{ $asesi->status }}
                                </span>
                            @elseif($asesi->status == 'dilanjutkan asesmen')
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                    {{ $asesi->status }}
                                </span>
                            @else
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200">
                                    {{ $asesi->status ?? 'N/A' }}
                                </span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Status Pembayaran</dt>
                        <dd class="mt-1 text-sm">
                            @if ($asesi->status == 'daftar')
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100">
                                    belum bayar
                                </span>
                            @elseif($asesi->status == 'pending')
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100">
                                    pending
                                </span>
                            @elseif($asesi->status == 'dilanjutkan asesmen')
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                    sudah bayar
                                </span>
                            @endif
                        </dd>
                    </div>
                </div>
                {{-- Bagian Aksi Keputusan Untuk Pendaftar Baru --}}
                @if ($asesi->status == 'daftar')
                    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-3">Tindakan Keputusan:
                        </h4>
                        <div class="flex items-center justify-start space-x-3">
                            {{-- Tombol Lanjutkan --}}
                            <form class="inline-block"
                                action="{{ route('update_status',[$asesi->id, $asesi->sertification_id]) }}"
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

                            {{-- Tombol Tidak Melanjutkan --}}
                            <form class="inline-block"
                                action="{{ route('update_status',[$asesi->id, $asesi->sertification_id]) }}"
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
                @endif
                {{-- Bagian Aksi Keputusan Untuk Asesi yang sudah melakukan pembayaran dan menyertakan bukti pembayaran --}}
                @if ($asesi->status == 'daftar')
                    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-3">Tindakan Keputusan:
                        </h4>
                        <div class="flex items-center justify-start space-x-3">
                            {{-- Tombol Lanjutkan --}}
                            <form class="inline-block"
                                action="{{ route("update_status_bayar",$asesi->id) }}"
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
                                    Bukti Pembayaran Terverifikasi
                                </button>
                            </form>

                            {{-- Tombol Tidak Melanjutkan --}}
                            <form class="inline-block"
                                action="{{ route("update_status_bayar",$asesi->id) }}"
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
                                    Bukti Pembayaran Ditolak
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
