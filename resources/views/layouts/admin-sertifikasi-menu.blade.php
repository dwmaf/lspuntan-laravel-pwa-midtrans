<div class="flex flex-wrap space-x-4 mt-1">
    {{-- Detail --}}
    <div>
        <a href="/admin/sertification/{{ $sertification->id }}"
            class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase  hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md
    dark:text-white text-gray-600 ">
            Detail
        </a>
        @if (Request::is('admin/sertification/*'))
            <div style="margin-top:-4px" class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
        @endif
    </div>
    {{-- Praasesmen --}}
    <div>
        <a href="{{ route('admin.sertification.pre-assessment.edit', $sertification->id) }}/edit}}"
            class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase  hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md
dark:text-white text-gray-600 ">
            Praasesmen
        </a>
        @if (Route::is('admin.sertification.pre-assessment.edit'))
            <div style="margin-top:-4px" class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
        @endif
    </div>
    {{-- Asesmen --}}
    <div>
        <a href="{{ route('admin.sertification.assessment.edit', $sertification->id) }}/edit}}"
            class="flex items-center gap-2 px-4 py-3  font-semibold text-xs uppercase hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md 
            dark:text-white text-gray-600 ">
            Asesmen
        </a>
        @if (Route::is('admin.sertification.assessment.edit'))
            <div style="margin-top:-4px" class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
        @endif
    </div>
    {{-- Daftar asesi yg mendaftar --}}
    <div>
        <a href="{{ route('admin.sertification.applicants.index', $sertification->id) }}}}"
            class="flex items-center gap-2 px-4 py-3  font-semibold text-xs uppercase hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md
            dark:text-white text-gray-600 ">
            Peserta
        </a>
        @if (Route::is(['admin.sertification.applicants.index','admin.applicants.show']))
            <div style="margin-top:-4px" class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
        @endif
    </div>
</div>
