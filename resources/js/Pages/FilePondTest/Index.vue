<script setup>
import { Link, useForm } from '@inertiajs/vue3';

defineProps({
    tests: Array,
});

const form = useForm({});

const deleteTest = (id) => {
    if (confirm('Are you sure?')) {
        form.delete(route('filepond-test.destroy', id));
    }
};
</script>

<template>
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">FilePond Tests</h1>
            <Link :href="route('filepond-test.create')" class="bg-blue-500 text-white px-4 py-2 rounded">Create New</Link>
        </div>

        <div class="bg-white shadow-md rounded my-6">
            <table class="text-left w-full border-collapse">
                <thead>
                    <tr>
                        <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">Title</th>
                        <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">File</th>
                        <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="test in tests" :key="test.id" class="hover:bg-grey-lighter">
                        <td class="py-4 px-6 border-b border-grey-light">{{ test.title }}</td>
                        <td class="py-4 px-6 border-b border-grey-light">
                            <div v-if="test.file_path && test.file_path.length > 0" class="flex flex-col gap-1">
                                <a 
                                    v-for="(path, index) in test.file_path" 
                                    :key="index"
                                    :href="`/storage/${path}`" 
                                    target="_blank" 
                                    class="text-blue-500 underline text-sm"
                                >
                                    View File {{ index + 1 }}
                                </a>
                            </div>
                            <span v-else class="text-gray-400">No File</span>
                        </td>
                        <td class="py-4 px-6 border-b border-grey-light">
                            <Link :href="route('filepond-test.edit', test.id)" class="text-grey-lighter font-bold py-1 px-3 rounded text-xs bg-green hover:bg-green-dark text-blue-500 mr-2">Edit</Link>
                            <button @click="deleteTest(test.id)" class="text-grey-lighter font-bold py-1 px-3 rounded text-xs bg-blue hover:bg-blue-dark text-red-500">Delete</button>
                        </td>
                    </tr>
                    <tr v-if="tests.length === 0">
                        <td colspan="3" class="text-center py-4">No data found.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
