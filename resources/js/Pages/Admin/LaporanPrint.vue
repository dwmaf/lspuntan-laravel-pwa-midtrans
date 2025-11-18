<script setup>
import { onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    sertification: Object,
});

// Otomatis muncul dialog print saat halaman dibuka
onMounted(() => {
    window.print();
});
</script>

<template>
    <Head title="Cetak Berita Acara" />

    <div class="bg-white text-black min-h-screen p-8 font-sans">
        
        <div class="max-w-[210mm] mx-auto">
            
            <div class="border-b-2 border-gray-800 pb-4 mb-6 flex items-center gap-4">
                <img src="/logo-lsp.png" alt="Logo LSP" class="h-20 w-auto" /> 
                <div class="text-center flex-1">
                    <h1 class="text-2xl font-bold uppercase">LSP (NAMA LSP ANDA)</h1>
                    <p class="text-sm text-gray-600">Alamat Lengkap LSP, No Telepon, Website</p>
                    <p class="text-xs text-gray-500 italic">Licenced by BNSP</p>
                </div>
            </div>

            <div class="text-center mb-8">
                <h2 class="text-xl font-bold underline decoration-2 underline-offset-4">
                    BERITA ACARA HASIL ASESMEN
                </h2>
                <p class="text-sm mt-1 text-gray-600">Nomor: ...../BA-LSP/..../202..</p>
            </div>

            <div class="mb-6 text-sm border border-gray-400 p-4 rounded-sm">
                <div class="grid grid-cols-[200px_10px_1fr] gap-y-2">
                    <div class="font-semibold">Skema Sertifikasi</div>
                    <div>:</div>
                    <div>{{ props.sertification.skema?.nama_skema }}</div>

                    <div class="font-semibold">Nomor Skema</div>
                    <div>:</div>
                    <div>{{ props.sertification.skema?.kode_skema || '-' }}</div>

                    <div class="font-semibold">Tanggal Pelaksanaan</div>
                    <div>:</div>
                    <div>{{ props.sertification.tgl_sertifikasi }}</div>

                    <div class="font-semibold">TUK</div>
                    <div>:</div>
                    <div>{{ props.sertification.tuk }}</div>
                    
                    <div class="font-semibold">Nama Asesor</div>
                    <div>:</div>
                    <div>
                         <span v-for="(asesor, idx) in props.sertification.asesors" :key="asesor.id">
                            {{ asesor.user?.name }}<span v-if="idx < props.sertification.asesors.length - 1">, </span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h4 class="text-md font-bold mb-2">Rekapitulasi Hasil:</h4>
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
                            <td class="border border-gray-400 px-4 py-2 font-medium">
                                {{ asesi.student?.user?.name }}
                            </td>
                            <td class="border border-gray-400 px-4 py-2">
                                {{ asesi.student?.nim || '-' }} 
                            </td>
                            <td class="border border-gray-400 px-4 py-2 text-center font-bold">
                                {{ asesi.status === 'lulus_sertifikasi' ? 'Kompeten (K)' : 'Belum Kompeten (BK)' }}
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
                    <p class="font-bold underline">( Nama Manajer )</p>
                </div>
                <div class="text-center">
                    <p class="mb-20">Dibuat Oleh,<br><strong>Asesor Kompetensi</strong></p>
                    <p class="font-bold underline">
                        <span v-for="(asesor, idx) in props.sertification.asesors" :key="asesor.id">
                            {{ asesor.user?.name }}
                        </span>
                    </p>
                </div>
            </div>
            
            
        </div>
    </div>
</template>

<style>
@page {
    margin: 0;
    size: auto; 
}
body {
    background: white;
}
</style>