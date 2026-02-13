<script setup>
import { computed } from "vue";
import PrimaryLinkButton from "@/Components/PrimaryLinkButton.vue";
import StatusBadge from "@/Components/StatusBadge.vue";
import { useFormat } from "@/Composables/useFormat";

const props = defineProps({
    sertifications: {
        type: Array,
        required: true
    },
    asesis: {
        type: Object,
        default: () => ({})
    },
    emptyMessage: {
        type: String,
        default: 'Anda belum mendaftar di sertifikasi manapun.'
    }
});

const { formatCurrency, formatDate } = useFormat();

const getEnrollment = (sertId) => {
    return props.asesis[sertId];
};
</script>

<template>
    <div
        class="bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden border border-gray-100 dark:border-gray-700">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Skema Sertifikasi
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Jadwal Pendaftaran
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
                            Status
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-if="sertifications.length > 0" v-for="sert in sertifications" :key="sert.id"
                        class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ sert.skema?.nama_skema }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                            {{ formatDate(sert.tgl_apply_dibuka, 'short') }} - <br>
                            {{ formatDate(sert.tgl_apply_ditutup, 'short') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            <span :class="{ 'text-gray-400 italic': !sert.tuk }">
                                {{ sert.tuk || 'Belum Ditentukan' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                            {{ formatCurrency(sert.biaya) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <StatusBadge v-if="sert.status === 'selesai'" variant="primary" class="mr-2">
                                Selesai
                            </StatusBadge>
                            <StatusBadge v-else-if="sert.status === 'berlangsung'" variant="success" class="mr-2">
                                Berlangsung
                            </StatusBadge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <PrimaryLinkButton v-if="getEnrollment(sert.id)"
                                :href="route('asesi.sertifikasi.applied.show', { sertification: sert.id, asesi: getEnrollment(sert.id).id })"
                                class="w-full justify-center">
                                Lihat Status
                            </PrimaryLinkButton>
                            <span v-else class="text-gray-400 italic text-xs">
                                Belum Terdaftar
                            </span>
                        </td>
                    </tr>
                    <tr v-else>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ emptyMessage }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
