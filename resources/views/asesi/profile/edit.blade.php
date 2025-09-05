<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>
    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 dark:bg-green-700 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-100 rounded-md"
            role="alert">
            {{ session('success') }}
        </div>
    @endif
    <div class="flex flex-col gap-4">
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg">
            @include('asesi.profile.partials.update-profile-information-form')
        </div>
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg">
            @include('asesi.profile.partials.update-password-form')
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
