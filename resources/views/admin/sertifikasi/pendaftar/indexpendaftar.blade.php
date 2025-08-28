<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    {{-- Navigasi Tab --}}
    @include('layouts.admin-sertifikasi-menu')
    <div class="p-6 bg-white dark:bg-gray-800 shadow-xl rounded-lg ">
        <div class="px-6 py-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                Daftar Pendaftar Skema: {{ $sertification->skema->nama_skema ?? 'Tidak Diketahui' }}
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full ">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            No
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Nama Asesi
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status Asesi
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status Pembayaran
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Aksi
                        </th>
                        
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 ">
                    @forelse ($sertification->asesi as $index => $asesi)
                        <tr class="">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $loop->iteration }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $asesi->student->name ?? 'Nama Tidak Tersedia' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if ($asesi->status == 'daftar')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100">
                                        Daftar
                                    </span>
                                @elseif($asesi->status == 'perlu_perbaikan_berkas')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100">
                                        Perlu perbaikan berkas
                                    </span>
                                @elseif($asesi->status == 'ditolak')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">
                                        Ditolak
                                    </span>
                                @elseif($asesi->status == 'dilanjutkan_asesmen')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                        Dilanjutkan ke asesmen
                                    </span>
                                @elseif($asesi->status == 'lulus_sertifikasi')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                        Lulus Sertifikasi
                                    </span>
                                @else
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200">
                                        {{ $asesi->status ?? 'N/A' }}
                                    </span>
                                @endif
                            </td>
                            @php
                                $latestTransaction = $asesi->transaction->sortByDesc('created_at')->first();
                            @endphp
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if ($latestTransaction)
                                    @if ($latestTransaction?->status == 'belum bayar')
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100">
                                            Belum Bayar
                                        </span>
                                    @elseif(
                                        $latestTransaction?->tipe == 'manual' &&
                                            $latestTransaction?->bukti_bayar &&
                                            $latestTransaction?->status == 'pending')
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100">
                                            Menunggu Verifikasi
                                        </span>
                                    @elseif($latestTransaction?->tipe == 'manual' && $latestTransaction?->status == 'bukti_pembayaran_terverifikasi')
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                            Pembayaran Terverifikasi
                                        </span>
                                    @elseif($latestTransaction?->tipe == 'manual' && $latestTransaction?->status == 'bukti_pembayaran_ditolak')
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">
                                            Bukti Pembayaran Ditolak
                                        </span>
                                    @endif
                                @else
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100">
                                        Belum Submit Bukti Pembayaran
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.sertifikasi.pendaftar.show', [$sertification->id, $asesi->id]) }}"
                                    class="cursor-pointer px-2 py-1 text-sm font-medium text-white bg-indigo-500 hover:bg-indigo-600 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-600 dark:hover:bg-indigo-700 dark:focus:ring-indigo-700">
                                    Detail
                                </a>
                            </td>
                            
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                Tidak ada data pendaftar untuk skema ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
