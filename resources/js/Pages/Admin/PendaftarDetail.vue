<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AdminSertifikasiMenu from "@/Components/AdminSertifikasiMenu.vue";
import CustomHeader from '@/Components/CustomHeader.vue';
import PendaftarDetailDataStatis from "@/Pages/Admin/PendaftarDetailDataStatis.vue";
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue";
import EditButton from "@/Components/Button/EditButton.vue";
import SeeButton from "@/Components/Button/SeeButton.vue";
import LoadingSpinner from "@/Components/LoadingSpinner.vue";
import InputLabel from "@/Components/Input/InputLabel.vue";
import TextInput from "@/Components/Input/TextInput.vue";
import SelectInput from "@/Components/Input/SelectInput.vue";
import TextareaInput from "@/Components/Input/TextareaInput.vue";
import FileIcon from "@/Components/FileIcon.vue";
import SingleFileInput from "@/Components/Input/SingleFileInput.vue";
import InputError from "@/Components/Input/InputError.vue";
import { useForm, usePage, Link } from "@inertiajs/vue3";
import { ref, computed } from 'vue';

const props = defineProps({
    asesi: Object,
    sertification: Object,
    statusAksesMenuAsesmenOptions: Array,
    statusBerkasAdministrasiOptions: Array,
    StatusFinalAsesiOptions: Array,
});

const showStatusModal = ref(false);
const modalType = ref(''); // 'berkas', 'akses', 'final'
const isEditingAsesi = ref(false);
const isEditingCertificate = ref(false);

const asesiForm = useForm({
    _method: 'PATCH',
    apl_1: null,
    apl_2: null,
    delete_files_asesi: [],
});

const statusForm = useForm({
    status_berkas: props.asesi.status_berkas,
    catatan_perbaikan: props.asesi.catatan_perbaikan || '',
    status_akses_asesmen: props.asesi.status_akses_asesmen,
    status_final: props.asesi.status_final,
});

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

const openModal = (type) => {
    modalType.value = type;
    statusForm.reset();
    // Re-sync values from props to ensure form is clean and accurate
    statusForm.status_berkas = props.asesi.status_berkas;
    statusForm.catatan_perbaikan = props.asesi.catatan_perbaikan || '';
    statusForm.status_akses_asesmen = props.asesi.status_akses_asesmen;
    statusForm.status_final = props.asesi.status_final;
    showStatusModal.value = true;
};

const closeModal = () => {
    statusForm.reset();
    showStatusModal.value = false;
};

const cancelEditAsesi = () => {
    asesiForm.reset();
    isEditingAsesi.value = false;
};

const cancelEditCertificate = () => {
    certificateForm.reset();
    isEditingCertificate.value = false;
};

const submitStatusUpdate = () => {
    let routeName = '';
    if (modalType.value === 'berkas') routeName = 'admin.sertifikasi.pendaftar.update-status-berkas';
    if (modalType.value === 'akses') routeName = 'admin.sertifikasi.pendaftar.update-akses-asesmen';
    if (modalType.value === 'final') routeName = 'admin.sertifikasi.pendaftar.update-status-final';

    statusForm.patch(route(routeName, { sertification: props.sertification.id, asesi: props.asesi.id }), {
        onSuccess: () => closeModal(),
    });
};

const submitCertificate = () => {
    certificateForm.post(route('admin.sertifikasi.pendaftar.update-certificate', { sertification: props.sertification.id, asesi: props.asesi.id }), {
        onSuccess: () => isEditingCertificate.value = false,
    });
};


const getStatusBerkasAdministrasi = (status) => {
    const data = {
        'menunggu_verifikasi_admin': {
            class: 'bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100',
            text: 'Menunggu Verifikasi Admin'
        },
        'perlu_perbaikan_berkas': {
            class: 'bg-amber-100 text-amber-800 dark:bg-amber-700 dark:text-amber-100',
            text: 'Perlu Perbaikan Berkas'
        },
        'sudah_lengkap': {
            class: 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100',
            text: 'Sudah Lengkap'
        },
    };
    return data[status] || {
        class: 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200',
        text: status
    };
};

const getStatusAksesMenuAsesmen = (status) => {
    const data = {
        'belum_diberikan': {
            class: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100',
            text: 'Belum Diberikan'
        },
        'diberikan': {
            class: 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100',
            text: 'Diberikan'
        },
    };
    return data[status] || {
        class: 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200',
        text: status
    };
};

const getStatusFinalAsesi = (status) => {
    const data = {
        'belum_ditetapkan': {
            class: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100',
            text: 'Belum Ditetapkan'
        },
        'belum_kompeten': {
            class: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100',
            text: 'Belum Kompeten'
        },
        'kompeten': {
            class: 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100',
            text: 'Kompeten'
        },
        'diskualifikasi': {
            class: 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100',
            text: 'Diskualifikasi'
        },
    };
    return data[status] || {
        class: 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200',
        text: status
    };
};

</script>

<template>
    <AdminLayout>
        <CustomHeader :judul="`${sertification.skema.nama_skema}: Detail Peserta`" />
        <AdminSertifikasiMenu :sertification-id="props.sertification.id" />

        <!-- Tampilan Detail Utama -->
        <div v-show="!isEditingCertificate && !isEditingAsesi"
            class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
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
                    <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Status Berkas Administrasi
                        Asesi</dt>
                    <dd class="mt-1 text-sm flex flex-wrap items-center gap-2">
                        <span
                            :class="['px-2 py-1 text-xs leading-5 font-semibold rounded-full', getStatusBerkasAdministrasi(asesi.status_berkas).class]">
                            {{ getStatusBerkasAdministrasi(asesi.status_berkas).text }}
                        </span>
                        <EditButton @click="openModal('berkas')">Ubah Status</EditButton>
                    </dd>
                </div>
                <div>
                    <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Hak Akses Menu Asesmen</dt>
                    <dd class="mt-1 text-sm flex flex-wrap items-center gap-2">
                        <span
                            :class="['px-2 py-1 text-xs leading-5 font-semibold rounded-full', getStatusAksesMenuAsesmen(asesi.status_akses_asesmen).class]">
                            {{ getStatusAksesMenuAsesmen(asesi.status_akses_asesmen).text }}
                        </span>
                        <EditButton @click="openModal('akses')">Ubah Status</EditButton>
                    </dd>
                </div>
                <div>
                    <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Status Akhir Asesi</dt>
                    <dd class="mt-1 text-sm flex flex-wrap items-center gap-2">
                        <span
                            :class="['px-2 py-1 text-xs leading-5 font-semibold rounded-full', getStatusFinalAsesi(asesi.status_final).class]">
                            {{ getStatusFinalAsesi(asesi.status_final).text }}
                        </span>
                        <EditButton @click="openModal('final')">Ubah Status</EditButton>
                    </dd>
                </div>
            </div>


            <h3 class="text-md font-semibold dark:text-gray-300 mb-2 border-b pb-1 border-gray-700 mt-6">F. Sertifikat
            </h3>
            <div class="mt-2">
                <dt v-if="!asesi.sertifikat" class="block text-sm font-medium text-gray-600 dark:text-gray-400">
                    Sertifikat bisa
                    diupload jika status akhir asesi adalah Kompeten</dt>
                <EditButton v-if="props.asesi.sertifikat" @click="isEditingCertificate = true">Ubah Data Sertifikat
                </EditButton>
                <SeeButton class="mt-1" v-else-if="props.asesi.status_final === 'kompeten'"
                    @click="isEditingCertificate = true">
                    Upload Sertifikat</SeeButton>
                <a v-if="props.asesi.sertifikat" :href="`/storage/${props.asesi.sertifikat.file_path}`" target="_blank"
                    class="text-sm text-blue-500 hover:text-blue-700 mt-1">
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
                <TextInput id="nomor_seri" label="Nomor Seri" v-model="certificateForm.nomor_seri" type="text" required
                    :error="certificateForm.errors.nomor_seri" />
                <TextInput id="nomor_sertifikat" label="Nomor Sertifikat" v-model="certificateForm.nomor_sertifikat"
                    type="text" required :error="certificateForm.errors.nomor_sertifikat" />
                <TextInput id="nomor_registrasi" label="Nomor Registrasi" v-model="certificateForm.nomor_registrasi"
                    type="text" required :error="certificateForm.errors.nomor_registrasi" />
                <TextInput id="tanggal_terbit" label="Tanggal Terbit" v-model="certificateForm.tanggal_terbit"
                    type="date" required :error="certificateForm.errors.tanggal_terbit" />
                <TextInput id="berlaku_hingga" label="Berlaku Hingga" v-model="certificateForm.berlaku_hingga"
                    type="date" required :error="certificateForm.errors.berlaku_hingga" />
                <SingleFileInput v-model="certificateForm.file_path" v-model:deleteList="certificateForm.delete_files"
                    delete-identifier="file_path" label="File Sertifikat" is-label-required
                    :existing-file-url="asesi?.sertifikat?.file_path ? `/storage/${asesi.sertifikat.file_path}` : null"
                    :is-marked-for-deletion="certificateForm.delete_files.includes('file_path')"
                    accept=".pdf,.jpg,.jpeg,.png" :error="certificateForm.errors.file_path"
                    :required="!asesi?.sertifikat?.file_path || certificateForm.delete_files.includes('file_path')" />
                <div class="flex gap-2 items-center">
                    <PrimaryButton :disabled="certificateForm.processing">Simpan</PrimaryButton>
                    <SecondaryButton type="button" @click="cancelEditCertificate">Batal</SecondaryButton>
                </div>
            </form>
        </div>

        <!-- Modal Ubah Status (Unified) -->
        <Modal :show="showStatusModal" @close="closeModal">
            <div class="p-4">
                <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-6">
                    {{ modalType === 'berkas' ? 'Konfirmasi Ubah Status Berkas' :
                        modalType === 'akses' ? 'Konfirmasi Ubah Akses Menu Asesmen' :
                            'Konfirmasi Ubah Status Akhir Asesi' }}
                </h3>

                <form @submit.prevent="submitStatusUpdate" class="flex flex-col gap-4 mt-1">
                    <template v-if="modalType === 'berkas'">
                        <SelectInput id="status_berkas" label="Status Berkas Asesi" v-model="statusForm.status_berkas"
                            :options="statusBerkasAdministrasiOptions" :error="statusForm.errors.status_berkas"
                            required />
                        <TextareaInput v-if="statusForm.status_berkas === 'perlu_perbaikan_berkas'"
                            id="catatan_perbaikan" label="Catatan Perbaikan" v-model="statusForm.catatan_perbaikan"
                            required :error="statusForm.errors.catatan_perbaikan" rows="3" />
                    </template>

                    <template v-else-if="modalType === 'akses'">
                        <SelectInput id="status_akses_asesmen" label="Hak Akses Asesi ke Menu Asesmen"
                            v-model="statusForm.status_akses_asesmen" :options="statusAksesMenuAsesmenOptions"
                            :error="statusForm.errors.status_akses_asesmen" required />
                    </template>

                    <template v-else-if="modalType === 'final'">
                        <SelectInput id="status_final" label="Status Akhir Asesi" v-model="statusForm.status_final"
                            :options="StatusFinalAsesiOptions" :error="statusForm.errors.status_final" required />
                    </template>

                    <div class="flex items-center gap-3 justify-end mt-4">
                        <SecondaryButton type="button" @click="closeModal">Batal</SecondaryButton>
                        <PrimaryButton :disabled="statusForm.processing">Simpan</PrimaryButton>
                        <LoadingSpinner v-if="statusForm.processing" />
                    </div>
                </form>
            </div>
        </Modal>

    </AdminLayout>
</template>