<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import CustomHeader from '@/Components/CustomHeader.vue';
import AdminSertifikasiMenu from "@/Components/AdminSertifikasiMenu.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import EditButton from "@/Components/EditButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import MultiFileInput from "@/Components/MultiFileInput.vue";
import ToggleSwitch from "@/Components/ToggleSwitch.vue";
import DateInput from "@/Components/DateInput.vue";
import { useForm, usePage, Link, router } from "@inertiajs/vue3";
import { ref, computed, onMounted } from 'vue';


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
    newFiles: [],
    is_published: false,
    delete_files_collection: [],
});


const enterEditMode = () => {
    form.content = props.sertification.asesmen?.content || 'Isi rincian tugas asesmen di sini...';
    form.deadline = props.sertification.asesmen?.deadline || '';
    form.is_published = !!(props.sertification.asesmen?.published_at);
    isEditing.value = true;
};

const cancelEdit = () => {
    isEditing.value = false;
    form.reset();
    form.clearErrors();
};


onMounted(() => {
    if (!props.sertification.asesmen) {
        enterEditMode();
    }
    if (props.initialAsesiId) {
        const asesiToOpen = props.filteredAsesi.find(a => a.id == props.initialAsesiId);
        if (asesiToOpen) {
            console.log('asesi ditemukan, ');
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
</script>
<template>
    <AdminLayout>
        <CustomHeader judul="Asesmen"/>
        <AdminSertifikasiMenu :sertification-id="props.sertification.id" />
        <!-- Mode Tampilan -->
        <div v-if="!isEditing" class="py-3 px-5 bg-white dark:bg-gray-800 rounded-lg shadow-md mb-2">
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
                        <h5 v-if="props.sertification.asesmen"
                            class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                            {{ props.sertification.asesmen.name || 'Admin' }}
                        </h5>

                        <div v-if="props.sertification.asesmen" class="text-xs text-gray-400">
                            {{ new Date(props.sertification.asesmen.created_at).toLocaleString() }}
                            <span v-if="props.sertification.asesmen.updated_at != props.sertification.asesmen.created_at">(diedit)</span>
                        </div>
                    </div>
                </div>
                <EditButton @click="enterEditMode">Edit</EditButton>
            </div>

            <div v-if="props.sertification.asesmen?.content" v-html="props.sertification.asesmen.content.replace(/\n/g, '<br>')"
                class="font-medium text-sm text-gray-800 dark:text-gray-100"></div>
            <p v-else class="text-sm font-medium text-gray-700 dark:text-gray-300">
                Asesor belum memberikan rincian tugas asesmen, buat tugas asesmen agar asesi bisa mengumpulkan tugas
                asesmen
            </p>

            <div class="flex">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mr-1">Batas Akhir Pengumpulan : </dt>
                <dd v-if="props.sertification.asesmen" class="text-sm text-gray-900 dark:text-gray-100">
                    {{ props.sertification.asesmen.deadline }}
                </dd>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
                <div v-if="props.sertification.asesmen" v-for="file in props.sertification.asesmen.asesmenfiles"
                    :key="file.id"
                    class="flex items-center justify-between gap-4 px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md text-xs">
                    <a :href="`/storage/${file.path_file}`" target="_blank"
                        class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-500 hover:underline truncate flex-1">
                        {{ file.path_file.split('/').pop() }}
                    </a>
                </div>
            </div>
        </div>


        <!-- Mode edit  -->
        <div v-else class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md flex flex-col gap-2">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Edit Rincian Tugas Asesmen</h3>
            </div>
            <form @submit.prevent="submit" class="flex flex-col gap-4">
                <div>
                    <InputLabel value="Rincian" required/>
                    <textarea v-model="form.content" rows="8"
                        class="mt-1 w-full text-sm p-3 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100"></textarea>
                    <InputError :message="form.errors.content" />
                </div>
                <div>
                    <InputLabel value="Batas Pengumpulan" />
                    <DateInput v-model="form.deadline" />
                    <InputError :message="form.errors.deadline" />
                </div>
                
                
                <MultiFileInput v-model="form.newFiles" v-model:deleteList="form.delete_files_collection"
                :existing-files="props.sertification.asesmen?.asesmenfiles ?? []" 
                    label="Lampiran" :max-files="5" accept=".jpg,.png,.jpeg,.pdf,.docx" :error="form.errors.newFiles"
                    :error-list="form.errors['newFiles.0']" />
                <div>
                    <div class="flex items-center justify-between">
                        <InputLabel for="is_published" value="Publikasikan Instruksi?" />
                        <ToggleSwitch id="is_published" v-model="form.is_published" />
                    </div>
                    <InputError :message="form.errors.is_published" />
                </div>
                <div class="flex items-center gap-2">
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Simpan
                    </PrimaryButton>
                    <SecondaryButton type="button" @click="cancelEdit">Batal</SecondaryButton>
                </div>
            </form>
        </div>

        <div v-if="viewMode === 'list'" class="p-6 bg-white dark:bg-gray-800 shadow-md rounded-lg mt-2">
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

                        <tr v-for="(asesi, index) in props.filteredAsesi" :key="asesi.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ index + 1 }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ asesi.student?.user?.name || 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <span v-if="asesi.asesiasesmenfiles.length"
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100">Diserahkan</span>
                                <span v-else
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100">Belum
                                    ada tugas dikumpulkan</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">

                                <button v-if="asesi.asesiasesmenfiles.length > 0" @click="showDetailView(asesi)"
                                    class="cursor-pointer px-2 py-1 text-sm font-medium text-white bg-indigo-500 hover:bg-indigo-600 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-600 dark:hover:bg-indigo-700 dark:focus:ring-indigo-700">
                                    Lihat
                                </button>

                                <span v-else class="text-xs text-gray-500">Belum ada tugas dikumpulkan</span>

                            </td>
                        </tr>

                        <tr v-if="!props.filteredAsesi.length">
                            <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                Belum ada pendaftar yang memenuhi kriteria (Dilanjutkan Asesmen dan Pembayarannya
                                Terverifikasi)
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
        <div v-if="viewMode === 'detail'" class="p-6 bg-white dark:bg-gray-800 shadow-md rounded-lg mt-2">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Tugas Terkumpul - {{ selectedAsesi?.student?.user?.name }}
                </h3>
                <SecondaryButton @click="showListView">
                    &larr; Kembali ke Daftar
                </SecondaryButton>
            </div>

            <div v-if="selectedAsesi?.asesiasesmenfiles?.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <div v-for="file in selectedAsesi.asesiasesmenfiles" :key="file.id"
                    class="flex items-center justify-between gap-4 px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md text-xs">
                    <a :href="`/storage/${file.path_file}`" target="_blank"
                        class="text-blue-600 dark:text-blue-400 hover:underline truncate flex-1">
                        {{ file.path_file.split('/').pop() }}
                    </a>
                </div>
            </div>
            <p v-else class="text-sm text-gray-500">Tidak ada file yang ditemukan untuk asesi ini.</p>
        </div>
    </AdminLayout>
</template>
