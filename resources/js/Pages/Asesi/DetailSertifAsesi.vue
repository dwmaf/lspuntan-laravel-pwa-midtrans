<script setup>
import AsesiLayout from "@/Layouts/AsesiLayout.vue";
import AsesiSertifikasiMenu from "@/Components/AsesiSertifikasiMenu.vue";
import CustomHeader from "@/Components/CustomHeader.vue";
import PendaftarDetailDataStatis from "@/Pages/Admin/PendaftarDetailDataStatis.vue"; // Kita bisa pakai ulang komponen ini
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue";
import EditButton from "@/Components/Button/EditButton.vue";
import InputLabel from "@/Components/Input/InputLabel.vue";
import FileIcon from "@/Components/FileIcon.vue";
import TextInput from "@/Components/Input/TextInput.vue";
import SingleFileInput from "@/Components/Input/SingleFileInput.vue";
import SelectInput from "@/Components/Input/SelectInput.vue";
import MultiFileInput from "@/Components/Input/MultiFileInput.vue";
import InputError from "@/Components/Input/InputError.vue";
import Alert from "@/Components/Alert.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { ref, computed, onMounted } from 'vue';
import { X } from 'lucide-vue-next';
const props = defineProps({
    sertification: Object,
    student: Object,
    asesi: Object,
    statusAksesMenuAsesmenOptions: Array,
    statusBerkasAdministrasiOptions: Array,
    StatusFinalAsesiOptions: Array,
});
const genderOptions = [
    { value: 'Laki-laki', text: 'Laki-Laki' },
    { value: 'Perempuan', text: 'Perempuan' },
];

const tujuanOptions = [
    { value: 'Sertifikasi', text: 'Sertifikasi' },
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

// State untuk mode edit
const isEditing = ref(false);

// Inisialisasi form dengan data yang ada
const form = useForm({
    _method: 'patch',
    name: props.asesi.student.user.name,
    nik: props.asesi.student.nik,
    tmpt_lhr: props.asesi.student?.tmpt_lhr || '',
    tgl_lhr: props.asesi.student?.tgl_lhr || '',
    kelamin: props.asesi.student?.kelamin || 'Laki-laki',
    kebangsaan: props.asesi.student?.kebangsaan || '',
    no_tlp_hp: props.asesi.student.user?.no_tlp_hp || '',
    no_tlp_rmh: props.asesi.student?.no_tlp_rmh || '',
    no_tlp_kntr: props.asesi.student?.no_tlp_kntr || '',
    kualifikasi_pendidikan: props.asesi.student?.kualifikasi_pendidikan || 'Mahasiswa S1',
    nama_institusi: props.asesi.student?.nama_institusi || '',
    jabatan: props.asesi.student?.jabatan || '',
    alamat_kantor: props.asesi.student?.alamat_kantor || '',
    no_tlp_email_fax: props.asesi.student?.no_tlp_email_fax || '',
    tujuan_sert: props.asesi.tujuan_sert,
    makulNilais: props.asesi.makulnilais.length > 0 ? props.asesi.makulnilais.map(m => ({ nama_makul: m.nama_makul, nilai_makul: m.nilai_makul })) : [{ nama_makul: '', nilai_makul: '' }],
    apl_1: null,
    apl_2: null,
    foto_ktp: null,
    foto_ktm: null,
    pas_foto: null,
    kartu_hasil_studi: [],
    surat_ket_magang: [],
    sertif_pelatihan: [],
    dok_pendukung_lain: [],
    delete_files_collection: [],
    delete_files_student: [],
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
    form.post(route('asesi.sertifikasi.applied.update', { sert_id: props.sertification.id, asesi_id: props.asesi.id }), {
        onSuccess: () => cancelEdit(),
    });
};

const addMakul = () => form.makulNilais.push({ nama_makul: '', nilai_makul: '' });
const removeMakul = (index) => form.makulNilais.splice(index, 1);

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

const getFiles = (collection, type) => {
    if (!collection) return [];
    return collection.filter(file => file.type === type);
};

const kartuHasilStudiFiles = computed(() => getFiles(props.asesi.asesifiles, 'kartu_hasil_studi'));
const suratMagangFiles = computed(() => getFiles(props.asesi.asesifiles, 'surat_ket_magang'));
const sertifPelatihanFiles = computed(() => getFiles(props.asesi.asesifiles, 'sertif_pelatihan'));
const dokPendukungFiles = computed(() => getFiles(props.asesi.asesifiles, 'dok_pendukung_lain'));

</script>

<template>
    <AsesiLayout>

        <CustomHeader :judul="`Detail Pengajuan: ${sertification.skema?.nama_skema ?? ''}`" />

        <AsesiSertifikasiMenu :sertification="props.sertification" :asesi="props.asesi" />

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

                    <!-- Mata Kuliah Dinamis -->
                    <div>
                        <InputLabel value="Mata Kuliah terkait Skema Sertifikasi dan Nilai" required />
                        <div class="mt-1 space-y-3">
                            <div v-for="(makul, index) in form.makulNilais" :key="index"
                                class="flex flex-col sm:flex-row items-center gap-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                                <div class="w-full sm:grow">
                                    <InputLabel :for="`makul_nama_${index}`" value="Nama Mata Kuliah" class="text-xs" />
                                    <TextInput :id="`makul_nama_${index}`" v-model="makul.nama_makul"
                                        placeholder="Contoh: Struktur Data Algoritma" required />
                                </div>
                                <div class="w-full sm:w-24 shrink-0">
                                    <InputLabel :for="`makul_nilai_${index}`" value="Nilai" class="text-xs" />
                                    <TextInput :id="`makul_nilai_${index}`" v-model="makul.nilai_makul"
                                        placeholder="e.g., A" required />
                                </div>
                                <div class="w-full sm:w-auto pt-2 sm:pt-5">
                                    <button @click="removeMakul(index)" type="button"
                                        v-show="form.makulNilais.length > 1"
                                        class="w-auto px-3 py-1 rounded-md sm:w-8 sm:h-8 shrink-0 sm:flex sm:items-center sm:justify-center sm:rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-300 dark:hover:bg-gray-600 hover:text-gray-800 dark:hover:text-gray-200">
                                        <span class="hidden sm:block">
                                            <X class="w-4 mx-1" />
                                        </span>
                                        <span class="sm:hidden font-medium text-sm">Hapus</span>
                                    </button>

                                </div>
                            </div>
                        </div>
                        <button type="button" @click="addMakul"
                            class="mt-3 inline-flex items-center gap-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                                    clip-rule="evenodd" />
                            </svg>
                            Tambah Mata Kuliah
                        </button>

                    </div>

                    <!-- Bukti Kelengkapan -->
                    <h3 class="dark:text-gray-300 font-semibold pt-4">D. Bukti Kelengkapan</h3>
                    <SingleFileInput v-model="form.apl_1" v-model:deleteList="form.delete_files_asesi"
                        delete-identifier="apl_1" label="Form APL.01" is-label-required
                        :template-url="`/storage/${sertification.skema.format_apl_1}`"
                        :existing-file-url="asesi?.apl_1 ? `/storage/${asesi.apl_1}` : null"
                        :is-marked-for-deletion="form.delete_files_asesi.includes('apl_1')" accept=".pdf,.doc,.docx"
                        :error="form.errors.apl_1"
                        :required="!asesi?.apl_1 || form.delete_files_asesi.includes('apl_1')" />
                    <SingleFileInput v-model="form.apl_2" v-model:deleteList="form.delete_files_asesi"
                        delete-identifier="apl_2" label="Form APL.02" is-label-required
                        :template-url="`/storage/${sertification.skema.format_apl_2}`"
                        :existing-file-url="asesi?.apl_2 ? `/storage/${asesi.apl_2}` : null"
                        :is-marked-for-deletion="form.delete_files_asesi.includes('apl_2')" accept=".pdf,.doc,.docx"
                        :error="form.errors.apl_2"
                        :required="!asesi?.apl_2 || form.delete_files_asesi.includes('apl_2')" />
                    <SingleFileInput v-model="form.foto_ktp" v-model:deleteList="form.delete_files_student"
                        delete-identifier="foto_ktp" label="Scan KTP" is-label-required
                        :existing-file-url="student?.foto_ktp ? `/storage/${student.foto_ktp}` : null"
                        :is-marked-for-deletion="form.delete_files_student.includes('foto_ktp')"
                        accept=".jpg,.png,.jpeg,.pdf" :error="form.errors.foto_ktp"
                        :required="!student?.foto_ktp || form.delete_files_student.includes('foto_ktp')" />
                    <SingleFileInput v-model="form.pas_foto" v-model:deleteList="form.delete_files_student"
                        delete-identifier="pas_foto"
                        label="Pasfoto terbaru dengan latar belakang merah, berukuran 4x6 (ukuran file maksimal 1 MB)"
                        is-label-required :existing-file-url="student?.pas_foto ? `/storage/${student.pas_foto}` : null"
                        :is-marked-for-deletion="form.delete_files_student.includes('pas_foto')"
                        accept=".jpg,.png,.jpeg,.pdf" :error="form.errors.pas_foto"
                        :required="!student?.pas_foto || form.delete_files_student.includes('pas_foto')" />
                    <SingleFileInput v-model="form.foto_ktm" v-model:deleteList="form.delete_files_asesi"
                        delete-identifier="foto_ktm" label="Scan KTM (ukuran file maksimal 1 MB)" is-label-required
                        :existing-file-url="asesi?.foto_ktm ? `/storage/${asesi.foto_ktm}` : null"
                        :is-marked-for-deletion="form.delete_files_asesi.includes('foto_ktm')"
                        accept=".jpg,.png,.jpeg,.pdf" :error="form.errors.foto_ktm"
                        :required="!asesi?.foto_ktm || form.delete_files_asesi.includes('foto_ktm')" />
                    <MultiFileInput v-model="form.kartu_hasil_studi" v-model:deleteList="form.delete_files_collection"
                        label="Scan Kartu Hasil Studi (Bisa upload lebih dari satu)"
                        :existing-files="kartuHasilStudiFiles" :max-files="5" accept=".jpg,.png,.jpeg,.pdf,.docx"
                        :required="kartuHasilStudiFiles.length === 0 || form.kartu_hasil_studi.length === 0"
                        :error="form.errors.kartu_hasil_studi" :error-list="form.errors['kartu_hasil_studi.0']" />
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
            <div v-else class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <div class="flex justify-end mb-4">
                    <EditButton @click="enterEditMode">Edit Data</EditButton>
                </div>

                <Alert v-if="showUrlNotification" type="success" title="Notifikasi">
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
                            <span
                                :class="['px-2 py-1 text-xs leading-5 font-semibold rounded-full', getStatusBerkasAdministrasi(asesi.status_berkas).class]">
                                {{ getStatusBerkasAdministrasi(asesi.status_berkas).text }}
                            </span>

                        </dd>
                    </div>
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Hak Akses Menu Asesmen
                        </dt>
                        <dd class="mt-1 text-sm flex flex-wrap items-center gap-2">
                            <span
                                :class="['px-2 py-1 text-xs leading-5 font-semibold rounded-full', getStatusAksesMenuAsesmen(asesi.status_akses_asesmen).class]">
                                {{ getStatusAksesMenuAsesmen(asesi.status_akses_asesmen).text }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Status Akhir Asesi</dt>
                        <dd class="mt-1 text-sm flex flex-wrap items-center gap-2">
                            <span
                                :class="['px-2 py-1 text-xs leading-5 font-semibold rounded-full', getStatusFinalAsesi(asesi.status_final).class]">
                                {{ getStatusFinalAsesi(asesi.status_final).text }}
                            </span>

                        </dd>
                    </div>
                </div>


                <div v-if="props.asesi.sertifikat">
                    <h3 class="text-md font-semibold dark:text-gray-300 mb-2 border-b pb-1 border-gray-700 mt-6">F.
                        Sertifikat
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                        <div>
                            <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Sertifikat
                                Asesi
                            </dt>
                            <dd v-if="asesi.status_final === 'kompeten'" class="mt-1 text-sm space-y-2">
                                <a :href="`/storage/${asesi.sertifikat.file_path}`" target="_blank"
                                    class="text-blue-500 hover:text-blue-700">
                                    Lihat Sertifikat
                                </a>
                            </dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AsesiLayout>
</template>