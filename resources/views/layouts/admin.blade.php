<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LSP UNTAN') }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="{{ asset('logo-lsp.png') }}" type="image/png">

</head>

<body class="font-sans antialiased">
    <div class="flex min-h-screen bg-gray-100 dark:bg-gray-900">
        <div class="hidden md:flex">
            @include('layouts.navigation')
        </div>

        <div class="flex-1 flex flex-col min-w-0">
            <div class="md:hidden mb-2">
                @include('layouts.top-navigation')
            </div>
            @isset($header)
                <header x-data="{ showNotifikasi: false }"
                    class="bg-white dark:bg-gray-800 shadow-sm p-4 m-2 flex items-center justify-between relative">
                    <div class="flex-1">
                        {{ $header }}
                    </div>
                    <div class="relative flex items-center ml-4">
                        {{-- Ikon Lonceng --}}
                        <button @click="showNotifikasi = !showNotifikasi" class="relative focus:outline-none">
                            <x-fas-bell class="w-5 h-5 text-gray-700 dark:text-gray-200" />
                            @auth
                                @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
                                @if ($unreadCount > 0)
                                    <span id="notif-badge"
                                        class="absolute -top-1 -right-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full p-1">
                                        {{ $unreadCount }}
                                    </span>
                                @endif
                            @endauth
                        </button>

                        {{-- Dropdown Daftar Notifikasi --}}
                        <div x-show="showNotifikasi" x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95" @click.outside="showNotifikasi = false"
                            class="absolute right-0 top-full mt-2 w-80 bg-white dark:bg-gray-700 rounded-md shadow-lg z-10 border border-gray-200 dark:border-gray-600">
                            <div class="py-2 max-h-64 overflow-y-auto">
                                @auth
                                    @php $latest = auth()->user()->notifications()->latest()->take(5)->get(); @endphp
                                    @forelse ($latest as $n)
                                        @php
                                            $data = $n->data;
                                            $isUnread = is_null($n->read_at);
                                        @endphp
                                        <a href="{{ $data['link'] ?? '#' }}"
                                            class="notification-item block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 {{ $isUnread ? 'bg-gray-50 dark:bg-gray-800' : '' }}">
                                            <div class="flex items-start justify-between">
                                                <div class="text-gray-800 dark:text-gray-100 truncate">
                                                    {{ $data['message'] ?? 'Notifikasi' }}</div>
                                                <div class="text-xs text-gray-500 ml-2">{{ $n->created_at->diffForHumans() }}
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="px-4 py-3 text-sm text-gray-500">Tidak ada notifikasi.</div>
                                    @endforelse
                                @endauth
                            </div>

                            <div
                                class="border-t border-gray-100 dark:border-gray-600 flex items-center justify-between px-3 py-2 bg-gray-50 dark:bg-gray-800 rounded-b-md">
                                <button id="mark-all-read-btn" onclick="markAllRead()"
                                    class="text-xs text-gray-700 dark:text-gray-300 hover:underline">Tandai dibaca
                                    semua</button>
                                <a href="{{ route('notifications.index') }}"
                                    class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Lihat semua</a>
                            </div>
                        </div>
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="p-2">
                {{ $slot }}
            </main>
        </div>

    </div>
    @stack('scripts')
    <script>
        async function markAllRead() {
            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const res = await fetch("{{ route('notifications.markAllRead') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                });
                if (!res.ok) throw new Error('Network response was not ok');
                // update UI: hide badge and remove unread styling
                const badge = document.getElementById('notif-badge');
                if (badge) badge.remove();
                document.querySelectorAll('.notification-item').forEach(el => el.classList.remove('bg-gray-50',
                    'dark:bg-gray-800'));
            } catch (e) {
                console.error(e);
            }
        }
    </script>
    @stack('scripts-asesmen')
</body>

</html>
