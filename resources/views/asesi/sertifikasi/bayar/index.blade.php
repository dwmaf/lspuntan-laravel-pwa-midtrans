<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    <div>
        <form action="{{ route('payment.process', $sertification->id) }}" method="POST">
            @csrf
            <label for="name">Nama Lengkap</label>
            <input type="text" name="name" id="name" disabled required value="{{ $user->name }}">

            <label for="email">Email</label>
            <input type="email" name="email" id="email" disabled required value="{{ $user->email }}">

            <button type="submit">Pay Now</button>
        </form>


    </div>
</x-app-layout>
