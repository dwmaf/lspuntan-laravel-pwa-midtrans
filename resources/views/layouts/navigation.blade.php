<div x-data="{ open: true }">
    <aside class="h-full  bg-white dark:bg-gray-800 p-3 transform transition-all duration-300 "
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
        <h6 class="text-gray-700 mt-10 dark:text-gray-200" :class="!open ? 'hidden' : ''">Selamat datang,
            {{ auth()->user()->name }}</h6>

        @if (auth()->user()->role == 'asesor' || auth()->user()->role == 'admin')
            <a href="{{ route('admin.dashboard') }}"
                class="w-full flex items-center gap-2 leading-none mt-2 mb-2 px-3 py-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-sm"
                :class="[(open && location.pathname === '/admin/dashboard') ? 'bg-gray-200 dark:bg-gray-800' : '', !open ?
                    'mt-10' : ''
                ]">
                <x-bxs-grid-alt class="w-5 text-gray-700 dark:text-gray-200" />

                <span class="text-gray-700 dark:text-gray-200 rounded-sm" :class="!open ? 'hidden' : ''">
                    Dashboard
                </span>
            </a>
        @else
            <a href="{{ route('asesi.dashboard') }}"
                class="w-full flex items-center gap-2 leading-none mt-2 mb-2 px-3 py-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-sm"
                :class="[(open && location.pathname === '/asesi/dashboard') ? 'bg-gray-200 dark:bg-gray-800' : '', !open ? 'mt-10' :
                    ''
                ]">
                <x-bxs-grid-alt class="w-5 text-gray-700 dark:text-gray-200" />
                <span class="text-gray-700 dark:text-gray-200 rounded-sm" :class="!open ? 'hidden' : ''">
                    Dashboard
                </span>
            </a>
        @endif

        @if (auth()->user()->role == 'asesor' || auth()->user()->role == 'admin')
            <a href="/sertification"
                class="w-full flex items-center gap-2 leading-none mt-2 mb-2 px-3 py-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-sm"
                :class="(open && location.pathname === '/sertification') ? 'bg-gray-200 dark:bg-gray-800' : ''">
                <x-tni-certificate class="w-4 text-gray-700 dark:text-gray-200" />
                <span class="text-gray-700 dark:text-gray-200 rounded-sm" :class="[!open ? 'hidden' : '']">
                    Sertifikasi
                </span>
            </a>
        @else
            <a href="{{ route('asesi.certifications.index') }}"
                class="w-full flex items-center gap-2 leading-none mt-2 mb-2 px-3 py-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-sm"
                :class="(location.pathname === '/asesi/certifications') ? 'bg-gray-200 dark:bg-gray-800' : ''">
                <x-tni-certificate class="w-4 text-gray-700 dark:text-gray-200" />
                <span class="text-gray-700 dark:text-gray-200 rounded-sm" :class="[!open ? 'hidden' : '']">
                    Sertifikasi
                </span>
            </a>
        @endif

        @if (auth()->user()->role == 'asesor' || auth()->user()->role == 'admin')
            <a href="{{ route('admin.skema') }}"
                class="w-full flex items-center gap-2 leading-none mt-2 mb-2 px-3 py-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-sm"
                :class="(open && location.pathname === '/admin/skema') ? 'bg-gray-200 dark:bg-gray-800' : ''">
                <x-bxs-book class="w-4 text-gray-700 dark:text-gray-200" />
                <span class="text-gray-700 dark:text-gray-200 rounded-sm" :class="[!open ? 'hidden' : '']">
                    Skema
                </span>
            </a>
            <a href="/asesor"
                class="w-full flex items-center gap-2 leading-none mt-2 mb-2 px-3 py-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-sm"
                :class="(open && location.pathname === '/asesor') ? 'bg-gray-200 dark:bg-gray-800' : ''">
                <x-fas-chalkboard-teacher class="w-4 text-gray-700 dark:text-gray-200" />
                <span class="text-gray-700 dark:text-gray-200 rounded-sm" :class="[!open ? 'hidden' : '']">
                    Asesor
                </span>
            </a>
        @endif

        @if (auth()->user()->role == 'asesor' || auth()->user()->role == 'admin')
            <a href="/profile"
                class="w-full flex items-center gap-2 leading-none mt-2 mb-2 px-3 py-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-sm"
                :class="(open && location.pathname === '/profile') ? 'bg-gray-200 dark:bg-gray-800' : ''">
                <x-fas-user class="w-4 text-gray-700 dark:text-gray-200" />
                <span class="text-gray-700 dark:text-gray-200  rounded-sm" :class="[!open ? 'hidden' : '']">
                    Profile
                </span>
            </a>
        @else
            <a href="/profile_asesi"
                class="w-full flex items-center gap-2 leading-none mt-2 mb-2 px-3 py-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-sm"
                :class="(open && location.pathname === '/profile_asesi') ? 'bg-gray-200 dark:bg-gray-800' : ''">
                <x-fas-user class="w-4 text-gray-700 dark:text-gray-200" />
                <span class="text-gray-700 dark:text-gray-200 rounded-sm" :class="[!open ? 'hidden' : '']">
                    Profile
                </span>
            </a>
        @endif
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="gap-2 flex items-center w-full px-3 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-sm cursor-pointer">
                <x-fas-right-from-bracket class="w-4 text-gray-700 dark:text-gray-200" />
                <span class="text-gray-700 dark:text-gray-200 rounded-sm" :class="[!open ? 'hidden' : '']">
                    Logout
                </span>
            </button>
        </form>
    </aside>
</div>
