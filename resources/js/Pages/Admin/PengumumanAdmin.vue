<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AdminSertifikasiMenu from "@/Components/AdminSertifikasiMenu.vue";
import CustomHeader from '@/Components/CustomHeader.vue';
import SingleFileInput from "@/Components/Input/SingleFileInput.vue";
import TextareaInput from "@/Components/Input/TextareaInput.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue";
import { useForm, router, usePage, InfiniteScroll } from "@inertiajs/vue3";
import { ref, computed } from 'vue';
import AddButton from "@/Components/Button/AddButton.vue";
import EditButton from "@/Components/Button/EditButton.vue";
import DeleteButton from "@/Components/Button/DeleteButton.vue";
import FileIcon from '@/Components/FileIcon.vue';
import Modal from "@/Components/Modal.vue";
import Checkbox from "@/Components/Input/Checkbox.vue";
import CreatorInfo from "@/Components/CreatorInfo.vue";
import StatusBadge from "@/Components/StatusBadge.vue";

const props = defineProps({
    sertifikasi: Object,
    listPengumuman: Object,
});

const authUser = computed(() => usePage().props.auth.user);
const isAdmin = computed(() => (usePage().props.auth.roles ?? []).includes('admin'));

const canManage = (pengumuman) => isAdmin.value || (authUser.value?.id === pengumuman.user_id);

const formMode = ref('list'); // 'list', 'create', 'edit'
const editingPengumumanId = ref(null);

const form = useForm({
    content: '',
    path_file: null,
    delete_files: [],
    send_notification: true,
    is_certif_news: false,
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
    form.path_file = null;
    form.delete_files = [];
    form.send_notification = !pengumuman.published_at;
    form.is_certif_news = pengumuman.is_certif_news;
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
                only: ['listPengumuman'],
                reset: ['listPengumuman'],
            });
        },
    };
    if (formMode.value === 'create') {
        form.post(route('admin.sertifikasi.pengumuman.store', { sertifikasi: props.sertifikasi.id }), options);
    } else if (formMode.value === 'edit') {
        form.post(route('admin.sertifikasi.pengumuman.update', { sertifikasi: props.sertifikasi.id, pengumuman: editingPengumumanId.value }), options);
    }
};

const showDeleteConfirmModal = ref(false);
const pengumumanToDelete = ref(null);

const confirmDelete = (pengumumanId) => {
    pengumumanToDelete.value = pengumumanId;
    showDeleteConfirmModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteConfirmModal.value = false;
    pengumumanToDelete.value = null;
};

const deletePengumuman = () => {
    if (pengumumanToDelete.value) {
        router.delete(route('admin.sertifikasi.pengumuman.destroy', {
            sertifikasi: props.sertifikasi.id,
            pengumuman: pengumumanToDelete.value
        }), {
            preserveScroll: true,
            onSuccess: () => {
                closeDeleteModal(); // Tutup modal ketika sukses
                router.reload({
                    only: ['listPengumuman'],
                    reset: ['listPengumuman'],
                });
            }
        });
    }
};

const headerTitle = computed(() => {
    let action = '';
    if (formMode.value === 'edit') action = 'Edit ';
    if (formMode.value === 'create') action = 'Tambah ';

    return `${props.sertifikasi.skema.nama_skema}: ${action}Pengumuman`;
});

</script>
<template>
    <AdminLayout>
        <CustomHeader :judul="headerTitle" />
        <AdminSertifikasiMenu :sertifikasi-id="props.sertifikasi.id" />
        <div v-if="formMode === 'edit'"
            class="p-3 md:p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md max-w-3xl mx-auto">
            <StatusBadge v-if="form.is_certif_news" variant="success" class="mb-2">
                Pengumuman Pengambilan Sertifikat
            </StatusBadge>
            <form @submit.prevent="submit" class=" flex flex-col gap-4">
                <div class="">
                    <TextareaInput id="content" label="Rincian" v-model="form.content" rows="8" required
                        :error="form.errors.content" />
                </div>
                <div class="">
                    <SingleFileInput v-model="form.path_file" v-model:deleteList="form.delete_files"
                        delete-identifier="path_file" label="Lampiran Tambahan"
                        :existing-file-url="listPengumuman.data.find(p => p.id === editingPengumumanId)?.path_file ? `/download/pengumuman/${editingPengumumanId}/path_file` : null"
                        :is-marked-for-deletion="form.delete_files.includes('path_file')"
                        accept=".zip,.rar,.docx,.xlsx,.pptx,.jpg,.png,.jpeg,.pdf" :error="form.errors.path_file" />
                </div>

                <div v-if="isAdmin" class="mt-2">
                    <label class="flex items-center">
                        <Checkbox id="is_certif_news" v-model:checked="form.is_certif_news" />
                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Pengumuman Pengambilan
                            Sertifikat?</span>
                    </label>
                </div>
                <div class="mt-2">
                    <label class="flex items-center">
                        <Checkbox id="send_notif" v-model:checked="form.send_notification" />
                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Kirim Notifikasi ke
                            Asesi?</span>
                    </label>
                </div>
                <div class="flex items-center gap-4 pt-2">
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Update
                    </PrimaryButton>

                    <SecondaryButton type="button" @click="cancelForm">Batal</SecondaryButton>
                </div>
            </form>
        </div>
        <div v-if="formMode === 'create'"
            class="p-3 md:p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md max-w-3xl mx-auto">
            <StatusBadge v-if="form.is_certif_news" variant="success" class="mb-2">
                Pengumuman Pengambilan Sertifikat
            </StatusBadge>
            <form @submit.prevent="submit" class="flex flex-col gap-4">
                <TextareaInput id="content" label="Rincian" v-model="form.content" rows="8" required
                    :error="form.errors.content" />
                <SingleFileInput v-model="form.path_file" v-model:deleteList="form.delete_files"
                    delete-identifier="path_file" label="Lampiran Tambahan"
                    accept=".zip,.rar,.docx,.xlsx,.pptx,.jpg,.png,.jpeg,.pdf" :error="form.errors.path_file" />
                <div v-if="isAdmin" class="mt-2">
                    <label class="flex items-center">
                        <Checkbox id="is_certif_news" v-model:checked="form.is_certif_news" />
                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Pengumuman Pengambilan
                            Sertifikat?</span>
                    </label>
                </div>
                <div class="mt-2">
                    <label class="flex items-center">
                        <Checkbox id="send_notif" v-model:checked="form.send_notification" />
                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Kirim Notifikasi ke
                            Asesi?</span>
                    </label>
                </div>
                <div class="flex items-center gap-4 pt-2">
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Simpan
                    </PrimaryButton>

                    <SecondaryButton type="button" @click="cancelForm">Batal</SecondaryButton>
                </div>
            </form>
        </div>
        <div v-if="formMode === 'list'" class="max-w-3xl mx-auto">
            <div class="flex flex-col gap-2 mb-2">
                <AddButton class="self-end" @click="showCreateForm">Tambah Pengumuman</AddButton>
                <div v-if="!listPengumuman.data || listPengumuman.data.length === 0"
                    class="py-3 px-5 bg-white dark:bg-gray-800 rounded-lg shadow-md mb-2">
                    <p class="text-gray-500 dark:text-gray-400 font-semibold text-sm">Belum ada pengumuman untuk para
                        asesi.</p>
                </div>
            </div>
            <InfiniteScroll data="listPengumuman" class="space-y-2">
                <div v-if="listPengumuman.data.length > 0" v-for="pengumuman in listPengumuman.data"
                    :key="pengumuman.id" class="py-3 px-5 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <div class="flex flex-wrap justify-between items-start mb-2 gap-2">
                        <CreatorInfo :name="pengumuman.user?.asesor ? pengumuman.user?.name : 'Admin'"
                            :created-at="pengumuman.created_at" :updated-at="pengumuman.updated_at" class="min-w-0" />

                        <div v-if="canManage(pengumuman)" class="mt-1 flex flex-wrap gap-2 md:justify-end ">
                            <EditButton @click="showEditForm(pengumuman)">Edit</EditButton>
                            <DeleteButton @click="confirmDelete(pengumuman.id)">Hapus</DeleteButton>
                        </div>
                    </div>

                    <StatusBadge v-if="pengumuman.is_certif_news" variant="success" class="mb-2">
                        Pengumuman Pengambilan Sertifikat
                    </StatusBadge>

                    <h6 v-html="pengumuman.content.replace(/\n/g, '<br>')"
                        class="font-medium text-sm text-gray-800 dark:text-gray-100">
                    </h6>

                    <div v-if="pengumuman.path_file" class="mt-2">
                        <a :href="`/download/pengumuman/${pengumuman.id}/path_file`" target="_blank"
                            class="text-sm flex items-center gap-2 group min-w-0">
                            <FileIcon :path="pengumuman.path_file" />
                            <span class="text-blue-500 group-hover:text-blue-700 truncate group-hover:underline">
                                {{ pengumuman.path_file.split('/').pop() }}
                            </span>
                        </a>
                    </div>

                </div>
            </InfiniteScroll>
        </div>
        <Modal :show="showDeleteConfirmModal" @close="closeDeleteModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Yakin ingin menghapus pengumuman ini?
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Pengumuman dan lampirannya (jika ada) akan dihapus dari sistem.
                </p>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeDeleteModal"> Batal </SecondaryButton>
                    <DeleteButton class="ml-3" @click="deletePengumuman">
                        Ya, Hapus
                    </DeleteButton>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>