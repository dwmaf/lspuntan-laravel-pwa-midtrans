<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AdminSertifikasiMenu from "@/Components/AdminSertifikasiMenu.vue";
import CustomHeader from '@/Components/CustomHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import SeeButton from '@/Components/Button/SeeButton.vue';
import BackButton from '@/Components/Button/BackButton.vue';
import Modal from '@/Components/Modal.vue';
import TextInput from '@/Components/Input/TextInput.vue';
import SelectInput from '@/Components/Input/SelectInput.vue';
import InputLabel from '@/Components/Input/InputLabel.vue';
import PrimaryButton from '@/Components/Button/PrimaryButton.vue';
import SecondaryButton from '@/Components/Button/SecondaryButton.vue';
import { ref, reactive, computed, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { MoveRight, FunnelIcon, X } from 'lucide-vue-next';
import { useFormat } from "@/Composables/useFormat";

const props = defineProps({
    sertifikasi: Object,
    logs: Object,
    filters: Object,
    filterOptions: Object,
    sertifikatAsesiMap: Object,
    asesorMap: Object,
});

const { formatDateTime, formatDate, formatCurrency } = useFormat();

const filtersForm = reactive({
    search: props.filters?.search || '',
    date_from: props.filters?.date_from || '',
    date_to: props.filters?.date_to || '',
    subject_type: props.filters?.subject_type || '',
    event: props.filters?.event || '',
});

const hasActiveFilters = computed(() => {
    const { search, ...advancedFilters } = filtersForm;
    return Object.values(advancedFilters).some(value => value !== '' && value !== null);
});

const showFilterModal = ref(false);
const openFilterModal = () => showFilterModal.value = true;
const closeFilterModal = () => showFilterModal.value = false;

let searchTimeoutId = null;
watch(() => filtersForm.search, (newValue) => {
    clearTimeout(searchTimeoutId);
    searchTimeoutId = setTimeout(() => {
        router.get(window.location.pathname, filtersForm, {
            preserveState: true,
            replace: true,
        });
    }, 500);
});

const applyFilters = () => {
    router.get(window.location.pathname, filtersForm, {
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

const subjectTypeLabels = {
    Sertifikasi: 'Sertifikasi',
    Pengumuman: 'Pengumuman',
};

const cleanSubjectType = (subject) => {
    if (!subject) return 'N/A';
    const parts = subject.split('\\');
    const name = parts.pop();
    return subjectTypeLabels[name] || name;
};

const subjectOptions = computed(() => {
    if (!props.filterOptions?.subjects) return [{ value: '', text: 'Semua Target' }];
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

const getRoleLabel = (log) => {
    if (log.subject_type === 'App\\Models\\Asesmen') return 'Asesor ';
    const causerRoles = log.causer?.roles?.map(r => r.name) || [];
    if (causerRoles.includes('admin')) return 'Admin ';
    if (causerRoles.includes('asesor')) return 'Asesor ';
    if (causerRoles.includes('asesi')) return 'Asesi ';
    return 'Admin ';
};

const canShowDetail = (log) => {
    if (log.event === 'deleted') return false;
    if (log.event === 'created' && log.subject_type === 'App\\Models\\Asesi') return false;
    if (log.subject_type === 'App\\Models\\Asesi') {
        const propsData = getParsedProperties(log);
        const oldAsesor = propsData.old?.asesor_id;
        const newAsesor = propsData.attributes?.asesor_id;

        if (newAsesor) {
            if (!oldAsesor) {
                const hasOtherChanges = propsData.attributes?.status_berkas || propsData.attributes?.status_final;
                if (!hasOtherChanges) {
                    return false;
                }
            }
        }
    }
    return true;
};

const getParsedProperties = (log) => {
    return typeof log.properties === 'string' ? JSON.parse(log.properties) : (log.properties || {});
};

const getContentPreview = (log) => {
    if (log.subject_type !== 'App\\Models\\Pengumuman' && log.subject_type !== 'App\\Models\\Asesmen') return null;
    const props = getParsedProperties(log);
    let content = null;
    if (log.event === 'deleted') {
        content = props.old?.content;
    } else {
        content = props.attributes?.content;
    }
    if (!content) return null;
    return content.length > 60 ? content.substring(0, 60) + '...' : content;
};

const getFileName = (path) => {
    if (!path) return null;
    const parts = path.split('/');
    return parts.pop();
};

const getAsesiName = (subjectId) => {
    const asesi = props.sertifikasi.asesi?.find(a => a.id === subjectId);
    return asesi?.mahasiswa?.user?.name ?? 'Asesi';
};

const getAsesorName = (asesorId) => {
    const asesor = props.sertifikasi.asesor?.find(a => a.id === asesorId);
    if (asesor?.user?.name) return asesor.user.name;
    return props.asesorMap?.[asesorId] ?? `Asesor #${asesorId}`;
};

const resolveAsesorIds = (ids) => {
    if (!ids || !Array.isArray(ids)) return [];
    return ids.map(id => getAsesorName(Number(id)));
};

const getAsesiActionText = (log) => {
    const propsData = getParsedProperties(log);
    const newStatusBerkas = propsData.attributes?.status_berkas;
    const newStatusFinal = propsData.attributes?.status_final;
    const newAsesorId = propsData.attributes?.asesor_id;

    const rawAsesiName = getAsesiName(log.subject_id);
    const asesiName = `<strong class="font-semibold text-gray-900 dark:text-gray-100">${rawAsesiName}</strong>`;

    const causerRoles = log.causer?.roles?.map(r => r.name) || [];
    const isAsesi = causerRoles.includes('asesi');

    const skemaName = props.sertifikasi.skema?.nama_skema ?? 'Sertifikasi';
    if (log.event === 'created') return ` mendaftar sertifikasi ${skemaName}`;

    if (isAsesi && propsData.old?.status_berkas === 'perlu_perbaikan_berkas' && newStatusBerkas === 'menunggu_verifikasi_admin') return ` memperbaiki berkasnya`;
    if (newStatusBerkas === 'sudah_lengkap') return ` menyatakan berkas ${asesiName} sudah lengkap`;
    if (newStatusBerkas === 'menunggu_verifikasi_admin') return ` menyatakan berkas ${asesiName} masih pending`;
    if (newStatusBerkas === 'perlu_perbaikan_berkas') return ` menyatakan berkas ${asesiName} perlu diperbaiki`;

    if (newStatusFinal === 'kompeten') return ` menyatakan ${asesiName} Kompeten`;
    if (newStatusFinal === 'belum_kompeten') return ` menyatakan ${asesiName} Belum Kompeten`;
    if (newStatusFinal === 'belum_ditetapkan') return ` mereset status final ${asesiName} menjadi Belum Ditetapkan`;
    if (newStatusFinal === 'diskualifikasi') return ` mendiskualifikasi ${asesiName}`;
    if (newAsesorId) {
        const rawAsesorName = getAsesorName(newAsesorId);
        const asesorName = `<strong class="font-semibold text-gray-900 dark:text-gray-100">${rawAsesorName}</strong>`;
        return ` menetapkan Asesor ${asesorName} kepada asesi ${asesiName}`;
    }

    return `memperbarui data asesi ${asesiName}`;
}

const getAsesiDetailItems = (log) => {
    const propsData = getParsedProperties(log);
    const items = [];
    const old = propsData.old || {};
    const attrs = propsData.attributes || {};
    const allKeys = [...new Set([...Object.keys(old), ...Object.keys(attrs)])];

    for (const key of allKeys) {
        let label = key;
        let oldValue = old[key];
        let newValue = attrs[key];

        if (key === 'status_berkas') {
            label = 'Status Berkas';
            // hapus tanda _ jadi spasi
            oldValue = oldValue ? oldValue.replace(/_/g, ' ') : null;
            newValue = newValue ? newValue.replace(/_/g, ' ') : null;
        } else if (key === 'status_final') {
            label = 'Status Kelulusan';
            oldValue = oldValue ? oldValue.replace(/_/g, ' ') : null;
            newValue = newValue ? newValue.replace(/_/g, ' ') : null;
        } else if (key === 'asesor_id') {
            label = 'Asesor';
            // id asesor jadi nama mereka
            oldValue = oldValue ? getAsesorName(oldValue) : '-';
            newValue = newValue ? getAsesorName(newValue) : '-';

            items.push({
                label,
                oldValue,
                newValue
            });
            continue; // Langsung skip ke perulangan berikutnya agar formatValue bawaan tidak menimpa
        }

        items.push({
            label,
            oldValue: formatValue(oldValue),
            newValue: formatValue(newValue),
        });
    }
    return items;
};

const getPengumumanDetailItems = (log) => {
    const props = getParsedProperties(log);
    const items = [];

    if (log.event === 'created') {
        if (props.attributes?.content) {
            items.push({ label: 'Konten Pengumuman', newValue: props.attributes.content, oldValue: null });
        }
        if (props.attributes?.path_file) {
            items.push({ label: 'File', newValue: getFileName(props.attributes.path_file), oldValue: null });
        }
    } else if (log.event === 'updated') {
        const old = props.old || {};
        const attrs = props.attributes || {};
        const allKeys = [...new Set([...Object.keys(old), ...Object.keys(attrs)])];
        for (const key of allKeys) {
            if (key === 'sertifikasi_id') continue;
            if (key === 'content') {
                items.push({ label: 'Konten Pengumuman', oldValue: formatValue(old[key]), newValue: formatValue(attrs[key]) });
            } else if (key === 'path_file') {
                const label = old[key] && !attrs[key] ? 'File (dihapus)' : 'File';
                items.push({ label, oldValue: getFileName(old[key]), newValue: getFileName(attrs[key]) });
            }
        }
    }

    return items;
};

const getAsesmenDetailItems = (log) => {
    const props = getParsedProperties(log);
    const items = [];

    if (log.event === 'created') {
        if (props.attributes?.content) {
            items.push({ label: 'Instruksi Tugas', newValue: props.attributes.content, oldValue: null });
        }
        if (props.attributes?.deadline) {
            items.push({ label: 'Tenggat Waktu', newValue: formatDateTime(props.attributes.deadline, 'short'), oldValue: null });
        }
        if (props.attributes?.path_file) {
            items.push({ label: 'File Lampiran', newValue: getFileName(props.attributes.path_file), oldValue: null });
        }
    } else if (log.event === 'updated') {
        const old = props.old || {};
        const attrs = props.attributes || {};
        const allKeys = [...new Set([...Object.keys(old), ...Object.keys(attrs)])];
        for (const key of allKeys) {
            if (key === 'sertifikasi_id') continue;
            const label = key === 'content' ? 'Instruksi Tugas'
                : key === 'deadline' ? 'Tenggat Waktu'
                    : key === 'path_file' ? (old[key] && !attrs[key] ? 'File Lampiran (dihapus)' : 'File Lampiran')
                        : key;
            let valOld = null;
            if (old[key] !== null && old[key] !== undefined && old[key] !== '') {
                if (key === 'deadline') valOld = formatDateTime(old[key], 'short');
                else valOld = key === 'path_file' ? getFileName(old[key]) : formatValue(old[key]);
            }
            let valNew = null;
            if (attrs[key] !== null && attrs[key] !== undefined && attrs[key] !== '') {
                if (key === 'deadline') valNew = formatDateTime(attrs[key], 'short');
                else valNew = key === 'path_file' ? getFileName(attrs[key]) : formatValue(attrs[key]);
            }
            items.push({ label, oldValue: valOld, newValue: valNew });
        }
    }

    return items;
};

const getSertifikatAsesiName = (log) => {
    if (log.subject?.asesi?.mahasiswa?.user?.name) return log.subject.asesi.mahasiswa.user.name;

    const asesiId = props.sertifikatAsesiMap?.[log.subject_id]
        ?? getParsedProperties(log).attributes?.asesi_id
        ?? getParsedProperties(log).old?.asesi_id;
    if (asesiId) {
        const asesi = props.sertifikasi.asesi?.find(a => a.id === asesiId);
        if (asesi?.mahasiswa?.user?.name) return asesi.mahasiswa.user.name;
    }

    return 'Asesi';
};

const sertifikasiFieldLabels = {
    tuk: 'TUK',
    biaya: 'Biaya',
    no_rek: 'No. Rekening',
    bank: 'Bank / E-Wallet',
    atas_nama_rek: 'Atas Nama',
    tgl_apply_dibuka: 'Tgl Pendaftaran Dibuka',
    tgl_apply_ditutup: 'Tgl Pendaftaran Ditutup',
    tgl_asesmen_mulai: 'Tgl Asesmen Mulai',
    tgl_asesmen_selesai: 'Tgl Asesmen Selesai',
    status: 'Status',
};

const formatSertifikasiValue = (key, val) => {
    if (val === null || val === undefined) return null;
    if (['tgl_apply_dibuka', 'tgl_apply_ditutup', 'tgl_asesmen_mulai', 'tgl_asesmen_selesai'].includes(key)) return formatDate(val);
    if (key === 'biaya') return formatCurrency(val);
    if (key === 'status') {
        const labels = { berlangsung: 'Berlangsung', selesai: 'Selesai', dibatalkan: 'Dibatalkan' };
        return labels[val] || val;
    }
    return String(val);
};

const getSertifikasiActionText = (log) => {
    const skemaName = props.sertifikasi.skema?.nama_skema ?? 'Sertifikasi';
    const propsData = getParsedProperties(log);

    if (log.event === 'created') return `memulai sertifikasi ${skemaName}`;

    if (propsData.added_asesor_names || propsData.removed_asesor_names || propsData.attributes?.asesor || propsData.old?.asesor) {
        return `mengubah data sertifikasi ${skemaName}`;
    }

    if (propsData.asesi_ids) return log.description.replace(/_/g, ' ');
    if (log.event === 'updated') {
        if (propsData.attributes?.status === 'selesai') {
            const otherKeys = Object.keys(propsData.attributes).filter(k => k !== 'status');
            const suffix = otherKeys.length ? ` (${otherKeys.length} perubahan lain)` : '';
            return `menyatakan sertifikasi ${skemaName} selesai${suffix}`;
        }
        return `mengubah data sertifikasi ${skemaName}`;
    }
    return log.description;
};

const getSertifikasiDetailItems = (log) => {
    const props = getParsedProperties(log);
    const items = [];

    // Asesor changes
    if (props.added_asesor_names || props.removed_asesor_names || props.attributes?.asesor || props.old?.asesor) {
        let oldValue = null;
        let newValue = null;
        if (props.added_asesor_names?.length || props.removed_asesor_names?.length) {
            if (props.removed_asesor_names?.length) oldValue = props.removed_asesor_names.join(', ');
            if (props.added_asesor_names?.length) newValue = props.added_asesor_names.join(', ');
        } else {
            oldValue = resolveAsesorIds(props.old?.asesor).join(', ') || null;
            newValue = resolveAsesorIds(props.attributes?.asesor).join(', ') || null;
        }
        items.push({
            label: 'Asesor',
            oldValue,
            newValue,
        });
    }

    // Bulk operations
    if (props.asesi_ids) {
        items.push({
            label: 'Asesi',
            oldValue: null,
            newValue: props.asesi_names?.join(', ') ?? String(props.asesi_ids.length) + ' asesi',
        });
        if (props.asesor_name) {
            items.push({
                label: 'Asesor',
                oldValue: null,
                newValue: props.asesor_name,
            });
        }
        if (props.attributes?.status_berkas) {
            items.push({
                label: 'Status Berkas',
                oldValue: null,
                newValue: String(props.attributes.status_berkas).replace(/_/g, ' '),
            });
        }
        if (props.attributes?.status_final) {
            items.push({
                label: 'Status Kelulusan',
                oldValue: null,
                newValue: String(props.attributes.status_final).replace(/_/g, ' '),
            });
        }
        return items;
    }

    // Field changes
    const old = props.old || {};
    const attrs = props.attributes || {};
    const allKeys = [...new Set([...Object.keys(old), ...Object.keys(attrs)])];

    for (const key of allKeys) {
        if (key === 'asesor' || key === 'asesor_names') continue;
        const oldValue = formatSertifikasiValue(key, old[key]);
        const newValue = formatSertifikasiValue(key, attrs[key]);
        if (oldValue === null && newValue === null) continue;
        const label = sertifikasiFieldLabels[key] || key;
        items.push({
            label,
            oldValue,
            newValue,
        });
    }

    return items;
};

const getSertifikatActionText = (log) => {
    const asesiName = `<strong class="font-semibold text-gray-900 dark:text-gray-100">${getSertifikatAsesiName(log)}</strong>`;
    if (log.event === 'created') return ` menambah data sertifikat asesi ${asesiName}`;
    if (log.event === 'deleted') return ` menghapus data sertifikat asesi ${asesiName}`;
    return ` mengubah data sertifikat asesi ${asesiName}`;
};

const sertifikatFieldLabels = {
    nomor_seri: 'Nomor Seri',
    nomor_sertifikat: 'Nomor Sertifikat',
    nomor_registrasi: 'Nomor Registrasi',
    tanggal_terbit: 'Tanggal Terbit',
    berlaku_hingga: 'Berlaku Hingga',
};

const formatSertifikatValue = (key, val) => {
    if (val === null || val === undefined) return null;
    if (key === 'tanggal_terbit' || key === 'berlaku_hingga') return formatDate(val);
    if (key === 'file_path') return getFileName(val);
    return String(val);
};

const getSertifikatDetailItems = (log) => {
    const props = getParsedProperties(log);
    const items = [];

    if (log.event === 'created') {
        for (const [key, label] of Object.entries(sertifikatFieldLabels)) {
            if (props.attributes?.[key]) {
                items.push({ label, newValue: formatSertifikatValue(key, props.attributes[key]), oldValue: null });
            }
        }
    } else if (log.event === 'updated') {
        const old = props.old || {};
        const attrs = props.attributes || {};
        for (const [key, label] of Object.entries(sertifikatFieldLabels)) {
            if (key in old || key in attrs) {
                items.push({ label, oldValue: formatSertifikatValue(key, old[key]), newValue: formatSertifikatValue(key, attrs[key]) });
            }
        }
    }

    return items;
};

const formatValue = (val) => {
    if (val === null || val === undefined) return '-';
    return String(val);
};
</script>
<template>
    <AdminLayout>
        <CustomHeader :judul="`Log Sertifikasi: ${sertifikasi.skema.nama_skema}`" />
        <AdminSertifikasiMenu :sertifikasi-id="props.sertifikasi.id" />
        <div class="max-w-7xl mx-auto gap-3 flex flex-col">
            <div v-if="viewMode === 'list'" class="p-3 sm:p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <div class="flex justify-end items-center gap-2 mb-4">
                    <div class="w-[243px]">
                        <TextInput v-model="filtersForm.search" type="text" placeholder="Cari log atau nama..." />
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
                                    Waktu</th>
                                <th
                                    class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase pr-3">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="(log, index) in logs.data" :key="log.id">
                                <td class="px-2 py-3 text-sm text-gray-700 dark:text-gray-200 pl-3">{{ logs.from + index
                                    }}</td>
                                <td class="px-2 py-3 text-sm text-gray-700 dark:text-gray-200">
                                    <span class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ getRoleLabel(log) }}</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100 mr-1"> {{ log.causer?.name
                                        ?? 'Sistem' }}</span>
                                    <span class="text-gray-500 dark:text-gray-400">
                                        <template v-if="log.subject_type === 'App\\Models\\Asesi'">
                                            <span v-html="getAsesiActionText(log)">
                                            </span>
                                        </template>
                                        <template v-else-if="log.subject_type === 'App\\Models\\Sertifikat'">
                                            <span v-html="getSertifikatActionText(log)">
                                            </span>
                                        </template>
                                        <template v-else-if="log.subject_type === 'App\\Models\\Sertifikasi'">
                                            {{ getSertifikasiActionText(log) }}
                                        </template>
                                        <template v-else>
                                            {{ log.description }}
                                        </template>
                                        <span v-if="log.subject_id && (log.subject_type === 'App\\Models\\Pengumuman' || log.subject_type === 'App\\Models\\Asesmen')"
                                            class="font-mono text-xs text-gray-400 dark:text-gray-500">
                                            (ID: {{ log.subject_id }})
                                        </span>
                                    </span>
                                    <span v-if="getContentPreview(log)"
                                        class="text-gray-400 dark:text-gray-500 block text-xs mt-0.5">
                                        “{{ getContentPreview(log) }}”
                                    </span>
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
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada aktivitas.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 flex justify-between items-center">
                    <span v-if="logs.total > 0" class="text-sm text-gray-700 dark:text-gray-400">
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
                    <BackButton @click="backToList">Kembali</BackButton>
                </div>

                <template v-if="selectedLog.subject_type === 'App\\Models\\Pengumuman'">
                    <p class="text-base text-gray-900 dark:text-gray-200 mb-4">
                        <span class="font-medium">{{ getRoleLabel(selectedLog) }}</span>
                        <span class="font-semibold"> {{ selectedLog.causer?.name ?? 'Sistem' }}</span>
                        <span>
                            {{ selectedLog.event === 'created' ? ' membuat '
                                : selectedLog.event === 'deleted' ? 'menghapus ' : ' mengedit ' }}
                            pengumuman
                        </span>
                        <span v-if="selectedLog.subject_id"
                            class="font-mono text-xs text-gray-400 dark:text-gray-500 ml-1">
                            (ID = {{ selectedLog.subject_id }})
                        </span>
                        <span class="">pada {{ formatDateTime(selectedLog.created_at) }}</span>
                    </p>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg px-4 py-1">
                        <dl class="divide-y divide-gray-200 dark:divide-gray-600">
                            <div v-for="(item, i) in getPengumumanDetailItems(selectedLog)" :key="i"
                                class="flex py-2 text-sm">
                                <dt class="w-2/5 font-medium text-gray-500 dark:text-gray-400">{{ item.label }}</dt>
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

                <template v-else-if="selectedLog.subject_type === 'App\\Models\\Asesmen'">
                    <p class="text-base text-gray-900 dark:text-gray-200 mb-4">
                        <span class="font-medium">Asesor </span>
                        <span class="font-semibold"> {{ selectedLog.causer?.name ?? 'Sistem' }}</span>
                        {{ selectedLog.event === 'created' ? ' membuat '
                            : selectedLog.event === 'deleted' ? ' menghapus ' : ' mengedit ' }}
                        instruksi asesmen
                        <span v-if="selectedLog.subject_id"
                            class="font-mono text-xs text-gray-400 dark:text-gray-500 ml-1">
                            (ID = {{ selectedLog.subject_id }})
                        </span>
                        <span>pada {{
                            formatDateTime(selectedLog.created_at) }}</span>
                    </p>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg px-4 py-1">
                        <dl class="divide-y divide-gray-200 dark:divide-gray-600">
                            <div v-for="(item, i) in getAsesmenDetailItems(selectedLog)" :key="i"
                                class="flex py-2 text-sm">
                                <dt class="w-2/5 font-medium text-gray-500 dark:text-gray-400">{{ item.label }}</dt>
                                <dd class="w-3/5 text-gray-900 dark:text-gray-200">
                                    <template v-if="item.oldValue !== null">
                                        <template v-if="item.newValue !== null">
                                            <span class="line-through">{{ item.oldValue }}</span>
                                            <MoveRight class="w-4 h-4 inline mx-2 text-gray-400" />
                                            <span>{{ item.newValue }}</span>
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

                <template v-else-if="selectedLog.subject_type === 'App\\Models\\Sertifikat'">
                    <p class="text-base text-gray-900 dark:text-gray-200 mb-4">
                        <span class="font-medium">Admin </span>
                        <span class="font-semibold"> {{ selectedLog.causer?.name ?? 'Sistem' }}</span>
                        <span>
                            {{ selectedLog.event === 'created' ? ' menambah data sertifikat asesi '
                                : selectedLog.event === 'deleted' ? ' menghapus data sertifikat asesi ' : ' mengubah data sertifikat asesi ' }}
                        </span>
                        <strong class="font-semibold text-gray-900 dark:text-gray-100">{{ getSertifikatAsesiName(selectedLog) }}</strong>
                        <span class="ml-1">pada {{ formatDateTime(selectedLog.created_at) }}</span>
                    </p>
                    <div v-if="selectedLog.event !== 'deleted'" class="bg-gray-50 dark:bg-gray-700 rounded-lg px-4 py-1">
                        <dl class="divide-y divide-gray-200 dark:divide-gray-600">
                            <div v-for="(item, i) in getSertifikatDetailItems(selectedLog)" :key="i"
                                class="flex py-2 text-sm">
                                <dt class="w-2/5 font-medium text-gray-500 dark:text-gray-400">{{ item.label }}</dt>
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

                <template v-else-if="selectedLog.subject_type === 'App\\Models\\Sertifikasi'">
                    <p class="text-base text-gray-900 dark:text-gray-200 mb-4">
                        <span class="font-medium">Admin </span>
                        <span class="font-semibold"> {{ selectedLog.causer?.name ?? 'Sistem' }}</span>
                        <span>{{ ' ' + getSertifikasiActionText(selectedLog) }}</span>
                        <span class="mx-1">pada {{ formatDateTime(selectedLog.created_at) }}</span>
                        <span v-if="selectedLog.event === 'updated'">dengan rincian perubahan berikut.</span>
                    </p>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg px-4 py-1">
                        <dl class="divide-y divide-gray-200 dark:divide-gray-600">
                            <div v-for="(item, i) in getSertifikasiDetailItems(selectedLog)" :key="i"
                                class="flex py-2 text-sm">
                                <dt class="w-2/5 font-medium text-gray-500 dark:text-gray-400">{{ item.label }}</dt>
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

                <template v-else-if="selectedLog.subject_type === 'App\\Models\\Asesi'">
                    <p class="text-base text-gray-900 dark:text-gray-200 mb-4">
                        <span class="font-medium">{{ getRoleLabel(selectedLog) }}</span>
                        <span class="font-semibold"> {{ selectedLog.causer?.name ?? 'Sistem' }}</span>
                        <span v-html="getAsesiActionText(selectedLog)"></span>
                        <span class="mx-1">pada {{ formatDateTime(selectedLog.created_at) }}</span>
                        <template v-if="selectedLog.event !== 'created'">dengan rincian perubahan berikut.</template>
                    </p>
                    <div v-if="selectedLog.event !== 'created'" class="bg-gray-50 dark:bg-gray-700 rounded-lg px-4 py-1">
                        <dl class="divide-y divide-gray-200 dark:divide-gray-600">
                            <div v-for="(item, i) in getAsesiDetailItems(selectedLog)" :key="i"
                                class="flex py-2 text-sm">
                                <dt class="w-2/5 font-medium text-gray-500 dark:text-gray-400">{{ item.label }}</dt>
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
