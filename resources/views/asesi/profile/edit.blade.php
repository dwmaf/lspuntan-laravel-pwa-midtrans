<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>
    <div x-data="{ show: false, message: '' }"
        x-on:profile-updated.window="message=$event.detail.message; show=true; setTimeout(() => show=false, 3000)"
        x-on:password-updated.window="message=$event.detail.message; show=true; setTimeout(() => show=false, 3000)"
        x-show="show" x-transition.leave.duration.500ms
        class="mb-4 p-4 bg-green-100 text-green-700 border border-green-200 rounded-lg"
        style="display: none;"
        x-text="message">
    </div>
    <div class="flex flex-col gap-4">
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg">
            <livewire:asesi.profile.update-profile-asesi />
        </div>
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg">
            <livewire:asesi.profile.update-password-asesi />
        </div>
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg">
            @include('asesi.profile.partials.pengaturan')
        </div>

        {{-- <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div> --}}
    </div>
</x-app-layout>
