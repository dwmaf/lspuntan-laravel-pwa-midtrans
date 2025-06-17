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
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 sm:mb-0">
            Praasesmen {{ $sertification->skema->nama_skema }}
        </h3>
        <form action="/rincian_asesmen/{{ $sertification->id }}/update" class="mt-4 flex flex-col gap-2" method="POST"
            x-data="{ error: '', files: null }" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1" for="rincian_asesmen">Rincian
                Asesmen</label>
            <trix-editor input="rincian_asesmen">
                {!! old('rincian_asesmen', $sertification?->rincian_asesmen) !!}
            </trix-editor>
            <input id="rincian_asesmen" type="hidden" name="rincian_asesmen">

            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Lampiran (maks 5
                file)</label>
            @if ($sertification->asesmenfile && $sertification->asesmenfile->isNotEmpty())
                <ul class="list-disc list-inside">
                    @foreach ($sertification->asesmenfile as $attachment)
                        <li>
                            <a href="{{ asset('storage/' . $attachment->path_file) }}" target="_blank"
                                class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">Lihat</a>
                        </li>
                    @endforeach
                </ul>
            @else
                <span class="text-xs text-gray-900 dark:text-gray-100">Belum ada file.</span>
            @endif
            <input type="file" name="asesmen_attachment_file[]" multiple
                accept=".jpg,.jpeg,.png,.pdf,.docx,.pptx,.xlx"
                class="block w-full border border-gray-300 p-2 rounded-md"
                @change="
            files = $event.target.files;
            if (files.length > 5) {
                error = 'Maksimal 5 file yang boleh dipilih.';
                $event.target.value = '';
                files = null;
            } else {
                error = '';
            }" />
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" id="file_input_help">
                Tipe file: JPG, JPEG, PNG, PDF, DOCX, PPTX, XLSX.
            </p>
            <template x-if="error">
                <p class="text-red-600 mt-2" x-text="error"></p>
            </template>
            @error('praasesmen_attachment_file.*')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
            @error('praasesmen_attachment_file')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror

            <button type="submit"
                class="bg-blue-700 text-gray-800 dark:text-white px-4 py-2 rounded-lg hover:bg-blue-500 dark:hover:bg-blue-500 dark:bg-blue-800 transition self-end mt-4 cursor-pointer">
                Simpan Perubahan
            </button>
        </form>
    </div>
    @push('scripts-trix-editor-asesmen')
        {{-- trix --}}
        {{-- <script>
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
        </script> --}}
        {{-- summernote --}}
        {{-- <script>
            $(document).ready(function() {
                $('#rincian_praasesmen_editor').summernote({
                    height: 300, // Tinggi editor
                    minHeight: 200, // Tinggi minimal
                    maxHeight: 500, // Tinggi maksimal
                    placeholder: 'Masukkan rincian pra-asesmen...',
                    toolbar: [
                        ['style', ['style']], // Dropdown style tidak disertakan jika tidak diinginkan
                        ['font', ['bold', 'italic', 'underline', 'clear']], // Font formatting
                        ['fontsize', ['fontsize']], // Font size, bisa dihilangkan jika tidak diinginkan
                        ['color', [
                        'color']], // Font dan background color, bisa dihilangkan jika tidak diinginkan
                        ['para', ['ul', 'ol', 'paragraph']], // List dan paragraph
                        ['table', ['table']], // Tabel, bisa dihilangkan jika tidak diinginkan
                        ['insert', ['link', 'picture',
                        'video']], // Insert link, picture, video - sesuaikan sesuai kebutuhan
                        ['view', ['fullscreen',
                        'help']] // View options, bisa dihilangkan jika tidak diinginkan
                    ],
                });
            });
        </script> --}}
        {{-- quill --}}
        {{-- <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Pastikan Quill tersedia dan elemen container ada
                if (typeof Quill !== 'undefined' && document.getElementById('quill_editor_container')) {
                    var quill = new Quill('#quill_editor_container', {
                        theme: 'snow', // Tema 'snow' atau 'bubble'
                        modules: {
                            toolbar: [
                                ['bold', 'italic'], // tombol toggle
                                [{
                                    'list': 'bullet'
                                }],
                                ['clean'] // tombol hapus format
                            ]
                        },
                        placeholder: 'Masukkan rincian pra-asesmen di sini...',
                    });

                    // Sinkronisasi konten Quill ke hidden input saat ada perubahan
                    var hiddenInput = document.getElementById('rincian_praasesmen');
                    quill.on('text-change', function(delta, oldDelta, source) {
                        if (hiddenInput) {
                            // Simpan konten HTML dari Quill
                            hiddenInput.value = quill.root.innerHTML;
                            console.log(hiddenInput.value);

                        }
                    });

                    // Jika ada konten awal di hidden input (misalnya dari validasi error),
                    // dan div editor kosong, coba paste konten tersebut.
                    // Namun, karena kita sudah memasukkan ke dalam div,
                    // Quill seharusnya sudah mengambilnya. Baris ini untuk kasus jika div awalnya kosong.
                    // if (hiddenInput && hiddenInput.value && quill.getLength() <= 1) { // <=1 karena Quill selalu punya newline di awal
                    //    quill.clipboard.dangerouslyPasteHTML(0, hiddenInput.value);
                    // }

                } else {
                    console.error('Quill.js atau container #quill_editor_container tidak ditemukan.');
                }
            });
        </script> --}}
    @endpush
</x-admin-layout>
