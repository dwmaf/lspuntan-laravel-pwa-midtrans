<script setup>
import { BookOpen, CalendarRange, DollarSign, MapPin, Users } from "lucide-vue-next";
import { IconChalkboardTeacher, IconPointFilled } from "@tabler/icons-vue";

const props = defineProps({
    sert: Object,
    showAsesor: { type: Boolean, default: false },
    showTuk: { type: Boolean, default: false },
    showAsesiCount: { type: Boolean, default: false },
});

const formatDate = (dateString) => {
    if (!dateString) return null;
    const date = new Date(dateString);
    const options = { day: 'numeric', month: 'short', year: 'numeric' };
    return new Intl.DateTimeFormat('id-ID', options).format(date);
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { 
        style: 'currency', 
        currency: 'IDR', 
        minimumFractionDigits: 0 
    }).format(value);
};
</script>

<template>
    <div class="relative overflow-hidden group bg-white p-6 rounded-lg dark:bg-gray-800 flex flex-col h-full transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_0_30px_rgba(37,99,235,0.2)] dark:hover:shadow-[0_0_30px_rgba(59,130,246,0.1)] border border-gray-100 dark:border-gray-700 hover:border-blue-400/40 dark:hover:border-blue-400/30">
        <!-- Top Border Hover Effect -->
        <div class="absolute top-0 left-0 right-0 h-1 bg-linear-to-r from-blue-400 via-cyan-400 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

        <div class="flex items-center gap-3">
            <BookOpen class="shrink-0 mt-1 w-6 h-6 text-blue-600 dark:text-blue-400" />
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 leading-tight">
                        {{ sert.skema.nama_skema }}
                    </h3>
                    <div class="flex flex-wrap gap-1">
                        <slot name="badges"></slot>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 space-y-3">
            <!-- Asesor Section -->
            <div v-if="showAsesor && sert.asesors?.length" class="space-y-2">
                <div v-for="(asesor, index) in sert.asesors" :key="asesor.id" class="flex items-center gap-2">
                    <IconChalkboardTeacher class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                    <p class="text-gray-600 text-sm dark:text-gray-300">
                        <span class="font-semibold">Asesor {{ index + 1 }}:</span> {{ asesor.user.name }}
                    </p>
                </div>
            </div>

            <!-- Common Info -->
            <div class="flex items-center gap-2">
                <CalendarRange class="shrink-0 w-4 h-4 text-gray-500 dark:text-gray-400" />
                <p class="text-gray-600 text-sm dark:text-gray-300">
                    <span class="font-semibold lg:inline hidden">Pendaftaran:</span>
                    {{ formatDate(sert.tgl_apply_dibuka) }} &ndash; {{ formatDate(sert.tgl_apply_ditutup) }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                <DollarSign class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                <p class="text-gray-600 text-sm dark:text-gray-300">
                    <span class="font-semibold">Biaya:</span> {{ formatCurrency(sert.biaya) }}
                </p>
            </div>

            <!-- TUK Section -->
            <div v-if="showTuk" class="flex items-center gap-2">
                <MapPin class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                <p class="text-gray-600 text-sm dark:text-gray-300">
                    <span class="font-semibold">TUK: </span>
                    <span :class="{ 'text-gray-400 italic': !sert.tuk }">
                        {{ sert.tuk ?? 'Belum Ditentukan' }}
                    </span>
                </p>
            </div>
        </div>

        <div v-if="showAsesiCount" class="flex items-center mt-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-800">
                <Users class="w-3.5 h-3.5" />
                <span class="text-xs font-semibold">{{ sert.asesis_count }} Asesi Terdaftar</span>
            </div>
        </div>
        
        <div class="mt-auto pt-4">
            <slot name="actions"></slot>
        </div>
    </div>
</template>