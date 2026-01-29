<script setup>
import {  FileSpreadsheet } from 'lucide-vue-next';
const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    target: {
        type: String,
        required: false,
    }
    
});
</script>
<template>
    <a :href="href" :target="target" class="space-x-1 inline-flex items-center px-2 py-1 text-sm font-medium text-white bg-green-500 hover:bg-green-600 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-700 transition ease-in-out duration-150 cursor-pointer">
        <FileSpreadsheet class="w-4 h-4 mr-1 my-0" />
        <slot></slot>
    </a>
</template>