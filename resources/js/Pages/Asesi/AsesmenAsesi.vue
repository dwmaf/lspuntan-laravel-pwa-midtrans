<script setup>
import AsesiLayout from "@/Layouts/AsesiLayout.vue";
import AsesiSertifikasiMenu from "@/Components/AsesiSertifikasiMenu.vue";
import CustomHeader from "@/Components/CustomHeader.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue";
import SingleFileInput from "@/Components/Input/SingleFileInput.vue";
import MultiFileInput from "@/Components/Input/MultiFileInput.vue";
import CreatorInfo from "@/Components/CreatorInfo.vue";
import { useForm, usePage, router } from "@inertiajs/vue3";
import { computed, ref } from 'vue';

const props = defineProps({
    sertification: Object,
    asesi: Object,
});

const isDeadlinePassed = computed(() => {
    if (!props.sertification.asesmen?.deadline) return false;
    return new Date() > new Date(props.sertification.asesmen.deadline);
});

// List of potential assessment documents
const assessmentFields = [
    { id: 'ak_1', label: 'AK-01 (Persetujuan Asesmen & Kerahasiaan)', skemaKey: 'format_ak_1' },
    { id: 'ak_2', label: 'AK-02 (Rekaman Asesmen Kompetensi)', skemaKey: 'format_ak_2' },
    { id: 'ak_3', label: 'AK-03 (Umpan Balik & Catatan Asesmen)', skemaKey: 'format_ak_3' },
    { id: 'ak_4', label: 'AK-04 (Laporan Asesmen)', skemaKey: 'format_ak_4' },
    { id: 'ac_1', label: 'AC-01 (Keputusan & Umpan Balik Asesmen)', skemaKey: 'format_ac_1' },
    { id: 'map_1', label: 'MAP-01 (Merencanakan Aktivitas & Proses Asesmen)', skemaKey: 'format_map_1' },
    { id: 'ia_1', label: 'IA-01 (Ceklis Observasi Aktivitas di Tempat Kerja)', skemaKey: 'format_ia_1' },
    { id: 'ia_2', label: 'IA-02 (Tugas Praktik Demonstrasi)', skemaKey: 'format_ia_2' },
    { id: 'ia_5', label: 'IA-05 (Pertanyaan Tertulis Pilihan Ganda)', skemaKey: 'format_ia_5' },
    { id: 'ia_6', label: 'IA-06 (Pertanyaan Tertulis Esai)', skemaKey: 'format_ia_6' },
    { id: 'ia_7', label: 'IA-07 (Pertanyaan Lisan)', skemaKey: 'format_ia_7' },
];

const activeFields = computed(() => {
    return assessmentFields.filter(field => props.sertification.skema[field.skemaKey]);
});

const submissionMode = ref(props.asesi.asesiasesmen ? 'view' : 'submit');

const form = useForm({
    ak_1: null,
    ak_2: null,
    ak_3: null,
    ak_4: null,
    ac_1: null,
    map_1: null,
    ia_1: null,
    ia_2: null,
    ia_5: null,
    ia_6: null,
    ia_7: null,
    delete_files_asesi: [],
    lampiran_lain: [],
    delete_files_collection: [],
});

const submit = () => {
    form.post(route('asesi.assessmen.update', [props.sertification.id, props.asesi.id]), {
        forceFormData: true,
        onSuccess: () => {
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

const getExistingFileUrl = (fieldId) => {
    if (!props.asesi.asesiasesmen) return '';
    const path = props.asesi.asesiasesmen[fieldId];
    return path ? `/storage/${path}` : '';
};

const getFiles = (collection, type) => {
    if (!collection) return [];
    return collection.filter(file => file.type === type);
};

const lampiranLainFiles = computed(() => getFiles(props.asesi.asesifiles, 'lampiran_lain'));

</script>

<template>
    <AsesiLayout>

        <CustomHeader :judul="`Instruksi Asesmen: ${sertification.skema?.nama_skema ?? ''}`" />
        <AsesiSertifikasiMenu :sertification="props.sertification" :asesi="props.asesi"
            :latest-transaction="props.asesi.latest_transaction" />

        <div class="max-w-7xl mx-auto">
            <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md mb-2">
                <div v-if="props.sertification.asesmen">
                    <!-- Info Pembuat Tugas -->
                    <div class="flex items-center gap-3 mb-4">
                        <CreatorInfo :name="sertification.asesmen?.name" :created-at="sertification.asesmen?.created_at"
                            :updated-at="sertification.asesmen?.updated_at" v-if="sertification.asesmen" class="mb-4" />
                    </div>

                    <!-- Rincian Tugas -->
                    <div v-html="props.sertification.asesmen.content.replace(/\n/g, '<br>')"
                        class="prose dark:prose-invert max-w-none text-sm text-gray-800 dark:text-gray-100"></div>


                    <div v-if="props.sertification.asesmen.deadline" class="mt-4 mb-4 p-3 rounded-md border"
                        :class="isDeadlinePassed ? 'bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-800' : 'bg-blue-50 border-blue-200 dark:bg-blue-900/20 dark:border-blue-800'">
                        <div class="flex items-center gap-2">
                            <svg v-if="isDeadlinePassed" xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-red-600 dark:text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-blue-600 dark:text-blue-400" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm font-medium"
                                :class="isDeadlinePassed ? 'text-red-800 dark:text-red-300' : 'text-blue-800 dark:text-blue-300'">
                                {{ isDeadlinePassed ? 'Batas waktu pengumpulan telah berakhir.' : 'Batas waktu: ' + new
                                    Date(props.sertification.asesmen.deadline).toLocaleString('id-ID', {
                                        dateStyle: 'long',
                                        timeStyle: 'short'
                                    }) + ' WIB' }}
                            </span>
                        </div>
                    </div>

                    <!-- Lampiran dari Asesor -->
                    <div v-if="sertification.asesmen.asesmenfiles.length > 0" class="mt-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Lampiran Soal/Materi:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-1">
                            <a v-for="file in sertification.asesmen.asesmenfiles" :key="file.id"
                                :href="`/storage/${file.path_file}`" target="_blank"
                                class="flex items-center justify-between gap-4 px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md text-xs">
                                <span
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-500 hover:underline truncate flex-1">{{
                                        file.path_file.split('/').pop() }}</span>
                            </a>
                        </div>
                    </div>

                    <!-- Form Pengumpulan Tugas Asesi -->
                    <div v-if="isDeadlinePassed"
                        class="mt-8 text-center p-6 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg">
                        <p class="text-gray-500 dark:text-gray-400">Anda tidak dapat mengumpulkan tugas karena batas
                            waktu telah berakhir.</p>
                    </div>

                    <div v-else class="mt-8 border-t dark:border-gray-700 pt-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200 tracking-wider">
                                {{ submissionMode === 'view' ? 'Berkas Tugas Terkumpul' : 'Unggah Berkas Tugas' }}
                            </h4>
                            <div v-if="submissionMode === 'view' && !isDeadlinePassed">
                                <SecondaryButton @click="showEditMode">
                                    Edit Pengiriman
                                </SecondaryButton>
                            </div>
                        </div>

                        <form v-if="submissionMode === 'submit'" @submit.prevent="submit" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div v-for="field in activeFields" :key="field.id">
                                    <SingleFileInput v-model="form[field.id]" :id="field.id"
                                        v-model:deleteList="form.delete_files_asesi" :delete-identifier="field.id"
                                        :label="field.label" :existing-file-url="getExistingFileUrl(field.id)"
                                        :error="form.errors[field.id]" required
                                        :template-url="props.sertification.skema[field.skemaKey] ? `/storage/${props.sertification.skema[field.skemaKey]}` : null"
                                        :disabled="submissionMode === 'view'" accept=".docx," />
                                </div>
                                <MultiFileInput v-model="form.lampiran_lain"
                                    v-model:deleteList="form.delete_files_collection"
                                    label="Lampiran lainnya (maks 5, ukuran file maksimal 5 MB)"
                                    :existing-files="lampiranLainFiles" :max-files="5"
                                    accept=".jpg,.png,.jpeg,.pdf,.docx,.pptx,.xlsx,.txt" :error="form.errors.lampiran_lain"
                                    :error-list="form.errors['lampiran_lain.0']" />
                            </div>

                            <div v-if="submissionMode === 'submit'" class="flex items-center gap-4 mt-6">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    {{ props.asesi.asesiasesmen ? 'Simpan Perubahan' : 'Kumpulkan Sekarang' }}
                                </PrimaryButton>
                                <SecondaryButton v-if="props.asesi.asesiasesmen" @click="showViewMode">
                                    Batal
                                </SecondaryButton>
                            </div>
                        </form>
                    </div>
                </div>
                <p v-else class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Asesor belum memberikan instruksi asesmen.
                </p>
            </div>
        </div>
    </AsesiLayout>
</template>