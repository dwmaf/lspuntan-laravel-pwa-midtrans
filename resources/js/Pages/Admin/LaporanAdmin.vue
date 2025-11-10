<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AdminSertifikasiMenu from "@/Components/AdminSertifikasiMenu.vue";
const props = defineProps({
    sertification: Object,
});
</script>
<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Laporan Sertifikasi
            </h2>
        </template>

        <AdminSertifikasiMenu :sertification-id="props.sertification.id" />

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{
                            props.sertification.skema.nama_skema
                            }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Laporan Kelulusan Peserta</p>
                    </div>
                    <button onclick="window.print()"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">
                        Cetak
                    </button>
                </div>

                <div
                    class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Pelaksanaan</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ props.sertification.tgl_sertifikasi
                            }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tempat Uji Kompetensi (TUK)
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ props.sertification.tuk }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Asesor</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{
                            props.sertification.asesors }}
                        </dd>
                    </div>
                </div>

                <div class="mt-8">
                    <h4 class="text-lg font-medium text-gray-900 dark:text-gray-200">Daftar Peserta Lulus</h4>
                    <div class="mt-4 flex flex-col">
                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                <div
                                    class="shadow overflow-hidden border-b border-gray-200 dark:border-gray-700 sm:rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    No</th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Nama Peserta</th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    NIM / ID</th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Status</th>
                                            </tr>
                                        </thead>
                                        <tbody
                                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">


                                            <tr v-for="(asesi, index) in props.sertification.asesis" :key="asesi.id">
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    {{ index + 1 }}</td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ asesi.student.user.name }}</td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Lulus
                                                    </span>
                                                </td>
                                            </tr>

                                            <tr v-if="!props.sertification.asesis">
                                                <td colspan="4"
                                                    class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                                    Belum ada peserta yang dinyatakan lulus untuk sertifikasi ini.
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </AdminLayout>
</template>