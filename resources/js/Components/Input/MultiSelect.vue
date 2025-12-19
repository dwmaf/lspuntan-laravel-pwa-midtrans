<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import InputLabel from './Input/InputLabel.vue';
import InputError from './Input/InputError.vue';
import { X, Search } from 'lucide-vue-next';

const props = defineProps({
    modelValue: {
        type: Array,
        required: true,
    },
    options: {
        type: Array,
        required: true,
    },
    placeholder: {
        type: String,
        required: true,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    required: Boolean,
    labelProp: {
        type: String,
        default: 'text'
    },
    valueProp: {
        type: String,
        default: 'value'
    }
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const searchQuery = ref('');
const dropdownRef = ref(null);

const filteredOptions = computed(() => {
    if (!searchQuery.value) {
        return props.options;
    }
    return props.options.filter(option =>
        String(option[props.labelProp]).toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

const selectedOptions = computed(() => {
    return props.options.filter(option => props.modelValue.includes(option[props.valueProp]));
});


function toggleOption(optionValue) {
    const newModelValue = [...props.modelValue];
    const index = newModelValue.indexOf(optionValue);

    if (index > -1) {
        newModelValue.splice(index, 1);
    } else {
        newModelValue.push(optionValue);
    }
    emit('update:modelValue', newModelValue);
}


const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        isOpen.value = false;
    }
};

onMounted(() => document.addEventListener('mousedown', handleClickOutside));
onUnmounted(() => document.removeEventListener('mousedown', handleClickOutside));
</script>

<template>
    <div ref="dropdownRef" class="p-0 bg-transparent border-0">
        <div @click="!disabled && (isOpen = !isOpen)"
            :class="[' flex flex-wrap gap-2 items-center p-2 min-h-[42px] border rounded-md shadow-sm', disabled ? 'border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 cursor-not-allowed opacity-70' : 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 cursor-pointer']">
            <span v-if="selectedOptions.length === 0" class="text-gray-400">{{ placeholder }}</span>
            <span v-for="selected in selectedOptions" :key="selected[valueProp]"
                class="flex items-center gap-1 px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-300">
                {{ selected[labelProp] }}
                <button v-if="!disabled" @click.stop="toggleOption(selected[valueProp])" type="button"
                    class="hover:text-red-500">
                    <X class="w-3 h-3" />
                </button>
            </span>
        </div>

        <div v-show="isOpen"
            class="absolute mt-1 w-full rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-20"
            style="display: none;">
            <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <Search class="w-4 h-4 text-gray-400" />
                    </span>
                    <input type="text" v-model="searchQuery"
                        class="w-full pl-9 pr-3 py-2 text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md">
                </div>
            </div>


            <ul class="max-h-60 overflow-y-auto p-1">
                <li v-for="option in filteredOptions" :key="option[valueProp]" @click="toggleOption(option[valueProp])"
                    class="flex items-center p-2 text-sm text-gray-900 dark:text-gray-100 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer">
                    <input type="checkbox" :checked="modelValue.includes(option[valueProp])"
                        class="mr-3 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" readonly>
                    <span class="capitalize">{{ option[labelProp] }}</span>
                </li>
                <li v-if="filteredOptions.length === 0" class="p-2 text-sm text-center text-gray-500">
                    Tidak ditemukan.
                </li>
            </ul>
        </div>
    </div>

</template>