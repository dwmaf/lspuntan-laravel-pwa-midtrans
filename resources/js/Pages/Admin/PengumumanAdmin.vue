<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AdminSertifikasiMenu from "@/Components/AdminSertifikasiMenu.vue";
import TextInput from "../../Components/TextInput.vue";
import FileInput from "../../Components/FileInput.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { useForm, usePage, Link, router } from "@inertiajs/vue3";
import { ref, computed } from 'vue';
import AddButton from "../../Components/AddButton.vue";
import EditButton from "../../Components/EditButton.vue";
import DeleteButton from "../../Components/DeleteButton.vue";

// 1. Terima props dari controller
const props = defineProps({
    sertification: Object,
    pengumumans: Array,
});

// 2. State management untuk mode form
const formMode = ref('list'); // 'list', 'create', 'edit'
const editingPengumumanId = ref(null);

// 3. Form helper Inertia
const form = useForm({
    rincian_pengumuman_asesmen: '',
    newFiles: [],
    _method: 'POST', // Untuk membedakan antara POST dan PUT
});

const showCreateForm = () => {
    form.reset();
    form._method = 'POST';
    formMode.value = 'create';
};

const showEditForm = (pengumuman) => {
    form.rincian_pengumuman_asesmen = pengumuman.rincian_pengumuman_asesmen;
    form.newFiles = [];
    form._method = 'PATCH';
    editingPengumumanId.value = pengumuman.id;
    formMode.value = 'edit';
};

const cancelForm = () => {
    form.reset();
    formMode.value = 'list';
    editingPengumumanId.value = null;
};

const submit = () => {
    if (formMode.value === 'create') {
        form.post(route('admin.sertifikasi.assessment-announcement.store', {sert_id : props.sertification.id}), {
            onSuccess: () => cancelForm(),
        });
    } else if (formMode.value === 'edit') {
        // Gunakan form.post dengan _method 'PUT' untuk upload file
        form.post(route('admin.sertifikasi.assessment-announcement.update', { sert_id: props.sertification.id, peng_id: editingPengumumanId.value }), {
            onSuccess: () => cancelForm(),
        });
    }
};

// 6. Fungsi untuk menghapus file dan pengumuman
const deleteFile = (fileId) => {
    if (confirm('Yakin ingin menghapus file ini?')) {
        router.delete(route('admin.sertifikasi.assessment-announcement.file.delete', fileId), {
            preserveScroll: true,
        });
    }
};

const deletePengumuman = (pengumumanId) => {
    if (confirm('Yakin ingin menghapus pengumuman ini?')) {
        router.delete(route('admin.sertifikasi.assessment-announcement.destroy', { sert_id: props.sertification.id, peng_id: pengumumanId }), {
            preserveScroll: true,
        });
    }
};


</script>
<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Sertifikasi
            </h2>
        </template>
        <div>
            <AdminSertifikasiMenu :sertification-id="props.sertification.id" />
            <div v-if="formMode === 'create' || formMode === 'edit'"
                class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">{{ formMode === 'create' ?
                    'BuatPengumuman' : 'Edit Pengumuman' }}</h2>
                <form @submit.prevent="submit" class="mt-4 flex flex-col gap-4">
                    <div>
                        <InputLabel value="Rincian" />
                        <textarea v-model="form.rincian_pengumuman_asesmen" rows="8"
                            class="mt-1 w-full text-sm p-3 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100"></textarea>
                        <InputError :message="form.errors.rincian_pengumuman_asesmen" />
                    </div>
                    <div>
                        <InputLabel value="Lampiran" />
                        <!-- File yang sudah ada (hanya di mode edit) -->
                        <div v-if="formMode === 'edit'" class="grid grid-cols-1 md:grid-cols-2 gap-2 my-2">
                            <div v-for="file in props.pengumumans.find(p => p.id === editingPengumumanId)?.pengumumanasesmenfile"
                                :key="file.id"
                                class="flex items-center gap-2 px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-md text-xs text-blue-600 dark:text-blue-400 hover:bg-gray-200 dark:hover:bg-gray-600">
                                <a :href="`/storage/${file.path_file}`" target="_blank"
                                    class="text-blue-600 dark:text-blue-400 truncate flex-1">{{
                                    file.path_file.split('/').pop() }}</a>
                                <button type="button" @click="deleteFile(file.id)"
                                    class="flex-shrink-0 p-1 rounded-full text-gray-500 hover:bg-gray-300 dark:hover:bg-gray-600">
                                    <FontAwesomeIcon icon="fa-solid fa-xmark" />
                                </button>
                            </div>
                        </div>
                        <FileInput v-model="form.newFiles" accept="image/jpeg,image/png,application/pdf,docx" multiple />
                        
                        <InputError :message="form.errors.newFiles" />
                        <InputError  :message="form.errors['newFiles.0']" />
                        
                    </div>
                    <div class="flex items-center gap-4 pt-2">
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            {{ formMode === 'edit' ? 'Update' : 'Simpan' }}
                        </PrimaryButton>
                        <SecondaryButton type="button" @click="cancelForm">Batal</SecondaryButton>
                    </div>
                </form>
            </div>
            <div v-else>
                <div class="p-6 mb-2 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <div class="flex justify-between items-center">
                        <p class="text-gray-500 dark:text-gray-400 text-xs">Buat pengumuman baru untuk para asesi.</p>
                        <AddButton @click="showCreateForm">Tambah Pengumuman</AddButton>
                    </div>
                </div>
                <div class="space-y-2">

                    <div v-if="props.pengumumans.length > 0" v-for="pengumuman in props.pengumumans"
                        :key="pengumuman.id" class="py-3 px-5 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                        <div class="flex justify-between items-start mb-2">

                            <div class="flex items-center gap-3 min-w-0">
                                <div class="flex-shrink-0">
                                    <svg class="h-10 w-10 text-gray-400 dark:text-gray-600 rounded-full bg-gray-200 dark:bg-gray-700 p-1"
                                        fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <h5 class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">
                                        {{ pengumuman.pembuatpengumuman?.user?.name || 'Admin' }}
                                    </h5>
                                    <div class="text-xs text-gray-400">
                                        {{ new Date(pengumuman.created_at).toLocaleString() }}
                                        <span v-if="pengumuman.updated_at !== pengumuman.created_at"
                                            class="text-gray-500">(diedit)</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <EditButton @click="showEditForm(pengumuman)">Edit</EditButton>

                                <DeleteButton @click="deletePengumuman(pengumuman.id)">Hapus</DeleteButton>
                            </div>
                        </div>

                        <h6 class="font-medium text-sm text-gray-800 dark:text-gray-100">{{
                            pengumuman.rincian_pengumuman_asesmen }}</h6>
                        <div v-if="pengumuman.pengumumanasesmenfile.length > 0"
                            class="mt-3 border-t dark:border-gray-700 pt-2 grid grid-cols-1 md:grid-cols-2 gap-2">
                            <a v-for="file in pengumuman.pengumumanasesmenfile" :key="file.id"
                                :href="`/storage/${file.path_file}`" target="_blank"
                                class="flex items-center gap-2 px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-md text-xs text-blue-600 dark:text-blue-400 hover:bg-gray-200 dark:hover:bg-gray-600">
                                <span class="truncate">{{ file.path_file.split('/').pop() }}</span>
                            </a>
                        </div>
                    </div>

                    <div v-else class="text-center text-gray-500 dark:text-gray-300 py-8">
                        Belum ada pengumuman.
                    </div>

                </div>
            </div>

        </div>
    </AdminLayout>
</template>