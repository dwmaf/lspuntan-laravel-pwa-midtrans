<script setup>
import AsesiLayout from "@/Layouts/AsesiLayout.vue";
import AsesiSertifikasiMenu from "@/Components/AsesiSertifikasiMenu.vue";
import CustomHeader from "@/Components/CustomHeader.vue";
import CreatorInfo from "@/Components/CreatorInfo.vue";
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
        <CustomHeader :judul="`Pengumuman: ${sertification.skema?.nama_skema ?? ''}`" />
        <AsesiSertifikasiMenu :sertification="props.sertification" :asesi="props.asesi"
            :latest-transaction="props.asesi.latest_transaction" />

        <div class="max-w-7xl mx-auto" v-if="showMode === 'list'">
            <div v-if="props.pengumumans.length > 0" class="space-y-4">
                <div v-for="pengumuman in props.pengumumans" :key="pengumuman.id"
                    class="py-3 px-5 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <CreatorInfo 
                        :name="pengumuman.user?.name || 'Admin'"
                        :created-at="pengumuman.created_at"
                        :updated-at="pengumuman.updated_at"
                        class="mb-4" 
                    />
                    <div v-html="truncateText(pengumuman.content)"
                        class="prose dark:prose-invert max-w-none text-sm text-gray-800 dark:text-gray-100"></div>
                    <div class="mt-3">
                        <button @click="showDetail(pengumuman)"
                            class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
                            Buka Pengumuman
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


                <CreatorInfo 
                        :name="selectedNews.user?.name || 'Admin'"
                        :created-at="selectedNews.created_at"
                        :updated-at="selectedNews.updated_at"
                        class="mb-4" 
                    />


                <div v-html="selectedNews.content"
                    class="prose dark:prose-invert max-w-none text-sm text-gray-800 dark:text-gray-100"></div>


                <div v-if="selectedNews.path_file"
                    class="mt-4 pt-4 dark:border-gray-700">
                    <div
                        class="flex items-center justify-between gap-4 px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md text-xs">
                        <a :href="`/storage/${selectedNews.path_file}`" target="_blank"
                            class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-500 hover:underline truncate flex-1">
                            {{ selectedNews.path_file.split('/').pop() }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </AsesiLayout>
</template>