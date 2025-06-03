<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg">
            @include('asesi.profile.partials.update-profile-information-form')
            
        </div>
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg">
            @include('asesi.profile.partials.update-password-form')
        </div>

        {{-- <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div> --}}
    </div>
</x-app-layout>
