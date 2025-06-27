<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 dark:bg-green-700 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-100 rounded-md" role="alert">
                {{ session('success') }}
        </div>
    @endif
    <div class="max-w-7xl mx-auto mb-4 mt-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            @foreach ($sertifications as $sert)
                <div class="bg-white p-6 rounded-lg dark:bg-gray-800">

                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">{{ $sert->skema->nama_skema }}
                    </h3>
                    <div class="flex items-center mt-4">
                        <x-bxs-calendar class="w-4 text-gray-700 dark:text-gray-200" />
                        <p class="ml-2 text-gray-600 text-sm dark:text-gray-200">
                            Pendaftaran:
                            {{ \Carbon\Carbon::parse($sert->tgl_apply_dibuka)->format('d M Y') }}
                            &ndash;
                            {{ \Carbon\Carbon::parse($sert->tgl_apply_ditutup)->format('d M Y') }}
                        </p>
                    </div>
                    <div class="flex items-center mt-4">
                        <x-tni-money class="w-4 text-gray-700 dark:text-gray-200" />
                        <p class="ml-2 text-gray-600 text-sm dark:text-gray-200">
                            Biaya:
                            {{ $sert->harga}}
                        </p>
                    </div>
                    <div class="mt-4">
                        <a href="sertification/{{ $sert->id }}"
                            class=" self-start font-medium bg-blue-500 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-700 cursor-pointer">Lihat</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">

        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Mulai Sertifikasi</h2>
        <form action="/sertification" class="mt-4 flex flex-col gap-2" method="POST">
            @csrf
            <div id="asesor dan skema">
                <label for="skema_asesor" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Skema
                    dan Asesor:</label>
                <select required name="asesor_skema"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <option value="" disabled selected>--Silahkan pilih asesor dan skema--</option>
                    @foreach ($asesors as $asesor)
                        @foreach ($asesor->skemas as $skema)
                            <option class="" value="{{ $asesor->id . ',' . $skema->id }}">
                                {{ $asesor->name }} - {{ $skema->nama_skema }}
                            </option>
                        @endforeach
                    @endforeach
                </select>
                @error('asesor_skema')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div id="tanggal_apply_dibuka">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Tanggal Daftar
                    Dibuka
                </label>
                <x-text-input id="tgl_apply_dibuka" name="tgl_apply_dibuka" type="date" class="mt-1 block w-full" :value="old('tgl_apply_dibuka')"
                    required />
                @error('tgl_apply_dibuka')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div id="tanggal_apply_ditutup">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Tanggal Daftar
                    Ditutup
                </label>
                <x-text-input id="tgl_apply_ditutup" name="tgl_apply_ditutup" type="date" class="mt-1 block w-full" :value="old('tgl_apply_ditutup')"
                    required />
                @error('tgl_apply_ditutup')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div id="tanggal_bayar_ditutup">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Tanggal Bayar
                    Ditutup
                </label>
                <x-text-input id="tgl_bayar_ditutup" name="tgl_bayar_ditutup" type="datetime-local" class="mt-1 block w-full" :value="old('tgl_bayar_ditutup')"
                    required />
                @error('tgl_bayar_ditutup')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div id="biaya_sertifikasi">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Biaya
                    Sertifikasi
                </label>
                <x-text-input id="harga" name="harga" type="number" min="0" class="mt-1 block w-full" :value="old('harga')"
                    required />
                @error('harga')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit"
                class="self-start font-medium bg-blue-500 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-700 cursor-pointer">Mulai</button>
        </form>
    </div>
</x-admin-layout>
