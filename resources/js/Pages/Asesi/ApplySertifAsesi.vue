<script setup>
import AsesiLayout from "@/Layouts/AsesiLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { useForm, Link } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    sertification: Object,
    student: Object,
    user: Object,
});

// Inisialisasi form dengan data yang ada dari props
const form = useForm({
    sertification_id: props.sertification.id,
    student_id: props.student.id,
    name: props.user?.name || '',
    nik: props.student?.nik || '',
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
    tujuan_sert: '',
    makulNilais: [{ nama_makul: '', nilai_makul: '' }], // Untuk input dinamis
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

// Fungsi untuk input dinamis Mata Kuliah
const addMakul = () => {
    form.makulNilais.push({ nama_makul: '', nilai_makul: '' });
};
const removeMakul = (index) => {
    form.makulNilais.splice(index, 1);
};

// Fungsi untuk submit form
const submit = () => {
    form.post(route('asesi.sertifikasi.apply.store'), {
        onError: (errors) => {
            // Bisa tambahkan logika jika ada error, misal scroll ke error pertama
            console.log(errors);
        },
    });
};

// Helper untuk file yang sudah ada
const getExistingFiles = (type) => {
    return props.student?.studentattachmentfile?.filter(file => file.type === type) || [];
};
const khsFiles = getExistingFiles('kartu_hasil_studi');

</script>

<template>
    <AsesiLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Pendaftaran Sertifikasi
            </h2>
        </template>

        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">
                Daftar Sertifikasi: {{ sertification.skema.nama_skema }}
            </h3>
            
            <form @submit.prevent="submit" class="mt-6 space-y-6">
                <!-- Data Pribadi -->
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
                    <select id="kelamin" v-model="form.kelamin" required class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="Laki-laki">Laki-Laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                    <InputError :message="form.errors.kelamin"/>
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
                    <InputLabel value="No. Tlp Rumah"  />
                    <TextInput v-model="form.no_tlp_rmh" type="text"  />
                    <InputError :message="form.errors.no_tlp_rmh" />
                </div>
                <div>
                    <InputLabel value="No. Tlp Kantor"  />
                    <TextInput v-model="form.no_tlp_kntr" type="text"  />
                    <InputError :message="form.errors.no_tlp_kntr" />
                </div>
                <div>
                    <InputLabel value="Kualifikasi Pendidikan (tulis: Mahasiswa S1)"  />
                    <TextInput v-model="form.kualifikasi_pendidikan" type="text" required />
                    <InputError :message="form.errors.kualifikasi_pendidikan" />
                </div>

                <!-- Data Pekerjaan -->
                <h3 class="dark:text-gray-300 font-semibold pt-4">B. Data Pekerjaan Sekarang</h3>
                <div>
                    <InputLabel value="Nama Institusi/Perusahaan"  />
                    <TextInput v-model="form.nama_institusi" type="text"/>
                    <InputError :message="form.errors.nama_institusi" />
                </div>
                <div>
                    <InputLabel value="Jabatan"/>
                    <TextInput v-model="form.jabatan" type="text"/>
                    <InputError :message="form.errors.jabatan" />
                </div>
                <div>
                    <InputLabel value="Alamat Kantor"/>
                    <TextInput v-model="form.alamat_kantor" type="text"/>
                    <InputError :message="form.errors.alamat_kantor" />
                </div>
                <div>
                    <InputLabel value="No. Tlp/Email/Fax"/>
                    <TextInput v-model="form.no_tlp_email_fax" type="text"/>
                    <InputError :message="form.errors.no_tlp_email_fax" />
                </div>

                <!-- Data Sertifikasi -->
                <h3 class="dark:text-gray-300 font-semibold pt-4">C. Data Sertifikasi</h3>
                <div>
                    <InputLabel value="Tujuan Sertifikasi" required />
                    <select v-model="form.tujuan_sert" required class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:border-indigo-600 focus:ring-indigo-600 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="" disabled>--Pilih tujuan sertifikasi--</option>
                        <option value="Sertifikasi">Sertifikasi</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                    <InputError :message="form.errors.tujuan_sert"/>
                </div>

                <!-- Mata Kuliah Dinamis -->
                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-md">
                    <InputLabel value="Mata Kuliah terkait Skema Sertifikasi dan Nilai" required />
                    <div v-for="(makul, index) in form.makulNilais" :key="index" class="flex items-center gap-2 mb-2">
                        <TextInput v-model="makul.nama_makul" placeholder="Nama Mata Kuliah" class="flex-grow" required />
                        <TextInput v-model="makul.nilai_makul" placeholder="Nilai (e.g., A)" class="w-1/4" required />
                        <button type="button" @click="removeMakul(index)" v-show="form.makulNilais.length > 1" class="p-2 text-red-500 hover:text-red-700 dark:hover:text-red-400 cursor-pointer">
                            <FontAwesomeIcon icon="fa-solid fa-trash" />
                        </button>
                    </div>
                    <button type="button" @click="addMakul" class="mt-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-300 cursor-pointer">+ Tambah Mata Kuliah</button>
                    <InputError :message="form.errors[`makulNilais.${index}.nama_makul`]" v-for="(makul, index) in form.makulNilais" :key="`err-nama-${index}`" />
                    <InputError :message="form.errors[`makulNilais.${index}.nilai_makul`]" v-for="(makul, index) in form.makulNilais" :key="`err-nilai-${index}`" />
                </div>

                <!-- Bukti Kelengkapan -->
                <h3 class="dark:text-gray-300 font-semibold pt-4">D. Bukti Kelengkapan</h3>
                <div>
                    <InputLabel value="Form APL.01" required />
                    <a :href="`/storage/${sertification.skema.format_apl_1}`" class="text-blue-500 hover:text-blue-300 text-sm" target="_blank">Lihat Template</a>
                    <TextInput @input="form.apl_1 = $event.target.files[0]" type="file" required/>
                    <!-- <input @input="form.apl_1 = $event.target.files[0]" type="file" class="mt-1 w-full ..." required /> -->
                    <InputError :message="form.errors.apl_1" />
                </div>
                <div>
                    <InputLabel value="Form APL.02" required />
                    <a :href="`/storage/${sertification.skema.format_apl_2}`" class="text-blue-500 hover:text-blue-300 text-sm" target="_blank">Lihat Template</a>
                    <TextInput @input="form.apl_2 = $event.target.files[0]" type="file" required/>
                    <InputError :message="form.errors.apl_2" />
                </div>
                <!-- Contoh untuk file KTP (dengan kondisi) -->
                <div>
                    <InputLabel value="Scan KTP" :required="!student.foto_ktp" />
                    <p v-if="student.foto_ktp" class="text-sm text-gray-500 mb-1">File sudah ada: <a :href="`/storage/${student.foto_ktp}`" class="text-blue-500" target="_blank">Lihat File</a></p>
                    <TextInput @input="form.foto_ktp = $event.target.files[0]" type="file" :required="!student.foto_ktp"/>
                    <!-- <input id="foto_ktp" @input="form.foto_ktp = $event.target.files[0]" type="file" class="mt-1 w-full ..." :required="!student.foto_ktp" /> -->
                    <InputError :message="form.errors.foto_ktp" />
                </div>
                <div>
                    <InputLabel value="Scan KTM (ukuran file maksimal 1 MB)" :required="!student.foto_ktm" />
                    <p v-if="student.foto_ktm" class="text-sm text-gray-500 mb-1">File sudah ada: <a :href="`/storage/${student.foto_ktm}`" class="text-blue-500" target="_blank">Lihat File</a></p>
                    <TextInput @input="form.foto_ktm = $event.target.files[0]" type="file" :required="!student.foto_ktm"/>
                    <InputError :message="form.errors.foto_ktm" />
                </div>
                <!-- Contoh untuk file KHS (multiple) -->
                <div>
                    <InputLabel value="Scan Kartu Hasil Studi (Bisa upload lebih dari satu)" :required="khsFiles.length === 0" />
                    <div v-if="khsFiles.length > 0" class="text-sm text-gray-600 dark:text-gray-400">
                        <p>File yang sudah diunggah:</p>
                        <ul><li v-for="file in khsFiles" :key="file.id"><a class="text-blue-500 hover:text-blue-300" :href="`/storage/${file.path_file}`" target="_blank">Lihat File</a></li></ul>
                    </div>
                    <TextInput @input="form.kartu_hasil_studi = $event.target.files" type="file" multiple :required="khsFiles.length === 0"/>
                    <InputError :message="form.errors.kartu_hasil_studi" />
                </div>
                <div>
                    <InputLabel value="Pasfoto terbaru dengan latar belakang merah, berukuran 4x6 (ukuran file maksimal 1 MB)" :required="!student.pas_foto" />
                    <p v-if="student.pas_foto" class="text-sm text-gray-500 mb-1">File sudah ada: <a :href="`/storage/${student.pas_foto}`" class="text-blue-500" target="_blank">Lihat File</a></p>
                    <TextInput @input="form.pas_foto = $event.target.files[0]" type="file" :required="!student.pas_foto"/>
                    <InputError :message="form.errors.pas_foto" />
                </div>
                <div>
                    <InputLabel value="Scan Surat Keterangan Magang/PKL/MBKM (maks 5, ukuran file maksimal 3 MB)"/>
                    <TextInput @input="form.surat_ket_magang = $event.target.files" type="file" multiple/>
                    <InputError :message="form.errors.surat_ket_magang" />
                </div>
                <div>
                    <InputLabel value="Dokumen pendukung lainnya: dapat berupa Laporan kegiatan PKL/Magang/MBKM/Publikasi Jurnal/dll (maks 5, ukuran file maksimal 5 MB)"/>
                    <TextInput @input="form.dok_pendukung_lain = $event.target.files" type="file" multiple/>
                    <InputError :message="form.errors.dok_pendukung_lain" />
                </div>

                <div class="flex items-center gap-4 pt-4">
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Daftar
                    </PrimaryButton>
                    <SecondaryButton :href="route('asesi.sertifikasi.index')">
                        Daftar
                    </SecondaryButton>
                </div>
            </form>
        </div>
    </AsesiLayout>
</template>