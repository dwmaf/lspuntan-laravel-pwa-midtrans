<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import CustomHeader from '@/Components/CustomHeader.vue';
import { Link } from "@inertiajs/vue3";
import { Award, Activity, Users, Wallet, GraduationCap, User2 } from 'lucide-vue-next';
import { route } from 'ziggy-js';
import VueApexCharts from "vue3-apexcharts";
import { computed, ref, onMounted, onUnmounted, watch } from 'vue';
import { useDark } from '@vueuse/core';

const props = defineProps({
    sertificationBerlangsung: Array,
    sertificationSelesaiCount: Number,
    totalAsesiCount: Number,
    asesiLulusCount: Number,
    pipelineStats: Object,
    charts: Object,
    recentActivities: Array,
    isAsesor: Boolean,
});

const isDark = useDark();
// watch(isDark, (val) => {
//   console.log('Mode berubah jadi:', val ? 'Gelap' : 'Terang');
// });
// Chart 1: Trend Pendaftaran (Line/Area)
const trendOptions = computed(() => {
    // Definisi Warna
    const text = isDark.value ? '#cbd5e1' : '#64748b'; // Slate-300 (Dark) / Slate-500 (Light)
    const grid = isDark.value ? '#374151' : '#e2e8f0'; // Gray-700 (Dark) / Gray-200 (Light)

    return {
        chart: { type: 'area', toolbar: { show: false }, fontFamily: 'Inter, sans-serif', background: 'transparent' },
        theme: { mode: isDark.value ? 'dark' : 'light' }, // Beritahu engine chart
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        xaxis: {
            categories: props.charts?.monthlyStats.map(s => s.date) || [],
            labels: { style: { colors: text } }, // Terapkan warna text
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: { labels: { style: { colors: text } } }, // Terapkan warna text
        grid: { borderColor: grid, strokeDashArray: 4 }, // Terapkan warna grid
        tooltip: { theme: isDark.value ? 'dark' : 'light', x: { format: 'MM/yy' } },
        colors: ['#3b82f6'],
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.3 } }
    };
});

const trendSeries = computed(() => [{
    name: 'Pendaftar Baru',
    data: props.charts?.monthlyStats.map(s => s.count) || []
}]);

// Chart 2: Top Skema (Bar Horizontal)
const schemeOptions = computed(() => {
    const text = isDark.value ? '#cbd5e1' : '#64748b';

    return {
        chart: { type: 'bar', toolbar: { show: false }, fontFamily: 'Inter, sans-serif', background: 'transparent' },
        theme: { mode: isDark.value ? 'dark' : 'light' },
        plotOptions: {
            bar: { borderRadius: 4, horizontal: true, barHeight: '70%', distributed: true }
        },
        dataLabels: { enabled: true, textAnchor: 'start', style: { colors: ['#fff'] }, offsetX: 0 },
        xaxis: {
            categories: props.charts?.topSchemes.map(s => s.nama_skema) || [],
            labels: { show: true, style: { colors: text } } // Warna Label X
        },
        yaxis: {
            labels: { style: { colors: text }, maxWidth: 200 } // Warna Label Y
        },
        colors: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6'],
        legend: { show: false },
        grid: { show: false }
    };
});

const schemeSeries = computed(() => [{
    name: 'Jumlah Pendaftar',
    data: props.charts?.topSchemes.map(s => s.total_pendaftar) || []
}]);

// Chart 3: Status Kompetensi (Donut)
const competencyOptions = computed(() => {
    const text = isDark.value ? '#cbd5e1' : '#64748b';
    const border = isDark.value ? '#1f2937' : '#ffffff'; // Border donut menyesuaikan bg card

    return {
        chart: { type: 'donut', fontFamily: 'Inter, sans-serif', background: 'transparent' },
        theme: { mode: isDark.value ? 'dark' : 'light' },
        labels: props.charts?.competencyStats.map(s => s.status_final === 'kompeten' ? 'Kompeten' : 'Belum Kompeten') || [],
        colors: ['#10b981', '#ef4444', '#f59e0b'],
        legend: {
            position: 'bottom',
            labels: { colors: text } // Warna Legend
        },
        stroke: { show: true, colors: [border] }, // Warna Border Donut
        plotOptions: { pie: { donut: { size: '65%' } } }
    };
});

const competencySeries = computed(() => props.charts?.competencyStats.map(s => s.count) || []);

const timeAgo = (dateParam) => {
    if (!dateParam) return null;

    const date = new Date(dateParam);
    const now = new Date();
    const seconds = Math.round((now - date) / 1000);
    const minutes = Math.round(seconds / 60);
    const hours = Math.round(minutes / 60);
    const days = Math.round(hours / 24);

    if (seconds < 60) return 'Baru saja';
    if (minutes < 60) return `${minutes} menit yang lalu`;
    if (hours < 24) return `${hours} jam yang lalu`;
    if (days < 7) return `${days} hari yang lalu`;
    if (days < 30) return `${Math.floor(days / 7)} minggu yang lalu`;

    // Jika lebih dari sebulan, tampilkan tanggal biasa (Cth: 20 Januari 2025)
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric'
    }).format(date);
};

const getActivityColor = (event) => {
    switch (event) {
        case 'created': return 'bg-green-500';
        case 'updated': return 'bg-blue-500';
        case 'deleted': return 'bg-red-500';
        default: return 'bg-purple-500';
    }
};

const formatEventName = (event) => {
    const map = { created: 'Dibuat', updated: 'Diperbarui', deleted: 'Dihapus' };
    return map[event] || event;
}

// Calculate total asesi in pipeline and percentage for each stage
const pipelinePercentages = computed(() => {
    const total = props.pipelineStats.verifikasi_berkas +
        props.pipelineStats.revisi_asesi +
        props.pipelineStats.menunggu_jadwal +
        props.pipelineStats.proses_asesmen;

    if (total === 0) return {
        verifikasi_berkas: 0,
        revisi_asesi: 0,
        menunggu_jadwal: 0,
        proses_asesmen: 0
    };

    return {
        verifikasi_berkas: Math.round((props.pipelineStats.verifikasi_berkas / total) * 100),
        revisi_asesi: Math.round((props.pipelineStats.revisi_asesi / total) * 100),
        menunggu_jadwal: Math.round((props.pipelineStats.menunggu_jadwal / total) * 100),
        proses_asesmen: Math.round((props.pipelineStats.proses_asesmen / total) * 100)
    };
});

</script>

<template>
    <AdminLayout>
        <CustomHeader :judul="isAsesor ? 'Dashboard Asesor' : 'Dashboard Admin'" />
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <Link :href="route('admin.kelolasertifikasi.index')"
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 flex items-center justify-between">
                <div class="min-w-0">
                    <h3 title="Sertifikasi Berlangsung"
                        class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Sertifikasi Berlangsung
                    </h3>
                    <p class="mt-1 text-3xl font-semibold text-blue-600">{{ sertificationBerlangsung.length }}</p>
                </div>
                <Award class="w-8 h-8 text-blue-600 shrink-0 ml-3" />
            </Link>
            <Link :href="route('admin.kelolasertifikasi.index', { tab: 'selesai' })"
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 flex items-center justify-between">
                <div class="min-w-0">
                    <h3 title="Sertifikasi Berlangsung"
                        class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Sertifikasi Selesai</h3>
                    <p class="mt-1 text-3xl font-semibold text-green-600">{{ sertificationSelesaiCount }}</p>
                </div>
                <Activity class="w-8 h-8 text-green-600 shrink-0 ml-3" />
            </Link>
            <div
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 flex items-center justify-between">
                <div class="min-w-0">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Asesi</h3>
                    <p class="mt-1 text-3xl font-semibold text-purple-600">{{ totalAsesiCount }}</p>
                </div>
                <Users class="w-8 h-8 text-purple-600 shrink-0 ml-3" />
            </div>
            <div
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 flex items-center justify-between">
                <div class="min-w-0">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Lulusan</h3>
                    <p class="mt-1 text-3xl font-semibold text-fuchsia-600">{{ asesiLulusCount }}</p>
                </div>
                <GraduationCap class="w-8 h-8 text-fuchsia-600 shrink-0 ml-3" />
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-4">
            <div
                class="bg-white dark:bg-gray-800 p-5 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm col-span-1 lg:col-span-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <Activity class="w-5 h-5 text-gray-500" />
                    Status Pipeline Sertifikasi yang Berlangsung
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <!-- Step 1 -->
                    <div
                        class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600">
                        <p class="text-xs text-gray-500 dark:text-gray-200 uppercase font-semibold">1. Menunggu
                            Verifikasi Admin</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{
                            pipelineStats.verifikasi_berkas }}</p>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2 dark:bg-gray-700">
                            <div class="bg-blue-500 h-1.5 rounded-full"
                                :style="`width: ${pipelinePercentages.verifikasi_berkas}%`"></div>
                        </div>
                    </div>
                    <!-- Step 2 -->
                    <div
                        class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600">
                        <p class="text-xs text-gray-500 dark:text-gray-200 uppercase font-semibold">2. Asesi Harus
                            Lengkapi Berkas</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ pipelineStats.revisi_asesi
                            || '-' }}</p>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2 dark:bg-gray-700">
                            <div class="bg-yellow-500 h-1.5 rounded-full"
                                :style="`width: ${pipelinePercentages.revisi_asesi}%`"></div>
                        </div>
                    </div>
                    <!-- Step 3 -->
                    <div
                        class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600">
                        <p class="text-xs text-gray-500 dark:text-gray-200 uppercase font-semibold">3. Belum Diberikan
                            Akses Asesmen</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{
                            pipelineStats.menunggu_jadwal }}</p>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2 dark:bg-gray-700">
                            <div class="bg-purple-500 h-1.5 rounded-full"
                                :style="`width: ${pipelinePercentages.menunggu_jadwal}%`"></div>
                        </div>
                    </div>
                    <!-- Step 4 -->
                    <div
                        class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600">
                        <p class="text-xs text-gray-500 dark:text-gray-200 uppercase font-semibold">4. Menunggu Pleno
                        </p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ pipelineStats.proses_asesmen
                            || '-' }}</p>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2 dark:bg-gray-700">
                            <div class="bg-green-500 h-1.5 rounded-full"
                                :style="`width: ${pipelinePercentages.proses_asesmen}%`"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Charts Section -->
        <div v-if="!isAsesor && props.charts" class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
            <!-- Trend Chart - Full Width on Mobile, 2/3 on Desktop -->
            <div
                class="bg-white dark:bg-gray-800 p-5 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm lg:col-span-2">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Trend Pendaftaran (12 Bulan
                    Terakhir)</h3>
                <VueApexCharts :key="isDark" type="area" height="320" :options="trendOptions" :series="trendSeries" />
            </div>

            <!-- Competency Chart - 1/3 on Desktop -->
            <div class="bg-white dark:bg-gray-800 p-5 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Rasio Kelulusan</h3>
                <div class="flex items-center justify-center h-[300px]">
                    <VueApexCharts :key="isDark" type="donut" width="100%" :options="competencyOptions"
                        :series="competencySeries" />
                </div>
            </div>

            <!-- Top Schemes Chart - Full Width -->
            <div
                class="bg-white dark:bg-gray-800 p-5 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm lg:col-span-3">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Top 5 Skema Sertifikasi Paling
                    Diminati</h3>
                <VueApexCharts :key="isDark" type="bar" height="300" :options="schemeOptions" :series="schemeSeries" />
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 flex flex-col gap-2">
                <h3 class="text-xl font-medium text-gray-900 dark:text-gray-100 truncate">Manajemen Sertifikasi</h3>
                <p class="text-sm font-normal text-gray-600 dark:text-gray-500">Kelola sertifikasi yang berlangsung</p>
                <Link :href="route('admin.kelolasertifikasi.show', sert.id)"
                    class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg justify-between"
                    v-for="sert in sertificationBerlangsung">
                    <div class="">
                        <h3 class="font-medium text-gray-900 dark:text-gray-300">{{ sert.skema.nama_skema }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-500">{{ sert.asesis_count }} asesi terdaftar</p>
                    </div>
                    <div class="text-xs bg-green-100 px-2 py-1 rounded-full text-green-800">
                        {{ sert.status }}
                    </div>
                </Link>
            </div>

            <div v-if="!isAsesor"
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 flex flex-col gap-2">
                <h3 class="text-xl font-medium text-gray-800 dark:text-gray-100 truncate">Aktivitas Terbaru</h3>
                <p class="text-sm font-normal text-gray-500">Ringkasan aktivitas sistem</p>

                <div class="space-y-4 mt-2">
                    <div v-for="activity in recentActivities" :key="activity.id" class="flex items-start gap-3">
                        <div :class="['w-2 h-2 rounded-full mt-1.5 shrink-0', getActivityColor(activity.event)]"></div>
                        <div>
                            <p class="text-sm dark:text-gray-300 font-medium">
                                {{ activity.description }}
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                <span class="font-semibold text-gray-600 dark:text-gray-400">
                                    {{ activity.causer ? activity.causer.name : 'Sistem' }}
                                </span>
                                <span class="mx-1">&bull;</span>
                                <span class="capitalize">{{ formatEventName(activity.event) }}</span>
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ timeAgo(activity.created_at) }}
                            </p>
                        </div>
                    </div>
                    <div v-if="recentActivities.length === 0" class="text-center py-4 text-gray-500 text-sm">
                        Belum ada aktivitas tercatat.
                    </div>

                </div>
            </div>
        </div>
    </AdminLayout>

</template>
