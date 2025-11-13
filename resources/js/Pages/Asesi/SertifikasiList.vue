<script setup>
import AsesiLayout from "@/Layouts/AsesiLayout.vue";
import PrimaryLinkButton from "../../Components/PrimaryLinkButton.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import { MapPin, DollarSign, CalendarRange, BookOpen } from "lucide-vue-next";
import { IconChalkboardTeacher } from "@tabler/icons-vue";

const props = defineProps({
    sertifications: Array,
    asesi: Object,
});

const isNew = (sert) => {
    const createdAt = new Date(sert.created_at);
    const now = new Date();
    const diffDays = (now - createdAt) / (1000 * 3600 * 24);
    return diffDays <= 7;
}

// Fungsi helper untuk mengecek status pendaftaran
const getSertifikasiStatus = (sert) => {
    const sudahDaftar = !!props.asesi[sert.id];
    const pendaftaranDitutup = new Date() > new Date(sert.tgl_apply_ditutup);
    const pendaftaranDibuka = new Date() >= new Date(sert.tgl_apply_dibuka);

    if (sudahDaftar) {
        return {
            type: 'applied',
            text: 'Lihat Status',
            class: 'bg-blue-500 hover:bg-blue-600 text-white',
            href: route('asesi.sertifikasi.applied.show', { sert_id: sert.id, asesi_id: props.asesi[sert.id].id }),
        };
    }
    if (pendaftaranDitutup) {
        return { type: 'closed', text: 'Pendaftaran Ditutup', class: 'bg-gray-400 text-white cursor-not-allowed opacity-70' };
    }
    if (!pendaftaranDibuka) {
        return { type: 'soon', text: 'Belum Dibuka', class: 'bg-yellow-400 text-white cursor-not-allowed opacity-70' };
    }
    return {
        type: 'open',
        text: 'Daftar',
        class: 'bg-green-500 hover:bg-green-600 text-white',
        href: route('asesi.sertifikasi.apply.create', sert.id),
    };
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};
</script>

<template>
    <AsesiLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Sertifikasi
            </h2>
        </template>

        

        <div class="max-w-7xl mx-auto mb-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div v-for="sert in props.sertifications" :key="sert.id"
                    class="bg-white p-6 rounded-lg dark:bg-gray-800 flex flex-col">
                    <div class="flex items-center gap-2">
                        <BookOpen class="shrink-0 text-gray-700 dark:text-gray-200" />
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                            {{ sert.skema.nama_skema }}
                        </h3>
                        <span v-if="isNew(sert) && getSertifikasiStatus(sert).type === 'open'"
                            class=" px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-700 dark:bg-blue-700 dark:text-blue-100">
                            Dibuka
                        </span>
                        <span v-if="getSertifikasiStatus(sert).type === 'open'"
                            class=" px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-700 dark:bg-green-700 dark:text-green-100">
                            Dibuka
                        </span>
                        <span v-else-if="getSertifikasiStatus(sert).type === 'closed'"
                            class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-700 dark:bg-red-700 dark:text-red-100">
                            Ditutup
                        </span>
                    </div>

                    <div class="flex items-center mt-4">
                        <CalendarRange class="shrink-0 w-4 h-4 text-gray-700 dark:text-gray-200" />
                        <p class="ml-2 text-gray-600 text-sm dark:text-gray-200">
                            Pendaftaran: {{ formatDate(sert.tgl_apply_dibuka) }} &ndash; {{
                                formatDate(sert.tgl_apply_ditutup)
                            }}
                        </p>
                    </div>
                    <div class="flex items-center mt-2">
                        <DollarSign class="w-4 h-4 text-gray-700 dark:text-gray-200" />
                        <p class="ml-2 text-gray-600 text-sm dark:text-gray-200">
                            Biaya: {{ formatCurrency(sert.payment_instruction.biaya) }}
                        </p>
                    </div>

                    <div class="mt-auto pt-4">
                        <PrimaryLinkButton v-if="getSertifikasiStatus(sert).href" :href="getSertifikasiStatus(sert).href">{{ getSertifikasiStatus(sert).text }}
                        </PrimaryLinkButton>
                        <span v-else
                            :class="['inline-block font-medium px-4 py-2 rounded-lg', getSertifikasiStatus(sert).class]">
                            {{ getSertifikasiStatus(sert).text }}
                        </span>
                    </div>
                </div>
                <div v-if="props.sertifications.length === 0"
                    class="col-span-1 sm:col-span-2 text-center py-12 text-gray-500 dark:text-gray-400">
                    Saat ini belum ada sertifikasi yang dibuka.
                </div>
            </div>
        </div>
    </AsesiLayout>
</template>