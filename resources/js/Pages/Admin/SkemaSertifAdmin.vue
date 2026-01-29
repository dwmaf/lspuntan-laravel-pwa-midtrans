<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import CustomHeader from '@/Components/CustomHeader.vue';
import InputLabel from "@/Components/Input/InputLabel.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue";
import TextInput from "@/Components/Input/TextInput.vue";
import SingleFileInput from "@/Components/Input/SingleFileInput.vue";
import AddButton from "@/Components/Button/AddButton.vue";
import EditButton from "@/Components/Button/EditButton.vue";
import Pagination from "@/Components/Pagination.vue";
import DeleteButton from "@/Components/Button/DeleteButton.vue";
import Checkbox from "@/Components/Input/Checkbox.vue";
import Alert from "@/Components/Alert.vue";
import { useForm, usePage, router } from "@inertiajs/vue3";
import { ref, computed, reactive, watch } from "vue";

const props = defineProps({
    skemas: Object,
    filters: Object,
});

const filtersForm = reactive({
    search: props.filters.search || '',
});
let searchTimeoutId = null;
watch(() => filtersForm.search, (newValue) => {
    clearTimeout(searchTimeoutId);
    searchTimeoutId = setTimeout(() => {
        router.get(route('admin.skema.create'), filtersForm, {
            preserveState: true,
            replace: true,
        });
    }, 500);
});

const formMode = ref('list');
const page = usePage();


const form = useForm({
    id: null,
    nama_skema: '',
    is_active: true,
    format_apl_1: null,
    format_apl_2: null,
    format_ak_1: null,
    format_ak_2: null,
    format_ak_3: null,
    format_ak_4: null,
    format_ac_1: null,
    format_map_1: null,
    format_ia_1: null,
    format_ia_2: null,
    format_ia_5: null,
    format_ia_6: null,
    format_ia_7: null,
    delete_files: [],
    _method: 'POST',
});


const docFields = [
    { id: 'format_apl_1', label: 'FR. APL.01' },
    { id: 'format_apl_2', label: 'FR. APL.02' },
    { id: 'format_ak_1', label: 'FR. AK.01' },
    { id: 'format_ak_2', label: 'FR. AK.02' },
    { id: 'format_ak_3', label: 'FR. AK.03' },
    { id: 'format_ak_4', label: 'FR. AK.04' },
    { id: 'format_ac_1', label: 'FR. AC.01' },
    { id: 'format_map_1', label: 'FR. MAP.01' },
    { id: 'format_ia_1', label: 'FR. IA.01' },
    { id: 'format_ia_2', label: 'FR. IA.02' },
    { id: 'format_ia_5', label: 'FR. IA.05' },
    { id: 'format_ia_6', label: 'FR. IA.06' },
    { id: 'format_ia_7', label: 'FR. IA.07' },
];

const showCreateForm = () => {
    form.reset();
    form.is_active = true;
    form._method = 'POST';
    formMode.value = 'create';
};

const showEditForm = (skema) => {
    form.reset();
    form.id = skema.id;
    form.nama_skema = skema.nama_skema;
    form.is_active = Boolean(skema.is_active);
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

        <CustomHeader judul="Manajemen Skema Sertifikasi">
            <div class="flex" v-if="formMode === 'list'">
                <AddButton @click="showCreateForm">Tambah Skema</AddButton>
            </div>
        </CustomHeader>

        <!-- Form Tambah -->
        <div v-if="formMode === 'create'" class="p-4 sm:p-8 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                Tambah Skema Sertifikasi
            </h2>
            <form @submit.prevent="save" class="mt-4 flex flex-col gap-4">
                <TextInput id="nama_skema" label="Nama Skema" v-model="form.nama_skema" type="text" required
                    :error="form.errors.nama_skema" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div v-for="field in docFields" :key="field.id">
                        <SingleFileInput v-model="form[field.id]" v-model:deleteList="form.delete_files"
                            :delete-identifier="field.id" :label="`Format ${field.label}`" accept=".docx"
                            :error="form.errors[field.id]" />
                    </div>
                </div>

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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div v-for="field in docFields" :key="field.id">
                        <SingleFileInput v-model="form[field.id]" :label="`Format ${field.label}`"
                            v-model:deleteList="form.delete_files" :delete-identifier="field.id"
                            :existing-file-url="props.skemas.data.find(s => s.id === form.id)?.[field.id] ? `/storage/${props.skemas.data.find(s => s.id === form.id)?.[field.id]}` : null"
                            :is-marked-for-deletion="form.delete_files.includes(field.id)" accept=".pdf,.docx"
                            :error="form.errors[field.id]" />
                    </div>
                </div>

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
            <div class="flex justify-end items-center mb-6 gap-3">
                <div class="w-[243px]">
                    <TextInput v-model="filtersForm.search" type="text" placeholder="Cari nama skema..." />
                </div>
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
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider  min-w-[350px]">
                                Format File</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="(skema, index) in skemas.data" :key="skema.id">
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ index + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">
                                {{ skema.nama_skema }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">
                                <div class="flex flex-wrap gap-1 ">
                                    <template v-for="field in docFields" :key="field.id">
                                        <a v-if="skema[field.id]" :href="`/storage/${skema[field.id]}`" target="_blank"
                                            class="px-1.5 py-0.5 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800 rounded text-[10px] font-bold hover:bg-blue-100 transition-colors"
                                            :title="field.label">
                                            {{ field.label.replace('FR. ', '') }}
                                        </a>
                                    </template>
                                    <p v-if="!docFields.some(f => skema[f.id])" class="text-gray-400 italic text-xs">
                                        Belum ada format file</p>
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
                        <tr v-if="skemas.data.length === 0">
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                Belum ada skema sertifikasi.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex justify-between items-center">
                <span v-if="skemas.total > 0" class="text-sm text-gray-700 dark:text-gray-400 hidden lg:flex">
                    Menampilkan {{ skemas.from }} sampai {{ skemas.to }} dari {{ skemas.total }} hasil
                </span>
                <span v-else></span>
                <Pagination :links="skemas.links" />
            </div>

        </div>
        <Alert type="info" title="Ketentuan Penghapusan Skema" class="mt-6">
            <ul class="list-disc list-inside space-y-1">
                <li>Skema hanya bisa dihapus jika belum pernah terlibat dalam sertifikasi</li>
                <li>Skema tidak bisa dihapus jika telah pernah terlibat dalam sertifikasi demi kebutuhan akuntabilitas
                    data dan audit</li>
                <li>Jika ingin agar skema sertifikasi tidak muncul dalam pilihan ketika memulai event sertifikasi, cukup
                    ubah
                    statusnya jadi "Non-aktif" Skema hanya bisa dihapus jika belum pernah terlibat dalam sertifikasi</li>
                <li>Skema sertifikasi yang statusnya bisa diubah jadi "Non-Aktif" hanya jika tidak ada sertifikasi yang berlangsung</li>
            </ul>
        </Alert>
    </AdminLayout>
</template>