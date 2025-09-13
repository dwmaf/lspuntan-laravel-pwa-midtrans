@php
    $student = auth()->user()->student;
    $asesi = $student->asesi()->where('sertification_id', $sertification->id)->first();
    $latestTransaction = $asesi->transaction->sortByDesc('created_at')->first();
@endphp
<div class="flex flex-wrap space-x-4 mt-1">
    {{-- Detail --}}
    @if ($asesi)
        <div>
            <a href='{{ route('asesi.sertifikasi.applied.show', [$sertification->id, $asesi->id]) }}' wire:navigate
                class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md
    dark:text-white text-gray-600 ">
                Detail
            </a>
            @if (Route::is('asesi.sertifikasi.applied.show'))
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
            <a href='{{ route('asesi.payment.create', [$sertification->id, $asesi->id]) }}' wire:navigate
                class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md
    dark:text-white text-gray-600 ">
                Bayar
            </a>
            @if (Route::is('asesi.payment.create'))
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
            <a href='{{ route('asesi.pengumuman.index', [$sertification->id, $asesi->id]) }}' wire:navigate
                class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md
    dark:text-white text-gray-600 ">
                Pengumuman
            </a>
            @if (Route::is('asesi.pengumuman.index'))
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

            <a href="{{ route('asesi.assessmen.index', [$sertification->id, $asesi->id]) }}" wire:navigate
                class="flex items-center gap-2 px-4 py-3  font-semibold text-xs uppercase  
     hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md dark:text-white text-gray-600">
                Asesmen
            </a>
            @if (Route::is(['asesi.assessmen.index']))
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