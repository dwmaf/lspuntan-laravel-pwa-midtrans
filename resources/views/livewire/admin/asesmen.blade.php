<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    {{-- Notifikasi --}}
    <div x-data="{ show: false, message: '' }"
        x-on:notify.window="message=$event.detail.message;show=true;setTimeout(()=>show=false,2500)" x-show="show"
        x-text="message" x-transition class="fixed top-20 right-4 text-xs px-3 py-2 rounded bg-green-600 text-white z-50"
        style="display:none"></div>
    @include('layouts.admin-sertifikasi-menu')
    {{-- Mode tampilan --}}
    <div x-show="!$wire.editingRincian" class="py-3 px-5 bg-white dark:bg-gray-800 rounded-lg shadow-md mb-2">
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
                        @if ($sert->pembuatrinciantugasasesmen && $sert->pembuatrinciantugasasesmen->asesor)
                            {{ $sert->pembuatrinciantugasasesmen->asesor->user->name }}
                        @else
                            Admin
                        @endif
                    </h5>
                    <div class="text-xs text-gray-400">
                        {{ $sert->tanggalRincianAsesmenDibuatFormatted }}
                        @if ($sert->tugasasesmen_updatedat)
                            (Diedit)
                        @endif
                    </div>
                </div>
            </div>
            <x-edit-button type="button" @click="$wire.set('editingRincian', true)">
                <x-loading-spinner wire:loading wire:target="edit"></x-loading-spinner>
                <span>
                    Edit
                </span>
            </x-edit-button>
        </div>

        <h6 class="font-medium text-sm text-gray-800 dark:text-gray-100">{!! nl2br(e($rincian_tugas_asesmen)) !!}</h6>
        <div class="flex">
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mr-1">Batas Akhir Pengumpulan : </dt>
            <dd class="text-sm text-gray-900 dark:text-gray-100">
                {{ $batas_pengumpulan_tugas_asesmen ? $sertification->batasPengumpulanFormatted : '-' }}
            </dd>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
            @forelse($existingFiles as $f)
                <div
                    class="flex items-center justify-between gap-4 px-3 py-2 border-1 border-gray-300 dark:border-gray-700 rounded-md text-xs">
                    <a href="{{ asset('storage/' . $f->path_file) }}" target="_blank"
                        class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-500 hover:underline truncate flex-1">
                        {{ basename($f->path_file) }}
                    </a>
                </div>
            @empty
                <p class="text-xs text-gray-500">Tidak ada lampiran.</p>
            @endforelse
        </div>
    </div>
    

    {{-- Mode edit --}}
    <div x-show="$wire.editingRincian" class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md flex flex-col gap-2">
        <div class="flex justify-between items-center mb-2">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Edit Rincian Tugas Asesmen</h3>
        </div>
        <form wire:submit.prevent="save" class="flex flex-col gap-4">
            {{-- Rincian --}}
            <div>
                <x-input-label>Rincian</x-input-label>
                <textarea wire:model.defer="rincian_tugas_asesmen" rows="8"
                    class="w-full text-sm p-3 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100">{{ $rincian_tugas_asesmen }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('rincian_tugas_asesmen')" />
            </div>

            {{-- Batas --}}
            <div>
                <x-input-label>Batas Pengumpulan</x-input-label>
                <x-text-input type="datetime-local" wire:model.defer="batas_pengumpulan_tugas_asesmen"
                    class="mt-1 block w-full" />
                <x-input-error class="mt-2" :messages="$errors->get('batas_pengumpulan_tugas_asesmen')" />
            </div>

            {{-- File Lama --}}
            <div>
                <x-input-label>Lampiran ({{ $existingCount }}/5)</x-input-label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2">
                    @forelse($existingFiles as $file)
                        <div
                            class="flex items-center justify-between gap-4 px-3 py-2 border-1 border-gray-300 dark:border-gray-700 rounded-md text-xs">
                            <a href="{{ asset('storage/' . $file->path_file) }}" target="_blank"
                                class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-500 hover:underline truncate flex-1">
                                {{ basename($file->path_file) }}
                            </a>
                            <button wire:click="deleteFile({{ $file->id }})" type="button"
                                wire:confirm="Yakin hapus lampiran ini?" wire:loading.attr="disabled"
                                class="cursor-pointer flex-shrink-0 p-1 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-300 dark:hover:bg-gray-600 hover:text-gray-800 dark:hover:text-gray-200 transition-colors">
                                <x-fas-xmark class="w-4 h-4 text-gray-700 dark:text-gray-200" />
                            </button>
                            <x-loading-spinner wire:loading
                                wire:target="deleteFile({{ $file->id }})"></x-loading-spinner>
                        </div>
                    @empty
                        <p class="text-[11px] text-gray-500">Belum ada file.</p>
                    @endforelse
                </div>
                <x-file-input type="file" multiple wire:model.defer="newFiles" />
                <p class="text-[11px] text-gray-500">Tipe: JPG, JPEG, PNG, PDF, DOCX, PPTX, XLS/XLSX. Maks 5 total.
                </p>
                <x-input-error class="mt-2" :messages="$errors->get('newFiles')" />
                <x-input-error class="mt-2" :messages="$errors->get('newFiles.*')" />
                <div wire:loading wire:target="newFiles" class="text-[11px] text-gray-500">Memproses file...</div>
            </div>

            <div class="flex items-center gap-2">
                <x-loading-spinner wire:loading wire:target="save"></x-loading-spinner>
                <x-primary-button wire:loading.attr="disabled" wire:target="save">Simpan</x-primary-button>
                <x-secondary-button wire:click="$set('editingRincian', false)">Batal</x-secondary-button>
            </div>
        </form>
    </div>
    
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
</div>
