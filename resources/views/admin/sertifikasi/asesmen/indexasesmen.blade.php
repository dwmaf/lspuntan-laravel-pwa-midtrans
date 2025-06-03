<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    @include('layouts.admin-sertifikasi-menu')
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <h4 class="inline-block  text-gray-800 dark:text-white px-4 py-2 rounded-lg transition">
            Praasesmen {{ $sertification->skema->nama_skema }}</h4>
        <form action="/rincian_asesmen/{{ $sertification->id }}/update" class="mt-4 flex flex-col gap-2" method="POST"
            x-data="{ error: '', files: null }">
            @csrf
            @method('PATCH')

            <label for="rincian_asesmen">Rincian pra-asesmen</label>
            <trix-editor input="rincian_asesmen"></trix-editor>
            <input id="rincian_asesmen" type="hidden" name="rincian_asesmen">

            <label class="mt-4">Lampiran (maks 5 file)</label>
            <input type="file" name="asesmen_attachment_file[]" multiple
                accept=".jpg,.jpeg,.png,.pdf,.docx,.pptx,.xlx"
                class="block w-full border border-gray-300 p-2 rounded-md"
                @change="
            files = $event.target.files;
            if (files.length > 5) {
                error = 'Maksimal 5 file yang boleh dipilih.';
                $event.target.value = '';
                files = null;
            } else {
                error = '';
            }" />
            <template x-if="error">
                <p class="text-red-600 mt-2" x-text="error"></p>
            </template>

            <button type="submit"
                class="bg-blue-700 text-gray-800 dark:text-white px-4 py-2 rounded-lg hover:bg-blue-500 dark:hover:bg-blue-500 dark:bg-blue-800 transition self-end mt-4">
                Buat
            </button>
        </form>
    </div>
    
</x-admin-layout>
