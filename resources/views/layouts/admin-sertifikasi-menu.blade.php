<div class="flex flex-wrap space-x-4 mt-1">
    {{-- Detail --}}
    <div>
        <a href="/sertification/{{ $sertification->id }}"
            class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase  hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md
    dark:text-white text-gray-600 ">
            Detail
        </a>
        @if (Request::is('sertification/*'))
            <div style="margin-top:-4px" class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
        @endif
    </div>
    {{-- Praasesmen --}}
    <div>
        <a href="/rincian_praasesmen/{{ $sertification->id }}/edit"
            class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase  hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md
dark:text-white text-gray-600 ">
            Praasesmen
        </a>
        @if (Request::is('rincian_praasesmen/*'))
            <div style="margin-top:-4px" class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
        @endif
    </div>
    {{-- Asesmen --}}
    <div>
        <a href="/rincian_asesmen/{{ $sertification->id }}/edit"
            class="flex items-center gap-2 px-4 py-3  font-semibold text-xs uppercase hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md 
            dark:text-white text-gray-600 ">
            Asesmen
        </a>
        @if (Request::is('rincian_asesmen/*'))
            <div style="margin-top:-4px" class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
        @endif
    </div>
    <div>
        <a href="/list_asesi/{{ $sertification->id }}"
            class="flex items-center gap-2 px-4 py-3  font-semibold text-xs uppercase hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md
            dark:text-white text-gray-600 ">
            Peserta
        </a>
        @if (Request::is(['list_asesi/*','rincian_data_asesi/*']))
            <div style="margin-top:-4px" class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
        @endif
    </div>
</div>
