@php
    $student = auth()->user()->student;
    $asesi = $student->asesi()->where('sertification_id', $sertification->id)->first();
@endphp
<div class="flex space-x-4">
    {{-- Praasesmen --}}
    @if ($asesi->status == 'ikut_praasesmen' || $asesi->status == 'belum_bayar' || $asesi->status == 'sudah_bayar')
        <a href='/rincian_praasesmen_asesi/{{ $sertification->id }}'
            class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase hover:text-slate-700 dark:hover:text-gray-100 rounded-t-md 
                            dark:text-gray-200 text-slate-600 {{ Request::is('apply_sertifikasi') ? 'border-b-2 border-slate-800' : '' }}">
            Praasesmen
        </a>
    @else
        <div
            class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase rounded-t-md 
                            dark:text-gray-200 text-slate-400 ">
            <x-tni-lock class="w-4 " />
            Praasesmen
        </div>
    @endif

    {{-- Bayar --}}
    @if ($asesi->status == 'belum_bayar' || $asesi->status == 'sudah_bayar')
        <form action="/payment" method="POST" class="">
            @csrf
            <input type="text" hidden name="asesi_id" value="{{ $asesi->id }}">
            <input type="text" hidden name="biaya" value="{{ $sertification->harga }}">
            <input type="text" hidden name="name" value="{{ $user->name }}">
            <input type="text" hidden name="email" value="{{ $user->email }}">
            <input type="text" hidden name="no_tlp_hp" value="{{ $user->student->no_tlp_hp }}">

            <button type="submit"
                class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase tracking-widest hover:text-slate-700 dark:hover:text-gray-100 rounded-t-md
     dark:text-gray-200 text-gray-700 {{ Request::is('apply_sertifikasi/*') ? 'border-b-2 border-slate-800' : '' }}"
                {{ $asesi->status != 'daftar' ? 'disabled' : '' }}>

                Bayar
            </button>
        </form>
    @else
        <div
            class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase rounded-t-md 
            dark:text-gray-200 text-slate-400 ">
            <x-tni-lock class="w-4 " />
            Bayar
        </div>
    @endif
    {{-- Asesmen --}}
    @if ($asesi->status == 'sudah_bayar')
        <a href="/asesmen"
            class="flex items-center gap-2 px-4 py-2  font-semibold text-xs uppercase  
    hover:text-slate-700 dark:hover:text-gray-100 dark:text-gray-200 text-gray-700 {{ Request::is('apply_sertifikasi') ? 'border-b-2 border-slate-800' : '' }}">
            Asesmen
        </a>
    @else
        <div
            class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase rounded-t-md 
            dark:text-gray-200 text-slate-400 ">
            <x-tni-lock class="w-4 " />
            Asesmen
        </div>
    @endif

</div>