<script setup>
import { computed } from 'vue';
import { Award, FileText, Download, CheckCircle, Edit } from 'lucide-vue-next';
import SecondaryButton from '@/Components/Button/SecondaryButton.vue';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    href: {
        type: String,
        default: null,
    },
    icon: {
        type: String,
        default: 'file', // 'award' atau 'file'
        validator: (value) => ['award', 'file'].includes(value),
    },
    status: {
        type: String,
        default: null,
    },
    editable: { type: Boolean, default: false }
});
const emit = defineEmits(['edit']);
</script>

<template>
    <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 transition-all">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <div
                class="p-3 bg-white dark:bg-gray-700 rounded-lg shadow-sm border border-gray-100 dark:border-gray-600 shrink-0">
                <Award v-if="icon === 'award'" class="w-8 h-8 text-blue-600 dark:text-blue-400" />
                <FileText v-else class="w-8 h-8 text-indigo-600 dark:text-indigo-400" />
            </div>
            <div class="flex-1 min-w-0 w-full">
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 break-all mb-1">
                    {{ title.split('/').pop() }}
                </p>

                <div v-if="status" class="flex items-center flex-wrap gap-2 text-xs text-gray-500">
                    <span v-if="status"
                        class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                        <CheckCircle class="w-3.5 h-3.5" />
                        {{ status }}
                    </span>
                </div>
            </div>

            <hr class="w-full border-gray-200 dark:border-gray-700 sm:hidden transition-colors" />

            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <a v-if="href" :href="href" target="_blank"
                    class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors w-auto">
                    <Download class="w-4 h-4 mr-2" />
                    <span>Lihat</span>
                </a>
                <SecondaryButton v-if="editable" @click="emit('edit')" class="normal-case! tracking-normal!">
                    <Edit class="w-4 h-4 mr-2" />
                    <span>Edit</span>
                </SecondaryButton>
            </div>

        </div>
    </div>
</template>