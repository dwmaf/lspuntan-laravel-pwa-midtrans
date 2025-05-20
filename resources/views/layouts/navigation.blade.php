{{-- <div x-data="{ open: false }" class="relative z-50">

    <!-- Header (topbar) -->
    <div class="mt-2 ml-2 rounded-2xl w-full bg-white dark:bg-gray-800 shadow-sm p-3 flex items-center justify-center cursor-pointer">
        <!-- Burger Button -->
        <button @click="open = !open" class="text-gray-800 dark:text-white focus:outline-none cursor-pointer">
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
                        class="block w-full text-left px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-sm cursor-pointer">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </nav>
        </div>
    </div>
</div> --}}

<div x-data="{ open: false }">
    <aside class="h-screen bg-gray-800 p-3 transform transition-all duration-300 "
        :class="open ? 'w-48 translate-x-0' : 'w-16 translate-x-0'">
        <!-- Sidebar Toggle Button -->
        <button @click="open = !open" class="text-gray-700 dark:text-gray-200 cursor-pointer mb-2 absolute top-5 right-5">
            <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Sidebar Content -->
        @if (auth()->user()->role == 'asesor' || auth()->user()->role == 'admin')
            <a href="{{ route('dashboardadmin') }}"
                class="w-full flex items-center gap-2 leading-none mt-2 mb-2 px-3 py-2 hover:bg-gray-700 rounded-sm"
                :class="(open && location.pathname === '/dashboardadmin') ? 'bg-gray-800' : ''">
                <x-fas-home class="w-4 text-gray-700 dark:text-gray-200"/>
                <span class="text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-sm"
                    :class="!open ? 'hidden' : ''">
                    Home
                </span>
            </a>
        @else
            <a href="/dashboard"
                class="w-full flex items-center gap-2 leading-none mt-10 mb-2 px-3 py-2 hover:bg-gray-700 rounded-sm"
                :class="(open && location.pathname === '/dashboard') ? 'bg-gray-800' : ''">
                <x-fas-home class="w-4 text-gray-700 dark:text-gray-200"/>
                <span class="text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-sm"
                    :class="!open ? 'hidden' : ''">
                    Home
                </span>
            </a>
        @endif

        @if (auth()->user()->role == 'asesor' || auth()->user()->role == 'admin')
            <a href="/sertification"
                class="w-full flex items-center gap-2 leading-none mt-2 mb-2 px-3 py-2 hover:bg-gray-700 rounded-sm"
                :class="(open && location.pathname === '/sertification') ? 'bg-gray-800' : ''">
                <x-fas-award class="w-4 text-gray-700 dark:text-gray-200"/>
                <span class="text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-sm"
                    :class="[!open ? 'hidden' : '']">
                    Sertifikasi
                </span>
            </a>
        @else
            <a href="/sertification-asesi"
                class="w-full flex items-center gap-2 leading-none mt-2 mb-2 px-3 py-2 hover:bg-gray-700 rounded-sm"
                :class="(open && location.pathname === '/sertification-asesi') ? 'bg-gray-800' : ''">
                <x-fas-award class="w-4 text-gray-700 dark:text-gray-200"/>
                <span class="text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-sm"
                    :class="[!open ? 'hidden' : '']">
                    Sertifikasi
                </span>
            </a>
        @endif

        @if (auth()->user()->role == 'asesor' || auth()->user()->role == 'admin')
            <a href="/asesor"
                class="w-full flex items-center gap-2 leading-none mt-2 mb-2 px-3 py-2 hover:bg-gray-700 rounded-sm"
                :class="(open && location.pathname === '/asesor') ? 'bg-gray-800' : ''">
                <x-fas-graduation-cap class="w-4 text-gray-700 dark:text-gray-200"/>
                <span class="text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-sm"
                    :class="[ !open ? 'hidden' : '']">
                    Asesor
                </span>
            </a>
        @endif

        @if (auth()->user()->role == 'asesor' || auth()->user()->role == 'admin')
            <a href="/profile"
                class="w-full flex items-center gap-2 leading-none mt-2 mb-2 px-3 py-2 hover:bg-gray-700 rounded-sm"
                :class="(open && location.pathname === '/profile') ? 'bg-gray-800' : ''">
                <x-fas-user class="w-4 text-gray-700 dark:text-gray-200"/>
                <span class="text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-sm"
                    :class="[ !open ? 'hidden' : '']">
                    Profile
                </span>
            </a>
        @else
            <a href="/profile_asesi"
                class="w-full flex items-center gap-2 leading-none mt-2 mb-2 px-3 py-2 hover:bg-gray-700 rounded-sm"
                :class="(open && location.pathname === '/profile_asesi') ? 'bg-gray-800' : ''">
                <x-fas-user class="w-4 text-gray-700 dark:text-gray-200"/>
                <span class="text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-sm"
                    :class="[ !open ? 'hidden' : '']">
                    Profile
                </span>
            </a>
        @endif
        <form method="POST" action="{{ route('logout') }}" class="flex items-center w-full text-left px-3 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-sm cursor-pointer">
            @csrf
            <x-fas-right-from-bracket class="w-4 text-gray-700 dark:text-gray-200"/>
            <button type="submit"
                :class="!open ? 'hidden' : ''">
                {{ __('Log Out') }}
            </button>
        </form>


    </aside>
</div>
