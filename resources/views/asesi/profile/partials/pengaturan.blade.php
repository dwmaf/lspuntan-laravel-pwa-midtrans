{{-- filepath: d:\Laravel-App\lsp-untan-laravel-pwa\resources\views\asesi\profile\partials\pengaturan.blade.php --}}
<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Pengaturan') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Anda bisa mengatur notifikasi.') }}
        </p>
    </header>

    {{-- PERBAIKAN: Tambahkan Toggle Switch Notifikasi di sini --}}
    <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
        <div x-data="{ 
                enabled: false, 
                permission: 'default',
                init() {
                    if ('Notification' in window) {
                        this.permission = Notification.permission;
                        this.enabled = (this.permission === 'granted');
                    } else {
                        this.permission = 'unsupported';
                    }
                }
            }" x-init="init()" class="flex items-center justify-between">
            
            {{-- Label dan Deskripsi --}}
            <span class="flex flex-col">
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Notifikasi Push</span>
                <template x-if="permission === 'denied'">
                    <span class="text-xs text-red-600 dark:text-red-400">Anda telah memblokir notifikasi. Aktifkan melalui pengaturan browser.</span>
                </template>
                <template x-if="permission === 'default'">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Izinkan notifikasi untuk info penting.</span>
                </template>
                 <template x-if="permission === 'granted'">
                    <span class="text-xs text-green-600 dark:text-green-400">Notifikasi sudah aktif.</span>
                </template>
            </span>

            {{-- Toggle Switch --}}
            <button type="button" 
                id="enable-notifications-btn"
                @click="enabled = !enabled"
                :class="{ 'bg-blue-600': enabled, 'bg-gray-200 dark:bg-gray-600': !enabled }"
                :disabled="permission !== 'default'"
                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" 
                role="switch" 
                :aria-checked="enabled.toString()">
                <span class="sr-only">Use setting</span>
                <span aria-hidden="true" 
                    :class="{ 'translate-x-5': enabled, 'translate-x-0': !enabled }"
                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
            </button>
        </div>
    </div>
</section>
