<script setup>
import { computed } from 'vue';
import { Image } from 'lucide-vue-next';

const props = defineProps({
    path: {
        type: String,
        required: true,
    }
});

const iconDetails = computed(() => {
    const extension = props.path.split('.').pop().toLowerCase();

    if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].includes(extension)) {
        return { type: 'lucide', icon:'image', color: 'bg-red-500', text: '' };
    }
    if (['doc', 'docx'].includes(extension)) {
        return { type: 'text', color: 'bg-blue-500', text: 'W' };
    }
    if (['xls', 'xlsx'].includes(extension)) {
        return { type: 'text', color: 'bg-green-500', text: 'X' };
    }
    if (['ppt', 'pptx'].includes(extension)) {
        return { type: 'text', color: 'bg-yellow-500', text: 'P' };
    }
    if (extension === 'pdf') {
        return { type: 'text', color: 'bg-red-600', text: 'PDF', class:'text-[10px]' };
    }
    // Default icon
    return { type: 'svg', path: 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z', color: 'bg-gray-500', text: '' };
});
</script>

<template>
    <div :class="[iconDetails.color, 'w-5 h-5 rounded-sm shrink-0 flex items-center justify-center']">
        <Image v-if="iconDetails.type === 'lucide'" class="h-4 w-4 text-white" stroke-width="2"/>
        <!-- <FontAwesomeIcon v-if="iconDetails.type === 'lucide'" :icon="iconDetails.icon" class="h-4 w-4 text-white" /> -->
        <svg v-else-if="iconDetails.type === 'svg'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" :d="iconDetails.path" />
        </svg>
        <span v-else :class="['text-white font-bold leading-none', iconDetails.class || 'text-xs']">
            {{ iconDetails.text }}
        </span>
    </div>
</template>