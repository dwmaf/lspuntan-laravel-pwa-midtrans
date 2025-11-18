<script setup>
import AsesiLayout from "@/Layouts/AsesiLayout.vue";
import AsesiSertifikasiMenu from "@/Components/AsesiSertifikasiMenu.vue";
import Header from "@/Components/CustomHeader.vue";
import PendaftarDetailDataStatis from "@/Pages/Admin/PendaftarDetailDataStatis.vue"; // Kita bisa pakai ulang komponen ini
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import EditButton from "@/Components/EditButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import FileIcon from "@/Components/FileIcon.vue";
import TextInput from "@/Components/TextInput.vue";
import SingleFileInput from "../../Components/SingleFileInput.vue";
import MultiFileInput from "../../Components/MultiFileInput.vue";
import InputError from "@/Components/InputError.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { ref, computed, onMounted } from 'vue';

const props = defineProps({
    sertification: Object,
    student: Object,
    asesi: Object,
    asesiStatusEnum: Object,
    transactionStatusEnum: Object,
});
// console.log(props.asesi.status);

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



const getAsesiStatusClass = (status) => {
    const classes = {
        [props.asesiStatusEnum.MENUNGGU_VERIFIKASI_BERKAS]: 'bg-amber-100 text-amber-800 dark:bg-amber-700 dark:text-amber-100',
        [props.asesiStatusEnum.PERLU_PERBAIKAN_BERKAS]: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100',
        [props.asesiStatusEnum.DITOLAK]: 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100',
        [props.asesiStatusEnum.DILANJUTKAN_ASESMEN]: 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100',
        [props.asesiStatusEnum.LULUS_SERTIFIKASI]: 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100',
    };
    return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200';
};
const getPaymentStatusInfo = (transaction) => {
    if (!transaction) {
        return { text: 'Belum Submit Bukti Pembayaran', class: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100' };
    }
    if (transaction.status === 'belum_bayar') {
        return { text: 'Belum Bayar', class: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100', secondclass: "border-gray-300 dark:border-gray-600" };
    }
    if (transaction.tipe === 'manual' && transaction.bukti_bayar && props.transactionStatusEnum.PENDING) {
        return { text: 'Menunggu Verifikasi', class: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100', secondclass: "border-yellow-300 dark:border-yellow-700" };
    }
    if (transaction.tipe === 'manual' && transaction.status === props.transactionStatusEnum.BUKTI_PEMBAYARAN_TERVERIFIKASI) {
        return { text: 'Pembayaran Terverifikasi', class: 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100', secondclass: "border-green-300 dark:border-green-700" };
    }
    if (transaction.tipe === 'manual' && transaction.status === props.transactionStatusEnum.BUKTI_PEMBAYARAN_DITOLAK) {
        return { text: 'Bukti Pembayaran Ditolak', class: 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100', secondclass: "border-red-300 dark:border-red-700" };
    }
    return { text: 'N/A', class: 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200' };
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
        
        <Header judul="Detail Pengajuan: {{ sertification.skema?.nama_skema ?? '' }}"/>
        
        <AsesiSertifikasiMenu :sertification-id="props.sertification.id" :asesi="props.asesi"
            :latest-transaction="props.asesi.latest_transaction" />

        <div class="max-w-7xl mx-auto">
            <div v-if="isEditing" class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <form @submit.prevent="update" class="mt-6 space-y-6">
                    <h3 class="dark:text-gray-300 font-semibold">A. Data Pribadi</h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel value="Nama Lengkap" required />
                            <TextInput v-model="form.name" type="text" required />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div>
                            <InputLabel value="No. KTP" required />
                            <TextInput v-model="form.nik" type="text" required />
                            <InputError :message="form.errors.nik" />
                        </div>
                        <div>
                            <InputLabel value="Tempat Lahir" required />
                            <TextInput v-model="form.tmpt_lhr" type="text" required />
                            <InputError :message="form.errors.tmpt_lhr" />
                        </div>
                        <div>
                            <InputLabel value="Tanggal Lahir" required />
                            <TextInput v-model="form.tgl_lhr" type="date" required />
                            <InputError :message="form.errors.tgl_lhr" />
                        </div>
                        <div>
                            <InputLabel for="kelamin" value="Jenis Kelamin" required />
                            <select id="kelamin" v-model="form.kelamin" required
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="Laki-laki">Laki-Laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                            <InputError :message="form.errors.kelamin" />
                        </div>
                        <div>
                            <InputLabel value="Kebangsaan" required />
                            <TextInput v-model="form.kebangsaan" type="text" required />
                            <InputError :message="form.errors.kebangsaan" />
                        </div>
                        <div>
                            <InputLabel value="No. Tlp HP(WA)" required />
                            <TextInput v-model="form.no_tlp_hp" type="text" required />
                            <InputError :message="form.errors.no_tlp_hp" />
                        </div>
                        <div>
                            <InputLabel value="No. Tlp Rumah" />
                            <TextInput v-model="form.no_tlp_rmh" type="text" />
                            <InputError :message="form.errors.no_tlp_rmh" />
                        </div>
                        <div>
                            <InputLabel value="No. Tlp Kantor" />
                            <TextInput v-model="form.no_tlp_kntr" type="text" />
                            <InputError :message="form.errors.no_tlp_kntr" />
                        </div>
                        <div>
                            <InputLabel value="Kualifikasi Pendidikan (tulis: Mahasiswa S1)" />
                            <TextInput v-model="form.kualifikasi_pendidikan" type="text" required />
                            <InputError :message="form.errors.kualifikasi_pendidikan" />
                        </div>
                    </div>

                    <!-- Data Pekerjaan -->
                    <h3 class="dark:text-gray-300 font-semibold pt-4">B. Data Pekerjaan Sekarang</h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel value="Nama Institusi/Perusahaan" />
                            <TextInput v-model="form.nama_institusi" type="text" />
                            <InputError :message="form.errors.nama_institusi" />
                        </div>
                        <div>
                            <InputLabel value="Jabatan" />
                            <TextInput v-model="form.jabatan" type="text" />
                            <InputError :message="form.errors.jabatan" />
                        </div>
                        <div>
                            <InputLabel value="Alamat Kantor" />
                            <TextInput v-model="form.alamat_kantor" type="text" />
                            <InputError :message="form.errors.alamat_kantor" />
                        </div>
                        <div>
                            <InputLabel value="No. Tlp/Email/Fax" />
                            <TextInput v-model="form.no_tlp_email_fax" type="text" />
                            <InputError :message="form.errors.no_tlp_email_fax" />
                        </div>
                    </div>

                    <!-- Data Sertifikasi -->
                    <h3 class="dark:text-gray-300 font-semibold pt-4">C. Data Sertifikasi</h3>
                    <div>
                        <InputLabel value="Tujuan Sertifikasi" required />
                        <select v-model="form.tujuan_sert" required
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:border-indigo-600 focus:ring-indigo-600 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option value="" disabled>--Pilih tujuan sertifikasi--</option>
                            <option value="Sertifikasi">Sertifikasi</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                        <InputError :message="form.errors.tujuan_sert" />
                    </div>

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
                                            <FontAwesomeIcon icon=" fa-xmark" class=" text-sm" />
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
                <div v-if="showUrlNotification"
                    class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 border border-green-300 dark:border-green-800"
                    role="alert">
                    <span class="font-medium">!Notifikasi</span> {{ urlNotificationMessage }}
                </div>
                <div v-if="asesi.status === props.asesiStatusEnum.PERLU_PERBAIKAN_BERKAS"
                    class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300 border border-yellow-300 dark:border-yellow-800"
                    role="alert">
                    <span class="font-medium">Perhatian!</span> Admin meminta perbaikan berkas dengan catatan:
                    <p v-if="asesi.catatan_perbaikan" class="mt-2 font-mono">{{ asesi.catatan_perbaikan }}</p>
                    <p v-else class="mt-2 font-mono">Tanpa catatan</p>
                </div>
                <PendaftarDetailDataStatis :asesi="props.asesi" />


                <h3 class="text-md font-semibold dark:text-gray-300 mb-2 border-b pb-1 border-gray-700 mt-6">E. Status
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Status Asesi</dt>
                        <dd class="mt-1 text-sm mr-1">
                            <span
                                :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getAsesiStatusClass(asesi.status)]">
                                {{ asesi.status.replace(/_/g, ' ') }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Status Pembayaran</dt>
                        <dd>
                            <span
                                :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getPaymentStatusInfo(asesi.latest_transaction).class]">
                                {{ getPaymentStatusInfo(asesi.latest_transaction).text }}
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
                            <dd v-if="asesi.status === 'lulus_sertifikasi'" class="mt-1 text-sm space-y-2">
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