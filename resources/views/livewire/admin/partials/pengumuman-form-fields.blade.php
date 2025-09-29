<div>
    <x-input-label>Rincian</x-input-label>
    <textarea wire:model.defer="rincian_pengumuman_asesmen" rows="8"
        class="mt-1 w-full text-sm p-3 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100"></textarea>
    <x-input-error class="mt-2" :messages="$errors->get('rincian_pengumuman_asesmen')" />
</div>


<div>
    <x-input-label>Lampiran</x-input-label>
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
    <x-file-input type="file" wire:model.defer="newFiles" multiple />
    <x-input-error class="mt-2" :messages="$errors->get('newFiles')" />
    <x-input-error class="mt-2" :messages="$errors->get('newFiles.*')" />
</div>

<div class="flex items-center gap-4 pt-2">
    <x-loading-spinner wire:loading wire:target="save"></x-loading-spinner>
    <x-primary-button wire:loading.attr="disabled">
        {{ $formMode === 'edit' ? 'Update' : 'Simpan' }}
    </x-primary-button>
    <x-secondary-button wire:click="resetForm">
        Batal
    </x-secondary-button>
</div>