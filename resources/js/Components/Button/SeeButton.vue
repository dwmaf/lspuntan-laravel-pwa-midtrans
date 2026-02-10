<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    href: {
        type: String,
        default: null,
    },
    type: {
        type: String,
        default: 'button',
    },
    target: {
        type: String,
        default: '_self',
    }
});

const classes = "space-x-1 inline-flex items-center px-2 py-1 text-sm font-medium text-white bg-blue-500 hover:bg-blue-600 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-700 transition ease-in-out duration-150 cursor-pointer border border-transparent";
</script>

<template>
    <Link 
        v-if="href" 
        :href="href" 
        :class="classes"
    >
        <slot></slot>
    </Link>
    <button 
        v-else 
        :type="type" 
        :class="classes"
    >
        <slot></slot>
    </button>
</template>