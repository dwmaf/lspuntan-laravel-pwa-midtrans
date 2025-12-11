<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    icon: {
        type: [Object, Function],
        required: true,
    },
    isOpen: {
        type: Boolean,
        default: true,
    },
    label: {
        type: String,
        required: true,
    },
    badgeText: {
        type: String,
        default: null,
    }
});
</script>

<template>
    <a 
        :href="href" 
        target="_blank" 
        rel="noopener noreferrer"
        class="w-full flex items-center gap-2 leading-none mb-2 px-3 py-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-sm relative group"
    >
        <!-- Ikon -->
        <component 
            :is="icon" 
            class="text-gray-700 dark:text-gray-200"
            :size="20"
            stroke-width="2"
        />
        <!-- Konten (Teks dan Badge) -->
        <div v-if="isOpen" class="text-gray-700 dark:text-gray-200 rounded-sm font-semibold text-sm w-full">
            <div class="flex items-center justify-between w-full">
                <span>{{ label }}</span>
                <span v-if="badgeText"
                    class="ml-2 px-1.5 py-0.5 text-xs font-semibold text-yellow-900 bg-yellow-400 rounded-full">
                    {{ badgeText }}
                </span>
            </div>
        </div>
        <!-- Tooltip (jika sidebar ditutup) -->
        <div id="tooltip"
            v-if="!isOpen"
            class="absolute left-full ml-4 px-2 py-1 rounded bg-gray-200 dark:bg-gray-700 dark:text-white text-gray-700 text-sm border dark:border-gray-200 border-gray-700
                invisible opacity-0 -translate-x-3 transition-all 
                md:group-hover:visible md:group-hover:opacity-100 md:group-hover:translate-x-0 
                z-50 whitespace-nowrap"
        >
            {{ label }}
        </div>
    </a>
</template>