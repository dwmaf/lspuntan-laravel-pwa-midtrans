<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AdminSertifikasiMenu from "@/Components/AdminSertifikasiMenu.vue";
import CustomHeader from '@/Components/CustomHeader.vue';
import TextInput from '@/Components/Input/TextInput.vue';
import SmallLinkButton from "@/Components/SmallLinkButton.vue";
import { MoveRight, FunnelIcon, X } from 'lucide-vue-next';
import { ref, computed, watch, reactive } from 'vue';
import PrimaryButton from '@/Components/Button/PrimaryButton.vue';
import SecondaryButton from '@/Components/Button/SecondaryButton.vue';
import SelectInput from '@/Components/Input/SelectInput.vue';
import InputLabel from '@/Components/Input/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
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

const searchQuery = ref('');
const showFilterModal = ref(false);

const asesiStatusOptions = [
    { value: 'menunggu_verifikasi_berkas', text: 'Menunggu Verifikasi Berkas' },
    { value: 'perlu_perbaikan_berkas', text: 'Perlu Perbaikan Berkas' },
    { value: 'ditolak', text: 'Ditolak' },
    { value: 'dilanjutkan_asesmen', text: 'Dilanjutkan Asesmen' },
    { value: 'lulus_sertifikasi', text: 'Lulus Sertifikasi' },
    { value: 'tidak_lulus', text: 'Tidak Lulus' },
];

const transactionStatusOptions = [
    { value: 'pending', text: 'Pending (Menunggu Verifikasi)' },
    { value: 'bukti_pembayaran_terverifikasi', text: 'Terverifikasi' },
    { value: 'bukti_pembayaran_ditolak', text: 'Ditolak' },
    { value: 'perlu_perbaikan_bukti_bayar', text: 'Perlu Perbaikan' },
];

const filtersForm = ref({
    asesiStatus: '',
    transactionStatus: '',
});

const activeFilters = ref({
    asesiStatus: '',
    transactionStatus: '',
});

const applyFilters = () => {
    activeFilters.value = { ...filtersForm.value };
    showFilterModal.value = false;
};

const resetFilters = () => {
    filtersForm.value = { asesiStatus: '', transactionStatus: '' };
    activeFilters.value = { asesiStatus: '', transactionStatus: '' };
    // showFilterModal.value = false; // Optional: close on reset or keep open
};

const closeFilterModal = () => {
    showFilterModal.value = false;
    // Reset form to currently active filters to discard unsaved changes
    filtersForm.value = { ...activeFilters.value };
};

const filteredAsesis = computed(() => {
    let result = props.sertification.asesis;

    // Filter by Search Query (Name or Email)
    if (searchQuery.value) {
        const lower = searchQuery.value.toLowerCase();
        result = result.filter(asesi =>
            (asesi.student?.user?.name?.toLowerCase() || '').includes(lower) ||
            (asesi.student?.user?.email?.toLowerCase() || '').includes(lower)
        );
    }

    // Filter by Asesi Status
    if (activeFilters.value.asesiStatus) {
        result = result.filter(asesi => asesi.status === activeFilters.value.asesiStatus);
    }

    // Filter by Transaction Status
    if (activeFilters.value.transactionStatus) {
        result = result.filter(asesi =>
            asesi.latest_transaction?.status === activeFilters.value.transactionStatus
        );
    }

    return result;
});


</script>
<template>
    <AdminLayout>
        <CustomHeader judul="Daftar Peserta" />
        <AdminSertifikasiMenu :sertification-id="props.sertification.id" />

        <div class="p-6 bg-white dark:bg-gray-800 shadow-xl rounded-lg ">
            <div class="flex justify-end items-center gap-2 mb-4">
                <div class="w-[243px]">
                    <TextInput v-model="searchQuery" type="text" placeholder="Cari nama atau email..." />
                </div>
                <button @click="showFilterModal = true"
                    class="relative mt-1 inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-500 text-sm font-medium rounded-md text-gray-500 dark:text-gray-300 bg-white dark:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none transition ease-in-out duration-150">
                    <FunnelIcon class="w-4" />
                    <span v-if="activeFilters.asesiStatus || activeFilters.transactionStatus"
                        class="absolute -top-1 -right-1 h-2 w-2 bg-blue-500 rounded-full"></span>
                </button>
            </div>
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

                        <tr v-if="sertification.asesis.length > 0" v-for="(asesi, index) in filteredAsesis"
                            :key="asesi.id" class="">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ index + 1 }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ asesi.student.user.name ?? 'Nama Tidak Tersedia' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span
                                    :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getAsesiStatusClass(asesi.status)]">
                                    {{ asesi.status.replace(/_/g, ' ') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span
                                    :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getPaymentStatusInfo(asesi.latest_transaction).class]">
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
        <Modal :show="showFilterModal" @close="showFilterModal = false">
            <div class="flex justify-end p-2">
                <button @click="closeFilterModal">
                    <X class="w-4 dark:text-white" />
                </button>
            </div>
            <div class="p-6">
                <div class="flex flex-col gap-4">
                    <div>
                        <InputLabel value="Status Asesi" />
                        <SelectInput v-model="filtersForm.asesiStatus"
                            :options="[{ value: '', text: 'Semua' }, ...asesiStatusOptions]" />
                    </div>
                    <div>
                        <InputLabel value="Status Pembayaran Asesi" />
                        <SelectInput v-model="filtersForm.transactionStatus"
                            :options="[{ value: '', text: 'Semua' }, ...transactionStatusOptions]" />
                    </div>
                </div>
                <div class="my-4 border-t border-gray-200 dark:border-gray-600"></div>
                <div class=" flex gap-3">
                    <SecondaryButton @click="resetFilters"> Reset </SecondaryButton>
                    <PrimaryButton @click="applyFilters">Apply Filter</PrimaryButton>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>