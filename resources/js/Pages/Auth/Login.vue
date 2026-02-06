<script setup>
import Checkbox from '@/Components/Input/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/Button/PrimaryButton.vue';
import TextInput from '@/Components/Input/TextInput.vue';
import CustomHeader from '@/Components/CustomHeader.vue';
import { Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>

        <CustomHeader judul="Log in Asesi" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <TextInput id="email" label="Email" type="email" v-model="form.email" required autofocus autocomplete="username" :error="form.errors.email"/>
            <TextInput id="password" label="Password" type="password" v-model="form.password" required
                autocomplete="current-password" :error="form.errors.password" />
            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Remember me</span>
                </label>
            </div>

            <div class="mt-6 flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                <Link v-if="canResetPassword" :href="route('password.request')"
                    class="text-sm text-gray-600 underline hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                    Lupa password?
                </Link>

                <div class="flex items-center justify-end gap-4">
                    <Link :href="route('register')"
                        class="text-sm text-gray-600 underline hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                        Buat akun baru
                    </Link>

                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Log in
                    </PrimaryButton>
                </div>
            </div>
        </form>
    </GuestLayout>
</template>
