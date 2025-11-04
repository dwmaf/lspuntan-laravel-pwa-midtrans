<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    label: String,
    options: Array,
    modelValue: [String, Number]
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const dropdownRef = ref(null);

const selectedOptionText = computed(() => {
    const selected = props.options.find(option => option.value == props.modelValue);
    return selected ? selected.text : `Semua ${props.label}`;
});

const selectOption = (value) => {
    emit('update:modelValue', value);
    isOpen.value = false;
};

const handleClickOutside = (event) => {
    if (isOpen.value && dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        isOpen.value = false;
    }
};

onMounted(() => document.addEventListener('mousedown', handleClickOutside));
onUnmounted(() => document.removeEventListener('mousedown', handleClickOutside));
</script>

<template>
    <div class="relative w-full" ref="dropdownRef">
        
        <button @click="isOpen = !isOpen"
            class="mt-1 w-full flex items-center justify-between gap-2 px-3 py-2 text-sm text-left font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z">
                </path>
            </svg>
            <span class="truncate">{{ selectedOptionText }}</span>
            <svg class="w-4 h-4 transition-transform shrink-0" :class="{ 'rotate-180': isOpen }"
                fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
            </svg>
        </button>

        <div v-if="isOpen"
            class="absolute right-0 mt-2 w-full max-h-60 overflow-y-auto bg-white dark:bg-gray-800 rounded-md shadow-lg z-20 border border-gray-200 dark:border-gray-600">
            <div class="py-1">
                <button @click="selectOption('')" :class="{ 'bg-indigo-50 dark:bg-indigo-900': !modelValue }"
                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    Semua {{ label }}
                </button>
                <button v-for="option in options" :key="option.value" @click="selectOption(option.value)"
                    :class="{ 'bg-indigo-50 dark:bg-indigo-900': modelValue == option.value }"
                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    {{ option.text }}
                </button>
            </div>
        </div>
    </div>
</template>