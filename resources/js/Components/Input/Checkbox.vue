<script setup>
import { computed } from 'vue';
import InputError from '@/Components/Input/InputError.vue';
import InputLabel from '@/Components/Input/InputLabel.vue';

const emit = defineEmits(['update:checked']);

const props = defineProps({
    checked: {
        type: [Array, Boolean],
        required: true,
    },
    value: {
        default: null,
    },
    id: { type: String, required: true },
    label: { type: String, default: null },
    description: { type: String, default: null },
    error: { type: String, default: null },
});

const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit('update:checked', val);
    },
});
</script>

<template>
    <div class="flex items-start gap-3">
        <input :id="id" type="checkbox" :value="value" v-model="proxyChecked"
            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" />
        <div v-if="label || description || error" class="text-sm">
            <InputLabel v-if="label" :for="id" :value="label" class="mb-0! cursor-pointer select-none" />
            <p v-if="description" class="text-gray-500 dark:text-gray-400 mt-0.5 leading-relaxed">
                {{ description }}
            </p>

            <InputError v-if="error" :message="error" class="mt-1" />
        </div>
    </div>
</template>
