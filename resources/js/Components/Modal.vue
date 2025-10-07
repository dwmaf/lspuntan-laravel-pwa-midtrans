<script setup>
import { computed, onMounted, ref, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: '2xl',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['close']);

const dialog = ref(null);

// Gunakan 'watch' untuk sinkronisasi state modal
watch(
    () => props.show,
    (show) => {
        if (show) {
            document.body.style.overflow = 'hidden';
            dialog.value?.showModal(); // Buka modal bawaan browser
        } else {
            document.body.style.overflow = '';
            dialog.value?.close(); // Tutup modal bawaan browser
        }
    }
);

// Fungsi ini akan dipanggil oleh event bawaan <dialog>
const handleDialogClose = () => {
    if (props.closeable) {
        emit('close');
    }
};

// Logika untuk class
const maxWidthClass = computed(() => {
    return {
        sm: 'sm:max-w-sm',
        md: 'sm:max-w-md',
        lg: 'sm:max-w-lg',
        xl: 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl',
    }[props.maxWidth];
});

// Mencegah penutupan dari klik di backdrop jika tidak closeable
const handleCancel = (event) => {
    if (!props.closeable) {
        event.preventDefault();
    }
}
</script>

<template>
    <dialog ref="dialog" @close="handleDialogClose" @cancel="handleCancel" class="p-0 bg-transparent">
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div @click.self="handleDialogClose" class="absolute inset-0 bg-black/50 "></div>
            <div class="relative transform overflow-hidden rounded-lg bg-white shadow-xl transition-all sm:w-full dark:bg-gray-800"
                :class="maxWidthClass">
                <slot />
            </div>
        </div>
    </dialog>
</template>