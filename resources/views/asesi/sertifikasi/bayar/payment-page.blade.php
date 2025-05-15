<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    <div id="payment-container">
        <button id="pay-button" onclick="payUsingMidtrans()">Pay Using Midtrans</button>
    </div>

    <script type="text/javascript" src="https://app.midtrans.com/snap/snap.js"
        data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    <script type="text/javascript">
        function payUsingMidtrans() {
            snap.pay('{{ $snapToken }}', {
                onSuccess: function(result) {
                    window.location.href = "{{ route('payment.success') }}?order_id=" + result.order_id;
                },
                onFailure: function(result) {
                    window.location.href = "{{ route('payment.failure') }}";
                },
                onPending: function(result) {
                    window.location.href = "{{ route('payment.pending') }}";
                }
            });
        }
    </script>


</x-app-layout>
