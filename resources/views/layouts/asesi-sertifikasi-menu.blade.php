@php
    $student = auth()->user()->student;
    $asesi = $student->asesi()->where('sertification_id', $sertification->id)->first();
@endphp
<div class="flex flex-wrap space-x-4 mt-1">
    {{-- Praasesmen --}}
    <!-- @if ($asesi && $asesi->status == 'dilanjutkan asesmen')
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
    @endif -->
    {{-- Detail --}}
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
    {{-- Bayar --}}
    
    @if ($asesi && $asesi->status == 'dilanjutkan asesmen')
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
            dark:text-gray-200 text-slate-400 ">
            <x-tni-lock class="w-4 " />
            Bayar
        </div>
    @endif
    {{-- Asesmen --}}
    
    @if ($asesi && $asesi->transaction[0]->status == 'paid' && $asesi->status == 'dilanjutkan asesmen')
    <div>

        <a href="{{ route('asesi.applied.assessment.show', $sertification->id) }}"
            class="flex items-center gap-2 px-4 py-3  font-semibold text-xs uppercase  
     hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md dark:text-white text-gray-600">
            Asesmen
        </a>
        @if (Route::is('asesi.applied.assessment.show'))
            <div style="margin-top:-4px" class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
        @endif
    </div>
    @else
        <div
            class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase rounded-t-md 
            dark:text-gray-200 text-slate-400 ">
            <x-tni-lock class="w-4 " />
            Asesmen
        </div>
    @endif

</div>