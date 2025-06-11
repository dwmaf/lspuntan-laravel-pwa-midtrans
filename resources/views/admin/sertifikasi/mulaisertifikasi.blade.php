<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    
    <div class="max-w-7xl mx-auto mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($sertifications as $sert)    
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-gray-900">{{ $sert->skema->nama_skema }}</h3>
                <p class="text-gray-600 mt-2">Tanggal Pendaftaran Dibuka : {{ $sert->tgl_apply_dibuka }}</p>
                <a href="sertification/{{ $sert->id }}">Lihat</a>
            </div>
            @endforeach
        </div>
    </div>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
        
        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Mulai Sertifikasi</h2>
        <form action="/sertification" class="mt-4 flex flex-col gap-2" method="POST">
            @csrf
            <div id="asesor dan skema">
                <label for="skema_asesor" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Skema dan Asesor:</label>
                <select name="asesor_skema[]" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" multiple>
                    @foreach ($asesors as $asesor)
                        @foreach ($asesor->skemas as $skema)
                            <option value="{{ $asesor->id . ',' . $skema->id }}">
                                {{ $asesor->name }} - {{ $skema->nama_skema }}
                            </option>
                        @endforeach
                    @endforeach
                </select>
            </div>
            <div id="tanggal_apply_dibuka">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Tanggal Daftar
                    Dibuka
                </label>
                <input type="date" name="tgl_apply_dibuka" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
            </div>
            <div id="tanggal_apply_ditutup">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Tanggal Daftar
                    Ditutup
                </label>
                <input type="date" name="tgl_apply_ditutup" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
            </div>
            <div id="tanggal_bayar_ditutup">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Tanggal Bayar
                    Ditutup
                </label>
                <input type="datetime-local" name="tgl_bayar_ditutup" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
            </div>
            <div id="biaya_sertifikasi">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Biaya Sertifikasi
                    
                </label>
                <input type="number" name="harga" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
            </div>
            <button type="submit"
                class="self-start bg-blue-700 text-gray-800 dark:text-white px-4 py-2 rounded-lg hover:bg-blue-500 dark:hover:bg-blue-500 dark:bg-blue-800">Mulai</button>
        </form>
    </div>
</x-admin-layout>