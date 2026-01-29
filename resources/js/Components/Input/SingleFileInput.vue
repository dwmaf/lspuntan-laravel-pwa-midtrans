<script setup>
import { computed, ref } from 'vue';
import FileInput from '@/Components/Input/FileInput.vue';
import FileIcon from '@/Components/FileIcon.vue';
import InputLabel from '@/Components/Input/InputLabel.vue';
import InputError from '@/Components/Input/InputError.vue';
import { X, Download, UploadCloud } from 'lucide-vue-next';

const props = defineProps({
    modelValue: [File, null],
    deleteList: Array,
    deleteIdentifier: String,
    existingFileUrl: String,
    isMarkedForDeletion: Boolean,
    label: String,
    id: String,
    isLabelRequired: { type: Boolean, default: false },
    accept: String,
    required: Boolean,
    error: String,
    templateUrl: String,
});

const emit = defineEmits(['update:modelValue', 'update:deleteList']);

const fileInputRef = ref(null);
const isDragActive = ref(false);
const localValidationError = ref('');

const existingFileName = computed(() => {
    if (!props.existingFileUrl) {
        return '';
    }
    return props.existingFileUrl.split('/').pop();
});

const showDropZone = computed(() => {
    if (props.modelValue) return false;
    return !props.existingFileUrl || props.isMarkedForDeletion;
});

const fileDownloadUrl = computed(() => {
    if (props.modelValue) {
        return URL.createObjectURL(props.modelValue);
    }
    if (props.existingFileUrl && !props.isMarkedForDeletion) {
        return props.existingFileUrl;
    }
    return '#';
});

function handleRemove() {
    if (props.modelValue) {
        emit('update:modelValue', null);
    } else if (props.existingFileUrl && !props.isMarkedForDeletion) {
        if (props.deleteIdentifier && props.deleteList) {
            const newList = [...props.deleteList, props.deleteIdentifier];
            emit('update:deleteList', newList);
        }
    }
}

function triggerBrowse() {
    fileInputRef.value?.browse();
}

function handleDragOver(e) {
    isDragActive.value = true;
}

function handleDragLeave(e) {
    isDragActive.value = false;
}

function handleDrop(e) {
    isDragActive.value = false;
    fileInputRef.value?.handleDrop(e.dataTransfer.files);
}

function handleValidationError(msg) {
    localValidationError.value = msg;
    // Clear error after 3 seconds
    setTimeout(() => {
        localValidationError.value = '';
    }, 3000);
}
</script>

<template>
    <div class="w-full">
        <div class="flex justify-between items-center mb-1">
            <InputLabel :for="id" :value="label" :required="required" />
            <a v-if="templateUrl" :href="templateUrl" class="text-blue-500 hover:text-blue-300 text-sm font-medium"
                target="_blank">
                Lihat Template
            </a>
        </div>

        <!-- Main Drop Zone / File Display Container -->
        <div class="w-full p-2 rounded-xl  flex flex-col justify-center transition-all duration-300 ease-in-out relative bg-zinc-200 dark:bg-gray-700"
            :class="[
                isDragActive ? 'bg-indigo-50 dark:bg-indigo-900/20 ring-2 ring-indigo-500' : '',
                !showDropZone ? 'cursor-default' : 'cursor-pointer hover:bg-zinc-300 dark:hover:bg-gray-600'
            ]" @dragover.prevent="handleDragOver" @dragleave.prevent="handleDragLeave" @drop.prevent="handleDrop"
            @click="showDropZone ? triggerBrowse() : null">

            <!-- File Card (Visualisasi File Terpilih/Existing) -->
            <div v-if="!showDropZone" class="relative overflow-hidden rounded-lg shadow-sm">
                <!-- Bagian konten utama ala FilePond (Dark Panel) -->
                <div class="text-white rounded-lg py-1.5 px-2 flex items-center gap-3 shadow-inner"
                    :class="modelValue ? 'bg-[#369763]' : 'bg-[#635f5d]'">
                    <!-- Icon -->
                    <div class="shrink-0 text-gray-300">
                        <FileIcon :path="existingFileUrl || (modelValue ? modelValue.name : '')" class="w-7 h-7" />
                    </div>

                    <!-- Info File -->
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium truncate">
                            {{ modelValue ? modelValue.name : existingFileName }}
                        </p>
                        <p class="text-xs" :class="modelValue ? 'text-[#9bcbb1]' : 'text-gray-400'">
                            {{ modelValue ? (modelValue.size / 1024).toFixed(1) + ' KB' : 'File Saved' }}
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2">
                        <!-- Download Button -->
                        <a v-if="!modelValue" :href="fileDownloadUrl" target="_blank"
                            class="p-1.5 rounded-full hover:bg-gray-600 transition-colors text-gray-300 hover:text-white"
                            title="Open in new tab">
                            <Download class="w-4 h-4" />
                        </a>

                        <!-- Remove Button -->
                        <button @click.stop="handleRemove" type="button"
                            class="p-1.5 rounded-full bg-black hover:outline-2 hover:outline-white transition-colors text-gray-300"
                            title="Remove">
                            <X class="w-3.5 h-3.5" :stroke-width="4" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Empty State / Drop Prompt -->
            <div v-else class="pointer-events-none flex flex-col items-center gap-1 text-center py-3">
                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                    Drop files here or<span class="underline"> Browse</span>
                </p>
            </div>

            <!-- Hidden Input -->
            <FileInput ref="fileInputRef" :model-value="modelValue"
                @update:model-value="$emit('update:modelValue', $event)" @validation-error="handleValidationError"
                :accept="accept" :required="required" />
        </div>

        <!-- Error Messages -->
        <p v-if="localValidationError" class="mt-2 text-sm text-red-600 dark:text-red-400 animate-pulse">
            {{ localValidationError }}
        </p>
        <InputError :message="error" class="mt-1" />
    </div>
</template>