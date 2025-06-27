<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    @include('layouts.asesi-sertifikasi-menu')
    <div class="p-6 bg-white dark:bg-gray-800 rounded-tr-lg rounded-bl-lg rounded-br-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">
            Pembayaran</h3>
        <h3 class="dark:text-gray-300">Bayar dengan fitur in-app-payment yang disediakan oleh Aplikasi ini yang terintegrasi
            dengan Midtrans
            Payment</h3>
        <form action="{{ route('asesi.applied.payment.checkout', [$sertification->id, $asesi->id]) }}" method="POST" class="mt-6 space-y-6">
            @csrf
            <!-- Rincian Pembayaran -->
            <div class="space-y-4 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200">Rincian Pembayaran</h4>
                <dl class="space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-600 dark:text-gray-400">Nama Lengkap</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $asesi->student?->name }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-600 dark:text-gray-400">No. HP</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $asesi->student?->no_tlp_hp }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-600 dark:text-gray-400">Sertifikasi</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $sertification->skema?->nama_skema }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-600 dark:text-gray-400">Asesor</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $sertification->asesor?->name }}</dd>
                    </div>
                    <div class="pt-2">
                        <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
                    </div>
                    <div class="flex items-center justify-between pt-2">
                        <dt class="text-lg font-semibold text-gray-900 dark:text-white">Total Biaya</dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-white">
                            Rp{{ number_format($sertification?->harga, 0, ',', '.') }}
                        </dd>
                    </div>
                </dl>
            </div>
            <div class="flex justify-center">
                <button type="submit"
                    class="inline-flex items-center gap-x-2 font-medium bg-green-500 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-700 cursor-pointer">
                    <x-fas-wallet class="w-4 h-4" />
                    <span>Lanjutkan ke Pembayaran</span>
                </button>
            </div>
        </form>

    </div>

    <div class="relative flex py-5 items-center">
        <div class="flex-grow border-t border-gray-400 dark:border-gray-600"></div>
        <span class="flex-shrink mx-4 text-gray-500 dark:text-gray-400">ATAU</span>
        <div class="flex-grow border-t border-gray-400 dark:border-gray-600"></div>
    </div>

    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md mb-2">
        <h3 class="dark:text-gray-300 mb-2">Silahkan bayar ke virtual account yang sudah disiapkan berikut dan submit bukti
            pembayarannya di form
            berikut</h3>
        <form action="{{ route('asesi.applied.payment.manual.store', [$sertification->id, $asesi->id]) }}" method="POST" class="mt-6 space-y-6" enctype="multipart/form-data">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Bukti pembayaran</label>
                <input id="bukti_pembayaran" name="bukti_pembayaran" type="file"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:outline-hidden focus:ring-2 focus:border-blue-800 focus:ring-blue-800 rounded-md"
                    required>
                <x-input-error class="mt-2" :messages="$errors->get('bukti_pembayaran')" />
            </div>
            <button type="submit"
                class="self-start font-medium bg-indigo-500 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-600 dark:hover:bg-indigo-700 dark:focus:ring-indigo-700 cursor-pointer">Submit
                Bukti Pembayaran</button>
        </form>
    </div>
</x-app-layout>
