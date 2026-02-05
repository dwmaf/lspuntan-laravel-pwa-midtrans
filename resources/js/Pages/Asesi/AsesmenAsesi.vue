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

const submissionMode = ref(props.asesi.path_file_asesmen ? 'view' : 'submit');

const form = useForm({
    path_file_asesmen: null,
    delete_files_asesi: [],
    lampiran_lain: [],    
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

</script>

<template>
    <AsesiLayout>

        <CustomHeader :judul="`Instruksi Asesmen: ${sertification.skema?.nama_skema ?? ''}`" />
        <AsesiSertifikasiMenu :sertification="props.sertification" :asesi="props.asesi"/>

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
                    <div v-if="sertification.asesmen?.path_file" class="mt-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Lampiran Soal/Materi:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-1">
                            <a
                                :href="`/storage/${sertification.asesmen.path_file}`" target="_blank"
                                class="flex items-center justify-between gap-4 px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md text-xs">
                                <span
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-500 hover:underline truncate flex-1">
                                    {{ sertification.asesmen.path_file.split('/').pop() }}
                                        </span>
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
                            <div v-if="asesi.path_file_asesmen">
                                {{ asesi.path_file_asesmen.split('/').pop() }}
                            </div>
                            <div v-if="submissionMode === 'view' && !isDeadlinePassed">
                                <SecondaryButton @click="showEditMode">
                                    Edit Pengiriman
                                </SecondaryButton>
                            </div>
                        </div>

                        <form v-if="submissionMode === 'submit'" @submit.prevent="submit" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <SingleFileInput v-model="form.path_file_asesmen" id="path_file_asesmen"
                                    v-model:deleteList="form.delete_files_asesi" delete-identifier="path_file_asesmen"
                                    label="File asesmen anda" :existing-file-url="asesi?.file_path_asesmen ? `/storage/${asesi.file_path_asesmen}` : null"
                                    :error="form.errors.path_file_asesmen" :required="!asesi?.file_path_asesmen || form.delete_files.includes('file_path_asesmen')"
                                    :template-url="sertification.skema.format_asesmen ? `/storage/${sertification.skema.format_asesmen}` : null"
                                    :disabled="submissionMode === 'view'" accept=".zip,.rar,.docx," />
                            </div>

                            <div v-if="submissionMode === 'submit'" class="flex items-center gap-4 mt-6">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    {{ props.asesi.path_file_asesmen ? 'Simpan Perubahan' : 'Kumpulkan Sekarang' }}
                                </PrimaryButton>
                                <SecondaryButton v-if="props.asesi.path_file_asesmen" @click="showViewMode">
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