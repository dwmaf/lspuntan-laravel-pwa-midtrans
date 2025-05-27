<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    @php
        $asesi = $student->asesi()->where('sertification_id', $sertification->id)->first();
    @endphp
    <div class="flex space-x-4">
        <a href='/rincian_praasesmen_asesi/{{ $sertification->id }}'
            class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase hover:text-slate-700 dark:hover:text-gray-100 rounded-t-md 
                        dark:text-gray-200 text-slate-600 {{ Request::is('apply_sertifikasi') ? 'border-b-2 border-slate-800' : '' }}">
            Praasesmen
        </a>
        <a href="/asesmen"
            class="flex items-center gap-2 px-4 py-2  font-semibold text-xs uppercase  
hover:text-slate-700 dark:hover:text-gray-100 dark:text-gray-200 text-gray-700 {{ Request::is('apply_sertifikasi') ? 'border-b-2 border-slate-800' : '' }}">
            Asesmen
        </a>
    </div>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <h4 class="inline-block  text-gray-800 dark:text-white px-4 py-2 rounded-lg transition">
            Praasesmen {{ $sertification->skema->nama_skema }}</h4>
        <form action="/rincian_praasesmen/{{ $sertification->id }}/update" class="mt-4 flex flex-col gap-2"
            method="POST">
            @csrf
            @method('PATCH')
            <label for="rincian_praasesmen">Rincian pra-asesmen</label>
            <trix-editor input="rincian_praasesmen"></trix-editor>
            <input id="rincian_praasesmen" type="hidden" name="rincian_praasesmen">

            <button type="submit"
                class="bg-blue-700 text-gray-800 dark:text-white px-4 py-2 rounded-lg hover:bg-blue-500 dark:hover:bg-blue-500 dark:bg-blue-800 transition self-end">Buat</button>
        </form>
    </div>
</x-admin-layout>
