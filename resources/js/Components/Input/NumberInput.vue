<script setup>
import { onMounted, ref } from 'vue';
import InputLabel from '@/Components/Input/InputLabel.vue';
import InputError from '@/Components/Input/InputError.vue';

defineOptions({
    inheritAttrs: false,
});

defineProps({
    modelValue: [String, Number],
    required: {
        type: Boolean,
        default: false,
    },
    label: String,
    error: String,
    id: String,
    formattedValue: [String, Number],
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <div>
        <InputLabel v-if="label" :for="id" :value="label" :required="required" />
        
        <input :id="id" type="number"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 placeholder:text-sm"
            @input="$emit('update:modelValue', $event.target.value)" :value="modelValue" :required="required"
            v-bind="$attrs" ref="input" />
        <p v-if="formattedValue" class="text-sm font-medium text-gray-800 dark:text-gray-400">
            {{ formattedValue }}
        </p>
        <InputError v-if="error" :message="error" />
    </div>
</template>
