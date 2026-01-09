<script setup>
import AsesiLayout from "@/Layouts/AsesiLayout.vue";
import AsesiSertifikasiMenu from "@/Components/AsesiSertifikasiMenu.vue";
import Header from "@/Components/CustomHeader.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import SingleFileInput from "@/Components/Input/SingleFileInput.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { computed } from 'vue';

const props = defineProps({
    sertification: Object,
    asesi: Object,
});

// Cek status pembayaran
const isPaid = computed(() => {
    const status = props.asesi.latest_transaction?.status;
    return status === 'bukti_pembayaran_terverifikasi' || status === 'paid';
});


const form = useForm({
    bukti_bayar: null,
    delete_files: [],
});

const submit = () => {
    form.post(route('asesi.payment.store', { sert_id: props.sertification.id, asesi_id: props.asesi.id }), {
        onSuccess: () => form.reset('bukti_bayar', 'delete_files'),
        preserveScroll: true,
    });
};


const formatCurrency = (value) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
const formatDate = (dateString) => new Date(dateString).toLocaleString('id-ID', { dateStyle: 'long', timeStyle: 'short' });
</script>

<template>
    <AsesiLayout>
        <Header judul="Instruksi Pembayaran" />
        <AsesiSertifikasiMenu :sertification="props.sertification" :asesi="props.asesi"
            :latest-transaction="props.asesi.latest_transaction" />

        <div class="max-w-7xl mx-auto">
            <!-- Tampilan Jika Sudah Lunas -->
            <div v-if="isPaid" class="p-6 sm:p-8 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <div class="flex flex-col items-center text-center">
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                        <svg class="w-12 h-12 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-2xl font-bold text-gray-800 dark:text-white">Bukti Pembayaran Terverifikasi
                    </h3>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Terima kasih, bukti pembayaran Anda telah kami
                        terima dan
                        verifikasi.</p>
                </div>
                <hr class="my-6 border-gray-200 dark:border-gray-700">
                <div class="space-y-4">
                    <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Rincian Transaksi</h4>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-600 dark:text-gray-400">Tanggal Upload Bukti Pembayaran</dt>
                            <dd class="font-medium text-gray-900 dark:text-white">{{
                                formatDate(asesi.latest_transaction.updated_at) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600 dark:text-gray-400">Metode</dt>
                            <dd class="font-medium text-gray-900 dark:text-white">{{ asesi.latest_transaction.tipe ===
                                'manual'
                                ? 'Transfer Manual' : 'Online' }}</dd>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                            <dt class="text-base font-semibold text-gray-900 dark:text-white">Total Bayar</dt>
                            <dd class="text-base font-semibold text-gray-900 dark:text-white">{{
                                formatCurrency(sertification.harga) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>


            <div v-else class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md mb-2">
                <form @submit.prevent="submit" class="space-y-6">
                    <div v-if="sertification.payment_instruction?.published_at">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="shrink-0">
                                <svg class="h-10 w-10 text-gray-400 dark:text-gray-600 rounded-full bg-gray-200 dark:bg-gray-700 p-1"
                                    fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <div>
                                <h5 v-if="props.sertification"
                                    class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                    {{
                                        props.sertification.payment_instruction.name ?? 'Admin'
                                    }}
                                </h5>
                                <div class="text-xs text-gray-400">
                                    {{ props.sertification.payment_instruction.created_at }}
                                    <span
                                        v-if="props.sertification.payment_instruction.updated_at != props.sertification.payment_instruction.created_at">(Diedit)</span>
                                </div>
                            </div>
                        </div>
                        <div v-html="sertification.payment_instruction.content"
                            class="prose dark:prose-invert max-w-none text-sm text-gray-800 dark:text-gray-100 mb-2">
                        </div>
                        <div class="flex">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mr-1">Biaya Sertifikasi:
                            </dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{
                                formatCurrency(sertification.payment_instruction.biaya)
                            }}</dd>
                        </div>


                        <div class="pt-4">
                            <SingleFileInput v-model="form.bukti_bayar" v-model:deleteList="form.delete_files"
                                delete-identifier="bukti_bayar" label="Unggah Bukti Pembayaran" is-label-required
                                :is-marked-for-deletion="form.delete_files.includes('bukti_bayar')"
                                :existing-file-url="asesi.latest_transaction ? `/storage/${asesi.latest_transaction.bukti_bayar}` : null"
                                accept=".pdf,.jpg,.jpeg,.png" :error="form.errors.bukti_bayar" required />
                        </div>
                        <div class="flex items-center gap-4 pt-2">
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Submit Bukti Pembayaran
                            </PrimaryButton>
                        </div>
                    </div>
                    <div v-else>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Admin belum memberikan rincian pembayaran. Anda belum bisa mengunggah bukti bayar.
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </AsesiLayout>
</template>