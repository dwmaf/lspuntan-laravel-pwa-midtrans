<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AdminSertifikasiMenu from "@/Components/AdminSertifikasiMenu.vue";
import CustomHeader from '@/Components/CustomHeader.vue';
import PendaftarDetailDataStatis from "@/Pages/Admin/PendaftarDetailDataStatis.vue";
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue";
import DangerButton from "@/Components/Button/DangerButton.vue";
import SecondaryLinkButton from "@/Components/SecondaryLinkButton.vue";
import EditButton from "@/Components/Button/EditButton.vue";
import SeeButton from "@/Components/Button/SeeButton.vue";
import LoadingSpinner from "@/Components/LoadingSpinner.vue";
import StatusBadge from "@/Components/StatusBadge.vue";
import TextInput from "@/Components/Input/TextInput.vue";
import SelectInput from "@/Components/Input/SelectInput.vue";
import TextareaInput from "@/Components/Input/TextareaInput.vue";
import FileIcon from "@/Components/FileIcon.vue";
import BackButton from "@/Components/Button/BackButton.vue";
import FileCard from "@/Components/FileCard.vue";
import SingleFileInput from "@/Components/Input/SingleFileInput.vue";
import { Award } from 'lucide-vue-next';
import { useForm, usePage, Link, router } from "@inertiajs/vue3";
import { ref, computed } from 'vue';

const props = defineProps({
    asesi: Object,
    sertification: Object,
    statusAksesMenuAsesmenOptions: Array,
    statusBerkasAdministrasiOptions: Array,
    StatusFinalAsesiOptions: Array,
    canManageCertificate: Boolean,
});

const showStatusModal = ref(false);
const modalType = ref(''); // 'berkas', 'akses', 'final'
const isEditingAsesi = ref(false);
const isEditingCertificate = ref(false);

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

const deleteCertificate = () => {
    if (confirm('Apakah Anda yakin ingin menghapus sertifikat ini secara permanen?')) {
        router.delete(route('admin.sertifikasi.pendaftar.destroy-certificate', {
            sertification: props.sertification.id,
            asesi: props.asesi.id
        }), {
            onSuccess: () => {
                certificateForm.defaults({
                    nomor_seri: '',
                    nomor_sertifikat: '',
                    nomor_registrasi: '',
                    tanggal_terbit: '',
                    berlaku_hingga: '',
                    file_path: null,
                    delete_files: [],
                });
                certificateForm.reset();
                isEditingCertificate.value = false;
            },
            preserveScroll: true
        });
    }
};

const getStatusBerkasAdministrasi = (status) => {
    const data = {
        'menunggu_verifikasi_admin': {
            variant: 'primary',
            text: 'Menunggu Verifikasi Admin'
        },
        'perlu_perbaikan_berkas': {
            variant: 'warning',
            text: 'Perlu Perbaikan Berkas'
        },
        'sudah_lengkap': {
            variant: 'success',
            text: 'Sudah Lengkap'
        },
    };
    return data[status] || {
        variant: 'neutral',
        text: status
    };
};

const getStatusAksesMenuAsesmen = (status) => {
    const data = {
        'belum_diberikan': {
            variant: 'warning',
            text: 'Belum Diberikan'
        },
        'diberikan': {
            variant: 'success',
            text: 'Diberikan'
        },
    };
    return data[status] || {
        variant: 'neutral',
        text: status
    };
};

const getStatusFinalAsesi = (status) => {
    const data = {
        'belum_ditetapkan': {
            variant: 'neutral',
            text: 'Belum Ditetapkan'
        },
        'belum_kompeten': {
            variant: 'warning',
            text: 'Belum Kompeten'
        },
        'kompeten': {
            variant: 'success',
            text: 'Kompeten'
        },
        'diskualifikasi': {
            variant: 'danger',
            text: 'Diskualifikasi'
        },
    };
    return data[status] || {
        variant: 'neutral',
        text: status
    };
};

</script>

<template>
    <AdminLayout>
        <CustomHeader :judul="`${sertification.skema.nama_skema}: Detail Peserta`" />
        <AdminSertifikasiMenu :sertification-id="props.sertification.id" />

        <div v-show="!isEditingCertificate && !isEditingAsesi"
            class="p-3 sm:p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="flex justify-end items-center mb-4">
                <BackButton :href="route('admin.sertifikasi.pendaftar.index', props.sertification.id)"
                    class="self-end sm:self-auto" />
            </div>
            <PendaftarDetailDataStatis :asesi="props.asesi" />
            <!-- Bagian E: Status -->
            <h3 class="text-md font-semibold dark:text-gray-300 mb-2 border-b pb-1 border-gray-700 mt-6">E. Status</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                <div>
                    <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Status Berkas Administrasi
                        Asesi</dt>
                    <dd class="mt-1 text-sm flex flex-wrap items-center gap-2">
                        <StatusBadge :variant="getStatusBerkasAdministrasi(asesi.status_berkas).variant">
                            {{ getStatusBerkasAdministrasi(asesi.status_berkas).text }}
                        </StatusBadge>
                        <EditButton @click="openModal('berkas')">Ubah Status</EditButton>
                    </dd>
                </div>
                <div>
                    <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Hak Akses Menu Asesmen</dt>
                    <dd class="mt-1 text-sm flex flex-wrap items-center gap-2">
                        <StatusBadge :variant="getStatusAksesMenuAsesmen(asesi.status_akses_asesmen).variant">
                            {{ getStatusAksesMenuAsesmen(asesi.status_akses_asesmen).text }}
                        </StatusBadge>
                        <EditButton @click="openModal('akses')">Ubah Status</EditButton>
                    </dd>
                </div>
                <div>
                    <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Status Akhir Asesi</dt>
                    <dd class="mt-1 text-sm flex flex-wrap items-center gap-2">
                        <StatusBadge :variant="getStatusFinalAsesi(asesi.status_final).variant">
                            {{ getStatusFinalAsesi(asesi.status_final).text }}
                        </StatusBadge>
                        <EditButton @click="openModal('final')">Ubah Status</EditButton>
                    </dd>
                </div>
            </div>


            <!-- F. Sertifikat - Hanya untuk Admin -->
            <div v-if="canManageCertificate">
                <h3 class="text-md font-semibold dark:text-gray-300 mb-2 border-b pb-1 border-gray-700 mt-6">F.
                    Sertifikat
                </h3>
                <div class="mt-4">
                    <p v-if="!asesi.sertifikat && props.asesi.status_final !== 'kompeten'"
                        class="text-sm font-medium text-gray-500 dark:text-gray-400 italic">
                        Sertifikat bisa diupload jika status akhir asesi adalah <strong>Kompeten</strong>.
                    </p>

                    <div v-else-if="!asesi.sertifikat && props.asesi.status_final === 'kompeten'">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Belum ada sertifikat yang diunggah.</p>
                        <SeeButton @click="isEditingCertificate = true">
                            Upload Sertifikat
                        </SeeButton>
                    </div>

                    <FileCard v-else-if="props.asesi.sertifikat" :title="props.asesi.sertifikat.file_path"
                        :href="`/storage/${props.asesi.sertifikat.file_path}`" icon="award" status="Telah Terbit"
                        editable @edit="isEditingCertificate = true">
                    </FileCard>
                </div>
            </div>
        </div>


        <!-- Form Edit Sertifikat -->
        <div v-show="isEditingCertificate" class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                {{ props.asesi.sertifikat ? 'Ubah Data Sertifikat' : 'Upload Sertifikat' }}
            </h3>
            <p class="my-1 text-sm text-gray-600 dark:text-gray-400">Untuk: <span class="font-semibold">{{
                props.asesi.student.user.name }}</span></p>
            <form @submit.prevent="submitCertificate" class="grid md:grid-cols-2 gap-4 mt-4">
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
                    <DangerButton v-if="props.asesi.sertifikat" type="button" @click="deleteCertificate"
                        :disabled="certificateForm.processing">
                        Hapus Sertifikat
                    </DangerButton>
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