<script setup>
import AsesiLayout from "@/Layouts/AsesiLayout.vue";
import AsesiSertifikasiMenu from "@/Components/AsesiSertifikasiMenu.vue";
import { ref, onMounted } from "vue";

const props = defineProps({
    sertification: Object,
    asesi: Object,
    pengumumans: Array,
    initialNewsId: [String, Number],
});
console.log(props.initialNewsId);
const showMode = ref('list');
const selectedNews = ref(null);


const formatDate = (dateString) => {
    const date = new Date(dateString);
    const today = new Date();
    if (date.toDateString() === today.toDateString()) {
        return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    }
    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};

const truncateText = (html, length = 150) => {
    const text = html.replace(/<[^>]*>/g, '');
    if (text.length <= 150) {
        return html;
    }
    return text.substring(0, length) + '...';
}

const showDetail = (pengumuman) => {
    selectedNews.value = pengumuman;
    showMode.value = 'show';
}

const showList = () => {
    selectedNews.value = null;
    showMode.value = 'list';
}

onMounted(() => {
    if (props.initialNewsId) {
        const newsToOpen = props.pengumumans.find(p => p.id == props.initialNewsId);
        if (newsToOpen) {
            console.log('pengumuman ditemukan, id pengumuman: ', newsToOpen.value)
            showDetail(newsToOpen);
        }
    }
})

</script>

<template>
    <AsesiLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Pengumuman
            </h2>
        </template>

        <AsesiSertifikasiMenu :sertification-id="props.sertification.id" :asesi="props.asesi"
            :latest-transaction="props.asesi.latest_transaction" />

        <div class="max-w-7xl mx-auto" v-if="showMode === 'list'">
            <div v-if="props.pengumumans.length > 0" class="space-y-4">
                <div v-for="pengumuman in props.pengumumans" :key="pengumuman.id"
                    class="py-3 px-5 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex-shrink-0">
                            <svg class="h-10 w-10 text-gray-400 dark:text-gray-600 rounded-full bg-gray-200 dark:bg-gray-700 p-1"
                                fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div>
                            <h5 class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                {{ pengumuman.pembuatpengumuman?.user?.name || 'Admin' }}
                            </h5>
                            <div class="text-xs text-gray-400">
                                {{ formatDate(pengumuman.created_at) }}
                                <span v-if="pengumuman.updated_at !== pengumuman.created_at"
                                    class="text-gray-500">(diedit)</span>
                            </div>
                        </div>
                    </div>
                    <div v-html="truncateText(pengumuman.rincian_pengumuman_asesmen)"
                        class="prose dark:prose-invert max-w-none text-sm text-gray-800 dark:text-gray-100"></div>
                    <div class="mt-3">
                        <button @click="showDetail(pengumuman)"
                            class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                            Baca Selengkapnya
                        </button>
                    </div>
                    <div v-if="pengumuman.newsfiles.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
                        <div v-for="file in pengumuman.newsfiles" :key="file.id"
                            class="flex items-center justify-between gap-4 px-3 py-2 border-1 border-gray-300 dark:border-gray-700 rounded-md text-xs">
                            <a :href="`/storage/${file.path_file}`" target="_blank"
                                class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-500 hover:underline truncate flex-1">
                                {{ file.path_file.split('/').pop() }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="text-center text-gray-500 dark:text-gray-300 py-12">
                <p>Belum ada pengumuman apapun.</p>
            </div>
        </div>
        <div class="max-w-7xl mx-auto" v-if="showMode === 'show' && selectedNews">
            <div class="py-3 px-5 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <button @click="showList"
                    class="flex items-center gap-2 text-sm text-blue-600 dark:text-blue-400 hover:underline mb-4">
                    &larr;
                    Kembali ke Daftar
                </button>


                <div class="flex items-center gap-3 mb-4">
                    <div class="flex-shrink-0">

                        <svg class="h-10 w-10 text-gray-400 dark:text-gray-600 rounded-full bg-gray-200 dark:bg-gray-700 p-1"
                            fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <h5 class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                            {{ selectedNews.pembuatpengumuman?.user?.name || 'Admin' }}
                        </h5>
                        <div class="text-xs text-gray-400">
                            {{ formatDate(selectedNews.created_at) }}
                            <span v-if="selectedNews.updated_at !== selectedNews.created_at"
                                class="text-gray-500">(diedit)</span>
                        </div>
                    </div>
                </div>


                <div v-html="selectedNews.rincian_pengumuman_asesmen"
                    class="prose dark:prose-invert max-w-none text-sm text-gray-800 dark:text-gray-100"></div>


                <div v-if="selectedNews.newsfiles.length > 0"
                    class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4 pt-4 dark:border-gray-700">
                    <div v-for="file in selectedNews.newsfiles" :key="file.id"
                        class="flex items-center justify-between gap-4 px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md text-xs">
                        <a :href="`/storage/${file.path_file}`" target="_blank"
                            class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-500 hover:underline truncate flex-1">
                            {{ file.path_file.split('/').pop() }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </AsesiLayout>
</template>