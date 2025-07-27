{{-- filepath: d:\Laravel-App\lsp-untan-laravel-pwa\resources\views\layouts\top-navigation.blade.php --}}
<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('admin.dashboard') }}">
                        <img src="{{ asset('logo-lsp.png') }}" alt="Logo LSP" class="block h-9 w-auto">
                    </a>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Dropdown) -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden">
        <div class="pt-2 pb-3 space-y-1">
            @hasrole(['admin', 'asesor'])
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">Dashboard</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.sertification.index')" :active="request()->routeIs('admin.sertification.*')">Sertifikasi</x-responsive-nav-link>
                @hasrole('admin')
                    <x-responsive-nav-link :href="route('admin.skema.create')" :active="request()->routeIs('admin.skema.*')">Skema</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.asesor.index')" :active="request()->routeIs('admin.asesor.*')">Asesor</x-responsive-nav-link>
                @endhasrole
                <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">Profile</x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('asesi.dashboard')" :active="request()->routeIs('asesi.dashboard')">Dashboard</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('asesi.certifications.index')" :active="request()->routeIs('asesi.certifications.*')">Sertifikasi</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('profile_asesi.edit')" :active="request()->routeIs('profile_asesi.edit')">Profile</x-responsive-nav-link>
            @endhasrole
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="space-y-1">
                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
