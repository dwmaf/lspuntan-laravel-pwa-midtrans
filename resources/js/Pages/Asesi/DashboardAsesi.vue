<script setup>
import AsesiLayout from '@/Layouts/AsesiLayout.vue';
import CustomHeader from '@/Components/CustomHeader.vue';
import { Link } from "@inertiajs/vue3";
import { Award, Activity, Wallet, GraduationCap } from 'lucide-vue-next';
import { route } from 'ziggy-js';

const props = defineProps({
    sertifikasiBerlangsung: Array,
    sertifikasiSelesai: Array,
    
    user: Object,
    student: Object
});
</script>

<template>
    <AsesiLayout>
        <CustomHeader judul="Dashboard Asesi" />
        

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
            <!-- Sertifikasi Berlangsung -->
            <Link :href="route('asesi.sertifikasi.index')"
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Sertifikasi Berlangsung</h3>
                <p class="mt-1 text-3xl font-semibold text-blue-600">{{ sertifikasiBerlangsung.length }}</p>
            </div>
            <Award class="w-8 h-8 text-blue-600" />
            </Link>
            
            <!-- Sertifikasi Selesai -->
            <div
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Sertifikasi Selesai</h3>
                    <p class="mt-1 text-3xl font-semibold text-green-600">{{ sertifikasiSelesai.length }}</p>
                </div>
                <Activity class="w-8 h-8 text-green-600" />
            </div>

            
            <!-- <div
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">something else</h3>
                    <p class="mt-1 text-3xl font-semibold text-yellow-600"></p>
                </div>
                <Wallet class="w-8 h-8 text-yellow-600" />
            </div> -->
        </div>

        <!-- Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Active Certifications List -->
            <div
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 flex flex-col gap-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-medium text-gray-900 dark:text-gray-100 truncate">Sertifikasi Saya</h3>
                    <Link :href="route('asesi.sertifikasi.index')" class="text-sm text-blue-600 hover:underline">Lihat
                    Semua</Link>
                </div>

                <div v-if="sertifikasiBerlangsung.length > 0" class="flex flex-col gap-3">
                    <Link v-for="asesi in sertifikasiBerlangsung" :key="asesi.id"
                        :href="route('asesi.sertifikasi.applied.show', [asesi.sertification_id, asesi.id])"
                        class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg justify-between hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-gray-300">{{ asesi.sertification.skema.nama_skema
                            }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-500">Status: {{ asesi.status }}</p>
                    </div>
                    <div class="text-xs px-2 py-1 rounded-full" :class="{
                        'bg-blue-100 text-blue-800': asesi.status === 'daftar',
                        'bg-yellow-100 text-yellow-800': asesi.status === 'perlu_perbaikan_berkas',
                        'bg-green-100 text-green-800': asesi.status === 'diterima',
                    }">
                        {{ asesi.status }}
                    </div>
                    </Link>
                </div>
                <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <p>Belum ada sertifikasi yang diikuti.</p>
                    <Link :href="route('asesi.sertifikasi.index')"
                        class="mt-2 inline-block text-blue-600 hover:underline">Daftar Sekarang</Link>
                </div>
            </div>

            <!-- Welcome / Info Card -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5">
                <h3 class="text-xl font-medium text-gray-900 dark:text-gray-100 mb-2">Selamat Datang, {{ user.name }}!
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Selamat datang di Dashboard Asesi LSP UNTAN. Di sini Anda dapat memantau status sertifikasi,
                    melakukan pembayaran, dan melihat pengumuman terkait asesmen.
                </p>

                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-100 dark:border-blue-800">
                    <h4 class="font-medium text-blue-800 dark:text-blue-300 mb-2">Informasi Penting</h4>
                    <ul class="list-disc list-inside text-sm text-blue-700 dark:text-blue-400 space-y-1">
                        <li>Pastikan profil Anda sudah lengkap.</li>
                        <li>Cek notifikasi secara berkala untuk update terbaru.</li>
                    </ul>
                </div>
            </div>
        </div>
        
    </AsesiLayout>
</template>
