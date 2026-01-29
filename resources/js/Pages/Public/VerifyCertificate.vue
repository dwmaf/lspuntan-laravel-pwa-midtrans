<script setup>
import { useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/Input/InputLabel.vue';
import TextInput from '@/Components/Input/TextInput.vue';
import PrimaryButton from '@/Components/Button/PrimaryButton.vue';
import CustomHeader from '@/Components/CustomHeader.vue';
import InputError from '@/Components/Input/InputError.vue';

const props = defineProps({
    sertifications: Array,
    certificate: Object,
    input: Object,
    errors: Object,
});

const form = useForm({
    nomor_sertifikat: props.input?.nomor_sertifikat || '',
    sertification_id: props.input?.sertification_id || '',
});

const submit = () => {
    form.get(route('certificate.verify'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};
</script>

<template>

    <CustomHeader judul="Verifikasi Sertifikat" />
    <div
        class="font-sans text-gray-900 dark:text-gray-100 antialiased min-h-screen bg-gray-100 dark:bg-gray-900 flex flex-col items-center pt-6 sm:pt-10">
        <div>
            <h1 class="text-2xl font-bold text-center">Verifikasi Sertifikat</h1>
        </div>

        <div
            class="w-full sm:max-w-xl mt-6 px-6 py-8 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">


            <form @submit.prevent="submit">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Masukkan Nomor Sertifikat dan pilih Skema Sertifikasi untuk memverifikasi keaslian sertifikat.
                </p>
                <div>
                    <InputLabel for="nomor_sertifikat" value="Nomor Sertifikat" />
                    <TextInput id="nomor_sertifikat" type="text" class="mt-1 block w-full"
                        v-model="form.nomor_sertifikat" required autofocus />
                </div>

                <div class="mt-4">
                    <InputLabel for="sertification_id" value="Skema Sertifikasi" />
                    <select id="sertification_id" v-model="form.sertification_id" required
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="" disabled>-- Pilih Skema --</option>
                        <option v-for="sert in sertifications" :key="sert.id" :value="sert.id">
                            {{ sert.skema.nama_skema }}
                        </option>
                    </select>
                </div>

                <InputError class="mt-2" :message="errors.search" />

                <div class="flex items-center justify-end mt-4">
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Verifikasi
                    </PrimaryButton>
                </div>
            </form>


            <div v-if="certificate" class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="flex flex-col items-center text-center">
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                        <svg class="w-12 h-12 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="mt-4 text-xl font-bold text-gray-800 dark:text-white">Sertifikat Terverifikasi</h2>
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
                            <dd class="text-gray-700 dark:text-gray-300 text-right">{{
                                formatDate(certificate.berlaku_hingga) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</template>