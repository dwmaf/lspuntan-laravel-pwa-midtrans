<x-admin-layout>
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
            Praasesmen {{ $sertification->skema->nama_skema }}
        </h3>
        @php
            $jumlahFileLama = $sertification->praasesmenfile ? $sertification->praasesmenfile->count() : 0;
        @endphp
        <form id="myForm" action="/rincian_praasesmen/{{ $sertification->id }}/update" class="mt-4 flex flex-col gap-4"
            method="POST" x-data="{ error: '', files: null, }" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div>
                <label for="rincian_praasesmen_editor"
                    class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Rincian
                    pra-asesmen</label>
                <input required id="rincian_praasesmen" type="hidden" name="rincian_praasesmen"
                    value="{{ old('rincian_praasesmen', $sertification?->rincian_praasesmen) }}">
                <div class="trix-container">
                    <trix-editor input="rincian_praasesmen" id="rincian_praasesmen_editor">
                        {!! old('rincian_praasesmen', $sertification?->rincian_praasesmen) !!}
                    </trix-editor>
                </div>

                @error('rincian_praasesmen')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="praasesmen_attachment_file"
                    class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Lampiran (maks 5 file,
                    opsional)</label>
                <p class="text-sm">Jumlah file lama: <span x-text="$store.praasesmen.jumlahFileLama"></span></p>
                {{-- file input multiple bawaan --}}
                @if ($sertification->praasesmenfile && $sertification->praasesmenfile->isNotEmpty())
                    <ul class="list-disc list-inside">
                        @foreach ($sertification->praasesmenfile as $attachment)
                            <li id="file-{{ $attachment->id }}" class="flex">
                                <a href="{{ asset('storage/' . $attachment->path_file) }}" target="_blank"
                                    class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 no-underline">Lihat</a>
                                <button type="button" class="ml-2 text-red-600 cursor-pointer flex items-center gap-1"
                                    onclick="hapusFileAjax({{ $attachment->id }}, this)">
                                    Hapus
                                    <span class="hidden ml-1" id="spinner-{{ $attachment->id }}">
                                        <svg class="animate-spin h-4 w-4 text-red-600"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                        </svg>
                                    </span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <span class="text-xs text-gray-900 dark:text-gray-100">Belum ada file.</span>
                @endif

                <input type="file" name="praasesmen_attachment_file[]" id="praasesmen_attachment_file" multiple
                    accept=".jpg,.jpeg,.png,.pdf,.docx,.pptx,.xlsx"
                    class="w-full px-3 py-2 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-hidden dark:bg-gray-900 focus-ring-2 focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                    @change="
                        files = $event.target.files;
                        if (files.length + $store.praasesmen.jumlahFileLama > 5) {
                            error = 'Maksimal file tidak boleh lebih dari 5. Anda sudah punya ' + $store.praasesmen.jumlahFileLama + ' file.';
                            $event.target.value = ''; // Clear the input
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
                @error('praasesmen_attachment_file.*')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                @error('praasesmen_attachment_file')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="self-start font-medium bg-blue-500 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-700 cursor-pointer">
                Simpan Perubahan
            </button>
        </form>
    </div>

    @push('scripts')
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
        {{-- hapus file via ajax --}}
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('praasesmen', {
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
                fetch('/praasesmen-file-ajax-delete', {
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
                            if (window.Alpine && Alpine.store('praasesmen')) {
                                Alpine.store('praasesmen').jumlahFileLama--;
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
