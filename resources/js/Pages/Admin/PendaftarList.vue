<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AdminSertifikasiMenu from "@/Components/AdminSertifikasiMenu.vue";
import CustomHeader from '@/Components/CustomHeader.vue';
import TextInput from '@/Components/Input/TextInput.vue';
import SmallLinkButton from "@/Components/SmallLinkButton.vue";
import { MoveRight, FunnelIcon, X } from 'lucide-vue-next';
import { ref, computed, watch, reactive } from 'vue';
import PrimaryButton from '@/Components/Button/PrimaryButton.vue';
import SecondaryButton from '@/Components/Button/SecondaryButton.vue';
import SelectInput from '@/Components/Input/SelectInput.vue';
import InputLabel from '@/Components/Input/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import Checkbox from '@/Components/Input/Checkbox.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    sertification: Object,
});

const selectedAsesis = ref([]);

const getStatusBerkasAdministrasi = (status) => {
    const data = {
        'menunggu_verifikasi_admin': {
            class: 'bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100',
            text: 'Menunggu Verifikasi Admin'
        },
        'perlu_perbaikan_berkas': {
            class: 'bg-amber-100 text-amber-800 dark:bg-amber-700 dark:text-amber-100',
            text: 'Perlu Perbaikan Berkas'
        },
        'sudah_lengkap': {
            class: 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100',
            text: 'Sudah Lengkap'
        },
    };
    return data[status] || {
        class: 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200',
        text: status
    };
};

const getStatusAksesMenuAsesmen = (status) => {
    const data = {
        'belum_diberikan': {
            class: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100',
            text: 'Belum Diberikan'
        },
        'diberikan': {
            class: 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100',
            text: 'Diberikan'
        },
    };
    return data[status] || {
        class: 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200',
        text: status
    };
};

const getStatusFinalAsesi = (status) => {
    const data = {
        'belum_ditetapkan': {
            class: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100',
            text: 'Belum Ditetapkan'
        },
        'belum_kompeten': {
            class: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100',
            text: 'Belum Kompeten'
        },
        'kompeten': {
            class: 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100',
            text: 'Kompeten'
        },
    };
    return data[status] || {
        class: 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200',
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
const statusAksesMenuAsesmenOptions = [
    { value: 'belum_diberikan', text: 'Belum Diberikan' },
    { value: 'diberikan', text: 'Diberikan' },
];
const statusFinalAsesiOptions = [
    { value: 'belum_ditentukan', text: 'Belum Ditentukan' },
    { value: 'belum_kompeten', text: 'Belum Kompeten' },
    { value: 'kompeten', text: 'Kompeten' },
];

const filtersForm = ref({
    statusBerkasAdministrasi: '',
    statusAksesMenuAsesmen: '',
    statusFinalAsesi: '',
});

const activeFilters = ref({
    statusBerkasAdministrasi: '',
    statusAksesMenuAsesmen: '',
    statusFinalAsesi: '',
});

const applyFilters = () => {
    activeFilters.value = { ...filtersForm.value };
    showFilterModal.value = false;
};

const resetFilters = () => {
    filtersForm.value = { statusBerkasAdministrasi: '', statusAksesMenuAsesmen: '', statusFinalAsesi: '' };
    activeFilters.value = { statusBerkasAdministrasi: '', statusAksesMenuAsesmen: '', statusFinalAsesi: '' };
    // showFilterModal.value = false; // Optional: close on reset or keep open
};

const closeFilterModal = () => {
    showFilterModal.value = false;
    // Reset form to currently active filters to discard unsaved changes
    filtersForm.value = { ...activeFilters.value };
};

const filteredAsesis = computed(() => {
    let result = props.sertification.asesis;

    // Filter by Search Query (Name or Email)
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
    if (activeFilters.value.statusAksesMenuAsesmen) {
        result = result.filter(asesi => asesi.status_akses_asesmen === activeFilters.value.statusAksesMenuAsesmen);
    }
    if (activeFilters.value.statusFinalAsesi) {
        result = result.filter(asesi => asesi.status_final === activeFilters.value.statusFinalAsesi);
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
const bulkType = ref(''); // 'berkas', 'akses', 'final'
const bulkForm = useForm({
    asesi_ids: [],
    status_berkas: '',
    status_akses_asesmen: '',
    status_final: '',
    catatan_perbaikan: '',
});

const openBulkModal = (type) => {
    bulkForm.reset('status_berkas', 'status_akses_asesmen', 'status_final', 'catatan_perbaikan');
    bulkType.value = type;
    bulkForm.asesi_ids = selectedAsesis.value;
    showBulkActionModal.value = true;
};

const submitBulk = () => {
    let routeName = '';
    if (bulkType.value === 'berkas') routeName = 'admin.sertifikasi.pendaftar.update-status-berkas-bulk';
    if (bulkType.value === 'akses') routeName = 'admin.sertifikasi.pendaftar.update-akses-asesmen-bulk';
    if (bulkType.value === 'final') routeName = 'admin.sertifikasi.pendaftar.update-status-final-bulk';

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
        <CustomHeader judul="Daftar Peserta" />
        <AdminSertifikasiMenu :sertification-id="props.sertification.id" />

        <div class="p-6 bg-white dark:bg-gray-800 shadow-xl rounded-lg ">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                <div v-if="selectedAsesis.length > 0" class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        {{ selectedAsesis.length }} dipilih:
                    </span>
                    <SecondaryButton @click="openBulkModal('berkas')" class="!py-1 !px-2 !text-[10px]">Update Berkas
                    </SecondaryButton>
                    <SecondaryButton @click="openBulkModal('akses')" class="!py-1 !px-2 !text-[10px]">Update Akses
                    </SecondaryButton>
                    <SecondaryButton @click="openBulkModal('final')" class="!py-1 !px-2 !text-[10px]">Status Final
                    </SecondaryButton>
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
                            v-if="activeFilters.statusBerkasAdministrasi || activeFilters.statusAksesMenuAsesmen || activeFilters.statusFinalAsesi"
                            class="absolute -top-1 -right-1 h-2 w-2 bg-blue-500 rounded-full"></span>
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full ">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left">
                                <Checkbox v-model:checked="isSelectAll" />
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                No
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Nama Asesi
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status Berkas Administrasi
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Akses Menu Asesmen
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status Final Asesi
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Aksi
                            </th>

                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 ">

                        <tr v-if="filteredAsesis.length > 0" v-for="(asesi, index) in filteredAsesis" :key="asesi.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <Checkbox v-model:checked="selectedAsesis" :value="asesi.id" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ index + 1 }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ asesi.student.user.name ?? 'Nama Tidak Tersedia' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span
                                    :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusBerkasAdministrasi(asesi.status_berkas).class]">
                                    {{ getStatusBerkasAdministrasi(asesi.status_berkas).text }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span
                                    :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusAksesMenuAsesmen(asesi.status_akses_asesmen).class]">
                                    {{ getStatusAksesMenuAsesmen(asesi.status_akses_asesmen).text }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span
                                    :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusFinalAsesi(asesi.status_final).class]">
                                    {{ getStatusFinalAsesi(asesi.status_final).text }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <SmallLinkButton
                                    :href="route('admin.sertifikasi.pendaftar.show', [props.sertification.id, asesi.id])">
                                    Detail
                                </SmallLinkButton>
                            </td>
                        </tr>
                        <tr v-else>
                            <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                Tidak ada data pendaftar untuk skema ini.
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
                    <SelectInput id="statusAksesMenuAsesmen" label="Status Akses Menu Asesmen"
                        v-model="filtersForm.statusAksesMenuAsesmen"
                        :options="[{ value: '', text: 'Semua' }, ...statusAksesMenuAsesmenOptions]" />
                    <SelectInput id="statusFinalAsesi" label="Status Final Asesi" v-model="filtersForm.statusFinalAsesi"
                        :options="[{ value: '', text: 'Semua' }, ...statusFinalAsesiOptions]" />
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

                        <template v-if="bulkType === 'akses'">
                            <SelectInput label="Akses Menu Asesmen" v-model="bulkForm.status_akses_asesmen"
                                :options="statusAksesMenuAsesmenOptions" />
                        </template>

                        <template v-if="bulkType === 'final'">
                            <SelectInput label="Status Final Asesi" v-model="bulkForm.status_final"
                                :options="statusFinalAsesiOptions" />
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