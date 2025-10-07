<!-- filepath: d:\Laravel-App\lsp-untan-laravel-pwa\resources\js\Components\AsesiSertifikasiMenu.vue -->
<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    sertificationId: Number,
    asesi: Object, // Kirim objek asesi dari parent
    latestTransaction: Object, // Kirim transaksi terakhir dari parent
});

const isDetailEnabled = !!props.asesi;
const isPembayaranEnabled = props.asesi && props.asesi.status === 'dilanjutkan_asesmen';
const isPengumumanEnabled = props.asesi && props.asesi.status === 'dilanjutkan_asesmen';
const isAsesmenEnabled = props.asesi &&
    props.latestTransaction &&
    props.latestTransaction.status === 'bukti_pembayaran_terverifikasi' &&
    props.asesi.status === 'dilanjutkan_asesmen';

const routeActive = (name) => route().current(name);
</script>

<template>
    <div>
        <div class="flex flex-wrap space-x-4 mt-1">
            <div>
                <template v-if="isDetailEnabled">
                    <Link :href="route('asesi.sertifikasi.applied.show', { sert_id: sertificationId, asesi_id: asesi.id })"
                        class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md dark:text-white text-gray-600">
                        Detail
                    </Link>
                    <div v-if="routeActive('asesi.sertifikasi.applied.show')" style="margin-top:-4px" class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
                </template>
                <template v-else>
                    <div class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase rounded-t-md dark:text-gray-200 text-slate-400">
                        <span class="w-4 h-4 inline-block">🔒</span>
                        Detail
                    </div>
                </template>
            </div>
            <!-- Pembayaran -->
            <div>
                <template v-if="isPembayaranEnabled">
                    <Link :href="route('asesi.payment.create', { sert_id: sertificationId, asesi_id: asesi.id })"
                        class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md dark:text-white text-gray-600">
                        Bayar
                    </Link>
                    <div v-if="routeActive('asesi.payment.create')" style="margin-top:-4px" class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
                </template>
                <template v-else>
                    <div class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase rounded-t-md dark:text-gray-200 text-slate-400">
                        <span class="w-4 h-4 inline-block">🔒</span>
                        Bayar
                    </div>
                </template>
            </div>
            <!-- Pengumuman -->
            <div>
                <template v-if="isPengumumanEnabled">
                    <Link :href="route('asesi.pengumuman.index', { sert_id: sertificationId, asesi_id: asesi.id })"
                        class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md dark:text-white text-gray-600">
                        Pengumuman
                    </Link>
                    <div v-if="routeActive('asesi.pengumuman.index')" style="margin-top:-4px" class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
                </template>
                <template v-else>
                    <div class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase rounded-t-md dark:text-gray-200 text-slate-400">
                        <span class="w-4 h-4 inline-block">🔒</span>
                        Pengumuman
                    </div>
                </template>
            </div>
            <!-- Asesmen -->
            <div>
                <template v-if="isAsesmenEnabled">
                    <Link :href="route('asesi.assessmen.index', { sert_id: sertificationId, asesi_id: asesi.id })"
                        class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase hover:bg-gray-100 hover:dark:bg-gray-700 rounded-t-md dark:text-white text-gray-600">
                        Asesmen
                    </Link>
                    <div v-if="routeActive('asesi.assessmen.index')" style="margin-top:-4px" class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
                </template>
                <template v-else>
                    <div class="flex items-center gap-2 px-4 py-3 font-semibold text-xs uppercase rounded-t-md dark:text-gray-200 text-slate-400">
                        <span class="w-4 h-4 inline-block">🔒</span>
                        Asesmen
                    </div>
                </template>
            </div>
        </div>
        <hr class="border-gray-200 dark:border-gray-700 mb-2">
    </div>
</template>