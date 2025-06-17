<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>

    <div class="mt-4 p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Edit {{ $skema->nama_skema }}</h2>
        <form action="{{ route('manage_skema.update',$skema->id) }}" class="mt-4 flex flex-col gap-2" method="POST" enctype="multipart/form-data">
            @method('patch')
            @csrf
            <div id="nama skema">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Nama
                    Skema</label>
                <x-text-input name="nama_skema" type="text" class="mt-1 block w-full" :value="old('nama_skema', $skema?->nama_skema)" required />
            </div>
            <div id="format apl 1">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Format File APL.01 (docx)
                </label>
                <!-- Feedback jika file sudah ada -->
                @if ($skema->format_apl_1)
                    <p class="text-sm text-gray-500 mt-1">File sudah ada:
                        <a href="{{ asset('storage/' . $skema->format_apl_1) }}" class="text-blue-500"
                            target="_blank">Lihat File</a>
                    </p>
                @endif
                <!-- Input file -->
                <input id="format_apl_1" name="format_apl_1" type="file"
                    class="w-full px-3 py-2 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-hidden focus:ring-2 focus:border-blue-800 focus:ring-blue-800"
                    @if (!$skema->format_apl_1) required @endif>
            </div>
            <div id="format apl 2">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Format File APL.02 (docx)
                </label>
                <!-- Feedback jika file sudah ada -->
                @if ($skema->format_apl_2)
                    <p class="text-sm text-gray-500 mt-1">File sudah ada:
                        <a href="{{ asset('storage/' . $skema->format_apl_2) }}" class="text-blue-500"
                            target="_blank">Lihat File</a>
                    </p>
                @endif
                <!-- Input file -->
                <input id="format_apl_2" name="format_apl_2" type="file"
                    class="w-full px-3 py-2 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-hidden focus:ring-2 focus:border-blue-800 focus:ring-blue-800"
                    @if (!$skema->format_apl_2) required @endif>
            </div>
            <button type="submit"
                class="self-start font-medium bg-blue-500 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-700 cursor-pointer">Update</button>
        </form>
    </div>
</x-admin-layout>
