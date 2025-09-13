<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>
    {{-- Notifikasi Global untuk Halaman Ini --}}
    <div x-data="{ show: false, message: '' }"
        x-on:profile-updated.window="message=$event.detail.message; show=true; setTimeout(() => show=false, 3000)"
        x-on:password-updated.window="message=$event.detail.message; show=true; setTimeout(() => show=false, 3000)"
        x-show="show" x-transition.leave.duration.500ms
        class="mb-4 p-4 bg-green-100 text-green-700 border border-green-200 rounded-lg"
        style="display: none;"
        x-text="message">
    </div>

    <div class="flex flex-col gap-4">
        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow rounded-lg">
            {{-- Panggil komponen informasi profil --}}
            <livewire:admin.profile.update-profile />
        </div>

        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow rounded-lg">
            {{-- Panggil komponen update password --}}
            <livewire:admin.profile.update-password />
        </div>

        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow rounded-lg">
            {{-- Anda bisa membiarkan ini atau mengubahnya menjadi komponen juga jika perlu --}}
            @include('admin.profile.partials.pengaturan')
        </div>
    </div>
    {{-- <div class="flex flex-col gap-4">
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg">
            @include('admin.profile.partials.update-profile-information-form')
        </div>
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg">
            @include('admin.profile.partials.update-password-form')
        </div>
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg">
            @include('admin.profile.partials.pengaturan')
        </div>
    </div> --}}
</x-admin-layout>
