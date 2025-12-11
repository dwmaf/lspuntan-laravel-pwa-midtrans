<script setup>
import DevLayout from "@/Pages/Dev/DevLayout.vue";
import DeleteButton from "@/Components/DeleteButton.vue";
import AddButton from "@/Components/AddButton.vue";
import { ref, computed } from "vue";
import { useForm, usePage, Link, router } from "@inertiajs/vue3";
const props = defineProps({
    sertification: Object,

});

const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};

const formatDateTime = (dateString) => {
    if (!dateString) return "N/A";
    const formatted = new Date(dateString).toLocaleString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
    }).replace('pukul', ',').replace('.', ':');
    return `${formatted} WIB`;
};

const formattedHarga = computed(() => {
    if (!form.harga) return "";
    const number = parseFloat(form.harga);
    if (isNaN(number)) return "";
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(number);
});

const destroyAsesmen = () => {
    if (confirm('Hapus asesmen?')) {
        router.delete(route('dev.sertification.destroy.asesmen', props.sertification.id));
    }
};

const storeDummyNews = () => {
    if (confirm('Buat 30 pengumuman dummy?')) {
        router.post(route('dev.sertification.store.news', props.sertification.id));
    }
};

const destroyNews = () => {
    if (confirm('Hapus semua pengumuman untuk sertifikasi ini? Tindakan ini tidak bisa diurungkan.')) {
        router.delete(route('dev.sertification.destroy.news', props.sertification.id));
    }
};
</script>
<template>
    <DevLayout>
        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 sm:mb-0">
                    Detail Sertifikasi
                </h3>
            </div>
    
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Id Sertifikasi</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ props.sertification.id }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Skema</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ props.sertification.skema.nama_skema }}</dd>
                </div>
                <div v-if="props.sertification.asesors && props.sertification.asesors.length > 0"
                    v-for="(asesor, index) in props.sertification.asesors" :key="asesor.id">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Asesor {{ index + 1 }}</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ asesor.user.name }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Pendaftaran Dibuka</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ formatDate(props.sertification.tgl_apply_dibuka) }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Pendaftaran Ditutup
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ formatDate(props.sertification.tgl_apply_ditutup) }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Batas Akhir Pembayaran</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ formatDateTime(props.sertification.deadline_bayar) }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Biaya Sertifikasi</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">Rp
                        {{ new Intl.NumberFormat('id-ID').format(props.sertification.biaya) }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah Asesi</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ props.sertification.asesis_count }} terdaftar</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">TUK</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ props.sertification.tuk }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                    <dd class="mt-1 text-sm">
    
                        <span v-if="props.sertification.status === 'berlangsung'"
                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                            Sedang Berlangsung
                        </span>
                        <span v-if="props.sertification.status === 'selesai'"
                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100">
                            Selesai
                        </span>
                    </dd>
                </div>
            </div>
            <div class="flex mt-4 gap-2 items-center">
                <p class=" text-gray-600 text-sm dark:text-gray-200">
                    <span class="font-semibold">
                        Status Asesmen:
                    </span>
                    {{ sertification.asesmen ? 'Ada':'Belum Dibuat' }}
                </p>
                <div v-if="sertification.asesmen">
                    <DeleteButton @click="destroyAsesmen">Hapus Asesmen</DeleteButton>
                </div>
            </div>
            <div class="flex mt-4 gap-2 items-center">
                <p class=" text-gray-600 text-sm dark:text-gray-200">
                    <span class="font-semibold">
                        Jumlah Pengumuman:
                    </span>
                    {{ sertification.news ? sertification.news.length : '0' }}
                </p>
                <div v-if="sertification.news && sertification.news.length > 0">
                    <DeleteButton @click="destroyNews">Hapus Semua Pengumuman</DeleteButton>
                </div>
                <AddButton @click="storeDummyNews">Buat 30 Pengumuman</AddButton>
            </div>
            
        </div>
    </DevLayout>
</template>
