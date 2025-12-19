<script setup>
import DevLayout from "@/Pages/Dev/DevLayout.vue";

const props = defineProps({
    sertification: Object,
});

</script>

<template>
    <DevLayout>

        <div class="bg-white p-6 rounded-lg dark:bg-gray-800">
            <h3 class="text-xl font-bold mb-2">Sertifikasi {{ sertification.skema.nama_skema }}, Id: {{ sertification.id }}</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full ">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Id user
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Id student
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Id asesi
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Nama Asesi
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Transaksi
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Email
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 ">

                        <tr v-if="props.sertification.asesis.length > 0"
                            v-for="(asesi, index) in props.sertification.asesis" :key="asesi.id" class="">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ asesi.student.user.id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ asesi.student.id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ asesi.id }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ asesi.student.user.name ?? 'Nama Tidak Tersedia' }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ asesi.transaction }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ asesi.student.user.email }}
                            </td>
                        </tr>
                        <tr v-else>
                            <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                Tidak ada data pendaftar untuk skema ini.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </DevLayout>
</template>
