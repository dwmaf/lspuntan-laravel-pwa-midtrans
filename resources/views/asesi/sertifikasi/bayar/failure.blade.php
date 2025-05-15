<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    <div class="container">
        <h1>Payment Failed</h1>
        <p>Unfortunately, your payment could not be processed. Please try again later or contact support.</p>
        <a href="{{ route('payment.form', $sertification->id) }}" class="btn btn-danger">Retry Payment</a>
    </div>
</x-app-layout>
