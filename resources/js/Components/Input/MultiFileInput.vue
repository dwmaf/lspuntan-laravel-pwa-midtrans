<script setup>
import { computed, ref } from 'vue';
import FileInput from '@/Components/Input/FileInput.vue';
import FileIcon from '@/Components/FileIcon.vue';
import InputLabel from '@/Components/Input/InputLabel.vue';
import InputError from '@/Components/Input/InputError.vue';
import { X, Download, UploadCloud } from 'lucide-vue-next';

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
    existingFiles: {
        type: Array,
        default: () => [],
    },
    deleteList: {
        type: Array,
        default: () => [],
    },
    label: String,
    maxFiles: {
        type: Number,
        default: 5,
    },
    accept: String,
    required: { type: Boolean, default: false },
    error: String,
    errorList: String,
    inputId: {
        type: String,
        default: 'file-input'
    },
});

const emit = defineEmits(['update:modelValue', 'update:deleteList']);

const fileInputRef = ref(null);
const isDragActive = ref(false);
const localValidationError = ref('');

// 1. Buat satu daftar gabungan untuk semua file yang akan ditampilkan.
const displayedFiles = computed(() => {
    // Filter file lama yang TIDAK ada di daftar hapus, tandai sebagai 'isNew: false'.
    const visibleExisting = props.existingFiles
        .filter(file => !props.deleteList.includes(file.id))
        .map(file => ({ ...file, isNew: false, uniqueId: `existing-${file.id}` }));

    // Map file baru untuk memiliki struktur yang sama, tandai sebagai 'isNew: true'.
    const newFiles = props.modelValue.map((file, index) => ({
        id: `new-${index}`, // ID sementara untuk rendering
        path_file: file.name, // Gunakan nama file sebagai path sementara
        fileObject: file, // Simpan objek file asli
        isNew: true,
        uniqueId: `new-${index}-${file.name}`
    }));

    return [...visibleExisting, ...newFiles];
});

// 2. Hitung sisa slot berdasarkan daftar gabungan. Ini lebih akurat.
const remainingSlots = computed(() => {
    return props.maxFiles - displayedFiles.value.length;
});

// 3. Fungsi hapus yang cerdas, bisa membedakan file lama dan baru.
const removeFile = (file) => {
    if (file.isNew) {
        // Jika file baru, hapus dari 'modelValue' (form.newFiles)
        const updatedNewFiles = props.modelValue.filter(f => f !== file.fileObject);
        emit('update:modelValue', updatedNewFiles);
    } else {
        // Jika file lama, tambahkan ID-nya ke 'deleteList' (form.delete_files)
        const currentDeleteList = [...props.deleteList];
        if (!currentDeleteList.includes(file.id)) {
            currentDeleteList.push(file.id);
            emit('update:deleteList', currentDeleteList);
        }
    }
};

// 4. Logika handle file selection yang lebih sederhana dan benar.
const handleFileSelection = (newlySelectedFiles) => {
    // Gabungkan file baru yang sudah ada dengan yang baru saja dipilih.
    const combinedNewFiles = [...props.modelValue, ...newlySelectedFiles];
    // Potong array HANYA berdasarkan sisa slot yang tersedia.
    const limitedFiles = combinedNewFiles.slice(0, props.modelValue.length + remainingSlots.value);
    emit('update:modelValue', limitedFiles);
};

// UI Handling functions
function triggerBrowse() {
    fileInputRef.value?.browse();
}

function handleDragOver(e) {
    if (remainingSlots.value > 0) isDragActive.value = true;
}

function handleDragLeave(e) {
    isDragActive.value = false;
}

function handleDrop(e) {
    isDragActive.value = false;
    if (remainingSlots.value > 0) {
        fileInputRef.value?.handleDrop(e.dataTransfer.files);
    }
}

function handleValidationError(msg) {
    localValidationError.value = msg;
    setTimeout(() => {
        localValidationError.value = '';
    }, 3000);
}

function getDownloadUrl(file) {
    if (file.isNew && file.fileObject) {
        return URL.createObjectURL(file.fileObject);
    }
    if (!file.isNew && file.path_file) {
        // Asumsi path relatif ke storage, atau bisa jadi full URL
        return file.path_file.startsWith('http') ? file.path_file : `/storage/${file.path_file}`;
    }
    return '#';
}
</script>

<template>
    <div class="w-full">
        <InputLabel :for="inputId" :value="`${label} (${displayedFiles.length}/${maxFiles})`" :required="required" />

        <!-- Main Container (DropZone + List) -->
        <div class="w-full p-2 rounded-xl flex flex-col relative bg-zinc-200 dark:bg-gray-700 " :class="[
            isDragActive ? 'bg-indigo-50 dark:bg-indigo-900/20 ring-2 ring-indigo-500' : '',
            remainingSlots > 0 ? 'cursor-pointer hover:bg-zinc-300 dark:hover:bg-gray-700' : 'cursor-default'
        ]" @dragover.prevent="handleDragOver" @dragleave.prevent="handleDragLeave" @drop.prevent="handleDrop"
            @click="remainingSlots > 0 ? triggerBrowse() : null">
            <!-- Drop Hint / Browse Trigger (Only visible if slots remain) -->
            <div v-if="remainingSlots > 0"
                class="pointer-events-none flex flex-col items-center gap-1 text-center py-4 flex-1 justify-center">
                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                    Drop files here or<span class="underline"> Browse</span>
                </p>
            </div>
            <!-- List Files -->
            <transition-group name="list" tag="div" class="flex flex-col gap-2 w-full">
                <div v-for="file in displayedFiles" :key="file.uniqueId"
                    class="relative overflow-hidden rounded-lg shadow-sm" @click.stop>
                    <!-- Stop propogation so clicking card doesn't open browse -->

                    <div class="text-white rounded-lg py-1.5 px-2 flex items-center gap-3 shadow-inner"
                        :class="file.isNew ? 'bg-[#369763]' : 'bg-[#635f5d]'">
                        <!-- Icon -->
                        <div class="shrink-0 text-gray-300">
                            <FileIcon :path="file.path_file" class="w-7 h-7" />
                        </div>

                        <!-- Info -->
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium truncate">
                                {{ file.path_file.split('/').pop() }}
                            </p>
                            <p class="text-xs" :class="file.isNew ? 'text-[#9bcbb1]' : 'text-gray-400'">
                                {{ file.isNew && file.fileObject ? (file.fileObject.size / 1024).toFixed(1) + ' KB' :
                                    'Saved' }}
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-2">
                            <!-- Download -->
                            <a v-if="!file.isNew" :href="getDownloadUrl(file)" target="_blank"
                                class="p-1.5 rounded-full hover:bg-gray-600 transition-colors text-gray-300 hover:text-white"
                                title="Open in new tab">
                                <Download class="w-4 h-4" />
                            </a>

                            <!-- Remove -->
                            <button @click="removeFile(file)" type="button"
                                class="p-1.5 rounded-full bg-black hover:outline-2 hover:outline-white transition-colors text-gray-300"
                                title="Remove">
                                <X class="w-3.5 h-3.5" :stroke-width="4" />
                            </button>
                        </div>
                    </div>
                </div>
            </transition-group>



            <!-- Hidden Input -->
            <FileInput ref="fileInputRef" :model-value="modelValue" @update:modelValue="handleFileSelection"
                @validation-error="handleValidationError" :accept="accept"
                :required="required && displayedFiles.length === 0" multiple :max-files="remainingSlots" />
        </div>

        <p v-if="localValidationError" class="mt-2 text-sm text-red-600 dark:text-red-400 animate-pulse">
            {{ localValidationError }}
        </p>
        <InputError :message="error" class="mt-2" />
        <InputError :message="errorList" class="mt-2" />
    </div>
</template>

<style scoped>
.list-enter-active,
.list-leave-active {
    transition: all 0.3s ease;
}

.list-enter-from,
.list-leave-to {
    opacity: 0;
    transform: translateY(10px);
}
</style>