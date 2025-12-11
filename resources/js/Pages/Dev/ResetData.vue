<script setup>
import DevLayout from "@/Pages/Dev/DevLayout.vue";

import PrimaryLinkButton from "@/Components/PrimaryLinkButton.vue";
import { useForm, usePage, Link, router } from "@inertiajs/vue3";
import { MapPin, DollarSign, CalendarRange, BookOpen, HatGlasses, Users } from "lucide-vue-next";
import { IconChalkboardTeacher, IconPointFilled } from "@tabler/icons-vue";
import Multiselect from "@/Components/MultiSelect.vue";

const props = defineProps({
    sertifications: Array,
});
const formatDate = (dateString) => {
    if (!dateString) return null;
    const date = new Date(dateString);
    const options = { day: 'numeric', month: 'long', year: 'numeric' };
    return new Intl.DateTimeFormat('id-ID', options).format(date);
};
</script>

<template>
    <DevLayout>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div v-for="sert in sertifications" :key="sert.id" class="bg-white p-6 rounded-lg dark:bg-gray-800">
                <div class="flex items-center gap-2">
                    <BookOpen class="shrink-0 text-gray-700 dark:text-gray-200" />
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                        {{ sert.skema.nama_skema }}
                    </h3>
                </div>
                <div class="flex items-center mt-4 gap-2">
                    <HatGlasses class="shrink-0 w-4 h-4 text-gray-700 dark:text-gray-200" />
                    <p class=" text-gray-600 text-sm dark:text-gray-200">
                        <span class="font-semibold">
                            Id Sertifikasi:
                        </span>
                        {{ sert.id }}
                    </p>
                </div>
                <div v-for="(asesor, index) in sert.asesors" :key="asesor.id" class="flex items-center mt-4 gap-2">
                    <IconChalkboardTeacher class="w-4 h-4 text-gray-700 dark:text-gray-200" />
                    <p class=" text-gray-600 text-sm dark:text-gray-200">
                        <span class="font-semibold">Asesor {{ index + 1 }} : {{ asesor.user.name }}</span>
                    </p>
                </div>
                <div class="flex items-center mt-4 gap-2">
                    <CalendarRange class="shrink-0 w-4 h-4 text-gray-700 dark:text-gray-200" />
                    <p class=" text-gray-600 text-sm dark:text-gray-200">
                        Pendaftaran:
                        {{ formatDate(sert.tgl_apply_dibuka) }} &ndash;
                        {{ formatDate(sert.tgl_apply_ditutup) }}
                    </p>
                </div>
                <div class="flex items-center mt-4 gap-2">
                    <DollarSign class="w-4 h-4 text-gray-700 dark:text-gray-200" />
                    <p class=" text-gray-600 text-sm dark:text-gray-200">
                        Biaya: Rp
                        {{
                            new Intl.NumberFormat(
                                "id-ID"
                            ).format(sert.biaya)
                        }}
                    </p>
                </div>
                <div class="flex items-center mt-4">
                    <MapPin class="w-4 h-4 text-gray-700 dark:text-gray-200" />
                    <p class="ml-2 text-gray-600 text-sm dark:text-gray-200">
                        <span class="font-semibold">TUK:</span>
                        {{ sert.tuk }}
                    </p>
                </div>
                <div class="flex items-center mt-4">
                    <Link :href="route('dev.sertification.list.asesis',sert.id)"
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-800">
                        <Users class="w-3.5 h-3.5" />
                        <span class="text-xs font-semibold">{{ sert.asesis_count }} Asesi Terdaftar</span>
                    </Link> 
                </div>
                <div class="mt-4">
                    <PrimaryLinkButton :href="route(
                        'dev.sertification.show',
                        sert.id
                    )
                        ">Detail
                    </PrimaryLinkButton>
                </div>
            </div>


        </div>
    </DevLayout>
</template>
