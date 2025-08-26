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
    @php
        $jumlahFileLama = $sertification->asesmenfile ? $sertification->asesmenfile->count() : 0;
        $rincianDefault = 'Silahkan buat rincian tugas asesmen...';
        $punyaRincian =
            !empty($sertification->rincian_tugas_asesmen) && $sertification->rincian_tugas_asesmen !== $rincianDefault;
    @endphp
    <div x-data="{ editingRincian: {{ $punyaRincian ? 'false' : 'true' }}, punyaRincian: {{ $punyaRincian ? 'true' : 'false' }} }">
        {{-- Blok untuk menampilkan rincian yang sudah ada (View Mode) --}}
        <div class="py-3 px-5 bg-white dark:bg-gray-800 rounded-lg shadow-md mb-2" x-show="!editingRincian">
            <div class="flex justify-between items-center mb-2">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex-shrink-0">
                        <svg class="h-10 w-10 text-gray-400 dark:text-gray-600 rounded-full bg-gray-200 dark:bg-gray-700 p-1"
                            fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <h5 class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                            @if ($sertification->pembuatrinciantugasasesmen->asesor)
                                {{-- Jika pembuatnya adalah seorang asesor, tampilkan nama dari tabel asesor --}}
                                {{ $sertification->pembuatrinciantugasasesmen->asesor->name }}
                            @else
                                {{-- Fallback jika karena suatu hal data pembuat tidak ada --}}
                                Admin
                            @endif
                        </h5>
                        <div class="text-xs text-gray-400">
                            @if (\Carbon\Carbon::parse($sertification->rincian_tugasasesmen_dibuat_pada)->isToday())
                                {{-- Jika hari ini, tampilkan jam --}}
                                {{ \Carbon\Carbon::parse($sertification->rincian_tugasasesmen_dibuat_pada)->format('H:i') }}
                            @else
                                {{-- Jika sudah lewat, tampilkan tanggal --}}
                                {{ \Carbon\Carbon::parse($sertification->rincian_tugasasesmen_dibuat_pada)->format('d M Y') }}
                            @endif
                        </div>
                    </div>
                </div>
                <button type="button" @click="editingRincian = true"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-yellow-500 hover:bg-yellow-600 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-700 transition ease-in-out duration-150 cursor-pointer">
                    <x-bxs-edit class="w-4 h-4 mr-2" />
                    Edit
                </button>
            </div>
            <h6 class="font-medium text-sm text-gray-800 dark:text-gray-100">{!! $sertification?->rincian_tugas_asesmen !!}</h6>
        </div>
        {{-- Blok untuk menampilkan editor (Edit Mode) --}}
        <div x-show="editingRincian"
            class="p-6 bg-white dark:bg-gray-800 rounded-tr-lg rounded-bl-lg rounded-br-lg shadow-md">
            <div class="flex justify-between items-center mb-2">
                <p class="text-gray-500 dark:text-gray-400 text-xs">Silahkan berikan rincian tugas asesmen, misalkan
                    file
                    apa yg
                    harus dikumpulkan para asesi.</p>
                <button x-show="punyaRincian" type="button" @click="editingRincian = false"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 cursor-pointer">
                    Batal
                </button>
            </div>
            <form action="{{ route('admin.sertifikasi.assessment.update', $sertification->id) }}"
                class="mt-4 flex flex-col gap-2" method="POST" x-data="{ error: '', files: null }" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1"
                    for="rincian_tugas_asesmen">Rincian</label>
                @include('layouts.custom-rich-editor', [
                    'inputName' => 'rincian_tugas_asesmen',
                    'initialValue' => old(
                        'rincian_tugas_asesmen',
                        $sertification?->rincian_tugas_asesmen ?? $rincianDefault),
                ])
                @error('rincian_tugas_asesmen')
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
                                        <svg class="animate-spin h-4 w-4 text-red-600"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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
    </div>
    {{-- Daftar mahasiswa yg dilanjutkan ke asesmen dan pembayarannya sudah terverifikasi --}}
    <div class="p-6 bg-white dark:bg-gray-800 shadow-xl rounded-lg ">
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
                                {{ $asesi->student->name ?? 'Nama Tidak Tersedia' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if ($asesi->asesiasesmenfiles && $asesi->asesiasesmenfiles->isNotEmpty())
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100">Menunggu Dilihat</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100">Belum ada tugas dikumpulkan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if ($asesi->asesiasesmenfiles && $asesi->asesiasesmenfiles->isNotEmpty())
                                    <a href="{{ route('admin.sertifikasi.rincian.assessment.asesi.index', [$asesi->id, $sertification->id]) }}"
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
                            <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                Belum ada pendaftar yang memenuhi kriteria (Dilanjutkan Asesmen dan Pembayarannya
                                Terverifikasi)
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @push('scripts-asesmen')
        {{-- inisialisasi jumlah file lama --}}
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('asesmen', {
                    jumlahFileLama: {{ $jumlahFileLama }}
                });
            });
        </script>
        {{-- ajax buat ngapus file --}}
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
