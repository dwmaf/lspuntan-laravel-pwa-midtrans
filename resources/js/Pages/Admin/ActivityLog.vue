<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SecondaryButton from '@/Components/Button/SecondaryButton.vue';
import SeeButton from '@/Components/Button/SeeButton.vue';
import BackButton from '@/Components/Button/BackButton.vue';
import Modal from '@/Components/Modal.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, reactive, computed, watch } from 'vue';
import { MoveRight, FunnelIcon, X } from 'lucide-vue-next';
import TextInput from '@/Components/Input/TextInput.vue';
import CustomHeader from '@/Components/CustomHeader.vue';
import SelectInput from '@/Components/Input/SelectInput.vue';
import Dropdown from '@/Components/Dropdown.vue';
import InputLabel from '@/Components/Input/InputLabel.vue';
import PrimaryButton from '@/Components/Button/PrimaryButton.vue';
import { useFormat } from "@/Composables/useFormat";
const props = defineProps({
    logs: Object,
    filters: Object,
    filterOptions: Object,
    skemaMap: Object,
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
        onFinish: () => {
            if (Object.keys(usePage().props.errors).length === 0) {
                closeFilterModal();
            }
        },
    });
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

const { formatDateTime } = useFormat();
const subjectTypeLabels = {
    News: 'Pengumuman',
};

const cleanSubjectType = (subject) => {
    if (!subject) return 'N/A';
    const parts = subject.split('\\');
    const name = parts.pop();
    return subjectTypeLabels[name] || name;
};

const getRoleLabel = (log) => {
    if (!log || !log.causer || !log.causer.roles) {
        return '';
    }
    const isAdmin = log.causer.roles.some(role => role.name === 'admin');
    return isAdmin ? 'Admin ' : '';
};

const getUserLogMessage = (log) => {
    if (log.event !== 'updated' || log.subject_type !== 'App\\Models\\User') return null;
    const props = typeof log.properties === 'string' ? JSON.parse(log.properties) : (log.properties || {});
    return props?.attributes?.banned_at ? ' memblokir akses login ' : ' memulihkan akses login ';
};

const getFileName = (path) => {
    if (!path) return null;
    const parts = String(path).split('/');
    return parts.pop();
};

const getSkemaLogMessage = (log) => {
    if (log.subject_type !== 'App\\Models\\Skema') return null;
    const props = typeof log.properties === 'string' ? JSON.parse(log.properties) : (log.properties || {});
    const namaSkema = log.subject?.nama_skema ?? '';

    if (log.event === 'created') return ' menambahkan ' + namaSkema;
    if (log.event === 'deleted') return ' menghapus ' + namaSkema;

    if (log.event === 'updated') {
        const oldKeys = Object.keys(props.old || {});
        const attrKeys = Object.keys(props.attributes || {});
        const onlyIsActive = oldKeys.length === 1 && oldKeys[0] === 'is_active'
            && attrKeys.length === 1 && attrKeys[0] === 'is_active';

        if (onlyIsActive) {
            return props.attributes.is_active == 0
                ? ' menonaktifkan skema ' + namaSkema
                : ' mengaktifkan skema ' + namaSkema;
        }
        return ' mengubah data ' + namaSkema;
    }

    return null;
};

const getAsesorLogMessage = (log) => {
    if (log.subject_type !== 'App\\Models\\Asesor') return null;
    const props = typeof log.properties === 'string' ? JSON.parse(log.properties) : (log.properties || {});
    const namaAsesor = props.asesor_user_name ?? log.subject?.user?.name ?? '';

    if (log.event === 'created') return ' menambahkan asesor ' + namaAsesor;
    if (log.event === 'deleted') return ' menghapus akun asesor ' + namaAsesor;

    if (log.event === 'updated') {
        const oldKeys = Object.keys(props.old || {});
        const attrKeys = Object.keys(props.attributes || {});
        const onlyIsActive = oldKeys.length === 1 && oldKeys[0] === 'is_active'
            && attrKeys.length === 1 && attrKeys[0] === 'is_active';

        if (onlyIsActive) {
            return props.attributes.is_active == 0
                ? ' menonaktifkan asesor ' + namaAsesor
                : ' mengaktifkan asesor ' + namaAsesor;
        }
        if ('is_active' in (props.old ?? {})) {
            const suffix = props.attributes?.is_active == 0
                ? ' dan menonaktifkannya'
                : ' dan mengaktifkannya';
            return ' mengubah data asesor ' + namaAsesor + suffix;
        }
        return ' mengubah data asesor ' + namaAsesor;
    }

    return null;
};

const canShowDetail = (log) => {
    if (log.subject_type === 'App\\Models\\User') return false;
    if (log.subject_type === 'App\\Models\\Skema' || log.subject_type === 'App\\Models\\Asesor') {
        if (log.event === 'deleted') return false;
        if (log.event === 'updated') {
            const props = typeof log.properties === 'string' ? JSON.parse(log.properties) : (log.properties || {});
            const oldKeys = Object.keys(props.old || {});
            const attrKeys = Object.keys(props.attributes || {});
            if (oldKeys.length === 1 && oldKeys[0] === 'is_active'
                && attrKeys.length === 1 && attrKeys[0] === 'is_active') {
                return false;
            }
        }
        return true;
    }
    return true;
};

const fieldLabels = {
    nama_skema: 'Nama Skema',
    is_active: 'Status Aktif',
    format_apl_1: 'Format APL-1',
    format_apl_2: 'Format APL-2',
    format_asesmen: 'Format Asesmen',
    no_reg_met: 'No. Registrasi MET',
    masa_berlaku_sertif_teknis: 'Masa Berlaku Sertifikat Teknis',
    masa_berlaku_sertif_asesor: 'Masa Berlaku Sertifikat Asesor',
    name: 'Nama Asesor',
    email: 'Email',
    no_tlp_hp: 'No. WhatsApp',
};

const getFieldLabel = (key) => fieldLabels[key] ?? key;

const getSkemaDetailItems = (log) => {
    const props = typeof log.properties === 'string' ? JSON.parse(log.properties) : (log.properties || {});
    const items = [];

    if (log.event === 'created') {
        for (const [key, value] of Object.entries(props.attributes || {})) {
            if (key === 'is_active') continue;
            let valNew = null;
            if (value !== null && value !== undefined && value !== '') {
                // Ekstrak nama file khusus kolom format_...
                valNew = key.startsWith('format_') ? getFileName(value) : formatValue(value);
            }
            items.push({ label: key, newValue: valNew, oldValue: null, isDeletedFile: false });
        }
    } else if (log.event === 'updated') {
        const old = props.old || {};
        const attrs = props.attributes || {};
        const allKeys = [...new Set([...Object.keys(old), ...Object.keys(attrs)])];
        for (const key of allKeys) {
            let valOld = null;
            if (old[key] !== null && old[key] !== undefined && old[key] !== '') {
                valOld = key.startsWith('format_') ? getFileName(old[key]) : formatValue(old[key]);
            }

            let valNew = null;
            if (attrs[key] !== null && attrs[key] !== undefined && attrs[key] !== '') {
                valNew = key.startsWith('format_') ? getFileName(attrs[key]) : formatValue(attrs[key]);
            }

            // Cek apakah ini file yang diupdate dan isinya dihapus
            const isDeletedFile = key.startsWith('format_') && old[key] && !attrs[key];

            items.push({
                label: key,
                oldValue: valOld,
                newValue: valNew,
                isDeletedFile: isDeletedFile
            });
        }
    }

    return items;
};

const getAsesorToggleSuffix = (log) => {
    if (log.event !== 'updated') return '';
    const props = typeof log.properties === 'string' ? JSON.parse(log.properties) : (log.properties || {});
    if ('is_active' in (props.old ?? {})) {
        return props.attributes?.is_active == 0 ? ' dan menonaktifkan' : ' dan mengaktifkan';
    }
    return '';
};

const getAsesorDetailItems = (log) => {
    const props = typeof log.properties === 'string' ? JSON.parse(log.properties) : (log.properties || {});
    const items = [];

    if (log.event === 'updated') {
        const old = props.old || {};
        const attrs = props.attributes || {};
        const allKeys = [...new Set([...Object.keys(old), ...Object.keys(attrs)])];
        for (const key of allKeys) {
            if (key === 'is_active') continue;

            let valOld = null;
            if (old[key] !== null && old[key] !== undefined && old[key] !== '') {
                valOld = formatValue(old[key]);
            }
            let valNew = null;
            if (attrs[key] !== null && attrs[key] !== undefined && attrs[key] !== '') {
                valNew = formatValue(attrs[key]);
            }

            items.push({ label: key, oldValue: valOld, newValue: valNew });
        }
    }

    return items;
};

const getSkemaNameById = (id, log) => {
    const logIds = log?.properties?.skema_ids ?? [];
    const logNames = log?.properties?.skema_names ?? [];
    const idx = logIds.indexOf(id);
    if (idx !== -1 && logNames[idx]) return logNames[idx];
    return props.skemaMap?.[id] ?? `Skema #${id}`;
};

const getCreatedSkemaNames = (log) => {
    if (log.properties?.skema_names) return log.properties.skema_names;
    return (log.properties?.skema_ids ?? []).map(id => getSkemaNameById(id, log));
};

const getAddedSkemas = (log) => {
    if (log.properties?.added_skema_names) return log.properties.added_skema_names;
    const oldSkemas = log.properties?.old_skema_ids ?? [];
    const newSkemas = log.properties?.skema_ids ?? [];
    return newSkemas.filter(s => !oldSkemas.includes(s)).map(id => getSkemaNameById(id, log));
};

const getRemovedSkemas = (log) => {
    if (log.properties?.removed_skema_names) return log.properties.removed_skema_names;
    const oldSkemas = log.properties?.old_skema_ids ?? [];
    const newSkemas = log.properties?.skema_ids ?? [];
    return oldSkemas.filter(s => !newSkemas.includes(s)).map(id => getSkemaNameById(id, log));
};

const formatValue = (val) => {
    if (typeof val === 'boolean') return val ? 'Ya' : 'Tidak';
    if (val === null || val === undefined) return '-';
    return String(val);
};

</script>

<template>
    <AdminLayout>
        <CustomHeader judul="Catatan Aktivitas Sistem" />

        <div v-if="viewMode === 'list'" class="p-3 sm:p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
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
                                class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase pl-3">
                                No</th>
                            <th
                                class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Deskripsi</th>

                            <th
                                class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Waktu
                            </th>
                            <th
                                class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase pr-3">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="(log, index) in logs.data" :key="log.id">
                            <td class="px-2 py-3 text-sm text-gray-700 dark:text-gray-200 pl-3">{{ logs.from + index }}
                            </td>
                            <td class="px-2 py-3 text-sm text-gray-700 dark:text-gray-200">
                                <template v-if="getUserLogMessage(log)">
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ getRoleLabel(log) }}
                                        {{ log.causer?.name ?? 'Sistem' }}</span>
                                    <span class="text-gray-500 dark:text-gray-400"> {{ getUserLogMessage(log) }}</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ log.subject?.name ??
                                        'User #' + log.subject_id }}</span>
                                    <span v-if="log.subject_id"
                                        class="font-mono text-xs text-gray-400 dark:text-gray-500 ml-1">
                                        (ID: {{ log.subject_id }})
                                    </span>
                                </template>
                                <template v-else-if="getSkemaLogMessage(log)">
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ getRoleLabel(log) }}
                                        {{ log.causer?.name ?? 'Sistem' }}</span>
                                    <span class="text-gray-500 dark:text-gray-400"> {{ getSkemaLogMessage(log) }}</span>
                                    <span v-if="log.subject_id"
                                        class="font-mono text-xs text-gray-400 dark:text-gray-500 ml-1">
                                        (ID: {{ log.subject_id }})
                                    </span>
                                </template>
                                <template v-else-if="getAsesorLogMessage(log)">
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ getRoleLabel(log) }}
                                        {{ log.causer?.name ?? 'Sistem' }}</span>
                                    <span class="text-gray-500 dark:text-gray-400"> {{ getAsesorLogMessage(log) }}
                                    </span>
                                    <span v-if="log.subject_id"
                                        class="font-mono text-xs text-gray-400 dark:text-gray-500 ml-1">
                                        (ID: {{ log.subject_id }})
                                    </span>
                                </template>
                                <template v-else>
                                    <div class="truncate max-w-[300px] lg:max-w-none" :title="log.description">
                                        {{ log.description }}
                                    </div>
                                </template>
                            </td>

                            <td class="px-2 py-3 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                {{ formatDateTime(log.created_at, 'short') }}
                            </td>
                            <td class="px-2 py-3 pr-3">
                                <SeeButton v-if="canShowDetail(log)" @click="showDetailView(log)">Lihat</SeeButton>
                                <span v-else class="text-xs text-gray-400">-</span>
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
            class="p-3 sm:p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="flex justify-between items-center mb-6 border-b pb-4 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Detail Aktivitas</h3>
                <BackButton @click="backToList">
                    Kembali
                </BackButton>
            </div>

            <template
                v-if="selectedLog.subject_type === 'App\\Models\\Skema' && getSkemaDetailItems(selectedLog).length > 0">
                <p class="text-base text-gray-900 dark:text-gray-200 mb-4">
                    <span class="font-semibold">{{ selectedLog.causer?.name ?? 'Sistem' }}</span>
                    {{ selectedLog.event === 'created' ? ' menambahkan skema ' : ' mengubah data skema ' }}
                    <span class="font-semibold">{{ selectedLog.subject?.nama_skema ?? '' }}</span>
                    <span v-if="selectedLog.subject_id" class="font-mono text-xs text-gray-400 dark:text-gray-500 ml-1">
                        (ID = {{ selectedLog.subject_id }})
                    </span>
                    {{ selectedLog.event === 'created' ? ' dengan data berikut.' : ' berikut.' }}
                </p>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg px-4 py-1">
                    <dl class="divide-y divide-gray-200 dark:divide-gray-600">
                        <div v-for="(item, i) in getSkemaDetailItems(selectedLog)" :key="i" class="flex py-2 text-sm">
                            <dt class="w-2/5 font-medium text-gray-500 dark:text-gray-400">
                                {{ getFieldLabel(item.label) }} {{ item.isDeletedFile ? ' (dihapus)' : '' }}
                            </dt>
                            <dd class="w-3/5 text-gray-900 dark:text-gray-200">
                                <template v-if="item.oldValue !== null">
                                    <template v-if="item.newValue !== null">
                                        <span class=" line-through">{{ item.oldValue }}</span>
                                        <MoveRight class="w-4 h-4 inline mx-2 text-gray-400" />
                                        <span>{{ item.newValue }}</span>
                                    </template>
                                    <template v-else>
                                        <span class="line-through">{{ item.oldValue }}</span>
                                    </template>
                                </template>
                                <template v-else>
                                    {{ item.newValue }}
                                </template>
                            </dd>
                        </div>
                    </dl>
                </div>
            </template>

            <template v-else-if="selectedLog.subject_type === 'App\\Models\\Asesor'">
                <p class="text-base text-gray-900 dark:text-gray-200 mb-4">
                    <span class="font-semibold">{{ selectedLog.causer?.name ?? 'Sistem' }}</span>
                    <template v-if="selectedLog.event === 'created'"> menambahkan asesor </template>
                    <template v-else> mengubah data asesor{{ getAsesorToggleSuffix(selectedLog) }} </template>
                    <span class="font-semibold">{{ selectedLog.properties?.asesor_user_name ??
                        selectedLog.subject?.user?.name ?? ''
                    }}</span>
                    <span v-if="selectedLog.subject_id" class="font-mono text-xs text-gray-400 dark:text-gray-500 ml-1">
                        (ID = {{ selectedLog.subject_id }})
                    </span>
                    {{ selectedLog.event === 'created' ? ' dengan skema yang diampu berikut.' : ' berikut.' }}
                </p>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg px-4 py-1">
                    <dl class="divide-y divide-gray-200 dark:divide-gray-600">
                        <template v-if="selectedLog.event === 'created'">
                            <div v-for="(skema, i) in getCreatedSkemaNames(selectedLog)" :key="'s' + i"
                                class="flex py-2 text-sm">
                                <dt class="w-2/5 font-medium text-gray-500 dark:text-gray-400">Skema {{ i + 1 }}</dt>
                                <dd class="w-3/5 text-gray-900 dark:text-gray-200">{{ skema }}</dd>
                            </div>
                            <div v-for="(item, i) in getSkemaDetailItems(selectedLog)" :key="'f' + i"
                                class="flex py-2 text-sm">
                                <dt class="w-2/5 font-medium text-gray-500 dark:text-gray-400">{{
                                    getFieldLabel(item.label) }}</dt>
                                <dd class="w-3/5 text-gray-900 dark:text-gray-200">{{ item.newValue }}</dd>
                            </div>
                        </template>
                        <template v-if="selectedLog.event === 'updated' && (selectedLog.properties?.old_skema_ids || selectedLog.properties?.added_skema_ids || selectedLog.properties?.removed_skema_ids)">
                            <div class="flex py-2 text-sm">
                                <dt class="w-2/5 font-medium text-gray-500 dark:text-gray-400">Skema</dt>
                                <dd class="w-3/5 text-gray-900 dark:text-gray-200">
                                    <template v-if="getRemovedSkemas(selectedLog).length || getAddedSkemas(selectedLog).length">
                                        <span v-if="getRemovedSkemas(selectedLog).length" class="line-through">{{ getRemovedSkemas(selectedLog).join(', ') }}</span>
                                        <MoveRight v-if="getRemovedSkemas(selectedLog).length && getAddedSkemas(selectedLog).length" class="w-4 h-4 inline mx-2 text-gray-400" />
                                        <span v-if="getAddedSkemas(selectedLog).length">{{ getAddedSkemas(selectedLog).join(', ') }}</span>
                                    </template>
                                </dd>
                            </div>
                        </template>
                        <div v-for="(item, i) in getAsesorDetailItems(selectedLog)" :key="i" class="flex py-2 text-sm">
                            <dt class="w-2/5 font-medium text-gray-500 dark:text-gray-400">{{ getFieldLabel(item.label)
                            }}</dt>
                            <dd class="w-3/5 text-gray-900 dark:text-gray-200">
                                <template v-if="item.oldValue !== null">
                                    <template v-if="item.newValue !== null">
                                        <span class="line-through">{{ item.oldValue }}</span>
                                        <MoveRight class="w-4 h-4 inline mx-2 text-gray-400" />
                                        <span>{{ item.newValue }}</span>
                                    </template>
                                    <template v-else>
                                        <span class="line-through">{{ item.oldValue }}</span>
                                    </template>
                                </template>
                                <template v-else>
                                    {{ item.newValue }}
                                </template>
                            </dd>
                        </div>
                    </dl>
                </div>
            </template>

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
                    <TextInput id="date_from" label="Dari" v-model="filtersForm.date_from" type="date" class="w-full" :error="$page.props.errors.date_from" />
                    <TextInput id="date_to" label="Ke" v-model="filtersForm.date_to" type="date" class="w-full" :error="$page.props.errors.date_to" />
                </div>
            </div>
            <SelectInput id="subject_type" label="Target Data" v-model="filtersForm.subject_type"
                :options="subjectOptions" />
            <SelectInput id="event" label="Jenis Aksi" v-model="filtersForm.event" :options="eventOptions" />
            <div class="my-4 border-t border-gray-200 dark:border-gray-600"></div>
            <div class="flex gap-3">
                <SecondaryButton @click="resetFilters"> Reset </SecondaryButton>
                <PrimaryButton @click="applyFilters">Apply Filter</PrimaryButton>
            </div>
        </div>
    </Modal>
</template>