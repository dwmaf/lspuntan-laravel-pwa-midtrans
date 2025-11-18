<script setup>
defineProps({
    judul: { type: String, required: true, },
});
</script>
<template>
    <div class="flex flex-col sm:flex-row sm:justify-between gap-2 items-start sm:items-center my-6">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">{{ judul }}</h2>
        <slot></slot>
    </div>
</template>