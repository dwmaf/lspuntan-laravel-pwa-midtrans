<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AdminSertifikasiMenu from "@/Components/AdminSertifikasiMenu.vue";
import CustomHeader from '@/Components/CustomHeader.vue';
import MultiFileInput from "@/Components/Input/MultiFileInput.vue";
import SingleFileInput from "@/Components/Input/SingleFileInput.vue";
import TextareaInput from "@/Components/Input/TextareaInput.vue";
import InputError from "@/Components/Input/InputError.vue";
import InputLabel from "@/Components/Input/InputLabel.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue";
import { useForm, Link, router, InfiniteScroll } from "@inertiajs/vue3";
import { ref, computed } from 'vue';
import AddButton from "@/Components/Button/AddButton.vue";
import EditButton from "@/Components/Button/EditButton.vue";
import DeleteButton from "@/Components/Button/DeleteButton.vue";
import FileIcon from '@/Components/FileIcon.vue';
import Modal from "@/Components/Modal.vue";
import ToggleSwitch from "@/Components/ToggleSwitch.vue";
import Checkbox from "@/Components/Input/Checkbox.vue";
import CreatorInfo from "@/Components/CreatorInfo.vue";

const props = defineProps({
    sertification: Object,
    pengumumans: Object,
    totalAsesis: Number,
});

const formMode = ref('list'); // 'list', 'create', 'edit'
const editingPengumumanId = ref(null);

const form = useForm({
    content: '',
    path_file: null,
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
    form.path_file = null;
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
        form.post(route('admin.sertifikasi.announcement.store', { sertification: props.sertification.id }), options);
    } else if (formMode.value === 'edit') {
        form.post(route('admin.sertifikasi.assessment-announcement.update', { sertification: props.sertification.id, news: editingPengumumanId.value }), options);
    }
};

const deletePengumuman = (pengumumanId) => {
    if (confirm('Yakin ingin menghapus pengumuman ini?')) {
        router.delete(route('admin.sertifikasi.assessment-announcement.destroy', { sertification: props.sertification.id, news: pengumumanId }), {
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

const headerTitle = computed(() => {
    let action = '';
    if (formMode.value === 'edit') action = 'Edit ';
    if (formMode.value === 'create') action = 'Tambah ';

    return `${props.sertification.skema.nama_skema}: ${action}Pengumuman`;
});

</script>
<template>
    <AdminLayout>
        <CustomHeader :judul="headerTitle" />
        <AdminSertifikasiMenu :sertification-id="props.sertification.id" />
        <div v-if="formMode === 'edit'" class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md max-w-3xl mx-auto">
            <form @submit.prevent="submit" class="mt-4 flex flex-col gap-4">
                <div class="">
                    <TextareaInput id="content" label="Rincian" v-model="form.content" rows="8" required :error="form.errors.content"/>
                </div>
                <div class="">
                    <SingleFileInput v-model="form.path_file" v-model:deleteList="form.delete_files"
                        delete-identifier="path_file" label="Lampiran Tambahan"
                        :existing-file-url="pengumumans.data.find(p => p.id === editingPengumumanId)?.path_file ? `/storage/${pengumumans.data.find(p => p.id === editingPengumumanId).path_file}` : null"
                        :is-marked-for-deletion="form.delete_files.includes('path_file')"
                        accept=".zip,.rar,.docx,.xlsx,.pptx,.jpg,.png,.jpeg,.pdf" :error="form.errors.path_file" />
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
        <div v-if="formMode === 'create'" class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md max-w-3xl mx-auto">
            <form @submit.prevent="submit" class="mt-4 flex flex-col gap-4">
                <TextareaInput id="content" label="Rincian" v-model="form.content" rows="8" required :error="form.errors.content"/>
                <SingleFileInput v-model="form.path_file" v-model:deleteList="form.delete_files"
                    delete-identifier="path_file" label="Lampiran Tambahan"
                    accept=".zip,.rar,.docx,.xlsx,.pptx,.jpg,.png,.jpeg,.pdf" :error="form.errors.path_file" />
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
                <div v-if="!pengumumans" class="py-3 px-5 bg-white dark:bg-gray-800 rounded-lg shadow-md mb-2">
                    <p class="text-gray-500 dark:text-gray-400 font-semibold text-sm">Belum ada pengumuman untuk
                        para asesi.</p>
                </div>
            </div>
            <InfiniteScroll data="pengumumans" class="space-y-2">
                <div v-if="pengumumans.data.length > 0" v-for="pengumuman in pengumumans.data" :key="pengumuman.id"
                    class="py-3 px-5 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-start mb-2 gap-2">
                        <CreatorInfo :name="pengumuman.user?.asesor ? pengumuman.user?.name : 'Admin'"
                            :created-at="pengumuman.created_at" :updated-at="pengumuman.updated_at" class="min-w-0" />

                        <div class="mt-1 flex flex-wrap gap-2 md:justify-end w-full md:w-auto">
                            <EditButton @click="showEditForm(pengumuman)">Edit</EditButton>
                            <DeleteButton @click="deletePengumuman(pengumuman.id)">Hapus</DeleteButton>
                        </div>
                    </div>

                    <h6 v-html="pengumuman.content.replace(/\n/g, '<br>')"
                        class="font-medium text-sm text-gray-800 dark:text-gray-100">
                    </h6>

                    <div v-if="pengumuman.path_file"
                        class="mt-2">
                        <a :href="`/storage/${pengumuman.path_file}`" target="_blank"
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
    </AdminLayout>
</template>