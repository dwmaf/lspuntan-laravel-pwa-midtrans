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
import InputError from "@/Components/InputError.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { ref } from 'vue';

const props = defineProps({
    sertification: Object,
    asesi: Object,
});

// State untuk mode edit
const isEditing = ref(false);

// Inisialisasi form dengan data yang ada
const form = useForm({
    _method: 'PUT', // Untuk update
    name: props.asesi.student.user.name,
    nik: props.asesi.student.nik,
    tmpt_lhr: props.student?.tmpt_lhr || '',
    tgl_lhr: props.student?.tgl_lhr || '',
    kelamin: props.student?.kelamin || 'Laki-laki',
    kebangsaan: props.student?.kebangsaan || '',
    no_tlp_hp: props.user?.no_tlp_hp || '',
    no_tlp_rmh: props.student?.no_tlp_rmh || '',
    no_tlp_kntr: props.student?.no_tlp_kntr || '',
    kualifikasi_pendidikan: props.student?.kualifikasi_pendidikan || 'Mahasiswa S1',
    nama_institusi: props.student?.nama_institusi || '',
    jabatan: props.student?.jabatan || '',
    alamat_kantor: props.student?.alamat_kantor || '',
    no_tlp_email_fax: props.student?.no_tlp_email_fax || '',
    tujuan_sert: props.asesi.tujuan_sert,
    makulNilais: props.asesi.makulnilais.length > 0 ? props.asesi.makulnilais.map(m => ({ nama_makul: m.nama_makul, nilai_makul: m.nilai_makul })) : [{ nama_makul: '', nilai_makul: '' }],
    apl_1: null,
    apl_2: null,
    foto_ktp: null,
    foto_ktm: null,
    kartu_hasil_studi: [],
    pas_foto: null,
    surat_ket_magang: [],
    sertif_pelatihan: [],
    dok_pendukung_lain: [],
});

// Fungsi untuk masuk/keluar mode edit
const enterEditMode = () => {
    isEditing.value = true;
};
const cancelEdit = () => {
    isEditing.value = false;
    form.reset(); // Reset form ke nilai awal
    form.clearErrors();
};

// Fungsi untuk submit form update
const update = () => {
    form.post(route('asesi.sertifikasi.applied.update', { sert_id: props.sertification.id, asesi_id: props.asesi.id }), {
        onSuccess: () => cancelEdit(),
        preserveScroll: true,
    });
};

// Fungsi untuk input dinamis Mata Kuliah
const addMakul = () => form.makulNilais.push({ nama_makul: '', nilai_makul: '' });
const removeMakul = (index) => form.makulNilais.splice(index, 1);

// Notifikasi
const notification = computed(() => usePage().props.flash?.message || usePage().props.flash?.Success);
// Helper Status
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
    if (transaction.status === 'belum bayar') {
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
</script>

<template>
    <AsesiLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Detail Pengajuan: {{ sertification.skema.nama_skema }}
            </h2>
        </template>

        <!-- Notifikasi -->
        <div v-if="notification" class="fixed top-20 right-4 text-sm px-4 py-2 rounded bg-green-600 text-white z-50">
            {{ notification }}
        </div>

        <AsesiSertifikasiMenu :sertification-id="props.sertification.id" :asesi="props.asesi" :latest-transaction="props.asesi.latest_transaction" />

        <div class="max-w-7xl mx-auto">
            <!-- Tampilan Form Edit -->
            <div v-if="isEditing" class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <form @submit.prevent="update" class="mt-6 space-y-6">
                    <!-- Semua field form di sini, mirip dengan ApplySertifAsesi.vue -->
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
                    <div>
                        <InputLabel value="Form APL.01" required />
                        <a :href="`/storage/${sertification.skema.format_apl_1}`"
                            class="text-blue-500 hover:text-blue-300 text-sm" target="_blank">Lihat Template</a>
                        <TextInput @input="form.apl_1 = $event.target.files[0]" type="file" required />
                        <!-- <input @input="form.apl_1 = $event.target.files[0]" type="file" class="mt-1 w-full ..." required /> -->
                        <InputError :message="form.errors.apl_1" />
                    </div>
                    <div>
                        <InputLabel value="Form APL.02" required />
                        <a :href="`/storage/${sertification.skema.format_apl_2}`"
                            class="text-blue-500 hover:text-blue-300 text-sm" target="_blank">Lihat Template</a>
                        <TextInput @input="form.apl_2 = $event.target.files[0]" type="file" required />
                        <InputError :message="form.errors.apl_2" />
                    </div>

                    <div>
                        <InputLabel value="Scan KTP" :required="!student.foto_ktp" />
                        <p v-if="student.foto_ktp" class="text-sm text-gray-500 mb-1">File sudah ada: <a
                                :href="`/storage/${student.foto_ktp}`" class="text-blue-500" target="_blank">Lihat
                                File</a></p>
                        <TextInput @input="form.foto_ktp = $event.target.files[0]" type="file"
                            :required="!student.foto_ktp" />
                        <!-- <input id="foto_ktp" @input="form.foto_ktp = $event.target.files[0]" type="file" class="mt-1 w-full ..." :required="!student.foto_ktp" /> -->
                        <InputError :message="form.errors.foto_ktp" />
                    </div>
                    <div>
                        <InputLabel value="Scan KTM (ukuran file maksimal 1 MB)" :required="!student.foto_ktm" />
                        <p v-if="student.foto_ktm" class="text-sm text-gray-500 mb-1">File sudah ada: <a
                                :href="`/storage/${student.foto_ktm}`" class="text-blue-500" target="_blank">Lihat
                                File</a></p>
                        <TextInput @input="form.foto_ktm = $event.target.files[0]" type="file"
                            :required="!student.foto_ktm" />
                        <InputError :message="form.errors.foto_ktm" />
                    </div>

                    <div>
                        <InputLabel value="Scan Kartu Hasil Studi (Bisa upload lebih dari satu)"
                            :required="khsFiles.length === 0" />
                        <div v-if="khsFiles.length > 0" class="text-sm text-gray-600 dark:text-gray-400">
                            <p>File yang sudah diunggah:</p>
                            <ul>
                                <li v-for="file in khsFiles" :key="file.id"><a class="text-blue-500 hover:text-blue-300"
                                        :href="`/storage/${file.path_file}`" target="_blank">Lihat File</a></li>
                            </ul>
                        </div>
                        <TextInput @input="form.kartu_hasil_studi = $event.target.files" type="file" multiple
                            :required="khsFiles.length === 0" />
                        <InputError :message="form.errors.kartu_hasil_studi" />
                    </div>
                    <div>
                        <InputLabel
                            value="Pasfoto terbaru dengan latar belakang merah, berukuran 4x6 (ukuran file maksimal 1 MB)"
                            :required="!student.pas_foto" />
                        <p v-if="student.pas_foto" class="text-sm text-gray-500 mb-1">File sudah ada: <a
                                :href="`/storage/${student.pas_foto}`" class="text-blue-500" target="_blank">Lihat
                                File</a></p>
                        <TextInput @input="form.pas_foto = $event.target.files[0]" type="file"
                            :required="!student.pas_foto" />
                        <InputError :message="form.errors.pas_foto" />
                    </div>
                    <div>
                        <InputLabel value="Scan Surat Keterangan Magang/PKL/MBKM (maks 5, ukuran file maksimal 3 MB)" />
                        <TextInput @input="form.surat_ket_magang = $event.target.files" type="file" multiple />
                        <InputError :message="form.errors.surat_ket_magang" />
                    </div>
                    <div>
                        <InputLabel
                            value="Dokumen pendukung lainnya: dapat berupa Laporan kegiatan PKL/Magang/MBKM/Publikasi Jurnal/dll (maks 5, ukuran file maksimal 5 MB)" />
                        <TextInput @input="form.dok_pendukung_lain = $event.target.files" type="file" multiple />
                        <InputError :message="form.errors.dok_pendukung_lain" />
                    </div>

                    <div class="flex items-center gap-4">
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Update
                        </PrimaryButton>
                        <SecondaryButton type="button" @click="cancelEdit">Batal</SecondaryButton>
                    </div>
                </form>
            </div>

            <!-- Tampilan Show Detail -->
            <div v-else class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <div class="flex justify-end mb-4">
                    <EditButton @click="enterEditMode">Edit Data</EditButton>
                </div>
                <!-- Menggunakan kembali komponen dari Admin untuk menampilkan data -->
                <PendaftarDetailDataStatis :asesi="props.asesi" />

                <!-- Status Section -->
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
                            <EditButton @click="showStatusModal = true">Ubah Status</EditButton>
                        </dd>
                    </div>
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Status Pembayaran</dt>
                        <dd
                            :class="[mt - 1 text - sm rounded - lg p - 2 border, getPaymentStatusInfo(asesi.latest_transaction).secondclass]">
                            <span
                                :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getPaymentStatusInfo(asesi.latest_transaction).class]">
                                {{ getPaymentStatusInfo(asesi.latest_transaction).text }}
                            </span>
                            <div v-if="props.asesi.latest_transaction" class="flex justify-end">
                                <EditButton @click="showPaymentModal = true">Ubah Status</EditButton>
                            </div>
                        </dd>
                    </div>
                </div>

                <!-- Sertifikat Section -->
                <div v-if="props.asesi.sertifikat">
                    <h3 class="text-md font-semibold dark:text-gray-300 mb-2 border-b pb-1 border-gray-700 mt-6">F.
                        Sertifikat
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                        <div>
                            <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Sertifikat
                                Asesi
                            </dt>
                            <dd class="mt-1 text-sm mr-1 space-y-2">
                                <a :href="`/storage/${asesi.sertifikat.file_path}`"
                                  target="_blank"
                                    class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-semibold">
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