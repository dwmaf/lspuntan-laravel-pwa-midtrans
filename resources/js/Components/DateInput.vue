<script setup>
defineProps({
    /**
     * Nilai input, untuk v-model.
     */
    modelValue: String,
    /**
     * Tipe input, bisa 'datetime-local' atau 'date'.
     */
    type: {
        type: String,
        default: 'datetime-local',
        validator: (value) => ['datetime-local', 'date'].includes(value),
    },
    required: {
        type: Boolean,
        default: false
    },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <input
        :type="type"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :required="required"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
    />
</template>