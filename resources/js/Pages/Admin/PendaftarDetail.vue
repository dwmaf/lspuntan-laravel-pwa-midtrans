<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AdminSertifikasiMenu from "@/Components/AdminSertifikasiMenu.vue";
import CustomHeader from '@/Components/CustomHeader.vue';
import PendaftarDetailDataStatis from "@/Pages/Admin/PendaftarDetailDataStatis.vue";
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import EditButton from "@/Components/EditButton.vue";
import SeeButton from "@/Components/SeeButton.vue";
import LoadingSpinner from "@/Components/LoadingSpinner.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import FileIcon from "@/Components/FileIcon.vue";
import SingleFileInput from "@/Components/SingleFileInput.vue";
import InputError from "@/Components/InputError.vue";
import { useForm, usePage, Link } from "@inertiajs/vue3";
import { ref, computed } from 'vue';

const props = defineProps({
    asesi: Object,
    sertification: Object,
    asesiStatusOptions: Array, 
    paymentStatusOptions: Array, 
});

const showStatusModal = ref(false);
const showPaymentModal = ref(false);
const isEditingCertificate = ref(false);

const statusForm = useForm({
    status: props.asesi.status,
    catatan_perbaikan: props.asesi.catatan_perbaikan || '',
});

const submitStatus = () => {
    statusForm.patch(route('admin.sertifikasi.pendaftar.update-status', { sert_id: props.sertification.id, asesi_id: props.asesi.id }), {
        onSuccess: () => showStatusModal.value = false,

    });
};
const cancelShowStatus = () => {
    statusForm.reset();
    showStatusModal.value = false;
};
const paymentForm = useForm({
    status: props.asesi.latest_transaction?.status || '',
});

const submitPaymentStatus = () => {
    paymentForm.patch(route('admin.sertifikasi.pendaftar.update-payment-status', { sert_id: props.sertification.id, transaction_id: props.asesi.latest_transaction.id }), {
        onSuccess: () => showPaymentModal.value = false,

    });
};
const cancelShowPayment = () => {
    paymentForm.reset();
    showPaymentModal.value = false;
};
const certificateForm = useForm({
    _method: 'PATCH',
    nomor_seri: props.asesi.sertifikat?.nomor_seri || '',
    nomor_sertifikat: props.asesi.sertifikat?.nomor_sertifikat || '',
    nomor_registrasi: props.asesi.sertifikat?.nomor_registrasi || '',
    tanggal_terbit: props.asesi.sertifikat?.tanggal_terbit || '',
    berlaku_hingga: props.asesi.sertifikat?.berlaku_hingga || '',
    file_path: null,
    delete_files: [],
});

const submitCertificate = () => {
    certificateForm.post(route('admin.sertifikasi.pendaftar.upload-certificate.update', { sert_id: props.sertification.id, asesi_id: props.asesi.id }), {
        onSuccess: () => isEditingCertificate.value = false,
    });
};
const cancelEditCertificate = () => {
    certificateForm.reset();
    isEditingCertificate.value = false;
};


const getAsesiStatusClass = (status) => {
    const classes = {
        'menunggu_verifikasi_berkas': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100',
        'perlu_perbaikan_berkas': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100',
        'ditolak': 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100',
        'dilanjutkan_asesmen': 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100',
        'lulus_sertifikasi': 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100',
    };
    return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200';
};
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
        <CustomHeader judul="Detail Peserta"/>
        <AdminSertifikasiMenu :sertification-id="props.sertification.id" />

        <!-- Tampilan Detail Utama -->
        <div v-show="!isEditingCertificate" class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Rincian Pendaftar</h3>
                <Link :href="route('admin.sertifikasi.pendaftar.index', props.sertification.id)"
                    class="text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 cursor-pointer">
                &larr; Kembali</Link>
            </div>

            <PendaftarDetailDataStatis :asesi="props.asesi" />

            <!-- Bagian E: Status -->
            <h3 class="text-md font-semibold dark:text-gray-300 mb-2 border-b pb-1 border-gray-700 mt-6">E. Status</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                <div>
                    <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Status Asesi</dt>
                    <dd class="mt-1 text-sm flex flex-wrap items-center gap-2">
                        <span
                            :class="['px-2 py-1 text-xs leading-5 font-semibold rounded-full', getAsesiStatusClass(asesi.status)]">
                            {{ asesi.status.replace(/_/g, ' ') }}
                        </span>
                        <EditButton @click="showStatusModal = true">Ubah Status</EditButton>
                    </dd>
                </div>
                <div>
                    <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Status Pembayaran</dt>
                    <dd class="mt-1 text-sm flex flex-wrap items-center gap-2">
                        <span
                            :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getPaymentStatusInfo(asesi.latest_transaction).class]">
                            {{ getPaymentStatusInfo(asesi.latest_transaction).text }}
                        </span>
                        <EditButton v-if="props.asesi.latest_transaction" @click="showPaymentModal = true">Ubah Status
                        </EditButton>
                        <a v-if="props.asesi.latest_transaction"
                            :href="`/storage/${props.asesi.latest_transaction.bukti_bayar}`"
                            class="flex items-center gap-2 group min-w-0">
                            <FileIcon :path="props.asesi.latest_transaction.bukti_bayar" />
                            <span class="text-blue-500 group-hover:text-blue-700 truncate group-hover:underline">
                                {{ props.asesi.latest_transaction.bukti_bayar.split('/').pop() }}</span>
                        </a>
                    </dd>
                </div>
            </div>


            <h3 class="text-md font-semibold dark:text-gray-300 mb-2 border-b pb-1 border-gray-700 mt-6">F. Sertifikat
            </h3>
            <div class="mt-2">
                <dt v-if="!asesi.sertifikat" class="block text-sm font-medium text-gray-600 dark:text-gray-400">
                    Sertifikat bisa
                    diupload jika status asesi adalah 'lulus sertifikasi'</dt>
                <EditButton v-if="props.asesi.sertifikat" @click="isEditingCertificate = true">Ubah Data Sertifikat
                </EditButton>
                <SeeButton v-else-if="props.asesi.status === 'lulus_sertifikasi'" @click="isEditingCertificate = true">
                    Upload Sertifikat</SeeButton>
                <a v-if="props.asesi.sertifikat" :href="`/storage/${props.asesi.sertifikat.file_path}`" target="_blank"
                    class="text-sm text-blue-500 hover:text-blue-700">
                    Lihat Sertifikat
                </a>
            </div>
        </div>

        <!-- Form Edit Sertifikat -->
        <div v-show="isEditingCertificate" class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                {{ props.asesi.sertifikat ? 'Ubah Data Sertifikat' : 'Upload Sertifikat' }}
            </h3>
            <p class="my-1 text-sm text-gray-600 dark:text-gray-400">Untuk: <span class="font-semibold">{{
                props.asesi.student.user.name }}</span></p>
            <form @submit.prevent="submitCertificate" class="flex flex-col gap-4 mt-4">
                <div>
                    <InputLabel value="Nomor Seri" />
                    <TextInput v-model="certificateForm.nomor_seri" type="text" required />
                    <InputError :message="certificateForm.errors.nomor_seri" />
                </div>
                <div>
                    <InputLabel value="Nomor Sertifikat" />
                    <TextInput v-model="certificateForm.nomor_sertifikat" type="text" required />
                    <InputError :message="certificateForm.errors.nomor_sertifikat" />
                </div>
                <div>
                    <InputLabel value="Nomor Registrasi" />
                    <TextInput v-model="certificateForm.nomor_sertifikat" type="text" required />
                    <InputError :message="certificateForm.errors.nomor_sertifikat" />
                </div>
                <div>
                    <InputLabel value="Tanggal Terbit" />
                    <TextInput v-model="certificateForm.tanggal_terbit" type="date" required />
                    <InputError :message="certificateForm.errors.tanggal_terbit" />
                </div>
                <div>
                    <InputLabel value="Berlaku Hingga" />
                    <TextInput v-model="certificateForm.berlaku_hingga" type="date" required />
                    <InputError :message="certificateForm.errors.berlaku_hingga" />
                </div>
                <SingleFileInput v-model="certificateForm.file_path" 
                    v-model:deleteList="certificateForm.delete_files"
                    delete-identifier="file_path" label="File Sertifikat" is-label-required
                    :existing-file-url="asesi?.sertifikat?.file_path ? `/storage/${asesi.sertifikat.file_path}` : null"
                    :is-marked-for-deletion="certificateForm.delete_files.includes('file_path')"
                    accept=".pdf,.jpg,.jpeg,.png" :error="certificateForm.errors.file_path"
                    @remove="removeFileSertifikat('file_path')"
                    :required="!asesi?.sertifikat?.file_path || certificateForm.delete_files.includes('file_path')" />
                <div class="flex gap-2 items-center">
                    <PrimaryButton :disabled="certificateForm.processing">Simpan</PrimaryButton>
                    <SecondaryButton type="button" @click="cancelEditCertificate">Batal</SecondaryButton>
                </div>
            </form>
        </div>

        <!-- Modal Ubah Status Asesi -->
        <Modal :show="showStatusModal" @close="showStatusModal = false">
            <div class="p-4">
                <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-6">Konfirmasi Ubah Status Asesi
                </h3>
                <form @submit.prevent="submitStatus" class="flex flex-col gap-4 mt-1">
                    <div>
                        <InputLabel value="Status Asesi" required />
                        <select v-model="statusForm.status"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option v-for="option in asesiStatusOptions" :key="option.value" :value="option.value" class="capitalize">
                                {{ option.label }}
                            </option>
                        </select>
                        <InputError :message="statusForm.errors.status" />
                    </div>
                    <div v-if="statusForm.status === 'perlu_perbaikan_berkas'">
                        <InputLabel value="Catatan Perbaikan" />
                        <textarea v-model="statusForm.catatan_perbaikan"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            rows="3" required></textarea>
                        <InputError :message="statusForm.errors.catatan_perbaikan" />
                    </div>
                    <div class="flex items-center gap-3 justify-end">
                        <SecondaryButton @click="cancelShowStatus">Batal</SecondaryButton>
                        <PrimaryButton :disabled="statusForm.processing">Simpan</PrimaryButton>
                        <LoadingSpinner v-if="statusForm.processing" />
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Modal Ubah Status Pembayaran -->
        <Modal :show="showPaymentModal" @close="showPaymentModal = false">
            <div class="p-4">
                <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-6">Konfirmasi Ubah Status Pembayaran
                    Asesi
                </h3>
                <form @submit.prevent="submitPaymentStatus" class="flex flex-col gap-4 mt-1">
                    <div>
                        <InputLabel value="Status Pembayaran Asesi" />
                        <select v-model="paymentForm.status"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option v-for="option in paymentStatusOptions" :key="option.value" :value="option.value" class="capitalize">
                                {{ option.label }}
                            </option>
                        </select>
                        <InputError :message="paymentForm.errors.status" />
                    </div>
                    <div class="flex gap-3 items-center justify-end">
                        <SecondaryButton @click="cancelShowPayment">Batal</SecondaryButton>
                        <PrimaryButton :disabled="paymentForm.processing">Simpan</PrimaryButton>
                        <LoadingSpinner v-if="paymentForm.processing" />
                    </div>
                </form>
            </div>
        </Modal>

    </AdminLayout>
</template>