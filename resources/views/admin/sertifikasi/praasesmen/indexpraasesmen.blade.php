<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    @if (session('success'))
        <div class="my-2 p-4 bg-green-100 dark:bg-green-700 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-100 rounded-md" role="alert">
                {{ session('success') }}
        </div>
    @endif
    @include('layouts.admin-sertifikasi-menu')
    <div class="p-6 bg-white dark:bg-gray-800 rounded-tr-lg rounded-bl-lg rounded-br-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 sm:mb-0">
            Praasesmen {{ $sertification->skema->nama_skema }}
        </h3>

        <form action="/rincian_praasesmen/{{ $sertification->id }}/update" class="mt-4 flex flex-col gap-4" method="POST"
            x-data="{ error: '', files: null }" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div>
                <label for="rincian_praasesmen_editor"
                    class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Rincian
                    pra-asesmen</label>
                {{-- Wrapper untuk styling konsisten dengan input lain --}}
                <input required id="rincian_praasesmen" type="hidden" name="rincian_praasesmen"
                    value="{{ old('rincian_praasesmen', $sertification?->rincian_praasesmen) }}">
                {{-- <div class="trix-container">
                    <trix-editor input="rincian_praasesmen" id="rincian_praasesmen_editor"></trix-editor>
                </div> --}}

                {{-- Summernote Textarea --}}
                {{-- <textarea id="rincian_praasesmen_editor" name="rincian_praasesmen_summernote" class="form-control">{{ old('rincian_praasesmen') }}</textarea> --}}

                {{-- quill js editor --}}
                <div id="quill_editor_container" class="rounded-md">
                    {!! old('rincian_praasesmen', $sertification?->rincian_praasesmen) !!}
                </div>

                @error('rincian_praasesmen')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="praasesmen_attachment_file"
                    class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Lampiran (maks 5 file,
                    opsional)</label>
                @if ($sertification->praasesmenfile && $sertification->praasesmenfile->isNotEmpty())
                    <ul class="list-disc list-inside">
                        @foreach ($sertification->praasesmenfile as $attachment)
                            <li>
                                <a href="{{ asset('storage/' . $attachment->path_file) }}" target="_blank"
                                    class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">Lihat</a>
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
                if (files.length > 5) {
                    error = 'Maksimal 5 file yang boleh dipilih.';
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
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-800 dark:focus:ring-blue-800 transition self-end mt-4 cursor-pointer">
                Simpan Perubahan
            </button>
        </form>
    </div>


    @push('scripts')
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
        <script>
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
        </script>
    @endpush
</x-admin-layout>
