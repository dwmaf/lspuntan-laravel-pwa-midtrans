<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import FilterDropdown from "@/Components/FilterDropdown.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryLinkButton from "@/Components/PrimaryLinkButton.vue";
import TextInput from "@/Components/TextInput.vue";
import NumberInput from "@/Components/NumberInput.vue";
import DateInput from "@/Components/DateInput.vue";
import { useForm, usePage, Link, router } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";
import { MapPin, DollarSign, CalendarRange, BookOpen } from "lucide-vue-next";
import { IconChalkboardTeacher, IconPointFilled } from "@tabler/icons-vue";
import Multiselect from 'vue-multiselect';

const props = defineProps({
    sertifications_berlangsung: Array,
    sertifications_selesai: Array,
    asesors: Array,
    skemas: Array,
    errors: Object,
});
const asesorFilter = ref('');
const skemaFilter = ref('');
const tahunFilter = ref('');
const asesorOptions = computed(() =>
    props.asesors.map(asesor => ({ value: asesor.id, text: asesor.user.name }))
);
const skemaOptions = computed(() =>
    props.skemas.map(skema => ({ value: skema.id, text: skema.nama_skema }))
);
const yearOptions = computed(() => {
    const currentYear = new Date().getFullYear();
    const startYear = 2025;
    const years = [];
    for (let year = startYear; year >= currentYear; year--) {
        years.push({ value: year, text: year.toString() });
    }
    return years;
});
const resetFilters = () => {
    asesorFilter.value = '';
    skemaFilter.value = '';
    tahunFilter.value = '';
};
const filteredSertificationsSelesai = computed(() => {
    return props.sertifications_selesai.filter(sert => {
        const isAsesorMatch = !asesorFilter.value || sert.asesor.id == asesorFilter.value;
        const isSkemaMatch = !skemaFilter.value || sert.skema.id == skemaFilter.value;
        const sertYear = new Date(sert.tgl_apply_ditutup).getFullYear();
        const isTahunMatch = !tahunFilter.value || sertYear == tahunFilter.value;
        return isAsesorMatch && isSkemaMatch && isTahunMatch;
    });
});

const tab = ref("berlangsung");
const form = useForm({
    skema_id: "",
    asesor_ids: [],
    tgl_apply_dibuka: "",
    tgl_apply_ditutup: "",
    deadline: "",
    tuk: "",
    biaya: "",
});
form.transform((data) => {
    return {
        ...data,
        asesor_ids: data.asesor_ids.map(asesor => asesor.id)
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
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Sertifikasi
            </h2>
        </template>

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
                        <div v-for="(asesor, index) in sert.asesors" :key="asesor.id" class="flex items-center mt-4 gap-2">
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
                                {{
                                    new Intl.NumberFormat("id-ID").format(
                                        sert.payment_instruction.biaya
                                    )
                                }}
                            </p>
                        </div>
                        <div class="flex items-center mt-4">
                            <MapPin class="w-4 h-4 text-gray-700 dark:text-gray-200" />
                            <p class="ml-2 text-gray-600 text-sm dark:text-gray-200">
                                <span class="font-semibold">
                                    TUK:
                                </span>
                                {{
                                    sert.tuk
                                }}
                            </p>
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
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end mb-4">
                    <FilterDropdown label="Asesor" :options="asesorOptions" v-model="asesorFilter" />
                    <FilterDropdown label="Skema" :options="skemaOptions" v-model="skemaFilter" />
                    <FilterDropdown label="Tahun" :options="yearOptions" v-model="tahunFilter" />
                    <SecondaryButton @click="resetFilters" class="w-full justify-center h-10">
                        Reset
                    </SecondaryButton>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div v-if="filteredSertificationsSelesai.length > 0" v-for="sert in filteredSertificationsSelesai"
                        :key="sert.id" class="bg-white p-6 rounded-lg dark:bg-gray-800">
                        <div class="flex items-center gap-2">
                            <BookOpen class="shrink-0 text-gray-700 dark:text-gray-200" />
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                                {{ sert.skema.nama_skema }}
                            </h3>
                        </div>
                        <div v-for="(asesor, index) in sert.asesors" :key="asesor.id" class="flex items-center mt-4 gap-2">
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
                                {{
                                    new Intl.NumberFormat(
                                        "id-ID"
                                    ).format(sert.payment_instruction.biaya)
                                }}
                            </p>
                        </div>
                        <div class="flex items-center mt-4">
                            <MapPin class="w-4 h-4 text-gray-700 dark:text-gray-200" />
                            <p class="ml-2 text-gray-600 text-sm dark:text-gray-200">
                                <span class="font-semibold">
                                    TUK:
                                </span>
                                {{
                                    sert.tuk
                                }}
                            </p>
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
                        Tidak ada riwayat sertifikasi untuk filter yang
                        dipilih.
                    </p>
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
                    <div v-if="form.skema_id">
                        <InputLabel value="Pilih Asesor (bisa lebih dari satu):" required />
                        <Multiselect v-model="form.asesor_ids" :options="availableAsesors" :multiple="true"
                            :close-on-select="false" placeholder="Cari atau pilih asesor" label="name" track-by="id"
                            :class="{ 'border-red-500': form.errors.asesor_ids }">
                            <template #noOptions>Tidak ada asesor tersedia untuk skema ini.</template>
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
                        <DateInput v-model="form.deadline" required />
                        <InputError :message="form.errors.deadline" />
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
</template>
