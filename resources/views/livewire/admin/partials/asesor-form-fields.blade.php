<div>
    <x-input-label>Nama Asesor</x-input-label>
    <x-text-input wire:model.live="name" type="text" class="mt-1 block w-full" />
    <x-input-error class="mt-2" :messages="$errors->get('name')" />
</div>
{{-- Dropdown Skema dengan Alpine.js --}}
<div x-data="{ open: false }" class="relative">
    <button type="button" @click="open = !open"
        class="p-2 text-sm font-medium rounded-md w-full text-left flex justify-between items-center mt-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
        <span>
            Pilih Skema
            <span x-show="$wire.selectedSkemas.length > 0" x-text="`(${$wire.selectedSkemas.length} terpilih)`"
                class="ml-1 text-xs text-gray-400"></span>
        </span>
        <svg class="w-4 h-4 transform transition-transform" :class="{ 'rotate-180': open }" fill="currentColor"
            viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                clip-rule="evenodd" />
        </svg>
    </button>
    <div x-show="open" @click.away="open = false"
        class="absolute left-0 w-full rounded-b-md z-20 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 dark:text-white"
        x-cloak>
        <div class="p-2 max-h-60 overflow-y-auto">
            @foreach ($allSkemas as $skema)
                <label class="flex items-center p-1 hover:bg-gray-200 dark:hover:bg-gray-700 rounded cursor-pointer">
                    <input type="checkbox" wire:model.live="selectedSkemas" value="{{ $skema->id }}"
                        class="mr-2 rounded text-blue-500">
                    <span class="text-sm">{{ $skema->nama_skema }}</span>
                </label>
            @endforeach
        </div>
    </div>
    <x-input-error class="mt-2" :messages="$errors->get('selectedSkemas')" />

</div>

<div>
    <x-input-label>Email</x-input-label>
    <x-text-input wire:model.live="email" type="email" class="mt-1 block w-full" />
    <x-input-error class="mt-2" :messages="$errors->get('email')" />
</div>

<div>
    <x-input-label>No. HP</x-input-label>
    <x-text-input wire:model.live="no_tlp_hp" type="tel" class="mt-1 block w-full" />
    <x-input-error class="mt-2" :messages="$errors->get('no_tlp_hp')" />
</div>

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
