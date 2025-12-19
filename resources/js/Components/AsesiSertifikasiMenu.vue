<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Lock} from "lucide-vue-next";
import {
    IconInfoCircle,
    IconReceipt,
    IconSpeakerphone,
    IconChecklist
} from '@tabler/icons-vue';

const props = defineProps({
    sertificationId: Number,
    asesi: Object, 
    latestTransaction: Object,
});

const isDetailEnabled = !!props.asesi;
const isPembayaranEnabled = !!props.asesi;
const isPengumumanEnabled = !!props.asesi;
const isAsesmenEnabled = props.asesi &&
    props.latestTransaction &&
    props.latestTransaction.status === 'bukti_pembayaran_terverifikasi' &&
    (props.asesi.status === 'dilanjutkan_asesmen' || props.asesi.status === 'lulus_sertifikasi');

const routeActive = (name) => route().current(name);
</script>

<template>
    <div>
        <div class="flex flex-wrap gap-4 mt-1">
            <div>
                <template v-if="isDetailEnabled">
                    <Link :href="route('asesi.sertifikasi.applied.show', { sert_id: sertificationId, asesi_id: asesi.id })"
                        class="flex items-center gap-2 px-4 py-3 font-semibold text-sm hover:bg-gray-300 hover:dark:bg-gray-700 rounded-t-md dark:text-white text-gray-600">
                        <IconInfoCircle class="shrink-0 w-5 " />
                        Detail
                    </Link>
                    <div v-if="routeActive('asesi.sertifikasi.applied.show')" style="margin-top:-4px" class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
                </template>
                <template v-else>
                    <div class="flex items-center gap-2 px-4 py-3 font-semibold text-sm rounded-t-md dark:text-gray-200 text-slate-400">
                        <Lock class="shrink-0 w-5 text-gray-500 dark:text-gray-200" />
                        Detail
                    </div>
                </template>
            </div>
            <!-- Pembayaran -->
            <div>
                <template v-if="isPembayaranEnabled">
                    <Link :href="route('asesi.payment.create', { sert_id: sertificationId, asesi_id: asesi.id })"
                        class="flex items-center gap-2 px-4 py-3 font-semibold text-sm hover:bg-gray-300 hover:dark:bg-gray-700 rounded-t-md dark:text-white text-gray-600">
                        <IconReceipt class="shrink-0 w-5 " />
                        Bayar
                    </Link>
                    <div v-if="routeActive('asesi.payment.create')" style="margin-top:-4px" class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
                </template>
                <template v-else>
                    <div class="flex items-center gap-2 px-4 py-3 font-semibold text-sm rounded-t-md dark:text-gray-200 text-slate-400">
                        <Lock class="shrink-0 w-5 text-gray-500 dark:text-gray-200" />
                        Bayar
                    </div>
                </template>
            </div>
            <!-- Pengumuman -->
            <div>
                <template v-if="isPengumumanEnabled">
                    <Link :href="route('asesi.pengumuman.index', { sert_id: sertificationId, asesi_id: asesi.id })"
                        class="flex items-center gap-2 px-4 py-3 font-semibold text-sm hover:bg-gray-300 hover:dark:bg-gray-700 rounded-t-md dark:text-white text-gray-600">
                        <IconSpeakerphone class="shrink-0 w-5 " />
                        Pengumuman
                    </Link>
                    <div v-if="routeActive('asesi.pengumuman.index')" style="margin-top:-4px" class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
                </template>
                <template v-else>
                    <div class="flex items-center gap-2 px-4 py-3 font-semibold text-sm rounded-t-md dark:text-gray-200 text-slate-400">
                        <Lock class="shrink-0 w-5 text-gray-500 dark:text-gray-200" />
                        Pengumuman
                    </div>
                </template>
            </div>
            <!-- Asesmen -->
            <div>
                <template v-if="isAsesmenEnabled">
                    <Link :href="route('asesi.assessmen.index', { sert_id: sertificationId, asesi_id: asesi.id })"
                        class="flex items-center gap-2 px-4 py-3 font-semibold text-sm hover:bg-gray-300 hover:dark:bg-gray-700 rounded-t-md dark:text-white text-gray-600">
                        <IconChecklist class="shrink-0 w-5 " />
                        Asesmen
                    </Link>
                    <div v-if="routeActive('asesi.assessmen.index')" style="margin-top:-4px" class="w-full h-1 bg-gray-300 dark:bg-gray-700 rounded-t-md"></div>
                </template>
                <template v-else>
                    <div class="flex items-center gap-2 px-4 py-3 rounded-t-md ">
                        <Lock class="shrink-0 w-5 text-gray-500 dark:text-gray-200" />
                        <span class="font-semibold text-sm dark:text-gray-200 text-slate-400">
                            Asesmen
                        </span>
                    </div>
                </template>
            </div>
        </div>
        <hr class="border-gray-200 dark:border-gray-700 mb-2">
    </div>
</template>