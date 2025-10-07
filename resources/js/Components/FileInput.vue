<!-- filepath: d:\Laravel-App\lsp-untan-laravel-pwa\resources\js\Components\FileInput.vue -->
<script setup>
import { ref } from 'vue';

const props = defineProps({
    modelValue: [File, Array, null], // Prop untuk v-model
    maxSize: Number, // Ukuran maksimum dalam Kilobytes (KB)
    accept: String, // Tipe file yang diterima, misal: 'image/jpeg,image/png'
});

// Mendefinisikan event yang akan di-emit oleh komponen ini.
// 'update:modelValue' adalah event standar yang digunakan oleh v-model.
const emit = defineEmits(['update:modelValue']);

const input = ref(null);
const localError = ref('');
function validateFile(file) {
    // Validasi Ukuran
    if (props.maxSize && (file.size / 1024) > props.maxSize) {
        return `Ukuran file tidak boleh melebihi ${props.maxSize} KB.`;
    }
    // Validasi Tipe
    if (props.accept) {
        const acceptedTypes = props.accept.split(',').map(t => t.trim());
        if (!acceptedTypes.includes(file.type)) {
            return `Tipe file tidak valid. Harap unggah: ${props.accept}`;
        }
    }
    return ''; // Tidak ada error
}
function onFileChange(event) {
    // Reset error setiap kali ada perubahan
    localError.value = '';
    const files = event.target.files;

    if (files.length === 0) {
        emit('update:modelValue', input.value.multiple ? [] : null);
        return;
    }

    // 3. Terapkan validasi
    if (input.value.multiple) {
        const fileList = Array.from(files);
        for (const file of fileList) {
            const errorMessage = validateFile(file);
            if (errorMessage) {
                localError.value = errorMessage;
                input.value.value = ''; // Reset input file
                emit('update:modelValue', []); // Emit nilai kosong
                return; // Hentikan proses jika satu file gagal
            }
        }
        emit('update:modelValue', fileList);
    } else {
        const file = files[0];
        const errorMessage = validateFile(file);
        if (errorMessage) {
            localError.value = errorMessage;
            input.value.value = ''; // Reset input file
            emit('update:modelValue', null); // Emit nilai kosong
            return; // Hentikan proses
        }
        emit('update:modelValue', file);
    }
}

// Expose fungsi untuk membersihkan input jika diperlukan dari parent
defineExpose({
    clear: () => {
        if (input.value) {
            input.value.value = '';
            onFileChange({ target: { files: [] } });
        }
    }
});
</script>

<template>
    <input type="file" ref="input" :accept="accept" @change="onFileChange"
        class="text-md w-full px-3 py-2 dark:text-gray-300 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-hidden dark:bg-gray-900 focus-ring-2 focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600" />
    <p v-if="localError" class="mt-2 text-sm text-red-600 dark:text-red-400">
        {{ localError }}
    </p>
</template>