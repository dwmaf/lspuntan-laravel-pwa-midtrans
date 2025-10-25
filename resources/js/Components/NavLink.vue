<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
    icon: {
        type: String,
        default: "",
    },
    isOpen: {
        type: Boolean,
        default: true,
    },
    method: {
        type: String,
        default: 'get',
    }
});

const classes = computed(() =>
    props.active
        ? "w-full flex items-center gap-2 leading-none mb-2 px-3 py-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-sm relative bg-gray-200 dark:bg-gray-700"
        : "w-full flex items-center gap-2 leading-none mb-2 px-3 py-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-sm relative"
);
</script>

<template>
    <Link :href="href" :class="classes" :method="method" class="group">
        <FontAwesomeIcon v-if="icon" :icon="icon" class="text-lg text-gray-700 dark:text-gray-200" />
        <span v-if="isOpen" class="text-gray-700 dark:text-gray-200 rounded-sm">
            <slot />
        </span>
        <div
            v-if="!isOpen"
            class="absolute left-full ml-4 px-2 py-1 rounded bg-gray-200 dark:bg-gray-700 text-white text-sm border dark:border-gray-200 border-gray-700
                   invisible opacity-0 -translate-x-3 transition-all 
                   group-hover:visible group-hover:opacity-100 group-hover:translate-x-0 
                   z-50 whitespace-nowrap"
        >
            <slot />
        </div>
    </Link>
</template>
