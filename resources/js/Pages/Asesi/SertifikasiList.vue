<script setup>
import AsesiLayout from "@/Layouts/AsesiLayout.vue";
import PrimaryLinkButton from "@/Components/PrimaryLinkButton.vue";
import CustomHeader from "@/Components/CustomHeader.vue";
import SertifikasiCard from "@/Components/SertifikasiCard.vue";
import { ref, computed } from "vue";

const props = defineProps({
    sertifications_tersedia: Array,
    sertifications_saya: Array,
    asesis: Object,
});

const activeTab = ref('tersedia');

const displaySertifications = computed(() => {
    return activeTab.value === 'tersedia' 
        ? props.sertifications_tersedia 
        : props.sertifications_saya;
});

const isNew = (sert) => {
    const createdAt = new Date(sert.tgl_apply_dibuka);
    const now = new Date();
    const diffDays = (now - createdAt) / (1000 * 3600 * 24);
    return diffDays <= 7;
}

const getSertifikasiStatus = (sert) => {
    const sudahDaftar = !!props.asesis[sert.id];
    const pendaftaranDitutup = new Date() > new Date(sert.tgl_apply_ditutup);
    const pendaftaranDibuka = new Date() >= new Date(sert.tgl_apply_dibuka);

    if (sudahDaftar) {
        return {
            type: 'applied',
            text: 'Lihat Status',
            class: 'bg-blue-500 hover:bg-blue-600 text-white',
            href: route('asesi.sertifikasi.applied.show', { sertification: sert.id, asesi: props.asesis[sert.id].id }),
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

</script>

<template>
    <AsesiLayout>
        <CustomHeader judul="Daftar Sertifikasi" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex flex-wrap space-x-4 mt-1" aria-label="Tabs">
                <div>
                    <button @click="activeTab = 'tersedia'"
                        class="flex items-center gap-2 px-4 py-3 font-semibold text-sm hover:bg-gray-300 hover:dark:bg-gray-700 rounded-t-md dark:text-white text-gray-600 cursor-pointer">
                        Tersedia
                    </button>
                    <div style="margin-top: -4px" v-show="activeTab === 'tersedia'"
                        class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
                </div>
                <div>
                    <button @click="activeTab = 'saya'"
                        class="flex items-center gap-2 px-4 py-3 font-semibold text-sm hover:bg-gray-300 hover:dark:bg-gray-700 rounded-t-md dark:text-white text-gray-600 cursor-pointer">
                        <span>Sertifikasi Saya</span>
                    </button>
                    <div style="margin-top: -4px" v-show="activeTab === 'saya'"
                        class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
                </div>
            </nav>
            <hr class="border-gray-200 dark:border-gray-700 mb-6" />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <SertifikasiCard v-for="sert in displaySertifications" :key="sert.id" :sert="sert">
                    <template #badges>
                        <span v-if="sert.status === 'selesai'"
                            class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                            Selesai
                        </span>

                        <span v-if="isNew(sert) && getSertifikasiStatus(sert).type === 'open'"
                            class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-700 dark:bg-blue-700 dark:text-blue-100">
                            Baru
                        </span>
                        <span v-if="getSertifikasiStatus(sert).type === 'open'"
                            class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-700 dark:bg-green-700 dark:text-green-100">
                            Dibuka
                        </span>
                        <span v-else-if="getSertifikasiStatus(sert).type === 'closed'"
                            class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-700 dark:bg-red-700 dark:text-red-100">
                            Ditutup
                        </span>
                    </template>

                    <template #actions>
                        <PrimaryLinkButton v-if="getSertifikasiStatus(sert).href" :href="getSertifikasiStatus(sert).href">
                            {{ getSertifikasiStatus(sert).text }}
                        </PrimaryLinkButton>
                        <span v-else
                            :class="['inline-block font-medium px-4 py-2 rounded-lg', getSertifikasiStatus(sert).class]">
                            {{ getSertifikasiStatus(sert).text }}
                        </span>
                    </template>
                </SertifikasiCard>

                <div v-if="displaySertifications.length === 0"
                    class="col-span-1 sm:col-span-2 text-center py-12 bg-white dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-200 dark:border-gray-700">
                    <p class="text-gray-500 dark:text-gray-400">
                        {{ activeTab === 'tersedia' ? 'Saat ini belum ada sertifikasi yang dibuka.' : 'Anda belum mendaftar di sertifikasi manapun.' }}
                    </p>
                </div>
            </div>
        </div>
    </AsesiLayout>
</template>