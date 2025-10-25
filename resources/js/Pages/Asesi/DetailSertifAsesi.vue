<!-- filepath: d:\Laravel-App\lsp-untan-laravel-pwa\resources\js\Pages\Asesi\DetailSertifAsesi.vue -->
<script setup>
import AsesiLayout from "@/Layouts/AsesiLayout.vue";
import AsesiSertifikasiMenu from "@/Components/AsesiSertifikasiMenu.vue";
import PendaftarDetailDataStatis from "@/Pages/Admin/PendaftarDetailDataStatis.vue"; // Kita bisa pakai ulang komponen ini
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import EditButton from "@/Components/EditButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import SingleFileInput from "../../Components/SingleFileInput.vue";
import MultiFileInput from "../../Components/MultiFileInput.vue";
import InputError from "@/Components/InputError.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { ref, computed } from 'vue';

const props = defineProps({
    sertification: Object,
    student: Object,
    asesi: Object,
});
// console.log(props.asesi.status);


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
        'daftar': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100',
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
    if (transaction.status === 'belum_bayar') {
        return { text: 'Belum Bayar', class: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100', secondclass: "border-gray-300 dark:border-gray-600" };
    }
    if (transaction.tipe === 'manual' && transaction.bukti_bayar && transaction.status === 'pending') {
        return { text: 'Menunggu Verifikasi', class: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100', secondclass: "border-yellow-300 dark:border-yellow-700" };
    }
    if (transaction.tipe === 'manual' && transaction.status === 'bukti_pembayaran_terverifikasi') {
        return { text: 'Pembayaran Terverifikasi', class: 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100', secondclass: "border-green-300 dark:border-green-700" };
    }
    if (transaction.tipe === 'manual' && transaction.status === 'bukti_pembayaran_ditolak') {
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
const removeFileStudent = (fieldName) => {
    if (form[fieldName]) {
        form[fieldName] = null;
    }
    else if (props.student[fieldName] && !form.delete_files_student.includes(fieldName)) {
        form.delete_files_student.push(fieldName);
    }
};
const removeFileAsesi = (fieldName) => {
    if (form[fieldName]) {
        form[fieldName] = null;
    }
    else if (props.asesi[fieldName] && !form.delete_files_asesi.includes(fieldName)) {
        form.delete_files_asesi.push(fieldName);
    }
};
</script>

<template>
    <AsesiLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Detail Pengajuan: {{ sertification.skema.nama_skema }}
            </h2>
        </template>

        <AsesiSertifikasiMenu :sertification-id="props.sertification.id" :asesi="props.asesi"
            :latest-transaction="props.asesi.latest_transaction" />

        <div class="max-w-7xl mx-auto">
            <div v-if="isEditing" class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <form @submit.prevent="update" class="mt-6 space-y-6">
                    <h3 class="dark:text-gray-300 font-semibold">A. Data Pribadi</h3>
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

                    <!-- Data Pekerjaan -->
                    <h3 class="dark:text-gray-300 font-semibold pt-4">B. Data Pekerjaan Sekarang</h3>
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
                    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-md">
                        <InputLabel value="Mata Kuliah terkait" required />
                        <div v-for="(makul, index) in form.makulNilais" :key="index"
                            class="flex items-center gap-2 mb-2">
                            <TextInput v-model="makul.nama_makul" placeholder="Nama Mata Kuliah" class="flex-grow"
                                required />
                            <TextInput v-model="makul.nilai_makul" placeholder="Nilai" class="w-1/4" required />
                            <button type="button" @click="removeMakul(index)" v-show="form.makulNilais.length > 1"
                                class="p-2 text-red-500 hover:text-red-700 dark:hover:text-red-400 cursor-pointer">
                                <FontAwesomeIcon icon="fa-solid fa-trash" />
                            </button>
                        </div>
                        <button type="button" @click="addMakul" class="mt-2 text-sm font-medium text-blue-600">+ Tambah
                            Mata
                            Kuliah</button>
                    </div>

                    <!-- Bukti Kelengkapan -->
                    <h3 class="dark:text-gray-300 font-semibold pt-4">D. Bukti Kelengkapan</h3>
                    <SingleFileInput v-model="form.apl_1" label="Form APL.01" is-label-required
                        :template-url="`/storage/${sertification.skema.format_apl_1}`"
                        :existing-file-url="asesi?.apl_1 ? `/storage/${asesi.apl_1}` : null"
                        :is-marked-for-deletion="form.delete_files_asesi.includes('apl_1')" accept=".pdf,.doc,.docx"
                        :error="form.errors.apl_1" @remove="removeFileAsesi('apl_1')"
                        :required="!asesi?.apl_1 || form.delete_files_asesi.includes('apl_1')" />
                    <SingleFileInput v-model="form.apl_2" label="Form APL.02" is-label-required
                        :template-url="`/storage/${sertification.skema.format_apl_2}`"
                        :existing-file-url="asesi?.apl_2 ? `/storage/${asesi.apl_2}` : null"
                        :is-marked-for-deletion="form.delete_files_asesi.includes('apl_2')" accept=".pdf,.doc,.docx"
                        :error="form.errors.apl_2" @remove="removeFileAsesi('apl_2')"
                        :required="!asesi?.apl_2 || form.delete_files_asesi.includes('apl_2')" />
                        
                        
                    <SingleFileInput v-model="form.foto_ktp" label="Scan KTP" is-label-required
                        :existing-file-url="student?.foto_ktp ? `/storage/${student.foto_ktp}` : null"
                        :is-marked-for-deletion="form.delete_files_student.includes('foto_ktp')" accept=".jpg,.png,.jpeg,.pdf"
                        :error="form.errors.foto_ktp" @remove="removeFileStudent('foto_ktp')"
                        :required="!student?.foto_ktp || form.delete_files_student.includes('foto_ktp')" />
                    <SingleFileInput v-model="form.pas_foto"
                        label="Pasfoto terbaru dengan latar belakang merah, berukuran 4x6 (ukuran file maksimal 1 MB)"
                        is-label-required :existing-file-url="student?.pas_foto ? `/storage/${student.pas_foto}` : null"
                        :is-marked-for-deletion="form.delete_files_student.includes('pas_foto')" accept=".jpg,.png,.jpeg,.pdf"
                        :error="form.errors.pas_foto" @remove="removeFileStudent('pas_foto')"
                        :required="!student?.pas_foto || form.delete_files_student.includes('pas_foto')" />
                    <SingleFileInput v-model="form.foto_ktm" label="Scan KTM (ukuran file maksimal 1 MB)"
                        is-label-required :existing-file-url="asesi?.foto_ktm ? `/storage/${asesi.foto_ktm}` : null"
                        :is-marked-for-deletion="form.delete_files_asesi.includes('foto_ktm')" accept=".jpg,.png,.jpeg,.pdf"
                        :error="form.errors.foto_ktm" @remove="removeFileAsesi('foto_ktm')"
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
                <div v-if="asesi.status === 'perlu_perbaikan_berkas' && asesi.catatan_perbaikan"
                    class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300 border border-yellow-300 dark:border-yellow-800"
                    role="alert">
                    <span class="font-medium">Perhatian!</span> Admin meminta perbaikan berkas dengan catatan:
                    <p class="mt-2 font-mono">{{ asesi.catatan_perbaikan }}</p>
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