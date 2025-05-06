<aside class="fixed left-0 top-0 z-40 h-screen w-64 bg-white dark:bg-gray-800 shadow-lg transition-transform lg:translate-x-0" id="sidebar">
    <!-- Logo Section -->
    <div class="flex items-center justify-center h-20 border-b border-gray-200 dark:border-gray-700">
        <a href="{{ route('dashboard') }}" class="flex items-center">
            <x-application-logo class="h-12 w-auto fill-current text-primary-600 dark:text-primary-400" />
        </a>
    </div>
    
    <!-- User Profile Section -->
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center space-x-3">
            <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                <span class="text-lg font-medium text-gray-700 dark:text-gray-200">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </span>
            </div>
            <div>
                <p class="font-medium text-gray-700 dark:text-gray-200">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ ucfirst(Auth::user()->role) }}</p>
            </div>
        </div>
    </div>
    
    <!-- Navigation Links -->
    <div class="py-4 px-3">
        <ul class="space-y-2">
            <!-- Home Link -->
            @if (auth()->user()->role == 'asesor' || auth()->user()->role == 'admin')
                <li>
                    <a href="{{ route('dashboardadmin') }}" 
                       class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-primary-50 dark:hover:bg-gray-700 group transition-all duration-200 {{ request()->routeIs('dashboardadmin') ? 'bg-primary-50 dark:bg-gray-700 text-primary-600 dark:text-primary-400 font-medium' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 {{ request()->routeIs('dashboardadmin') ? 'text-primary-500 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>{{ __('Home') }}</span>
                    </a>
                </li>
            @else
                <li>
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-primary-50 dark:hover:bg-gray-700 group transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-primary-50 dark:bg-gray-700 text-primary-600 dark:text-primary-400 font-medium' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-primary-500 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>{{ __('Home') }}</span>
                    </a>
                </li>
            @endif
            
            <!-- Sertifikasi Link -->
            <li>
                @if (auth()->user()->role == 'asesor' || auth()->user()->role == 'admin')
                    <a href="/sertification" 
                       class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-primary-50 dark:hover:bg-gray-700 group transition-all duration-200 {{ request()->is('sertification') ? 'bg-primary-50 dark:bg-gray-700 text-primary-600 dark:text-primary-400 font-medium' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 {{ request()->is('sertification') ? 'text-primary-500 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>{{ __('Sertifikasi') }}</span>
                    </a>
                @else
                    <a href="/sertification-asesi" 
                       class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-primary-50 dark:hover:bg-gray-700 group transition-all duration-200 {{ request()->is('sertification-asesi') ? 'bg-primary-50 dark:bg-gray-700 text-primary-600 dark:text-primary-400 font-medium' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 {{ request()->is('sertification-asesi') ? 'text-primary-500 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>{{ __('Sertifikasi') }}</span>
                    </a>
                @endif
            </li>
            
            <!-- Asesor Link (Only for admin and asesor roles) -->
            @if (auth()->user()->role == 'asesor' || auth()->user()->role == 'admin')
                <li>
                    <a href="/asesor" 
                       class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-primary-50 dark:hover:bg-gray-700 group transition-all duration-200 {{ request()->is('asesor') ? 'bg-primary-50 dark:bg-gray-700 text-primary-600 dark:text-primary-400 font-medium' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 {{ request()->is('asesor') ? 'text-primary-500 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>{{ __('Asesor') }}</span>
                    </a>
                </li>
            @endif
            
            <!-- Profile Link -->
            <li>
                @if (auth()->user()->role == 'asesor' || auth()->user()->role == 'admin')
                    <a href="/profile" 
                       class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-primary-50 dark:hover:bg-gray-700 group transition-all duration-200 {{ request()->is('profile') ? 'bg-primary-50 dark:bg-gray-700 text-primary-600 dark:text-primary-400 font-medium' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 {{ request()->is('profile') ? 'text-primary-500 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>{{ __('Profile') }}</span>
                    </a>
                @else
                    <a href="/profile_asesi" 
                       class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-primary-50 dark:hover:bg-gray-700 group transition-all duration-200 {{ request()->is('profile_asesi') ? 'bg-primary-50 dark:bg-gray-700 text-primary-600 dark:text-primary-400 font-medium' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 {{ request()->is('profile_asesi') ? 'text-primary-500 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>{{ __('Profile') }}</span>
                    </a>
                @endif
            </li>
        </ul>
    </div>
    
    <!-- Logout Section -->
    <div class="mt-auto px-3 pb-5 border-t border-gray-200 dark:border-gray-700 pt-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex w-full items-center px-4 py-3 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 group transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-gray-500 dark:text-gray-400 group-hover:text-red-500 dark:group-hover:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="group-hover:text-red-500 dark:group-hover:text-red-400">{{ __('Log Out') }}</span>
            </button>
        </form>
    </div>
</aside>

<!-- Mobile Menu Toggle Button -->
<div class="lg:hidden fixed bottom-4 right-4 z-50">
    <button id="mobile-menu-toggle" class="p-3 bg-primary-500 dark:bg-primary-600 text-white rounded-full shadow-lg focus:outline-none">
        <svg id="menu-open-icon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
        <svg id="menu-close-icon" xmlns="http://www.w3.org/2000/svg" class="hidden h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('sidebar');
        const toggleButton = document.getElementById('mobile-menu-toggle');
        const openIcon = document.getElementById('menu-open-icon');
        const closeIcon = document.getElementById('menu-close-icon');

        // Atur posisi sidebar awal (hanya untuk layar kecil)
        if (window.innerWidth < 1024) {
            sidebar.classList.add('-translate-x-full');
        }

        // Toggle sidebar
        toggleButton.addEventListener('click', function () {
            const isHidden = sidebar.classList.contains('-translate-x-full');

            if (isHidden) {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                openIcon.classList.add('hidden');
                closeIcon.classList.remove('hidden');
            } else {
                sidebar.classList.remove('translate-x-0');
                sidebar.classList.add('-translate-x-full');
                openIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
            }
        });
    });
</script>
