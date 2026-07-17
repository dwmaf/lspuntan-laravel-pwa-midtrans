<script setup>
import AsesiLayout from "@/Layouts/AsesiLayout.vue";
import AsesiSertifikasiMenu from "@/Components/AsesiSertifikasiMenu.vue";
import CustomHeader from "@/Components/CustomHeader.vue";
import PendaftarDetailDataStatis from "@/Pages/Admin/PendaftarDetailDataStatis.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue";
import EditButton from "@/Components/Button/EditButton.vue";
import StatusBadge from "@/Components/StatusBadge.vue";
import TextInput from "@/Components/Input/TextInput.vue";
import SingleFileInput from "@/Components/Input/SingleFileInput.vue";
import SelectInput from "@/Components/Input/SelectInput.vue";
import MultiFileInput from "@/Components/Input/MultiFileInput.vue";
import FileCard from "@/Components/FileCard.vue";
import { useFormat } from "@/Composables/useFormat";
import Alert from "@/Components/Alert.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { ref, computed, onMounted } from 'vue';
const props = defineProps({
    sertifikasi: Object,
    mahasiswa: Object,
    asesi: Object,
    statusBerkasAdministrasiOptions: Array,
    StatusFinalAsesiOptions: Array,
});
const genderOptions = [
    { value: 'Laki-laki', text: 'Laki-Laki' },
    { value: 'Perempuan', text: 'Perempuan' },
];

const tujuanOptions = [
    { value: 'Sertifikasi', text: 'Sertifikasi' },
    { value: 'Pengakuan Kompetensi Terkini (PKT)', text: 'Pengakuan Kompetensi Terkini (PKT)' },
    { value: 'Rekognisi Pembelajaran Lampau (RPL)', text: 'Rekognisi Pembelajaran Lampau (RPL)' },
    { value: 'Lainnya', text: 'Lainnya' },
];

const showUrlNotification = ref(false);
const urlNotificationMessage = ref('');

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    const message = params.get('messageNotif');
    if (message) {
        urlNotificationMessage.value = message;
        showUrlNotification.value = true;
    }
});

const canEdit = computed(() => {
    return props.asesi?.status_berkas !== 'sudah_lengkap';
});

const isUrlNotificationRelevant = computed(() => {
    const msg = urlNotificationMessage.value;
    const asesiData = props.asesi;
    if (!msg || !asesiData) return false;

    if (asesiData.status_berkas === 'sudah_lengkap' && msg.includes('lengkap')) return true;
    if (asesiData.status_berkas === 'menunggu_verifikasi_admin' && msg.includes('antrean')) return true;

    if (asesiData.status_final === 'belum_kompeten' && msg.includes('Belum Kompeten')) return true;
    if (asesiData.status_final === 'kompeten' && msg.includes('Kompeten') && !msg.includes('Belum Kompeten')) return true;
    if (asesiData.status_final === 'diskualifikasi' && msg.includes('Diskualifikasi')) return true;
    if (asesiData.status_final === 'belum_ditetapkan' && msg.includes('direset')) return true;

    if (asesiData.asesor && msg.includes('ditetapkan sebagai Asesor')) return true;

    if (asesiData.sertifikat && (msg.includes('download sertifikat') || msg.includes('download versi terbaru'))) return true;
    if (asesiData.status_final === 'kompeten' && !asesiData.sertifikat && msg.includes('dihapus')) return true;

    return false;
});

const urlNotificationType = computed(() => {
    const msg = urlNotificationMessage.value;
    if (!msg) return 'success';
    if (msg.includes('lengkap') || (msg.includes('Kompeten') && !msg.includes('Belum Kompeten')) || msg.includes('download')) return 'success';
    if (msg.includes('Belum Kompeten') || msg.includes('Diskualifikasi') || msg.includes('dihapus')) return 'error';
    if (msg.includes('direset')) return 'warning';
    return 'info';
});

const isEditing = ref(false);

const form = useForm({
    _method: 'patch',
    name: props.asesi.mahasiswa.user.name,
    nik: props.asesi.mahasiswa.nik,
    tmpt_lhr: props.asesi.mahasiswa?.tmpt_lhr || '',
    tgl_lhr: props.asesi.mahasiswa?.tgl_lhr || '',
    kelamin: props.asesi.mahasiswa?.kelamin || 'Laki-laki',
    kebangsaan: props.asesi.mahasiswa?.kebangsaan || '',
    no_tlp_hp: props.asesi.mahasiswa.user?.no_tlp_hp || '',
    no_tlp_rmh: props.asesi.mahasiswa?.no_tlp_rmh || '',
    no_tlp_kntr: props.asesi.mahasiswa?.no_tlp_kntr || '',
    kualifikasi_pendidikan: props.asesi.mahasiswa?.kualifikasi_pendidikan || 'Mahasiswa S1',
    nama_institusi: props.asesi.mahasiswa?.nama_institusi || '',
    jabatan: props.asesi.mahasiswa?.jabatan || '',
    alamat_kantor: props.asesi.mahasiswa?.alamat_kantor || '',
    no_tlp_email_fax: props.asesi.mahasiswa?.no_tlp_email_fax || '',
    tujuan_sert: props.asesi.tujuan_sert,
    rekap_nilai: props.asesi.rekap_nilai,
    bukti_bayar: null,
    apl_1: null,
    apl_2: null,
    foto_ktp: null,
    foto_ktm: null,
    pas_foto: null,
    transkrip_nilai: null,
    surat_ket_magang: [],
    sertif_pelatihan: [],
    dok_pendukung_lain: [],
    delete_files_collection: [],
    delete_files_mahasiswa: [],
    delete_files_asesi: []
});

const enterEditMode = () => {
    isEditing.value = true;
    console.log(!form.apl_1);
};
const cancelEdit = () => {
    isEditing.value = false;
    form.reset();
    form.clearErrors();
};

const update = () => {
    form.post(route('asesi.sertifikasi.applied.update', { sertifikasi: props.sertifikasi, asesi: props.asesi }), {
        onSuccess: () => cancelEdit(),
    });
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

const getFiles = (collection, type) => {
    if (!collection) return [];
    return collection
        .filter(file => file.type === type)
        .map(file => ({
            ...file,
            downloadUrl: `/download/berkas_asesi/${file.id}/path_file`
        }));
};

const suratMagangFiles = computed(() => getFiles(props.asesi.berkas_asesi, 'surat_ket_magang'));
const sertifPelatihanFiles = computed(() => getFiles(props.asesi.berkas_asesi, 'sertif_pelatihan'));
const dokPendukungFiles = computed(() => getFiles(props.asesi.berkas_asesi, 'dok_pendukung_lain'));

const { formatCurrency, formatDateTime } = useFormat();
</script>

<template>
    <AsesiLayout>

        <CustomHeader :judul="`Detail Pengajuan: ${sertifikasi.skema?.nama_skema ?? ''}`" />

        <AsesiSertifikasiMenu :sertifikasi="props.sertifikasi" :asesi="props.asesi" />

        <div class="max-w-7xl mx-auto">
            <div v-if="isEditing" class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <form @submit.prevent="update" class="mt-6 space-y-6">
                    <h3 class="dark:text-gray-300 font-semibold">A. Data Pribadi</h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <TextInput id="name" label="Nama Lengkap Sesuai KTP" v-model="form.name" type="text" required
                            :error="form.errors.name" />
                        <TextInput id="nik" label="No. KTP" v-model="form.nik" type="text" required
                            :error="form.errors.nik" />
                        <TextInput id="tmpt_lhr" label="Tempat Lahir" v-model="form.tmpt_lhr" type="text" required
                            :error="form.errors.tmpt_lhr" />
                        <TextInput id="tgl_lhr" label="Tanggal Lahir" v-model="form.tgl_lhr" type="date" required
                            :error="form.errors.tgl_lhr" />
                        <SelectInput id="kelamin" label="Jenis Kelamin" v-model="form.kelamin" :options="genderOptions"
                            placeholder="--Pilih Kelamin--" :error="form.errors.kelamin" required />
                        <TextInput id="kebangsaan" label="Kebangsaan" v-model="form.kebangsaan" type="text" required
                            :error="form.errors.kebangsaan" />
                        <TextInput id="no_tlp_hp" label="No. Tlp HP(WA)" v-model="form.no_tlp_hp" type="text" required
                            :error="form.errors.no_tlp_hp" />
                        <TextInput id="no_tlp_rmh" label="No. Tlp Rumah" v-model="form.no_tlp_rmh" type="text"
                            :error="form.errors.no_tlp_rmh" />
                        <TextInput id="no_tlp_kntr" label="No. Tlp Kantor" v-model="form.no_tlp_kntr" type="text"
                            :error="form.errors.no_tlp_kntr" />
                        <TextInput id="kualifikasi_pendidikan" label="Kualifikasi Pendidikan Terakhir"
                            v-model="form.kualifikasi_pendidikan" type="text" required
                            :error="form.errors.kualifikasi_pendidikan" />
                    </div>

                    <!-- Data Pekerjaan -->
                    <h3 class="dark:text-gray-300 font-semibold pt-4">B. Data Pekerjaan Sekarang</h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <TextInput id="nama_institusi" label="Nama Institusi/Perusahaan" v-model="form.nama_institusi"
                            type="text" :error="form.errors.nama_institusi" />
                        <TextInput id="jabatan" label="Jabatan" v-model="form.jabatan" type="text"
                            :error="form.errors.jabatan" />
                        <TextInput id="alamat_kantor" label="Alamat Kantor" v-model="form.alamat_kantor" type="text"
                            :error="form.errors.alamat_kantor" />
                        <TextInput id="no_tlp_email_fax" label="No. Tlp/Email/Fax" v-model="form.no_tlp_email_fax"
                            type="text" :error="form.errors.no_tlp_email_fax" />
                    </div>

                    <!-- Data Sertifikasi -->
                    <h3 class="dark:text-gray-300 font-semibold pt-4">C. Data Sertifikasi</h3>
                    <SelectInput id="tujuan_sert" label="Tujuan Sertifikasi" v-model="form.tujuan_sert"
                        :options="tujuanOptions" placeholder="--Pilih tujuan sertifikasi--"
                        :error="form.errors.tujuan_sert" required />
                    <p class="text-sm text-gray-800 dark:text-gray-100">
                        Silahkan lakukan pembayaran sebesar
                        <span class="font-medium">
                            {{ formatCurrency(sertifikasi.biaya) }}
                        </span>
                        ke nomor rekening
                        <span class="font-medium">
                            {{ sertifikasi.no_rek }}
                            {{ sertifikasi.bank }}
                        </span>
                        an.
                        <span class="font-medium">
                            {{ sertifikasi.atas_nama_rek }}.
                        </span>
                        Submit bukti pembayaran paling lambat
                        <span class="font-medium">
                            {{ formatDateTime(sertifikasi.tgl_apply_ditutup) }}
                        </span>
                    </p>
                    <div v-if="new Date() < new Date(sertifikasi.tgl_apply_ditutup)">
                        <SingleFileInput id="bukti_bayar" v-model="form.bukti_bayar" label="Bukti Pembayaran"
                            v-model:deleteList="form.delete_files_asesi" is-label-required accept=".jpg,.png,.jpeg,.pdf"
                            :error="form.errors.bukti_bayar"
                            :existing-file-url="asesi?.bukti_bayar ? `/download/asesis/${asesi.id}/bukti_bayar` : null"
                            :is-marked-for-deletion="form.delete_files_asesi.includes('bukti_bayar')"
                            delete-identifier="bukti_bayar"
                            :required="!asesi?.bukti_bayar || form.delete_files_asesi.includes('bukti_bayar')" />
                    </div>
                    <!-- Bukti Kelengkapan -->
                    <h3 class="dark:text-gray-300 font-semibold pt-4">D. Bukti Kelengkapan</h3>
                    <SingleFileInput id="apl_1" v-model="form.apl_1" v-model:deleteList="form.delete_files_asesi"
                        delete-identifier="apl_1" label="Form APL.01" is-label-required
                        :template-url="`/download/skemas/${sertifikasi.skema.id}/format_apl_1`"
                        :existing-file-url="asesi?.apl_1 ? `/download/asesis/${asesi.id}/apl_1` : null"
                        :is-marked-for-deletion="form.delete_files_asesi.includes('apl_1')" accept=".doc,.docx"
                        :error="form.errors.apl_1"
                        :required="!asesi?.apl_1 || form.delete_files_asesi.includes('apl_1')" />
                    <SingleFileInput v-model="form.apl_2" v-model:deleteList="form.delete_files_asesi"
                        delete-identifier="apl_2" label="Form APL.02" is-label-required
                        :template-url="`/download/skemas/${sertifikasi.skema.id}/format_apl_2`"
                        :existing-file-url="asesi?.apl_2 ? `/download/asesis/${asesi.id}/apl_2` : null"
                        :is-marked-for-deletion="form.delete_files_asesi.includes('apl_2')" accept=".doc,.docx"
                        :error="form.errors.apl_2"
                        :required="!asesi?.apl_2 || form.delete_files_asesi.includes('apl_2')" />
                    <SingleFileInput v-model="form.foto_ktp" v-model:deleteList="form.delete_files_mahasiswa"
                        delete-identifier="foto_ktp" label="Scan KTP" is-label-required
                        :existing-file-url="mahasiswa?.foto_ktp ? `/download/mahasiswa/${mahasiswa.id}/foto_ktp` : null"
                        :is-marked-for-deletion="form.delete_files_mahasiswa.includes('foto_ktp')"
                        accept=".jpg,.png,.jpeg,.pdf" :error="form.errors.foto_ktp"
                        :required="!mahasiswa?.foto_ktp || form.delete_files_mahasiswa.includes('foto_ktp')" />
                    <SingleFileInput v-model="form.pas_foto" v-model:deleteList="form.delete_files_mahasiswa"
                        delete-identifier="pas_foto"
                        label="Pasfoto terbaru dengan latar belakang merah, berukuran 4x6 (ukuran file maksimal 1 MB)"
                        is-label-required
                        :existing-file-url="mahasiswa?.pas_foto ? `/download/mahasiswa/${mahasiswa.id}/pas_foto` : null"
                        :is-marked-for-deletion="form.delete_files_mahasiswa.includes('pas_foto')"
                        accept=".jpg,.png,.jpeg,.pdf" :error="form.errors.pas_foto"
                        :required="!mahasiswa?.pas_foto || form.delete_files_mahasiswa.includes('pas_foto')" />
                    <SingleFileInput v-model="form.foto_ktm" v-model:deleteList="form.delete_files_asesi"
                        delete-identifier="foto_ktm" label="Scan KTM (ukuran file maksimal 1 MB)" is-label-required
                        :existing-file-url="asesi?.foto_ktm ? `/download/asesis/${asesi.id}/foto_ktm` : null"
                        :is-marked-for-deletion="form.delete_files_asesi.includes('foto_ktm')"
                        accept=".jpg,.png,.jpeg,.pdf" :error="form.errors.foto_ktm"
                        :required="!asesi?.foto_ktm || form.delete_files_asesi.includes('foto_ktm')" />
                    <SingleFileInput v-model="form.transkrip_nilai" v-model:deleteList="form.delete_files_asesi"
                        delete-identifier="transkrip_nilai" label="Transkrip Nilai Terbaru" is-label-required
                        :existing-file-url="asesi?.transkrip_nilai ? `/download/asesis/${asesi.id}/transkrip_nilai` : null"
                        :is-marked-for-deletion="form.delete_files_asesi.includes('transkrip_nilai')"
                        accept=".pdf" :error="form.errors.transkrip_nilai"
                        :required="!asesi?.transkrip_nilai || form.delete_files_asesi.includes('transkrip_nilai')" />
                    <MultiFileInput v-model="form.surat_ket_magang" v-model:deleteList="form.delete_files_collection"
                        label="Scan Surat Keterangan Magang/PKL/MBKM (maks 5, ukuran file maksimal 3 MB)"
                        :existing-files="suratMagangFiles" :max-files="5" accept=".jpg,.png,.jpeg,.pdf"
                        :error="form.errors.surat_ket_magang" :error-list="form.errors['surat_ket_magang.0']" />
                    <MultiFileInput v-model="form.sertif_pelatihan" v-model:deleteList="form.delete_files_collection"
                        label="Scan Sertifikat Pelatihan (maks 5, ukuran file maksimal 3 MB)"
                        :existing-files="sertifPelatihanFiles" :max-files="5" accept=".jpg,.png,.jpeg,.pdf"
                        :error="form.errors.sertif_pelatihan" :error-list="form.errors['sertif_pelatihan.0']" />
                    <MultiFileInput v-model="form.dok_pendukung_lain" v-model:deleteList="form.delete_files_collection"
                        label="Dokumen pendukung lainnya (maks 5, ukuran file maksimal 5 MB)"
                        :existing-files="dokPendukungFiles" :max-files="5" accept=".jpg,.png,.jpeg,.pdf,.doc,.docx"
                        :error="form.errors.dok_pendukung_lain" :error-list="form.errors['dok_pendukung_lain.0']" />

                    <div class="flex items-center gap-4">
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Update
                        </PrimaryButton>
                        <SecondaryButton type="button" @click="cancelEdit">Batal</SecondaryButton>
                    </div>
                </form>
            </div>

            <!-- Mode Tampilan -->
            <div v-else class="p-3 sm:p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <div v-if="canEdit" class="flex justify-end mb-4">
                    <EditButton @click="enterEditMode">Edit Data</EditButton>
                </div>

                <Alert v-if="showUrlNotification && isUrlNotificationRelevant" :type="urlNotificationType" title="Notifikasi">
                    {{ urlNotificationMessage }}
                </Alert>

                <Alert v-if="asesi.status_berkas === 'perlu_perbaikan_berkas'" type="warning" title="Perhatian!">
                    <span v-if="asesi.catatan_perbaikan">
                        Admin meminta perbaikan berkas dengan catatan:
                        <p class="mt-2 font-mono">{{ asesi.catatan_perbaikan }}</p>
                    </span>
                    <span v-else>
                        Admin meminta perbaikan berkas
                    </span>
                </Alert>

                <PendaftarDetailDataStatis :asesi="props.asesi" />
                <h3 class="text-md font-semibold dark:text-gray-300 mb-2 border-b pb-1 border-gray-700 mt-6">E. Status
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">
                            Status Berkas Administrasi Asesi</dt>
                        <dd class="mt-1 text-sm flex flex-wrap items-center gap-2">
                            <StatusBadge :variant="getStatusBerkasAdministrasi(asesi.status_berkas).variant">
                                {{ getStatusBerkasAdministrasi(asesi.status_berkas).text }}
                            </StatusBadge>
                        </dd>
                    </div>

                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">
                            Asesor</dt>
                        <dd class="mt-1 text-sm flex flex-wrap items-center gap-2">
                            <span v-if="asesi.asesor" class="font-medium text-gray-900 dark:text-gray-100">
                                {{ asesi.asesor.user?.name }}
                            </span>
                            <StatusBadge v-else variant="neutral">
                                Belum Ditetapkan
                            </StatusBadge>
                        </dd>
                    </div>

                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Status Akhir Asesi</dt>
                        <dd class="mt-1 text-sm flex flex-wrap items-center gap-2">
                            <StatusBadge :variant="getStatusFinalAsesi(asesi.status_final).variant">
                                {{ getStatusFinalAsesi(asesi.status_final).text }}
                            </StatusBadge>
                        </dd>
                    </div>
                </div>

                <div v-if="props.asesi.sertifikat">
                    <h3 class="text-md font-semibold dark:text-gray-300 mb-2 border-b pb-1 border-gray-700 mt-6">F.
                        Sertifikat
                    </h3>
                    <div class="mt-4">
                        <FileCard v-if="asesi.status_final === 'kompeten' && asesi.sertifikat"
                            :title="asesi.sertifikat.file_path"
                            :href="`/download/sertifikat/${asesi.sertifikat.id}/file_path`" icon="award"
                            status="Telah Terbit" />
                    </div>
                </div>
            </div>
        </div>
    </AsesiLayout>
</template>