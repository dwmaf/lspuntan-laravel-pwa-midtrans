<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import AddButton from "@/Components/AddButton.vue";
import EditButton from "@/Components/EditButton.vue";
import DeleteButton from "@/Components/DeleteButton.vue";
import { useForm, usePage, router } from "@inertiajs/vue3";
import { ref, computed, onMounted, onUnmounted } from "vue";

const props = defineProps({
    asesors: Array,
    skemas: Array,
});

// State untuk mengontrol tampilan: 'list', 'create', 'edit'
const formMode = ref('list');

const isSkemaDropdownOpen = ref(false);
const skemaDropdownRef = ref(null);
// Inisialisasi form dengan useForm
const form = useForm({
    id: null,
    name: '',
    email: '',
    no_tlp_hp: '',
    selectedSkemas: [],
});

// Fungsi untuk menampilkan form tambah
const showCreateForm = () => {
    form.reset();
    formMode.value = 'create';
};

// Fungsi untuk menampilkan form edit
const showEditForm = (asesor) => {
    form.id = asesor.id;
    form.name = asesor.user.name;
    form.email = asesor.user.email;
    form.no_tlp_hp = asesor.user.no_tlp_hp ?? '';
    form.selectedSkemas = asesor.skemas.map(s => s.id);
    formMode.value = 'edit';
};

// Fungsi untuk kembali ke daftar
const backToList = () => {
    formMode.value = 'list';
    isSkemaDropdownOpen.value = false;
    form.reset();
    form.clearErrors();
};

// Fungsi untuk submit (create atau update)
const save = () => {
    if (formMode.value === 'create') {
        form.post(route('admin.asesor.store'), {
            onSuccess: () => backToList(),
        });
    } else if (formMode.value === 'edit') {
        form.put(route('admin.asesor.update', form.id), {
            onSuccess: () => backToList(),
        });
    }
};

// Fungsi untuk menghapus
const destroy = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus asesor ini?')) {
        router.delete(route('admin.asesor.destroy', id));
    }
};
const handleSkemaClickOutside = (event) => {
    if (isSkemaDropdownOpen.value && skemaDropdownRef.value && !skemaDropdownRef.value.contains(event.target)) {
        isSkemaDropdownOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('mousedown', handleSkemaClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('mousedown', handleSkemaClickOutside);
});
</script>

<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Manajemen Asesor
            </h2>
        </template>

        
        <!-- Form Tambah/Edit -->
        <div v-if="formMode === 'create' || formMode === 'edit'"
            class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                {{ formMode === 'create' ? 'Tambah Asesor' : 'Edit Asesor' }}
            </h2>
            <form @submit.prevent="save" class="mt-4 flex flex-col gap-4">
                <div>
                    <InputLabel value="Pilih Skema" required />
                    <div class="relative" ref="skemaDropdownRef">
                        <button type="button" @click="isSkemaDropdownOpen = !isSkemaDropdownOpen"
                            class="p-2 text-sm font-medium rounded-md w-full text-left flex justify-between items-center mt-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                            <span>
                                Pilih Skema
                                <span v-if="form.selectedSkemas.length > 0" class="ml-1 text-xs text-gray-400">
                                    ({{ form.selectedSkemas.length }} terpilih)
                                </span>
                            </span>
                            <svg class="w-4 h-4 transform transition-transform"
                                :class="{ 'rotate-180': isSkemaDropdownOpen }" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div v-show="isSkemaDropdownOpen" 
                            class="absolute left-0 w-full rounded-b-md z-20 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 dark:text-white"
                            style="display: none;">
                            <div class="p-2 max-h-60 overflow-y-auto">
                                <label v-for="skema in skemas" :key="skema.id" @clicl.stop
                                    class="flex items-center p-1 hover:bg-gray-200 dark:hover:bg-gray-700 rounded cursor-pointer">
                                    <input type="checkbox" :value="skema.id" v-model="form.selectedSkemas" required
                                        class="mr-2 rounded text-indigo-600 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ skema.nama_skema }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <InputError :message="form.errors.selectedSkemas" />
                </div>
                <div>
                    <InputLabel value="Nama Lengkap" required />
                    <TextInput v-model="form.name" type="text" required />
                    <InputError :message="form.errors.name" />
                </div>
                <div>
                    <InputLabel value="Email" required />
                    <TextInput v-model="form.email" type="email" required />
                    <InputError :message="form.errors.email" />
                </div>
                <div>
                    <InputLabel value="No. HP" />
                    <TextInput v-model="form.no_tlp_hp" type="text" />
                    <InputError :message="form.errors.no_tlp_hp" />
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
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Daftar Asesor</h2>
                <AddButton @click="showCreateForm">Tambah Asesor</AddButton>
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
                                Nama & Email</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Skema</th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="(asesor, index) in asesors" :key="asesor.id">
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{ index + 1 }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">
                                <div class="font-medium">{{ asesor.user.name }}</div>
                                <div class="text-xs text-gray-500">{{ asesor.user.email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">
                                <div class="flex flex-wrap gap-1">
                                    <span v-for="skema in asesor.skemas" :key="skema.id"
                                        class="inline-block rounded bg-gray-200 dark:bg-gray-700 px-2 py-1 text-xs font-medium">{{
                                        skema.nama_skema }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <EditButton @click="showEditForm(asesor)">Edit</EditButton>
                                    <DeleteButton @click="destroy(asesor.id)">Hapus</DeleteButton>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="asesors.length === 0">
                            <td colspan="4" class="px-6 py-4 text-center">Tidak ada data asesor.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>