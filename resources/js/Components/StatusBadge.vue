<script setup>
defineProps({
    variant: {
        type: String,
        default: 'neutral',
        validator: (value) => ['primary', 'success', 'danger', 'warning', 'neutral'].includes(value)
    }
});
</script>

<template>
    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border"
        :class="{
            'bg-green-100 text-green-800 border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-800': variant === 'success',
            'bg-red-100 text-red-800 border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-800': variant === 'danger',
            'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800': variant === 'primary',
            'bg-yellow-100 text-yellow-800 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-800': variant === 'warning',
            'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600': variant === 'neutral',
        }">
        <slot></slot>
    </span>
</template>