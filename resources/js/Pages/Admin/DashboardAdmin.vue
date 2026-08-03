<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import CustomHeader from '@/Components/CustomHeader.vue';
import { Link } from "@inertiajs/vue3";
import { Award, Activity, Users, GraduationCap } from 'lucide-vue-next';
import { route } from 'ziggy-js';
import { computed } from 'vue';
import BaseChart from '@/Components/BaseChart.vue';
import { useDark } from '@vueuse/core';
import { useActivityLog } from "@/Composables/useActivityLog";
const { getUserLogMessage, getSkemaLogMessage, getAsesorLogMessage } = useActivityLog();

const props = defineProps({
    sertifikasiBerlangsung: Array,
    sertifikasiSelesaiCount: Number,
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

// ---------Chart.js--------

const trendData = computed(() => ({
    labels: props.charts?.monthlyStats.map(s => s.date) || [],
    datasets: [{
        label: 'Pendaftar Baru',
        data: props.charts?.monthlyStats.map(s => s.count) || [],
        borderColor: '#3b82f6',
        backgroundColor: 'rgba(59, 130, 246, 0.3)',
        fill: true,
        borderWidth: 2,
        pointRadius: 2,
        tension: 0,
    }],
}));

const trendOptions = computed(() => {
    const text = isDark.value ? '#cbd5e1' : '#64748b';
    const grid = isDark.value ? '#374151' : '#e2e8f0';

    return {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        scales: {
            x: {
                ticks: { color: text, maxRotation: -45 },
                grid: { display: false },
                border: { display: false },
            },
            y: {
                beginAtZero: true,
                ticks: { color: text, precision: 0 },
                grid: { color: grid },
                border: { display: false },
            },
        },
        plugins: {
            legend: { display: false },
            tooltip: { backgroundColor: isDark.value ? '#1f2937' : 'fff' },
        },
    };
});

// Chart 2: Top Skema (Bar Horizontal)
const schemeData = computed(() => ({
    labels: props.charts?.topSchemes.map(s => s.nama_skema) || [],
    datasets: [{
        label: 'Jumlah Pendaftar',
        data: props.charts?.topSchemes.map(s => s.total_pendaftar) || [],
        backgroundColor: ['#059669', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6'],
        borderRadius: 4,
        barThickness: 24,
    }],
}));

const schemeOptions = computed(() => {
    const text = isDark.value ? '#cbd5e1' : '#64748b';

    return {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
                beginAtZero: true,
                ticks: { color: text, precision: 0 },
                grid: { display: false },
                border: { display: false },
            },
            y: {
                ticks: { color: text },
                grid: { display: false },
                border: { display: false },
            },
        },
        plugins: {
            legend: { display: false },
            datalabels: {
                display: true,
                color: text,
                font: { size: 12, weight: 'bold' },
                formatter: (value) => value,
            },
        },
    };
});

// Chart 3: Status Kompetensi (Donut)
const competencyData = computed(() => ({
    labels: props.charts?.competencyStats.map(s => {
        const map = { kompeten: 'Kompeten', belum_kompeten: 'Belum Kompeten', diskualifikasi: 'Diskualifikasi', belum_ditetapkan: 'Belum Ditentukan' };
        return map[s.status_final] || s.status_final;
    }) || [],
    datasets: [{
        data: props.charts?.competencyStats.map(s => s.count) || [],
        backgroundColor: ['#059669', '#ef4444', '#6b7280', '#f59e0b'],
        borderColor: isDark.value ? '#1f2937' : '#ffffff',
        borderWidth: 2,
    }],
}));

const competencyOptions = computed(() => {
    const text = isDark.value ? '#cbd5e1' : '#64748b';

    return {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '65%',
        plugins: {
            legend: {position: 'bottom', labels: {color:text, usePointStyle: true}},
            datalabels: {
                display: true,
                color: '#ffffff',
                font: { size: 12, weight: 'bold' },
                formatter: (value, ctx) => {
                    if (!value) return null;
                    const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                    const pct = total ? (value / total) * 100 : 0;
                    return `${pct.toFixed(1)}%`;
                },
            },
            tooltip: {
                callbacks: {
                    label: (ctx) => {
                        const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                        const pct = total ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                        return `${ctx.label}: ${ctx.parsed} (${pct}%)`;
                    },
                },
            },
        },
    };
});

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

const getActivityMessage = (activity) => {
    return getUserLogMessage(activity)
        ?? getSkemaLogMessage(activity)
        ?? getAsesorLogMessage(activity)
        ?? activity.description;
};

// Calculate total asesi in pipeline and percentage for each stage
const pipelinePercentages = computed(() => {
    const total = props.pipelineStats.verifikasi_berkas +
        props.pipelineStats.revisi_asesi +
        props.pipelineStats.berkas_lengkap_belum_asesor +
        props.pipelineStats.ada_asesor_belum_ditetapkan;

    if (total === 0) return {
        verifikasi_berkas: 0,
        revisi_asesi: 0,
        berkas_lengkap_belum_asesor: 0,
        ada_asesor_belum_ditetapkan: 0,
    };

    return {
        verifikasi_berkas: Math.round((props.pipelineStats.verifikasi_berkas / total) * 100),
        revisi_asesi: Math.round((props.pipelineStats.revisi_asesi / total) * 100),
        berkas_lengkap_belum_asesor: Math.round((props.pipelineStats.berkas_lengkap_belum_asesor / total) * 100),
        ada_asesor_belum_ditetapkan: Math.round((props.pipelineStats.ada_asesor_belum_ditetapkan / total) * 100),
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
                    <p class="mt-1 text-3xl font-semibold text-blue-600">{{ sertifikasiBerlangsung.length }}</p>
                </div>
                <Award class="w-8 h-8 text-blue-600 shrink-0 ml-3" />
            </Link>
            <Link :href="route('admin.kelolasertifikasi.index', { tab: 'selesai' })"
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 flex items-center justify-between">
                <div class="min-w-0">
                    <h3 title="Sertifikasi Berlangsung"
                        class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Sertifikasi Selesai</h3>
                    <p class="mt-1 text-3xl font-semibold text-green-600">{{ sertifikasiSelesaiCount }}</p>
                </div>
                <Activity class="w-8 h-8 text-green-600 shrink-0 ml-3" />
            </Link>
            <div
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 flex items-center justify-between">
                <div class="min-w-0">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">{{ isAsesor ? 'Asesi Anda'
                        : 'Total Asesi' }}</h3>
                    <p class="mt-1 text-3xl font-semibold text-purple-600 dark:text-purple-300">{{ totalAsesiCount }}
                    </p>
                </div>
                <Users class="w-8 h-8 text-purple-600 dark:text-purple-300 shrink-0 ml-3" />
            </div>
            <div
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 flex items-center justify-between">
                <div class="min-w-0">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">{{ isAsesor ?
                        'Lulusan Anda' : 'Total Lulusan' }}</h3>
                    <p class="mt-1 text-3xl font-semibold text-fuchsia-600">{{ asesiLulusCount }}</p>
                </div>
                <GraduationCap class="w-8 h-8 text-fuchsia-600 shrink-0 ml-3" />
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-4">
            <div
                class="bg-white dark:bg-gray-800 p-5 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm col-span-1 lg:col-span-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <Activity class="shrink-0 w-5 h-5 text-gray-500" />
                    Status Pipeline Sertifikasi yang Berlangsung
                </h3>
                <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <!-- Step 1 -->
                    <div
                        class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600">
                        <p class="text-xs text-gray-500 dark:text-gray-200 uppercase font-semibold">1. Menunggu
                            Verifikasi Admin</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">
                            {{ pipelineStats.verifikasi_berkas ?? 0 }}</p>
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
                        <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">
                            {{ pipelineStats.revisi_asesi ?? 0 }}</p>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2 dark:bg-gray-700">
                            <div class="bg-yellow-500 h-1.5 rounded-full"
                                :style="`width: ${pipelinePercentages.revisi_asesi}%`"></div>
                        </div>
                    </div>
                    <!-- Step 3 -->
                    <div
                        class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600">
                        <p class="text-xs text-gray-500 dark:text-gray-200 uppercase font-semibold">3. Berkas sudah
                            lengkap tapi
                            belum punya asesor
                        </p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">
                            {{ pipelineStats.berkas_lengkap_belum_asesor ?? 0 }}</p>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2 dark:bg-gray-700">
                            <div class="bg-orange-500 h-1.5 rounded-full"
                                :style="`width: ${pipelinePercentages.berkas_lengkap_belum_asesor}%`"></div>
                        </div>
                    </div>
                    <!-- Step 4 -->
                    <div
                        class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600">
                        <p class="text-xs text-gray-500 dark:text-gray-200 uppercase font-semibold">4. Sudah ada asesor
                            tapi
                            status final belum ditetapkan
                        </p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">
                            {{ pipelineStats.ada_asesor_belum_ditetapkan ?? 0 }}</p>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2 dark:bg-gray-700">
                            <div class="bg-green-500 h-1.5 rounded-full"
                                :style="`width: ${pipelinePercentages.ada_asesor_belum_ditetapkan}%`"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Charts Section -->
        <div v-if="!isAsesor && props.charts" class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
            <!-- Trend Chart - Full Width on Mobile, 2/3 on Desktop -->
            <div
                class="bg-white dark:bg-gray-800 p-2 sm:p-5 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm lg:col-span-2">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Trend Pendaftaran (12 Bulan
                    Terakhir)</h3>
                <div class="overflow-x-auto pb-2">
                    <div class="min-w-125 h-80">
                        <BaseChart type="line" :data="trendData" :options="trendOptions"/>
                    </div>
                </div>
            </div>

            <!-- Competency Chart - 1/3 on Desktop -->
            <div
                class="bg-white dark:bg-gray-800 p-2 sm:p-5 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Rasio Kelulusan</h3>
                <div class="h-[300px]">
                     <BaseChart type="doughnut" :data="competencyData" :options="competencyOptions"/>
                </div>
            </div>

            <!-- Top Schemes Chart - Full Width -->
            <div
                class="bg-white dark:bg-gray-800 p-5 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm lg:col-span-3">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Top 5 Skema Sertifikasi Paling
                    Diminati</h3>
                <div class="overflow-x-auto">
                    <div class="min-w-150 h-[300px]">
                        <BaseChart type="bar" :data="schemeData" :options="schemeOptions"/>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 flex flex-col gap-2">
                <h3 class="text-xl font-medium text-gray-900 dark:text-gray-100 truncate">Manajemen Sertifikasi</h3>
                <p class="text-sm font-normal text-gray-600 dark:text-gray-500">Kelola sertifikasi yang berlangsung</p>
                <Link :href="route('admin.kelolasertifikasi.show', sert.id)"
                    class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg justify-between"
                    v-for="sert in sertifikasiBerlangsung">
                    <div class="">
                        <h3 class="font-medium text-gray-900 dark:text-gray-300">{{ sert.skema.nama_skema }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-500">{{ sert.asesi_count }} asesi terdaftar</p>
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
                                {{ activity.causer ? activity.causer.name : 'Sistem' }}
                                {{ getActivityMessage(activity) }}
                                <!-- <span v-if="activity.subject_id"
                                    class="font-mono text-xs text-gray-400 dark:text-gray-500 ml-1">
                                    (ID: {{ activity.subject_id }})
                                </span> -->
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
