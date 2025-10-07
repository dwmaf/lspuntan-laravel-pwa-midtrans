<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import FileInput from '@/Components/FileInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    user: Object,
    student: Object,
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    id: props.student?.id,
    name: props.user?.name,
    nik: props.student?.nik,
    tmpt_lhr: props.student?.tmpt_lhr,
    tgl_lhr: props.student?.tgl_lhr,
    kelamin: props.student?.kelamin,
    kebangsaan: props.student?.kebangsaan,
    no_tlp_hp: props.user?.no_tlp_hp,
    kualifikasi_pendidikan: props.student?.kualifikasi_pendidikan,
    
    // File fields diinisialisasi sebagai null
    foto_ktp: null,
    foto_ktm: null,
    pas_foto: null,
    kartu_hasil_studi: null, // Akan menjadi array jika multiple
});
const submit = () => {
    form.post(route('profile_asesi.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Profile Information
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Update your account's profile information and email address.
            </p>
        </header>

        <form @submit.prevent="submit" class="mt-6 space-y-6">
            <div>
                <InputLabel value="Nama Lengkap Sesuai KTP" required />
                <TextInput type="text"  v-model="form.name" required autofocus />
                <InputError :message="form.errors.name" />
            </div>

            <!-- NIK -->
            <div>
                <InputLabel for="nik" value="NIK" required />
                <TextInput id="nik" type="text" v-model="form.nik" required />
                <InputError  :message="form.errors.nik" />
            </div>

            <!-- Tempat & Tanggal Lahir -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <InputLabel for="tmpt_lhr" value="Tempat Lahir" required />
                    <TextInput id="tmpt_lhr" type="text"  v-model="form.tmpt_lhr" required />
                    <InputError  :message="form.errors.tmpt_lhr" />
                </div>
                <div>
                    <InputLabel for="tgl_lhr" value="Tanggal Lahir" required />
                    <DateInput id="tgl_lhr"  v-model="form.tgl_lhr" required />
                    <InputError  :message="form.errors.tgl_lhr" />
                </div>
            </div>

            <!-- Kelamin & Kebangsaan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <InputLabel for="kelamin" value="Kelamin" required />
                    <select id="kelamin" v-model="form.kelamin" required class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="Laki-laki">Laki-Laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                    <InputError  :message="form.errors.kelamin" />
                </div>
                <div>
                    <InputLabel for="kebangsaan" value="Kebangsaan" required />
                    <TextInput id="kebangsaan" type="text"  v-model="form.kebangsaan" required />
                    <InputError  :message="form.errors.kebangsaan" />
                </div>
            </div>

            <!-- No HP & Kualifikasi Pendidikan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <InputLabel for="no_tlp_hp" value="No tlp HP(WA)" required />
                    <TextInput id="no_tlp_hp" type="text" v-model="form.no_tlp_hp" required />
                    <InputError  :message="form.errors.no_tlp_hp" />
                </div>
                <div>
                    <InputLabel for="kualifikasi_pendidikan" value="Kualifikasi Pendidikan (Isi Mahasiswa S1)" required />
                    <TextInput id="kualifikasi_pendidikan" type="text"  v-model="form.kualifikasi_pendidikan" required />
                    <InputError  :message="form.errors.kualifikasi_pendidikan" />
                </div>
            </div>

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 pt-4 border-t border-gray-200 dark:border-gray-700">Data Sertifikasi</h2>

            <!-- Foto KTP -->
            <div>
                <InputLabel for="foto_ktp" value="Foto KTP" :required="!student?.foto_ktp" />
                <p v-if="student?.foto_ktp" class="text-sm text-gray-500 mt-1">
                    File sudah ada: <a :href="`/storage/${student.foto_ktp}`" class="text-blue-500" target="_blank">Lihat File</a>
                </p>
                <FileInput id="foto_ktp" v-model="form.foto_ktp" accept="image/jpeg,image/png,application/pdf" />
                <InputError  :message="form.errors.foto_ktp" />
            </div>

            <!-- Foto KTM -->
            <div>
                <InputLabel for="foto_ktm" value="Foto KTM" :required="!student?.foto_ktm" />
                <p v-if="student?.foto_ktm" class="text-sm text-gray-500 mt-1">
                    File sudah ada: <a :href="`/storage/${student.foto_ktm}`" class="text-blue-500" target="_blank">Lihat File</a>
                </p>
                <FileInput id="foto_ktm" v-model="form.foto_ktm" accept="image/jpeg,image/png,application/pdf" />
                <InputError  :message="form.errors.foto_ktm" />
            </div>

            <!-- Pas Foto -->
            <div>
                <InputLabel for="pas_foto" value="Pas Foto Terbaru 4x6 Latar Belakang Merah" :required="!student?.pas_foto" />
                <p v-if="student?.pas_foto" class="text-sm text-gray-500 mt-1">
                    File sudah ada: <a :href="`/storage/${student.pas_foto}`" class="text-blue-500" target="_blank">Lihat File</a>
                </p>
                <FileInput id="pas_foto" v-model="form.pas_foto" accept="image/jpeg,image/png" />
                <InputError  :message="form.errors.pas_foto" />
            </div>

            <!-- Kartu Hasil Studi -->
            <div>
                <InputLabel for="kartu_hasil_studi" value="Kartu Hasil Studi (dari semester I-V, maks 5 file)" :required="!student?.studentattachmentfiles?.length" />
                <div v-if="student?.studentattachmentfiles?.length" class="mt-2 mb-2 text-sm text-gray-600 dark:text-gray-400">
                    <p class="font-semibold">File yang sudah diunggah:</p>
                    <ul class="list-disc list-inside pl-2">
                        <li v-for="file in student.studentattachmentfiles" :key="file.id">
                            <a :href="`/storage/${file.path_file}`" class="text-blue-500 hover:underline" target="_blank">Lihat file</a>
                        </li>
                    </ul>
                </div>
                <FileInput id="kartu_hasil_studi" v-model="form.kartu_hasil_studi" accept="image/jpeg,image/png,application/pdf" multiple />
                <InputError  :message="form.errors.kartu_hasil_studi" />
                <InputError  :message="form.errors['kartu_hasil_studi.0']" />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
                <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0" leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">Saved.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
