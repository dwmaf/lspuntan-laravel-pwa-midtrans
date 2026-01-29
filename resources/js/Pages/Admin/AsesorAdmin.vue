<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue";
import TextInput from "@/Components/Input/TextInput.vue";
import DateInput from "@/Components/Input/DateInput.vue";
import AddButton from "@/Components/Button/AddButton.vue";
import EditButton from "@/Components/Button/EditButton.vue";
import DeleteButton from "@/Components/Button/DeleteButton.vue";
import CustomHeader from "@/Components/CustomHeader.vue";
import Pagination from "@/Components/Pagination.vue";
import SelectInput from "@/Components/Input/SelectInput.vue";
import Multiselect from "@/Components/Input/MultiSelect.vue";
import Alert from "@/Components/Alert.vue";
import { useForm, usePage, router } from "@inertiajs/vue3";
import { ref, computed, onMounted, onUnmounted, reactive, watch } from "vue";
import { MoveRight, FunnelIcon, X } from 'lucide-vue-next';

const props = defineProps({
    asesors: Object,
    skemas: Array,
    filters: Object,
});
const filtersForm = reactive({
    skema: props.filters.skema || '',
    trashed: props.filters.trashed || '',
    search: props.filters.search || '',
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
        router.get(route('admin.asesor.index'), filtersForm, {
            preserveState: true,
            replace: true,
        });
    }, 500);
});

const skemaOptions = computed(() =>
    [{ value: '', text: 'Semua' }, ...props.skemas.map(skema => ({ value: skema.id, text: skema.nama_skema }))]
);

const skemaMultiselectOptions = computed(() =>
    props.skemas.map(skema => ({ id: skema.id, name: skema.nama_skema }))
);

const trashedOptions = [
    { value: '', text: 'Hanya Aktif' },
    { value: 'with', text: 'Semua (Termasuk Sampah)' },
    { value: 'only', text: 'Hanya Sampah' },
];

const applyFilters = () => {
    router.get(route('admin.asesor.index'), filtersForm, {
        preserveState: true,
        replace: true,
    });
    closeFilterModal();
};
const resetFilters = () => {
    filtersForm.skema = '';
    filtersForm.trashed = '';
    filtersForm.search = '';
    applyFilters();
};
const formMode = ref('list');

const form = useForm({
    id: null,
    name: '',
    email: '',
    no_met: '',
    masa_berlaku_sertif_teknis: '',
    masa_berlaku_sertif_asesor: '',
    no_tlp_hp: '',
    selectedSkemas: [],
    _method: 'POST',
});


const showCreateForm = () => {
    form.reset();
    form._method = 'POST';
    formMode.value = 'create';
};


const showEditForm = (asesor) => {
    form.id = asesor.id;
    form.name = asesor.user.name;
    form.email = asesor.user.email;
    form.no_met = asesor.no_reg_met;
    form.masa_berlaku_sertif_teknis = asesor.masa_berlaku_sertif_teknis;
    form.masa_berlaku_sertif_asesor = asesor.masa_berlaku_sertif_asesor;
    form.no_tlp_hp = asesor.user.no_tlp_hp;
    form.selectedSkemas = asesor.skemas.map(s => s.id);
    form._method = 'PATCH';
    formMode.value = 'edit';
};


const backToList = () => {
    formMode.value = 'list';
    form.reset();
    form.clearErrors();
};

const save = () => {
    form.clearErrors('selectedSkemas');

    if (form.selectedSkemas.length === 0) {
        form.setError('selectedSkemas', 'Pilih minimal satu skema.');
        return;
    }
    if (formMode.value === 'create') {
        form.post(route('admin.asesor.store'), {
            onSuccess: () => backToList(),
        });
    } else if (formMode.value === 'edit') {
        form.post(route('admin.asesor.update', form.id), {
            onSuccess: () => backToList(),
        });
    }
};

const destroy = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus asesor ini?')) {
        router.delete(route('admin.asesor.destroy', id));
    }
};

const restore = (id) => {
    if (confirm('Apakah Anda yakin ingin memulihkan asesor ini?')) {
        router.patch(route('admin.asesor.restore', id));
    }
};

</script>

<template>
    <AdminLayout>
        <!-- Form Tambah/Edit -->
        <div v-if="formMode === 'create' || formMode === 'edit'"
            class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                {{ formMode === 'create' ? 'Tambah Asesor' : 'Edit Asesor' }}
            </h2>
            <form @submit.prevent="save" class="mt-4 flex flex-col gap-4">
                <Multiselect id="skema_ids" label="Pilih Skema" v-model="form.selectedSkemas"
                    :options="skemaMultiselectOptions" :multiple="true" placeholder="Cari atau pilih skema"
                    label-prop="name" value-prop="id" :error="form.errors.selectedSkemas" required />
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <TextInput id="name" label="Nama Lengkap" v-model="form.name" type="text" required
                        :error="form.errors.name" />
                    <TextInput id="email" label="Email" v-model="form.email" type="email" required
                        :error="form.errors.email" />
                    <TextInput id="no_met" label="No. MET" v-model="form.no_met" type="text" required
                        :error="form.errors.no_met" />
                    <DateInput id="masa_berlaku_sertif_teknis" label="Masa Berlaku Sertifikat Teknis"
                        v-model="form.masa_berlaku_sertif_teknis" type="date" required
                        :error="form.errors.masa_berlaku_sertif_teknis" />
                    <DateInput id="masa_berlaku_sertif_asesor" label="Masa Berlaku Sertifikat Asesor"
                        v-model="form.masa_berlaku_sertif_asesor" type="date" required
                        :error="form.errors.masa_berlaku_sertif_asesor" />
                    <TextInput id="no_tlp_hp" label="No. HP/WA" v-model="form.no_tlp_hp" type="text" required
                        :error="form.errors.no_tlp_hp" />
                </div>
                <div class="flex items-center gap-4">
                    <PrimaryButton :disabled="form.processing">Simpan</PrimaryButton>
                    <SecondaryButton type="button" @click="backToList">Batal</SecondaryButton>
                </div>
            </form>
        </div>

        <!-- Tampilan Daftar -->
        <div v-else class="flex flex-col">
            <CustomHeader judul="Manajemen Asesor">
                <div class="flex gap-2">
                    <a :href="route('admin.asesor.export')" target="_blank"
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                        Export Excel
                    </a>
                    <AddButton @click="showCreateForm">Tambah Asesor</AddButton>
                </div>
            </CustomHeader>
            <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <div class="flex justify-end items-center mb-6 gap-3">
                    <div class="w-[243px]">
                        <TextInput v-model="filtersForm.search" type="text" placeholder="Cari nama..." />
                    </div>
                    <button @click="openFilterModal"
                        class="relative mt-1 inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-500 text-sm font-medium rounded-md text-gray-500 dark:text-gray-300 bg-white dark:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none transition ease-in-out duration-150">
                        <FunnelIcon class="w-4" />
                        <span v-if="hasActiveFilters"
                            class="absolute -top-1 -right-1 h-2 w-2 bg-blue-500 rounded-full"></span>
                    </button>
                </div>
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    No</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Nama & Email</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Skema Sertifikasi</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Jumlah Sertifikasi</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="(asesor, index) in asesors.data" :key="asesor.id"
                                :class="{ 'bg-red-50 dark:bg-red-900/20': asesor.deleted_at }">
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">
                                    {{ index + asesors.from }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">
                                    <div class="font-medium">{{ asesor.user.name }}</div>
                                    <div class="text-xs text-gray-500">{{ asesor.user.email }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">
                                    <div class="flex flex-wrap gap-1">
                                        <span v-for="skema in asesor.skemas" :key="skema.id"
                                            class="inline-block rounded bg-gray-200 dark:bg-gray-700 px-2 py-1 text-xs font-medium">{{
                                                skema.nama_skema }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200">
                                    <div class="font-medium">{{ asesor.sertifications_count }} kali</div>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <div v-if="asesor.deleted_at" class="flex items-center justify-center space-x-2">
                                        <SecondaryButton @click="restore(asesor.id)"
                                            class="bg-green-100! text-green-800! border-green-300 hover:bg-green-200!">
                                            Restore
                                        </SecondaryButton>
                                    </div>
                                    <div v-else class="flex items-center justify-center space-x-2">
                                        <EditButton @click="showEditForm(asesor)">Edit</EditButton>
                                        <DeleteButton @click="destroy(asesor.id)">Hapus</DeleteButton>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="asesors.data.length === 0">
                                <td colspan="4" class="px-6 py-4 text-center">Tidak ada data asesor yang cocok dengan
                                    filter.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 flex justify-between items-center">
                    <span v-if="asesors.total > 0" class="text-sm text-gray-700 dark:text-gray-400 hidden lg:flex">
                        Menampilkan {{ asesors.from }} sampai {{ asesors.to }} dari {{ asesors.total }} hasil
                    </span>
                    <span v-else></span>
                    <Pagination :links="asesors.links" />
                </div>
            </div>
        </div>
        <Alert type="info" title="Ketentuan Penghapusan Data Asesor" class="mt-6">
            <ul class="list-disc list-inside space-y-1">
                <li>Asesor hanya bisa dihapus jika belum pernah terlibat dalam sertifikasi</li>
                <li>Asesor tidak bisa dihapus jika telah pernah terlibat dalam sertifikasi demi kebutuhan akuntabilitas
                    data dan audit</li>
            </ul>
        </Alert>
    </AdminLayout>
    <Modal :show="showFilterModal" @close="showFilterModal = false">
        <div class="flex justify-end p-2">
            <button @click="closeFilterModal">
                <X class="w-4 dark:text-white" />
            </button>
        </div>
        <div class="p-6 flex flex-col gap-4">
            <SelectInput id="skema-filter" label="Skema" v-model="filtersForm.skema" :options="skemaOptions" />
            <SelectInput id="trashed-filter" label="Status Data" v-model="filtersForm.trashed"
                :options="trashedOptions" />
            <div class="my-4 border-t border-gray-200 dark:border-gray-600"></div>
            <div class=" flex gap-3">
                <SecondaryButton @click="resetFilters"> Reset </SecondaryButton>
                <PrimaryButton @click="applyFilters">Apply Filter</PrimaryButton>
            </div>
        </div>
    </Modal>
</template>