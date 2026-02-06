<script setup>
import AsesiLayout from '@/Layouts/AsesiLayout.vue';
import CustomHeader from '@/Components/CustomHeader.vue';
import { Link } from "@inertiajs/vue3";
import { Award, Activity, Wallet, GraduationCap, Bell, Clock, AlertTriangle } from 'lucide-vue-next';
import { route } from 'ziggy-js';
import { computed } from 'vue';

const props = defineProps({
    sertifikasiBerlangsung: Array,
    sertifikasiSelesai: Array,
    pengumumanTerbaru: Array,
    user: Object,
    student: Object
});

const isProfileIncomplete = computed(() => {
    return !props.user?.name ||
        !props.student?.nik ||
        !props.student?.tmpt_lhr ||
        !props.student?.tgl_lhr ||
        !props.student?.kelamin ||
        !props.student?.kebangsaan ||
        !props.user?.no_tlp_hp ||
        !props.student?.kualifikasi_pendidikan ||
        !props.student?.foto_ktp ||
        !props.student?.pas_foto;
});

const getBerkasColor = (status) => {
    switch (status) {
        case 'sudah_lengkap': return 'bg-green-100 text-green-800 border-green-200';
        case 'perlu_perbaikan_berkas': return 'bg-red-100 text-red-800 border-red-200 animate-pulse'; // Kasih animasi biar sadar
        default: return 'bg-yellow-100 text-yellow-800 border-yellow-200';
    }
};

const getAsesmenColor = (status) => {
    return status === 'diberikan'
        ? 'bg-blue-100 text-blue-800 border-blue-200'
        : 'bg-gray-100 text-gray-500 border-gray-200';
};

const getFinalColor = (status) => {
    switch (status) {
        case 'kompeten': return 'bg-emerald-100 text-emerald-800 border-emerald-200 font-bold';
        case 'belum_kompeten': return 'bg-red-100 text-red-800 border-red-200';
        case 'diskualifikasi': return 'bg-black text-white border-black';
        default: return 'bg-gray-100 text-gray-400 border-gray-200 border-dashed';
    }
};
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
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Sertifikasi Berlangsung
                    </h3>
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
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-start">
            <!-- Active Certifications List -->
            <div
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 flex flex-col gap-4 h-fit">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-medium text-gray-900 dark:text-gray-100 truncate">Sertifikasi Saya</h3>
                    <Link :href="route('asesi.sertifikasi.index')" class="text-sm text-blue-600 hover:underline">Lihat
                        Semua</Link>
                </div>

                <div v-if="sertifikasiBerlangsung.length > 0" class="flex flex-col gap-4">
                    <Link v-for="asesi in sertifikasiBerlangsung" :key="asesi.id"
                        :href="route('asesi.sertifikasi.applied.show', [asesi.sertification_id, asesi.id])"
                        class="block p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all shadow-sm hover:shadow-md">

                        <!-- Header Card -->
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-200">
                                {{ asesi.sertification.skema.nama_skema }}
                            </h3>

                        </div>

                        <!-- Grid 3 Status -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                    📄 Administrasi
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium border"
                                    :class="getBerkasColor(asesi.status_berkas)">
                                    {{ asesi.status_berkas_label }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                    📝 Akses Ujian
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium border"
                                    :class="getAsesmenColor(asesi.status_akses_asesmen)">
                                    {{ asesi.status_akses_asesmen_label }}
                                </span>
                            </div>
                            <div
                                class="flex items-center justify-between text-sm pt-2 border-t border-gray-100 dark:border-gray-700 mt-2">
                                <span class="text-gray-700 dark:text-gray-300 font-medium">
                                    🎓 Hasil Akhir
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium border"
                                    :class="getFinalColor(asesi.status_final)">
                                    {{ asesi.status_final_label }}
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>
                <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <p>Belum ada sertifikasi yang diikuti.</p>
                    <Link :href="route('asesi.sertifikasi.index')"
                        class="mt-2 inline-block text-blue-600 hover:underline">Daftar Sekarang</Link>
                </div>
            </div>
            <div class="flex flex-col gap-4">
                <!-- Welcome / Info Card -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5">
                    <h3 class="text-xl font-medium text-gray-900 dark:text-gray-100 mb-2">Selamat Datang, {{ user.name }}!
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        Selamat datang di Dashboard Asesi LSP UNTAN. Di sini Anda dapat memantau status sertifikasi dan
                        melihat pengumuman terkait asesmen.
                    </p>
    
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-100 dark:border-blue-800">
                        <h4 class="font-medium text-blue-800 dark:text-blue-300 mb-2">Informasi Penting</h4>
                        <ul class="list-disc list-inside text-sm text-blue-700 dark:text-blue-400 space-y-1">
                            <li v-if="isProfileIncomplete">Pastikan profil Anda sudah lengkap.</li>
                            <li>Cek notifikasi secara berkala untuk update terbaru.</li>
                        </ul>
                    </div>
                </div>
    
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center gap-2">
                            <Bell class="w-5 h-5 text-yellow-500" />
                            Pengumuman Terbaru
                        </h3>
                    </div>
    
                    <div v-if="pengumumanTerbaru && pengumumanTerbaru.length > 0" class="space-y-4">
                        <div v-for="info in pengumumanTerbaru" :key="info.id"
                            class="relative pl-4 border-l-2 border-blue-200 dark:border-blue-800 hover:border-blue-500 transition-colors">
    
                            <div class="mb-1 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                <Clock class="w-3 h-3" />
                                {{ info.tanggal }}
                            </div>
    
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                {{ info.judul }}
                            </h4>
    
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">
                                {{ info.pesan }}
                            </p>
                        </div>
                    </div>
    
                    <div v-else class="text-center py-6">
                        <div
                            class="bg-gray-50 dark:bg-gray-700/50 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-3">
                            <Bell class="w-6 h-6 text-gray-400" />
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada pengumuman terbaru.</p>
                    </div>
                </div>
            </div>
        </div>

    </AsesiLayout>
</template>
