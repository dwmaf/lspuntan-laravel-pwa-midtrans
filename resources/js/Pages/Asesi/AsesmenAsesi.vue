<script setup>
import AsesiLayout from "@/Layouts/AsesiLayout.vue";
import AsesiSertifikasiMenu from "@/Components/AsesiSertifikasiMenu.vue";
import CustomHeader from "@/Components/CustomHeader.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue";
import SingleFileInput from "@/Components/Input/SingleFileInput.vue";
import CreatorInfo from "@/Components/CreatorInfo.vue";
import { FileText, CheckCircle, Download, Edit, AlertTriangle, Clock } from 'lucide-vue-next';
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

const deadlineStatus = computed(() => {
    const hasSubmitted = !!props.asesi.path_file_asesmen;
    const deadline = props.sertification.asesmen?.deadline;

    const format = (d) => new Date(d).toLocaleString('id-ID', { dateStyle: 'long', timeStyle: 'short' }) + ' WIB';
    if (hasSubmitted) {
        // Jika sudah kumpul, statusnya AMAN (Biru/Hijau) meskipun deadline lewat
        return {
            colorClass: 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-800',
            textClass: 'text-green-800 dark:text-green-300',
            iconColor: 'text-green-600 dark:text-green-400',
            text: deadline ? `Tugas telah dikumpulkan. Batas waktu: ${format(deadline)}` : 'Tugas telah dikumpulkan.'
        };
    } else if (deadline && isDeadlinePassed.value) {
        // Belum kumpul & Telat -> MERAH
        return {
            colorClass: 'bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-800',
            textClass: 'text-red-800 dark:text-red-300',
            iconColor: 'text-red-600 dark:text-red-400',
            text: `Batas waktu pengumpulan telah berakhir pada ${format(deadline)}`
        };
    } else if (deadline) {
        return {
            colorClass: 'bg-blue-50 border-blue-200 dark:bg-blue-900/20 dark:border-blue-800',
            textClass: 'text-blue-800 dark:text-blue-300',
            iconColor: 'text-blue-600 dark:text-blue-400',
            text: `Batas waktu: ${format(deadline)}`
        };
    } else {
        // Belum kumpul & Masih ada waktu -> BIRU
        return {
            colorClass: 'bg-blue-50 border-blue-200 dark:bg-blue-900/20 dark:border-blue-800',
            textClass: 'text-blue-800 dark:text-blue-300',
            iconColor: 'text-blue-600 dark:text-blue-400',
            text: 'Tidak ada batas waktu khusus untuk tugas ini.'
        };
    }
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
        <AsesiSertifikasiMenu :sertification="props.sertification" :asesi="props.asesi" />

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


                    <div class="mt-4 mb-4 p-3 rounded-md border"
                        :class="deadlineStatus.colorClass">
                        <div class="flex items-center gap-2">
                            <CheckCircle v-if="asesi.path_file_asesmen" class="h-5 w-5" :class="deadlineStatus.iconColor" />
                            <AlertTriangle v-else-if="isDeadlinePassed" class="h-5 w-5" :class="deadlineStatus.iconColor" />
                            <Clock v-else class="h-5 w-5" :class="deadlineStatus.iconColor" />
                            <span class="text-sm font-medium" :class="deadlineStatus.textClass">
                                {{ deadlineStatus.text }}
                            </span>
                        </div>
                    </div>

                    <!-- Lampiran dari Asesor -->
                    <div v-if="sertification.asesmen?.path_file" class="mt-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Lampiran Tambahan:</h4>
                        <a :href="`/storage/${sertification.asesmen.path_file}`" target="_blank"
                            class="mt-1 flex items-center justify-between gap-4 px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md text-xs">
                            <span
                                class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-500 hover:underline truncate flex-1">
                                {{ sertification.asesmen.path_file.split('/').pop() }}
                            </span>
                        </a>
                    </div>

                    <!-- Form Pengumpulan Tugas Asesi -->
                    <div v-if="isDeadlinePassed && !asesi.path_file_asesmen"
                        class="mt-8 text-center p-6 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg">
                        <p class="text-gray-500 dark:text-gray-400">Anda tidak dapat mengumpulkan tugas karena batas
                            waktu telah berakhir.</p>
                    </div>

                    <div v-else class="mt-8 border-t dark:border-gray-700 pt-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200 tracking-wider uppercase">
                                {{ submissionMode === 'view' && asesi.path_file_asesmen ? 'Status Pengumpulan' :
                                    'Unggah Tugas Asesmen' }}
                            </h4>
                        </div>
                        <div v-if="submissionMode === 'view' && asesi.path_file_asesmen"
                            class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">

                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                <div
                                    class="p-3 bg-white dark:bg-gray-700 rounded-lg shadow-sm border border-gray-100 dark:border-gray-600">
                                    <FileText class="w-8 h-8 text-blue-600 dark:text-blue-400" />
                                </div>

                                <!-- Info File -->
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate mb-1">
                                        {{ asesi.path_file_asesmen.split('/').pop() }}
                                    </p>
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                            <CheckCircle class="w-3.5 h-3.5" />
                                            Sudah Dikumpulkan
                                        </span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div
                                    class="flex items-center gap-2 w-full sm:w-auto mt-2 sm:mt-0 border-t sm:border-t-0 pt-3 sm:pt-0 border-gray-200 dark:border-gray-700">
                                    <!-- Tombol Download -->
                                    <a :href="`/storage/${asesi.path_file_asesmen}`" target="_blank"
                                        class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 w-full sm:w-auto">
                                        <Download class="w-4 h-4 sm:mr-2" />
                                        <span class="hidden sm:inline">Unduh</span>
                                    </a>

                                    <SecondaryButton v-if="!isDeadlinePassed" @click="showEditMode"
                                        class="w-full sm:w-auto justify-center">
                                        <Edit class="w-4 h-4 sm:mr-2" />
                                        <span class="hidden sm:inline">Ubah File</span>
                                        <span class="sm:hidden">Edit</span>
                                    </SecondaryButton>
                                </div>
                            </div>
                        </div>

                        <form v-if="submissionMode === 'submit'" @submit.prevent="submit"
                            class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-lg border border-dashed border-gray-300 dark:border-gray-700">
                            <SingleFileInput v-model="form.path_file_asesmen" id="path_file_asesmen"
                                v-model:deleteList="form.delete_files_asesi" delete-identifier="path_file_asesmen"
                                label="File asesmen anda"
                                :existing-file-url="asesi?.path_file_asesmen ? `/storage/${asesi.path_file_asesmen}` : null"
                                :error="form.errors.path_file_asesmen"
                                :required="!asesi?.path_file_asesmen || form.delete_files_asesi.includes('path_file_asesmen')"
                                :template-url="sertification.skema.format_asesmen ? `/storage/${sertification.skema.format_asesmen}` : null"
                                :disabled="submissionMode === 'view'" accept=".zip,.rar,.docx," />
                            <div class="flex items-center gap-4 mt-6">
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