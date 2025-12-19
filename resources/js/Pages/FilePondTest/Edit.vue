<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import vueFilePond from 'vue-filepond';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import { computed, ref } from 'vue';

import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';

import DevMultiFileInput from '@/Components/Input/MultiFileInput.vue';
import DevSingleFileInput from '@/Components/Input/SingleFileInput.vue';

const FilePond = vueFilePond(
    FilePondPluginImagePreview,
    FilePondPluginFileValidateType
);

const props = defineProps({
    test: Object,
});

const pond = ref(null);

// Transform existing files for DevMultiFileInput
const devExistingFiles = computed(() => {
    return (props.test.file_path || []).map((path, index) => ({
        id: index,
        path_file: path,
        name: path.split('/').pop()
    }));
});

const form = useForm({
    _method: 'PUT',
    title: props.test.title,
    files: [],

    // Dev Input States
    dev_files: [], // New files to upload
    dev_delete_files: [], // ID of files to delete
    dev_single_file: null, // For single file test
    dev_single_delete: false, // Flag to delete single file
});

const existingFiles = computed(() => {
    return (props.test.file_path || []).map(path => ({
        source: path,
        options: {
            type: 'local',
        },
    }));
});


const pondServer = {
    process: {
        url: '/filepond/process',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
    },
    revert: (uniqueFileId, load, error) => {
        fetch('/filepond/revert', {
            method: 'DELETE',
            body: uniqueFileId,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).then(res => {
            if (res.ok) {
                load();
            } else {
                error('Could not revert file');
            }
        });
    },
    load: (source, load, error, progress, abort, headers) => {
        fetch(`/filepond/load?load=${source}`)
            .then(res => {
                if (!res.ok) {
                    error('Could not load file');
                    return;
                }
                return res.blob();
            })
            .then(load);
    },
};

const submit = () => {
    form.files = pond.value.getFiles().map(file => {
        return file.serverId ? file.serverId : file.source;
    });
    form.post(route('filepond-test.update', props.test.id));
};
</script>

<template>
    <div class="p-6 max-w-2xl mx-auto bg-white rounded-xl shadow-md space-y-6">
        <h1 class="text-2xl font-bold">Edit FilePond Test</h1>

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
                <label class="block text-sm font-medium text-gray-700">Files</label>
                <FilePond name="files[]" ref="pond"
                    label-idle="Drop new files here or <span class='filepond--label-action'>Browse</span>"
                    allow-multiple="true" max-files="5" :files="existingFiles" :server="pondServer" />

            </div>

            <!-- Custom Dev Components Comparison -->
            <div class="p-4 border rounded-lg bg-gray-50 space-y-4">
                <h2 class="text-lg font-semibold mb-2">Custom Dev UI (FilePond Lookalike)</h2>

                <!-- Multi File Input -->
                <div>
                    <DevMultiFileInput v-model="form.dev_files" v-model:delete-list="form.dev_delete_files"
                        :existing-files="devExistingFiles" label="Dev Multi File Input" :max-files="5"
                        accept="image/*,application/pdf" />
                    <div class="mt-1 text-xs text-gray-500">
                        Delete List: {{ form.dev_delete_files }}
                    </div>
                </div>

                <!-- Single File Input (Mockup for Edit) -->
                <div>
                    <!-- Assuming single file edit just replaces/shows existing -->
                    <DevSingleFileInput v-model="form.dev_single_file"
                        :existing-file-url="devExistingFiles.length > 0 ? devExistingFiles[0].path_file : null"
                        label="Dev Single File Input" accept="image/*" />
                </div>
            </div>


            <div class="flex justify-between pt-4">
                <Link :href="route('filepond-test.index')" class="text-gray-500 hover:text-gray-700">Cancel</Link>
                <button type="submit" :disabled="form.processing"
                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Update
                </button>
            </div>
        </form>
    </div>
</template>

<style>
.filepond--credits {
    display: none;
}
</style>
