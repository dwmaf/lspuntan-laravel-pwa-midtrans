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
    <Link :href="href" :class="classes" :method="method">
        <FontAwesomeIcon v-if="icon" :icon="icon" class="text-lg text-gray-700 dark:text-gray-200" />
        <span v-if="isOpen" class="text-gray-700 dark:text-gray-200 rounded-sm">
            <slot />
        </span>
    </Link>
</template>
