<script setup>
import AsesiLayout from "@/Layouts/AsesiLayout.vue";
import AsesiSertifikasiMenu from "@/Components/AsesiSertifikasiMenu.vue";
import CustomHeader from "@/Components/CustomHeader.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue";
import SingleFileInput from "@/Components/Input/SingleFileInput.vue";
import CreatorInfo from "@/Components/CreatorInfo.vue";
import FileIcon from '@/Components/FileIcon.vue';
import FileCard from "@/Components/FileCard.vue";
import { CheckCircle, AlertTriangle, Clock } from 'lucide-vue-next';
import { useForm } from "@inertiajs/vue3";
import { computed, ref } from 'vue';

const props = defineProps({
    sertifikasi: Object,
    asesi: Object,
});

const isDeadlinePassed = computed(() => {
    if (!props.sertifikasi.asesmen?.deadline) return false;
    return new Date() > new Date(props.sertifikasi.asesmen.deadline);
});

const isStatusFinalLocked = computed (() => {
    return props.asesi.status_final !== 'belum_ditetapkan';
});

const cannotSubmit = computed(() => {
    return isDeadlinePassed.value || isStatusFinalLocked.value;
});

const deadlineStatus = computed(() => {
    const hasSubmitted = !!props.asesi.path_file_asesmen;
    const deadline = props.sertifikasi.asesmen?.deadline;

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
    form.post(route('asesi.assessmen.update', [props.sertifikasi.id, props.asesi.id]), {
        forceFormData: true,
        onSuccess: () => {
            form.reset();
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

        <CustomHeader :judul="`Instruksi Asesmen: ${sertifikasi.skema?.nama_skema ?? ''}`" />
        <AsesiSertifikasiMenu :sertifikasi="props.sertifikasi" :asesi="props.asesi" />

        <div class="max-w-3xl mx-auto">
            <div class="p-3 sm:p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md mb-2">
                <div v-if="props.sertifikasi.asesmen">
                    <div class="flex items-center gap-3 mb-4">
                        <CreatorInfo :name="sertifikasi.asesmen?.user?.name" :created-at="sertifikasi.asesmen?.created_at"
                            :updated-at="sertifikasi.asesmen?.updated_at" v-if="sertifikasi.asesmen" class="mb-4" />
                    </div>

                    <div v-html="props.sertifikasi.asesmen.content.replace(/\n/g, '<br>')"
                        class="prose dark:prose-invert max-w-none font-medium text-sm text-gray-800 dark:text-gray-100"></div>


                    <div class="mt-4 mb-4 p-3 rounded-md border" :class="deadlineStatus.colorClass">
                        <div class="flex items-center gap-2">
                            <CheckCircle v-if="asesi.path_file_asesmen" class="h-5 w-5"
                                :class="deadlineStatus.iconColor" />
                            <AlertTriangle v-else-if="isDeadlinePassed" class="h-5 w-5"
                                :class="deadlineStatus.iconColor" />
                            <Clock v-else class="h-5 w-5" :class="deadlineStatus.iconColor" />
                            <span class="text-sm font-medium" :class="deadlineStatus.textClass">
                                {{ deadlineStatus.text }}
                            </span>
                        </div>
                    </div>

                    <!-- Lampiran Tambahan dari Asesor -->
                    <div v-if="sertifikasi.asesmen?.path_file" class="mt-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Lampiran Tambahan:</h4>
                        <a :href="`/download/asesmen/${sertifikasi.asesmen.id}/path_file`" target="_blank"
                            class="text-sm flex items-center gap-2 group min-w-0">
                            <FileIcon :path="sertifikasi.asesmen.path_file" />
                            <span
                                class="text-blue-500 group-hover:text-blue-700 truncate group-hover:underline">
                                {{ sertifikasi.asesmen.path_file.split('/').pop() }}
                            </span>
                        </a>
                    </div>

                    <!-- Form Pengumpulan Tugas Asesi -->
                    <div v-if="cannotSubmit && !asesi.path_file_asesmen"
                        class="mt-8 text-center p-6 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg">
                        <p v-if="isDeadlinePassed" class="text-gray-500 dark:text-gray-400">Anda tidak dapat mengumpulkan tugas karena batas
                            waktu telah berakhir.</p>
                        <p v-else-if="isStatusFinalLocked" class="text-gray-500 dark:text-gray-400">Anda tidak dapat mengumpulkan tugas karena status final Anda telah ditetapkan.</p>
                    </div>

                    <div v-else class="mt-8 border-t dark:border-gray-700 pt-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200 tracking-wider uppercase">
                                {{ submissionMode === 'view' && asesi.path_file_asesmen ? 'Status Pengumpulan' :
                                    'Unggah Tugas Asesmen' }}
                            </h4>
                        </div>
                        <FileCard v-if="submissionMode === 'view' && asesi.path_file_asesmen"
                            :title="asesi.path_file_asesmen" :href="`/download/asesi/${asesi.id}/path_file_asesmen`"
                            status="Sudah Dikumpulkan" :editable="!cannotSubmit" @edit="showEditMode" />

                        <form v-if="submissionMode === 'submit'" @submit.prevent="submit"
                            class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-lg border border-dashed border-gray-300 dark:border-gray-700">
                            <SingleFileInput v-model="form.path_file_asesmen" id="path_file_asesmen"
                                v-model:deleteList="form.delete_files_asesi" delete-identifier="path_file_asesmen"
                                label="File asesmen anda"
                                :existing-file-url="asesi?.path_file_asesmen ? `/download/asesis/${asesi.id}/path_file_asesmen` : null"
                                :is-marked-for-deletion="form.delete_files_asesi.includes('path_file_asesmen')"
                                :error="form.errors.path_file_asesmen"
                                :required="!asesi?.path_file_asesmen || form.delete_files_asesi.includes('path_file_asesmen')"
                                :template-url="sertifikasi.skema.format_asesmen ? `/download/skema/${sertifikasi.skema.id}/format_asesmen` : null"
                                :disabled="submissionMode === 'view'" accept=".zip,.rar,.docx," />
                            <div class="flex items-center gap-4 mt-6">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    {{ props.asesi.path_file_asesmen ? 'Simpan' : 'Kumpulkan Sekarang' }}
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