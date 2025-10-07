<script setup>
import { Link } from "@inertiajs/vue3";
const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    
});
</script>
<template>
    <Link :href="href" class="cursor-pointer px-2 py-1 text-sm font-medium text-white bg-blue-500 hover:bg-blue-600 rounded-md  active:ring-2 active:ring-offset-2 active:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:active:ring-blue-700">
        <slot />
    </Link>
</template>