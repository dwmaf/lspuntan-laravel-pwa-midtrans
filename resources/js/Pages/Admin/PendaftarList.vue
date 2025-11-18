<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AdminSertifikasiMenu from "@/Components/AdminSertifikasiMenu.vue";
import CustomHeader from '@/Components/CustomHeader.vue';
import SmallLinkButton from "@/Components/SmallLinkButton.vue";
const props = defineProps({
    sertification: Object,
});
const getAsesiStatusClass = (status) => {
    const classes = {
        'daftar': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100',
        'perlu_perbaikan_berkas': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100',
        'ditolak': 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100',
        'dilanjutkan_asesmen': 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100',
        'lulus_sertifikasi': 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100',
    };
    return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200';
};

// Helper untuk status pembayaran
const getPaymentStatusInfo = (transaction) => {
    if (!transaction) {
        return { text: 'Belum Submit Bukti Pembayaran', class: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100' };
    }
    if (transaction.status === 'belum bayar') {
        return { text: 'Belum Bayar', class: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100' };
    }
    if (transaction.tipe === 'manual' && transaction.bukti_bayar && transaction.status === 'pending') {
        return { text: 'Menunggu Verifikasi', class: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100' };
    }
    if (transaction.tipe === 'manual' && transaction.status === 'bukti_pembayaran_terverifikasi') {
        return { text: 'Pembayaran Terverifikasi', class: 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100' };
    }
    if (transaction.tipe === 'manual' && transaction.status === 'bukti_pembayaran_ditolak') {
        return { text: 'Bukti Pembayaran Ditolak', class: 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100' };
    }
    return { text: 'N/A', class: 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200' };
};
</script>
<template>
    <AdminLayout>
        
        <CustomHeader judul="Daftar Peserta"/>
        <AdminSertifikasiMenu :sertification-id="props.sertification.id" />

        <div class="p-6 bg-white dark:bg-gray-800 shadow-xl rounded-lg ">
            <div class="px-6 py-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                    Daftar Pendaftar Skema: {{ props.sertification.skema.nama_skema ?? 'Tidak Diketahui' }}
                </h3>
            </div>
            <!-- {{ props.sertification.asesis }} -->
            <div class="overflow-x-auto">
                <table class="min-w-full ">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                No
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Nama Asesi
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status Asesi
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status Pembayaran
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Aksi
                            </th>

                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 ">
                        
                        <tr v-if="props.sertification.asesis.length > 0" v-for="(asesi, index) in props.sertification.asesis" :key="asesi.id" class="">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ index + 1 }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ asesi.student.user.name ?? 'Nama Tidak Tersedia' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getAsesiStatusClass(asesi.status)]">
                                    {{ asesi.status.replace(/_/g, ' ') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getPaymentStatusInfo(asesi.latest_transaction).class]">
                                    {{ getPaymentStatusInfo(asesi.latest_transaction).text }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <SmallLinkButton
                                    :href="route('admin.sertifikasi.pendaftar.show', [props.sertification.id, asesi.id])">
                                    Detail
                                </SmallLinkButton>
                            </td>
                        </tr>
                        <tr v-else>
                            <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                Tidak ada data pendaftar untuk skema ini.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>

</template>