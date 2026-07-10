<script setup>
import { Link } from '@inertiajs/vue3';
import AppLogo from '@/Components/AppLogo.vue';

defineProps({
    status: Number,
});

const messages = {
    403: 'Forbidden',
    404: 'Halaman tidak ditemukan',
    419: 'Sesi telah kadaluarsa',
    429: 'Terlalu banyak permintaan',
    500: 'Terjadi kesalahan server',
    503: 'Layanan sedang dalam perbaikan',
};
</script>

<template>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100 dark:bg-gray-900 px-4">
        <AppLogo />
        <h1 class="text-6xl font-bold text-gray-800 dark:text-gray-200 mt-6">{{ status }}</h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 mt-2">{{ messages[status] || 'Terjadi kesalahan' }}</p>
        <Link href="/" class="mt-6 text-indigo-600 hover:underline">Kembali ke Beranda</Link>
    </div>
</template>