<!-- filepath: d:\Laravel-App\lsp-untan-laravel-pwa\resources\js\Components\AdminSertifikasiMenu.vue -->
<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    IconInfoCircle,
    IconReceipt,
    IconSpeakerphone,
    IconChecklist,
    IconUsers,
    IconChartBar
} from '@tabler/icons-vue';

const props = defineProps({
    sertificationId: {
        type: Number,
        required: true,
    },
});

const menuItems = computed(() => [
    {
        label: 'Detail',
        href: route('admin.kelolasertifikasi.show', props.sertificationId),
        active: route().current('admin.kelolasertifikasi.show'),
        icon: IconInfoCircle,
    },
    {
        label: 'Pembayaran',
        href: route('admin.sertifikasi.payment-desc.index', props.sertificationId),
        active: route().current('admin.sertifikasi.payment-desc.index'),
        icon: IconReceipt,
    },
    {
        label: 'Pengumuman',
        href: route('admin.sertifikasi.assessment-announcement.index', props.sertificationId),
        active: route().current('admin.sertifikasi.assessment-announcement.index'),
        icon: IconSpeakerphone,
    },
    {
        label: 'Asesmen',
        href: route('admin.sertifikasi.assessment.edit', props.sertificationId),
        active: route().current('admin.sertifikasi.assessment.edit'),
        icon: IconChecklist,
    },
    {
        label: 'Peserta',
        href: route('admin.sertifikasi.pendaftar.index', props.sertificationId),
        active: route().current('admin.sertifikasi.pendaftar.*'),
        icon: IconUsers,
    },
    {
        label: 'Laporan',
        href: route('admin.kelolasertifikasi.report', props.sertificationId),
        active: route().current('admin.kelolasertifikasi.report'),
        icon: IconChartBar,
    },
]);
</script>

<template>
    <div>
        <div class="flex flex-wrap gap-4 mt-1">
            <div v-for="item in menuItems" :key="item.href">
                <Link :href="item.href"
                    class="flex items-center gap-2 px-4 py-3 font-semibold text-sm hover:bg-gray-300 hover:dark:bg-gray-700 rounded-t-md dark:text-white text-gray-600">
                <component v-if="item.icon" :is="item.icon" class="text-gray-700 dark:text-gray-200" :size="20"
                    stroke-width="2" />
                {{ item.label }}
                </Link>
                <div v-if="item.active" style="margin-top:-4px"
                    class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
            </div>
        </div>
        <hr class="border-gray-200 dark:border-gray-700 mb-2">
    </div>
</template>