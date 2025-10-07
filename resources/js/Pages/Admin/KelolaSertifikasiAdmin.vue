<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import PrimaryLinkButton from "../../Components/PrimaryLinkButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { useForm, usePage, Link, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
    sertifications_berlangsung: Array,
    sertifications_selesai: Array,
    asesors: Array,
    skemas: Array,
    errors: Object,
    filters: Object,
});
const showFilter = ref(false);
const applyFilter = (filterValue) => {
    router.get(route('admin.kelolasertifikasi.index'), { filter: filterValue }, {
        preserveState: true,
        replace: true,
    });
    showFilter.value = false;
};
const tab = ref("mulai");
const form = useForm({
    asesor_skema: "",
    tgl_apply_dibuka: "",
    tgl_apply_ditutup: "",
    tgl_bayar_ditutup: "",
    tuk: "",
    harga: "",
});
const formattedHarga = computed(() => {
    if (!form.harga) return '';
    const number = parseFloat(form.harga);
    if (isNaN(number)) return '';
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(number);
});
const submit = () => {
    form.post(route("admin.kelolasertifikasi.update"), {
        onSudccess: () => form.reset(),
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
                    class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md dark:text-white text-gray-600 cursor-pointer">
                    Mulai Sertifikasi
                </button>
                <div style="margin-top: -4px" v-show="tab === 'mulai'"
                    class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
            </div>
            <div>
                <button @click="tab = 'berlangsung'"
                    class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md dark:text-white text-gray-600 cursor-pointer">
                    Sertifikasi Berlangsung
                </button>
                <div style="margin-top: -4px" v-show="tab === 'berlangsung'"
                    class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
            </div>
            <div>
                <button @click="tab = 'selesai'"
                    class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md dark:text-white text-gray-600 cursor-pointer">
                    Riwayat Sertifikasi
                </button>
                <div style="margin-top: -4px" v-show="tab === 'selesai'"
                    class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
            </div>
        </nav>
        <hr class="border-gray-200 dark:border-gray-700 mb-2" />

        <!-- Konten Tab -->
        <div>
            <div v-show="tab === 'berlangsung'">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div v-if="props.sertifications_berlangsung.length > 0"
                        v-for="sert in props.sertifications_berlangsung" :key="sert.id"
                        class="bg-white p-6 rounded-lg dark:bg-gray-800">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                            {{ sert.skema.nama_skema }}
                        </h3>
                        <div class="flex items-center mt-4">
                            <FontAwesomeIcon icon="fa-solid fa-calendar-days"
                                class="w-4 h-4 text-gray-700 dark:text-gray-200" />
                            <p class="ml-2 text-gray-600 text-sm dark:text-gray-200">
                                Pendaftaran:
                                {{ sert.tgl_apply_dibuka }}
                                &ndash;
                                {{ sert.tgl_apply_ditutup }}
                            </p>
                        </div>
                        <div class="flex items-center mt-4">
                            <FontAwesomeIcon icon="fa-solid fa-money-bill-1-wave"
                                class="w-4 h-4 text-gray-700 dark:text-gray-200" />
                            <p class="ml-2 text-gray-600 text-sm dark:text-gray-200">
                                Biaya: Rp
                                {{
                                    new Intl.NumberFormat("id-ID").format(
                                        sert.harga
                                    )
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
                <div class="flex justify-end items-center mb-4">
                    <div class="relative">
                        <button @click="showFilter = !showFilter"
                            class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z">
                                </path>
                            </svg>
                            Filter
                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': showFilter }" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div v-if="showFilter" @click.outside="showFilter = false"
                            class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-md shadow-lg z-10 border border-gray-200 dark:border-gray-600">
                            <div class="py-1">
                                <button @click="applyFilter('semua')"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ $selectedFilter === 'semua' ? 'bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : '' }}">
                                    Semua
                                </button>
                                <button @click="applyFilter('bulan_ini')"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ $selectedFilter === 'bulan_ini' ? 'bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : '' }}">
                                    Bulan Ini
                                </button>
                                <button @click="applyFilter('3_bulan')"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ $selectedFilter === '3_bulan' ? 'bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : '' }}">
                                    3 Bulan Terakhir
                                </button>
                                <button @click="applyFilter('tahun_ini')"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 {{ $selectedFilter === 'tahun_ini' ? 'bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : '' }}">
                                    Tahun Ini
                                </button>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div v-if="props.sertifications_selesai.length > 0" v-for="sert in props.sertifications_selesai"
                        :key="sert.id" class="bg-white p-6 rounded-lg dark:bg-gray-800 opacity-70">
                        <div class="bg-white p-6 rounded-lg dark:bg-gray-800 opacity-70">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                                {{ sert.skema.nama_skema }}
                            </h3>
                            <div class="flex items-center mt-4">
                                <FontAwesomeIcon icon="fa-solid fa-calendar-days"
                                    class="w-4 h-4 text-gray-700 dark:text-gray-200" />
                                <p class="ml-2 text-gray-600 text-sm dark:text-gray-200">
                                    Pendaftaran:
                                    {{ sert.tgl_apply_dibuka }} &ndash;
                                    {{ sert.tgl_apply_ditutup }}
                                </p>
                            </div>
                            <div class="flex items-center mt-4">
                                <FontAwesomeIcon icon="fa-solid fa-money-bill-1-wave"
                                    class="w-4 h-4 text-gray-700 dark:text-gray-200" />
                                <p class="ml-2 text-gray-600 text-sm dark:text-gray-200">
                                    Biaya: Rp
                                    {{
                                        new Intl.NumberFormat(
                                            "id-ID"
                                        ).format(sert.harga)
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
                    </div>

                    <p v-else class="text-gray-500 dark:text-gray-400 col-span-2">
                        Tidak ada riwayat sertifikasi untuk filter yang
                        dipilih.
                    </p>
                </div>
            </div>

            <!-- Konten untuk mulai sertifikasi -->
            <div v-show="tab === 'mulai'" class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                    Mulai Sertifikasi
                </h2>
                <form @submit.prevent="submit" class="mt-4 flex flex-col gap-4">
                    <div>
                        <InputLabel value="Pilih Skema dan Asesor:" />
                        <select v-model="form.asesor_skema" required
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option value="" disabled>
                                --Silahkan pilih asesor dan skema--
                            </option>
                            <template v-for="asesor in props.asesors" :key="asesor.id">
                                <option v-for="skema in asesor.skemas" :key="skema.id"
                                    :value="`${asesor.id},${skema.id}`">
                                    {{ asesor.user.name }} -
                                    {{ skema.nama_skema }}
                                </option>
                            </template>
                        </select>
                        <InputError :message="form.errors.asesor_skema" />
                    </div>
                    <div>
                        <InputLabel value="Tanggal Daftar Dibuka" />
                        <TextInput type="date" v-model="form.tgl_apply_dibuka" required />
                        <InputError :message="form.errors.tgl_apply_dibuka" />
                    </div>
                    <div id="tanggal_apply_ditutup">
                        <InputLabel value="Tanggal Daftar Ditutup" />
                        <TextInput type="date" v-model="form.tgl_apply_ditutup" required />
                        <InputError :message="form.errors.tgl_apply_ditutup" />
                    </div>
                    <div id="tanggal_bayar_ditutup">
                        <InputLabel value="Tanggal Bayar Ditutup" />
                        <TextInput type="date" v-model="form.tgl_bayar_ditutup" required />
                        <InputError :message="form.errors.tgl_bayar_ditutup" />
                    </div>
                    <div id="biaya_sertifikasi">
                        <InputLabel value="Biaya Sertifikasi" />
                        <p v-if="formattedHarga" class="text-sm font-medium text-gray-800 dark:text-gray-400">
                            {{ formattedHarga }}
                        </p>
                        <TextInput min="0" type="number" v-model="form.harga" required />
                        <InputError :message="form.errors.harga" />
                    </div>
                    <div id="tuk">
                        <InputLabel value="Tempat Uji Sertifikasi" />
                        <TextInput type="text" v-model="form.tuk" required />
                        <InputError :message="form.errors.tuk" />
                    </div>
                    <div class="flex">
                        <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing">
                            Mulai
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>

    </AdminLayout>
</template>
