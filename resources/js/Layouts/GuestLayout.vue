<script setup>
import AppLogo from "../Components/AppLogo.vue";
import { Link } from "@inertiajs/vue3";
</script>
<template>
    <div
        class="min-h-screen flex flex-col sm:justify-center items-center py-6 bg-gray-100 dark:bg-gray-900"
    >
        <div>
            <Link href="/">
                <AppLogo />
            </Link>
        </div>
        <main
            class="w-full sm:max-w-md mt-6 px-6 py-3 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg"
        >
            <slot></slot>
        </main>

        <div class="mt-6 text-center">
            <Link :href="route('certificate.verify')"
                class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 underline underline-offset-2 transition-colors">
                Verifikasi Sertifikat BNSP
            </Link>
        </div>
    </div>
</template>
