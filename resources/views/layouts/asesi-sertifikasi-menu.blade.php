@php
    $student = auth()->user()->student;
    $asesi = $student->asesi()->where('sertification_id', $sertification->id)->first();
    $latestTransaction = $asesi->transaction->sortByDesc('created_at')->first();
@endphp
<div class="flex flex-wrap space-x-4 mt-1">
    {{-- Praasesmen --}}
    {{-- @if ($asesi && $asesi->status == 'dilanjutkan asesmen')
    <div>
        <a href='{{ route('asesi.applied.pre-assessment.show', $sertification->id) }}'
            class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md 
                            dark:text-white text-gray-600">
            Praasesmen
        </a>
        @if (Route::is('asesi.applied.pre-assessment.show'))
            <div style="margin-top:-4px" class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
        @endif
    </div>
    @else
        <div
            class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase rounded-t-md 
                            dark:text-gray-200 text-slate-400 ">
            <x-tni-lock class="w-4 " />
            Praasesmen
        </div>
    @endif --}}
    {{-- Detail --}}
    @if ($asesi)
        <div>
            <a href='{{ route('asesi.applied.show', [$sertification->id, $asesi->id]) }}'
                class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md
    dark:text-white text-gray-600 ">
                Detail
            </a>
            @if (Route::is('asesi.applied.show'))
                <div style="margin-top:-4px" class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
            @endif
        </div>
    @else
        <div
            class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase rounded-t-md 
            dark:text-gray-200 text-slate-400 ">
            <x-tni-lock class="w-4 " />
            Detail
        </div>
    @endif
    {{-- Bayar --}}

    @if ($asesi && $asesi->status == 'dilanjutkan_asesmen')
        <div>
            <a href='{{ route('asesi.applied.payment.create', [$sertification->id, $asesi->id]) }}'
                class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md
    dark:text-white text-gray-600 ">
                Bayar
            </a>
            @if (Route::is('asesi.applied.payment.create'))
                <div style="margin-top:-4px" class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
            @endif
        </div>
    @else
        <div
            class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase rounded-t-md 
            dark:text-gray-200 text-slate-400">
            <x-tni-lock class="w-4 " />
            Bayar
        </div>
    @endif
    {{-- Pengumuman --}}
    @if ($asesi && $asesi->status == 'dilanjutkan_asesmen')
        <div>
            <a href='{{ route('asesi.applied.assessment-announcement.index', [$sertification->id, $asesi->id]) }}'
                class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md
    dark:text-white text-gray-600 ">
                Pengumuman
            </a>
            @if (Route::is('asesi.applied.assessment-announcement.index'))
                <div style="margin-top:-4px" class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
            @endif
        </div>
    @else
        <div
            class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase rounded-t-md 
            dark:text-gray-200 text-slate-400 ">
            <x-tni-lock class="w-4 " />
            Pengumuman
        </div>
    @endif
    {{-- Asesmen --}}

    @if (
        $asesi &&
            $latestTransaction &&
            $latestTransaction->status == 'bukti_pembayaran_terverifikasi' &&
            $asesi->status == 'dilanjutkan_asesmen')
        <div>

            <a href="{{ route('asesi.applied.assessment.index', [$sertification->id, $asesi->id]) }}"
                class="flex items-center gap-2 px-4 py-3  font-semibold text-xs uppercase  
     hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md dark:text-white text-gray-600">
                Asesmen
            </a>
            @if (Route::is('asesi.applied.assessment.index'))
                <div style="margin-top:-4px" class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
            @endif
        </div>
    @else
        <div
            class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase rounded-t-md 
            dark:text-gray-200 text-slate-400">
            <x-tni-lock class="w-4 " />
            Asesmen
        </div>
    @endif

</div>
<hr class=" border-gray-200 dark:border-gray-700 mb-2">