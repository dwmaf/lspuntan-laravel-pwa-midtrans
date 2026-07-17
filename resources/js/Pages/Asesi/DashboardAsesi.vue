<script setup>
import AsesiLayout from '@/Layouts/AsesiLayout.vue';
import CustomHeader from '@/Components/CustomHeader.vue';
import StatusBadge from "@/Components/StatusBadge.vue";
import { Link } from "@inertiajs/vue3";
import { Award, Activity, Bell, Clock, FileText, UserCheck, GraduationCap } from 'lucide-vue-next';
import { route } from 'ziggy-js';
import { computed } from 'vue';

const props = defineProps({
    sertifikasiBerlangsung: Array,
    sertifikasiSelesai: Array,
    pengumumanTerbaru: Array,
    user: Object,
    mahasiswa: Object
});

const isProfileIncomplete = computed(() => {
    return !props.user?.name ||
        !props.mahasiswa?.nik ||
        !props.mahasiswa?.tmpt_lhr ||
        !props.mahasiswa?.tgl_lhr ||
        !props.mahasiswa?.kelamin ||
        !props.mahasiswa?.kebangsaan ||
        !props.user?.no_tlp_hp ||
        !props.mahasiswa?.kualifikasi_pendidikan ||
        !props.mahasiswa?.foto_ktp ||
        !props.mahasiswa?.pas_foto;
});

const getStatusBerkasAdministrasi = (status) => {
    const data = {
        'menunggu_verifikasi_admin': {
            variant: 'primary',
            text: 'Menunggu Verifikasi Admin'
        },
        'perlu_perbaikan_berkas': {
            variant: 'warning',
            text: 'Perlu Perbaikan Berkas'
        },
        'sudah_lengkap': {
            variant: 'success',
            text: 'Sudah Lengkap'
        },
    };
    return data[status] || {
        variant: 'neutral',
        text: status
    };
};

const getStatusFinalAsesi = (status) => {
    const data = {
        'belum_ditetapkan': {
            variant: 'neutral',
            text: 'Belum Ditetapkan'
        },
        'belum_kompeten': {
            variant: 'warning',
            text: 'Belum Kompeten'
        },
        'kompeten': {
            variant: 'success',
            text: 'Kompeten'
        },
        'diskualifikasi': {
            variant: 'danger',
            text: 'Diskualifikasi'
        },
    };
    return data[status] || {
        variant: 'neutral',
        text: status
    };
};
</script>

<template>
    <AsesiLayout>
        <CustomHeader judul="Dashboard Asesi" />
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <Link :href="route('asesi.sertifikasi.index')"
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Sertifikasi Berlangsung
                    </h3>
                    <p class="mt-1 text-3xl font-semibold text-blue-600">{{ sertifikasiBerlangsung.length }}</p>
                </div>
                <Award class="w-8 h-8 text-blue-600" />
            </Link>
            <div
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Sertifikasi Selesai</h3>
                    <p class="mt-1 text-3xl font-semibold text-green-600">{{ sertifikasiSelesai.length }}</p>
                </div>
                <Activity class="w-8 h-8 text-green-600" />
            </div>
        </div>

        
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
                        :href="route('asesi.sertifikasi.applied.show', [asesi.sertifikasi_id, asesi.id])"
                        class="block p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all shadow-sm hover:shadow-md">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-200">
                                {{ asesi.sertifikasi.skema.nama_skema }}
                            </h3>

                        </div>

                        <!-- Grid 3 Status -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                    <FileText class="w-4 h-4" /> Administrasi
                                </span>
                                <StatusBadge :variant="getStatusBerkasAdministrasi(asesi.status_berkas).variant">
                                    {{ getStatusBerkasAdministrasi(asesi.status_berkas).text }}
                                </StatusBadge>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                    <UserCheck class="w-4 h-4" /> Asesor
                                </span>
                                <span v-if="asesi.asesor" class="font-medium text-gray-900 dark:text-gray-100 text-right truncate max-w-[200px]" :title="asesi.asesor.user?.name">
                                    {{ asesi.asesor.user?.name }}
                                </span>
                                <StatusBadge v-else variant="neutral">
                                    Belum Ditetapkan
                                </StatusBadge>
                            </div>
                            <div
                                class="flex items-center justify-between text-sm pt-2 border-t border-gray-100 dark:border-gray-700 mt-2">
                                <span class="text-gray-700 dark:text-gray-300 font-medium flex items-center gap-2">
                                    <GraduationCap class="w-4 h-4" /> Hasil Akhir
                                </span>
                                <StatusBadge :variant="getStatusFinalAsesi(asesi.status_final).variant">
                                    {{ getStatusFinalAsesi(asesi.status_final).text }}
                                </StatusBadge>
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
                        <div v-for="pengumuman in pengumumanTerbaru" :key="pengumuman.id"
                            class="relative pl-4 border-l-2 border-blue-200 dark:border-blue-800 hover:border-blue-500 transition-colors">
    
                            <div class="mb-1 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                <Clock class="w-3 h-3" />
                                {{ pengumuman.tanggal }}
                            </div>
    
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                {{ pengumuman.judul }}
                            </h4>
    
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">
                                {{ pengumuman.pesan }}
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
