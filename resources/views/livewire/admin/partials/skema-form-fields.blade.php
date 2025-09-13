{{-- Nama Skema --}}
<div>
    <x-input-label>Nama Skema Sertifikasi</x-input-label>
    <x-text-input wire:model.live="nama_skema" type="text" class="mt-1 block w-full" />
    <x-input-error class="mt-2" :messages="$errors->get('nama_skema')" />
</div>

<div>
    <x-input-label>Format File APL.01 (docx)</x-input-label>
    @if ($formMode === 'edit' && $editingSkema->format_apl_1)
        <span class="text-xs text-gray-500">File saat ini: {{ basename($editingSkema->format_apl_1) }}</span>
    @endif
    <x-file-input type="file" wire:model.live="format_apl_1"/>
    <div wire:loading wire:target="format_apl_1" class="text-xs text-gray-500">Uploading...</div>
    <x-input-error class="mt-2" :messages="$errors->get('format_apl_1')" />
</div>

<div>
    <x-input-label>Format File APL.02 (docx)</x-input-label>
    @if ($formMode === 'edit' && $editingSkema->format_apl_2)
        <span class="text-xs text-gray-500">File saat ini: {{ basename($editingSkema->format_apl_2) }}</span>
    @endif
    <x-file-input type="file" wire:model.live="format_apl_2"/>
    
    <div wire:loading wire:target="format_apl_2" class="text-xs text-gray-500">Uploading...</div>
    <x-input-error class="mt-2" :messages="$errors->get('format_apl_1')" />
</div>

{{-- Tombol Aksi --}}
<div class="flex items-center gap-4 pt-2">
    <span wire:loading wire:target="save, update" class="flex items-center">
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
