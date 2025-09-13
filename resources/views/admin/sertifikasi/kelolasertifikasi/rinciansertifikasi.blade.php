<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Rincian Sertifikasi') }}
        </h2>
    </x-slot>
    @include('layouts.admin-sertifikasi-menu')
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 sm:mb-0">
                Detail Sertifikasi
            </h3>
            {{-- {{ $sertification }} --}}
            <div class="flex items-center space-x-3">
                <x-edit-button href="{{ route('admin.kelolasertifikasi.edit', $sertification->id) }}" wire:navigate>Edit</x-edit-button>
                
                @if ($sertification->status == 'berlangsung')
                    <form class="inline-block" action="{{ route('admin.kelolasertifikasi.destroy',$sertification->id) }}"
                        method="post"
                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus data sertifikasi ini? Ini tidak akan menghapus skema atau asesor terkait, hanya jadwal sertifikasi ini.');">
                        @method('delete')
                        @csrf
                        <x-delete-button type="submit">Hapus</x-delete-button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Skema</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                    {{ $sertification->skema->nama_skema ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Asesor</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $sertification->asesor->user->name ?? 'N/A' }}
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Pendaftaran Dibuka</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                    {{ \Carbon\Carbon::parse($sertification->tgl_apply_dibuka)->isoFormat('D MMMM YYYY') ?? 'N/A' }}
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Pendaftaran Ditutup</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                    {{ \Carbon\Carbon::parse($sertification->tgl_apply_ditutup)->isoFormat('D MMMM YYYY') ?? 'N/A' }}
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Batas Akhir Pembayaran</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                    {{ \Carbon\Carbon::parse($sertification->tgl_bayar_ditutup)->isoFormat('D MMMM YYYY') ?? 'N/A' }}
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Biaya Sertifikasi</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">Rp
                    {{ number_format($sertification->harga, 0, ',', '.') ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">TUK</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                    {{ $sertification->tuk ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                <dd class="mt-1 text-sm">
                    @if ($sertification->status == 'berlangsung')
                        <span
                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                            Sedang Berlangsung
                        </span>
                    @else
                        <span
                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100">
                            Selesai
                        </span>
                    @endif
                </dd>
            </div>
        </div>
    </div>
</x-admin-layout>
