<script setup>
import { computed } from 'vue';

const props = defineProps({
    name: {
        type: String,
        default: 'Admin',
    },
    createdAt: [String, Date],
    updatedAt: [String, Date],
});

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    const now = new Date();

    const isToday = date.getDate() === now.getDate() &&
        date.getMonth() === now.getMonth() &&
        date.getFullYear() === now.getFullYear();

    const isSameYear = date.getFullYear() === now.getFullYear();

    if (isToday) {
        return new Intl.DateTimeFormat('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        }).format(date);
    }

    if (isSameYear) {
        return new Intl.DateTimeFormat('id-ID', {
            day: 'numeric',
            month: 'long'
        }).format(date);
    }

    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    }).format(date);
};

const isEdited = computed(() => {
    return props.createdAt && props.updatedAt && props.createdAt !== props.updatedAt;
});
</script>

<template>
    <div class="flex items-center gap-3">
        <div class="shrink-0">
            <svg class="h-10 w-10 text-gray-400 dark:text-gray-600 rounded-full bg-gray-200 dark:bg-gray-700 p-1"
                fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        </div>
        <div class="min-w-0">
            <h5 class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">
                {{ name }}
            </h5>
            <div class="text-xs text-gray-400">
                {{ formatDate(createdAt) }}
                <span v-if="isEdited" class="text-gray-500">(Diedit)</span>
            </div>
        </div>
    </div>
</template>
