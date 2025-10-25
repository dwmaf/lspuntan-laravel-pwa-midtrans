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
    <Link :href="href" class="flex gap-2 items-center px-2 py-1 text-sm font-medium text-white bg-blue-500 hover:bg-blue-600 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-700 transition ease-in-out duration-150 cursor-pointer">
        <slot />
    </Link>
</template>