<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Data Pribadi') }}
        </h2>

    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile_asesi.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Nama Lengkap Sesuai KTP <span class="text-red-500">*</span>
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)"
                    required autofocus />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                NIK <span class="text-red-500">*</span>
                <x-text-input id="nik" name="nik" type="text" class="mt-1 block w-full" :value="old('nik', $user->student?->nik)"
                    required />
                <x-input-error class="mt-2" :messages="$errors->get('nik')" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Tempat Tanggal Lahir <span class="text-red-500">*</span>
                <x-text-input id="tmpt_tgl_lhr" name="tmpt_tgl_lhr" type="text" class="mt-1 block w-full"
                    :value="old('tmpt_tgl_lhr', $user->student?->tmpt_tgl_lhr)" required />
                <x-input-error class="mt-2" :messages="$errors->get('tmpt_tgl_lhr')" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Kelamin <span class="text-red-500">*</span>
                <select name="kelamin" required
                    class="w-full px-3 py-2 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                    <option value="Laki-laki"
                        {{ old('kelamin', $user->student?->kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-Laki
                    </option>
                    <option value="Perempuan"
                        {{ old('kelamin', $user->student?->kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan
                    </option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('kelamin')" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Kebangsaan <span class="text-red-500">*</span>
                <x-text-input id="kebangsaan" name="kebangsaan" type="text" class="mt-1 block w-full"
                    :value="old('kebangsaan', $user->student?->kebangsaan)" required />
                <x-input-error class="mt-2" :messages="$errors->get('kebangsaan')" />
        </div>
        <div>
            <label for="no_tlp_rmh" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                No tlp Rumah
            </label>
            <x-text-input id="no_tlp_rmh" name="no_tlp_rmh" type="text" class="mt-1 block w-full"
                :value="old('no_tlp_rmh', $user->student?->no_tlp_rmh)" />
            <x-input-error class="mt-2" :messages="$errors->get('no_tlp_rmh')" />
        </div>
        <div>
            <label for="no_tlp_kntr" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                No tlp Kantor
            </label>
            <x-text-input id="no_tlp_kntr" name="no_tlp_kntr" type="text" class="mt-1 block w-full"
                :value="old('no_tlp_kntr', $user->student?->no_tlp_kntr)" />
            <x-input-error class="mt-2" :messages="$errors->get('no_tlp_kntr')" />
        </div>
        <div>
            <label for="no_tlp_hp" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                No tlp HP(WA) <span class="text-red-500">*</span>
            </label>
            <x-text-input id="no_tlp_hp" name="no_tlp_hp" type="text" class="mt-1 block w-full" :value="old('no_tlp_hp', $user->student?->no_tlp_hp)"
                required />
            <x-input-error class="mt-2" :messages="$errors->get('no_tlp_hp')" />
        </div>
        <div>
            <label for="kualifikasi_pendidikan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Kualifikasi Pendidikan (Isi Mahasiswa S1) <span class="text-red-500">*</span>
            </label>
            <x-text-input id="kualifikasi_pendidikan" name="kualifikasi_pendidikan" type="text"
                class="mt-1 block w-full" :value="old('kualifikasi_pendidikan', $user->student?->kualifikasi_pendidikan)" required />
            <x-input-error class="mt-2" :messages="$errors->get('kualifikasi_pendidikan')" />
        </div>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Data Pekerjaan Saat ini (Tidak Wajib Diisi)
        </h2>
        <div>
            <label for="nama_institusi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Nama Institusi
            </label>
            <x-text-input id="nama_institusi" name="nama_institusi" type="text" class="mt-1 block w-full"
                :value="old('nama_institusi', $user->student?->nama_institusi)" />
            <x-input-error class="mt-2" :messages="$errors->get('nama_institusi')" />
        </div>
        <div>
            <label for="jabatan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Jabatan
            </label>
            <x-text-input id="jabatan" name="jabatan" type="text" class="mt-1 block w-full" :value="old('jabatan', $user->student?->jabatan)" />
            <x-input-error class="mt-2" :messages="$errors->get('jabatan')" />
        </div>
        <div>
            <label for="alamat_kantor" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Alamat Kantor
            </label>
            <x-text-input id="alamat_kantor" name="alamat_kantor" type="text" class="mt-1 block w-full"
                :value="old('alamat_kantor', $user->student?->alamat_kantor)" />
            <x-input-error class="mt-2" :messages="$errors->get('alamat_kantor')" />
        </div>
        <div>
            <label for="no_tlp_email_fax" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                No Tlp/Email/Fax
            </label>
            <x-text-input id="no_tlp_email_fax" name="no_tlp_email_fax" type="text" class="mt-1 block w-full"
                :value="old('no_tlp_email_fax', $user->student?->no_tlp_email_fax)" />
            <x-input-error class="mt-2" :messages="$errors->get('no_tlp_email_fax')" />
        </div>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Data Sertifikasi</h2>

        <div>
            <label for="foto_ktp" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Foto KTP <span class="text-red-500">*</span>
            </label>
            <!-- Feedback jika file sudah ada -->
            @if ($user->student && $user->student->foto_ktp)
                <p class="text-sm text-gray-500 mt-1">File sudah ada:
                    <a href="{{ asset('storage/' . $user->student->foto_ktp) }}" class="text-blue-500"
                        target="_blank">Lihat File</a>
                </p>
            @else
                <p class="text-sm text-red-500 mt-1">Belum ada file yang diunggah</p>
            @endif
            <!-- Input file -->
            <input id="foto_ktp" name="foto_ktp" type="file"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:outline-hidden focus:ring-2 focus:border-blue-800 focus:ring-blue-800 rounded-md"
                @if (!$user->student || !$user->student->foto_ktp) required @endif>
            <x-input-error class="mt-2" :messages="$errors->get('foto_ktp')" />
        </div>
        <div>
            <label for="foto_ktm" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Foto KTM <span class="text-red-500">*</span>
            </label>
            <!-- Feedback jika file sudah ada -->
            @if ($user->student && $user->student->foto_ktm)
                <p class="text-sm text-gray-500 mt-1">File sudah ada:
                    <a href="{{ asset('storage/' . $user->student->foto_ktm) }}" class="text-blue-500"
                        target="_blank">Lihat File</a>
                </p>
            @else
                <p class="text-sm text-red-500 mt-1">Belum ada file yang diunggah</p>
            @endif
            <!-- Input file -->
            <input id="foto_ktm" name="foto_ktm" type="file"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:outline-hidden focus:ring-2 focus:border-blue-800 focus:ring-blue-800 rounded-md"
                @if (!$user->student || !$user->student->foto_ktm) required @endif>
            <x-input-error class="mt-2" :messages="$errors->get('foto_ktm')" />
        </div>

        <div>
            <label for="foto_khs" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Scan KHS <span class="text-red-500">*</span>
            </label>
            <!-- Feedback jika file sudah ada -->
            @if ($user->student && $user->student->foto_khs)
                <p class="text-sm text-gray-500 mt-1">File sudah ada:
                    <a href="{{ asset('storage/' . $user->student->foto_khs) }}" class="text-blue-500"
                        target="_blank">Lihat File</a>
                </p>
            @else
                <p class="text-sm text-red-500 mt-1">Belum ada file yang diunggah</p>
            @endif
            <!-- Input file -->
            <input id="foto_khs" name="foto_khs" type="file"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:outline-hidden focus:ring-2 focus:border-blue-800 focus:ring-blue-800 rounded-md"
                @if (!$user->student || !$user->student->foto_khs) required @endif>
            <x-input-error class="mt-2" :messages="$errors->get('foto_khs')" />
        </div>

        <div>
            <label for="pas_foto" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Pas Foto Terbaru 4x6 dengan Latar Belakang Merah <span class="text-red-500">*</span>
            </label>
            <!-- Feedback jika file sudah ada -->
            @if ($user->student && $user->student->pas_foto)
                <p class="text-sm text-gray-500 mt-1">File sudah ada:
                    <a href="{{ asset('storage/' . $user->student->pas_foto) }}" class="text-blue-500"
                        target="_blank">Lihat File</a>
                </p>
            @else
                <p class="text-sm text-red-500 mt-1">Belum ada file yang diunggah</p>
            @endif
            <!-- Input file -->
            <input id="pas_foto" name="pas_foto" type="file"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:outline-hidden focus:ring-2 focus:border-blue-800 focus:ring-blue-800 rounded-md"
                @if (!$user->student || !$user->student->pas_foto) required @endif>
            <x-input-error class="mt-2" :messages="$errors->get('pas_foto')" />
        </div>



        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
