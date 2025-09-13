{{-- filepath: resources/views/livewire/admin/pengumuman.blade.php --}}
<div x-data="{ showCreate: false }" x-on:pengumuman-created.window="showCreate = false"
    x-on:validation-error.window="$refs.createForm.scrollIntoView({ behavior: 'smooth', block: 'center' })">
    {{-- Notifikasi --}}
    <div x-data="{ show: false, message: '' }"
        x-on:notify.window="message=$event.detail.message;show=true;setTimeout(()=>show=false,2500)" x-show="show"
        x-transition x-text="message" class="fixed top-20 right-4 text-xs px-3 py-2 rounded bg-green-600 text-white z-50"
        style="display:none">
    </div>
    @if ($formMode === 'create')
        <div class="mt-4 p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Buat Pengumuman</h2>
            <form wire:submit.prevent="save" class="mt-4 flex flex-col gap-4">
                @include('livewire.admin.partials.pengumuman-form-fields')
            </form>
        </div>
    @else
        <div x-show="!showCreate" class="p-6 mb-2 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="flex justify-between items-center">
                <p class="text-gray-500 dark:text-gray-400 text-xs">Buat pengumuman baru untuk para asesi.</p>
                <x-add-button wire:click="showCreateForm">Tambah Pengumuman</x-add-button>
            </div>
        </div>
    @endif
    {{-- <div x-ref="createForm" x-show="showCreate" style="display: none;"
        class="p-6 mb-2 bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
            Buat Pengumuman Baru
        </h3>

        <div class="space-y-4">
            <div>
                <x-input-label>Rincian</x-input-label>
                <textarea wire:model.defer="rincian_pengumuman_asesmen" rows="8"
                    class="mt-1 w-full text-sm p-3 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100"></textarea>
                <x-input-error class="mt-2" :messages="$errors->get('rincian_pengumuman_asesmen')" />
            </div>
            <div>
                <x-input-label>Lampiran</x-input-label>

                <input type="file" wire:model="newFiles" multiple
                    class="mt-1 w-full px-3 py-2 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md text-sm">
                <x-input-error class="mt-2" :messages="$errors->get('newFiles.*')" />
            </div>
        </div>

        <div class="flex gap-2 items-center mt-6">
            <span wire:loading wire:target="save" class="flex items-center">
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4">
                    </circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
            </span>
            <x-primary-button wire:click="save" wire:loading.attr="disabled">Simpan</x-primary-button>
            <x-secondary-button @click="showCreate = false; $wire.resetForm()">Batal</x-secondary-button>
        </div>
    </div> --}}



    @if ($formMode === 'edit')
        <div class="fixed inset-0 z-40 bg-black/50" wire:click="resetForm"></div>

        <div
            class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-50 bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-2xl flex flex-col max-h-[90vh]">

            <div class="p-6 border-b dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Edit Pengumuman
                </h3>
            </div>


            <div class="p-6 flex-1 overflow-y-auto">

                <form wire:submit.prevent="save" class="flex flex-col gap-4">
                    @include('livewire.admin.partials.pengumuman-form-fields')
                </form>
            </div>
        </div>
    @endif


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
                        <x-edit-button wire:click="showEditForm({{ $pengumuman->id }})">Edit</x-edit-button>
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
</div>
