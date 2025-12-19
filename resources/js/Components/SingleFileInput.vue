<script setup>
import { computed } from 'vue';
import FileInput from '@/Components/Input/FileInput.vue';
import FileIcon from '@/Components/FileIcon.vue';
import InputLabel from '@/Components/Input/InputLabel.vue';
import InputError from '@/Components/Input/InputError.vue';
import { X } from 'lucide-vue-next';

const props = defineProps({
    modelValue: [File, null],
    deleteList: Array,
    deleteIdentifier: String,
    existingFileUrl: String,
    isMarkedForDeletion: Boolean,
    label: String,
    isLabelRequired: { type: Boolean, default: false },
    accept: String,
    required: Boolean,
    error: String,
    templateUrl: String,
});

const emit = defineEmits(['update:modelValue', 'update:deleteList']);

const existingFileName = computed(() => {
    if (!props.existingFileUrl) {
        return '';
    }
    return props.existingFileUrl.split('/').pop();
});

const showInput = computed(() => {
    if (props.modelValue) return false;
    return !props.existingFileUrl || props.isMarkedForDeletion;
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
</script>

<template>
    <div>
        <InputLabel :value="label" :required="isLabelRequired" />
        <a v-if="templateUrl" :href="templateUrl" class="text-blue-500 hover:text-blue-300 text-sm" target="_blank">
            Lihat Template
        </a>

        <!-- Preview untuk file BARU yang dipilih -->
        <div v-if="modelValue"
            class="mt-2 flex items-center justify-between gap-4 px-3 py-2 border border-green-400 dark:border-green-600 rounded-md text-xs bg-green-50 dark:bg-green-900/20">
            <div class="flex items-center gap-2 min-w-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500 shrink-0" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ modelValue.name }}</span>
            </div>
            <button @click="handleRemove" type="button"
                class="cursor-pointer rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-300 dark:hover:bg-gray-600 hover:text-gray-800 dark:hover:text-gray-200">
                <X class="w-4 mx-1" />
            </button>
        </div>

        <!-- Preview untuk file LAMA yang ada -->
        <div v-else-if="existingFileUrl && !isMarkedForDeletion"
            class="mt-2 flex items-center justify-between gap-4 px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md text-xs">
            <div class="flex items-center gap-2 min-w-0">
                <FileIcon :path="existingFileUrl" />
                <a :href="existingFileUrl" class="text-sm text-blue-500 dark:text-blue-400 hover:underline truncate"
                    target="_blank">{{ existingFileName }}</a>
            </div>
            <button @click="handleRemove" type="button"
                class="cursor-pointer rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-300 dark:hover:bg-gray-600 hover:text-gray-800 dark:hover:text-gray-200">
                <X class="w-4 mx-1" />
            </button>

        </div>

        <!-- Input File -->
        <div v-if="showInput" class="mt-2">
            <FileInput :model-value="modelValue" @update:model-value="$emit('update:modelValue', $event)"
                :accept="accept" :required="required" />
        </div>

        <InputError :message="error" />
    </div>
</template>