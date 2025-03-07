<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <a href=""
            class="inline-block bg-blue-600 text-gray-800 dark:text-white px-4 py-2 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-500 transition">Data
            Asesor</a>


        <form action="/asesor" class="mt-4 flex flex-col gap-2" method="POST">
            @csrf
            <div>
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Nama
                    Asesor</label>
                <input type="text" name="nama_asesor" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring focus:ring-blue-300">
            </div>
            <div>
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Email</label>
                <input type="email" name="email" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring focus:ring-blue-300">
            </div>
            <input type="text" name="role" value="asesor" hidden>
            <div x-data="{
                selectedSkemas: [],
                open: false,
                toggleDropdown() {
                    this.open = !this.open;
                },
                toggleSkema(skema) {
                    if (this.selectedSkemas.includes(skema)) {
                        this.selectedSkemas = this.selectedSkemas.filter(item => item !== skema);
                    } else {
                        this.selectedSkemas.push(skema);
                    }
                }
            }" class="relative mt-2">
                <button type="button" @click="toggleDropdown" class="bg-gray-200 p-2 rounded-md w-full text-left">
                    Pilih Skema
                </button>

                <div class="absolute left-0 mt-2 w-full bg-white shadow-md rounded-md z-20" x-show="open" x-cloak>
                    <div class="p-2 max-h-60 overflow-y-auto">
                        @foreach ($skemas as $skema)
                            <div class="flex items-center">
                                <input type="checkbox" id="skema_{{ $skema->id }}" :value="{{ $skema->id }}"
                                    x-on:click="selectedSkemas" name="selectedSkemas[]" class="mr-2">
                                <label for="skema_{{ $skema->id }}">{{ $skema->nama_skema }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <button type="submit"
                class=" bg-blue-200 text-gray-800 dark:text-white px-4 py-2 rounded-lg hover:bg-green-700 dark:hover:bg-green-500 dark:bg-green-800 transition self-end">Tambah
                baru</button>
        </form>

        <h2 class="mt-4 text-lg font-semibold text-gray-700 dark:text-gray-200">Asesor yang Tersedia</h2>
        <table class="mt-6 w-full border-collapse border border-gray-300 dark:border-gray-600">
            <thead>
                <tr class="bg-gray-200 dark:bg-gray-700">
                    <th
                        class="border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 text-left">
                        No</th>
                    <th
                        class="border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 text-left">
                        Nama</th>
                    <th
                        class="border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 text-left">
                        Skema</th>
                    <th
                        class="border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 text-left">
                        Aksi</th>
                </tr>
            </thead>
            {{-- <tbody>
                @foreach ($asesors as $asesor)
                    <tr>
                        <td
                            class="border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2">
                            {{ $loop->iteration }}</td>
                        <td
                            class="border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2">
                            {{ $asesor->nama }}</td>
                        <td
                            class="border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2">
                            {{ $asesor->nama_skema }}</td>
                        <td
                            class="border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 text-center">
                            <button x-data
                                x-on:click="$dispatch('open-modal', 'editskema'); $dispatch('edit-skema', { id: {{ $skema->id }}, nama_skema: '{{ $skema->nama_skema }}' })"
                                class="text-blue-500 hover:text-blue-700">
                                Edit
                            </button>
                            <button
                                class="bg-red-600 text-white px-3 py-1 rounded-lg hover:bg-red-700 dark:hover:bg-500 transition text-sm">Hapus</button>
                        </td>
                    </tr>
                @endforeach
            </tbody> --}}
        </table>
    </div>




</x-admin-layout>
