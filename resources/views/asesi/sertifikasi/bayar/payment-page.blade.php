<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    <div>
        <input type="text" name="asesi_id" hidden required value="{{ $asesi_id }}">
        <input type="text" name="name" hidden required value="{{ $name }}">
        <input type="text" name="email" hidden required value="{{ $email }}">
        <input type="text" name="no_tlp_hp" hidden required value="{{ $no_tlp_hp }}">
        <input type="number" name="biaya" hidden required value="{{ $biaya }}">
        <button id="pay-button">Bayar Sekarang</button>
        <script type="text/javascript">
            // Ambil snap_token yang dikirim dari backend
            var snapToken = '{{ $snap_token }}';

            // Inisialisasi Snap untuk memproses pembayaran
            document.getElementById('pay-button').onclick = function() {
                snap.pay(snapToken, {
                    onSuccess: function(result) {
                        alert("Pembayaran Berhasil!");
                        console.log(result);
                        // Kirim data ke backend untuk mengubah status transaksi
                        // Anda bisa menambahkan Ajax untuk mengirim status transaksi ke server
                    },
                    onPending: function(result) {
                        alert("Pembayaran Anda masih pending.");
                        console.log(result);
                        // Anda bisa menambahkan Ajax untuk mengirim status transaksi ke server
                    },
                    onError: function(result) {
                        alert("Terjadi kesalahan dalam pembayaran.");
                        console.log(result);
                        // Anda bisa menambahkan Ajax untuk mengirim status transaksi ke server
                    }
                });
            };
        </script>
        <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    </div>
</x-app-layout>
