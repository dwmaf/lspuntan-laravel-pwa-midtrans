<script setup>
import { Link } from '@inertiajs/vue3';
import { ChevronLeft } from 'lucide-vue-next';
defineProps({
    href: {
        type: String,
        default: null,
    },
    type: {
        type: String,
        default: 'button',
    },
});
const classes = "group flex items-center gap-1 p-1 sm:px-4 sm:py-2 text-sm font-medium text-gray-600 bg-white sm:border border-gray-300 rounded-full sm:rounded-lg hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 transition-all self-end sm:self-auto";
</script>

<template>
    <Link v-if="href" :href="href" :class="classes">
        <ChevronLeft class="w-5 h-5" />
        <span class="hidden sm:inline ml-1 text-sm font-medium">
            <slot>Kembali</slot>
        </span>
    </Link>
    <button v-else :type="type" :class="classes">
        <ChevronLeft class="w-5 h-5" />
        <span class="hidden sm:inline ml-1 text-sm font-medium">Kembali</span>
    </button>
</template>