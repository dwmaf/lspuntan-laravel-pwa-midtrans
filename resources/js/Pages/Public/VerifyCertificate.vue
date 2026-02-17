<script setup>
import { computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import TextInput from '@/Components/Input/TextInput.vue';
import SelectInput from '@/Components/Input/SelectInput.vue';
import PrimaryButton from '@/Components/Button/PrimaryButton.vue';
import SecondaryButton from '@/Components/Button/SecondaryButton.vue';
import InputError from '@/Components/Input/InputError.vue';
import { CheckCircle, AlertTriangle } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';
import { useFormat } from '@/Composables/useFormat.js';

const { formatDate } = useFormat();

const props = defineProps({
    skemas: Array,
    certificate: Object,
    input: Object,
    errors: Object,
});

const form = useForm({
    nomor_sertifikat: props.input?.nomor_sertifikat || '',
    skema_id: props.input?.skema_id || '',
});

const submit = () => {
    form.get(route('certificate.verify'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetForm = () => {
    router.visit(route('certificate.verify'), {
        method: 'get',
        preserveState: false,
    });
};

const isExpired = computed(() => {
    if (!props.certificate?.berlaku_hingga) return false;
    const expiryDate = new Date(props.certificate.berlaku_hingga);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return expiryDate < today;
});

const skemaOptions = computed(() => {
    return props.skemas.map(skema => ({
        value: skema.id,
        text: skema.nama_skema
    }));
});
</script>

<template>
    <div
        class="font-sans text-gray-900 dark:text-gray-100 antialiased min-h-screen bg-gray-100 dark:bg-gray-900 flex flex-col sm:justify-center items-center pt-6 sm:pt-0 pb-10">

        <div class="flex flex-col items-center justify-center mb-8 px-4">
            <Link href="/" class="mb-6 transform hover:scale-105 transition-transform duration-300">
                <img src="/logo-lsp.png" alt="Logo LSP Untan" class="w-auto h-24 drop-shadow-lg" />
            </Link>

            <h1 class="text-3xl font-extrabold text-center text-gray-900 dark:text-white tracking-tight">
                Verifikasi Sertifikat
            </h1>

            <h2 class="text-lg font-semibold text-center text-gray-600 dark:text-gray-300 mt-2">
                LSP Universitas Tanjungpura
            </h2>

            <p class="mt-4 text-center text-gray-500 dark:text-gray-400 max-w-lg text-sm leading-relaxed">
                Laman resmi ini digunakan untuk memverifikasi keaslian, validitas, dan status masa berlaku sertifikat
                kompetensi yang telah diterbitkan oleh BNSP dan disalurkan oleh Lembaga Sertifikasi Profesi (LSP) Universitas Tanjungpura.
            </p>
        </div>

        <div
            class="w-full sm:max-w-xl px-2 sm:px-6 py-8 bg-white dark:bg-gray-800 shadow-xl overflow-hidden sm:rounded-lg border border-gray-100 dark:border-gray-700 mx-4">


            <form @submit.prevent="submit">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 flex items-start gap-2">
                    Masukkan Nomor Sertifikat dan pilih Skema Sertifikasi untuk memverifikasi keaslian sertifikat
                    kompetensi Anda.
                </p>

                <div class="space-y-4">
                    <TextInput id="nomor_sertifikat" label="Nomor Sertifikat" type="text"
                        v-model="form.nomor_sertifikat" required autofocus placeholder="Contoh: No. 12345/LSP-UNTAN/..."
                        :error="form.errors.nomor_sertifikat" />

                    <SelectInput id="skema_id" label="Skema Sertifikasi" v-model="form.skema_id" :options="skemaOptions"
                        required placeholder="-- Pilih Skema Sertifikasi --" :error="form.errors.skema_id" />
                </div>

                <InputError class="mt-4" :message="errors.search" />

                <div class="flex items-center justify-end gap-3 mt-6">
                    <SecondaryButton v-if="certificate || form.nomor_sertifikat || form.skema_id" type="button"
                        @click="resetForm">
                        <span>Reset</span>
                    </SecondaryButton>

                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        <span>Verifikasi Sekarang</span>
                    </PrimaryButton>
                </div>
            </form>


            <div v-if="certificate" class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 animate-fade-in">
                <div class="flex flex-col items-center text-center">
                    <!-- Expired Certificate -->
                    <div v-if="isExpired"
                        class="p-3 bg-red-100 dark:bg-red-900/50 rounded-full ring-4 ring-red-50 dark:ring-red-900/30">
                        <AlertTriangle class="w-12 h-12 text-red-600 dark:text-red-400" />
                    </div>
                    <!-- Valid Certificate -->
                    <div v-else
                        class="p-3 bg-green-100 dark:bg-green-900/50 rounded-full ring-4 ring-green-50 dark:ring-green-900/30">
                        <CheckCircle class="w-12 h-12 text-green-600 dark:text-green-400" />
                    </div>

                    <h2 class="mt-4 text-xl font-bold text-gray-800 dark:text-white">
                        {{ isExpired ? 'Sertifikat Ditemukan (Masa Berlaku Habis)' : 'Sertifikat Valid & Terverifikasi'
                        }}
                    </h2>
                    <p class="text-sm font-medium mt-1"
                        :class="isExpired ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'">
                        {{ isExpired ?
                            'Sertifikat ini sudah melewati masa berlaku' : 'Data ditemukan dalam database kami' }}
                    </p>
                </div>

                <div class="mt-6 text-sm">
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Diberikan Kepada</dt>
                            <dd class="font-semibold text-gray-900 dark:text-white text-right">{{
                                certificate.asesi.student.user.name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Skema Sertifikasi</dt>
                            <dd class="text-gray-700 dark:text-gray-300 text-right">{{
                                certificate.asesi.sertification.skema.nama_skema }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Nomor Seri</dt>
                            <dd class="font-mono text-gray-700 dark:text-gray-300 text-right">{{
                                certificate.nomor_seri }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Nomor Sertifikat</dt>
                            <dd class="font-mono text-gray-700 dark:text-gray-300 text-right">{{
                                certificate.nomor_sertifikat }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Nomor Registrasi</dt>
                            <dd class="font-mono text-gray-700 dark:text-gray-300 text-right">{{
                                certificate.nomor_registrasi }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Tanggal Terbit</dt>
                            <dd class="text-gray-700 dark:text-gray-300 text-right">{{
                                formatDate(certificate.tanggal_terbit) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Berlaku Hingga</dt>
                            <dd class="text-right flex items-center gap-2 justify-end">
                                <span
                                    :class="isExpired ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-gray-700 dark:text-gray-300'">
                                    {{ formatDate(certificate.berlaku_hingga) }}
                                </span>
                                <span v-if="isExpired"
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300">
                                    Kadaluarsa
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</template>