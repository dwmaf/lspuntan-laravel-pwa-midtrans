<div x-data="{ showCreate: false }" x-on:pengumuman-created.window="showCreate = false"
    x-on:validation-error.window="$refs.createForm.scrollIntoView({ behavior: 'smooth', block: 'center' })">
    {{-- Notifikasi --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Rincian Sertifikasi') }}
        </h2>
    </x-slot>
    @include('layouts.admin-sertifikasi-menu')
    <div x-data="{ show: false, message: '' }"
        x-on:notify.window="message=$event.detail.message;show=true;setTimeout(()=>show=false,2500)" x-show="show"
        x-transition x-text="message" class="fixed top-20 right-4 text-xs px-3 py-2 rounded bg-green-600 text-white z-50"
        style="display:none">
    </div>
    @if ($formMode === 'create')
        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Buat Pengumuman</h2>
            <form wire:submit.prevent="save" class="mt-4 flex flex-col gap-4">
                @include('livewire.admin.partials.pengumuman-form-fields')
            </form>
        </div>
    @elseif($formMode === 'edit')
        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Edit Pengumuman</h2>
            <form wire:submit.prevent="save" class="mt-4 flex flex-col gap-4">
                @include('livewire.admin.partials.pengumuman-form-fields')
            </form>
        </div>
    @else
        <div class="p-6 mb-2 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="flex justify-between items-center">
                <p class="text-gray-500 dark:text-gray-400 text-xs">Buat pengumuman baru untuk para asesi.</p>
                <x-add-button wire:click="showCreateForm">
                    <span>
                        Tambah Pengumuman
                    </span>
                    <x-loading-spinner wire:target="showCreateForm" wire:loading></x-loading-spinner>
                </x-add-button>
            </div>
        </div>
        <div class="space-y-2">
            @forelse ($pengumumans as $pengumuman)
                <div class="py-3 px-5 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <div class="flex justify-between items-start mb-2">

                        <div class="flex items-center gap-3 min-w-0">
                            <div class="flex-shrink-0">
                                <svg class="h-10 w-10 text-gray-400 dark:text-gray-600 rounded-full bg-gray-200 dark:bg-gray-700 p-1"
                                    fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <h5 class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">
                                    @if ($pengumuman->pembuatpengumuman && $pengumuman->pembuatpengumuman->asesor)
                                        {{ $pengumuman->pembuatpengumuman->asesor->user->name }}
                                    @else
                                        Admin
                                    @endif
                                </h5>
                                <div class="text-xs text-gray-400">
                                    @if ($pengumuman->created_at->isToday())
                                        {{ $pengumuman->created_at->format('H:i') }}
                                    @else
                                        {{ $pengumuman->created_at->format('d M Y') }}
                                    @endif
                                    @if ($pengumuman->updated_at->ne($pengumuman->created_at))
                                        <span class="text-gray-500">(diedit)</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            <x-edit-button wire:click="showEditForm({{ $pengumuman->id }})">
                                <x-loading-spinner wire:loading
                                    wire:target="showEditForm({{ $pengumuman->id }})"></x-loading-spinner>
                                <span>
                                    Edit
                                </span>
                            </x-edit-button>
                            <x-delete-button wire:click="deletePengumuman({{ $pengumuman->id }})"
                                wire:confirm="Yakin ingin menghapus pengumuman ini?">Hapus</x-delete-button>
                        </div>
                    </div>

                    <h6 class="font-medium text-sm text-gray-800 dark:text-gray-100">{!! nl2br(e($pengumuman->rincian_pengumuman_asesmen)) !!}</h6>

                    @if ($pengumuman->pengumumanasesmenfile->isNotEmpty())
                        <div class="mt-3 border-t dark:border-gray-700 pt-2 grid grid-cols-1 md:grid-cols-2 gap-2">
                            @foreach ($pengumuman->pengumumanasesmenfile as $file)
                                <a href="{{ asset('storage/' . $file->path_file) }}" target="_blank"
                                    class="flex items-center gap-2 px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-md text-xs text-blue-600 dark:text-blue-400 hover:bg-gray-200 dark:hover:bg-gray-600">

                                    <span class="truncate">{{ basename($file->path_file) }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center text-gray-500 dark:text-gray-300 py-8">
                    Belum ada pengumuman.
                </div>
            @endforelse
        </div>
    @endif
</div>
