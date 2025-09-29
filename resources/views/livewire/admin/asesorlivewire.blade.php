{{-- filepath: d:\Laravel-App\lsp-untan-laravel-pwa\resources\views\livewire\admin\asesorlivewire.blade.php --}}
<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Asesor') }}
        </h2>
    </x-slot>
    {{-- Notifikasi --}}
    <div x-data="{ show: false, message: '' }"
        x-on:notify.window="message=$event.detail.message;show=true;setTimeout(()=>show=false,3000)" x-show="show"
        x-transition x-text="message" class="fixed top-20 right-4 text-xs px-3 py-2 rounded bg-green-600 text-white z-50"
        style="display:none">
    </div>

    @if ($formMode === 'create')
        {{-- Tampilan Form Create --}}
        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Tambah Asesor</h2>
            <form wire:submit.prevent="save" class="mt-4 flex flex-col gap-4">
                @include('livewire.admin.partials.asesor-form-fields')
            </form>
        </div>
    @elseif($formMode === 'edit')
        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Edit Asesor</h2>
            <form wire:submit.prevent="update" class="mt-4 flex flex-col gap-4">
                @include('livewire.admin.partials.asesor-form-fields')
            </form>
        </div>
    @else
        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Daftar Asesor</h2>
                <x-add-button wire:click="showCreateForm">
                    <span>
                        Tambah Asesor
                    </span>
                    <x-loading-spinner wire:loading wire:target="showCreateForm"></x-loading-spinner>
                </x-add-button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama & Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Skema</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($asesors as $asesor)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">
                                    <div class="font-medium">{{ $asesor->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $asesor->user->email }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($asesor->skemas as $skema)
                                            <span class="inline-block rounded bg-gray-200 dark:bg-gray-700 px-2 py-1 text-xs font-medium">{{ $skema->nama_skema }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <x-edit-button wire:click="edit({{ $asesor->id }})">
                                            <span>
                                                Edit
                                            </span>
                                            <x-loading-spinner wire:loading wire:target="edit({{ $asesor->id }})"></x-loading-spinner>
                                        </x-edit-button>
                                        <x-delete-button wire:click="destroy({{ $asesor->id }})" wire:confirm="Yakin ingin menghapus asesor ini? Semua data terkait akan hilang.">Hapus</x-delete-button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-4">Tidak ada data asesor.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
