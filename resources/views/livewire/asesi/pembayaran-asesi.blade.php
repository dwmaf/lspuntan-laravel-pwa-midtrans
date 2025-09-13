<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    <div x-data="{ show: false, message: '' }"
        x-on:notify.window="message=$event.detail.message;show=true;setTimeout(()=>show=false,3000)" x-show="show"
        x-transition x-text="message" class="fixed top-20 right-4 text-xs px-3 py-2 rounded bg-green-600 text-white z-50"
        style="display:none">
    </div>
    @include('layouts.asesi-sertifikasi-menu')

    @if (
        ($asesi->transaction->first()?->tipe == 'manual' &&
            $asesi->transaction->first()?->status == 'bukti_pembayaran_terverifikasi') ||
            $asesi->transaction->first()?->status == 'paid')
        <div class="p-6 sm:p-8 bg-white dark:bg-gray-800 rounded-tr-lg rounded-bl-lg rounded-br-lg shadow-md">
            <div class="flex flex-col items-center text-center">
                {{-- Ikon Sukses --}}
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                    <svg class="w-12 h-12 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>

                {{-- Pesan Utama --}}
                <h3 class="mt-4 text-2xl font-bold text-gray-800 dark:text-white">Pembayaran Lunas</h3>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Terima kasih, pembayaran Anda telah kami terima dan
                    verifikasi.</p>
            </div>

            {{-- Garis Pemisah --}}
            <hr class="my-6 border-gray-200 dark:border-gray-700">

            {{-- Rincian Transaksi --}}
            <div class="space-y-4">
                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Rincian Transaksi</h4>
                <dl class="space-y-2 text-sm">
                    @if ($asesi->transaction->first()?->tipe != 'manual')
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-600 dark:text-gray-400">Nomor Invoice</dt>
                            <dd class="font-mono text-gray-900 dark:text-white">
                                {{ $asesi->transaction->first()->invoice_number }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-600 dark:text-gray-400">Tanggal Pembayaran</dt>
                            <dd class="font-medium text-gray-900 dark:text-white">
                                {{ $asesi->transaction->first()->updated_at->format('d F Y, H:i') }}</dd>
                        </div>
                    @endif
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-600 dark:text-gray-400">Metode</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">
                            @if ($asesi->transaction->first()->tipe == 'manual')
                                Transfer Manual
                            @else
                                Online (Midtrans)
                            @endif
                        </dd>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                        <dt class="text-base font-semibold text-gray-900 dark:text-white">Total Bayar</dt>
                        <dd class="text-base font-semibold text-gray-900 dark:text-white">
                            Rp{{ number_format($asesi->sertification->harga, 0, ',', '.') }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    @else
        {{-- Tampilkan form pembayaran jika belum lunas --}}
        {{-- <div class="p-6 bg-white dark:bg-gray-800 rounded-tr-lg rounded-bl-lg rounded-br-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">
                Pembayaran</h3>
            <h3 class="dark:text-gray-300">Bayar dengan fitur in-app-payment yang disediakan oleh Aplikasi ini yang
                terintegrasi
                dengan Midtrans
                Payment</h3>
            <form action="{{ route('asesi.applied.payment.checkout', [$sertification->id, $asesi->id]) }}"
                method="POST" class="mt-6 space-y-6">
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
                            <dd class="font-medium text-gray-900 dark:text-white">
                                {{ $sertification->skema?->nama_skema }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-600 dark:text-gray-400">Asesor</dt>
                            <dd class="font-medium text-gray-900 dark:text-white">{{ $sertification->asesor?->name }}
                            </dd>
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
        </div> --}}

        {{-- garis pemisah antara pembayaran manual dgn midtrans --}}
        {{-- <div class="relative flex py-5 items-center">
            <div class="flex-grow border-t border-gray-400 dark:border-gray-600"></div>
            <span class="flex-shrink mx-4 text-gray-500 dark:text-gray-400">ATAU</span>
            <div class="flex-grow border-t border-gray-400 dark:border-gray-600"></div>
        </div> --}}

        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md mb-2">
            @if ($sertification->rincian_pembayaran)
                {{-- foto profil, nama, dan tanggal rincian pembayaran dibuat --}}
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex-shrink-0">
                        <svg class="h-10 w-10 text-gray-400 dark:text-gray-600 rounded-full bg-gray-200 dark:bg-gray-700 p-1"
                            fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <h5 class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                            @if ($sertification->pembuatrincianpembayaran->asesor)
                                {{-- Jika pembuatnya adalah seorang asesor, tampilkan nama dari tabel asesor --}}
                                {{ $sertification->pembuatrincianpembayaran->asesor->user->name }}
                            @else
                                {{-- Fallback jika karena suatu hal data pembuat tidak ada --}}
                                Admin
                            @endif
                        </h5>
                        <div class="text-xs text-gray-400">
                            @if (\Carbon\Carbon::parse($sertification->rincian_bayar_dibuat_pada)->isToday())
                                {{-- Jika hari ini, tampilkan jam --}}
                                {{ \Carbon\Carbon::parse($sertification->rincian_bayar_dibuat_pada)->format('H:i') }}
                            @else
                                {{-- Jika sudah lewat, tampilkan tanggal --}}
                                {{ \Carbon\Carbon::parse($sertification->rincian_bayar_dibuat_pada)->format('d M Y') }}
                            @endif
                        </div>
                    </div>
                </div>
                <h6 class="font-medium text-sm text-gray-800 dark:text-gray-100 mb-2">{!! $sertification?->rincian_pembayaran !!}</h6>
                <div class="flex">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mr-1">Biaya Sertifikasi : </dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-100">Rp
                        {{ number_format($sertification->harga, 0, ',', '.') ?? 'N/A' }}</dd>
                </div>
                <form wire:submit.prevent="save" class="mt-6 space-y-6">
                    <div>
                        <x-input-label>Bukti Pembayaran</x-input-label>
                        <x-file-input type="file" wire:model.defer="bukti_bayar"
                            @if (!$sertification->rincian_pembayaran) disabled @endif />
                        <x-input-error class="mt-2" :messages="$errors->get('bukti_bayar')" />
                    </div>
                    <div class="flex items-center gap-4 pt-2">
                        <span wire:loading wire:target="save" class="flex items-center">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </span>
                        <x-primary-button wire:loading.remove wire:target="save" @if (!$sertification->rincian_pembayaran) disabled @endif>
                            Submit Bukti Pembayaran
                        </x-primary-button>
                        
                    </div>
                    
                </form>
            @else
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Admin belum memberikan rincian pembayaran
                </p>
            @endif
        </div>
    @endif

</div>
