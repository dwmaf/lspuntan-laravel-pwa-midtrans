<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AdminSertifikasiMenu from "@/Components/AdminSertifikasiMenu.vue";
import CustomHeader from '@/Components/CustomHeader.vue';
import MultiFileInput from "@/Components/Input/MultiFileInput.vue";
import InputError from "@/Components/Input/InputError.vue";
import InputLabel from "@/Components/Input/InputLabel.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue";
import { useForm, Link, router, InfiniteScroll } from "@inertiajs/vue3";
import { ref, computed } from 'vue';
import AddButton from "@/Components/Button/AddButton.vue";
import EditButton from "@/Components/Button/EditButton.vue";
import DeleteButton from "@/Components/Button/DeleteButton.vue";
import Modal from "@/Components/Modal.vue";
import ToggleSwitch from "@/Components/ToggleSwitch.vue";
import Checkbox from "@/Components/Input/Checkbox.vue";

const props = defineProps({
    sertification: Object,
    pengumumans: Object,
    totalAsesis: Number,
});

const formMode = ref('list'); // 'list', 'create', 'edit'
const editingPengumumanId = ref(null);
const isPreviewing = ref(false);
const showReadersModal = ref(false);
const readersList = ref([]);
const loadingReaders = ref(false);

const form = useForm({
    content: '',
    newFiles: [],
    delete_files: [],
    send_notification: true,
    _method: 'POST',
});

const showCreateForm = () => {
    form.reset();
    form.send_notification = true;
    form._method = 'POST';
    formMode.value = 'create';
};

const showEditForm = (pengumuman) => {
    form.reset();
    form.content = pengumuman.content;
    form.newFiles = [];
    form.delete_files = [];
    form.send_notification = !pengumuman.published_at;
    form._method = 'PATCH';
    editingPengumumanId.value = pengumuman.id;
    formMode.value = 'edit';
};

const cancelForm = () => {
    form.reset();
    delete form._method;
    formMode.value = 'list';
    editingPengumumanId.value = null;
};

const submit = () => {
    const options = {
        preserveScroll: true,
        onSuccess: () => {
            cancelForm();
            router.reload({
                only: ['pengumumans'],
                reset: ['pengumumans'],
            });
        },
    };
    if (formMode.value === 'create') {
        form.post(route('admin.sertifikasi.announcement.store', { sert_id: props.sertification.id }), options);
    } else if (formMode.value === 'edit') {
        form.post(route('admin.sertifikasi.assessment-announcement.update', { sert_id: props.sertification.id, peng_id: editingPengumumanId.value }), options);
    }
};

const deletePengumuman = (pengumumanId) => {
    if (confirm('Yakin ingin menghapus pengumuman ini?')) {
        router.delete(route('admin.sertifikasi.assessment-announcement.destroy', { sert_id: props.sertification.id, peng_id: pengumumanId }), {
            preserveScroll: true,
            onSuccess: () => {
                router.reload({
                    only: ['pengumumans'],
                    reset: ['pengumumans'],
                });
            }
        });
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    const now = new Date();

    const isToday = date.getDate() === now.getDate() &&
        date.getMonth() === now.getMonth() &&
        date.getFullYear() === now.getFullYear();

    const isSameYear = date.getFullYear() === now.getFullYear();

    if (isToday) {
        return new Intl.DateTimeFormat('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        }).format(date);
    }

    if (isSameYear) {
        return new Intl.DateTimeFormat('id-ID', {
            day: 'numeric',
            month: 'long'
        }).format(date);
    }

    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    }).format(date);
};

// Compute preview files from both new uploads and existing ones (if editing)
const previewFiles = computed(() => {
    let files = [];

    // Existing files
    if (formMode.value === 'edit' && editingPengumumanId.value) {
        const currentPengumuman = props.pengumumans.data.find(p => p.id === editingPengumumanId.value);
        if (currentPengumuman && currentPengumuman.newsfiles) {
            const existing = currentPengumuman.newsfiles
                .filter(f => !form.delete_files.includes(f.id))
                .map(f => ({
                    id: f.id,
                    name: f.path_file.split('/').pop(),
                    url: `/storage/${f.path_file}`,
                    isLocal: false
                }));
            files = [...files, ...existing];
        }
    }

    // New files (local)
    if (form.newFiles && form.newFiles.length) {
        const newUploads = Array.from(form.newFiles).map(file => ({
            id: null,
            name: file.name,
            url: URL.createObjectURL(file),
            isLocal: true,
            fileObj: file
        }));
        files = [...files, ...newUploads];
    }

    return files;
});

const fetchReaders = (newsId) => {
    loadingReaders.value = true;
    readersList.value = [];
    showReadersModal.value = true;

    window.axios.get(route('admin.sertifikasi.assessment-announcement.readers', {
        sert_id: props.sertification.id,
        news_id: newsId
    }))
        .then(response => {
            readersList.value = response.data;
        })
        .catch(error => {
            console.error("Gagal mengambil data pembaca:", error);
        })
        .finally(() => {
            loadingReaders.value = false;
        });
};
</script>
<template>
    <AdminLayout>
        <CustomHeader judul="Pengumuman Sertifikasi" />
        <AdminSertifikasiMenu :sertification-id="props.sertification.id" />
        <div v-if="formMode === 'edit'" class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Edit Pengumuman</h2>
            <form @submit.prevent="submit" class="mt-4 flex flex-col gap-4">
                <div>
                    <InputLabel value="Rincian" />
                    <textarea v-model="form.content" rows="8"
                        class="mt-1 w-full text-sm p-3 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100"></textarea>
                    <InputError :message="form.errors.content" />
                </div>
                <div>
                    <MultiFileInput v-model="form.newFiles" v-model:delete-list="form.delete_files" label="Lampiran"
                        accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.ppt,.pptx,.xls,.xlsx"
                        :existing-files="pengumumans.data.find(p => p.id === editingPengumumanId)?.newsfiles || []"
                        :error="form.errors.newFiles" :error-list="form.errors['newFiles.0']" />
                </div>
                <div class="mt-2">
                    <label class="flex items-center">
                        <Checkbox v-model:checked="form.send_notification" />
                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Kirim Notifikasi ke
                            Asesi?</span>
                    </label>
                </div>
                <div class="flex items-center gap-4 pt-2">
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Update
                    </PrimaryButton>
                    <SecondaryButton type="button" @click="isPreviewing = true">Preview</SecondaryButton>
                    <SecondaryButton type="button" @click="cancelForm">Batal</SecondaryButton>
                </div>
            </form>
        </div>
        <div v-if="formMode === 'create'" class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Buat Pengumuman</h2>
            <form @submit.prevent="submit" class="mt-4 flex flex-col gap-4">
                <div>
                    <InputLabel value="Rincian" />
                    <textarea v-model="form.content" rows="8"
                        class="mt-1 w-full text-sm p-3 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100"></textarea>
                    <InputError :message="form.errors.content" />
                </div>
                <div>
                    <MultiFileInput v-model="form.newFiles" label="Lampiran"
                        accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.ppt,.pptx,.xls,.xlsx" :error="form.errors.newFiles"
                        :error-list="form.errors['newFiles.0']" />
                </div>
                <div class="mt-2">
                    <label class="flex items-center">
                        <Checkbox v-model:checked="form.send_notification" />
                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Kirim Notifikasi ke
                            Asesi?</span>
                    </label>
                </div>
                <div class="flex items-center gap-4 pt-2">
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Simpan
                    </PrimaryButton>
                    <SecondaryButton type="button" @click="isPreviewing = true">Preview</SecondaryButton>
                    <SecondaryButton type="button" @click="cancelForm">Batal</SecondaryButton>
                </div>
            </form>
        </div>
        <div v-if="formMode === 'list'">
            <div class="flex flex-col gap-2">
                <AddButton class="self-end" @click="showCreateForm">Tambah Pengumuman</AddButton>
                <div class="py-3 px-5 bg-white dark:bg-gray-800 rounded-lg shadow-md mb-2">
                    <p class="text-gray-500 dark:text-gray-400 font-semibold text-sm">Buat pengumuman baru untuk
                        para asesi.</p>
                </div>
            </div>
            <InfiniteScroll data="pengumumans" class="space-y-2">

                <div v-if="pengumumans.data.length > 0" v-for="pengumuman in pengumumans.data" :key="pengumuman.id"
                    class="py-3 px-5 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-start mb-2 gap-2">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="shrink-0">
                                <svg class="h-10 w-10 text-gray-400 dark:text-gray-600 rounded-full bg-gray-200 dark:bg-gray-700 p-1"
                                    fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <h5 class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">
                                    {{ pengumuman.user?.asesor ? pengumuman.user?.name : 'Admin' }}
                                </h5>
                                <div class="text-xs text-gray-400">
                                    {{ formatDate(pengumuman.created_at) }}
                                    <span v-if="pengumuman.created_at !== pengumuman.updated_at"
                                        class="text-gray-500">(diedit)</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-1 flex flex-wrap gap-2 md:justify-end w-full md:w-auto">
                            <EditButton @click="showEditForm(pengumuman)">Edit</EditButton>
                            <DeleteButton @click="deletePengumuman(pengumuman.id)">Hapus</DeleteButton>
                        </div>
                    </div>

                    <h6 v-html="pengumuman.content.replace(/\n/g, '<br>')"
                        class="font-medium text-sm text-gray-800 dark:text-gray-100">
                    </h6>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
                        <div v-if="pengumuman.newsfiles.length > 0" v-for="file in pengumuman.newsfiles" :key="file.id"
                            class="flex items-center justify-between gap-4 px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md text-xs">
                            <a :href="`/storage/${file.path_file}`" target="_blank"
                                class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-500 hover:underline truncate flex-1">
                                {{ file.path_file.split('/').pop() }}
                            </a>
                        </div>
                    </div>

                    <div class="mt-3 pt-2 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                        <button type="button" @click="fetchReaders(pengumuman.id)"
                            class="flex items-center gap-1 text-xs text-blue-600 dark:text-blue-400 hover:underline transition-colors"
                            title="Klik untuk melihat daftar pembaca">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <span>Dibaca: {{ pengumuman.reads_count }} / {{ props.totalAsesis }} Asesi</span>
                        </button>
                    </div>
                </div>


            </InfiniteScroll>
        </div>
        <div v-else class="text-center text-gray-500 dark:text-gray-300 py-8">
            Belum ada pengumuman.
        </div>
        <Modal :show="isPreviewing" @close="isPreviewing = false">
            <div class="p-6 bg-white dark:bg-gray-800">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Preview Pengumuman</h2>
                <div class="border-t border-gray-200 dark:border-gray-700 py-4">
                    <div class="flex justify-between items-center mb-2">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="shrink-0">
                                <svg class="h-10 w-10 text-gray-400 dark:text-gray-600 rounded-full bg-gray-200 dark:bg-gray-700 p-1"
                                    fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <div>
                                <h5 class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                    {{ $page.props.auth.user?.name || 'Admin' }}
                                </h5>
                                <div class="text-xs text-gray-400">
                                    {{ formatDate(new Date()) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-html="form.content.replace(/\n/g, '<br>')"
                        class="font-medium text-sm text-gray-800 dark:text-gray-100 mb-4"></div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2" v-if="previewFiles.length > 0">
                        <div v-for="(file, index) in previewFiles" :key="index"
                            class="flex items-center justify-between gap-4 px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md text-xs">
                            <a :href="file.url" target="_blank"
                                class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-500 hover:underline truncate flex-1">
                                {{ file.name }}
                            </a>
                            <span v-if="file.isLocal" class="text-xs text-gray-400 italic">(Baru)</span>
                        </div>
                    </div>

                    <div v-else class="text-xs text-gray-500 mb-2 italic">
                        Tidak ada lampiran.
                    </div>
                </div>
                <div class="flex justify-end mt-4">
                    <SecondaryButton @click="isPreviewing = false">Tutup Preview</SecondaryButton>
                </div>
            </div>
        </Modal>

        <Modal :show="showReadersModal" @close="showReadersModal = false">
            <div class="p-6 bg-white dark:bg-gray-800">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Status Literasi Asesi</h2>
                <div class="border-t border-gray-200 dark:border-gray-700 max-h-[60vh] overflow-y-auto">
                    <div v-if="loadingReaders" class="text-center py-4 text-gray-500">
                        Memuat data...
                    </div>
                    <div v-else-if="readersList.length > 0" class="space-y-3">
                        <div v-for="(reader, index) in readersList" :key="index"
                            class="flex flex-col sm:flex-row justify-between gap-2 sm:items-center py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                            <div>
                                <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ reader.name }}
                                </div>
                                <div class="text-xs text-gray-500">{{ reader.email }}</div>
                            </div>
                            <div class="mt-1 sm:mt-0 sm:text-right">
                                <div v-if="reader.has_read" class="text-xs">
                                    <span
                                        class="px-2 py-1 font-semibold text-green-700 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-300 whitespace-nowrap">
                                        Dilihat pada {{ formatDate(reader.read_at) }} WIB
                                    </span>
                                </div>
                                <div v-else class="text-xs">
                                    <span
                                        class="px-2 py-1 font-semibold text-red-700 bg-red-100 rounded-full dark:bg-red-900 dark:text-red-300 whitespace-nowrap">
                                        Belum Dilihat
                                    </span>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div v-else class="text-center py-4 text-gray-500 italic">
                        Tidak ada data asesi.
                    </div>
                </div>
                <div class="flex justify-end mt-4">
                    <SecondaryButton @click="showReadersModal = false">Tutup</SecondaryButton>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>