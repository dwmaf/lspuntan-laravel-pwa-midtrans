<script setup>
import { computed } from 'vue';
import { AlertCircle, CheckCircle, Info, XCircle } from 'lucide-vue-next';

const props = defineProps({
    type: {
        type: String,
        default: 'info', // info, success, warning, error
    },
    title: {
        type: String,
        default: '',
    },
});

const config = {
    info: {
        container: 'bg-blue-50 border-blue-400 dark:bg-blue-900/20 dark:border-blue-600 text-blue-700 dark:text-blue-300',
        icon: Info,
        iconClass: 'text-blue-600 dark:text-blue-400',
    },
    success: {
        container: 'bg-green-50 border-green-400 dark:bg-green-900/20 dark:border-green-600 text-green-700 dark:text-green-300',
        icon: CheckCircle,
        iconClass: 'text-green-600 dark:text-green-400',
    },
    warning: {
        container: 'bg-yellow-50 border-yellow-400 dark:bg-yellow-900/20 dark:border-yellow-600 text-yellow-700 dark:text-yellow-300',
        icon: AlertCircle,
        iconClass: 'text-yellow-600 dark:text-yellow-400',
    },
    error: {
        container: 'bg-red-50 border-red-400 dark:bg-red-900/20 dark:border-red-600 text-red-700 dark:text-red-300',
        icon: XCircle,
        iconClass: 'text-red-600 dark:text-red-400',
    },
};

const currentConfig = computed(() => config[props.type] || config.info);
</script>

<template>
    <div :class="['p-4 border-l-4 rounded-r-lg mb-4', currentConfig.container]" role="alert">
        <div class="flex items-start gap-3">
            <component :is="currentConfig.icon" :class="['w-5 h-5 shrink-0 mt-0.5', currentConfig.iconClass]" />
            <div class="text-sm">
                <span v-if="title" class="font-bold block mb-1">{{ title }}</span>
                <slot></slot>
            </div>
        </div>
    </div>
</template>
