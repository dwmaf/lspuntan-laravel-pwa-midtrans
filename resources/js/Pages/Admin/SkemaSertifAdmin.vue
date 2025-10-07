<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import FileInput from "../../Components/FileInput.vue";
import AddButton from "@/Components/AddButton.vue";
import EditButton from "@/Components/EditButton.vue";
import DeleteButton from "@/Components/DeleteButton.vue";
import { useForm, usePage, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
    skemas: Array,
});

// State untuk mengontrol tampilan: 'list', 'create', 'edit'
const formMode = ref('list');
const page = usePage();
const notification = computed(() => page.props.flash?.message);

// Inisialisasi form dengan useForm
const form = useForm({
    id: null,
    nama_skema: '',
    format_apl_1: null,
    format_apl_2: null,
});

// Fungsi untuk menampilkan form tambah
const showCreateForm = () => {
    form.reset();
    formMode.value = 'create';
};

// Fungsi untuk menampilkan form edit
const showEditForm = (skema) => {
    form.id = skema.id;
    form.nama_skema = skema.nama_skema;
    form.format_apl_1 = null; // File input tidak bisa di-prefill
    form.format_apl_2 = null;
    formMode.value = 'edit';
};

// Fungsi untuk kembali ke daftar
const backToList = () => {
    formMode.value = 'list';
    form.reset();
    form.clearErrors();
};

// Fungsi untuk submit (create atau update)
const save = () => {
    if (formMode.value === 'create') {
        form.post(route('admin.skema.store'), {
            onSuccess: () => backToList(),
        });
    } else if (formMode.value === 'edit') {
        // Inertia memerlukan method POST untuk form dengan file
        form.post(route('admin.skema.update', form.id), {
            _method: 'put', // Menambahkan method spoofing
            onSuccess: () => backToList(),
        });
    }
};

// Fungsi untuk menghapus
const destroy = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus skema ini?')) {
        router.delete(route('admin.skema.destroy', id));
    }
};
</script>

<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Skema Sertifikasi
            </h2>
        </template>

        <!-- Form Tambah/Edit -->
        <div v-if="formMode === 'create' || formMode === 'edit'" class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                {{ formMode === 'create' ? 'Tambah Skema Sertifikasi' : 'Edit Skema Sertifikasi' }}
            </h2>
            <form @submit.prevent="save" class="mt-4 flex flex-col gap-4">
                <div>
                    <InputLabel value="Nama Skema" />
                    <TextInput v-model="form.nama_skema" type="text" required />
                    <InputError :message="form.errors.nama_skema" />
                </div>
                <div>
                    <InputLabel value="Format APL.01 (.docx, .pdf)" />
                    <FileInput v-model="form.format_apl_1"/>
                    <InputError :message="form.errors.format_apl_1" />
                </div>
                <div>
                    <InputLabel value="Format APL.02 (.docx, .pdf)" />
                    <FileInput v-model="form.format_apl_2"/>
                    <InputError :message="form.errors.format_apl_2"/>
                </div>
                <div class="flex items-center gap-4">
                    <PrimaryButton :disabled="form.processing">Simpan</PrimaryButton>
                    <SecondaryButton type="button" @click="backToList">Batal</SecondaryButton>
                </div>
            </form>
        </div>

        <!-- Tampilan Daftar -->
        <div v-else class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Daftar Skema</h2>
                <AddButton @click="showCreateForm">Tambah Skema</AddButton>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Skema</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Format File APL</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="(skema, index) in skemas" :key="skema.id">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">{{ skema.nama_skema }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">
                                <div class="flex flex-col">
                                    <a v-if="skema.format_apl_1" :href="`/storage/${skema.format_apl_1}`" target="_blank" class="text-blue-500 hover:text-blue-700">APL.01</a>
                                    <p v-else class="text-gray-400">APL.01 belum ada</p>
                                    <a v-if="skema.format_apl_2" :href="`/storage/${skema.format_apl_2}`" target="_blank" class="text-blue-500 hover:text-blue-700">APL.02</a>
                                    <p v-else class="text-gray-400">APL.02 belum ada</p>
                                </div>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <EditButton @click="showEditForm(skema)">Edit</EditButton>
                                    <DeleteButton @click="destroy(skema.id)">Hapus</DeleteButton>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="skemas.length === 0">
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada skema sertifikasi.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>