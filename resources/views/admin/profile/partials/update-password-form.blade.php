<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div x-data="{ show: false }">
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <div class="relative">
                <input :type="show ? 'text' : 'password'" id="update_password_current_password" name="current_password"
                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                    required autocomplete="current-password">
                <button type="button" @click="show = !show"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 cursor-pointer">
                    {{-- Ikon Mata Terbuka --}}
                    <x-fas-eye x-show="!show" class="w-4 text-gray-700 dark:text-gray-300" />
                    {{-- Ikon Mata Tercoret --}}
                    <x-fas-eye-slash x-show="show" class="w-4 text-gray-700 dark:text-gray-300" />
                </button>
            </div>

            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div x-data="{ show: false }">
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <div class="relative">
                <input :type="show ? 'text' : 'password'" id="update_password_password" name="password"
                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                    required autocomplete="new-password">
                <button type="button" @click="show = !show"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 cursor-pointer">
                    {{-- Ikon Mata Terbuka --}}
                    <x-fas-eye x-show="!show" class="w-4 text-gray-700 dark:text-gray-300" />
                    {{-- Ikon Mata Tercoret --}}
                    <x-fas-eye-slash x-show="show" class="w-4 text-gray-700 dark:text-gray-300" />
                </button>
            </div>

            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div x-data="{ show: false }">
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <div class="relative">
                <input :type="show ? 'text' : 'password'" id="update_password_password_confirmation"
                    name="password_confirmation"
                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                    required autocomplete="new-password">
                <button type="button" @click="show = !show"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 cursor-pointer">
                    {{-- Ikon Mata Terbuka --}}
                    <x-fas-eye x-show="!show" class="w-4 text-gray-700 dark:text-gray-300" />
                    {{-- Ikon Mata Tercoret --}}
                    <x-fas-eye-slash x-show="show" class="w-4 text-gray-700 dark:text-gray-300" />
                </button>
            </div>

            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
