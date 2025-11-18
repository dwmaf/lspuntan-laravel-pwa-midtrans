<script setup>
import AsesiLayout from "@/Layouts/AsesiLayout.vue";
import AsesiSertifikasiMenu from "@/Components/AsesiSertifikasiMenu.vue";
import Header from "@/Components/CustomHeader.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import MultiFileInput from "../../Components/MultiFileInput.vue";
import { useForm, usePage, router } from "@inertiajs/vue3";
import { computed, ref } from 'vue';

const props = defineProps({
    sertification: Object,
    asesi: Object,
});

const submissionMode = ref(props.asesi.asesiasesmenfiles.length > 0 ? 'view' : 'submit');

const form = useForm({
    delete_files: [],
    newFiles: [],
});

const submit = () => {
    form.post(route('asesi.assessmen.update', [props.sertification.id, props.asesi.id]), {
        onSuccess: () => {
            form.reset('newFiles');
            submissionMode.value = 'view';
        },

    });
};

const showEditMode = () => {
    submissionMode.value = 'submit';
}

const showViewMode = () => {
    form.reset();
    submissionMode.value = 'view';
}

</script>

<template>
    <AsesiLayout>
        
        <Header judul="Instruksi Asesmen"/>
        <AsesiSertifikasiMenu :sertification-id="props.sertification.id" :asesi="props.asesi"
            :latest-transaction="props.asesi.latest_transaction" />

        <div class="max-w-7xl mx-auto">
            <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md mb-2">
                <div v-if="props.sertification.rincian_tugas_asesmen">
                    <!-- Info Pembuat Tugas -->
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
                                <div v-if="props.sertification.pembuatrinciantugasasesmen">
                                    {{ props.sertification.pembuatrinciantugasasesmen.name }}
                                </div>
                                <div v-else>
                                    Admin
                                </div>
                            </h5>
                            <div v-if="props.sertification.tanggal_rincian_asesmen_dibuat_formatted" class="text-xs text-gray-400">
                                {{ props.sertification.tanggal_rincian_asesmen_dibuat_formatted }}
                            </div>
                        </div>
                    </div>

                    <!-- Rincian Tugas -->
                    <div v-html="props.sertification.rincian_tugas_asesmen"
                        class="prose dark:prose-invert max-w-none text-sm text-gray-800 dark:text-gray-100 mb-2"></div>

                    <!-- Lampiran dari Asesor -->
                    <div v-if="sertification.asesmenfiles.length > 0" class="mt-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Lampiran Tugas:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-1">
                            <a v-for="file in sertification.asesmenfiles" :key="file.id"
                                :href="`/storage/${file.path_file}`" target="_blank"
                                class="flex items-center justify-between gap-4 px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md text-xs">
                                <span
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-500 hover:underline truncate flex-1">{{
                                        file.path_file.split('/').pop() }}</span>
                            </a>
                        </div>
                    </div>

                    <div v-if="submissionMode === 'view'">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tugas Terkumpul:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
                            <div v-for="file in props.asesi.asesiasesmenfiles" :key="file.id"
                                class="flex items-center justify-between gap-4 px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md text-xs">
                                <a :href="`/storage/${file.path_file}`" target="_blank"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-500 hover:underline truncate flex-1">
                                    {{ file.path_file.split('/').pop() }}
                                </a>
                            </div>
                        </div>
                        <div class="mt-3">
                            <SecondaryButton @click="showEditMode">
                                Batalkan Pengiriman
                            </SecondaryButton>
                        </div>
                    </div>
                    <!-- Form Pengumpulan Tugas Asesi -->
                    <form v-else @submit.prevent="submit"
                        class="border border-gray-300 dark:border-gray-600 rounded-md p-4 mt-10">
                        <MultiFileInput v-model="form.newFiles" v-model:deleteList="form.delete_files"
                            :existing-files="asesi.asesiasesmenfiles"
                            label="Kumpulkan Tugas Anda (maks 5 file, total ukuran maks 5 MB)"
                            accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xlx,.ppt,.pptx"
                            :error="form.errors.newFiles || form.errors.delete_files"
                            :error-list="form.errors['newFiles.0']"
                            :required="asesi.asesiasesmenfiles.length === 0 || form.newFiles.length === 0" />
                        <div class="flex items-center gap-4 mt-4">
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Kumpulkan
                            </PrimaryButton>
                            <SecondaryButton v-if="props.asesi.asesiasesmenfiles.length > 0" @click="showViewMode">
                                Batal
                            </SecondaryButton>
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