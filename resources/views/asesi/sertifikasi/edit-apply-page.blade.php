<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    @include('layouts.asesi-sertifikasi-menu')
    <div class="p-6 bg-white dark:bg-gray-800 rounded-tr-lg rounded-bl-lg rounded-br-lg shadow-md">
        <h4 class="text-gray-800 dark:text-white mb-2 rounded-lg">
            Edit Data Asesi pada Sertifikasi {{ $sertification->skema->nama_skema }}</h4>
        <h3 class="dark:text-gray-300">a. Data Pribadi</h3>
        <form action="{{ route('asesi.applied.update', [$sertification->id, $asesi->id]) }}" class="mt-6 space-y-6"
            method="POST" enctype="multipart/form-data">
            @csrf
            @method('put')
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
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Tempat/Tanggal
                    Lahir<span style="color: red">*</span>
                </label>
                <x-text-input id="tmpt_lhr" name="tmpt_lhr" type="text" class="mt-1 block w-full" :value="old('tmpt_lhr', $student?->tmpt_lhr)"
                    required />
            </div>
            <div id="tgl lahir">
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Tempat/Tanggal
                    Lahir<span style="color: red">*</span>
                </label>
                <x-text-input id="tgl_lhr" name="tmpt_tgl_lhr" type="text" class="mt-1 block w-full"
                    :value="old('tmpt_tgl_lhr', $student?->tmpt_tgl_lhr)" required />
            </div>
            <div id="kelamin">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Jenis
                    Kelamin<span style="color: red">*</span></label>
                <select name="kelamin" required
                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
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
                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="Sertifikasi"
                        {{ old('tujuan_sert', $asesi?->tujuan_sert) == 'Sertifikasi' ? 'selected' : '' }}>Sertifikasi
                    </option>
                    <option value="Lainnya"
                        {{ old('tujuan_sert', $asesi?->tujuan_sert) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
            {{-- <div id="Mata kuliah terkait dan nilainya">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Mata Kuliah
                    terkait Skema Sertifikasi dan Nilai yang diperoleh<span style="color: red">*</span>
                </label>
                <x-text-input name="makul_nilai" type="text" class="mt-1 block w-full" :value="old('makul_nilai', $asesi?->makul_nilai)" />
            </div> --}}
            <div x-data="{
                makulNilais: {{ old('makul_nama') ? json_encode(collect(old('makul_nama'))->map(function($item, $key) { return ['nama_makul' => $item, 'nilai' => old('makul_nilai')[$key]]; })) : $asesi->makulNilais->map->only('nama_makul', 'nilai_makul')->toJson() }},
                addMakul() {
                    this.makulNilais.push({ nama_makul: '', nilai_makul: '' });
                },
                removeMakul(index) {
                    this.makulNilais.splice(index, 1);
                }
            }" class="p-4 border border-gray-200 dark:border-gray-700 rounded-md">
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">
                    Mata Kuliah terkait Skema Sertifikasi dan Nilai yang diperoleh<span class="text-red-500">*</span>
                </label>

                <template x-for="(makul, index) in makulNilais" :key="index">
                    <div class="flex items-center gap-2 mb-2">
                        {{-- Input untuk Nama Mata Kuliah --}}
                        <div class="flex-grow">
                            <x-text-input
                                name="makul_nama[]"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="Nama Mata Kuliah"
                                x-model="makul.nama_makul"
                                required />
                        </div>

                        {{-- Input untuk Nilai --}}
                        <div class="w-1/4">
                            <x-text-input
                                name="makul_nilai[]"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="Nilai (e.g., A, B+)"
                                x-model="makul.nilai_makul"
                                required />
                        </div>

                        {{-- Tombol Hapus (hanya muncul jika ada lebih dari 1 baris) --}}
                        <button type="button" @click="removeMakul(index)" x-show="makulNilais.length > 1"
                            class="p-2 text-red-500 hover:text-red-700 dark:hover:text-red-400 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </template>

                {{-- Tombol untuk Menambah Baris Baru --}}
                <button type="button" @click="addMakul()"
                    class="mt-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline cursor-pointer">
                    + Tambah Mata Kuliah
                </button>

                {{-- Menampilkan Error Validasi --}}
                @error('makul_nama.*')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                @error('makul_nilai.*')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <h3 class="dark:text-gray-300">d. Bukti Kelengkapan</h3>
            <div id="file apl 1">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Form APL.01.
                    Permohonan Sertifikasi Kompetensi (file MS-Word yang telah diisi dan dilengkapi dengan tanda tangan
                    ukuran file maksimal 2 MB)<span style="color: red">*</span>
                </label>
                <!-- Feedback jika file sudah ada -->
                @if ($asesi->apl_1)
                    <p class="text-sm text-gray-500 mt-1">File sudah ada:
                        <a href="{{ asset('storage/' . $asesi->apl_1) }}" class="text-blue-500" target="_blank">Lihat
                            File</a>
                    </p>
                @endif
                <input type="file" name="apl_1" required
                    class="w-full px-3 py-2 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-hidden focus:ring-2 focus:border-blue-800 focus:ring-blue-800"
                    @if (!$asesi->apl_2) required @endif>
            </div>
            <div id="file apl 2">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Form APL.02.
                    Asesmen Mandiri (file MS-Word yang telah diisi dan dilengkapi dengan tanda tangan ukuran file
                    maksimal 3 MB)<span style="color: red">*</span>
                </label>
                <!-- Feedback jika file sudah ada -->
                @if ($asesi->apl_2)
                    <p class="text-sm text-gray-500 mt-1">File sudah ada:
                        <a href="{{ asset('storage/' . $asesi->apl_2) }}" class="text-blue-500"
                            target="_blank">Lihat File</a>
                    </p>
                @endif
                <input type="file" name="apl_2" required
                    class="w-full px-3 py-2 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-hidden focus:ring-2 focus:border-blue-800 focus:ring-blue-800"
                    @if (!$asesi->apl_2) required @endif>
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
                    class="w-full px-3 py-2 dark:text-gray-300 border border-gray-300 dark:border-gray-600  rounded-md focus:outline-hidden focus:ring-2 focus:border-blue-800 focus:ring-blue-800"
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
                    class="w-full px-3 py-2 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-hidden focus:ring-2 focus:border-blue-800 focus:ring-blue-800"
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
                    class="w-full px-3 py-2 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-hidden focus:ring-2 focus:border-blue-800 focus:ring-blue-800"
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
                    class="w-full px-3 py-2 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-hidden focus:ring-2 focus:border-blue-800 focus:ring-blue-800"
                    @if (!$student->pas_foto) required @endif>
            </div>
            <div id="surat keterangan magang">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Scan Surat
                    Keterangan Magang/PKL/MBKM (maks 5, ukuran file maksimal 3 MB)
                </label>
                @foreach ($asesi->asesiattachmentfiles->where('type','surat_ket_magang') as $file)
                    <p class="text-sm text-gray-500 mt-1">File sudah ada:
                        <a href="{{ asset('storage/' . $file->path_file) }}" class="text-blue-500"
                            target="_blank">Lihat File</a>
                    </p>
                @endforeach
                <input type="file" name="surat_ket_magang[]" multiple
                    class="w-full px-3 py-2 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-hidden focus:ring-2 focus:border-blue-800 focus:ring-blue-800">
            </div>
            <div id="sertif pelatihan">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Scan
                    Sertifikat Pelatihan (maks 5, ukuran file maksimal 3 MB)
                </label>
                @foreach ($asesi->asesiattachmentfiles->where('type','sertif_pelatihan') as $file)
                    <p class="text-sm text-gray-500 mt-1">File sudah ada:
                        <a href="{{ asset('storage/' . $file->path_file) }}" class="text-blue-500"
                            target="_blank">Lihat File</a>
                    </p>
                @endforeach
                <input type="file" name="sertif_pelatihan[]" multiple
                    class="w-full px-3 py-2 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-hidden focus:ring-2 focus:border-blue-800 focus:ring-blue-800">
            </div>
            <div id="dokumen pendukung lainnya">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Dokumen
                    pendukung lainnya: dapat berupa Laporan kegiatan PKL/Magang/MBKM/Publikasi Jurnal/dll (maks 5,
                    ukuran file maksimal 5 MB)
                </label>
                @foreach ($asesi->asesiattachmentfiles->where('type','dok_pendukung_lain') as $file)
                    <p class="text-sm text-gray-500 mt-1">File sudah ada:
                        <a href="{{ asset('storage/' . $file->path_file) }}" class="text-blue-500"
                            target="_blank">Lihat File</a>
                    </p>
                @endforeach
                <input type="file" name="dok_pendukung_lain[]" multiple
                    class="w-full px-3 py-2 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-hidden focus:ring-2 focus:border-blue-800 focus:ring-blue-800">
            </div>
            <button type="submit"
                class="bg-blue-700 text-gray-800 dark:text-white px-4 py-2 rounded-lg transition self-end cursor-pointer">Update</button>
        </form>
        <h4 class="dark:text-white"><span class="text-red-600">*</span>&rpar; Wajib diisi</h4>
    </div>
</x-app-layout>
