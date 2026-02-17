<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import CustomHeader from '@/Components/CustomHeader.vue';
import AdminSertifikasiMenu from "@/Components/AdminSertifikasiMenu.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import EditButton from "@/Components/Button/EditButton.vue";
import AddButton from "@/Components/Button/AddButton.vue";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue";
import SeeButton from "@/Components/Button/SeeButton.vue";
import SingleFileInput from "@/Components/Input/SingleFileInput.vue";
import DateInput from "@/Components/Input/DateInput.vue";
import TextareaInput from "@/Components/Input/TextareaInput.vue";
import CreatorInfo from "@/Components/CreatorInfo.vue";
import FileCard from "@/Components/FileCard.vue";
import BackButton from "@/Components/Button/BackButton.vue";
import Checkbox from "@/Components/Input/Checkbox.vue";
import DangerButton from "@/Components/Button/DangerButton.vue";
import { useForm, usePage, Link, router } from "@inertiajs/vue3";
import { FileText, CheckCircle, Download, Edit, AlertTriangle, Clock } from 'lucide-vue-next';
import { ref, computed, onMounted } from 'vue';
import { useFormat } from "@/Composables/useFormat";

const props = defineProps({
    sertification: Object,
    filteredAsesi: Array,
    initialAsesiId: [String, Number],
});

const isEditing = ref(false);

const viewMode = ref('list');
const selectedAsesi = ref(null);

const showDetailView = (asesi) => {
    selectedAsesi.value = asesi;
    viewMode.value = 'detail';
}

const showListView = () => {
    selectedAsesi.value = null;
    viewMode.value = 'list';
}

const form = useForm({
    _method: 'PATCH',
    content: '',
    deadline: '',
    path_file: null,
    delete_files: [],
    send_notification: true,
});


const enterEditMode = () => {
    form.content = props.sertification.asesmen?.content || '';
    form.deadline = props.sertification.asesmen?.deadline || '';
    form.send_notification = !props.sertification.asesmen;
    isEditing.value = true;
    viewMode.value = 'edit';
};

const cancelEdit = () => {
    isEditing.value = false;
    form.reset();
    form.clearErrors();
    viewMode.value = 'list';
};

onMounted(() => {
    if (props.initialAsesiId) {
        const asesiToOpen = props.filteredAsesi.find(a => a.id == props.initialAsesiId);
        if (asesiToOpen) {
            // console.log('asesi ditemukan, ');
            showDetailView(asesiToOpen);
        }
    }
});

const submit = () => {
    form.post(route('admin.sertifikasi.assessment.update', props.sertification.id), {
        onSuccess: () => {
            cancelEdit();
        },
    });
};

const deleteAsesmen = () => {
    if (confirm('Apakah Anda yakin ingin menghapus tugas asesmen ini? Instruksi dan lampiran soal akan dihapus, namun tugas/dokumen yang sudah dikumpulkan asesi akan TETAP TERSIMPAN.')) {
        router.delete(route('admin.sertifikasi.assessment.destroy', props.sertification.id), {
            onSuccess: () => {
                cancelEdit();
            },
        });
    }
};

const { formatDateTime } = useFormat();
</script>
<template>
    <AdminLayout>
        <CustomHeader :judul="`${sertification.skema.nama_skema}: Asesmen`" />
        <AdminSertifikasiMenu :sertification-id="props.sertification.id" />
        <!-- Mode Tampilan -->
        <div id="view-asesmen" v-if="!isEditing" class="max-w-3xl mx-auto">
            <div v-if="!sertification.asesmen" class="flex flex-col gap-2">
                <AddButton class="self-end" @click="enterEditMode">Buat Asesmen</AddButton>
                <div class="py-3 px-5 bg-white dark:bg-gray-800 rounded-lg shadow-md mb-2">
                    <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                        Asesor belum memberikan instruksi asesmen, buat instruksi asesmen agar asesi bisa mengumpulkan
                        dokumen asesmen seperti FR.AK, FR.IA, etc atau dokumen tambahan lainnya.
                    </p>
                </div>
            </div>
            <div v-else class="py-3 px-5 bg-white dark:bg-gray-800 rounded-lg shadow-md mb-2">
                <div class="flex justify-between items-center mb-2">
                    <CreatorInfo :name="sertification.asesmen?.name" :created-at="sertification.asesmen?.created_at"
                        :updated-at="sertification.asesmen?.updated_at" v-if="sertification.asesmen" class="mb-4" />
                    <div>
                        <div class="flex items-center gap-3">
                            <EditButton @click="enterEditMode">Edit</EditButton>
                        </div>
                    </div>
                </div>

                <div v-html="sertification.asesmen.content.replace(/\n/g, '<br>')"
                    class="font-medium text-sm text-gray-800 dark:text-gray-100"></div>
                <div class="flex">
                    <dt class="text-sm font-medium text-gray-800 dark:text-gray-400 mr-1">Batas Akhir Pengumpulan :
                    </dt>
                    <dd v-if="sertification.asesmen" class="text-sm text-gray-900 dark:text-gray-100">
                        {{ sertification.asesmen.deadline ? formatDateTime(sertification.asesmen.deadline) : "-" }}
                    </dd>
                </div>
                <div v-if="sertification.asesmen?.path_file"
                    class="flex items-center justify-between gap-4 px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md text-xs">
                    <a :href="`/storage/${sertification.asesmen.path_file}`" target="_blank"
                        class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-500 hover:underline truncate flex-1">
                        {{ sertification.asesmen.path_file.split('/').pop() }}
                    </a>
                </div>

            </div>
        </div>

        <!-- Mode edit  -->
        <div v-else class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md flex flex-col gap-2 max-w-3xl mx-auto">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                    {{ sertification.asesmen ? "Edit Instruksi Asesmen" : "Buat Instruksi Asesmen" }}
                </h3>
            </div>
            <form @submit.prevent="submit" class="flex flex-col gap-4">
                <TextareaInput id="content" label="Rincian" v-model="form.content" :error="form.errors.content" rows="8"
                    required />
                <DateInput id="deadline" label="Batas Pengumpulan" v-model="form.deadline"
                    :error="form.errors.deadline" />
                <SingleFileInput v-model="form.path_file" v-model:deleteList="form.delete_files"
                    delete-identifier="path_file" label="Lampiran Tambahan"
                    :existing-file-url="sertification.asesmen?.path_file ? `/storage/${sertification.asesmen.path_file}` : null"
                    :is-marked-for-deletion="form.delete_files.includes('path_file')"
                    accept=".zip,.rar,.docx,.xlsx,.pptx,.jpg,.png,.jpeg,.pdf" :error="form.errors.path_file" />
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Simpan
                        </PrimaryButton>
                        <SecondaryButton type="button" @click="cancelEdit">Batal</SecondaryButton>
                    </div>
                    <DangerButton v-if="props.sertification.asesmen" type="button" @click="deleteAsesmen">
                        Hapus Asesmen
                    </DangerButton>
                </div>
                <div class="mt-2">
                    <label class="flex items-center">
                        <Checkbox id="send_notif" v-model:checked="form.send_notification" />
                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Kirim Notifikasi ke Asesi?</span>
                    </label>
                </div>
            </form>
        </div>

        <div v-if="viewMode === 'list'" class="p-3 sm:p-6 bg-white dark:bg-gray-800 shadow-md rounded-lg mt-2 max-w-3xl mx-auto">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                No
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Nama Asesi
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status Tugas
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="(asesi, index) in filteredAsesi" :key="asesi.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ index + 1 }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ asesi.student?.user?.name || 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <span v-if="asesi.path_file_asesmen"
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100">Diserahkan</span>
                                <span v-else
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100">Belum
                                    ada tugas dikumpulkan</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <SeeButton v-if="asesi.path_file_asesmen" @click="showDetailView(asesi)">
                                    Lihat
                                </SeeButton>
                                <span v-else class="text-xs text-gray-500">Belum ada tugas dikumpulkan</span>
                            </td>
                        </tr>

                        <tr v-if="!props.filteredAsesi.length">
                            <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                Belum ada asesi yang diberikan hak akses ke menu asesmen
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div v-if="viewMode === 'detail'" class="p-3 sm:p-6 bg-white dark:bg-gray-800 shadow-md rounded-lg mt-2 max-w-3xl mx-auto">
            <div class="flex flex-col-reverse sm:flex-row sm:justify-between sm:items-center gap-3 mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Tugas Terkumpul - {{ selectedAsesi?.student?.user?.name }}
                </h3>
                <BackButton @click="showListView" class="self-end sm:self-auto" />
            </div>
            <div v-if="selectedAsesi.path_file_asesmen">
                <FileCard 
                    :title="selectedAsesi.path_file_asesmen"
                    :href="`/storage/${selectedAsesi.path_file_asesmen}`"
                    icon="file"
                    status="Sudah Dikumpulkan"
                />
            </div>
            <p v-else class="text-sm text-gray-500">Tidak ada file yang ditemukan untuk asesi ini.</p>
        </div>
    </AdminLayout>
</template>
