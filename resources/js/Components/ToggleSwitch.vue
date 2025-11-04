<script setup>
import { computed } from 'vue';

// Komponen ini akan menerima 'modelValue' dan mengirim event 'update:modelValue'
// Inilah cara kerja v-model di Vue 3
const props = defineProps({
    modelValue: {
        type: Boolean,
        required: true,
    },
});

const emit = defineEmits(['update:modelValue']);

// Fungsi untuk mengubah nilai saat diklik
const toggle = () => {
    emit('update:modelValue', !props.modelValue);
};

// Computed property untuk mengubah kelas CSS secara dinamis
// Mengubah warna latar belakang toggle berdasarkan statusnya (on/off)
const backgroundClasses = computed(() => {
    return props.modelValue
        ? 'bg-indigo-600 dark:bg-indigo-500' // Warna saat ON
        : 'bg-gray-200 dark:bg-gray-600';     // Warna saat OFF
});

// Computed property untuk menggeser lingkaran kecil
const dotClasses = computed(() => {
    return props.modelValue
        ? 'translate-x-5' // Posisi saat ON
        : 'translate-x-0';  // Posisi saat OFF
});
</script>

<template>
    <button
        type="button"
        @click="toggle"
        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
        :class="backgroundClasses"
        role="switch"
        :aria-checked="modelValue"
    >
        <span
            aria-hidden="true"
            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
            :class="dotClasses"
        />
    </button>
</template>