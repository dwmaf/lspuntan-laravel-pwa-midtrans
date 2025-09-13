{{-- filepath: d:\Laravel-App\lsp-untan-laravel-pwa\resources\views\livewire\admin\profile\update-password.blade.php --}}
<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Update Password
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Pastikan akun Anda menggunakan password yang panjang dan acak agar tetap aman.
        </p>
    </header>

    <form wire:submit.prevent="updatePassword" class="mt-6 space-y-6">
        <div>
            <x-input-label for="current_password" :value="__('Password Saat Ini')" />
            <x-text-input wire:model.defer="current_password" id="current_password" type="password"
                class="mt-1 block w-full" autocomplete="current-password" />
            @error('current_password')
                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <x-input-label for="password" :value="__('Password Baru')" />
            <x-text-input wire:model.defer="password" id="password" type="password" class="mt-1 block w-full"
                autocomplete="new-password" />
            @error('password')
                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
            <x-text-input wire:model.defer="password_confirmation" id="password_confirmation" type="password"
                class="mt-1 block w-full" autocomplete="new-password" />
            @error('password_confirmation')
                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
            @enderror
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
