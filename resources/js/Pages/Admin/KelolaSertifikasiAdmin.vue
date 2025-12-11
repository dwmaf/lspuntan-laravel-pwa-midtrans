<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import CustomHeader from '@/Components/CustomHeader.vue';
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Pagination from "@/Components/Pagination.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryLinkButton from "@/Components/PrimaryLinkButton.vue";
import TextInput from "@/Components/TextInput.vue";
import SelectInput from "@/Components/SelectInput.vue";
import Modal from "@/Components/Modal.vue";
import NumberInput from "@/Components/NumberInput.vue";
import DateInput from "@/Components/DateInput.vue";
import { useForm, usePage, Link, router } from "@inertiajs/vue3";
import { ref, computed, watch, reactive } from "vue";
import { MapPin, DollarSign, CalendarRange, BookOpen, FunnelIcon, X, Users } from "lucide-vue-next";
import { IconChalkboardTeacher, IconPointFilled } from "@tabler/icons-vue";
import Multiselect from "@/Components/MultiSelect.vue";

const props = defineProps({
    sertifications_berlangsung: Array,
    sertifications_selesai: Object,
    asesors: Array,
    skemas: Array,
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
    deadline_bayar: "",
    tuk: "",
    biaya: "",
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
const formatDate = (dateString) => {
    if (!dateString) return null;
    const date = new Date(dateString);
    const options = { day: 'numeric', month: 'long', year: 'numeric' };
    return new Intl.DateTimeFormat('id-ID', options).format(date);
};
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
        <hr class="border-gray-200 dark:border-gray-700 mb-2" />


        <div>
            <div v-show="tab === 'berlangsung'">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div v-if="props.sertifications_berlangsung.length > 0"
                        v-for="sert in props.sertifications_berlangsung" :key="sert.id"
                        class="bg-white p-6 rounded-lg dark:bg-gray-800">
                        <div class="flex items-center gap-2">
                            <BookOpen class="shrink-0 text-gray-700 dark:text-gray-200" />
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                                {{ sert.skema.nama_skema }}
                            </h3>
                        </div>
                        <div v-for="(asesor, index) in sert.asesors" :key="asesor.id"
                            class="flex items-center mt-4 gap-2">
                            <IconChalkboardTeacher class="w-4 h-4 text-gray-700 dark:text-gray-200" />
                            <p class=" text-gray-600 text-sm dark:text-gray-200">
                                <span class="font-semibold">Asesor {{ index + 1 }} : {{ asesor.user.name }}</span>
                            </p>
                        </div>

                        <div class="flex items-center mt-4 gap-2">
                            <CalendarRange class="shrink-0 w-4 h-4 text-gray-700 dark:text-gray-200" />
                            <p class=" text-gray-600 text-sm dark:text-gray-200">
                                <span class="font-semibold">
                                    Pendaftaran:
                                </span>
                                {{ formatDate(sert.tgl_apply_dibuka) }}
                                &ndash;
                                {{ formatDate(sert.tgl_apply_ditutup) }}
                            </p>
                        </div>
                        <div class="flex items-center mt-4">
                            <DollarSign class="w-4 h-4 text-gray-700 dark:text-gray-200" />
                            <p class="ml-2 text-gray-600 text-sm dark:text-gray-200">
                                <span class="font-semibold">
                                    Biaya: Rp
                                </span>
                                {{ new Intl.NumberFormat("id-ID").format(sert.biaya) }}
                            </p>
                        </div>
                        <div class="flex items-center mt-4">
                            <MapPin class="w-4 h-4 text-gray-700 dark:text-gray-200" />
                            <p class="ml-2 text-gray-600 text-sm dark:text-gray-200">
                                <span class="font-semibold">
                                    TUK:
                                </span>
                                {{ sert.tuk }}
                            </p>
                        </div>

                        <div class="flex items-center mt-4">
                            <div
                                class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-800">
                                <Users class="w-3.5 h-3.5" />
                                <span class="text-xs font-semibold">{{ sert.asesis_count }} Asesi Terdaftar</span>
                            </div>
                        </div>
                        <div class="mt-4">
                            <PrimaryLinkButton :href="route(
                                'admin.kelolasertifikasi.show',
                                sert.id
                            )
                                ">Detail
                            </PrimaryLinkButton>
                        </div>
                    </div>
                    <p v-else class="text-gray-500 dark:text-gray-400 col-span-2">
                        Tidak ada sertifikasi yang sedang berlangsung.
                    </p>
                </div>
            </div>

            <div v-show="tab === 'selesai'">
                <div class="flex justify-end items-center gap-2 mb-4">

                    <button data-cy="filter-trigger-button" @click="openFilterModal"
                        class="relative mt-1 inline-flex items-center px-3 py-3 border border-gray-300 dark:border-gray-500 text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-300 bg-white dark:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none transition ease-in-out duration-150">
                        <FunnelIcon class="w-4 h-4" />
                        <span v-if="hasActiveFilters"
                            class="absolute -top-1 -right-1 h-2 w-2 bg-blue-500 rounded-full"></span>
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div v-for="sert in sertifications_selesai.data" :key="sert.id"
                        class="bg-white p-6 rounded-lg dark:bg-gray-800">
                        <div class="flex items-center gap-2">
                            <BookOpen class="shrink-0 text-gray-700 dark:text-gray-200" />
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                                {{ sert.skema.nama_skema }}
                            </h3>
                        </div>
                        <div v-for="(asesor, index) in sert.asesors" :key="asesor.id"
                            class="flex items-center mt-4 gap-2">
                            <IconChalkboardTeacher class="w-4 h-4 text-gray-700 dark:text-gray-200" />
                            <p class=" text-gray-600 text-sm dark:text-gray-200">
                                <span class="font-semibold">Asesor {{ index + 1 }} : {{ asesor.user.name }}</span>
                            </p>
                        </div>
                        <div class="flex items-center mt-4 gap-2">
                            <CalendarRange class="shrink-0 w-4 h-4 text-gray-700 dark:text-gray-200" />
                            <p class=" text-gray-600 text-sm dark:text-gray-200">
                                Pendaftaran:
                                {{ formatDate(sert.tgl_apply_dibuka) }} &ndash;
                                {{ formatDate(sert.tgl_apply_ditutup) }}
                            </p>
                        </div>
                        <div class="flex items-center mt-4 gap-2">
                            <DollarSign class="w-4 h-4 text-gray-700 dark:text-gray-200" />
                            <p class=" text-gray-600 text-sm dark:text-gray-200">
                                Biaya: Rp
                                {{new Intl.NumberFormat("id-ID").format(sert.biaya)}}
                            </p>
                        </div>
                        <div class="flex items-center mt-4">
                            <MapPin class="w-4 h-4 text-gray-700 dark:text-gray-200" />
                            <p class="ml-2 text-gray-600 text-sm dark:text-gray-200">
                                <span class="font-semibold">TUK:</span>
                                {{ sert.tuk }}
                            </p>
                        </div>
                        <div class="flex items-center mt-4">
                            <div
                                class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-800">
                                <Users class="w-3.5 h-3.5" />
                                <span class="text-xs font-semibold">{{ sert.asesis_count }} Asesi Terdaftar</span>
                            </div>
                        </div>
                        <div class="mt-4">
                            <PrimaryLinkButton :href="route('admin.kelolasertifikasi.show', sert.id)">
                                Detail
                            </PrimaryLinkButton>
                        </div>
                    </div>

                    <p v-if="sertifications_selesai.data.length === 0"
                        class="text-gray-500 dark:text-gray-400 col-span-2">
                        Tidak ada riwayat sertifikasi untuk filter yang
                        dipilih.
                    </p>
                </div>
                <div class="mt-4 flex justify-between items-center">
                    <span v-if="sertifications_selesai.total > 0"
                        class="text-sm text-gray-700 dark:text-gray-400 hidden lg:flex">
                        Menampilkan {{ sertifications_selesai.from }} sampai {{ sertifications_selesai.to }} dari {{
                            sertifications_selesai.total }} hasil
                    </span>
                    <span v-else></span>
                    <Pagination :links="sertifications_selesai.links" />
                </div>
            </div>


            <div v-show="tab === 'mulai'" class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                    Mulai Sertifikasi
                </h2>
                <form @submit.prevent="submit" class="mt-4 flex flex-col gap-4">
                    <div>
                        <InputLabel value="Pilih Skema Sertifikasi:" required />
                        <select v-model="form.skema_id" required
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option value="" disabled>-- Silahkan pilih skema --</option>
                            <option v-for="skema in props.skemas" :key="skema.id" :value="skema.id">
                                {{ skema.nama_skema }}
                            </option>
                        </select>
                        <InputError :message="form.errors.skema_id" />
                    </div>
                    <div v-if="form.skema_id" class="relative">
                        <InputLabel value="Pilih Asesor (bisa lebih dari 1)" required />
                        <Multiselect v-model="form.asesor_ids" :options="availableAsesors" :multiple="true"
                            placeholder="Cari atau pilih asesor" label-prop="name" value-prop="id"
                            :class="{ 'border-red-500': form.errors.asesor_ids }">
                        </Multiselect>
                        <InputError :message="form.errors.asesor_ids" />
                    </div>

                    <div>
                        <InputLabel value="Tanggal Daftar Dibuka" required />
                        <TextInput type="date" v-model="form.tgl_apply_dibuka" required />
                        <InputError :message="form.errors.tgl_apply_dibuka" />
                    </div>
                    <div id="tanggal_apply_ditutup">
                        <InputLabel value="Tanggal Daftar Ditutup" required />
                        <TextInput type="date" v-model="form.tgl_apply_ditutup" required />
                        <InputError :message="form.errors.tgl_apply_ditutup" />
                    </div>
                    <div id="tanggal_bayar_ditutup">
                        <InputLabel value="Tanggal Bayar Ditutup" required />
                        <DateInput v-model="form.deadline_bayar" required />
                        <InputError :message="form.errors.deadline_bayar" />
                    </div>
                    <div id="biaya_sertifikasi">
                        <InputLabel value="Biaya Sertifikasi" required />
                        <p v-if="formattedHarga" class="text-sm font-medium text-gray-800 dark:text-gray-400">
                            {{ formattedHarga }}
                        </p>
                        <NumberInput min="0" v-model="form.biaya" required />
                        <InputError :message="form.errors.biaya" />
                    </div>
                    <div id="tuk">
                        <InputLabel value="Tempat Uji Sertifikasi" required />
                        <TextInput type="text" v-model="form.tuk" required />
                        <InputError :message="form.errors.tuk" />
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
                    <InputLabel value="Dari" />
                    <TextInput v-model="filtersForm.date_from" type="date" class="w-full" />
                    <InputLabel value="Ke" />
                    <TextInput v-model="filtersForm.date_to" type="date" class="w-full" />
                </div>
            </div>
            <div>
                <InputLabel value="Skema" />
                <SelectInput v-model="filtersForm.skema" :options="skemaOptions" />
            </div>

            <div>
                <InputLabel value="Asesor" />
                <SelectInput v-model="filtersForm.asesor" :options="asesorOptions" />
            </div>
            <div class="my-4 border-t border-gray-200 dark:border-gray-600"></div>
            <div class="flex gap-3">
                <SecondaryButton @click="resetFilters"> Reset </SecondaryButton>
                <PrimaryButton @click="applyFilters">Apply Filter</PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
