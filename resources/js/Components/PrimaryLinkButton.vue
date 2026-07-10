<script setup>
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    extraClass: {
        type: String,
        default: 'bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700',
    },
});
</script>

<template>
    <Link :href="href" :class="['cursor-pointer px-4 py-2 text-sm font-medium text-white rounded-md active:ring-2 active:ring-offset-2 active:ring-blue-500 dark:active:ring-blue-700', props.extraClass]">
        <slot></slot>
    </Link>
</template>
