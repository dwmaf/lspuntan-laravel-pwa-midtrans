<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/Input/InputError.vue';
import InputLabel from '@/Components/Input/InputLabel.vue';
import PrimaryButton from '@/Components/Button/PrimaryButton.vue';
import TextInput from '@/Components/Input/TextInput.vue';
import CustomHeader from '@/Components/CustomHeader.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';

const showPassword = ref(false);
const showPasswordConfirm = ref(false);
const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>

        <CustomHeader judul="Register" />

        <form @submit.prevent="submit">
            <TextInput id="name"label="Name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus
                autocomplete="name" :error="form.errors.name"/>
                <TextInput id="email" label="Email" type="email" class="block w-full" v-model="form.email" required
                    autocomplete="username" :error="form.errors.email"/>
            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'" id="password" v-model="form.password"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        required autocomplete="new-password">
                    <button type="button" @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 cursor-pointer">
                        <Eye v-if="showPassword" class="text-lg text-gray-700 dark:text-gray-200" />
                        <EyeOff v-if="!showPassword" class="text-lg text-gray-700 dark:text-gray-200" />
                    </button>
                </div>
                

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirm Password" />
                <div class="relative">
                    <input :type="showPasswordConfirm ? 'text' : 'password'" id="password_confirmation" v-model="form.password_confirmation"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        required autocomplete="new-password">
                    <button type="button" @click="showPasswordConfirm = !showPasswordConfirm"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 cursor-pointer">
                        <Eye v-if="showPasswordConfirm" class="text-lg text-gray-700 dark:text-gray-200" />
                        <EyeOff v-if="!showPasswordConfirm" class="text-lg text-gray-700 dark:text-gray-200" />
                    </button>
                </div>

                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800">
                    Sudah punya akun?
                </Link>

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Register
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
