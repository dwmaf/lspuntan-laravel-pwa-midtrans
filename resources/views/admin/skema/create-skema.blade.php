<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    <livewire:admin.skemalivewire />
    {{-- @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 dark:bg-green-700 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-100 rounded-md" role="alert">
                {{ session('success') }}
        </div>
    @endif
    <div class="mt-4 p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-6">Skema Sertifikasi yang Tersedia</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            No</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Skema</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Format File APL</th>
                        <th scope="col"
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($skemas as $skema)
                        <tr>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $loop->iteration }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">
                                {{ $skema->nama_skema }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">
                                <div class="flex flex-col">
                                    <a href="{{ asset('storage/' . $skema->format_apl_1) }}" target="_blank"
                                        class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">Lihat</a>
                                    <a href="{{ asset('storage/' . $skema->format_apl_2) }}" target="_blank"
                                        class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">Lihat</a>
                                </div>
                            </td>
                            <td class=" text-gray-700 dark:text-gray-200 px-4 py-2 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('admin.skema.edit',$skema->id) }}" wire:navigate
                                        class="cursor-pointer px-3 py-1 text-sm font-medium text-white bg-yellow-500 hover:bg-yellow-600 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-700">
                                        Edit
                                    </a>
                                    <form class="inline-block" action="{{ route('admin.skema.destroy', $skema->id) }}"
                                        method="post"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus skema ini?');">
                                        @method('delete')
                                        @csrf
                                        <button type="submit"
                                            class="cursor-pointer px-3 py-1 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-red-700 dark:hover:bg-red-800 dark:focus:ring-red-800">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                Tidak ada skema sertifikasi yang tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Tambah Skema Sertifikasi</h2>
        <form action="{{ route('admin.skema.store') }}" class="mt-4 flex flex-col gap-2" method="POST"
            enctype="multipart/form-data">
            @csrf
            <div id="nama skema">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Nama
                    Skema</label>
                <x-text-input name="nama_skema" type="text" class="mt-1 block w-full" :value="old('nama_skema')" required />
            </div>
            <div id="format apl 1">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Format File
                    APL.01 (docx)
                </label>
                <input type="file" name="format_apl_1" required
                    class="w-full px-3 py-2 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-hidden focus:ring-1 focus:border-blue-700 focus:ring-blue-700">
            </div>
            <div id="format apl 2">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Format File
                    APL.02 (docx)
                </label>
                <input type="file" name="format_apl_2" required
                    class="w-full px-3 py-2 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-hidden focus:ring-1 focus:border-blue-700 focus:ring-blue-700">
            </div>
            <button type="submit"
                class="self-start font-medium bg-blue-500 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-700 cursor-pointer">Tambah</button>
        </form>
    </div> --}}
</x-admin-layout>
