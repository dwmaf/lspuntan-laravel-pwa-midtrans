<script setup>
import { Users } from "lucide-vue-next";
import PrimaryLinkButton from "@/Components/PrimaryLinkButton.vue";

const props = defineProps({
    sertifications: {
        type: Array,
        required: true
    },
    emptyMessage: {
        type: String,
        default: 'Tidak ada data sertifikasi.'
    }
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value);
};
</script>

<template>
    <div class="p-6 bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden">
        <slot name="filter"></slot>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Skema Sertifikasi
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Asesor
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Tanggal Pendaftaran
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            TUK
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Biaya
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Asesi
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800">
                    <tr v-if="sertifications.length > 0" v-for="sert in sertifications" :key="sert.id" :class="[
                        'hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors border-b border-gray-200 dark:border-gray-700 last:border-0',
                        { 'bg-red-50 dark:bg-red-900/10': sert.status === 'dibatalkan' }
                    ]">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                            <div>{{ sert.skema.nama_skema }}</div>
                            <div v-if="sert.status === 'dibatalkan'" class="mt-1">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    Dibatalkan
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            <div v-if="sert.asesors?.length" class="flex flex-wrap gap-1">
                                <div v-for="asesor in sert.asesors" :key="asesor.id" class="inline-block rounded bg-gray-200 dark:bg-gray-700 px-2 py-1 text-xs font-medium">
                                    {{ asesor.user.name }}
                                </div>
                            </div>
                            <span v-else class="text-gray-400 italic">-</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                            {{ formatDate(sert.tgl_apply_dibuka) }} - {{ formatDate(sert.tgl_apply_ditutup) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            <span :class="{ 'text-gray-400 italic': !sert.tuk }">
                                {{ sert.tuk ?? 'Belum Ditentukan' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                            {{ formatCurrency(sert.biaya) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-800 text-xs font-semibold">
                                {{ sert.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-800 text-xs font-semibold">
                                <Users class="w-3.5 h-3.5" />
                                {{ sert.asesis_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <PrimaryLinkButton :href="route('admin.kelolasertifikasi.show', sert.id)">
                                Detail
                            </PrimaryLinkButton>
                        </td>
                    </tr>
                    <tr v-else>
                        <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ emptyMessage }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <slot name="pagination"></slot>
    </div>
</template>
