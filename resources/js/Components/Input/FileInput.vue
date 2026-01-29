<script setup>
import { ref } from 'vue';

const props = defineProps({
    modelValue: [File, Array, null],
    maxSize: Number, // KB
    accept: String,
    multiple: {
        type: Boolean,
        default: false
    },
    maxFiles: {
        type: Number,
        default: 0
    },
    required: {
        type: Boolean,
        default: false
    },
    id: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(['update:modelValue', 'validation-error']);

const input = ref(null);

function validateFile(file) {
    // Size Validation
    if (props.maxSize && (file.size / 1024) > props.maxSize) {
        return `Ukuran file tidak boleh melebihi ${props.maxSize} KB.`;
    }
    // Type Validation
    if (props.accept) {
        const acceptedTypes = props.accept.split(',').map(t => t.trim().toLowerCase());
        const fileType = file.type.toLowerCase();
        const fileName = file.name.toLowerCase();
        // Check if allow wildcard
        const isTypeAccepted = acceptedTypes.some(type => {
            if (type.startsWith('.')) {
                return fileName.endsWith(type);
            } else if (type.endsWith('/*')) {
                return fileType.startsWith(type.slice(0, -1));
            } else {
                return fileType === type;
            }
        });
        if (!isTypeAccepted) {
            return `Tipe file tidak valid. Harap unggah: ${props.accept}`;
        }
    }
    return '';
}

function handleFiles(files) {
    emit('validation-error', ''); // Clear errors

    if (files.length === 0) return;

    if (props.multiple && props.maxFiles > 0 && files.length > props.maxFiles) {
        emit('validation-error', `Anda hanya dapat memilih maksimal ${props.maxFiles} file.`);
        if (input.value) input.value.value = '';
        return;
    }

    if (props.multiple) {
        const fileList = Array.from(files);
        for (const file of fileList) {
            const error = validateFile(file);
            if (error) {
                emit('validation-error', error);
                if (input.value) input.value.value = '';
                return;
            }
        }
        emit('update:modelValue', fileList);
    } else {
        const file = files[0];
        const error = validateFile(file);
        if (error) {
            emit('validation-error', error);
            if (input.value) input.value.value = '';
            return;
        }
        emit('update:modelValue', file);
    }
}

function onFileChange(event) {
    handleFiles(event.target.files);
}

defineExpose({
    browse: () => input.value?.click(),
    handleDrop: (files) => handleFiles(files),
    clear: () => {
        if (input.value) {
            input.value.value = '';
        }
    }
});
</script>

<template>
    <input type="file" :id="id" ref="input" :accept="accept" @change="onFileChange" :multiple="multiple"
        :required="required"
        class="absolute w-px h-px p-0 -m-1px overflow-hidden clip-[rect(0,0,0,0)] whitespace-nowrap border-0 opacity-0"/>
</template>