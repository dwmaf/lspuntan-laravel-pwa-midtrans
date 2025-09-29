
<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    {{-- Notifikasi --}}
    <div x-data="{ show: false, message: '' }"
        x-on:notify.window="message=$event.detail.message;show=true;setTimeout(()=>show=false,3000)" x-show="show"
        x-transition x-text="message" class="fixed top-20 right-4 text-xs px-3 py-2 rounded bg-green-600 text-white z-50"
        style="display:none">
    </div>
    @if ($formMode === 'create')
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Tambah Skema Sertifikasi</h2>
        <form wire:submit.prevent="save" class="mt-4 flex flex-col gap-4">
            @include('livewire.admin.partials.skema-form-fields')
        </form>
    </div>
    @elseif($formMode === 'edit')
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Edit Skema Sertifikasi</h2>
        <form wire:submit.prevent="save" class="mt-4 flex flex-col gap-4">
            @include('livewire.admin.partials.skema-form-fields')
        </form>
    </div>
    @else
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Skema Sertifikasi</h2>
            <x-add-button wire:click="showCreateForm">
                <span>
                    Tambah Skema
                </span>
                <x-loading-spinner wire:loading wire:target="showCreateForm"></x-loading-spinner>
            </x-add-button>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            No</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Skema</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Format File APL</th>
                        <th scope="col"
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($skemas as $skema)
                        <tr>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $loop->iteration }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">
                                {{ $skema->nama_skema }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">
                                <div class="flex flex-col">
                                    @if ($skema->format_apl_1)
                                        <a href="{{ asset('storage/' . $skema->format_apl_1) }}" target="_blank"
                                            class="text-blue-500 hover:text-blue-700">APL.01</a>
                                    @else
                                        <p class="text-blue-500 ">APL.01 belum ada file</p>
                                    @endif
                                    @if ($skema->format_apl_2)
                                        <a href="{{ asset('storage/' . $skema->format_apl_2) }}" target="_blank"
                                            class="text-blue-500 hover:text-blue-700">APL.02</a>
                                    @else
                                        <p class="text-blue-500 ">APL.02 belum ada file</p>
                                    @endif
                                </div>
                            </td>
                            <td class="text-gray-700 dark:text-gray-200 px-4 py-2 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    
                                    <x-edit-button wire:click="showEditForm({{ $skema->id }})">
                                        <x-loading-spinner wire:loading wire:target="showEditForm({{ $skema->id }})"></x-loading-spinner>
                                        <span>
                                            Edit
                                        </span>
                                    </x-edit-button>
                                    <x-delete-button wire:click="destroy({{ $skema->id }})" wire:confirm="Apakah Anda yakin ingin menghapus skema ini?">Hapus</x-delete-button>
                                    
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4"
                                class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                Belum ada skema sertifikasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
