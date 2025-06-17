<div class="flex space-x-4 mt-1">
    {{-- Detail --}}
    <a href="/sertification/{{ $sertification->id }}"
        class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase  hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md
dark:text-white text-gray-600 {{ Request::is('sertification/*') ? 'bg-white dark:bg-gray-800 rounded-t-md' : '' }}">
        Detail
    </a>
    {{-- Praasesmen --}}
    <a href="/rincian_praasesmen/{{ $sertification->id }}/edit"
        class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase  hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md
dark:text-white text-gray-600 {{ Request::is('rincian_praasesmen/*') ? 'bg-white dark:bg-gray-800 rounded-t-md' : '' }}">
        Praasesmen
    </a>
    {{-- Asesmen --}}
    <a href="/rincian_asesmen/{{ $sertification->id }}/edit"
        class="flex items-center gap-2 px-4 py-2  font-semibold text-xs uppercase hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md 
 dark:text-white text-gray-700 {{ Request::is('rincian_asesmen/*') ? 'bg-white dark:bg-gray-800 rounded-t-md' : '' }}">
        Asesmen
    </a>
    <a href="/list_asesi/{{ $sertification->id }}"
        class="flex items-center gap-2 px-4 py-2  font-semibold text-xs uppercase hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md
 dark:text-white text-gray-700 {{ Request::is('list_asesi/*') ? 'bg-white dark:bg-gray-800 rounded-t-md' : '' }}">
        Peserta
    </a>
</div>
