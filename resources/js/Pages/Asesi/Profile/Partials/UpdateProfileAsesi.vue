<script setup>
import PrimaryButton from '@/Components/Button/PrimaryButton.vue';
import SecondaryButton from "@/Components/Button/SecondaryButton.vue";
import TextInput from '@/Components/Input/TextInput.vue';
import EditButton from "@/Components/Button/EditButton.vue";
import SingleFileInput from '@/Components/Input/SingleFileInput.vue';
import SelectInput from '@/Components/Input/SelectInput.vue';

import FileIcon from '@/Components/FileIcon.vue';
import Alert from '@/Components/Alert.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

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

const genderOptions = [
    { value: 'Laki-laki', text: 'Laki-Laki' },
    { value: 'Perempuan', text: 'Perempuan' },
];

const formatDate = (dateString) => {
    if (!dateString) return 'Tidak diisi';
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};

const isProfileIncomplete = computed(() => {
    return !props.user?.name ||
        !props.student?.nik ||
        !props.student?.tmpt_lhr ||
        !props.student?.tgl_lhr ||
        !props.student?.kelamin ||
        !props.student?.kebangsaan ||
        !props.user?.no_tlp_hp ||
        !props.student?.kualifikasi_pendidikan ||
        !props.student?.foto_ktp ||
        !props.student?.pas_foto;
});

const isEditing = ref(false);
const form = useForm({
    _method: 'patch',
    id: props.student?.id,
    name: props.user?.name,
    nik: props.student?.nik,
    tmpt_lhr: props.student?.tmpt_lhr,
    tgl_lhr: props.student?.tgl_lhr,
    kelamin: props.student?.kelamin || '',
    kebangsaan: props.student?.kebangsaan || 'Indonesia',
    no_tlp_hp: props.user?.no_tlp_hp,
    kualifikasi_pendidikan: props.student?.kualifikasi_pendidikan || 'SMA',
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
                <TextInput id="name" label="Nama Lengkap Sesuai KTP" v-model="form.name" type="text" required
                    :error="form.errors.name" />
                <TextInput id="nik" label="No. KTP" v-model="form.nik" type="text" required :error="form.errors.nik" />
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
                <TextInput id="kualifikasi_pendidikan" label="Kualifikasi Pendidikan Terakhir"
                    v-model="form.kualifikasi_pendidikan" type="text" required
                    :error="form.errors.kualifikasi_pendidikan" />
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
        <div v-else>
            <Alert 
            v-if="isProfileIncomplete" 
            type="warning" title="Perhatian:">
                Data profil Anda belum lengkap. Data berikut akan tersinkronisasi ketika anda mendaftar sertifikasi.
            </Alert>

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
                    <dd class="mt-1 text-sm"
                        :class="user?.name ? 'text-gray-900 dark:text-gray-100' : 'text-gray-400 dark:text-gray-500 italic'">
                        {{ user?.name || 'Belum diisi' }}
                    </dd>
                </div>
                <div>
                    <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">No. KTP</dt>
                    <dd class="mt-1 text-sm"
                        :class="student?.nik ? 'text-gray-900 dark:text-gray-100' : 'text-gray-400 dark:text-gray-500 italic'">
                        {{ student?.nik || 'Belum diisi' }}
                    </dd>
                </div>
                <div>
                    <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Tempat, Tanggal Lahir</dt>
                    <dd class="mt-1 text-sm"
                        :class="student?.tmpt_lhr && student?.tgl_lhr ? 'text-gray-900 dark:text-gray-100' : 'text-gray-400 dark:text-gray-500 italic'">
                        {{ student?.tmpt_lhr || 'Belum diisi' }}, {{ formatDate(student?.tgl_lhr) }}
                    </dd>
                </div>
                <div>
                    <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Jenis Kelamin</dt>
                    <dd class="mt-1 text-sm"
                        :class="student?.kelamin ? 'text-gray-900 dark:text-gray-100' : 'text-gray-400 dark:text-gray-500 italic'">
                        {{ student?.kelamin || 'Belum diisi' }}
                    </dd>
                </div>
                <div>
                    <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">No. HP/WA</dt>
                    <dd class="mt-1 text-sm"
                        :class="user?.no_tlp_hp ? 'text-gray-900 dark:text-gray-100' : 'text-gray-400 dark:text-gray-500 italic'">
                        {{ user?.no_tlp_hp || 'Belum diisi' }}
                    </dd>
                </div>
                <div>
                    <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Kebangsaan</dt>
                    <dd class="mt-1 text-sm"
                        :class="student?.kebangsaan ? 'text-gray-900 dark:text-gray-100' : 'text-gray-400 dark:text-gray-500 italic'">
                        {{ student?.kebangsaan || 'Belum diisi' }}
                    </dd>
                </div>
                <div>
                    <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Pendidikan Terakhir</dt>
                    <dd class="mt-1 text-sm"
                        :class="student?.kualifikasi_pendidikan ? 'text-gray-900 dark:text-gray-100' : 'text-gray-400 dark:text-gray-500 italic'">
                        {{ student?.kualifikasi_pendidikan || 'Belum diisi' }}
                    </dd>
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
                        <span v-else class="text-gray-400 dark:text-gray-500 italic">Belum ada file.</span>
                    </dd>
                </div>
            </dl>
        </div>
    </section>
</template>
