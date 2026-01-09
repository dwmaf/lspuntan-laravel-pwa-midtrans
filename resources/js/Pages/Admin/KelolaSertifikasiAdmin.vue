<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import CustomHeader from '@/Components/CustomHeader.vue';
import SertifikasiTable from '@/Components/SertifikasiTable.vue';
import InputLabel from "@/Components/Input/InputLabel.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import Pagination from "@/Components/Pagination.vue";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue";
import PrimaryLinkButton from "@/Components/PrimaryLinkButton.vue";
import TextInput from "@/Components/Input/TextInput.vue";
import SelectInput from "@/Components/Input/SelectInput.vue";
import Modal from "@/Components/Modal.vue";
import NumberInput from "@/Components/Input/NumberInput.vue";
import DateInput from "@/Components/Input/DateInput.vue";
import { useForm, usePage, Link, router } from "@inertiajs/vue3";
import { ref, computed, watch, reactive } from "vue";
import { FunnelIcon, X } from "lucide-vue-next";
import Multiselect from "@/Components/Input/MultiSelect.vue";

const props = defineProps({
    sertifications_berlangsung: Array,
    sertifications_selesai: Object,
    asesors: Array,
    skemas: Array,
    activeSkemas: Array,
    filters: Object,
    errors: Object,
});
const filtersForm = reactive({
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
    asesor: props.filters.asesor || '',
    skema: props.filters.skema || '',
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

const asesorOptions = computed(() =>
    props.asesors.map(asesor => ({ value: asesor.id, text: asesor.user.name }))
);
const skemaOptions = computed(() =>
    props.skemas.map(skema => ({ value: skema.id, text: skema.nama_skema }))
);
const activeSkemaOptions = computed(() =>
    props.activeSkemas.map(skema => ({ value: skema.id, text: skema.nama_skema }))
);
const tab = ref(props.filters.tab || "berlangsung");

watch(tab, (value) => {
    router.get(route('admin.kelolasertifikasi.index'), { ...filtersForm, tab: value }, {
        preserveState: true,
        replace: true,
    });
});
const applyFilters = () => {
    router.get(route('admin.kelolasertifikasi.index'), { ...filtersForm, tab: tab.value }, {
        preserveState: true,
        replace: true,
    });
    closeFilterModal();
};
const resetFilters = () => {
    Object.keys(filtersForm).forEach(key => filtersForm[key] = '');
    applyFilters();
};

const form = useForm({
    skema_id: "",
    asesor_ids: [],
    tgl_apply_dibuka: "",
    tgl_apply_ditutup: "",
    tuk: "",
    biaya: "",
    no_rek: "",
    bank: "",
    atas_nama_rek: "",
    tgl_asesmen_mulai: "",
    tgl_asesmen_selesai: "",
});
form.transform((data) => {
    return {
        ...data,
    };
});

const availableAsesors = computed(() => {
    if (!form.skema_id) {
        return [];
    }
    const filtered = props.asesors.filter(asesor =>
        asesor.skemas.some(skema => skema.id == form.skema_id)
    );

    return filtered.map(asesor => ({
        id: asesor.id,
        name: `${asesor.user.name} (Telah mensertifikasi ${asesor.sertifications_count} kali)`
    }));
});
watch(() => form.skema_id, (newSkemaId) => {
    form.asesor_ids = [];
});
const formattedHarga = computed(() => {
    if (!form.biaya) return '';
    const number = parseFloat(form.biaya);
    if (isNaN(number)) return '';
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(number);
});

const submit = () => {
    form.post(route("admin.kelolasertifikasi.store"), {
        onSuccess: () => form.reset(),
    });
};

</script>

<template>
    <AdminLayout>
        <CustomHeader judul="Manajemen Sertifikasi" />
        <nav class="flex flex-wrap space-x-4 mt-1" aria-label="Tabs">
            <div>
                <button @click="tab = 'mulai'"
                    class="flex items-center gap-2 px-4 py-3 font-semibold text-sm hover:bg-gray-300 hover:dark:bg-gray-700 rounded-t-md dark:text-white text-gray-600 cursor-pointer">
                    Mulai Sertifikasi
                </button>
                <div style="margin-top: -4px" v-show="tab === 'mulai'"
                    class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
            </div>
            <div>
                <button @click="tab = 'berlangsung'"
                    class="flex items-center gap-2 px-4 py-3 font-semibold text-sm hover:bg-gray-300 hover:dark:bg-gray-700 rounded-t-md dark:text-white text-gray-600 cursor-pointer">
                    <span>
                        Sertifikasi Berlangsung
                    </span>
                    <!-- <span class="bg-gray-300 dark:bg-gray-700 rounded-full px-2">
                        {{ props.sertifications_berlangsung.length }}
                    </span> -->
                </button>
                <div style="margin-top: -4px" v-show="tab === 'berlangsung'"
                    class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
            </div>
            <div>
                <button @click="tab = 'selesai'"
                    class="flex items-center gap-2 px-4 py-3 font-semibold text-sm hover:bg-gray-300 hover:dark:bg-gray-700 rounded-t-md dark:text-white text-gray-600 cursor-pointer">
                    <span>
                        Riwayat Sertifikasi
                    </span>
                    <!-- <span class="bg-gray-300 dark:bg-gray-700 rounded-full px-2">
                        {{ filteredSertificationsSelesai.length }}
                    </span> -->
                </button>
                <div style="margin-top: -4px" v-show="tab === 'selesai'"
                    class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
            </div>
        </nav>
        <hr class="border-gray-200 dark:border-gray-700 mb-4" />


        <div>
            <div v-show="tab === 'berlangsung'">
                <SertifikasiTable :sertifications="sertifications_berlangsung"
                    empty-message="Tidak ada sertifikasi yang sedang berlangsung." />
            </div>

            <div v-show="tab === 'selesai'">

                <SertifikasiTable :sertifications="sertifications_selesai.data"
                    empty-message="Tidak ada riwayat sertifikasi untuk filter yang dipilih.">
                    <template #filter>
                        <div class="flex justify-end items-center gap-2 mb-4">
                            <button data-cy="filter-trigger-button" @click="openFilterModal"
                                class="relative mt-1 inline-flex items-center px-3 py-3 border border-gray-300 dark:border-gray-500 text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-300 bg-white dark:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none transition ease-in-out duration-150">
                                <FunnelIcon class="w-4 h-4" />
                                <span v-if="hasActiveFilters"
                                    class="absolute -top-1 -right-1 h-2 w-2 bg-blue-500 rounded-full"></span>
                            </button>
                        </div>
                    </template>
                    <template #pagination>
                        <div class="mt-4 flex justify-between items-center">
                            <span v-if="sertifications_selesai.total > 0"
                                class="text-sm text-gray-700 dark:text-gray-400 hidden lg:flex">
                                Menampilkan {{ sertifications_selesai.from }} sampai {{ sertifications_selesai.to }}
                                dari {{
                                    sertifications_selesai.total }} hasil
                            </span>
                            <span v-else></span>
                            <Pagination :links="sertifications_selesai.links" />
                        </div>
                    </template>
                </SertifikasiTable>
            </div>

            <div v-show="tab === 'mulai'" class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                    Mulai Sertifikasi
                </h2>
                <form @submit.prevent="submit" class="mt-4 flex flex-col gap-4">

                    <SelectInput id="skema_id" label="Pilih Skema Sertifikasi:" v-model="form.skema_id"
                        :options="activeSkemaOptions" :error="form.errors.skema_id" required
                        placeholder="-- Pilih Skema Sertifikasi --" />
                    <Multiselect v-if="form.skema_id" id="asesor_ids" label="Pilih Asesor (bisa lebih dari 1)"
                        v-model="form.asesor_ids" :options="availableAsesors" :multiple="true"
                        placeholder="Cari atau pilih asesor" label-prop="name" value-prop="id"
                        :error="form.errors.asesor_ids" :class="{ 'border-red-500': form.errors.asesor_ids }"
                        required />
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <DateInput id="tgl_apply_dibuka" label="Tanggal Daftar Dibuka" v-model="form.tgl_apply_dibuka"
                            type="date" :error="form.errors.tgl_apply_dibuka" required />
                        <DateInput id="tgl_apply_ditutup" label="Tanggal Daftar Ditutup"
                            v-model="form.tgl_apply_ditutup" type="datetime-local"
                            :error="form.errors.tgl_apply_ditutup" required />
                        <DateInput id="tgl_asesmen_mulai" label="Tanggal Asesmen Dimulai"
                            v-model="form.tgl_asesmen_mulai" type="date" :error="form.errors.tgl_asesmen_mulai" />
                        <DateInput v-if="form.tgl_asesmen_mulai" id="tgl_asesmen_selesai"
                            label="Tanggal Asesmen Selesai" v-model="form.tgl_asesmen_selesai" type="date"
                            :error="form.errors.tgl_asesmen_selesai" />
                        <TextInput id="tuk" label="Tempat Uji Sertifikasi" v-model="form.tuk" type="text"
                            placeholder="Contoh: Gedung A Ruangan 12" :error="form.errors.tuk" required />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <NumberInput id="biaya" label="Biaya Sertifikasi" v-model="form.biaya" min="0"
                            placeholder="Contoh: 100000" :formatted-value="formattedHarga" :error="form.errors.biaya"
                            required />
                        <TextInput id="bank" label="Nama Bank / E-Wallet" v-model="form.bank"
                            placeholder="Contoh: Bank BSI, GoPay" :error="form.errors.bank" required />
                        <TextInput id="no_rek" label="Nomor Rekening / Virtual Account" v-model="form.no_rek"
                            placeholder="Contoh: 1234567890" :error="form.errors.no_rek" required />
                        <TextInput id="atas_nama_rek" label="Atas Nama Rekening" v-model="form.atas_nama_rek"
                            placeholder="Contoh: LSP Untan" :error="form.errors.atas_nama_rek" required />
                    </div>

                    <div class="flex">
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Mulai
                        </PrimaryButton>
                    </div>
                </form>
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
                    <TextInput id="date_from" label="Dari" v-model="filtersForm.date_from" type="date" class="w-full" />
                    <TextInput id="date_to" label="Ke" v-model="filtersForm.date_to" type="date" class="w-full" />
                </div>
            </div>
            <SelectInput id="skema-filter" label="Skema" v-model="filtersForm.skema" :options="skemaOptions" />
            <SelectInput id="asesor-filter" label="Asesor" v-model="filtersForm.asesor" :options="asesorOptions" />
            <div class="my-4 border-t border-gray-200 dark:border-gray-600"></div>
            <div class="flex gap-3">
                <SecondaryButton @click="resetFilters"> Reset </SecondaryButton>
                <PrimaryButton @click="applyFilters">Apply Filter</PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
