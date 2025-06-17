<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    @include('layouts.asesi-sertifikasi-menu')
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <h4 class="text-gray-800 dark:text-white mb-2 rounded-lg">
            Daftar Sertifikasi {{ $sertification->skema->nama_skema }}</h4>
        <h3 class="dark:text-gray-300">a. Data Pribadi</h3>
        <form action="{{ route('apply_sertifikasi.store') }}" class="mt-6 space-y-6" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="text" value="{{ $sertification->id }}" hidden name="sertification_id">
            <input type="text" value="{{ $student->id }}" hidden name="student_id">
            <div id="nama">
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Nama Lengkap
                    (Sesuai KTP)<span style="color: red">*</span>
                </label>
                <x-text-input name="name" type="text" class="mt-1 block w-full" :value="old('name', $student?->name)" required />
            </div>
            <div id="nik KTP">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">No. KTP<span
                        style="color: red">*</span>
                </label>
                <x-text-input id="nik" name="nik" type="text" class="mt-1 block w-full" :value="old('nik', $student?->nik)"
                    required />

            </div>
            <div id="tempat lahir">
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Tempat
                    Lahir<span style="color: red">*</span>
                </label>
                <x-text-input id="tmpt_lhr" name="tmpt_lhr" type="text" class="mt-1 block w-full" :value="old('tmpt_lhr', $student?->tmpt_lhr)"
                    required />
            </div>
            <div id=" tgl lahir">
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Tanggal
                    Lahir<span style="color: red">*</span>
                </label>
                <x-text-input id="tgl_lhr" name="tgl_lhr" type="date" class="mt-1 block w-full" :value="old('tgl_lhr', $student?->tgl_lhr)"
                    required />
            </div>
            <div id="kelamin">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Jenis
                    Kelamin<span style="color: red">*</span></label>
                <select name="kelamin" required
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <option value="Laki-laki" {{ old('kelamin', $student?->kelamin) == 'Laki-laki' ? 'selected' : '' }}>
                        Laki-Laki
                    </option>
                    <option value="Perempuan" {{ old('kelamin', $student?->kelamin) == 'Perempuan' ? 'selected' : '' }}>
                        Perempuan
                    </option>
                </select>

            </div>
            <div id="kebangsaan">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Kebangsaan<span
                        style="color: red">*</span></label>
                <x-text-input id="kebangsaan" name="kebangsaan" type="text" class="mt-1 block w-full"
                    :value="old('kebangsaan', $student?->kebangsaan)" required />
            </div>
            <div id="no tlp HP">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">No. Tlp
                    HP(WA)<span style="color: red">*</span></label>
                <x-text-input id="no_tlp_hp" name="no_tlp_hp" type="text" class="mt-1 block w-full" :value="old('no_tlp_hp', $student?->no_tlp_hp)"
                    required />
            </div>
            <div id="no tlp rumah">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">No. Tlp
                    Rumah</label>
                <x-text-input id="no_tlp_rmh" name="no_tlp_rmh" type="text" class="mt-1 block w-full"
                    :value="old('no_tlp_rmh', $student?->no_tlp_rmh)" />
            </div>
            <div id="no tlp kantor">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">No. Tlp
                    Kantor</label>
                <x-text-input id="no_tlp_kntr" name="no_tlp_kntr" type="text" class="mt-1 block w-full"
                    :value="old('no_tlp_kntr', $student?->no_tlp_kntr)" />
            </div>

            <div id="Kualifikasi Pendidikan (tulis: Mahasiswa S1)*">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Kualifikasi
                    Pendidikan (tulis: Mahasiswa S1)<span style="color: red">*</span></label>
                <x-text-input id="kualifikasi_pendidikan" name="kualifikasi_pendidikan" type="text"
                    class="mt-1 block w-full" :value="old('kualifikasi_pendidikan', $student?->kualifikasi_pendidikan)" required />
            </div>

            <h3 class="dark:text-gray-300">a. Data Pekerjaan Sekarang</h3>
            <div id="nama institusi/perusahaan">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Nama
                    Institusi/Perusahaan</label>
                <x-text-input id="nama_institusi" name="nama_institusi" type="text" class="mt-1 block w-full"
                    :value="old('nama_institusi', $student?->nama_institusi)" />
            </div>
            <div id="jabatan">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Jabatan
                </label>
                <x-text-input id="jabatan" name="jabatan" type="text" class="mt-1 block w-full"
                    :value="old('jabatan', $student?->jabatan)" />
            </div>
            <div id="alamat kantor">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Alamat Kantor
                </label>
                <x-text-input id="alamat_kantor" name="alamat_kantor" type="text" class="mt-1 block w-full"
                    :value="old('alamat_kantor', $student?->alamat_kantor)" />
            </div>
            <div id="No telepon/fax/email">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">No.
                    Tlp/Email/Fax
                </label>
                <x-text-input id="no_tlp_email_fax" name="no_tlp_email_fax" type="text" class="mt-1 block w-full"
                    :value="old('no_tlp_email_fax', $student?->no_tlp_email_fax)" />
            </div>

            <h3 class="dark:text-gray-300">c. Data Sertifikasi</h3>
            <div id="tujuan sertifikasi">
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Tujuan
                    Sertifikasi<span style="color: red">*</span></label>
                <select name="tujuan_sert" required
                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:border-indigo-600 focus:ring-indigo-600 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <option value="" disabled selected>--Silahkan pilih asesor dan skema--</option>
                    <option value="Sertifikasi">Sertifikasi</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            <div id="Mata kuliah terkait dan nilainya">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Mata Kuliah
                    terkait Skema Sertifikasi dan Nilai yang diperoleh<span style="color: red">*</span>
                </label>
                <x-text-input name="makul_nilai" type="text" class="mt-1 block w-full" />
            </div>

            <h3 class="dark:text-gray-300">d. Bukti Kelengkapan</h3>
            <div id="file apl 1">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300 ">Form APL.01.
                    Permohonan Sertifikasi Kompetensi (file MS-Word yang telah diisi dan dilengkapi dengan tanda tangan
                    ukuran file maksimal 2 MB)<span style="color: red">*</span>
                </label>
                <input type="file" name="apl_1" required
                    class="w-full px-3 py-2 dark:text-gray-300 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-hidden dark:bg-gray-900 focus-ring-2 focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">
            </div>
            <div id="file apl 2">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Form APL.02.
                    Asesmen Mandiri (file MS-Word yang telah diisi dan dilengkapi dengan tanda tangan ukuran file
                    maksimal 3 MB)<span style="color: red">*</span>
                </label>
                <input type="file" name="apl_2" required
                    class="w-full px-3 py-2 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-hidden dark:bg-gray-900 focus-ring-2 focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">
            </div>
            <div id="foto_ktp">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Scan KTP
                    (ukuran file maksimal 1 MB)<span style="color: red">*</span>
                </label>
                <!-- Feedback jika file sudah ada -->
                @if ($student->foto_ktp)
                    <p class="text-sm text-gray-500 mt-1">File sudah ada:
                        <a href="{{ asset('storage/' . $student->foto_ktp) }}" class="text-blue-500"
                            target="_blank">Lihat File</a>
                    </p>
                @endif
                <!-- Input file -->
                <input id="foto_ktp" name="foto_ktp" type="file"
                    class="w-full px-3 py-2 dark:text-gray-300 border border-gray-300 dark:border-gray-600  rounded-md focus:outline-hidden dark:bg-gray-900 focus-ring-2 focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                    @if (!$student->foto_ktp) required @endif>

            </div>
            <div id="foto_ktm">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Scan KTM
                    (ukuran file maksimal 1 MB)<span style="color: red">*</span>
                </label>
                <!-- Feedback jika file sudah ada -->
                @if ($student->foto_ktm)
                    <p class="text-sm text-gray-500 mt-1">File sudah ada:
                        <a href="{{ asset('storage/' . $student->foto_ktm) }}" class="text-blue-500"
                            target="_blank">Lihat File</a>
                    </p>
                @endif
                <!-- Input file -->
                <input id="foto_ktm" name="foto_ktm" type="file"
                    class="w-full px-3 py-2 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-hidden dark:bg-gray-900 focus-ring-2 focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                    @if (!$student->foto_ktm) required @endif>
            </div>
            <div id="foto_khs">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Scan Kartu
                    Hasil Studi (ukuran file maksimal 1 MB)<span style="color: red">*</span>
                </label>
                <!-- Feedback jika file sudah ada -->
                @if ($student->foto_khs)
                    <p class="text-sm text-gray-500 mt-1">File sudah ada:
                        <a href="{{ asset('storage/' . $student->foto_khs) }}" class="text-blue-500"
                            target="_blank">Lihat File</a>
                    </p>
                @endif
                <!-- Input file -->
                <input id="foto_khs" name="foto_khs" type="file"
                    class="w-full px-3 py-2 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-hidden dark:bg-gray-900 focus-ring-2 focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                    @if (!$student->foto_khs) required @endif>
            </div>
            <div id="pas foto">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Pasfoto
                    terbaru dengan latar belakang merah, berukuran 4x6 (ukuran file maksimal 1 MB)<span
                        style="color: red">*</span>
                </label>
                <!-- Feedback jika file sudah ada -->
                @if ($student->pas_foto)
                    <p class="text-sm text-gray-500 mt-1">File sudah ada:
                        <a href="{{ asset('storage/' . $student->pas_foto) }}" class="text-blue-500"
                            target="_blank">Lihat File</a>
                    </p>
                @endif
                <!-- Input file -->
                <input id="pas_foto" name="pas_foto" type="file"
                    class="w-full px-3 py-2 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-hidden dark:bg-gray-900 focus-ring-2 focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                    @if (!$student->pas_foto) required @endif>
            </div>
            <div id="surat keterangan magang">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Scan Surat
                    Keterangan Magang/PKL/MBKM (maks 5, ukuran file maksimal 3 MB)
                </label>
                
                <input type="file" name="surat_ket_magang[]" multiple
                    class="w-full px-3 py-2 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-hidden dark:bg-gray-900 focus-ring-2 focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">
                @error('surat_ket_magang.*')
                    {{-- Menampilkan error untuk setiap file dalam array --}}
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                @error('surat_ket_magang')
                    {{-- Menampilkan error umum untuk arraynya (misal, jumlah file) --}}
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div id="sertif pelatihan">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Scan
                    Sertifikat Pelatihan (maks 5, ukuran file maksimal 3 MB)
                </label>
                
                <input type="file" name="sertif_pelatihan[]" multiple
                    class="w-full px-3 py-2 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-hidden dark:bg-gray-900 focus-ring-2 focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">
                @error('sertif_pelatihan.*')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                @error('sertif_pelatihan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div id="dokumen pendukung lainnya">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Dokumen
                    pendukung lainnya: dapat berupa Laporan kegiatan PKL/Magang/MBKM/Publikasi Jurnal/dll (maks 5,
                    ukuran file maksimal 5 MB)
                </label>
                
                <input type="file" name="dok_pendukung_lain[]" multiple
                    class="w-full px-3 py-2 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-hidden dark:bg-gray-900 focus-ring-2 focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">
                @error('dok_pendukung_lain.*')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                @error('dok_pendukung_lain')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit"
                class="self-start font-medium bg-blue-500 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-700 cursor-pointer">Daftar</button>
        </form>
        <h4 class="dark:text-white"><span class="text-red-600">*</span>&rpar; Wajib diisi</h4>
    </div>
</x-app-layout>
@push('scripts')
    
@endpush
