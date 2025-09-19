{{-- filepath: d:\Laravel-App\lsp-untan-laravel-pwa\resources\views\livewire\asesi\profile\update-profile-asesi.blade.php --}}
<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Data Pribadi') }}
        </h2>
    </header>

    <form wire:submit.prevent="save" class="mt-6 space-y-6">
        {{-- Nama Lengkap --}}
        <div>
            <x-input-label for="name">Nama Lengkap Sesuai KTP <span class="text-red-500">*</span></x-input-label>
            <x-text-input wire:model="name" id="name" type="text" class="mt-1 block w-full" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        {{-- NIK --}}
        <div>
            <x-input-label for="nik">NIK <span class="text-red-500">*</span></x-input-label>
            <x-text-input wire:model="nik" id="nik" type="text" class="mt-1 block w-full" required />
            <x-input-error class="mt-2" :messages="$errors->get('nik')" />
        </div>

        {{-- Tempat Lahir --}}
        <div>
            <x-input-label for="tmpt_lhr">Tempat Lahir <span class="text-red-500">*</span></x-input-label>
            <x-text-input wire:model="tmpt_lhr" id="tmpt_lhr" type="text" class="mt-1 block w-full" required />
            <x-input-error class="mt-2" :messages="$errors->get('tmpt_lhr')" />
        </div>

        {{-- Tanggal Lahir --}}
        <div>
            <x-input-label for="tgl_lhr">Tanggal Lahir <span class="text-red-500">*</span></x-input-label>
            <x-text-input wire:model="tgl_lhr" id="tgl_lhr" type="date" class="mt-1 block w-full" required />
            <x-input-error class="mt-2" :messages="$errors->get('tgl_lhr')" />
        </div>

        {{-- Kelamin --}}
        <div>
            <x-input-label for="kelamin">Kelamin <span class="text-red-500">*</span></x-input-label>
            <select wire:model="kelamin" id="kelamin" required class="w-full px-3 py-2 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg ...">
                <option value="Laki-laki">Laki-Laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('kelamin')" />
        </div>

        {{-- ... (Lanjutkan untuk semua field teks lainnya: kebangsaan, no_tlp_rmh, dll.) ... --}}
        {{-- Contoh untuk No HP --}}
        <div>
            <x-input-label for="no_tlp_hp">No tlp HP(WA) <span class="text-red-500">*</span></x-input-label>
            <x-text-input wire:model="no_tlp_hp" id="no_tlp_hp" type="text" class="mt-1 block w-full" required />
            <x-input-error class="mt-2" :messages="$errors->get('no_tlp_hp')" />
        </div>

        {{-- ... (Lanjutkan untuk semua field pekerjaan) ... --}}

        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Data Sertifikasi</h2>

        {{-- Foto KTP --}}
        <div>
            <x-input-label for="foto_ktp">Foto KTP <span class="text-red-500">*</span></x-input-label>
            @if ($student->foto_ktp && !$foto_ktp)
                <p class="text-sm text-gray-500 mt-1">File sudah ada: <a href="{{ asset('storage/' . $student->foto_ktp) }}" class="text-blue-500" target="_blank">Lihat File</a></p>
            @endif
            <input wire:model="foto_ktp" id="foto_ktp" type="file" class="w-full ...">
            <div wire:loading wire:target="foto_ktp" class="text-sm text-gray-500 mt-1">Uploading...</div>
            <x-input-error class="mt-2" :messages="$errors->get('foto_ktp')" />
        </div>

        {{-- Foto KTM --}}
        <div>
            <x-input-label for="foto_ktm">Foto KTM <span class="text-red-500">*</span></x-input-label>
            @if ($student->foto_ktm && !$foto_ktm)
                <p class="text-sm text-gray-500 mt-1">File sudah ada: <a href="{{ asset('storage/' . $student->foto_ktm) }}" class="text-blue-500" target="_blank">Lihat File</a></p>
            @endif
            <input wire:model="foto_ktm" id="foto_ktm" type="file" class="w-full ...">
            <div wire:loading wire:target="foto_ktm" class="text-sm text-gray-500 mt-1">Uploading...</div>
            <x-input-error class="mt-2" :messages="$errors->get('foto_ktm')" />
        </div>

        {{-- Kartu Hasil Studi (KHS) --}}
        <div>
            <x-input-label for="kartu_hasil_studi">Kartu Hasil Studi (dari semester I-V) <span class="text-red-500">*</span></x-input-label>
            @php $khsFiles = $student->studentattachmentfiles->where('type', 'kartu_hasil_studi'); @endphp
            @if ($khsFiles->isNotEmpty())
                <div class="mt-2 mb-2 text-sm text-gray-600 dark:text-gray-400">
                    <p class="font-semibold">File yang sudah diunggah:</p>
                    <ul class="list-disc list-inside pl-2">
                        @foreach ($khsFiles as $file)
                            <li><a href="{{ asset('storage/' . $file->path_file) }}" class="text-blue-500 hover:underline" target="_blank">Lihat file</a></li>
                        @endforeach
                    </ul>
                    <p class="text-xs text-yellow-600 mt-1">Mengunggah file baru akan menghapus semua file KHS yang lama.</p>
                </div>
            @endif
            <input wire:model="kartu_hasil_studi" id="kartu_hasil_studi" type="file" multiple class="w-full ...">
            <div wire:loading wire:target="kartu_hasil_studi" class="text-sm text-gray-500 mt-1">Uploading...</div>
            <x-input-error class="mt-2" :messages="$errors->get('kartu_hasil_studi.*')" />
            <x-input-error class="mt-2" :messages="$errors->get('kartu_hasil_studi')" />
        </div>

        {{-- Pas Foto --}}
        <div>
            <x-input-label for="pas_foto">Pas Foto Terbaru 4x6 Latar Merah <span class="text-red-500">*</span></x-input-label>
            @if ($student->pas_foto && !$pas_foto)
                <p class="text-sm text-gray-500 mt-1">File sudah ada: <a href="{{ asset('storage/' . $student->pas_foto) }}" class="text-blue-500" target="_blank">Lihat File</a></p>
            @endif
            <input wire:model="pas_foto" id="pas_foto" type="file" class="w-full ...">
            <div wire:loading wire:target="pas_foto" class="text-sm text-gray-500 mt-1">Uploading...</div>
            <x-input-error class="mt-2" :messages="$errors->get('pas_foto')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>
                <span wire:loading.remove wire:target="save">Save</span>
                <span wire:loading wire:target="save">Saving...</span>
            </x-primary-button>
        </div>
    </form>
</section>
