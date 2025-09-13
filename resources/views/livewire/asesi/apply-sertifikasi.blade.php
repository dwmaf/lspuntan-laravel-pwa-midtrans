<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Daftar Sertifikasi: {{ $sertification->skema->nama_skema }}
        </h2>
    </x-slot>
<div x-data="{ show: false, message: '' }"
        x-on:notify.window="message=$event.detail.message;show=true;setTimeout(()=>show=false,3000)" x-show="show"
        x-transition x-text="message" class="fixed top-20 right-4 text-xs px-3 py-2 rounded bg-green-600 text-white z-50"
        style="display:none">
    </div>
    <div class="max-w-7xl mx-auto">
        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <form wire:submit.prevent="save" class="mt-6 space-y-6">
                <h3 class="dark:text-gray-300">a. Data Pribadi</h3>
                <div>
                    <x-input-label>Nama Lengkap (Sesuai KTP)<span style="color: red">*</span></x-input-label>
                    <x-text-input wire:model.defer="name" type="text" class="mt-1 block w-full" required />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>
                <div id="nik KTP">
                    <x-input-label>No. KTP<span style="color: red">*</span></x-input-label>
                    <x-text-input wire:model.defer="nik" type="text" class="mt-1 block w-full" />
                    <x-input-error class="mt-2" :messages="$errors->get('nik')" />
                </div>
                <div id="tempat lahir">
                    <x-input-label>Tempat Lahir<span style="color: red">*</span></x-input-label>
                    <x-text-input wire:model.defer="tmpt_lhr" type="text" class="mt-1 block w-full" />
                    <x-input-error class="mt-2" :messages="$errors->get('tmpt_lhr')" />
                </div>
                <div id=" tgl lahir">
                    <x-input-label>Tanggal Lahir<span style="color: red">*</span></x-input-label>
                    <x-text-input wire:model.defer="tgl_lhr" type="date" class="mt-1 block w-full"/>
                    <x-input-error class="mt-2" :messages="$errors->get('tgl_lhr')" />
                </div>
                <div id="kelamin">
                    <x-input-label>Jenis Kelamin<span style="color: red">*</span></x-input-label>
                    <select wire:model.defer="kelamin"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="Laki-laki"
                            {{ old('kelamin', $student?->kelamin) == 'Laki-laki' ? 'selected' : '' }}>
                            Laki-Laki
                        </option>
                        <option value="Perempuan"
                            {{ old('kelamin', $student?->kelamin) == 'Perempuan' ? 'selected' : '' }}>
                            Perempuan
                        </option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('kelamin')" />

                </div>
                <div id="kebangsaan">
                    <x-input-label>Kebangsaan<span style="color: red">*</span></x-input-label>
                    <x-text-input wire:model.defer="kebangsaan" type="text" class="mt-1 block w-full"/>
                    <x-input-error class="mt-2" :messages="$errors->get('kebangsaan')" />
                </div>
                <div id="no tlp HP">
                    <x-input-label>No. Tlp HP(WA)<span style="color: red">*</span></x-input-label>
                    <x-text-input wire:model.defer="no_tlp_hp" type="text" class="mt-1 block w-full"/>
                    <x-input-error class="mt-2" :messages="$errors->get('no_tlp_hp')" />
                </div>
                <div id="no tlp rumah">
                    <x-input-label>No. Tlp Rumah</x-input-label>
                    <x-text-input wire:model.defer="no_tlp_rmh" type="text" class="mt-1 block w-full"/>
                    <x-input-error class="mt-2" :messages="$errors->get('no_tlp_rmh')" />
                </div>
                <div id="no tlp kantor">
                    <x-input-label>No. Tlp Kantor</x-input-label>
                    <x-text-input wire:model.defer="no_tlp_kntr" type="text" class="mt-1 block w-full"/>
                    <x-input-error class="mt-2" :messages="$errors->get('no_tlp_kntr')" />
                </div>

                <div id="Kualifikasi Pendidikan (tulis: Mahasiswa S1)*">
                    <x-input-label>Kualifikasi Pendidikan (tulis: Mahasiswa S1)<span style="color: red">*</span></x-input-label>
                    <x-text-input wire:model.defer="kualifikasi_pendidikan" type="text" class="mt-1 block w-full"/>
                    <x-input-error class="mt-2" :messages="$errors->get('kualifikasi_pendidikan')" />
                </div>

                <h3 class="dark:text-gray-300">a. Data Pekerjaan Sekarang</h3>
                <div id="nama institusi/perusahaan">
                    <x-input-label>Nama Institusi/Perusahaan</x-input-label>
                    <x-text-input wire:model.defer="nama_institusi" type="text" class="mt-1 block w-full"/>
                    <x-input-error class="mt-2" :messages="$errors->get('nama_institusi')" />
                </div>
                <div id="jabatan">
                    <x-input-label>Jabatan</x-input-label>
                    <x-text-input wire:model.defer="jabatan" type="text" class="mt-1 block w-full"/>
                    <x-input-error class="mt-2" :messages="$errors->get('jabatan')" />
                </div>
                <div id="alamat kantor">
                    <x-input-label>Alamat Kantor</x-input-label>
                    <x-text-input wire:model.defer="alamat_kantor" type="text" class="mt-1 block w-full"/>
                    <x-input-error class="mt-2" :messages="$errors->get('alamat_kantor')" />
                </div>
                <div id="No telepon/fax/email">
                    <x-input-label>No. Tlp/Email/Fax</x-input-label>
                    <x-text-input wire:model.defer="no_tlp_email_fax" type="text" class="mt-1 block w-full"/>
                    <x-input-error class="mt-2" :messages="$errors->get('no_tlp_email_fax')" />
                </div>

                <h3 class="dark:text-gray-300">c. Data Sertifikasi</h3>
                <div id="tujuan sertifikasi">
                    <x-input-label>Tujuan Sertifikasi<span style="color: red">*</span></x-input-label>
                    <select wire:model.defer="tujuan_sert"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:border-indigo-600 focus:ring-indigo-600 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="" disabled selected>--Silahkan pilih tujuan sertifikasi anda--</option>
                        <option value="Sertifikasi">Sertifikasi</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('tujuan_sert')" />
                </div>
                {{-- Bagian Dinamis untuk Mata Kuliah --}}
                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-md">
                    <x-input-label>Mata Kuliah terkait (lihat di APL 1)<span style="color: red">*</span></x-input-label>
                    @foreach ($makulNilais as $index => $makul)
                        <div class="flex items-center gap-2 mb-2" wire:key="makul-{{ $index }}">
                            <x-text-input wire:model.defer="makulNilais.{{ $index }}.nama_makul" type="text"
                                class="mt-1 block w-full" placeholder="Nama Mata Kuliah" />
                            <x-text-input wire:model.defer="makulNilais.{{ $index }}.nilai_makul"
                                type="text" class="mt-1 block w-1/4" placeholder="Nilai" />
                            @if (count($makulNilais) > 1)
                                <button type="button" wire:click="removeMakul({{ $index }})"
                                    class="p-2 text-red-500">&times;</button>
                            @endif
                        </div>
                    @endforeach
                    <button type="button" wire:click="addMakul" class="mt-2 text-sm font-medium text-blue-600">+
                        Tambah
                        Mata Kuliah</button>
                </div>

                {{-- Bagian untuk File Upload --}}
                <h3 class="dark:text-gray-300">d. Bukti Kelengkapan</h3>
                <div>
                    <x-input-label>Form APL.01<span style="color: red">*</span></x-input-label>
                    <a href="{{ asset('storage/' . $sertification->skema->format_apl_1) }}"
                        class="text-blue-500 hover:text-blue-300" target="_blank">
                        Lihat Template
                    </a>
                    <x-file-input type="file" wire:model.defer="apl_1" />
                    <x-input-error class="mt-2" :messages="$errors->get('apl_1')" />
                </div>
                <div id="file apl 2">
                    <x-input-label>APL.02.
                        Asesmen Mandiri (file MS-Word yang telah diisi dan dilengkapi dengan tanda tangan ukuran file
                        maksimal 3 MB)<span style="color: red">*</span></x-input-label>
                    <a href="{{ asset('storage/' . $sertification->skema->format_apl_2) }}"
                        class="text-blue-500 hover:text-blue-300" target="_blank">
                        Lihat Template
                    </a>
                    <x-file-input type="file" wire:model.defer="apl_2" />
                    <x-input-error class="mt-2" :messages="$errors->get('apl_2')" />
                </div>
                <div id="foto_ktp">
                    <x-input-label>Scan KTP
                        (ukuran file maksimal 1 MB)<span style="color: red">*</span></x-input-label>
                    @if ($student->foto_ktp)
                        <p class="text-sm text-gray-500 mb-1">File sudah ada:
                            <a href="{{ asset('storage/' . $student->foto_ktp) }}"
                                class="text-blue-500 hover:text-blue-300" target="_blank">Lihat File</a>
                        </p>
                    @endif
                    <!-- Input file -->
                    <x-file-input type="file" wire:model.defer="foto_ktp" />
                    <x-input-error class="mt-2" :messages="$errors->get('foto_ktp')" />
                </div>
                <div id="foto_ktm">
                    <x-input-label>Scan KTM
                        (ukuran file maksimal 1 MB)<span style="color: red">*</span></x-input-label>
                    <!-- Feedback jika file sudah ada -->
                    @if ($student->foto_ktm)
                        <p class="text-sm text-gray-500 mb-1">File sudah ada:
                            <a href="{{ asset('storage/' . $student->foto_ktm) }}"
                                class="text-blue-500 hover:text-blue-300" target="_blank">Lihat File</a>
                        </p>
                    @endif
                    <x-file-input type="file" wire:model.defer="foto_ktm" />
                    <x-input-error class="mt-2" :messages="$errors->get('foto_ktm')" />
                </div>

                <div id="kartu_hasil_studi">
                    <x-input-label>Scan Kartu Hasil Studi (Semester I-V)<span
                            style="color: red">*</span></x-input-label>
                    {{-- Tampilkan daftar file KHS yang sudah ada --}}
                    @php
                        $khsFiles = $student->studentattachmentfile->where('type', 'kartu_hasil_studi');
                    @endphp
                    @if ($khsFiles->isNotEmpty())
                        <div class=" text-sm text-gray-600 dark:text-gray-400">
                            <p class="text-sm text-gray-500">File yang sudah diunggah:</p>
                            <ul class="list-disc list-inside pl-2 mb-1">
                                @foreach ($khsFiles as $file)
                                    <li>
                                        <a href="{{ asset('storage/' . $file->path_file) }}"
                                            class="text-blue-500 hover:text-blue-300" target="_blank">
                                            Lihat File
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <x-file-input type="file" multiple wire:model.defer="kartu_hasil_studi[]" />
                    <x-input-error class="mt-2" :messages="$errors->get('kartu_hasil_studi.*')" />
                    <x-input-error class="mt-2" :messages="$errors->get('kartu_hasil_studi')" />
                </div>
                <div id="pas foto">
                    <x-input-label>Pasfoto
                        terbaru dengan latar belakang merah, berukuran 4x6 (ukuran file maksimal 1 MB)<span
                            style="color: red">*</span></x-input-label>
                    @if ($student->pas_foto)
                        <p class="text-sm text-gray-500 mb-1">File sudah ada:
                            <a href="{{ asset('storage/' . $student->pas_foto) }}"
                                class="text-blue-500 hover:text-blue-300" target="_blank">Lihat File</a>
                        </p>
                    @endif
                    <x-file-input type="file" wire:model.defer="pas_foto" />
                    <x-input-error class="mt-2" :messages="$errors->get('pas_foto')" />
                </div>
                <div id="surat keterangan magang">
                    <x-input-label>Surat
                        Keterangan Magang/PKL/MBKM (maks 5, ukuran file maksimal 3 MB)</x-input-label>
                    <x-file-input type="file" multiple wire:model.defer="surat_ket_magang[]" />
                    <x-input-error class="mt-2" :messages="$errors->get('surat_ket_magang.*')" />
                    <x-input-error class="mt-2" :messages="$errors->get('surat_ket_magang')" />
                </div>
                <div id="sertif pelatihan">
                    <x-input-label>Scan
                        Sertifikat Pelatihan (maks 5, ukuran file maksimal 3 MB)</x-input-label>
                    <x-file-input type="file" multiple wire:model.defer="sertif_pelatihan[]" />
                    <x-input-error class="mt-2" :messages="$errors->get('sertif_pelatihan.*')" />
                    <x-input-error class="mt-2" :messages="$errors->get('sertif_pelatihan')" />
                </div>
                <div id="dokumen pendukung lainnya">
                    <x-input-label>Dokumen
                        pendukung lainnya: dapat berupa Laporan kegiatan PKL/Magang/MBKM/Publikasi Jurnal/dll (maks 5,
                        ukuran file maksimal 5 MB)</x-input-label>
                    <x-file-input type="file" multiple wire:model.defer="dok_pendukung_lain[]" />
                    <x-input-error class="mt-2" :messages="$errors->get('dok_pendukung_lain.*')" />
                    <x-input-error class="mt-2" :messages="$errors->get('dok_pendukung_lain')" />
                </div>
                <div class="flex items-center gap-4 pt-2">
                    <x-primary-button wire:loading.attr="disabled">
                        Daftar
                    </x-primary-button>
                    <span wire:loading wire:target="save" class="flex items-center">
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
                </div>
            </form>
        </div>
    </div>
</div>
