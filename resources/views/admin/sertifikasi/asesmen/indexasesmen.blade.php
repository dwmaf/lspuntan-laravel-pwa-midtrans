<x-admin-layout>
    <div id="alert-success"
        class="fixed top-4 right-4 p-4 bg-green-100 dark:bg-green-700 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-100 rounded-md shadow-lg z-50 hidden">
        File berhasil dihapus!
    </div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    @if (session('success'))
        <div class="my-2 p-4 bg-green-100 dark:bg-green-700 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-100 rounded-md"
            role="alert">
            {{ session('success') }}
        </div>
    @endif
    @include('layouts.admin-sertifikasi-menu')
    <div class="p-6 bg-white dark:bg-gray-800 rounded-tr-lg rounded-bl-lg rounded-br-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 sm:mb-0">
            Asesmen {{ $sertification->skema->nama_skema }}
        </h3>
        @php
            $jumlahFileLama = $sertification->asesmenfile ? $sertification->asesmenfile->count() : 0;
        @endphp
        <form action="{{ route('admin.sertification.assessment.update', $sertification->id) }}" class="mt-4 flex flex-col gap-2" method="POST"
            x-data="{ error: '', files: null }" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1" for="rincian_asesmen">Rincian
                Asesmen</label>
            <input id="rincian_asesmen" type="hidden" name="rincian_asesmen"
                value="{{ old('rincian_asesmen', $sertification?->rincian_asesmen) }}">
            <div class="trix-container">
                <trix-editor input="rincian_asesmen">
                    {!! old('rincian_asesmen', $sertification?->rincian_asesmen) !!}
                </trix-editor>
            </div>
            @error('rincian_asesmen')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror

            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Lampiran (maks 5
                file)</label>
            {{-- <p class="text-sm">Jumlah file lama: <span x-text="$store.asesmen.jumlahFileLama"></span></p> --}}
            @if ($sertification->asesmenfile && $sertification->asesmenfile->isNotEmpty())
                <div class="flex-wrap">
                    @foreach ($sertification->asesmenfile as $attachment)
                        <div id="file-{{ $attachment->id }}"
                            class="flex flex-row py-1 px-2 bg-gray-200 rounded-md items-center justify-between mb-1 max-w-[260px]">
                            @php
                                $basename = basename($attachment->path_file);
                                $short = strlen($basename) > 24 ? substr($basename, 0, 24) . '...' : $basename;
                            @endphp
                            <a href="{{ asset('storage/' . $attachment->path_file) }}" target="_blank"
                                class="font-medium text-sm text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 no-underline">{{ basename($short) }}</a>
                            <button type="button"
                                class="font-medium text-sm ml-2 text-red-600 cursor-pointer flex items-center gap-1"
                                onclick="hapusFileAjax({{ $attachment->id }}, this)">
                                Hapus
                                <span class="hidden ml-1" id="spinner-{{ $attachment->id }}">
                                    <svg class="animate-spin h-4 w-4 text-red-600" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    @endforeach
                </div>
            @else
                <span class="text-xs text-gray-900 dark:text-gray-100">Belum ada file.</span>
            @endif
            <input type="file" name="asesmen_attachment_file[]" multiple
                accept=".jpg,.jpeg,.png,.pdf,.docx,.pptx,.xlx"
                class="w-full px-3 py-2 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-hidden dark:bg-gray-900 focus-ring-2 focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                @change="
                    files = $event.target.files;
                    if (files.length + $store.asesmen.jumlahFileLama > 5) {
                        error = 'Maksimal 5 file yang boleh dipilih. Anda sudah punya  ' + $store.asesmen.jumlahFileLama + ' file.';
                        $event.target.value = '';
                        files = null;
                    } else {
                        error = '';
                    }" />
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" id="file_input_help">
                Tipe file: JPG, JPEG, PNG, PDF, DOCX, PPTX, XLSX.
            </p>
            <template x-if="error">
                <p class="text-red-600 text-xs mt-1" x-text="error"></p>
            </template>
            @error('asesmen_attachment_file.*')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
            @error('asesmen_attachment_file')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror

            <button type="submit"
                class="self-start font-medium bg-blue-500 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-700 cursor-pointer">
                Simpan Perubahan
            </button>
        </form>
    </div>
    @push('scripts-asesmen')
        {{-- trix --}}
        <script>
            document.addEventListener('trix-initialize', function(event) {
                const editorElement = event.target; // Mendapatkan elemen trix-editor
                const toolbar = editorElement.toolbarElement; // Mendapatkan elemen toolbar dari trix-editor

                // Daftar kelas CSS untuk tombol atau grup tombol yang ingin dihilangkan
                const buttonsToRemove = [
                    '.trix-button--icon-strike', // Strikethrough
                    '.trix-button--icon-link', // Link
                    '.trix-button--icon-quote', // Quote
                    '.trix-button-group--file-tools', // Grup tombol untuk attach files
                    '.trix-button-group--history-tools', // Grup tombol untuk undo/redo
                    '.trix-button--icon-decrease-nesting-level', // Decrease nesting level
                    '.trix-button--icon-increase-nesting-level', // Increase nesting level
                    '.trix-button--icon-code', // Code
                    '.trix-button--icon-number-list', // Numbers/Numbered list
                    '.trix-button--icon-heading-1', // Heading
                ];

                buttonsToRemove.forEach(selector => {
                    const buttonElement = toolbar.querySelector(selector);
                    if (buttonElement) {
                        buttonElement.remove();
                    }
                });

                // Mencegah Trix menerima file drop/paste, karena kita punya input file terpisah
                // dan tombol attach file sudah dihilangkan
                editorElement.addEventListener("trix-file-accept", function(e) {
                    e.preventDefault();
                }, true);
            });
        </script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('asesmen', {
                    jumlahFileLama: {{ $jumlahFileLama }}
                });
            });
        </script>
        <script>
            function hapusFileAjax(fileId, btn) {
                console.log(fileId);

                if (!confirm('Yakin ingin menghapus file ini?')) return;
                const spinner = document.getElementById('spinner-' + fileId);
                if (spinner) spinner.classList.remove('hidden');
                btn.disabled = true;
                fetch('/asesmen-file-ajax-delete', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'text/plain'
                        },
                        body: fileId.toString()
                    })
                    .then(res => {
                        if (res.ok) {
                            console.log('the response is ok');

                            // Hapus elemen file dari DOM
                            document.getElementById('file-' + fileId).remove();
                            // Update jumlahFileLama di Alpine
                            if (window.Alpine && Alpine.store('asesmen')) {
                                Alpine.store('asesmen').jumlahFileLama--;
                            }
                            if (spinner) spinner.classList.add('hidden');
                            btn.disabled = false;
                            // alert sukses menghapus file
                            // Tampilkan alert custom
                            const alertBox = document.getElementById('alert-success');
                            if (alertBox) {
                                alertBox.classList.remove('hidden');
                                setTimeout(() => {
                                    alertBox.classList.add('hidden');
                                }, 2000); // 2 detik
                            }

                        } else {
                            alert('Gagal menghapus file!');
                            if (spinner) spinner.classList.add('hidden');
                            btn.disabled = false;
                        }
                    })
                    .catch(() => {
                        alert('Gagal menghapus file!');
                        if (spinner) spinner.classList.add('hidden');
                        btn.disabled = false;
                    });

            }
        </script>
    @endpush
</x-admin-layout>
