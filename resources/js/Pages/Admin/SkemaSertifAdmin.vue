<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import CustomHeader from '@/Components/CustomHeader.vue';
import InputError from "@/Components/Input/InputError.vue";
import InputLabel from "@/Components/Input/InputLabel.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue";
import TextInput from "@/Components/Input/TextInput.vue";
import SingleFileInput from "@/Components/Input/SingleFileInput.vue";
import AddButton from "@/Components/Button/AddButton.vue";
import EditButton from "@/Components/Button/EditButton.vue";
import DeleteButton from "@/Components/Button/DeleteButton.vue";
import Checkbox from "@/Components/Input/Checkbox.vue";
import { useForm, usePage, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
    skemas: Array,
});


const formMode = ref('list');
const page = usePage();


const form = useForm({
    id: null,
    nama_skema: '',
    is_active: true,
    format_apl_1: null,
    format_apl_2: null,
    delete_files: [],
    _method: 'POST',
});


const showCreateForm = () => {
    form.reset();
    form.is_active = true;
    form._method = 'POST';
    formMode.value = 'create';
};

const showEditForm = (skema) => {
    form.id = skema.id;
    form.nama_skema = skema.nama_skema;
    form.is_active = Boolean(skema.is_active);
    form.format_apl_1 = null;
    form.format_apl_2 = null;
    formMode.value = 'edit';
    form._method = 'PATCH';
};

const backToList = () => {
    formMode.value = 'list';
    form.reset();
    form.clearErrors();
};

const save = () => {
    const options = {
        // Setelah sukses, muat ulang props dari server (termasuk 'skemas')
        // tapi pertahankan state lokal komponen (seperti 'formMode').
        preserveState: true,
        onSuccess: () => backToList(),
    };
    if (formMode.value === 'create') {
        form.post(route('admin.skema.store'), options);
    } else if (formMode.value === 'edit') {
        form.post(route('admin.skema.update', form.id), options);
    }
};

const destroy = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus skema ini?')) {
        router.delete(route('admin.skema.destroy', id));
    }
};

</script>

<template>
    <AdminLayout>

        <CustomHeader judul="Manajemen Skema Sertifikasi" />

        <!-- Form Tambah -->
        <div v-if="formMode === 'create'" class="p-4 sm:p-8 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                Tambah Skema Sertifikasi
            </h2>
            <form @submit.prevent="save" class="mt-4 flex flex-col gap-4">
                <TextInput id="nama_skema" label="Nama Skema" v-model="form.nama_skema" type="text" required
                    :error="form.errors.nama_skema" />
                <SingleFileInput v-model="form.format_apl_1" v-model:deleteList="form.delete_files"
                    delete-identifier="format_apl_1" label="Format APL.01" accept=".jpg,.png,.jpeg,.pdf,.doc,.docx"
                    :error="form.errors.format_apl_1" />
                <SingleFileInput v-model="form.format_apl_2" v-model:deleteList="form.delete_files"
                    delete-identifier="format_apl_2" label="Format APL.02" accept=".jpg,.png,.jpeg,.pdf,.doc,.docx"
                    :error="form.errors.format_apl_2" />
                <div class="flex items-center gap-2">
                    <Checkbox id="is_active" v-model:checked="form.is_active" />
                    <InputLabel for="is_active" value="Skema Aktif (Muncul saat pendaftaran sertifikasi baru)" />
                </div>
                <div class="flex items-center gap-4">
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Simpan
                    </PrimaryButton>
                    <SecondaryButton type="button" @click="backToList">Batal</SecondaryButton>
                </div>
            </form>
        </div>
        <div v-if="formMode === 'edit'" class="p-4 sm:p-8 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                Edit Skema Sertifikasi
            </h2>
            <form @submit.prevent="save" class="mt-4 flex flex-col gap-4">
                <TextInput id="nama_skema" label="Nama Skema" v-model="form.nama_skema" type="text" required
                    :error="form.errors.nama_skema" />
                <SingleFileInput v-model="form.format_apl_1" label="Format APL.01"
                    v-model:deleteList="form.delete_files" delete-identifier="format_apl_1"
                    :existing-file-url="props.skemas.find(s => s.id === form.id)?.format_apl_1 ? `/storage/${props.skemas.find(s => s.id === form.id)?.format_apl_1}` : null"
                    :is-marked-for-deletion="form.delete_files.includes('format_apl_1')" accept=".pdf,.doc,.docx"
                    :error="form.errors.format_apl_1" />
                <SingleFileInput v-model="form.format_apl_2" label="Format APL.02"
                    v-model:deleteList="form.delete_files" delete-identifier="format_apl_2"
                    :existing-file-url="props.skemas.find(s => s.id === form.id)?.format_apl_2 ? `/storage/${props.skemas.find(s => s.id === form.id)?.format_apl_2}` : null"
                    :is-marked-for-deletion="form.delete_files.includes('format_apl_2')" accept=".pdf,.doc,.docx"
                    :error="form.errors.format_apl_2" />
                <div class="flex items-center gap-2">
                    <Checkbox id="is_active" v-model:checked="form.is_active" />
                    <InputLabel for="is_active" value="Skema Aktif (Muncul saat pendaftaran sertifikasi baru)" />
                </div>
                <div class="flex items-center gap-4">
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Simpan
                    </PrimaryButton>
                    <SecondaryButton type="button" @click="backToList">Batal</SecondaryButton>
                </div>
            </form>
        </div>

        <!-- Tampilan Daftar -->
        <div v-if="formMode === 'list'" class="p-4 sm:p-8 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Daftar Skema</h2>
                <AddButton @click="showCreateForm">Tambah Skema</AddButton>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                No</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Skema</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Format File APL</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="(skema, index) in skemas" :key="skema.id">
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ index + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">
                                {{ skema.nama_skema }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">
                                <div class="flex flex-col">
                                    <a v-if="skema.format_apl_1" :href="`/storage/${skema.format_apl_1}`"
                                        target="_blank" class="text-blue-500 hover:text-blue-700">APL.01</a>
                                    <p v-else class="text-gray-400">Format APL.01 belum ada</p>
                                    <a v-if="skema.format_apl_2" :href="`/storage/${skema.format_apl_2}`"
                                        target="_blank" class="text-blue-500 hover:text-blue-700">APL.02</a>
                                    <p v-else class="text-gray-400">Format APL.02 belum ada</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">
                                <span v-if="skema.is_active"
                                    class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">Aktif</span>
                                <span v-else
                                    class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-500 rounded-full">Non-Aktif</span>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <EditButton @click="showEditForm(skema)">Edit</EditButton>
                                    <DeleteButton @click="destroy(skema.id)">Hapus</DeleteButton>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="skemas.length === 0">
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                Belum ada skema sertifikasi.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>