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
        <table class="table-auto border-separate border-spacing-0 border-2 border-gray-700 dark:border-white rounded-lg">
            <thead class="text-gray-800 dark:text-gray-200">
                <tr>
                    <th class=" px-3 py-2 text-left text-md font-semibold border-b-2 border-gray-700 dark:border-white">No</th>
                    <th class=" px-3 py-2 text-left text-md font-semibold border-b-2 border-gray-700 dark:border-white">Nama Asesi</th>
                    <th class=" px-3 py-2 text-left text-md font-semibold border-b-2 border-gray-700 dark:border-white">Status</th>
                    <th class=" px-3 py-2 text-left text-md font-semibold border-b-2 border-gray-700 dark:border-white">Action</th>
                </tr>
            </thead>
            <tbody class="text-gray-800 dark:text-gray-200">
                @foreach ($asesis as $asesi)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $asesi->id }}</td>
                        <td>{{ $asesi->status }}</td>
                        <td>
                            <a href="/rincian_data_asesi/{{ $asesi->id }}">Lihat data</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-admin-layout>
