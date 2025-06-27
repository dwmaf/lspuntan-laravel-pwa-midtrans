<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pembayaran Sertifikasi') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 border-b border-gray-200 dark:border-gray-700">

                    <!-- Invoice Header -->
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Invoice Pembayaran</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Silakan periksa kembali rincian di bawah ini sebelum melanjutkan.</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">INVOICE #{{ $asesi_id }}-{{ time() }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal: {{ \Carbon\Carbon::now()->format('d M Y') }}</p>
                        </div>
                    </div>

                    <!-- Billing Info -->
                    <div class="mb-8">
                        <h3 class="text-base font-semibold text-gray-600 dark:text-gray-300 mb-2">Ditagihkan Kepada:</h3>
                        <p class="text-gray-800 dark:text-gray-200">{{ $name }}</p>
                        <p class="text-gray-600 dark:text-gray-400">{{ $email }}</p>
                        <p class="text-gray-600 dark:text-gray-400">{{ $no_tlp_hp }}</p>
                    </div>

                    <!-- Items Table -->
                    <div class="w-full overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="border-b border-gray-200 dark:border-gray-700">
                                <tr>
                                    <th scope="col" class="font-semibold text-gray-800 dark:text-gray-200 py-2">Deskripsi</th>
                                    <th scope="col" class="font-semibold text-gray-800 dark:text-gray-200 py-2 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <td class="py-4 text-gray-700 dark:text-gray-300">
                                        Biaya Pendaftaran Sertifikasi
                                        {{-- Asumsi variabel $sertification tersedia untuk nama skema --}}
                                        {{-- @if(isset($sertification))
                                            <span class="block text-xs text-gray-500">{{ $sertification->skema?->nama_skema }}</span>
                                        @endif --}}
                                    </td>
                                    <td class="py-4 font-medium text-gray-900 dark:text-white text-right">Rp{{ number_format($biaya, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Total Section -->
                    <div class="flex justify-end mt-6">
                        <div class="w-full max-w-xs space-y-2">
                            <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                <span>Subtotal</span>
                                <span>Rp{{ number_format($biaya, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold text-gray-900 dark:text-white pt-2 border-t border-gray-200 dark:border-gray-700">
                                <span>TOTAL</span>
                                <span>Rp{{ number_format($biaya, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Pay Button -->
                    <div class="flex justify-center mt-8">
                        <button id="pay-button" class="inline-flex items-center justify-center gap-x-2 w-full sm:w-auto font-semibold bg-green-500 text-white px-6 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-700 cursor-pointer transition-colors duration-200">
                            <x-fas-shield-halved class="w-5 h-5" />
                            <span>Bayar Sekarang dengan Aman</span>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    <script type="text/javascript">
        var snapToken = '{{ $snap_token }}';

        document.getElementById('pay-button').onclick = function() {
            snap.pay(snapToken, {
                onSuccess: function(result) {
                    // SARAN: Redirect ke halaman sukses daripada menggunakan alert.
                    // window.location.href = '/payment/success?order_id=' + result.order_id;
                    alert("Pembayaran Berhasil!");
                    console.log(result);
                },
                onPending: function(result) {
                    // SARAN: Redirect ke halaman pending.
                    // window.location.href = '/payment/pending?order_id=' + result.order_id;
                    alert("Pembayaran Anda masih pending.");
                    console.log(result);
                },
                onError: function(result) {
                    // SARAN: Redirect ke halaman error.
                    // window.location.href = '/payment/error';
                    alert("Terjadi kesalahan dalam pembayaran.");
                    console.log(result);
                }
            });
        };
    </script>
</x-app-layout>
