<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>

    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <div>
            <a href="/list_asesi/{{ $sertification[0]->id }}">Lihat Pendaftar</a>
            <a href="/sertification/{{ $sertification[0]->id }}/edit">Edit</a>
            <a href="/rincian_praasesmen/{{ $sertification[0]->id }}/edit">Pra-asesmen</a>
            <form action="/sertification/{{ $sertification[0]->id }}" method="post">
                @method('delete')
                @csrf
                <button onclick="return confirm('Hapus data {{ $sertification[0]->id }}?')">Hapus</button>
            </form>
        </div>
        <h5>{{ $sertification[0]->skema->nama_skema }}</h5>
    </div>



</x-admin-layout>
