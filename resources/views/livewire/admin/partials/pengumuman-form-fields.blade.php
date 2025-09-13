<div>
    <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Rincian</label>
    <textarea wire:model.defer="rincian_pengumuman_asesmen" rows="8"
        class="mt-1 w-full text-sm p-3 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100"></textarea>
    @error('rincian_pengumuman_asesmen')
        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>


<div>
    <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Lampiran</label>

    @if ($existingFiles->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 my-2">
            @foreach ($existingFiles as $file)
                <div
                    class="flex items-center justify-between gap-2 pl-3 pr-2 py-2 bg-gray-200 dark:bg-gray-700 rounded-md text-xs">
                    <a href="{{ asset('storage/' . $file->path_file) }}" target="_blank"
                        class="text-blue-600 dark:text-blue-400 truncate flex-1">{{ basename($file->path_file) }}</a>
                    <button wire:click="deleteFile({{ $file->id }})" wire:confirm="Yakin hapus file ini?"
                        class="flex-shrink-0 p-1 rounded-full text-gray-500 hover:bg-gray-300 dark:hover:bg-gray-600">
                        <x-fas-xmark class="w-4 h-4 text-gray-700 dark:text-gray-200" />
                    </button>
                </div>
            @endforeach
        </div>
    @endif

    <input type="file" wire:model="newFiles" multiple
        class="mt-1 w-full px-3 py-2 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md text-sm">
    @error('newFiles.*')
        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="flex items-center gap-4 pt-2">
    <span wire:loading wire:target="save" class="flex items-center">
        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
            </circle>
            <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
        </svg>
    </span>
    <x-primary-button class="">
        {{ $formMode === 'edit' ? 'Update' : 'Simpan' }}
    </x-primary-button>
    <x-secondary-button wire:click="resetForm">
        Batal
    </x-secondary-button>
</div>