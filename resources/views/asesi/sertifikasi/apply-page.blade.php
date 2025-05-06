<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>

    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">

        <h4
            class="inline-block bg-blue-600 text-gray-800 dark:text-white px-4 py-2 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-500 transition">
            Daftar Sertifikasi {{ $sertification->skema->nama_skema }}</h4>
        <h3>a. Data Pribadi</h3>
        <form action="/asesi" class="mt-4 flex flex-col gap-2" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="text" value="daftar" hidden name="status">
            <input type="text" value="{{ $sertification->id }}" hidden name="sertification_id">
            <div id="email">
                <label for="" class="text-sm font-medium text-gray-600 dark:text-gray-300">Email<span style="color: red">*</span>
                </label>
                <input type="text" name="email" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
            </div>
            <div id="nama">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Nama Lengkap
                    (Sesuai KTP)<span style="color: red">*</span>
                </label>
                <input type="text" name="name" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
            </div>
            <div id="nik KTP">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">No. KTP<span style="color: red">*</span>
                </label>
                <input type="text" name="nik" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
            </div>
            <div id="tempat, tgl lahir">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Tempat/Tanggal
                    Lahir<span style="color: red">*</span>
                </label>
                <input type="text" name="tmpt_tgl_lhr" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
            </div>
            <div id="kelamin">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Jenis
                    Kelamin<span style="color: red">*</span></label>
                <select name="kelamin" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
                    <option value="Laki-laki">Laki-Laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
            </div>
            <div id="kebangsaan">
                <label for=""
                    class="block text-sm font-medium text-gray-600 dark:text-gray-300">Kebangsaan<span style="color: red">*</span></label>
                <input type="text" name="kebangsaan" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
            </div>
            <div id="no tlp HP">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">No. Tlp
                    HP(WA)<span style="color: red">*</span></label>
                <input type="text" name="no_tlp_hp" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
            </div>
            <div id="no tlp rumah">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">No. Tlp
                    Rumah</label>
                <input type="text" name="no_tlp_rmh"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
            </div>
            <div id="no tlp kantor">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">No. Tlp
                    Kantor</label>
                <input type="text" name="no_tlp_kntr"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
            </div>
            
            <div id="Kualifikasi Pendidikan (tulis: Mahasiswa S1)*">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Kualifikasi
                    Pendidikan (tulis: Mahasiswa S1)<span style="color: red">*</span></label>
                <input type="text" name="kualifikasi_pendidikan" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
            </div>

            <h3>a. Data Pekerjaan Sekarang</h3>
            <div id="nama institusi/perusahaan">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Nama Institusi/Perusahaan</label>
                <input type="text" name="nama_institusi"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
            </div>
            <div id="jabatan">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Jabatan
                    </label>
                <input type="text" name="jabatan"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
            </div>
            <div id="alamat kantor">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Alamat Kantor
                    </label>
                <input type="text" name="alamat_kantor"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
            </div>
            <div id="No telepon/fax/email">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">No. Tlp/Email/Fax
                    </label>
                <input type="text" name="no_tlp_email_fax"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
            </div>
            
            <h3>c. Data Sertifikasi</h3>
            <div id="tujuan sertifikasi">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Tujuan
                    Sertifikasi<span style="color: red">*</span></label>
                <select name="tujuan_sert" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
                    <option value="Sertifikasi">Sertifikasi</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            <div id="Mata kuliah terkait dan nilainya">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Mata Kuliah terkait Skema Sertifikasi dan Nilai yang diperoleh<span style="color: red">*</span>
                    </label>
                <input type="text" name="makul_nilai" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
            </div>

            <h3>d. Bukti Kelengkapan</h3>
            <div id="file apl 1">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Form APL.01. Permohonan Sertifikasi Kompetensi (file MS-Word yang telah diisi dan dilengkapi dengan tanda tangan ukuran file maksimal 2 MB)<span style="color: red">*</span>
                </label>
                <input type="file" name="apl_1" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
            </div>
            <div id="file apl 2">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Form APL.02. Asesmen Mandiri (file MS-Word yang telah diisi dan dilengkapi dengan tanda tangan ukuran file maksimal 3 MB)<span style="color: red">*</span>
                </label>
                <input type="file" name="apl_2" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
            </div>
            <div id="foto_ktp">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Scan KTP (ukuran file maksimal 1 MB)<span style="color: red">*</span>
                </label>
                <input type="file" name="foto_ktp" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
            </div>
            <div id="foto_ktm">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Scan KTM (ukuran file maksimal 1 MB)<span style="color: red">*</span>
                </label>
                <input type="file" name="foto_ktm" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
            </div>
            <div id="foto_khs">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Scan Kartu Hasil Studi (ukuran file maksimal 1 MB)<span style="color: red">*</span>
                </label>
                <input type="file" name="foto_khs" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
            </div>
            <div id="pas foto">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Pasfoto terbaru dengan latar belakang merah, berukuran 4x6   (ukuran file maksimal 1 MB)<span style="color: red">*</span>
                </label>
                <input type="file" name="pas_foto" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
            </div>
            <div id="surat keterangan magang">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Scan Surat Keterangan Magang/PKL/MBKM (maks 5, ukuran file maksimal 3 MB)
                </label>
                <input type="file" name="surat_ket_magang[]" multiple
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
            </div>
            <div id="sertif pelatihan">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Scan Sertifikat Pelatihan (maks 5, ukuran file maksimal 3 MB)
                </label>
                <input type="file" name="sertif_pelatihan[]" multiple
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
            </div>
            <div id="dokumen pendukung lainnya">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Dokumen pendukung lainnya: dapat berupa Laporan kegiatan PKL/Magang/MBKM/Publikasi Jurnal/dll (maks 5, ukuran file maksimal 5 MB)
                </label>
                <input type="file" name="dok_pendukung_lain[]" multiple
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
            </div>
            <button type="submit"
                class="bg-blue-700 text-gray-800 dark:text-white px-4 py-2 rounded-lg hover:bg-green-700 dark:hover:bg-green-500 dark:bg-green-800 transition self-end">Mulai</button>
        </form>
        <h4><span style="color: red">*</span>&rpar; Wajib diisi</h4>
    </div>
</x-app-layout>
