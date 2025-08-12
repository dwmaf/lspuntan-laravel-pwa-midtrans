<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Rincian Sertifikasi') }}
        </h2>
    </x-slot>
    @include('layouts.admin-sertifikasi-menu')
    <div x-data="{ showForm: false, showConfirmModal: false, deleteUrl: '' }">
        @if ($sertification->status == 'berlangsung')    
        <div class="p-6 mb-2 bg-white dark:bg-gray-800 rounded-tr-lg rounded-bl-lg rounded-br-lg shadow-md">
            <div class="flex justify-between items-center">
                <p class="text-gray-500 dark:text-gray-400 text-xs">Silahkan buat pengumuman untuk para asesi.</p>
                <button x-show="!showForm"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-500 hover:bg-blue-600 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-700 transition ease-in-out duration-150 cursor-pointer"
                    @click="showForm = !showForm">
                    <x-bxs-plus-square class="w-4 h-4 mr-2" />
                    <span>Tambah Pengumuman</span>
                </button>
                <button x-show="showForm" type="button" @click="showForm = !showForm"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 cursor-pointer">
                    Batal
                </button>
            </div>

            {{-- Form tambah pengumuman (di-hide/ditampilkan dengan Alpine) --}}
            <div x-show="showForm" x-transition>
                @include('admin.sertifikasi.pengumuman.createpengumuman')
            </div>


        </div>
        @endif
        {{-- Daftar pengumuman --}}
        @if ($pengumumans->isEmpty())
            <div class="text-center text-gray-500 dark:text-gray-300 py-8">
                Belum ada pengumuman.
            </div>
        @else
            @foreach ($pengumumans as $pengumuman)
                <div class="py-3 px-5 bg-white dark:bg-gray-800 rounded-lg shadow-md mb-2">
                    <div class="flex justify-between items-center mb-2">
                        <div class="text-xs text-gray-400">
                            @if ($pengumuman->created_at->isToday())
                                {{ $pengumuman->created_at->format('H:i') }}
                            @else
                                {{ $pengumuman->created_at->format('d M Y') }}
                            @endif
                        </div>
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('admin.sertification.assessment-announcement.edit', [$sertification->id, $pengumuman->id]) }}"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-yellow-500 hover:bg-yellow-600 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-700 transition ease-in-out duration-150 cursor-pointer">
                                <x-bxs-edit class="w-4 h-4 mr-2" />
                                Edit
                            </a>
                            <button type="button"
                                @click="showConfirmModal = true; deleteUrl = '{{ route('admin.sertification.assessment-announcement.destroy', [$sertification->id, $pengumuman->id]) }}'"
                                class="cursor-pointer inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-red-700 dark:hover:bg-red-800 dark:focus:ring-red-800 transition ease-in-out duration-150">
                                <x-bxs-trash class="w-4 h-4 mr-2" />
                                Hapus
                            </button>
                        </div>
                    </div>
                    <h6 class="font-medium text-sm text-gray-800 dark:text-gray-100">{!! $pengumuman?->rincian_pengumuman_asesmen !!}</h6>
                </div>
            @endforeach
        @endif
        <!-- Modal Konfirmasi Hapus -->
        <div x-show="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 "
            style="display: none;">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-4 w-[280px]">
                <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-6">Konfirmasi Hapus</h3>

                <div class="mt-4 flex justify-end space-x-2">
                    <button type="button" @click="showConfirmModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 cursor-pointer">
                        Batal
                    </button>
                    <form :action="deleteUrl" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md cursor-pointer">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
