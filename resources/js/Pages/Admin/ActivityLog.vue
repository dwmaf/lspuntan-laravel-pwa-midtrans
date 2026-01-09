<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SecondaryButton from '@/Components/Button/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, reactive, computed, watch } from 'vue';
import { MoveRight, FunnelIcon, X } from 'lucide-vue-next';
import TextInput from '@/Components/Input/TextInput.vue';
import CustomHeader from '@/Components/CustomHeader.vue';
import SelectInput from '@/Components/Input/SelectInput.vue';
import Dropdown from '@/Components/Dropdown.vue';
import InputLabel from '@/Components/Input/InputLabel.vue';
import PrimaryButton from '@/Components/Button/PrimaryButton.vue';
const props = defineProps({
    logs: Object,
    filters: Object,
    filterOptions: Object,
});

const filtersForm = reactive({
    search: props.filters.search || '',
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
    subject_type: props.filters.subject_type || '',
    event: props.filters.event || '',
});
const hasActiveFilters = computed(() => {
    const { search, ...advancedFilters } = filtersForm;
    return Object.values(advancedFilters).some(value => value !== '' && value !== null);
});
const showFilterModal = ref(false);
const openFilterModal = () => {
    showFilterModal.value = true;
};
const closeFilterModal = () => {
    showFilterModal.value = false;
};
let searchTimeoutId = null;
watch(() => filtersForm.search, (newValue) => {
    clearTimeout(searchTimeoutId);
    searchTimeoutId = setTimeout(() => {
        router.get(route('admin.activity-logs.index'), filtersForm, {
            preserveState: true,
            replace: true,
        });
    }, 500);
});
const applyFilters = () => {
    router.get(route('admin.activity-logs.index'), filtersForm, {
        preserveState: true,
        replace: true,
    });
    closeFilterModal();
};
const resetFilters = () => {
    Object.keys(filtersForm).forEach(key => filtersForm[key] = '');
    applyFilters();
};
const subjectOptions = computed(() => {
    return [
        { value: '', text: 'Semua Target' },
        ...props.filterOptions.subjects.map(s => ({ value: s, text: cleanSubjectType(s) }))
    ];
});
const eventOptions = [
    { value: '', text: 'Semua Aksi' },
    { value: 'created', text: 'Created' },
    { value: 'updated', text: 'Updated' },
    { value: 'deleted', text: 'Deleted' },
];
const viewMode = ref('list');
const selectedLog = ref(null);

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
        <CustomHeader judul="Catatan Aktivitas Sistem" />

        <div v-if="viewMode === 'list'" class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="flex justify-end items-center gap-2 mb-4">
                <div class="w-[243px]">
                    <TextInput v-model="filtersForm.search" type="text" placeholder="Cari causer..." />
                </div>
                <button data-cy="filter-trigger-button" @click="openFilterModal"
                    class="relative mt-1 inline-flex items-center px-3 py-3 border border-gray-300 dark:border-gray-500 text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-300 bg-white dark:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none transition ease-in-out duration-150">
                    <FunnelIcon class="w-4 h-4" />
                    <span v-if="hasActiveFilters"
                        class="absolute -top-1 -right-1 h-2 w-2 bg-blue-500 rounded-full"></span>
                </button>
            </div>
            <div class="overflow-x-auto custom-scrollbar">
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
            <div class="mt-4 flex justify-between items-center">
                <span v-if="logs.total > 0" class="text-sm text-gray-700 dark:text-gray-400 hidden lg:flex">
                    Menampilkan {{ logs.from }} sampai {{ logs.to }} dari {{ logs.total }} hasil
                </span>
                <span v-else></span>
                <Pagination :links="logs.links" />
            </div>

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
                                            <MoveRight
                                                v-if="selectedLog.properties.old && selectedLog.properties.old[field]"
                                                stroke-width="1" />
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
    <Modal :show="showFilterModal" @close="showFilterModal = false">
        <div class="flex justify-end p-2">
            <button @click="closeFilterModal">
                <X class="w-4 dark:text-white" />
            </button>
        </div>
        <div class="p-6 flex flex-col gap-4">

            <div>
                <InputLabel value="Rentang Waktu" />
                <div class="flex flex-col">
                    <TextInput id="date_from" label="Dari" v-model="filtersForm.date_from" type="date" class="w-full" />
                    <TextInput id="date_to" label="Ke" v-model="filtersForm.date_to" type="date" class="w-full" />
                </div>
            </div>
            <SelectInput id="subject_type" label="Target Data" v-model="filtersForm.subject_type" :options="subjectOptions" />
            <SelectInput id="event" label="Jenis Aksi" v-model="filtersForm.event" :options="eventOptions" />
            <div class="my-4 border-t border-gray-200 dark:border-gray-600"></div>
            <div class="flex gap-3">
                <SecondaryButton @click="resetFilters"> Reset </SecondaryButton>
                <PrimaryButton @click="applyFilters">Apply Filter</PrimaryButton>
            </div>
        </div>
    </Modal>
</template>