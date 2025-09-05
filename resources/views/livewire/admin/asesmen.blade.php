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
                        </div>
                    </div>
                </div>
                <button type="button" wire:click="$set('editingRincian', true)"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-yellow-500 hover:bg-yellow-600 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-700 transition ease-in-out duration-150 cursor-pointer">
                    <x-bxs-edit class="w-4 h-4 mr-2" />
                    Edit
                </button>
            </div>
            
            <h6 class="font-medium text-sm text-gray-800 dark:text-gray-100">{!! nl2br(e($rincian_tugas_asesmen)) !!}</h6>
            <div class="flex">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mr-1">Batas Akhir Pengumpulan : </dt>
                <dd class="text-sm text-gray-900 dark:text-gray-100">
                    {{ $batas_pengumpulan_tugas_asesmen ? $sertification->batasPengumpulanFormatted : '-' }}
                </dd>
            </div>
            <div class="space-y-1">
                @forelse($existingFiles as $f)

                    <a href="{{ $f['url'] }}" target="_blank"
                        class="text-xs text-blue-600 dark:text-blue-400 underline">{{ $f['short'] }}</a>
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
                <p class="text-gray-500 dark:text-gray-400 text-xs">Edit Rincian Asesmen</p>
                <button type="button" wire:click="$set('editingRincian', false)"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gray-500 hover:bg-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700 transition ease-in-out duration-150 cursor-pointer">
                    Batal
                </button>
            </div>

            {{-- Rincian --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Rincian</label>

                <textarea
                    wire:model.defer="rincian_tugas_asesmen"
                    rows="8"
                    class="w-full text-sm p-3 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100"
                >{{ $rincian_tugas_asesmen }}</textarea>

                @error('rincian_tugas_asesmen')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Batas --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Batas Pengumpulan</label>
                <x-text-input type="datetime-local" wire:model.defer="batas_pengumpulan_tugas_asesmen" class="mt-1 block w-full"/>
                @error('batas_pengumpulan_tugas_asesmen')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- File Lama --}}
            <div class="space-y-2">
                <label class="text-xs font-medium">Lampiran ({{ $existingCount }}/5)</label>
                <div class="flex flex-col gap-1">
                    @forelse($existingFiles as $f)
                        <div
                            class="flex items-center justify-between bg-gray-200 dark:bg-gray-700 rounded px-2 py-1 text-xs">
                            <a href="{{ $f['url'] }}" target="_blank"
                                class="text-blue-600 dark:text-blue-400">{{ $f['short'] }}</a>
                            <button wire:click="deleteFile({{ $f['id'] }})" wire:confirm="Yakin hapus file ini?"
                                wire:loading.attr="disabled" class="text-red-600">
                                Hapus
                            </button>
                        </div>
                    @empty
                        <p class="text-[11px] text-gray-500">Belum ada file.</p>
                    @endforelse
                </div>
            </div>

            {{-- Upload baru --}}
            <div>
                <input type="file" multiple wire:model="newFiles"
                    class="w-full px-3 py-2 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-hidden dark:bg-gray-900 focus-ring-2 focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">
                <p class="text-[11px] text-gray-500">Tipe: JPG, JPEG, PNG, PDF, DOCX, PPTX, XLS/XLSX. Maks 5 total.</p>
                @error('newFiles')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
                @error('newFiles.*')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror

                {{-- Preview file baru --}}
                @if ($newFiles)
                    <div class="space-y-1">
                        @foreach ($newFiles as $i => $nf)
                            <div
                                class="flex items-center justify-between bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-[11px]">
                                <span>{{ $nf->getClientOriginalName() }}</span>
                                <button type="button" wire:click="removeNewFileTemp({{ $i }})"
                                    class="text-red-600">x</button>
                            </div>
                        @endforeach
                    </div>
                @endif
                <div wire:loading wire:target="newFiles" class="text-[11px] text-gray-500">Memproses file...</div>
            </div>

            <div class="flex gap-2">
                <button wire:click="save" wire:loading.attr="disabled"
                    class="px-4 py-2 text-sm bg-blue-600 text-white rounded">
                    Simpan
                </button>
                <div wire:loading.delay.short wire:target="save" class="text-xs text-gray-500 self-center">
                    Menyimpan...
                </div>
            </div>
        </div>
    @endif
</div>
