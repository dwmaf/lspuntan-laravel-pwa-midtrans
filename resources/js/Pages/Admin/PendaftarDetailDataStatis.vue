<script setup>
import { computed } from 'vue';
import FileIcon from '../../Components/FileIcon.vue';
const props = defineProps({
    asesi: Object,
});

const formatDate = (dateString) => {
    if (!dateString) return 'Tidak diisi';
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};

const getFiles = (collection, type) => {
    if (!collection) return [];
    return collection.filter(file => file.type === type);
};

const kartuHasilStudiFiles = computed(() => getFiles(props.asesi.asesifiles, 'kartu_hasil_studi'));
const suratMagangFiles = computed(() => getFiles(props.asesi.asesifiles, 'surat_ket_magang'));
const sertifPelatihanFiles = computed(() => getFiles(props.asesi.asesifiles, 'sertif_pelatihan'));
const dokPendukungFiles = computed(() => getFiles(props.asesi.asesifiles, 'dok_pendukung_lain'));

const getFileName = (path) => {
    if (!path) return '';
    return path.split('/').pop();
}

</script>

<template>
    <div>
        <!-- Data Pribadi -->
        <h3 class="text-md font-semibold dark:text-gray-300 mb-2 border-b pb-1 border-gray-700">A. Data Pribadi</h3>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
            <div>
                <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Nama Lengkap</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ asesi.student?.user?.name || 'Tidak diisi'
                }}</dd>
            </div>
            <div>
                <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">No. KTP</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ asesi.student?.nik || 'Tidak diisi' }}</dd>
            </div>
            <div>
                <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Tempat, Tanggal Lahir</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ asesi.student?.tmpt_lhr || 'N/A' }}, {{
                    formatDate(asesi.student?.tgl_lhr) }}</dd>
            </div>
            <div>
                <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Jenis Kelamin</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ asesi.student?.kelamin || 'Tidak diisi' }}
                </dd>
            </div>
            <div>
                <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Kebangsaan</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                    {{ asesi.student?.kebangsaan || 'Tidak diisi' }}</dd>
            </div>
            <div>
                <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">No. Tlp HP (WA)</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                    {{ asesi.student?.user.no_tlp_hp || 'Tidak diisi' }}</dd>
            </div>
            <div>
                <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">No. Tlp Rumah</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                    {{ asesi.student?.no_tlp_rmh || 'Tidak diisi' }}</dd>
            </div>
            <div>
                <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">No. Tlp Kantor</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                    {{ asesi.student?.no_tlp_kntr || 'Tidak diisi' }}</dd>
            </div>
            <div>
                <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Kualifikasi
                    Pendidikan</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                    {{ asesi.student?.kualifikasi_pendidikan || 'Tidak diisi' }}</dd>
            </div>
        </dl>

        <!-- Data Pekerjaan -->
        <h3 class="text-md font-semibold dark:text-gray-300 mb-2 border-b pb-1 border-gray-700 mt-6">B. Data Pekerjaan
            Sekarang</h3>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
            <div>
                <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Nama
                    Institusi/Perusahaan</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                    {{ asesi.student?.nama_institusi || 'Tidak diisi' }}</dd>
            </div>
            <div>
                <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Jabatan</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                    {{ asesi.student?.jabatan || 'Tidak diisi' }}</dd>
            </div>
            <div>
                <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Alamat Kantor</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                    {{ asesi.student?.alamat_kantor || 'Tidak diisi' }}</dd>
            </div>
            <div>
                <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">No. Tlp/Email/Fax
                    Kantor
                </dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                    {{ asesi.student?.no_tlp_email_fax || 'Tidak diisi' }}</dd>
            </div>
        </dl>

        <!-- Data Sertifikasi -->
        <h3 class="text-md font-semibold dark:text-gray-300 mb-2 border-b pb-1 border-gray-700 mt-6">C. Data Sertifikasi
        </h3>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
            <div>
                <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Tujuan Sertifikasi</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ asesi.tujuan_sert || 'Tidak diisi' }}</dd>
            </div>
            <div>
                <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Mata Kuliah terkait</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                    <ul v-if="asesi.makulnilais?.length > 0">
                        <li v-for="makul in asesi.makulnilais" :key="makul.id">{{ makul.nama_makul }}: {{
                            makul.nilai_makul }}</li>
                    </ul>
                    <span v-else>Tidak diisi</span>
                </dd>
            </div>
        </dl>


        <h3 class="text-md font-semibold dark:text-gray-300 mb-2 border-b pb-1 border-gray-700 mt-6">D. Bukti
            Kelengkapan</h3>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
            <div v-for="file in [
                { label: 'Form APL.01', path: asesi.apl_1 },
                { label: 'Form APL.02', path: asesi.apl_2 },
                { label: 'Scan KTP', path: asesi.student?.foto_ktp },
                { label: 'Scan KTM', path: asesi?.foto_ktm },
                { label: 'Pasfoto', path: asesi.student?.pas_foto },
            ]" :key="file.label">
                <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">{{ file.label }}</dt>
                <dd class="mt-1 text-sm min-w-0">
                    <a v-if="file.path" :href="`/storage/${file.path}`" class="flex items-center gap-2 group min-w-0">
                        <FileIcon :path="file.path" />
                        <span class="text-blue-500 group-hover:text-blue-700 truncate group-hover:underline">{{
                            file.path.split('/').pop() }}</span>
                    </a>
                    <span v-else class="text-gray-900 dark:text-gray-100">Tidak ada file.</span>
                </dd>
            </div>

            <div v-for="fileGroup in [
                { label: 'Scan Kartu Hasil Studi', files: kartuHasilStudiFiles },
                { label: 'Scan Surat Keterangan Magang', files: suratMagangFiles },
                { label: 'Scan Sertifikat Pelatihan', files: sertifPelatihanFiles },
                { label: 'Dokumen Pendukung Lainnya', files: dokPendukungFiles },
            ]" :key="fileGroup.label">
                <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">{{ fileGroup.label }}</dt>
                <dd class="mt-1 text-sm min-w-0">
                    <ul v-if="fileGroup.files.length > 0" class="flex flex-col gap-2">
                        <li v-for="file in fileGroup.files" :key="file.id">
                            <a v-if="file.path_file" :href="`/storage/${file.path_file}`"
                                class="flex items-center gap-2 group min-w-0">
                                <FileIcon :path="file.path_file" />
                                <span class="text-blue-500 group-hover:text-blue-700 truncate group-hover:underline">{{
                                    file.path_file.split('/').pop() }}</span>
                            </a>
                        </li>
                    </ul>
                    <span v-else class="text-gray-900 dark:text-gray-100">Tidak ada file.</span>
                </dd>
            </div>
        </dl>
    </div>
</template>