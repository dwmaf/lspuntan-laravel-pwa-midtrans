<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Asesor') }}
        </h2>
    </x-slot>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <form action="/asesor" class="mt-4 flex flex-col gap-2" method="POST">
            @csrf
            <div>
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Nama
                    Asesor</label>
                <input type="text" name="name" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
            </div>
            <div>
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Email</label>
                <input type="email" name="email" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
            </div>
            
            <div>
                <label for=""
                    class="block text-sm font-medium text-gray-600 dark:text-gray-300">Password</label>
                <input type="password" name="password" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-hidden focus:ring-3 focus:ring-blue-300">
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
            <tbody>
                @foreach ($asesors as $asesor)
                    <tr>
                        <td
                            class="border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2">
                            {{ $loop->iteration }}</td>
                        <td
                            class="border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2">
                            {{ $asesor->name }}</td>
                        <td
                            class="border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2">
                            @foreach ($asesor->skemas as $skema)
                                <h4 class="rounded-lg bg-blue-100 p-2 mb-2">{{ $skema->nama_skema }}</h4>
                            @endforeach
                        </td>
                        <td
                            class="border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 text-center">
                            <button x-data
                                x-on:click="$dispatch('open-modal', 'editskema'); $dispatch('edit-skema', { id: {{ $skema->id }}, nama_skema: '{{ $skema->nama_skema }}' })"
                                class="text-blue-500 hover:text-blue-700">
                                Edit
                            </button>
                            <form x-data="{ open: false }" class="d-inline" action=""
                                method="post" @submit="if (!open) { event.preventDefault(); open = true }">
                                @method('delete')
                                @csrf
                                <button type="button" class="btn btn-danger border-0" @click="open = true">
                                    Hapus</i>
                                </button>

                                <div x-show="open" x-cloak
                                    class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 z-50">
                                    <div class="bg-white p-6 rounded-sm shadow-lg">
                                        <p>Hapus data {{ $asesor->user->name }}?</p>
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




</x-admin-layout>
