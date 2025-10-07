<!-- filepath: d:\Laravel-App\lsp-untan-laravel-pwa\resources\js\Components\AdminSertifikasiMenu.vue -->
<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

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
    },
    {
        label: 'Pembayaran',
        href: route('admin.sertifikasi.payment-desc.index', props.sertificationId),
        active: route().current('admin.sertifikasi.payment-desc.index'),
    },
    {
        label: 'Pengumuman',
        href: route('admin.sertifikasi.assessment-announcement.index', props.sertificationId),
        active: route().current('admin.sertifikasi.assessment-announcement.index'),
    },
    {
        label: 'Asesmen',
        href: route('admin.sertifikasi.assessment.edit', props.sertificationId),
        active: route().current('admin.sertifikasi.assessment.edit'),
    },
    {
        label: 'Peserta',
        href: route('admin.sertifikasi.pendaftar.index', props.sertificationId),
        active: route().current('admin.sertifikasi.pendaftar.*'),
    },
    {
        label: 'Laporan',
        href: route('admin.kelolasertifikasi.report', props.sertificationId),
        active: route().current('admin.kelolasertifikasi.report'),
    },
]);
</script>

<template>
    <div>
        <div class="flex flex-wrap space-x-4 mt-1">
            <div v-for="item in menuItems" :key="item.href">
                <Link :href="item.href" class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md dark:text-white text-gray-600">
                    {{ item.label }}
                </Link>
                <div v-if="item.active" style="margin-top:-4px" class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
            </div>
        </div>
        <hr class="border-gray-200 dark:border-gray-700 mb-2">
    </div>
</template>