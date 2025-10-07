<!-- filepath: d:\Laravel-App\lsp-untan-laravel-pwa\resources\js\Pages\Asesi\PengumumanAsesi.vue -->
<script setup>
import AsesiLayout from "@/Layouts/AsesiLayout.vue";
import AsesiSertifikasiMenu from "@/Components/AsesiSertifikasiMenu.vue";
import { computed } from 'vue';

const props = defineProps({
    sertification: Object,
    asesi: Object,
    pengumumans: Array,
});

// Fungsi untuk memformat tanggal
const formatDate = (dateString) => {
    const date = new Date(dateString);
    const today = new Date();
    
    // Cek jika tanggalnya hari ini
    if (date.toDateString() === today.toDateString()) {
        return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    }
    
    // Jika bukan hari ini, format tanggal
    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};

</script>

<template>
    <AsesiLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Pengumuman
            </h2>
        </template>

        <AsesiSertifikasiMenu :sertification-id="props.sertification.id" :asesi="props.asesi" :latest-transaction="props.asesi.latest_transaction" />

        <div class="max-w-7xl mx-auto">
            <div v-if="props.pengumumans.length > 0" class="space-y-4">
                <div v-for="pengumuman in props.pengumumans" :key="pengumuman.id" class="py-3 px-5 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <!-- Header Pengumuman -->
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex-shrink-0">
                            <!-- Avatar -->
                            <svg class="h-10 w-10 text-gray-400 dark:text-gray-600 rounded-full bg-gray-200 dark:bg-gray-700 p-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div>
                            <h5 class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                {{ pengumuman.pembuatpengumuman?.user?.name || 'Admin' }}
                            </h5>
                            <div class="text-xs text-gray-400">
                                {{ formatDate(pengumuman.created_at) }}
                                <span v-if="pengumuman.updated_at !== pengumuman.created_at" class="text-gray-500">(diedit)</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Isi Pengumuman -->
                    <div v-html="pengumuman.rincian_pengumuman_asesmen" class="prose dark:prose-invert max-w-none text-sm text-gray-800 dark:text-gray-100"></div>

                    <!-- Lampiran -->
                    <div v-if="pengumuman.pengumumanasesmenfile.length > 0" class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-700">
                        <h4 class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2">Lampiran:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <a v-for="file in pengumuman.pengumumanasesmenfile" :key="file.id" :href="`/storage/${file.path_file}`" target="_blank" class="flex items-center gap-2 p-2 border rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                <span class="truncate text-sm text-blue-600 dark:text-blue-400">{{ file.path_file.split('/').pop() }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="text-center text-gray-500 dark:text-gray-300 py-12">
                <p>Belum ada pengumuman apapun.</p>
            </div>
        </div>
    </AsesiLayout>
</template>