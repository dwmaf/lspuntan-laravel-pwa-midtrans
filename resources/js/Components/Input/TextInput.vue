<script setup>
import { onMounted, ref, computed } from 'vue';
import InputLabel from '@/Components/Input/InputLabel.vue';
import InputError from '@/Components/Input/InputError.vue';
import { Eye, EyeOff } from 'lucide-vue-next';

defineOptions({
    inheritAttrs: false,
});

const props = defineProps({
    label: String,
    error: String,
    id: String,
    required: Boolean,
    type: { type: String, default: 'text' },
});

const model = defineModel({
    type: String,
    required: false,
});

const input = ref(null);
const showPassword = ref(false);

const inputType = computed(() => {
    if (props.type === 'password' && showPassword.value) {
        return 'text';
    }
    return props.type;
});

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <div>
        <InputLabel v-if="label" :for="id" :value="label" :required="required"/>
        <div class="relative mt-1">
            <input
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 placeholder:text-sm"
                :class="{'pr-10': type === 'password'}" 
                v-model="model"
                ref="input"
                :id="id"
                :type="inputType"
                :required="required"
                v-bind="$attrs"
            />
            <button 
                v-if="type === 'password'"
                type="button" 
                @click="showPassword = !showPassword"
                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 cursor-pointer focus:outline-none"
                tabindex="-1"
            >
                <Eye v-if="showPassword" class="w-5 h-5" />
                <EyeOff v-else class="w-5 h-5" />
            </button>
        </div>
        <InputError v-if="error" :message="error"/>
    </div>
</template>
