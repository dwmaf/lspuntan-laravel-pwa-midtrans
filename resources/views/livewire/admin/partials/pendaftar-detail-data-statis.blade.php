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
                    $kartuHasilStudiFiles = $asesi->student->studentattachmentfiles->where('type', 'kartu_hasil_studi');
                @endphp
                @if ($kartuHasilStudiFiles->isNotEmpty())
                    <ul class="">
                        @foreach ($kartuHasilStudiFiles as $attachment)
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
                    $suratMagangAttachmentFiles = $asesi->asesiattachmentfiles->where('type', 'surat_ket_magang');
                @endphp
                @if ($suratMagangAttachmentFiles->isNotEmpty())
                    <ul class="">
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
                    $sertifPelatihanAttachmentFiles = $asesi->asesiattachmentfiles->where('type', 'sertif_pelatihan');
                @endphp
                @if ($sertifPelatihanAttachmentFiles->isNotEmpty())
                    <ul class="">
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
                    $dokPendukungAttachmentFiles = $asesi->asesiattachmentfiles->where('type', 'dok_pendukung_lain');
                @endphp
                @if ($dokPendukungAttachmentFiles->isNotEmpty())
                    <ul class="">
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
