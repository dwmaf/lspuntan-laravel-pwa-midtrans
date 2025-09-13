<div class="space-y-4">
    {{-- Notifikasi --}}
    <div x-data="{ show: false, message: '' }"
        x-on:notify.window="message=$event.detail.message;show=true;setTimeout(()=>show=false,2500)" x-show="show"
        x-text="message"
        x-transition class="text-xs px-3 py-2 rounded bg-green-600 text-white inline-block" style="display:none"></div>

    {{-- Mode tampilan --}}
    @if (!$editingRincian)
        <div class="py-3 px-5 bg-white dark:bg-gray-800 rounded-lg shadow-md mb-2">
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
                <x-edit-button type="button" wire:click="$set('editingRincian', true)">Edit</x-edit-button>
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
                <div class="flex items-center justify-between gap-4 px-3 py-2 border-1 border-gray-300 rounded-md text-xs">
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
    @endif

    {{-- Mode edit --}}
    @if ($editingRincian)
        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md flex flex-col gap-2">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Edit Rincian Tugas Asesmen</h3>
            </div>

            {{-- Rincian --}}
            <div>
                <x-input-label>Rincian</x-input-label>
                <textarea
                    wire:model.defer="rincian_tugas_asesmen"
                    rows="8"
                    class="w-full text-sm p-3 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100"
                >{{ $rincian_tugas_asesmen }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('rincian_tugas_asesmen')" />
            </div>

            {{-- Batas --}}
            <div>
                <x-input-label>Batas Pengumpulan</x-input-label>
                <x-text-input type="datetime-local" wire:model.defer="batas_pengumpulan_tugas_asesmen" class="mt-1 block w-full"/>
                <x-input-error class="mt-2" :messages="$errors->get('batas_pengumpulan_tugas_asesmen')" />
            </div>

            {{-- File Lama --}}
            <div>
                <x-input-label>Lampiran ({{ $existingCount }}/5)</x-input-label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2">
                    @forelse($existingFiles as $f)
                        <div
                            class="flex items-center justify-between gap-4 px-3 py-2 border-1 border-gray-300 rounded-md text-xs">
                            <a href="{{ asset('storage/' . $f->path_file) }}" target="_blank"
                                class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-500 hover:underline truncate flex-1">
                                {{ basename($f->path_file) }}
                            </a>
                            <button wire:click="deleteFile({{ $f->id }})" wire:confirm="Yakin hapus lampiran ini?"
                                wire:loading.attr="disabled" 
                                class="cursor-pointer flex-shrink-0 p-1 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-300 dark:hover:bg-gray-600 hover:text-gray-800 dark:hover:text-gray-200 transition-colors">
                                <x-fas-xmark class="w-4 h-4 text-gray-700 dark:text-gray-200" />
                            </button>
                        </div>
                    @empty
                        <p class="text-[11px] text-gray-500">Belum ada file.</p>
                    @endforelse
                </div>
                <x-file-input type="file" multiple wire:model="newFiles"/>
                <p class="text-[11px] text-gray-500">Tipe: JPG, JPEG, PNG, PDF, DOCX, PPTX, XLS/XLSX. Maks 5 total.</p>
                <x-input-error class="mt-2" :messages="$errors->get('newFiles')" />
                <x-input-error class="mt-2" :messages="$errors->get('newFiles.*')" />
                <div wire:loading wire:target="newFiles" class="text-[11px] text-gray-500">Memproses file...</div>
            </div>

            <div class="flex gap-2">
                <span wire:loading wire:target="save" class="flex items-center">
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </span>
                <x-primary-button wire:click="save" wire:loading.attr="disabled">Simpan</x-primary-button>
                <x-secondary-button wire:click="$set('editingRincian', false)">Batal</x-secondary-button>
            </div>
        </div>
    @endif
</div>
