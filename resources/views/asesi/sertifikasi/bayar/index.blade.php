<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    <div>
        <h3>Bayar dengan fitur in-app-payment yang disediakan oleh Aplikasi ini yang terintegrasi dengan Midtrans Payment</h3>
        <form action="/checkout" method="POST" class="mt-6 space-y-6">
            @csrf
            <input type="text" name="asesi_id" hidden required value="{{ $asesi_id }}">
            <input type="text" name="name" hidden required value="{{ $name }}">
            <input type="text" name="email" hidden required value="{{ $email }}">
            <input type="text" name="no_tlp_hp" hidden required value="{{ $no_tlp_hp }}">
            <input type="number" name="biaya" hidden required value="{{ $biaya }}">
            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300" for="name">Nama Lengkap</label>
            <x-text-input class="mt-1 block w-full" type="text" name="name" id="name" disabled required value="{{ $name }}"/>
            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300" for="email">Email</label>
            <x-text-input class="mt-1 block w-full" type="email" name="email" id="email" disabled required value="{{ $email }}"/>
            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300" for="no_tlp_hp">No. HP</label>
            <x-text-input class="mt-1 block w-full" type="text" name="no_tlp_hp" id="no_tlp_hp" disabled required value="{{ $no_tlp_hp }}"/>
            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300" for="biaya">Biaya</label>
            <x-text-input class="mt-1 block w-full" type="number" name="biaya" id="biaya" disabled required value="{{ $biaya }}"/>
            <button type="submit">Pay Now</button>
        </form>
        <h1>ATAU</h1>
        <h3>Silahkan bayar ke virtual account yang sudah disiapkan berikut dan submit bukti pembayarannya di form berikut</h3>
        <form action="/bukti-pembayaran" method="POST" class="mt-6 space-y-6" enctype="multipart/form-data">
            @csrf
            <input type="text" name="asesi_id" hidden required value="{{ $asesi_id }}">
            <input type="text" name="name" hidden required value="{{ $name }}">
            <input type="text" name="email" hidden required value="{{ $email }}">
            <input type="text" name="no_tlp_hp" hidden required value="{{ $no_tlp_hp }}">
            <input type="number" name="biaya" hidden required value="{{ $biaya }}">
            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Bukti pembayaran</label>
            <x-text-input class="mt-1 block w-full" type="file" name="bukti_pembayaran" required/>
            <button type="submit">Pay Now</button>
        </form>
    </div>
</x-app-layout>
