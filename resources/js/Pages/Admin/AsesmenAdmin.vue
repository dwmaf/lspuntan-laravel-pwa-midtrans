<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AdminSertifikasiMenu from "@/Components/AdminSertifikasiMenu.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import EditButton from "@/Components/EditButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import DateInput from "../../Components/DateInput.vue";
import FileInput from "@/Components/FileInput.vue";
import { useForm, usePage, Link, router } from "@inertiajs/vue3";
import { ref, computed, onMounted } from 'vue';


const props = defineProps({
    sertification: Object,
    filteredAsesi: Array,
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
    rincian_tugas_asesmen: '',
    batas_pengumpulan_tugas_asesmen: '',
    newFiles: [],
    delete_files_collection: [],
});
const displayedFiles = computed(() => {
    // Filter file lama yang TIDAK ada di daftar hapus
    const existingFiles = (props.sertification.asesmenfiles || [])
        .filter(file => !form.delete_files_collection.includes(file.id))
        .map(file => ({ ...file, isNew: false })); // Tandai sebagai file lama

    // Map file baru untuk memiliki struktur yang sama
    const newFiles = (form.newFiles || []).map((file, index) => ({
        id: `new-${index}-${file.name}`, // Buat ID unik sementara
        path_file: file.name,
        fileObject: file,
        isNew: true, // Tandai sebagai file baru
    }));

    return [...existingFiles, ...newFiles];
});

const enterEditMode = () => {
    form.rincian_tugas_asesmen = props.sertification.rincian_tugas_asesmen || 'Isi rincian tugas asesmen di sini...';
    const batas = props.sertification.batas_pengumpulan_tugas_asesmen;

    form.batas_pengumpulan_tugas_asesmen = batas ? batas.replace(' ', 'T').slice(0, 16) : null;
    isEditing.value = true;
};

const cancelEdit = () => {
    isEditing.value = false;
    form.reset();
    form.clearErrors();
};


onMounted(() => {
    if (!props.sertification.rincian_tugas_asesmen) {
        enterEditMode();
    }
});

const remainingFileSlots = computed(() => {
    const maxTotalFiles = 5;
    return maxTotalFiles - displayedFiles.value.length;
});
console.log(remainingFileSlots);



const submit = () => {
    form.post(route('admin.sertifikasi.assessment.update', props.sertification.id), {
        onSuccess: () => {
            cancelEdit();
        },
    });
};


const removeFile = (file) => {
    if (file.isNew) {
        // Hapus dari array form.newFiles
        form.newFiles = form.newFiles.filter(f => f !== file.fileObject);
    } else {
        // Tambahkan ID ke daftar hapus
        if (!form.delete_files_collection.includes(file.id)) {
            form.delete_files_collection.push(file.id);
        }
    }
};

const handleFileSelection = (newlySelectedFiles) => {
    const existingFileCount = (props.sertification.asesmenfiles || [])
        .filter(file => !form.delete_files_collection.includes(file.id))
        .length;
    const maxTotalFiles = 5;
    const combinedNewFiles = [...form.newFiles, ...newlySelectedFiles];
    const limitForNewFiles = maxTotalFiles - existingFileCount;
    form.newFiles = combinedNewFiles.slice(0, limitForNewFiles);
};
</script>
<template>
    <AdminLayout>

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Sertifikasi
            </h2>
        </template>
        <AdminSertifikasiMenu :sertification-id="props.sertification.id" />
        <!-- Mode Tampilan -->
        <div v-if="!isEditing" class="py-3 px-5 bg-white dark:bg-gray-800 rounded-lg shadow-md mb-2">
            <div class="flex justify-between items-center mb-2">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex-shrink-0">
                        <svg class="h-10 w-10 text-gray-400 dark:text-gray-600 rounded-full bg-gray-200 dark:bg-gray-700 p-1"
                            fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <h5 v-if="props.sertification.pembuatrinciantugasasesmen"
                            class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                            {{ props.sertification.pembuatrinciantugasasesmen.name || 'Admin' }}
                        </h5>

                        <div class="text-xs text-gray-400">
                            {{ props.sertification.tanggal_rincian_asesmen_dibuat_formatted }}
                            <span v-if="props.sertification.tugasasesmen_updatedat">(diedit)</span>

                        </div>
                    </div>
                </div>
                <EditButton @click="enterEditMode">Edit</EditButton>
            </div>

            <div v-if="props.sertification.rincian_tugas_asesmen"
                v-html="props.sertification.rincian_tugas_asesmen.replace(/\n/g, '<br>')"
                class="font-medium text-sm text-gray-800 dark:text-gray-100"></div>
            <p v-else class="text-sm font-medium text-gray-700 dark:text-gray-300">
                Asesor belum memberikan rincian tugas asesmen, buat tugas asesmen agar asesi bisa mengumpulkan tugas
                asesmen
            </p>

            <div class="flex">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mr-1">Batas Akhir Pengumpulan : </dt>
                <dd class="text-sm text-gray-900 dark:text-gray-100">
                    {{ props.sertification.batas_pengumpulan_tugas_asesmen_formatted }}
                </dd>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
                <div v-for="file in props.sertification.asesmenfiles" :key="file.id"
                    class="flex items-center justify-between gap-4 px-3 py-2 border-1 border-gray-300 dark:border-gray-700 rounded-md text-xs">
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
                    <InputLabel value="Rincian" />
                    <textarea v-model="form.rincian_tugas_asesmen" rows="8"
                        class="mt-1 w-full text-sm p-3 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100"></textarea>
                    <InputError :message="form.errors.rincian_tugas_asesmen" />
                </div>
                <div>
                    <InputLabel value="Batas Pengumpulan" />
                    <DateInput v-model="form.batas_pengumpulan_tugas_asesmen" />
                    <InputError :message="form.errors.batas_pengumpulan_tugas_asesmen" />
                </div>

                <div>
                    <InputLabel :value="`Lampiran ${props.sertification.asesmenfiles.length}/5`" />
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2">

                        <!-- Loop melalui file yang akan ditampilkan -->
                        <div v-for="file in displayedFiles" :key="file.id"
                            class="flex items-center justify-between gap-4 px-3 py-2 border rounded-md text-xs"
                            :class="file.isNew ? 'border-green-400 dark:border-green-600' : 'border-gray-300 dark:border-gray-700'">
                            <div class="flex items-center gap-2 min-w-0"> <!-- 1. Tambahkan min-w-0 di sini -->
                                <!-- Tampilan untuk file lama -->
                                <template v-if="!file.isNew">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 flex-shrink-0"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <a :href="`/storage/${file.path_file}`" target="_blank"
                                        class="text-sm text-blue-500 hover:text-blue-400 truncate">
                                        <!-- 2. truncate tetap di sini -->
                                        {{ file.path_file.split('/').pop() }}
                                    </a>
                                </template>
                                <!-- Tampilan untuk file baru -->
                                <template v-else>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500 flex-shrink-0"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-gray-800 dark:text-gray-200 truncate">
                                        {{ file.path_file }}
                                    </span>
                                </template>
                            </div>
                            <button @click.prevent="removeFile(file)" type="button"
                                class="cursor-pointer p-1 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-300 dark:hover:bg-gray-600 hover:text-gray-800 dark:hover:text-gray-200 transition-colors">
                                <FontAwesomeIcon icon="fa-xmark" />
                            </button>
                        </div>

                        <p v-if="displayedFiles.length === 0" class="text-sm text-gray-500 col-span-full">Belum ada file
                            lampiran.</p>

                    </div>

                    <!-- Sembunyikan input jika slot sudah habis -->
                    <FileInput v-if="remainingFileSlots > 0" @update:modelValue="handleFileSelection"
                        accept="image/jpeg,image/png,image/jpg,application/pdf,docx" multiple
                        :max-files="remainingFileSlots" />
                    <p class="text-[11px] text-gray-500">Tipe: JPG, JPEG, PNG, PDF, DOCX, PPTX, XLS/XLSX. Maks 5 total.
                    </p>
                    <InputError :message="form.errors.newFiles" />
                    <InputError :message="form.errors['newFiles.0']" />
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
