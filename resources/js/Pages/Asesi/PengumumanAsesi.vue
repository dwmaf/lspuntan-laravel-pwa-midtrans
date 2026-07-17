<script setup>
import AsesiLayout from "@/Layouts/AsesiLayout.vue";
import AsesiSertifikasiMenu from "@/Components/AsesiSertifikasiMenu.vue";
import CustomHeader from "@/Components/CustomHeader.vue";
import CreatorInfo from "@/Components/CreatorInfo.vue";
import FileIcon from '@/Components/FileIcon.vue';
import BackButton from "@/Components/Button/BackButton.vue";
import { ref, onMounted } from "vue";

const props = defineProps({
    sertifikasi: Object,
    asesi: Object,
    listPengumuman: Array,
    initialPengumumanId: [String, Number],
});
// console.log(props.initialPengumumanId);
const showMode = ref('list');
const selectedPengumuman = ref(null);

const truncateText = (html, length = 150) => {
    const text = html.replace(/<[^>]*>/g, '');
    if (text.length <= 150) {
        return html;
    }
    return text.substring(0, length) + '...';
}

const showDetail = (pengumuman) => {
    selectedPengumuman.value = pengumuman;
    showMode.value = 'show';
    showMode.value = 'show';
}

const showList = () => {
    selectedPengumuman.value = null;
    showMode.value = 'list';
}

onMounted(() => {
    if (props.initialPengumumanId) {
        const newsToOpen = props.listPengumuman.find(p => p.id == props.initialPengumumanId);
        if (newsToOpen) {
            // console.log('pengumuman ditemukan, id pengumuman: ', newsToOpen.value)
            showDetail(newsToOpen);
        }
    }
})

</script>

<template>
    <AsesiLayout>
        <CustomHeader :judul="`Pengumuman: ${sertifikasi.skema?.nama_skema ?? ''}`" />
        <AsesiSertifikasiMenu :sertifikasi="props.sertifikasi" :asesi="props.asesi"
            :latest-transaction="props.asesi.latest_transaction" />

        <div class="max-w-3xl mx-auto" v-if="showMode === 'list'">
            <div v-if="props.listPengumuman.length > 0" class="space-y-4">
                <div v-for="pengumuman in props.listPengumuman" :key="pengumuman.id"
                    class="py-3 px-5 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <CreatorInfo :name="pengumuman.user?.name || 'Admin'" :created-at="pengumuman.created_at"
                        :updated-at="pengumuman.updated_at" class="mb-4" />
                    <div v-html="truncateText(pengumuman.content)"
                        class="prose dark:prose-invert max-w-none font-medium text-sm text-gray-800 dark:text-gray-100"></div>
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
        <div class="max-w-3xl mx-auto" v-if="showMode === 'show' && selectedPengumuman">
            <div class="py-3 px-5 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <div class="flex justify-start items-center mb-4">
                    <BackButton @click="showList"
                        class="self-start sm:self-auto" />
                </div>
                <CreatorInfo :name="selectedPengumuman.user?.name || 'Admin'" :created-at="selectedPengumuman.created_at"
                    :updated-at="selectedPengumuman.updated_at" class="mb-4" />


                <div v-html="selectedPengumuman.content.replace(/\n/g, '<br>')"
                    class="prose dark:prose-invert max-w-none font-medium text-sm text-gray-800 dark:text-gray-100"></div>
                <div v-if="selectedPengumuman.path_file" class="mt-4 pt-4 dark:border-gray-700">
                    <a :href="`/download/pengumuman/${selectedPengumuman.id}/path_file`" target="_blank"
                        class="text-sm flex items-center gap-2 group min-w-0">
                        <FileIcon :path="selectedPengumuman.path_file" />
                        <span class="text-blue-500 group-hover:text-blue-700 truncate group-hover:underline">
                            {{ selectedPengumuman.path_file.split('/').pop() }}
                        </span>

                    </a>
                    <!-- <div
                        class="flex items-center justify-between gap-4 px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md text-xs">
                    </div> -->
                </div>
            </div>
        </div>
    </AsesiLayout>
</template>