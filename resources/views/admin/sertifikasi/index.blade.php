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
            </div>
            @endforeach
        </div>
    </div>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
        
        <h4
            class="inline-block bg-blue-600 text-gray-800 dark:text-white px-4 py-2 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-500 transition">
            Mulai
            Sertifikasi</h4>
        <form action="/sertification" class="mt-4 flex flex-col gap-2" method="POST">
            @csrf
            <div id="asesor dan skema">
                <label for="skema_asesor" class="block text-sm font-medium text-gray-700">Pilih Skema dan Asesor:</label>
                <select name="asesor_skema[]" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" multiple>
                    @foreach ($asesors as $asesor)
                        @foreach ($asesor->skemas as $skema)
                            <option value="{{ $asesor->id . ',' . $skema->id }}">
                                {{ $asesor->user->name }} - {{ $skema->nama_skema }}
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
            <button type="submit"
                class="bg-blue-700 text-gray-800 dark:text-white px-4 py-2 rounded-lg hover:bg-green-700 dark:hover:bg-green-500 dark:bg-green-800 transition self-end">Mulai</button>
        </form>

        <form action="/skema" class="mt-4 flex flex-col gap-2" method="POST">
            @csrf
            <div>
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Nama
                    Skema</label>
                <input type="text" name="nama_skema" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
            </div>
            <button type="submit"
                class="bg-blue-200 text-gray-800 dark:text-white px-4 py-2 rounded-lg hover:bg-green-700 dark:hover:bg-green-500 dark:bg-green-800 transition self-end">Tambah</button>
        </form>
        <h2 class="mt-4 text-lg font-semibold text-gray-700 dark:text-gray-200">Skema Sertifikasi yang Tersedia</h2>
        <table class="mt-6 w-full border-collapse border border-gray-300 dark:border-gray-600">
            <thead>
                <tr class="bg-gray-200 dark:bg-gray-700">
                    <th
                        class="border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 text-left">
                        No</th>
                    <th
                        class="border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 text-left">
                        Skema</th>
                    <th
                        class="border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 text-left">
                        Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($skemas as $skema)
                    <tr>
                        <td
                            class="border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2">
                            {{ $loop->iteration }}</td>
                        <td
                            class="border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2">
                            {{ $skema->nama_skema }}</td>
                        <td
                            class="border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 text-center">

                            <button x-data
                                x-on:click="$dispatch('open-modal', 'editskema'); $dispatch('edit-skema', { id: {{ $skema->id }}, nama_skema: '{{ $skema->nama_skema }}' })"
                                class="p-1 rounded-sm bg-yellow-300 text-gray-700">
                                Edit
                            </button>
                            <form x-data="{ open: false }" class="d-inline" action="" method="post"
                                @submit="if (!open) { event.preventDefault(); open = true }">
                                @method('delete')
                                @csrf
                                <button type="button" class="bg-red-600 p-1 rounded-sm text-white"
                                    @click="open = true">
                                    Hapus
                                </button>

                                <div x-show="open" x-cloak
                                    class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 z-50">
                                    <div class="bg-white p-6 rounded-sm shadow-lg">
                                        <p>Hapus data {{ $skema->nama_skema }}?</p>
                                        <div class="flex justify-end">
                                            <button type="button" class="btn btn-secondary mr-2"
                                                @click="open = false">Batal</button>
                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <x-modal name="editskema">
        <div x-data="{ id: null, nama_skema: '' }"
            x-on:edit-skema.window="id = $event.detail.id; nama_skema = $event.detail.nama_skema">
            <h2 class="text-lg font-bold">Edit Skema</h2>
            <form :action="'/skema/' + id" method="POST">
                @csrf
                @method('PUT')
                <label class="block mt-2">Nama Skema</label>
                <input type="text" name="nama_skema" x-model="nama_skema"
                    class="border p-2 w-full dark:bg-gray-700 dark:text-white">

                <div class="flex justify-end mt-4">
                    <button type="button" x-on:click="$dispatch('close-modal', 'editskema')"
                        class="bg-gray-500 text-white px-4 py-2 rounded-sm">Cancel</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-sm ml-2">Update</button>
                </div>
            </form>
        </div>
    </x-modal>



</x-admin-layout>
