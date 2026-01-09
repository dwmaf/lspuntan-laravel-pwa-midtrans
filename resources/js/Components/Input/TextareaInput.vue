<script setup>
import { onMounted, ref } from 'vue';
import InputLabel from '@/Components/Input/InputLabel.vue';
import InputError from '@/Components/Input/InputError.vue';

defineOptions({
    inheritAttrs: false,
});

const props = defineProps({
    label: String,
    error: String,
    id: String,
    required: Boolean,
});

const model = defineModel({
    type: String,
    required: false,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <div>
        <InputLabel v-if="label" :for="id" :value="label" :required="required" />
        <textarea
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
            v-model="model" ref="input" :id="id" :required="required" v-bind="$attrs"></textarea>
        <InputError v-if="error" :message="error" />
    </div>
</template>
