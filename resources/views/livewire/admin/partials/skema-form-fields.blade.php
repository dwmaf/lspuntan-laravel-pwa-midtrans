{{-- Nama Skema --}}
<div>
    <x-input-label>Nama Skema Sertifikasi</x-input-label>
    <x-text-input wire:model.defer="nama_skema" type="text" class="mt-1 block w-full" />
    <x-input-error class="mt-2" :messages="$errors->get('nama_skema')" />
</div>

<div>
    <x-input-label>Format File APL.01 (docx)</x-input-label>
    @if ($formMode === 'edit' && $skema->format_apl_1)
        <span class="text-xs text-gray-500">File saat ini: {{ basename($skema->format_apl_1) }}</span>
    @endif
    <x-file-input type="file" wire:model.defer="format_apl_1"/>
    <div wire:loading wire:target="format_apl_1" class="text-xs text-gray-500">Uploading...</div>
    <x-input-error class="mt-2" :messages="$errors->get('format_apl_1')" />
</div>

<div>
    <x-input-label>Format File APL.02 (docx)</x-input-label>
    @if ($formMode === 'edit' && $skema->format_apl_2)
        <span class="text-xs text-gray-500">File saat ini: {{ basename($skema->format_apl_2) }}</span>
    @endif
    <x-file-input type="file" wire:model.defer="format_apl_2"/>
    <div wire:loading wire:target="format_apl_2" class="text-xs text-gray-500">Uploading...</div>
    <x-input-error class="mt-2" :messages="$errors->get('format_apl_1')" />
</div>

<div class="flex items-center gap-4 pt-2">
    <x-loading-spinner wire:loading wire:target="save, update"></x-loading-spinner>
    <x-primary-button wire:loading.attr="disabled">
        {{ $formMode === 'edit' ? 'Update' : 'Simpan' }}
    </x-primary-button>
    <x-secondary-button wire:click="resetForm">
        Batal
    </x-secondary-button>
</div>
