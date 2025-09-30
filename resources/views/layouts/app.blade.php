<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LSP UNTAN') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- @livewireStyles --}}
    {{-- <link rel="icon" href="{{ asset('logo-lsp.png') }}" type="image/png"> --}}
    @laravelPWA
    @inertiaHead
</head>
<style>
    .min-h-12 {
        min-height: 3rem;
    }
</style>

<body class="font-sans antialiased">
    <div class="flex min-h-screen bg-gray-100 dark:bg-gray-900">
        {{-- Sidebar untuk Desktop (md dan lebih besar) --}}
        <div class="hidden md:flex">
            @include('layouts.navigation')
        </div>

        <div class="flex-1 flex flex-col">
            <div class="md:hidden mb-2">
                @include('layouts.top-navigation')
            </div>
            @isset($header)
                <header x-data="{ showNotifikasi: false }"
                    class="bg-white dark:bg-gray-800 shadow-sm p-4 m-2 flex items-center justify-between relative z-10">
                    <div class="flex-1">
                        {{ $header }}
                    </div>
                    <div class="relative flex items-center ml-4">
                        {{-- Ikon Lonceng --}}
                        <button @click="showNotifikasi = !showNotifikasi" class="relative focus:outline-none cursor-pointer">
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
                        <div x-show="showNotifikasi"
                             @click.outside="showNotifikasi = false"
                            class="absolute right-0 top-full mt-2 w-80 bg-white dark:bg-gray-700 rounded-md shadow-lg z-10 border border-gray-200 dark:border-gray-600">
                            <div class="max-h-64 overflow-y-auto divide-y divide-gray-200 dark:divide-gray-600">
                                @auth
                                    @php $latestNotif = auth()->user()->notifications()->latest()->take(5)->get(); @endphp
                                    @forelse ($latestNotif as $notif)
                                        @php
                                            $data = $notif->data;
                                            $isUnread = is_null($notif->read_at);
                                        @endphp
                                        <a href="{{ $data['link'] ?? '#' }}"
                                            class="notification-item block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600">
                                            <div class="flex items-start justify-between">
                                                <div class="{{ $isUnread ? 'text-gray-800 dark:text-gray-100' : 'text-gray-400' }} truncate">
                                                    {{ $data['message'] ?? 'Notifikasi' }}</div>
                                                <div class="text-xs text-gray-500 ml-2">{{ $notif->created_at->diffForHumans() }}
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

            <main class="p-2">
                {{ $slot }}
            </main>
        </div>

    </div>
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
    {{-- @livewireScripts --}}
    @inertia
</body>

</html>
