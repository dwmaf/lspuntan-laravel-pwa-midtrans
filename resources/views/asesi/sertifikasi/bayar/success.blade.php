<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    <div class="container">
        <h1>Payment Successful!</h1>
        <p>Your payment was processed successfully. Thank you for your purchase.</p>
        <a href="{{ route('home') }}" class="btn btn-primary">Go to Dashboard</a>
    </div>
</x-app-layout>