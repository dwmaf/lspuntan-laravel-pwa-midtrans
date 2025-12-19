<script setup>
import AsesiLayout from "@/Layouts/AsesiLayout.vue";
import InputError from "@/Components/Input/InputError.vue";
import Header from "@/Components/CustomHeader.vue";
import InputLabel from "@/Components/Input/InputLabel.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import SecondaryLinkButton from "@/Components/SecondaryLinkButton.vue";
import TextInput from "@/Components/Input/TextInput.vue";
import SingleFileInput from "@/Components/Input/SingleFileInput.vue";
import MultiFileInput from "@/Components/Input/MultiFileInput.vue";
import { useForm, Link } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    sertification: Object,
    student: Object,
    user: Object,
});


const form = useForm({
    sertification_id: props.sertification.id,
    student_id: props.student.id,
    name: props.user?.name,
    nik: props.student?.nik,
    tmpt_lhr: props.student?.tmpt_lhr,
    tgl_lhr: props.student?.tgl_lhr,
    kelamin: props.student?.kelamin || '',
    kebangsaan: props.student?.kebangsaan,
    no_tlp_hp: props.user?.no_tlp_hp,
    no_tlp_rmh: props.student?.no_tlp_rmh,
    no_tlp_kntr: props.student?.no_tlp_kntr,
    kualifikasi_pendidikan: props.student?.kualifikasi_pendidikan || 'Mahasiswa S1',
    nama_institusi: props.student?.nama_institusi,
    jabatan: props.student?.jabatan,
    alamat_kantor: props.student?.alamat_kantor,
    no_tlp_email_fax: props.student?.no_tlp_email_fax,
    tujuan_sert: '',
    makulNilais: [{ nama_makul: '', nilai_makul: '' }],
    apl_1: null,
    apl_2: null,
    foto_ktp: null,
    foto_ktm: null,
    kartu_hasil_studi: [],
    pas_foto: null,
    surat_ket_magang: [],
    sertif_pelatihan: [],
    dok_pendukung_lain: [],
    delete_files: [],
});


const addMakul = () => {
    form.makulNilais.push({ nama_makul: '', nilai_makul: '' });
};
const removeMakul = (index) => {
    form.makulNilais.splice(index, 1);
};
// const removeFile = (fieldName) => {
//     if (form[fieldName]) {
//         form[fieldName] = null;
//     }
//     else if (props.student[fieldName] && !form.delete_files.includes(fieldName)) {
//         form.delete_files.push(fieldName);
//     }
// };

const submit = () => {
    form.post(route('asesi.sertifikasi.apply.store', { student_id: props.student.id }));
};

</script>

<template>
    <AsesiLayout>

        <Header judul="Pendaftaran Sertifikasi" />

        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">
                Daftar Sertifikasi: {{ sertification.skema.nama_skema }}
            </h3>

            <form @submit.prevent="submit" class="mt-6 space-y-6">
                <!-- Data Pribadi -->
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
                            <option value="" disabled>--Pilih Kelamin--</option>
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
                        <InputLabel value="Kualifikasi Pendidikan (tulis: Mahasiswa S1)" required />
                        <TextInput v-model="form.kualifikasi_pendidikan" type="text" required />
                        <InputError :message="form.errors.kualifikasi_pendidikan" />
                    </div>
                </div>

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
                                <button @click="removeMakul(index)" type="button" v-show="form.makulNilais.length > 1"
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
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                                clip-rule="evenodd" />
                        </svg>
                        Tambah Mata Kuliah
                    </button>
                    <div v-for="(makul, index) in form.makulNilais">
                        <InputError :message="form.errors[`makulNilais.${index}.nama_makul`]" :key="`err-nama-${index}`"
                            class="mt-1" />
                        <InputError :message="form.errors[`makulNilais.${index}.nilai_makul`]"
                            :key="`err-nilai-${index}`" class="mt-1" />
                    </div>
                </div>

                <!-- Bukti Kelengkapan -->
                <h3 class="dark:text-gray-300 font-semibold pt-4">D. Bukti Kelengkapan</h3>
                <SingleFileInput v-model="form.apl_1" label="Form APL.01" is-label-required
                    :template-url="`/storage/${sertification.skema.format_apl_1}`" accept=".pdf,.doc,.docx"
                    :error="form.errors.apl_1" delete-identifier="apl_1" required />
                <SingleFileInput v-model="form.apl_2" label="Form APL.02" is-label-required
                    :template-url="`/storage/${sertification.skema.format_apl_2}`" accept=".pdf,.doc,.docx"
                    :error="form.errors.apl_2" delete-identifier="apl_2" required />
                <SingleFileInput v-model="form.foto_ktp" label="Scan KTP" is-label-required
                    :existing-file-url="student?.foto_ktp ? `/storage/${student.foto_ktp}` : null"
                    :is-marked-for-deletion="form.delete_files.includes('foto_ktp')" accept=".jpg,.png,.jpeg,.pdf"
                    :error="form.errors.foto_ktp" v-model:deleteList="form.delete_files" delete-identifier="foto_ktp"
                    :required="!student?.foto_ktp || form.delete_files.includes('foto_ktp')" />
                <SingleFileInput v-model="form.pas_foto"
                    label="Pasfoto terbaru dengan latar belakang merah, berukuran 4x6 (ukuran file maksimal 1 MB)"
                    is-label-required :existing-file-url="student?.pas_foto ? `/storage/${student.pas_foto}` : null"
                    :is-marked-for-deletion="form.delete_files.includes('pas_foto')" accept=".jpg,.png,.jpeg,.pdf"
                    :error="form.errors.pas_foto" v-model:deleteList="form.delete_files" delete-identifier="pas_foto"
                    :required="!student?.pas_foto || form.delete_files.includes('pas_foto')" />
                <SingleFileInput v-model="form.foto_ktm" label="Scan KTM (ukuran file maksimal 1 MB)" is-label-required
                    accept=".jpg,.png,.jpeg,.pdf" :error="form.errors.foto_ktm" delete-identifier="foto_ktm" required />
                <MultiFileInput v-model="form.kartu_hasil_studi" label="Scan Kartu Hasil Studi (Semester I - V)"
                    accept=".pdf" :error="form.errors.kartu_hasil_studi"
                    :error-list="form.errors['kartu_hasil_studi.0']" required />
                <MultiFileInput v-model="form.surat_ket_magang"
                    label="Scan Surat Keterangan Magang/PKL/MBKM (ukuran file maksimal 3 MB)" accept="application/pdf"
                    :error="form.errors.surat_ket_magang" :error-list="form.errors['surat_ket_magang.0']" />
                <MultiFileInput v-model="form.dok_pendukung_lain"
                    label="Dokumen pendukung lainnya: dapat berupa Laporan kegiatan PKL/Magang/MBKM/Publikasi Jurnal/dll (ukuran file maksimal 5 MB)"
                    accept="application/pdf" :error="form.errors.dok_pendukung_lain"
                    :error-list="form.errors['dok_pendukung_lain.0']" />
                <MultiFileInput v-model="form.sertif_pelatihan"
                    label="Scan Sertifikat Pelatihan (ukuran file maksimal 3 MB)" accept="application/pdf"
                    :error="form.errors.sertif_pelatihan" :error-list="form.errors['sertif_pelatihan.0']" />
                <div class="flex items-center gap-4 pt-4">
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Daftar
                    </PrimaryButton>
                    <SecondaryLinkButton :href="route('asesi.sertifikasi.index')">
                        Batal
                    </SecondaryLinkButton>
                </div>
            </form>
        </div>
    </AsesiLayout>
</template>