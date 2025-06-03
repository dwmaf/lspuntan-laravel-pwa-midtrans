<div class="flex space-x-4">
    {{-- Praasesmen --}}
    <a href="/rincian_praasesmen/{{ $sertification[0]->id }}/edit"
        class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase hover:text-slate-700 dark:hover:text-gray-100 rounded-t-md 
                        dark:text-gray-200 text-slate-600 {{ Request::is('rincian_praasesmen/*') ? 'border-b-2 border-slate-800' : '' }}">
        Praasesmen
    </a>
    {{-- Asesmen --}}
    <a href="/rincian_asesmen/{{ $sertification[0]->id }}/edit"
        class="flex items-center gap-2 px-4 py-2  font-semibold text-xs uppercase  
hover:text-slate-700 dark:hover:text-gray-100 dark:text-gray-200 text-gray-700 {{ Request::is('rincian_asesmen/*') ? 'border-b-2 border-slate-800' : '' }}">
        Asesmen
    </a>
    <a href="/list_asesi/{{ $sertification[0]->id }}"
        class="flex items-center gap-2 px-4 py-2  font-semibold text-xs uppercase  
hover:text-slate-700 dark:hover:text-gray-100 dark:text-gray-200 text-gray-700 {{ Request::is('list_asesi/*') ? 'border-b-2 border-slate-800' : '' }}">
        Peserta
    </a>
</div>
