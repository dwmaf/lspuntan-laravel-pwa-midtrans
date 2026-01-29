<script setup>
import AsesiLayout from "@/Layouts/AsesiLayout.vue";
import AsesiSertifikasiMenu from "@/Components/AsesiSertifikasiMenu.vue";
import CustomHeader from "@/Components/CustomHeader.vue";
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

const markNewsAsRead = (newsId) => {
    window.axios.post(route('asesi.pengumuman.mark-read', {
        sertification: props.sertification,
        asesi: props.asesi,
        news: newsId
    })).catch(err => console.error(err));
}

const showDetail = (pengumuman) => {
    selectedNews.value = pengumuman;
    showMode.value = 'show';
    showMode.value = 'show';
    if (!pengumuman.is_read) {
        markNewsAsRead(pengumuman.id);
        // Optimistically update UI
        pengumuman.is_read = true;
    }
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
        <CustomHeader :judul="`Pengumuman: ${sertification.skema?.nama_skema ?? ''}`" />
        <AsesiSertifikasiMenu :sertification="props.sertification" :asesi="props.asesi"
            :latest-transaction="props.asesi.latest_transaction" />

        <div class="max-w-7xl mx-auto" v-if="showMode === 'list'">
            <div v-if="props.pengumumans.length > 0" class="space-y-4">
                <div v-for="pengumuman in props.pengumumans" :key="pengumuman.id"
                    class="py-3 px-5 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="shrink-0">
                            <svg class="h-10 w-10 text-gray-400 dark:text-gray-600 rounded-full bg-gray-200 dark:bg-gray-700 p-1"
                                fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div>
                            <h5 class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                {{ pengumuman.user?.name || 'Admin' }}
                            </h5>
                            <div class="text-xs text-gray-400">
                                {{ formatDate(pengumuman.created_at) }}
                                
                                <span v-if="pengumuman.created_at !== pengumuman.updated_at" class="text-gray-500">(diedit)</span>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div class="ml-auto shrink-0">
                            <span v-if="pengumuman.is_read"
                                class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full dark:bg-green-900 dark:text-green-300 whitespace-nowrap">
                                Sudah Dibaca
                            </span>
                            <span v-else
                                class="px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full dark:bg-red-900 dark:text-red-300 whitespace-nowrap">
                                Belum Dibaca
                            </span>
                        </div>
                    </div>
                    <div v-html="truncateText(pengumuman.content)"
                        class="prose dark:prose-invert max-w-none text-sm text-gray-800 dark:text-gray-100"></div>
                    <div class="mt-3">
                        <button @click="showDetail(pengumuman)"
                            class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
                            {{ pengumuman.is_read ? 'Buka Kembali' : 'Buka Pengumuman' }}
                        </button>
                    </div>
                    <!-- Files hidden in list view -->
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
                    <div class="shrink-0">

                        <svg class="h-10 w-10 text-gray-400 dark:text-gray-600 rounded-full bg-gray-200 dark:bg-gray-700 p-1"
                            fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <h5 class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                            {{ selectedNews.user?.name || 'Admin' }}
                        </h5>
                        <div class="text-xs text-gray-400">
                            {{ formatDate(selectedNews.created_at) }}
                            <span v-if="selectedNews.created_at !== selectedNews.updated_at" class="text-gray-500">(diedit)</span>
                        </div>
                    </div>
                </div>


                <div v-html="selectedNews.content"
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