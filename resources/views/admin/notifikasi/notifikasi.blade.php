<x-admin-layout>
    <h1>Notifikasi Anda</h1>
    <ul>
        @forelse($notifications as $notification)
            <li>
                <a href="{{ $notification->data['link'] ?? '#' }}">
                    {{ $notification->data['message'] }}
                </a>
                <small>{{ $notification->created_at->diffForHumans() }}</small>
            </li>
        @empty
            <li>Tidak ada notifikasi.</li>
        @endforelse
    </ul>
    {{ $notifications->links() }}
</x-admin-layout>