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
    @php
        $jumlahFileLama = $sertification->asesmenfile ? $sertification->asesmenfile->count() : 0;
    @endphp
    <div x-data="{ editingRincian: {{ $sertification->punya_rincian_asesmen ? 'false' : 'true' }} }">
        
        {{-- Blok untuk menampilkan editor (Edit Mode) --}}
        {{-- <div x-show="editingRincian"
            class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="flex justify-between items-center mb-2">
                <p class="text-gray-500 dark:text-gray-400 text-xs">Silahkan berikan rincian tugas asesmen, misalkan
                    file
                    apa yg
                    harus dikumpulkan para asesi.</p>
                <button x-show="!editingRincian" type="button" @click="editingRincian = false"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 cursor-pointer">
                    Batal
                </button>
            </div>
            <form action="{{ route('admin.sertifikasi.assessment.update', $sertification->id) }}"
                class="mt-4 flex flex-col gap-2" method="POST" x-data="{ error: '', files: null, jumlahFileLama: {{ $jumlahFileLama }} }" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div id="rincian_tugas_asesmen">
                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1"
                        for="rincian_tugas_asesmen">Rincian</label>
                    @include('layouts.custom-rich-editor', [
                        'inputName' => 'rincian_tugas_asesmen',
                        'initialValue' => old(
                            'rincian_tugas_asesmen',
                            $sertification?->rincian_tugas_asesmen ??
                                \App\Models\Sertification::RINCIAN_DEFAULT_ASESMEN),
                    ])
                    <x-input-error class="mt-2" :messages="$errors->get('rincian_tugas_asesmen')" />
                </div>
                <div id="batas_pengumpulan_tugas_asesmen">
                    <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Batas
                        pengumpulan Tugas Asesmen
                    </label>
                    <x-text-input id="batas_pengumpulan_tugas_asesmen" name="batas_pengumpulan_tugas_asesmen"
                        type="datetime-local" class="mt-1 block w-full" :value="old(
                            'batas_pengumpulan_tugas_asesmen',
                            $sertification?->batas_pengumpulan_tugas_asesmen,
                        )" />
                    <x-input-error class="mt-2" :messages="$errors->get('batas_pengumpulan_tugas_asesmen')" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Lampiran (maks 5
                        file)</label>

                    @if ($sertification->tugasasesmenattachmentfile && $sertification->tugasasesmenattachmentfile->isNotEmpty())
                        <div class="flex-wrap" >
                            @foreach ($sertification->tugasasesmenattachmentfile as $attachment)
                                <div id="asesmen_file--{{ $attachment->id }}" x-init @ajax:before="confirm('Are you sure?') || $event.preventDefault()"
                                
                                    class="flex flex-row py-1 px-2 bg-gray-200 rounded-md items-center justify-between mb-1 max-w-[260px]">
                                    @php
                                        $basename = basename($attachment->path_file);
                                        $short = strlen($basename) > 24 ? substr($basename, 0, 24) . '...' : $basename;
                                    @endphp
                                    <a href="{{ asset('storage/' . $attachment->path_file) }}" target="_blank"
                                        class="font-medium text-sm text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 no-underline">{{ basename($short) }}</a>
                                    
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
                    if (files.length + jumlahFileLama > 5) {
                        if (jumlahFileLama === 0) {
                            error = 'Maksimal 5 file yang boleh dipilih';    
                        } else {
                            error = 'Maksimal 5 file yang boleh dipilih. Anda sudah punya  ' + jumlahFileLama + ' file.';
                        }
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
                    <x-input-error class="mt-2" :messages="$errors->get('asesmen_attachment_file')" />
                    <x-input-error class="mt-2" :messages="$errors->get('asesmen_attachment_file.*')" />
                </div>

                <button type="submit"
                    class="self-start font-medium bg-blue-500 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-700 cursor-pointer">
                    Simpan Perubahan
                </button>
            </form>
        </div> --}}
    </div>
    {{-- Daftar mahasiswa yg dilanjutkan ke asesmen dan pembayarannya sudah terverifikasi --}}
    <div class="p-6 bg-white dark:bg-gray-800 shadow-md rounded-lg mt-2">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            No
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Nama Asesi
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status Tugas
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($filteredAsesi as $index => $asesi)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $loop->iteration }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $asesi->student->user->name ?? 'Nama Tidak Tersedia' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if ($asesi->asesiasesmenfiles && $asesi->asesiasesmenfiles->isNotEmpty())
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100">Diserahkan</span>
                                @else
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100">Belum
                                        ada tugas dikumpulkan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if ($asesi->asesiasesmenfiles && $asesi->asesiasesmenfiles->isNotEmpty())
                                    <a href="{{ route('admin.sertifikasi.rincian.assessment.asesi.index', [$sertification->id, $asesi->id]) }}"
                                        class="cursor-pointer px-2 py-1 text-sm font-medium text-white bg-indigo-500 hover:bg-indigo-600 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-600 dark:hover:bg-indigo-700 dark:focus:ring-indigo-700">
                                        Lihat
                                    </a>
                                @else
                                    <span class="text-xs text-gray-500">Belum ada tugas dikumpulkan</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4"
                                class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                Belum ada pendaftar yang memenuhi kriteria (Dilanjutkan Asesmen dan Pembayarannya
                                Terverifikasi)
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
</x-admin-layout>
