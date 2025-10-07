<!-- filepath: d:\Laravel-App\lsp-untan-laravel-pwa\resources\js\Pages\Asesi\AsesmenAsesi.vue -->
<script setup>
import AsesiLayout from "@/Layouts/AsesiLayout.vue";
import AsesiSertifikasiMenu from "@/Components/AsesiSertifikasiMenu.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { useForm, usePage, router } from "@inertiajs/vue3";
import { computed } from 'vue';

const props = defineProps({
    sertification: Object,
    asesi: Object,
});

// Form untuk upload file baru
const form = useForm({
    newFiles: [],
});

const submit = () => {
    form.post(route('asesi.sertifikasi.asesmen.store', { sert_id: props.sertification.id, asesi_id: props.asesi.id }), {
        onSuccess: () => form.reset('newFiles'),
        preserveScroll: true,
    });
};

// Fungsi untuk menghapus file
const deleteFile = (fileId) => {
    if (confirm('Yakin ingin menghapus file ini?')) {
        router.delete(route('asesi.sertifikasi.asesmen.file.destroy', fileId), {
            preserveScroll: true,
        });
    }
};

// Notifikasi
const notification = computed(() => usePage().props.flash?.message);

</script>

<template>
    <AsesiLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Asesmen
            </h2>
        </template>

        <!-- Notifikasi -->
        <div v-if="notification" class="fixed top-20 right-4 text-sm px-4 py-2 rounded bg-green-600 text-white z-50">
            {{ notification }}
        </div>

        <AsesiSertifikasiMenu :sertification-id="props.sertification.id" :asesi="props.asesi" :latest-transaction="props.asesi.latest_transaction" />

        <div class="max-w-7xl mx-auto">
            <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md mb-2">
                <div v-if="props.sertification.rincian_tugas_asesmen">
                    <!-- Info Pembuat Tugas -->
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
                                <div v-if="props.sertification.pembuatrinciantugasasesmen.asesor">
                                    {{ props.sertification.pembuatrinciantugasasesmen.asesor.user.name }}
                                </div>
                                <div v-else>
                                    Admin
                                </div>
                            </h5>
                            <div class="text-xs text-gray-400">
                                {{ props.sertification.rincian_tugasasesmen_dibuat_pada }}
                                <!-- Jika sudah lewat, tampilkan tanggal  -->
                            </div>
                        </div>
                    </div>

                    <!-- Rincian Tugas -->
                    <div v-html="props.sertification.rincian_tugas_asesmen"
                        class="prose dark:prose-invert max-w-none text-sm text-gray-800 dark:text-gray-100 mb-2"></div>

                    <!-- Lampiran dari Asesor -->
                    <div v-if="sertification.tugasasesmenattachmentfile.length > 0" class="mt-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Lampiran Tugas:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-1">
                            <a v-for="file in sertification.tugasasesmenattachmentfile" :key="file.id"
                                :href="`/storage/${file.path_file}`" target="_blank"
                                class="flex items-center justify-between gap-4 px-3 py-2 border-1 border-gray-300 dark:border-gray-700 rounded-md text-xs">
                                <span class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-500 hover:underline truncate flex-1">{{ file.path_file.split('/').pop() }}</span>
                            </a>
                        </div>
                    </div>

                    <!-- Form Pengumpulan Tugas Asesi -->
                    <form @submit.prevent="submit"
                        class="border border-gray-300 dark:border-gray-600 rounded-md p-4 mt-10">
                        <InputLabel value="Kumpulkan Tugas Anda (maks 5 file, total ukuran maks 5 MB)" />

                        <!-- Daftar File yang Sudah Diunggah -->
                        <div v-if="asesi.asesiasesmenfiles.length > 0" class="my-2 space-y-1">
                            <div v-for="file in asesi.asesiasesmenfiles" :key="file.id"
                                class="flex flex-row py-1 px-2 border border-gray-300 dark:border-gray-600 rounded-md items-center justify-between mb-1">
                                <a :href="`/storage/${file.path_file}`" target="_blank" class="font-medium text-sm text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 no-underline">{{
                                    file.path_file.split('/').pop() }}</a>
                                <button type="button" @click="deleteFile(file.id)"
                                    class="font-medium text-sm ml-2 text-red-600 cursor-pointer flex items-center gap-1">Hapus</button>
                            </div>
                        </div>
                        <p v-else class="text-xs text-gray-500 dark:text-gray-400 my-2">Belum ada file yang dikumpulkan.
                        </p>

                        <!-- Input File Baru -->
                         <TextInput type="file" @input="form.newFiles = $event.target.files" multiple required />
                        <!-- <input type="file" @input="form.newFiles = $event.target.files" multiple/> -->
                        <InputError :message="form.errors.newFiles"/>
                        <InputError v-for="(error, index) in form.errors" :key="index" :message="error"
                            v-if="index.startsWith('newFiles.')" />

                        <div class="flex items-center gap-4 mt-4">
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Kumpulkan
                            </PrimaryButton>
                            <div v-if="form.progress" class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                <div class="bg-blue-600 h-2.5 rounded-full"
                                    :style="{ width: form.progress.percentage + '%' }">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <p v-else class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Asesor belum memberikan rincian tugas asesmen.
                </p>
            </div>
        </div>
    </AsesiLayout>
</template>