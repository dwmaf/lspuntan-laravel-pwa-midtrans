<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/Button/PrimaryButton.vue';
import TextInput from '@/Components/Input/TextInput.vue';
import CustomHeader from '@/Components/CustomHeader.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Check, X } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const passwordRequirements = computed(() => {
    const pwd = form.password;
    return [
        {
            label: "Minimal 8 karakter",
            met: pwd.length >= 8
        },
        {
            label: "Huruf Besar & Kecil",
            met: /[a-z]/.test(pwd) && /[A-Z]/.test(pwd)
        },
        {
            label: "Angka (0-9)",
            met: /[0-9]/.test(pwd)
        },
        {
            label: "Simbol (!, $, #, dll)",
            met: /[^A-Za-z0-9]/.test(pwd)
        },
        {
            label: "Konfirmasi password sesuai",
            met: pwd.length > 0 && pwd === form.password_confirmation
        }
    ];
});

const isPasswordValid = computed(() => {
    return passwordRequirements.value.every(req => req.met);
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>

        <CustomHeader judul="Register Akun Asesi" />

        <form @submit.prevent="submit">
            <TextInput id="name" label="Name" type="text" class="mt-1 block w-full" v-model="form.name" required
                autofocus autocomplete="name" :error="form.errors.name" />
            <TextInput id="email" label="Email" type="email" class="block w-full" v-model="form.email" required
                autocomplete="username" :error="form.errors.email" />
            <div>
                <TextInput id="password" label="Password" type="password" v-model="form.password" required
                    autocomplete="new-password" :error="form.errors.password" />
                <div class="mt-2 p-3 bg-gray-50 dark:bg-gray-800 rounded-md border border-gray-200 dark:border-gray-700">
                    <p class="text-xs font-semibold text-gray-500 mb-2">Syarat Password:</p>
                    <ul class="space-y-1">
                        <li v-for="(req, index) in passwordRequirements" :key="index" 
                            class="flex items-center text-xs transition-colors duration-200"
                            :class="req.met ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400'"
                        >
                            <span class="mr-2">
                                <Check v-if="req.met" class="w-3 h-3" stroke-width="3" />
                                <X v-else class="w-3 h-3" stroke-width="3" />
                            </span>
                            {{ req.label }}
                        </li>
                    </ul>
                </div>
            </div>
            <TextInput id="password_confirmation" label="Confirm Password" type="password"
                v-model="form.password_confirmation" required autocomplete="new-password"
                :error="form.errors.password_confirmation" />
            <div class="mt-4 flex items-center justify-end">
                <Link :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800">
                    Sudah punya akun asesi?
                </Link>

                <PrimaryButton 
                    class="ms-4 transition-all duration-300" 
                    :class="{ 'opacity-25 cursor-not-allowed': form.processing || !isPasswordValid }" 
                    :disabled="form.processing || !isPasswordValid"
                >
                    Register
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
