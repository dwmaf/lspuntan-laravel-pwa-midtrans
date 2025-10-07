<!-- filepath: d:\Laravel-App\lsp-untan-laravel-pwa\resources\js\Pages\Asesi\PembayaranAsesi.vue -->
<script setup>
import AsesiLayout from "@/Layouts/AsesiLayout.vue";
import AsesiSertifikasiMenu from "@/Components/AsesiSertifikasiMenu.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
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

// Form untuk upload bukti bayar
const form = useForm({
    bukti_bayar: null,
});

const submit = () => {
    form.post(route('asesi.sertifikasi.payment.store', { sert_id: props.sertification.id, asesi_id: props.asesi.id }), {
        onSuccess: () => form.reset('bukti_bayar'),
        preserveScroll: true,
    });
};

// Notifikasi
const notification = computed(() => usePage().props.flash?.message);

// Helper
const formatCurrency = (value) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
const formatDate = (dateString) => new Date(dateString).toLocaleString('id-ID', { dateStyle: 'long', timeStyle: 'short' });

</script>

<template>
    <AsesiLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Pembayaran Sertifikasi
            </h2>
        </template>

        <!-- Notifikasi -->
        <div v-if="notification" class="fixed top-20 right-4 text-sm px-4 py-2 rounded bg-green-600 text-white z-50">
            {{ notification }}
        </div>

        <AsesiSertifikasiMenu :sertification-id="props.sertification.id" :asesi="props.asesi" :latest-transaction="props.asesi.latest_transaction" />

        <div class="max-w-7xl mx-auto">
            <!-- Tampilan Jika Sudah Lunas -->
            <div v-if="isPaid" class="p-6 sm:p-8 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <div class="flex flex-col items-center text-center">
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                        <svg class="w-12 h-12 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-2xl font-bold text-gray-800 dark:text-white">Pembayaran Lunas</h3>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Terima kasih, pembayaran Anda telah kami terima dan verifikasi.</p>
                </div>
                <hr class="my-6 border-gray-200 dark:border-gray-700">
                <div class="space-y-4">
                    <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Rincian Transaksi</h4>
                    <dl class="space-y-2 text-sm">
                        <div v-if="asesi.latest_transaction.invoice_number" class="flex justify-between">
                            <dt class="text-gray-600 dark:text-gray-400">Nomor Invoice</dt>
                            <dd class="font-mono text-gray-900 dark:text-white">{{ asesi.latest_transaction.invoice_number }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600 dark:text-gray-400">Tanggal Pembayaran</dt>
                            <dd class="font-medium text-gray-900 dark:text-white">{{ formatDate(asesi.latest_transaction.updated_at) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600 dark:text-gray-400">Metode</dt>
                            <dd class="font-medium text-gray-900 dark:text-white">{{ asesi.latest_transaction.tipe === 'manual' ? 'Transfer Manual' : 'Online' }}</dd>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                            <dt class="text-base font-semibold text-gray-900 dark:text-white">Total Bayar</dt>
                            <dd class="text-base font-semibold text-gray-900 dark:text-white">{{ formatCurrency(sertification.harga) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Tampilan Form Pembayaran -->
            <div v-else class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md mb-2">
                <form @submit.prevent="submit" class="space-y-6">
                    <div v-if="sertification.rincian_pembayaran">
                        <!-- Info Pembuat Rincian -->
                        <div class="flex items-center gap-3 mb-4">
                            <!-- Avatar, Nama, Tanggal -->
                        </div>
                        <!-- Rincian Pembayaran -->
                        <div v-html="sertification.rincian_pembayaran" class="prose dark:prose-invert max-w-none text-sm text-gray-800 dark:text-gray-100 mb-2"></div>
                        <div class="flex">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mr-1">Biaya Sertifikasi:</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ formatCurrency(sertification.harga) }}</dd>
                        </div>

                        <!-- Input File -->
                        <div class="pt-4">
                            <InputLabel value="Unggah Bukti Pembayaran" />
                            <TextInput @input="form.bukti_bayar = $event.target.files[0]" type="file" required />
                            <!-- <input id="bukti_bayar" @input="form.bukti_bayar = $event.target.files[0]" type="file" class="mt-1 block w-full text-sm ..." required /> -->
                            <div v-if="form.progress" class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700 mt-2">
                                <div class="bg-blue-600 h-2.5 rounded-full" :style="{ width: form.progress.percentage + '%' }"></div>
                            </div>
                            <InputError :message="form.errors.bukti_bayar"/>
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