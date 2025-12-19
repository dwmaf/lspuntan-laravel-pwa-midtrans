<script setup>
import InputError from '@/Components/Input/InputError.vue';
import InputLabel from '@/Components/Input/InputLabel.vue';
import PrimaryButton from '@/Components/Button/PrimaryButton.vue';
import SecondaryButton from "@/Components/Button/SecondaryButton.vue";
import TextInput from '@/Components/Input/TextInput.vue';
import EditButton from "@/Components/Button/EditButton.vue";
import SingleFileInput from '@/Components/SingleFileInput.vue';
import DateInput from '@/Components/Input/DateInput.vue';
import FileIcon from '@/Components/FileIcon.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    user: Object,
    student: Object,
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
const formatDate = (dateString) => {
    if (!dateString) return 'Tidak diisi';
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};
const isEditing = ref(false);
const form = useForm({
    _method: 'patch',
    id: props.student?.id,
    name: props.user?.name,
    nik: props.student?.nik,
    tmpt_lhr: props.student?.tmpt_lhr,
    tgl_lhr: props.student?.tgl_lhr,
    kelamin: props.student?.kelamin,
    kebangsaan: props.student?.kebangsaan,
    no_tlp_hp: props.user?.no_tlp_hp,
    kualifikasi_pendidikan: props.student?.kualifikasi_pendidikan,
    foto_ktp: null,
    pas_foto: null,
    delete_files: [],
});
const enterEditMode = () => {
    isEditing.value = true;
}
const cancelEdit = () => {
    isEditing.value = false;
    form.reset();
    form.clearErrors();
}
// const removeFile = (fieldName) => {
//     if (form[fieldName]) {
//         form[fieldName] = null;
//     }
//     else if (props.student[fieldName] && !form.delete_files.includes(fieldName)) {
//         form.delete_files.push(fieldName);
//     }
// };
const submit = () => {
    form.post(route('profile_asesi.update'), {
        onSuccess: () => cancelEdit(),
    });
};
</script>

<template>
    <section>
        <div v-if="isEditing">
            <header>
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Data Pribadi
                </h2>
            </header>
            <form @submit.prevent="submit" class="mt-6 space-y-6">
                <div>
                    <InputLabel value="Nama Lengkap Sesuai KTP" required />
                    <TextInput type="text" v-model="form.name" required autofocus />
                    <InputError :message="form.errors.name" />
                </div>
                <div>
                    <InputLabel value="NIK" required />
                    <TextInput type="text" v-model="form.nik" required />
                    <InputError :message="form.errors.nik" />
                </div>
                <div>
                    <InputLabel value="Tempat Lahir" required />
                    <TextInput type="text" v-model="form.tmpt_lhr" required />
                    <InputError :message="form.errors.tmpt_lhr" />
                </div>
                <div>
                    <InputLabel value="Tanggal Lahir" required />
                    <DateInput type="date" v-model="form.tgl_lhr" required />
                    <InputError :message="form.errors.tgl_lhr" />
                </div>
                <div>
                    <InputLabel value="Kelamin" required />
                    <select v-model="form.kelamin" required
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="Laki-laki">Laki-Laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                    <InputError :message="form.errors.kelamin" />
                </div>
                <div>
                    <InputLabel value="Kebangsaan" required />
                    <TextInput type="text" v-model="form.kebangsaan" required />
                    <InputError :message="form.errors.kebangsaan" />
                </div>
                <div>
                    <InputLabel value="No tlp HP(WA)" required />
                    <TextInput type="text" v-model="form.no_tlp_hp" required />
                    <InputError :message="form.errors.no_tlp_hp" />
                </div>
                <div>
                    <InputLabel value="Kualifikasi Pendidikan (Isi Mahasiswa S1)" required />
                    <TextInput type="text" v-model="form.kualifikasi_pendidikan" required />
                    <InputError :message="form.errors.kualifikasi_pendidikan" />
                </div>
                <SingleFileInput v-model="form.foto_ktp" v-model:deleteList="form.delete_files"
                    delete-identifier="foto_ktp" label="Foto KTP" is-label-required
                    :existing-file-url="student?.foto_ktp ? `/storage/${student.foto_ktp}` : null"
                    :is-marked-for-deletion="form.delete_files.includes('foto_ktp')" accept=".jpg,.png,.jpeg,.pdf"
                    :error="form.errors.foto_ktp" @remove="removeFile('foto_ktp')"
                    :required="!student?.foto_ktp || form.delete_files.includes('foto_ktp')" />
                <SingleFileInput v-model="form.pas_foto" v-model:deleteList="form.delete_files"
                    delete-identifier="pas_foto"
                    label="Pasfoto terbaru dengan latar belakang merah, berukuran 4x6 (ukuran file maksimal 1 MB)"
                    is-label-required :existing-file-url="student?.pas_foto ? `/storage/${student.pas_foto}` : null"
                    :is-marked-for-deletion="form.delete_files.includes('pas_foto')" accept=".jpg,.png,.jpeg,.pdf"
                    :error="form.errors.pas_foto" @remove="removeFile('pas_foto')"
                    :required="!student?.pas_foto || form.delete_files.includes('pas_foto')" />
                <div class="flex items-center gap-4">
                    <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
                    <SecondaryButton type="button" @click="cancelEdit">Batal</SecondaryButton>
                </div>
            </form>
        </div>
        <div v-else class="">
            <div class="flex justify-between mb-4">
                <header>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Data Pribadi
                    </h2>
                </header>
                <EditButton @click="enterEditMode">Edit Data</EditButton>
            </div>

            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                <div>
                    <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Nama Lengkap</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ user?.name || 'Belum diisi'
                        }}</dd>
                </div>
                <div>
                    <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">No. KTP</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ student?.nik || 'Belum diisi' }}</dd>
                </div>
                <div>
                    <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Tempat, Tanggal Lahir</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ student?.tmpt_lhr || 'N/A' }}, {{
                        formatDate(student?.tgl_lhr) }}</dd>
                </div>
                <div>
                    <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Jenis Kelamin</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ student?.kelamin || 'Belum diisi' }}
                    </dd>
                </div>
                <div>
                    <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Kebangsaan</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ student?.kebangsaan || 'Belum diisi' }}</dd>
                </div>

            </dl>

            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                <div v-for="file in [
                    { label: 'Scan KTP', path: student?.foto_ktp },
                    { label: 'Pasfoto', path: student?.pas_foto },
                ]" :key="file.label">
                    <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">{{ file.label }}</dt>
                    <dd class="mt-1 text-sm min-w-0">
                        <a v-if="file.path" :href="`/storage/${file.path}`" target="_blank"
                            class="flex items-center gap-2 group min-w-0">
                            <FileIcon :path="file.path" />
                            <span class="text-blue-500 group-hover:text-blue-700 truncate group-hover:underline">{{
                                file.path.split('/').pop() }}</span>
                        </a>
                        <span v-else class="text-gray-900 dark:text-gray-100">Tidak ada file.</span>
                    </dd>
                </div>
            </dl>
        </div>
    </section>
</template>
