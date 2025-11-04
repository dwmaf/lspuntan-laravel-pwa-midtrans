<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { MoveRight } from 'lucide-vue-next';
const props = defineProps({
    logs: Object,
});
const viewMode = ref('list'); // 'list' atau 'detail'
const selectedLog = ref(null); // Untuk menyimpan data log yang dipilih

// --- 2. BUAT FUNGSI UNTUK BERPINDAH TAMPILAN ---
const showDetailView = (log) => {
    selectedLog.value = log;
    viewMode.value = 'detail';
};

const backToList = () => {
    selectedLog.value = null;
    viewMode.value = 'list';
};
const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleString('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short',
    });
};
const cleanSubjectType = (subject) => {
    if (!subject) return 'N/A';
    const parts = subject.split('\\');
    return parts.pop();
};
const formatFieldName = (fieldName) => {
    const result = fieldName.replace(/_/g, ' ').replace(/([A-Z])/g, ' $1');
    return result.charAt(0).toUpperCase() + result.slice(1);
};
</script>

<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Catatan Aktivitas Sistem
            </h2>
        </template>

        <div v-if="viewMode === 'list'" class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                No</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Deskripsi</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Oleh
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Waktu
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="(log, index) in logs.data" :key="log.id">
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{ logs.from + index }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{ log.description }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">
                                {{ log.causer?.name ?? 'Sistem' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                {{ formatDate(log.created_at) }}
                            </td>
                            <td class="px-6 py-4">
                                <button @click="showDetailView(log)"
                                    class="cursor-pointer px-2 py-1 text-sm font-medium text-white bg-indigo-500 hover:bg-indigo-600 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-600 dark:hover:bg-indigo-700 dark:focus:ring-indigo-700">
                                    Lihat
                                </button>
                            </td>
                        </tr>
                        <tr v-if="logs.data.length === 0">
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">Tidak ada aktivitas.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- <div v-if="logs.links.length > 3" class="mt-6 flex justify-between items-center">
                <div class="flex flex-wrap -mb-1">
                    <template v-for="(link, key) in logs.links" :key="key">
                        <div v-if="link.url === null" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded" v-html="link.label" />
                        <Link v-else class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-white focus:border-indigo-500 focus:text-indigo-500" :class="{ 'bg-white': link.active }" :href="link.url" v-html="link.label" />
                    </template>
                </div>
            </div> -->
        </div>
        <div v-else-if="viewMode === 'detail' && selectedLog"
            class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="flex justify-between items-center mb-6 border-b pb-4 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Detail Aktivitas</h3>
                <SecondaryButton @click="backToList">
                    Kembali
                </SecondaryButton>
            </div>

            <dl class="grid grid-cols-1 md:grid-cols-3 gap-x-4 gap-y-8">
                <div class="sm:col-span-3">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Deskripsi</dt>
                    <dd class="mt-1 text-base text-gray-900 dark:text-gray-200">{{ selectedLog.description }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Dilakukan Oleh</dt>
                    <dd class="mt-1 text-base text-gray-900 dark:text-gray-200">{{ selectedLog.causer?.name ?? 'Sistem'
                        }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Waktu</dt>
                    <dd class="mt-1 text-base text-gray-900 dark:text-gray-200">{{ formatDate(selectedLog.created_at) }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Jenis Aksi</dt>
                    <dd class="mt-1 text-base text-gray-900 dark:text-gray-200 capitalize">{{ selectedLog.event }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Target Data</dt>
                    <dd class="mt-1 text-base text-gray-900 dark:text-gray-200">{{
                        cleanSubjectType(selectedLog.subject_type) }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ID Target</dt>
                    <dd class="mt-1 text-base text-gray-900 dark:text-gray-200">{{ selectedLog.subject_id }}</dd>
                </div>
                
                <div class="sm:col-span-3" v-if="selectedLog.properties && selectedLog.properties.attributes">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Detail Perubahan</dt>
                    <dd class="mt-2 text-sm">
                        <div class="overflow-x-auto border rounded-md dark:border-gray-700">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800/50">
                                    <tr>
                                        <th
                                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-1/4">
                                            Kolom</th>
                                        <th
                                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                            Perubahan</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="(newValue, field) in selectedLog.properties.attributes" :key="field">
                                        <td class="px-4 py-3 font-semibold text-gray-800 dark:text-gray-200">
                                            {{ field }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300 flex items-center gap-3">
                                            <span v-if="selectedLog.properties.old && selectedLog.properties.old[field]"
                                                class="">
                                                {{ selectedLog.properties.old[field] }}
                                            </span>
                                            <MoveRight v-if="selectedLog.properties.old && selectedLog.properties.old[field]" stroke-width="1"/>
                                            <span>
                                                {{ newValue }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </dd>
                </div>
            </dl>
        </div>
    </AdminLayout>
</template>