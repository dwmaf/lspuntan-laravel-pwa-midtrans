<div x-data="{ open: false }" class="relative z-50">

    <!-- Header (topbar) -->
    <div class="mt-6 rounded-2xl w-full bg-white dark:bg-gray-800 shadow-sm p-4 flex items-center justify-between">
        <!-- Burger Button -->
        <button @click="open = !open" class="text-gray-800 dark:text-white focus:outline-none">
            <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

    </div>

    <!-- Sidebar + Push Content -->
    <div class="flex transition-all duration-300" :class="{ 'ml-64': open, 'ml-0': !open }">
        <!-- Sidebar -->
        <div class="fixed top-0 left-0 h-full w-64 bg-white dark:bg-gray-800 shadow-lg p-4 transition-transform duration-300 z-40"
             :class="{ '-translate-x-full': !open, 'translate-x-0': open }"
             x-cloak>
             <div class="flex justify-end mb-6">
                <button @click="open = false" class="text-gray-800 dark:text-white focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <!-- Sidebar content -->
            <nav class="space-y-4">
                <div class="flex items-center justify-center mb-6">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-10 fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>
                @if (auth()->user()->role == 'asesor' || auth()->user()->role == 'admin')
                    <a href="{{ route('dashboardadmin') }}"
                        class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-sm">
                        {{ __('Home') }}
                    </a>
                @else
                    <a href="{{ route('dashboard') }}"
                        class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-sm">
                        {{ __('Home') }}
                    </a>
                @endif
            
                @if (auth()->user()->role == 'asesor' || auth()->user()->role == 'admin')
                    <a href="/sertification"
                        class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-sm">
                        {{ __('Sertifikasi') }}
                    </a>
                @else
                    <a href="/sertification-asesi"
                        class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-sm">
                        {{ __('Sertifikasi') }}
                    </a>
                @endif
                @if (auth()->user()->role == 'asesor' || auth()->user()->role == 'admin')
                    <a href="/asesor"
                        class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-sm">
                        {{ __('Asesor') }}
                    </a>
                @endif
                @if (auth()->user()->role == 'asesor' || auth()->user()->role == 'admin')
                    <a href="/profile"
                        class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-sm">
                        {{ __('Profile') }}
                    </a>
                @else
                    <a href="/profile_asesi"
                        class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-sm">
                        {{ __('Profile') }}
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="block w-full text-left px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-sm">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </nav>
        </div>
    </div>
</div>
