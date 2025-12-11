<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import CustomHeader from '@/Components/CustomHeader.vue';
import { Link } from "@inertiajs/vue3";
import { Award, Activity, Users, Wallet, GraduationCap, User2 } from 'lucide-vue-next';
import { route } from 'ziggy-js';
const props = defineProps({
    pendingRegistrants: Array,
    sertificationBerlangsung: Array,
    sertificationSelesai: Array,
    totalAsesi: Array,
    asesiLulus: Array,
    asesiBaruDaftar: Array,
});
</script>

<template>
    <AdminLayout>
        <CustomHeader judul="Dashboard Admin"/>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <Link :href="route('admin.kelolasertifikasi.index')"
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Sertifikasi Berlangsung
                    </h3>
                    <p class="mt-1 text-3xl font-semibold text-blue-600">{{ sertificationBerlangsung.length }}</p>
                </div>
                <Award class="w-8 h-8 text-blue-600" />
            </Link>
            <Link :href="route('admin.kelolasertifikasi.index', { tab: 'selesai' })"
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Sertifikasi Selesai</h3>
                    <p class="mt-1 text-3xl font-semibold text-green-600">{{ sertificationSelesai.length }}</p>
                </div>
                <Activity class="w-8 h-8 text-green-600" />
            </Link>
            <div
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Pendaftar Baru</h3>
                    <p class="mt-1 text-3xl font-semibold text-cyan-600">{{ asesiBaruDaftar.length }}</p>
                </div>
                <User2 class="w-8 h-8 text-cyan-600" />
            </div>
            <div
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Bukti Pembayaran Pending</h3>
                    <p class="mt-1 text-3xl font-semibold text-yellow-600">{{ pendingRegistrants.length }}</p>
                </div>
                <Wallet class="w-8 h-8 text-yellow-600 shrink-0" />
            </div>
            <div
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Asesi</h3>
                    <p class="mt-1 text-3xl font-semibold text-purple-600">{{ totalAsesi.length }}</p>
                </div>
                <Users class="w-8 h-8 text-purple-600" />
            </div>
            <div
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Lulusan</h3>
                    <p class="mt-1 text-3xl font-semibold text-fuchsia-600">{{ asesiLulus.length }}</p>
                </div>
                <GraduationCap class="w-8 h-8 text-fuchsia-600" />
            </div>
            
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 flex flex-col gap-2">
                <h3 class="text-xl font-medium text-gray-900 dark:text-gray-100 truncate">Manajemen Sertifikasi</h3>
                <p class="text-sm font-normal text-gray-600 dark:text-gray-500">Kelola sertifikasi yang berlangsung</p>
                <Link :href="route('admin.kelolasertifikasi.show',sert.id)" class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg justify-between" v-for="sert in sertificationBerlangsung">
                    <div class="">
                        <h3 class="font-medium text-gray-900 dark:text-gray-300">{{ sert.skema.nama_skema }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-500">{{ sert.asesis_count }} asesi terdaftar</p>
                    </div>
                    <div class="text-xs bg-green-100 px-2 py-1 rounded-full text-green-800">
                        {{ sert.status }}
                    </div>
                </Link>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 flex flex-col gap-2">
                <h3 class="text-xl font-medium text-gray-800 dark:text-gray-100 truncate">Aktivitas Terbaru</h3>
                <p class="text-sm font-normal text-gray-500">Ringkasan aktivitas sistem</p>
                <div className="space-y-4">
              <div className="flex items-start gap-3">
                <div className="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                <div>
                  <p className="text-sm dark:text-gray-400 font-medium">Penilaian baru dimulai</p>
                  <p className="text-xs text-gray-500">Ahmad Rizki - Web Developer</p>
                  <p className="text-xs text-gray-400">2 jam yang lalu</p>
                </div>
              </div>
              <div className="flex items-start gap-3">
                <div className="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                <div>
                  <p className="text-sm font-medium">Sertifikat diterbitkan</p>
                  <p className="text-xs text-gray-500">Budi Santoso - Web Developer</p>
                  <p className="text-xs text-gray-400">5 jam yang lalu</p>
                </div>
              </div>
              <div className="flex items-start gap-3">
                <div className="w-2 h-2 bg-purple-500 rounded-full mt-2"></div>
                <div>
                  <p className="text-sm font-medium">Asesi baru mendaftar</p>
                  <p className="text-xs text-gray-500">Data Analyst Program</p>
                  <p className="text-xs text-gray-400">1 hari yang lalu</p>
                </div>
              </div>
              
            </div>
            </div>
        </div>
    </AdminLayout>

</template>
