<script setup>
import { computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: Boolean,
        required: true,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue']);


const toggle = () => {
    if (props.disabled) return;
    emit('update:modelValue', !props.modelValue);
};

const backgroundClasses = computed(() => {
    return props.modelValue
        ? 'bg-indigo-600 dark:bg-indigo-500' 
        : 'bg-gray-200 dark:bg-gray-600';     
});

const dotClasses = computed(() => {
    return props.modelValue
        ? 'translate-x-5' 
        : 'translate-x-0';
});
</script>

<template>
    <button type="button" @click="toggle" :disabled="disabled"
        class="relative inline-flex h-6 w-11 shrink-0 rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
        :class="[backgroundClasses, disabled ? 'cursor-not-allowed opacity-50' : 'cursor-pointer']" role="switch"
        :aria-checked="modelValue">
        <span aria-hidden="true"
            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
            :class="dotClasses"></span>
    </button>
</template>