<x-admin-layout>
    
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    @if (session('success'))
        <div class="my-2 p-4 bg-green-100 dark:bg-green-700 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-100 rounded-md"
            role="alert">
            {{ session('success') }}
        </div>
    @endif
    @include('layouts.admin-sertifikasi-menu')
    <div class="p-6 bg-white dark:bg-gray-800 rounded-tr-lg rounded-bl-lg rounded-br-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 sm:mb-0">
            Deskripsi Pembayaran {{ $sertification->skema->nama_skema }}
        </h3>
        <form action="{{ route('admin.sertification.payment-desc.update', $sertification->id) }}" class="mt-4 flex flex-col gap-2" method="POST">
            @csrf
            @method('PATCH')
            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1" for="rincian_pembayaran">Rincian
                Pembayaran</label>
            <input id="rincian_pembayaran" type="hidden" name="rincian_pembayaran"
                value="{{ old('rincian_pembayaran', $sertification?->rincian_pembayaran) }}">
            <div class="trix-container">
                <trix-editor input="rincian_pembayaran">
                    {!! old('rincian_pembayaran', $sertification?->rincian_pembayaran) !!}
                </trix-editor>
            </div>
            @error('rincian_pembayaran')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
            <button type="submit"
                class="self-start font-medium bg-blue-500 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-700 cursor-pointer">
                Simpan Perubahan
            </button>
        </form>
    </div>
    
</x-admin-layout>