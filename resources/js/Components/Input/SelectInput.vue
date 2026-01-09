<script setup>
import { ref } from 'vue';
import InputLabel from '@/Components/Input/InputLabel.vue';
import InputError from '@/Components/Input/InputError.vue';

defineOptions({
    inheritAttrs: false,
});

const props = defineProps({
    modelValue: [String, Number],
    options: {
        type: Array,
        required: true, // [{ value: '1', text: 'Option 1' }]
    },
    label: String,
    error: String,
    id: String,
    placeholder: {
        type: String,
        default: 'Pilih salah satu',
    },
    required: Boolean,
});

const emit = defineEmits(['update:modelValue']);

const updateValue = (event) => {
    emit('update:modelValue', event.target.value);
};
</script>

<template>
    <div>
        <InputLabel v-if="label" :for="id" :value="label" :required="required" />
        <select :id="id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
            :value="modelValue" @change="updateValue" v-bind="$attrs" :required="required">
            <option value="" disabled selected class="text-gray-400 dark:text-gray-500 text-sm">{{ placeholder }}
            </option>
            <option v-for="option in options" :key="option.value" :value="option.value">
                {{ option.text }}
            </option>
        </select>
        <InputError v-if="error" :message="error" />
    </div>
</template>