<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AdminSertifikasiMenu from "@/Components/AdminSertifikasiMenu.vue";
import CustomHeader from '@/Components/CustomHeader.vue';
import { Printer } from "lucide-vue-next";

const props = defineProps({
    sertification: Object,
});

</script>

<template>
    <AdminLayout>
        <CustomHeader judul="Laporan Sertifikasi" />
        <AdminSertifikasiMenu :sertification-id="props.sertification.id" />
        

        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">

                <div class="flex justify-end mb-6">
                    <a :href="route('admin.kelolasertifikasi.print_report', props.sertification.id)" target="_blank"
                        class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                        <Printer class="w-5 h-5" />
                        Cetak Laporan Resmi
                    </a>
                </div>

                <div class="max-w-4xl mx-auto bg-white border border-gray-200 p-6 shadow-sm">
                    <p class="text-center text-gray-500 italic">
                        (Klik tombol cetak di atas untuk membuka versi cetak resmi)
                    </p>
                </div>

                <div class="max-w-4xl mx-auto bg-white ">

                    <div class="border-b-2 border-gray-800 pb-4 mb-6 flex items-center gap-4">
                        <img src="/logo-lsp.png" alt="Logo LSP" class="h-20 w-auto" />
                        <div class="text-center flex-1">
                            <h1 class="text-2xl font-bold uppercase text-gray-900">LSP UNTAN</h1>
                            <p class="text-sm text-gray-600">Alamat Lengkap LSP, No Telepon, Website</p>
                            <p class="text-xs text-gray-500 italic">Licenced by BNSP</p>
                        </div>
                    </div>

                    <div class="text-center mb-8">
                        <h2 class="text-xl font-bold text-gray-900 underline decoration-2 underline-offset-4">
                            BERITA ACARA HASIL ASESMEN
                        </h2>
                        <p class="text-sm mt-1 text-gray-600">Nomor: ...../BA-LSP/..../202..</p>
                    </div>

                    <div class="mb-6 text-sm text-gray-900 border border-gray-300 p-4 rounded-sm">
                        <div class="grid grid-cols-[200px_10px_1fr] gap-y-2">
                            <div class="font-semibold">Skema Sertifikasi</div>
                            <div>:</div>
                            <div>{{ props.sertification.skema.nama_skema }}</div>

                            <div class="font-semibold">Nomor Skema</div>
                            <div>:</div>
                            <div>{{ props.sertification.skema.kode_skema || '-' }}</div>

                            <div class="font-semibold">Tanggal Pelaksanaan</div>
                            <div>:</div>
                            <div>{{ props.sertification.tgl_sertifikasi }}</div>

                            <div class="font-semibold">TUK</div>
                            <div>:</div>
                            <div>{{ props.sertification.tuk }}</div>

                            <div class="font-semibold">Nama Asesor</div>
                            <div>:</div>
                            <div v-for="asesor in props.sertification.asesors">{{ asesor.user?.name || '' }}</div>

                        </div>
                    </div>

                    <div class="mb-8">
                        <h4 class="text-md font-bold text-gray-900 mb-2">Rekapitulasi Hasil:</h4>
                        <table class="w-full border-collapse border border-gray-400 text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border border-gray-400 px-2 py-2 w-12 text-center">No</th>
                                    <th class="border border-gray-400 px-4 py-2 text-left">Nama Peserta</th>
                                    <th class="border border-gray-400 px-4 py-2 text-left">NIM / ID</th>
                                    <th class="border border-gray-400 px-4 py-2 text-center w-40">Rekomendasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(asesi, index) in props.sertification.asesis" :key="asesi.id">
                                    <td class="border border-gray-400 px-2 py-2 text-center">{{ index + 1 }}</td>
                                    <td class="border border-gray-400 px-4 py-2 font-medium text-gray-900">
                                        {{ asesi.student.user.name }}
                                    </td>
                                    <td class="border border-gray-400 px-4 py-2">
                                        {{ asesi.student.nim || '-' }}
                                    </td>
                                    <td class="border border-gray-400 px-4 py-2 text-center font-bold">
                                        {{ asesi.status_final === 'kompeten' ? 'Kompeten (K)' : 'Belum Kompeten (BK)' }}
                                    </td>
                                </tr>
                                <tr v-if="!props.sertification.asesis || props.sertification.asesis.length === 0">
                                    <td colspan="4" class="border border-gray-400 px-4 py-4 text-center text-gray-500">
                                        Tidak ada data peserta.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-12 grid grid-cols-2 gap-20 break-inside-avoid">
                        <div class="text-center">
                            <p class="mb-20">Mengetahui,<br><strong>Manajer Sertifikasi</strong></p>
                            <p class="font-bold underline text-gray-900">( Nama Manajer )</p>
                        </div>
                        <div class="text-center">
                            <p class="mb-20">Dibuat Oleh,<br><strong>Asesor Kompetensi</strong></p>
                            <p class="font-bold underline text-gray-900">{{ props.sertification.asesors.user?.name || ''
                                }}</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AdminLayout>
</template>