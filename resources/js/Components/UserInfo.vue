<script setup>
import { IconUserCircle } from '@tabler/icons-vue';

const props = defineProps({
    namaUser: {
        type: String,
    },
    userRole: {
        type: String,

    },
});

</script>

<template>
    <div class="ml-4 hidden sm:flex items-center gap-1">
        <div class="flex items-center shrink-0">
            <IconUserCircle stroke-width="1.5" class="w-9 h-9 text-gray-500" />
        </div>
        <div class="flex flex-col">
            <span class="text-sm font-medium text-gray-900 dark:text-gray-200 leading-tight uppercase">
                {{ namaUser }}
            </span>
            <span class="text-xs text-blue-600 dark:text-blue-400 font-bold tracking-widest mt-0.5 capitalize">
                {{ userRole }}
            </span>
        </div>
    </div>
</template>
