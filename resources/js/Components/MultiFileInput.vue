<script setup>
import { computed } from 'vue';
import FileInput from './FileInput.vue';
import FileIcon from './FileIcon.vue';
import InputLabel from './InputLabel.vue';
import InputError from './InputError.vue';
import { X } from 'lucide-vue-next';

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
    required: {type:Boolean, default: false},
    error: String,
    errorList: String,
});

const emit = defineEmits(['update:modelValue', 'update:deleteList']);
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


</script>

<template>
    <div>
        <InputLabel :value="`${label} (${displayedFiles.length}/${maxFiles})`" :required="required" />
        
        <div v-if="displayedFiles.length > 0" class="mt-2 space-y-2">
            <div v-for="file in displayedFiles" :key="file.uniqueId"
                class="flex items-center justify-between gap-2 px-3 py-2 border rounded-md text-xs"
                :class="file.isNew ? 'border-green-400 bg-green-50 dark:border-green-600 dark:bg-green-900/20' : 'border-gray-300 dark:border-gray-700'">
                
                <!-- Tampilan untuk file lama -->
                 
                <FileIcon v-if="!file.isNew" :path="file.path_file" />
                <a v-if="!file.isNew" :href="`/storage/${file.path_file}`" target="_blank"
                    class="text-sm text-blue-600 dark:text-blue-400 hover:underline truncate flex-1">
                    {{ file.path_file.split('/').pop() }}
                </a>
                
                <!-- Tampilan untuk file baru -->
                <svg v-if="file.isNew" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span v-if="file.isNew" class="text-sm text-gray-800 dark:text-gray-200 truncate flex-1">
                    {{ file.path_file }}
                </span>

                <!-- Tombol hapus yang sekarang memanggil fungsi 'removeFile' yang cerdas -->
                <button @click="removeFile(file)" type="button"
                    class="cursor-pointer rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-300 dark:hover:bg-gray-600">
                    <X class="w-4 mx-1"/>
                </button>
            </div>
        </div>

        <!-- Input file, hanya muncul jika masih ada slot -->
        <div v-if="remainingSlots > 0" class="mt-2">
            <FileInput
                @update:modelValue="handleFileSelection"
                :accept="accept"
                :required="required && displayedFiles.length === 0"
                multiple
                :max-files="remainingSlots"
            />
        </div>

        
        <p v-else class="mt-2 text-sm text-gray-500">Batas maksimal {{ maxFiles }} file telah tercapai.</p>
        <InputError :message="error" class="mt-2" />
        <InputError :message="errorList" class="mt-2" />
    </div>
</template>