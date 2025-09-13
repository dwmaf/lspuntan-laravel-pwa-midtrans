{{-- filepath: d:\Laravel-App\lsp-untan-laravel-pwa\resources\views\livewire\admin\profile\update-profile-information.blade.php --}}
<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Informasi Profil
        </h2>
    </header>

    <form wire:submit.prevent="save" class="mt-6 space-y-6">
        <div>
            <x-input-label for="name" :value="__('Nama')" />
            <x-text-input wire:model.live="name" id="name" type="text" class="mt-1 block w-full" required
                autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>
        <div>
            <x-input-label for="no_tlp_hp" :value="__('No HP/WA')" />
            <x-text-input wire:model.live="no_tlp_hp" id="no_tlp_hp" type="text" class="mt-1 block w-full"
                required />
            <x-input-error class="mt-2" :messages="$errors->get('no_tlp_hp')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Simpan</x-primary-button>

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
        </div>
    </form>
</section>
