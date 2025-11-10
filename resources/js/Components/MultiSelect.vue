<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import InputLabel from './InputLabel.vue';
import InputError from './InputError.vue';
import { X, Search } from 'lucide-vue-next';

const props = defineProps({
    modelValue: { // v-model: array of selected values (e.g., [1, 3])
        type: Array,
        required: true,
    },
    options: { // Array of objects: [{ value: 1, text: 'Admin' }, ...]
        type: Array,
        required: true,
    },
    disabled: { // Tambahkan prop ini
        type: Boolean,
        default: false,
    },
    label: String,
    error: String,
    required: Boolean,
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const searchQuery = ref('');
const dropdownRef = ref(null);

// Filter options based on search query
const filteredOptions = computed(() => {
    if (!searchQuery.value) {
        return props.options;
    }
    return props.options.filter(option =>
        option.text.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

// Get full objects of selected options to display their text
const selectedOptions = computed(() => {
    return props.options.filter(option => props.modelValue.includes(option.value));
});

// Toggle an option's selection status
function toggleOption(optionValue) {
    const newModelValue = [...props.modelValue];
    const index = newModelValue.indexOf(optionValue);

    if (index > -1) {
        newModelValue.splice(index, 1); // Remove if exists
    } else {
        newModelValue.push(optionValue); // Add if not exists
    }
    emit('update:modelValue', newModelValue);
}

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        isOpen.value = false;
    }
};

onMounted(() => document.addEventListener('mousedown', handleClickOutside));
onUnmounted(() => document.removeEventListener('mousedown', handleClickOutside));
</script>

<template>
    <div class="relative" ref="dropdownRef">
        <InputLabel :value="label" :required="required" />

        <!-- Display for selected items and dropdown trigger -->
        <div @click="!disabled && (isOpen = !isOpen)"
            :class="['mt-1 flex flex-wrap gap-2 items-center p-2 min-h-[42px] border rounded-md shadow-sm', disabled ? 'bg-gray-100 dark:bg-gray-800 cursor-not-allowed opacity-70' : 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 cursor-pointer' ]">
            <span v-if="selectedOptions.length === 0" class="text-gray-400">Pilih role...</span>
            <span v-for="selected in selectedOptions" :key="selected.value"
                class="flex items-center gap-1 px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-300">
                {{ selected.text }}
                <button v-if="!disabled" @click.stop="toggleOption(selected.value)" type="button" class="hover:text-red-500">
                    <X class="w-3 h-3" />
                </button>
            </span>
        </div>

        <!-- Dropdown with search and options -->
        <div v-show="isOpen"
            class="absolute mt-1 w-full rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-20"
            style="display: none;">
            <!-- Search Input -->
            <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <Search class="w-4 h-4 text-gray-400" />
                    </span>
                    <input type="text" v-model="searchQuery" placeholder="Cari role..."
                        class="w-full pl-9 pr-3 py-2 text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md">
                </div>
            </div>

            <!-- Options List -->
            <ul class="max-h-60 overflow-y-auto p-1">
                <li v-for="option in filteredOptions" :key="option.value" @click="toggleOption(option.value)"
                    class="flex items-center p-2 text-sm text-gray-900 dark:text-gray-100 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer">
                    <input type="checkbox" :checked="modelValue.includes(option.value)"
                        class="mr-3 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" readonly>
                    <span class="capitalize">{{ option.text }}</span>
                </li>
                <li v-if="filteredOptions.length === 0" class="p-2 text-sm text-center text-gray-500">
                    Role tidak ditemukan.
                </li>
            </ul>
        </div>
        <InputError :message="error" class="mt-2" />
    </div>
</template>