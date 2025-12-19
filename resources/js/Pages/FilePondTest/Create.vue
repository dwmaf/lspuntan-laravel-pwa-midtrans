<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import vueFilePond from 'vue-filepond';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';

import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';

import DevMultiFileInput from '@/Components/Input/MultiFileInput.vue';
import DevSingleFileInput from '@/Components/Input/SingleFileInput.vue';

const FilePond = vueFilePond(
    FilePondPluginImagePreview,
    FilePondPluginFileValidateType
);

const form = useForm({
    title: '',
    files: [],
    dev_files: [],
    dev_single_file: null,
});

const submit = () => {
    form.post(route('filepond-test.store'));
};
</script>

<template>
    <div class="p-6 max-w-2xl mx-auto bg-white rounded-xl shadow-md space-y-6">
        <h1 class="text-2xl font-bold">Create FilePond Test</h1>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Title</label>
                <input v-model="form.title" type="text"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required />
                <div v-if="form.errors.title" class="text-red-500 text-sm">{{ form.errors.title }}</div>
            </div>

            <!-- Original FilePond -->
            <div class="p-4 border rounded-lg bg-gray-50">
                <h2 class="text-lg font-semibold mb-2">Original FilePond</h2>
                <label class="block text-sm font-medium text-gray-700">File</label>
                <FilePond name="file" ref="pond"
                    label-idle="Drop files here or <span class='filepond--label-action'>Browse</span>"
                    allow-multiple="true" max-files="5" @updatefiles="(fileItems) => {
                        form.files = fileItems.map(item => item.file);
                    }" />
                <div v-if="form.errors.files" class="text-red-500 text-sm">{{ form.errors.files }}</div>
            </div>

            <!-- Custom Dev Components Comparison -->
            <div class="p-4 border rounded-lg bg-gray-50 space-y-4">
                <h2 class="text-lg font-semibold mb-2">Custom Dev UI (FilePond Lookalike)</h2>

                <!-- Multi File Input -->
                <div>
                    <DevMultiFileInput v-model="form.dev_files" label="Dev Multi File Input" :max-files="5"
                        accept="image/*,application/pdf" />
                </div>

                <!-- Single File Input -->
                <div>
                    <DevSingleFileInput v-model="form.dev_single_file" label="Dev Single File Input"
                        accept="image/*,application/pdf" />
                </div>
            </div>

            <div class="flex justify-between pt-4">
                <Link :href="route('filepond-test.index')" class="text-gray-500 hover:text-gray-700">Cancel</Link>
                <button type="submit" :disabled="form.processing"
                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Save
                </button>
            </div>
        </form>
    </div>
</template>
