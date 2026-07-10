<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AdminSertifikasiMenu from "@/Components/AdminSertifikasiMenu.vue";
import CustomHeader from '@/Components/CustomHeader.vue';
import TextInput from '@/Components/Input/TextInput.vue';
import SeeButton from "@/Components/Button/SeeButton.vue";
import StatusBadge from "@/Components/StatusBadge.vue";
import { MoveRight, FunnelIcon, X, FileText, Lock, Award } from 'lucide-vue-next';
import { ref, computed, watch, reactive } from 'vue';
import PrimaryButton from '@/Components/Button/PrimaryButton.vue';
import SecondaryButton from '@/Components/Button/SecondaryButton.vue';
import SelectInput from '@/Components/Input/SelectInput.vue';
import InputLabel from '@/Components/Input/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import Checkbox from '@/Components/Input/Checkbox.vue';
import { useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    sertification: Object,
    unassignedCount: {
        type: Number,
    }
});

const selectedAsesis = ref([]);

const roles = computed(() => (usePage().props.auth.roles ?? []).map(r => typeof r === 'string' ? r : r.name));
const isAdmin = computed(() => roles.value.includes('admin'));
const currentUserId = computed(() => usePage().props.auth.user?.id);
const isAsesor = computed(() =>
    props.sertification.asesors?.some(a => a.user_id === currentUserId.value)
);

const canBulkUpdateBerkas = computed(() => {
    if (selectedAsesis.value.length === 0) return false;
    if (!isAdmin.value) return false;
    const selected = filteredAsesis.value.filter(a => selectedAsesis.value.includes(a.id));
    return selected.every(a => {
        const finalStatus = typeof a.status_final === 'object' ? a.status_final?.value : a.status_final;
        return !a.asesor_id && finalStatus === 'belum_ditetapkan';
    });
});

const canBulkAssignAsesor = computed(() => {
    if (selectedAsesis.value.length === 0) return false;
    if (!isAdmin.value) return false;
    
    const selected = filteredAsesis.value.filter(a => selectedAsesis.value.includes(a.id));
    return selected.every(a => {
        const berkasStatus = typeof a.status_berkas === 'object' ? a.status_berkas?.value : a.status_berkas;
        const finalStatus = typeof a.status_final === 'object' ? a.status_final?.value : a.status_final;
        return berkasStatus === 'sudah_lengkap' && finalStatus === 'belum_ditetapkan';
    });
});


const canBulkUpdateFinal = computed(() => {
    if (selectedAsesis.value.length === 0) return false;
    const selected = filteredAsesis.value.filter(a => selectedAsesis.value.includes(a.id));
    return selected.every(a => {
        const user = usePage().props.auth.user;
        const berkasStatus = typeof a.status_berkas === 'object' ? a.status_berkas?.value : a.status_berkas;
        
        return berkasStatus === 'sudah_lengkap' && 
               a.asesor_id !== null &&
               !isAdmin.value &&
               user.id === a.asesor?.user_id;
    });
});

const getStatusBerkasAdministrasi = (status) => {
    const data = {
        'menunggu_verifikasi_admin': {
            variant: 'primary',
            text: 'Menunggu Verifikasi Admin'
        },
        'perlu_perbaikan_berkas': {
            variant: 'warning',
            text: 'Perlu Perbaikan Berkas'
        },
        'sudah_lengkap': {
            variant: 'success',
            text: 'Sudah Lengkap'
        },
    };
    return data[status] || {
        variant: 'neutral',
        text: status
    };
};

const getStatusFinalAsesi = (status) => {
    const data = {
        'belum_ditetapkan': {
            variant: 'neutral',
            text: 'Belum Ditetapkan'
        },
        'belum_kompeten': {
            variant: 'warning',
            text: 'Belum Kompeten'
        },
        'kompeten': {
            variant: 'success',
            text: 'Kompeten'
        },
        'diskualifikasi': {
            variant: 'danger',
            text: 'Diskualifikasi'
        },
    };
    return data[status] || {
        variant: 'neutral',
        text: status
    };
};

const searchQuery = ref('');
const showFilterModal = ref(false);

const statusBerkasAdministrasiOptions = [
    { value: 'menunggu_verifikasi_admin', text: 'Menunggu Verifikasi Admin' },
    { value: 'perlu_perbaikan_berkas', text: 'Perlu Perbaikan Berkas' },
    { value: 'sudah_lengkap', text: 'Sudah Lengkap' },
];

const statusFinalAsesiOptions = [
    { value: 'belum_ditentukan', text: 'Belum Ditentukan' },
    { value: 'belum_kompeten', text: 'Belum Kompeten' },
    { value: 'kompeten', text: 'Kompeten' },
];

const filtersForm = ref({
    statusBerkasAdministrasi: '',
    statusFinalAsesi: '',
    asesor: '',
});

const activeFilters = ref({
    statusBerkasAdministrasi: '',
    statusFinalAsesi: '',
    asesor: '',
});

const asesorFilterOptions = computed(() => [
    { value: '', text: 'Semua Asesi' },
    { value: 'assigned', text: 'Semua yang sudah ditetapkan' },
    { value: 'unassigned', text: 'Belum ditetapkan' },
    ...props.sertification.asesors.map(asesor => ({
        value: String(asesor.id),
        text: asesor.user.name,
    })),
]);

const applyFilters = () => {
    activeFilters.value = { ...filtersForm.value };
    showFilterModal.value = false;
};

const resetFilters = () => {
    filtersForm.value = { statusBerkasAdministrasi: '', statusFinalAsesi: '', asesor: '' };
    activeFilters.value = { statusBerkasAdministrasi: '', statusFinalAsesi: '', asesor: '' };
    showFilterModal.value = false;
};

const closeFilterModal = () => {
    showFilterModal.value = false;
    filtersForm.value = { ...activeFilters.value };
};

const filteredAsesis = computed(() => {
    let result = props.sertification.asesis;

    if (searchQuery.value) {
        const lower = searchQuery.value.toLowerCase();
        result = result.filter(asesi =>
            (asesi.student?.user?.name?.toLowerCase() || '').includes(lower) ||
            (asesi.student?.user?.email?.toLowerCase() || '').includes(lower)
        );
    }

    if (activeFilters.value.statusBerkasAdministrasi) {
        result = result.filter(asesi => asesi.status_berkas === activeFilters.value.statusBerkasAdministrasi);
    }
    if (activeFilters.value.statusFinalAsesi) {
        result = result.filter(asesi => asesi.status_final === activeFilters.value.statusFinalAsesi);
    }

    if (activeFilters.value.asesor) {
        const filterVal = activeFilters.value.asesor;
        if (filterVal === 'assigned') {
            result = result.filter(asesi => asesi.asesor_id);
        } else if (filterVal === 'unassigned') {
            result = result.filter(asesi => !asesi.asesor_id);
        } else {
            result = result.filter(asesi => String(asesi.asesor_id) === filterVal);
        }
    }

    return result;
});

const isSelectAll = computed({
    get: () => filteredAsesis.value.length > 0 && selectedAsesis.value.length === filteredAsesis.value.length,
    set: (val) => {
        if (val) {
            selectedAsesis.value = filteredAsesis.value.map(a => a.id);
        } else {
            selectedAsesis.value = [];
        }
    }
});

const showBulkActionModal = ref(false);
const bulkType = ref(''); // 'berkas', 'asesor', 'final'
const bulkForm = useForm({
    asesi_ids: [],
    status_berkas: '',
    status_final: '',
    catatan_perbaikan: '',
    asesor_id: '',
});

const asesorOptions = computed(() => {
    return props.sertification.asesors.map(asesor => ({
        value: asesor.id,
        text: asesor.user.name
    }));
});

const openBulkModal = (type) => {
    bulkForm.reset('status_berkas', 'status_final', 'catatan_perbaikan', 'asesor_id');
    bulkType.value = type;
    bulkForm.asesi_ids = selectedAsesis.value;
    showBulkActionModal.value = true;
};

const submitBulk = () => {
    let routeName = '';
    if (bulkType.value === 'berkas') routeName = 'admin.sertifikasi.pendaftar.update-status-berkas-bulk';
    if (bulkType.value === 'final') routeName = 'admin.sertifikasi.pendaftar.update-status-final-bulk';
    if (bulkType.value === 'assign_asesor') routeName = 'admin.sertifikasi.pendaftar.assign-asesor-bulk';

    bulkForm.patch(route(routeName, [props.sertification.id]), {
        onSuccess: () => {
            showBulkActionModal.value = false;
            selectedAsesis.value = [];
            bulkForm.reset();
        },
    });
};


</script>
<template>
    <AdminLayout>
        <CustomHeader :judul="`${sertification.skema.nama_skema}: Daftar Peserta`" />
        <AdminSertifikasiMenu :sertification-id="props.sertification.id" />

        <div class="p-3 sm:p-6 bg-white dark:bg-gray-800 shadow-xl rounded-lg ">
            <div class="flex flex-col-reverse sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                <div v-if="selectedAsesis.length > 0" class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        {{ selectedAsesis.length }} dipilih:
                    </span>
                    <SecondaryButton v-if="canBulkUpdateBerkas" @click="openBulkModal('berkas')" class="py-2! px-3! normal-case!">
                        <FileText class="w-4 mr-1" />
                        Update Status Berkas
                    </SecondaryButton>
                    <SecondaryButton v-if="canBulkAssignAsesor" @click="openBulkModal('assign_asesor')" class="py-2! px-3! normal-case!">
                        <FileText class="w-4 mr-1" />
                        Assign Asesor
                    </SecondaryButton>
                    <SecondaryButton v-if="canBulkUpdateFinal" @click="openBulkModal('final')" class="py-2! px-3! normal-case!">
                        <Award class="w-4 mr-1" />
                        Update Status Final
                    </SecondaryButton>
                    <span v-if="!canBulkUpdateBerkas && !canBulkAssignAsesor && !canBulkUpdateFinal" 
                          class="text-xs italic text-red-500 max-w-sm leading-tight inline-block">
                        <template v-if="$page.props.auth.user.role === 'admin'">
                            *Beberapa aksi tidak tersedia karena asesi yang dipilih memiliki status yang beragam, atau tidak memenuhi syarat (contoh: sudah memiliki asesor/status akhir).
                        </template>
                        <template v-else>
                            *Beberapa aksi tidak tersedia. Pastikan berkas asesi telah dinyatakan lengkap oleh Admin dan ditugaskan kepada Anda.
                        </template>
                    </span>
                </div>
                <div v-else></div>

                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <div class="w-full sm:w-[243px]">
                        <TextInput v-model="searchQuery" type="text" placeholder="Cari nama atau email..." />
                    </div>
                    <button @click="showFilterModal = true"
                        class="relative mt-1 inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-500 text-sm font-medium rounded-md text-gray-500 dark:text-gray-300 bg-white dark:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none transition ease-in-out duration-150">
                        <FunnelIcon class="w-4" />
                        <span
                            v-if="activeFilters.statusBerkasAdministrasi || activeFilters.asesor || activeFilters.statusFinalAsesi"
                            class="absolute -top-1 -right-1 h-2 w-2 bg-blue-500 rounded-full"></span>
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full ">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-2 py-3 text-left">
                                <Checkbox id="checkbox-select-all" v-model:checked="isSelectAll" />
                            </th>
                            <th scope="col"
                                class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                No
                            </th>
                            <th scope="col"
                                class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Nama Asesi
                            </th>
                            <th scope="col"
                                class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status Berkas Administrasi
                            </th>
                            <th scope="col"
                                class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Asesor
                            </th>
                            <th scope="col"
                                class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status Final Asesi
                            </th>
                            <th scope="col"
                                class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Aksi
                            </th>

                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 ">
                        <tr v-if="filteredAsesis.length > 0" v-for="(asesi, index) in filteredAsesis" :key="asesi.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-2 py-4 whitespace-nowrap">
                                <Checkbox :id="`checkbox-asesi-${asesi.id}`" v-model:checked="selectedAsesis"
                                    :value="asesi.id" />
                            </td>
                            <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ index + 1 }}
                            </td>
                            <td
                                class="px-2 py-4 flex flex-col gap-1 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ asesi.student.user.name ?? 'Nama Tidak Tersedia' }}
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ asesi.student.user.email }}
                                </div>
                            </td>
                            <td class="px-2 py-4 whitespace-nowrap text-sm">
                                <StatusBadge :variant="getStatusBerkasAdministrasi(asesi.status_berkas).variant">
                                    {{ getStatusBerkasAdministrasi(asesi.status_berkas).text }}
                                </StatusBadge>
                            </td>
                            <td class="px-2 py-4 whitespace-nowrap text-sm">
                                <span v-if="asesi.asesor" class="flex flex-col gap-1 font-medium text-gray-900 dark:text-gray-100">
                                    {{ asesi.asesor.user?.name }}
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ asesi.asesor.user?.email }}</div>
                                </span>
                                <StatusBadge v-else variant="neutral">
                                    Belum Ditetapkan
                                </StatusBadge>
                            </td>
                            <td class="px-2 py-4 whitespace-nowrap text-sm flex flex-col gap-1 items-start">
                                <StatusBadge :variant="getStatusFinalAsesi(asesi.status_final).variant">
                                    {{ getStatusFinalAsesi(asesi.status_final).text }}
                                </StatusBadge>
                                <template v-if="isAdmin && asesi.status_final === 'kompeten'">
                                    <span v-if="asesi.sertifikat" class="text-xs text-green-600 dark:text-green-400 font-medium">Sertifikat sudah terbit</span>
                                    <span v-else class="text-xs text-yellow-600 dark:text-yellow-400 font-medium">Sertifikat belum terbit</span>
                                </template>
                            </td>
                            <td class="px-2 py-4 whitespace-nowrap text-sm font-medium">
                                <SeeButton
                                    :href="route('admin.sertifikasi.pendaftar.show', [props.sertification.id, asesi.id])">
                                    Detail
                                </SeeButton>
                            </td>
                        </tr>
                        <tr v-else>
                            <td colspan="7" class="px-2 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                <template v-if="activeFilters.statusBerkasAdministrasi || activeFilters.statusFinalAsesi || activeFilters.asesor">
                                    Tidak ada data yang cocok dengan filter yang dipilih.
                                </template>
                                <template v-else-if="isAsesor">
                                    Belum ada asesi yang ditugaskan kepada Anda. Terdapat <span class="font-bold text-blue-600 dark:text-blue-400">{{ props.unassignedCount }}</span> asesi yang belum diassign ke asesor pada sertifikasi ini.
                                </template>
                                <template v-else>
                                    Tidak ada data pendaftar untuk skema ini.
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <Modal :show="showFilterModal" @close="showFilterModal = false">
            <div class="flex justify-end p-2">
                <button @click="closeFilterModal">
                    <X class="w-4 dark:text-white" />
                </button>
            </div>
            <div class="p-6">
                <div class="flex flex-col gap-4">
                    <SelectInput id="statusBerkasAdministrasi" label="Status Berkas Asesi"
                        v-model="filtersForm.statusBerkasAdministrasi"
                        :options="[{ value: '', text: 'Semua' }, ...statusBerkasAdministrasiOptions]" />
                    <SelectInput id="statusFinalAsesi" label="Status Final Asesi" v-model="filtersForm.statusFinalAsesi"
                        :options="[{ value: '', text: 'Semua' }, ...statusFinalAsesiOptions]" />
                    <SelectInput v-if="!isAsesor" id="asesor-filter" label="Asesor" v-model="filtersForm.asesor"
                        :options="asesorFilterOptions" />
                </div>
                <div class="my-4 border-t border-gray-200 dark:border-gray-600"></div>
                <div class=" flex gap-3">
                    <SecondaryButton @click="resetFilters"> Reset </SecondaryButton>
                    <PrimaryButton @click="applyFilters">Apply Filter</PrimaryButton>
                </div>
            </div>
        </Modal>

        <Modal :show="showBulkActionModal" @close="showBulkActionModal = false">
            <div class="flex justify-end p-2">
                <button @click="showBulkActionModal = false">
                    <X class="w-4 dark:text-white" />
                </button>
            </div>
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Update Bulk ({{ selectedAsesis.length }} asesi)
                </h3>

                <form @submit.prevent="submitBulk">
                    <div class="flex flex-col gap-4">
                        <template v-if="bulkType === 'berkas'">
                            <SelectInput label="Status Berkas Administrasi" v-model="bulkForm.status_berkas"
                                :options="statusBerkasAdministrasiOptions" />
                            <div v-if="bulkForm.status_berkas === 'perlu_perbaikan_berkas'">
                                <InputLabel for="catatan_perbaikan_bulk" value="Catatan Perbaikan" />
                                <TextInput id="catatan_perbaikan_bulk" class="mt-1 block w-full"
                                    v-model="bulkForm.catatan_perbaikan" />
                            </div>
                        </template>
                        <template v-if="bulkType === 'final'">
                            <SelectInput label="Status Final Asesi" v-model="bulkForm.status_final"
                                :options="statusFinalAsesiOptions" />
                        </template>
                        <template v-if="bulkType === 'assign_asesor'">
                            <SelectInput label="Asesor Penguji" v-model="bulkForm.asesor_id" :options="asesorOptions" />
                        </template>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <SecondaryButton @click="showBulkActionModal = false"> Batal </SecondaryButton>
                        <PrimaryButton type="submit" :disabled="bulkForm.processing">
                            Simpan Perubahan
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AdminLayout>
</template>