<script setup>
import InputLabel from '@/Components/Input/InputLabel.vue';
import InputError from '@/Components/Input/InputError.vue';
defineProps({
    modelValue: String,
    type: {
        type: String,
        default: 'datetime-local',
        validator: (value) => ['datetime-local', 'date'].includes(value),
    },
    required: {
        type: Boolean,
        default: false
    },
    label: String,
    error: String,
    id: String,
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div>
        <InputLabel v-if="label" :for="id" :value="label" :required="required"/>
        <input
            :type="type"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :required="required"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
        />
        <InputError v-if="error" :message="error"/>
    </div>
</template>