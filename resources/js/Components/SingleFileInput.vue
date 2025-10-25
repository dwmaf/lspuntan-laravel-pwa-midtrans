<script setup>
import { computed } from 'vue';
import FileInput from './FileInput.vue';
import InputLabel from './InputLabel.vue';
import InputError from './InputError.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

const props = defineProps({
    modelValue: [File, null],
    existingFileUrl: String, // URL file LAMA yang sudah ada
    isMarkedForDeletion: Boolean, // Apakah file LAMA ditandai untuk dihapus
    label: String,
    isLabelRequired: {type: Boolean, default: false},
    accept: String,
    required: Boolean,
    error: String,
    templateUrl: String,
});

const emit = defineEmits(['update:modelValue', 'remove']);

const existingFileName = computed(() => {
    if (!props.existingFileUrl) {
        return '';
    }
    // Memecah URL berdasarkan '/' dan mengambil bagian terakhir (nama file)
    return props.existingFileUrl.split('/').pop();
});

const showInput = computed(() => {
    // Tampilkan input jika:
    // 1. Tidak ada file baru yang dipilih
    if (props.modelValue) return false;
    // 2. Tidak ada file lama, ATAU file lama sudah ditandai untuk dihapus
    return !props.existingFileUrl || props.isMarkedForDeletion;
});
</script>

<template>
    <div>
        <InputLabel :value="label" :required="isLabelRequired" />
        <a v-if="templateUrl" :href="templateUrl" class="text-blue-500 hover:text-blue-300 text-sm" target="_blank">
            Lihat Template
        </a>

        <!-- Preview untuk file BARU yang dipilih -->
        <div v-if="modelValue" class="mt-2 flex items-center justify-between gap-4 px-3 py-2 border border-green-400 dark:border-green-600 rounded-md text-xs bg-green-50 dark:bg-green-900/20">
            <div class="flex items-center gap-2 min-w-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ modelValue.name }}</span>
            </div>
            <button @click="$emit('remove')" type="button" class="cursor-pointer p-1 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-300 dark:hover:bg-gray-600 hover:text-gray-800 dark:hover:text-gray-200"><FontAwesomeIcon icon="fa-xmark" /></button>
        </div>

        <!-- Preview untuk file LAMA yang ada -->
        <div v-else-if="existingFileUrl && !isMarkedForDeletion" class="mt-2 flex items-center justify-between gap-4 px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md text-xs">
            <div class="flex items-center gap-2 min-w-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <a :href="existingFileUrl" class="text-sm text-blue-500 hover:text-blue-400 truncate" target="_blank">{{ existingFileName }}</a>
            </div>
            <button @click="$emit('remove')" type="button" class="cursor-pointer p-1 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-300 dark:hover:bg-gray-600 hover:text-gray-800 dark:hover:text-gray-200"><FontAwesomeIcon icon="fa-xmark" /></button>
        </div>

        <!-- Input File -->
        <div v-if="showInput" class="mt-2">
            <FileInput :model-value="modelValue" @update:model-value="$emit('update:modelValue', $event)" :accept="accept" :required="required" />
        </div>

        <InputError :message="error" />
    </div>
</template>