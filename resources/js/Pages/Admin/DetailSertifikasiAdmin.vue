<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AdminSertifikasiMenu from "@/Components/AdminSertifikasiMenu.vue";
import ExportLink from "@/Components/Link/ExportLink.vue";
import CustomHeader from '@/Components/CustomHeader.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import InputError from "@/Components/Input/InputError.vue";
import InputLabel from "@/Components/Input/InputLabel.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue";
import EditButton from "@/Components/Button/EditButton.vue";
import TextInput from "@/Components/Input/TextInput.vue";
import SelectInput from "@/Components/Input/SelectInput.vue";
import NumberInput from "@/Components/Input/NumberInput.vue";
import DateInput from "@/Components/Input/DateInput.vue";
import Multiselect from '@/Components/Input/MultiSelect.vue';
import { useFormat } from "@/Composables/useFormat";
import { useForm } from "@inertiajs/vue3";
import { ref, computed, nextTick } from "vue";
import { FileText, CheckCircle, XCircle, AlertTriangle, UserCheck } from 'lucide-vue-next';
const props = defineProps({
    sertifikasi: Object,
    listAsesor: Array,
    listSkema: Array,
    activeSkemas: Array,
    isAsesor: Boolean,
});
const isEditing = ref(false);
const form = useForm({
    asesor_ids: [],
    skema_id: "",
    tgl_apply_dibuka: "",
    tgl_apply_ditutup: "",
    biaya: 0,
    tuk: "",
    status: "",
    no_rek: "",
    bank: "",
    atas_nama_rek: "",
    tgl_asesmen_mulai: "",
    tgl_asesmen_selesai: "",
});

const availableAsesors = computed(() => {
    if (!form.skema_id) {
        return [];
    }
    const filtered = props.listAsesor.filter(asesor =>
        asesor.skema.some(skema => skema.id == form.skema_id)
    );
    return filtered.map(asesor => ({
        id: asesor.id,
        name: asesor.user.name
    }));
});

const skemaOptions = computed(() => {
    // Start with active skemas
    let options = props.activeSkemas.map(skema => ({ value: skema.id, text: skema.nama_skema }));

    // Check if current skema is in the active list, if not add it so it's visible while editing
    const currentSkemaId = props.sertifikasi.skema_id;
    if (!options.find(o => o.value === currentSkemaId)) {
        const currentSkema = props.listSkema.find(s => s.id === currentSkemaId);
        if (currentSkema) {
            options.push({ value: currentSkema.id, text: `${currentSkema.nama_skema} (Non-Aktif)` });
        }
    }
    return options;
});

const edit = () => {
    form.skema_id = props.sertifikasi.skema_id;
    form.tgl_apply_dibuka = props.sertifikasi.tgl_apply_dibuka;
    form.tgl_apply_ditutup = props.sertifikasi.tgl_apply_ditutup;
    form.tuk = props.sertifikasi.tuk;
    form.status = props.sertifikasi.status;
    form.biaya = props.sertifikasi.biaya;
    form.deadline_bayar = props.sertifikasi.deadline_bayar;
    form.no_rek = props.sertifikasi.no_rek;
    form.bank = props.sertifikasi.bank;
    form.atas_nama_rek = props.sertifikasi.atas_nama_rek;
    form.tgl_asesmen_mulai = props.sertifikasi.tgl_asesmen_mulai;
    form.tgl_asesmen_selesai = props.sertifikasi.tgl_asesmen_selesai;
    nextTick(() => {
        // 'props.sertifikasi.asesor' adalah array objek dari relasi
        // perlu mengekstrak ID-nya
        const selectedAsesorIds = props.sertifikasi.asesor.map(a => a.id);
        form.asesor_ids = selectedAsesorIds;
    });
    isEditing.value = true;
};
const submit = () => {
    form.patch(
        route("admin.kelolasertifikasi.update", props.sertifikasi.id),
        {
            onSuccess: () => {
                isEditing.value = false;
            },
        }
    );
};

const { formatCurrency, formatDate, formatDateTime } = useFormat();
const formattedHarga = computed(() => {
    return formatCurrency(form.biaya);
});
</script>
<template>
    <AdminLayout>
        <CustomHeader :judul="`${sertifikasi.skema.nama_skema}: ${isEditing ? 'Edit' : 'Detail'} Sertifikasi`" />

        <AdminSertifikasiMenu :sertifikasi-id="props.sertifikasi.id" />
        <div class="max-w-7xl mx-auto gap-3 flex flex-col">
            <template v-if="isAsesor">
                <!-- Asesor: Stat Cards 2 Kolom -->
                <div class="bg-white dark:bg-gray-800 p-5 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                        <UserCheck class="w-5 h-5 text-green-500" />
                        Hasil Kelulusan Akhir — Asesi Anda
                    </h3>
                    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600">
                            <p class="text-xs text-gray-500 dark:text-gray-200 uppercase font-semibold">Kompeten</p>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ sertifikasi.asesi_asesor_kompeten_count }}</p>
                        </div>
                        <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600">
                            <p class="text-xs text-gray-500 dark:text-gray-200 uppercase font-semibold">Belum Kompeten</p>
                            <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">{{ sertifikasi.asesi_asesor_belum_kompeten_count }}</p>
                        </div>
                        <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600">
                            <p class="text-xs text-gray-500 dark:text-gray-200 uppercase font-semibold">Diskualifikasi</p>
                            <p class="text-2xl font-bold text-gray-600 dark:text-gray-400 mt-1">{{ sertifikasi.asesi_asesor_diskualifikasi_count }}</p>
                        </div>
                        <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600">
                            <p class="text-xs text-gray-500 dark:text-gray-200 uppercase font-semibold">Belum Ditetapkan</p>
                            <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">{{ sertifikasi.asesi_asesor_belum_ditetapkan_count }}</p>
                        </div>
                    </div>
                </div>
            </template>
            <template v-else-if="!isEditing">
                <!-- Admin: Box 1 + Box 2 -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                    <div class="bg-white dark:bg-gray-800 p-4 md:p-6 rounded-lg shadow-md">
                        <h4
                            class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase mb-4 flex items-center gap-2">
                            <FileText class="w-4 h-4 text-blue-500" /> Tahapan Sertifikasi Asesi
                        </h4>
                        <ul class="space-y-3">
                            <li class="flex justify-between items-center text-sm">
                                <span class="text-gray-600 dark:text-gray-400">1. Menunggu Verifikasi Admin</span>
                                <span
                                    class="font-bold bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 py-0.5 px-2.5 rounded-full">{{
                                        props.sertifikasi.asesi_menunggu_verifikasi_count }}</span>
                            </li>
                            <li class="flex justify-between items-center text-sm">
                                <span class="text-gray-600 dark:text-gray-400">2. Perlu Perbaikan Berkas (Oleh Asesi)</span>
                                <span
                                    class="font-bold bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 py-0.5 px-2.5 rounded-full">{{
                                        props.sertifikasi.asesi_perlu_perbaikan_count }}</span>
                            </li>
                            <li class="flex justify-between items-center text-sm">
                                <span class="text-gray-600 dark:text-gray-400">3. Berkas sudah lengkap tapi belum punya
                                    asesor</span>
                                <span
                                    class="font-bold bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200 py-0.5 px-2.5 rounded-full">{{
                                        props.sertifikasi.asesi_berkas_lengkap_count }}</span>
                            </li>
                            <li class="flex justify-between items-center text-sm">
                                <span class="text-gray-600 dark:text-gray-400">4. Asesor sudah ada tapi status final belum ditetapkan</span>
                                <span
                                    class="font-bold bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 py-0.5 px-2.5 rounded-full">{{
                                        props.sertifikasi.asesi_proses_asesmen_count }}</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-4 md:p-6 rounded-lg shadow-md">
                        <h4
                            class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase mb-4 flex items-center gap-2">
                            <UserCheck class="w-4 h-4 text-green-500" /> Hasil Kelulusan Akhir
                        </h4>
                        <ul class="space-y-3">
                            <li class="flex justify-between items-center text-sm">
                                <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                    <CheckCircle class="w-4 h-4 text-green-500" /> Kompeten (bersertifikat)
                                </div>
                                <span
                                    class="font-bold bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 py-0.5 px-2.5 rounded-full">
                                    {{ sertifikasi.asesi_kompeten_count - sertifikasi.asesi_kompeten_belum_sertifikat_count }}</span>
                            </li>
                            <li class="flex justify-between items-center text-sm">
                                <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                    <CheckCircle class="w-4 h-4 text-yellow-500" /> Kompeten (sertifikat belum terbit)
                                </div>
                                <span
                                    class="font-bold bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 py-0.5 px-2.5 rounded-full">
                                    {{ sertifikasi.asesi_kompeten_belum_sertifikat_count }}</span>
                            </li>
                            <li class="flex justify-between items-center text-sm">
                                <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                    <XCircle class="w-4 h-4 text-red-500" /> Belum Kompeten
                                </div>
                                <span
                                    class="font-bold bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 py-0.5 px-2.5 rounded-full">{{
                                        sertifikasi.asesi_belum_kompeten_count }}</span>
                            </li>
                            <li class="flex justify-between items-center text-sm">
                                <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                    <AlertTriangle class="w-4 h-4 text-gray-500" /> Diskualifikasi
                                </div>
                                <span
                                    class="font-bold bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 py-0.5 px-2.5 rounded-full">{{
                                        sertifikasi.asesi_diskualifikasi_count }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </template>

            <div v-if="isEditing" class="p-4 md:p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <form @submit.prevent="submit" class="space-y-6">
                    <SelectInput id="skema_id" label="Pilih Skema Sertifikasi:" v-model="form.skema_id"
                        :options="skemaOptions" :error="form.errors.skema_id" disabled
                        class="bg-gray-100 cursor-not-allowed opacity-75"
                        hint="*Skema sertifikasi tidak dapat diubah setelah jadwal dibuat untuk menjaga integritas data pendaftar." />
                    <Multiselect v-if="form.skema_id" id="asesor_ids" label="Pilih Asesor (bisa lebih dari satu):"
                        v-model="form.asesor_ids" :options="availableAsesors" :multiple="true"
                        placeholder="Cari atau pilih asesor" label-prop="name" value-prop="id"
                        :error="form.errors.asesor_ids" :class="{ 'border-red-500': form.errors.asesor_ids }"
                        required />
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <DateInput id="tgl_apply_dibuka" label="Tanggal Daftar Dibuka" v-model="form.tgl_apply_dibuka"
                            type="date" :error="form.errors.tgl_apply_dibuka" required />
                        <DateInput id="tgl_apply_ditutup" label="Tanggal Daftar Ditutup"
                            v-model="form.tgl_apply_ditutup" type="datetime-local"
                            :error="form.errors.tgl_apply_ditutup" required />
                        <DateInput id="tgl_asesmen_mulai" label="Tanggal Asesmen Dimulai"
                            v-model="form.tgl_asesmen_mulai" type="date" :error="form.errors.tgl_asesmen_mulai" />
                        <DateInput v-if="form.tgl_asesmen_mulai" id="tgl_asesmen_selesai"
                            label="Tanggal Asesmen Selesai" v-model="form.tgl_asesmen_selesai" type="date"
                            :error="form.errors.tgl_asesmen_selesai" />
                        <TextInput id="tuk" label="Tempat Uji Sertifikasi" v-model="form.tuk" type="text"
                            :error="form.errors.tuk" />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <NumberInput id="biaya" label="Biaya Sertifikasi" v-model="form.biaya" min="0"
                            :formatted-value="formattedHarga" :error="form.errors.biaya" required />
                        <TextInput id="bank" label="Nama Bank / E-Wallet" v-model="form.bank"
                            placeholder="Contoh: Bank BSI, GoPay" :error="form.errors.bank" required />
                        <TextInput id="no_rek" label="Nomor Rekening / Virtual Account" v-model="form.no_rek"
                            placeholder="Contoh: 1234567890" :error="form.errors.no_rek" required />
                        <TextInput id="atas_nama_rek" label="Atas Nama Rekening" v-model="form.atas_nama_rek"
                            placeholder="Contoh: LSP Untan" :error="form.errors.atas_nama_rek" required />
                    </div>
                    <div id="status" class="mb-2">
                        <InputLabel value="Status" />
                        <div class="flex gap-4">
                            <label class="inline-flex items-center cursor-pointer">
                                <input v-model="form.status" type="radio" value="berlangsung"
                                    class="cursor-pointer dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                    required>
                                <span class="ml-2 dark:text-gray-300">Sedang Berlangsung</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" v-model="form.status" value="selesai"
                                    class="cursor-pointer dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                    required>
                                <span class="ml-2 dark:text-gray-300">Selesai</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" v-model="form.status" value="dibatalkan"
                                    class="cursor-pointer dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                    required>
                                <span class="ml-2 dark:text-gray-300">Dibatalkan</span>
                            </label>
                        </div>
                        <InputError :message="form.errors.status" />
                    </div>

                    <div class="flex items-center gap-4 mt-4">
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Update
                        </PrimaryButton>
                        <SecondaryButton @click="isEditing = false">Batal</SecondaryButton>
                    </div>
                </form>
            </div>

            <div v-if="!isEditing" class="p-4 md:p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                    <div class="flex items-center space-x-3">
                        <EditButton v-if="!isAsesor" @click="edit">Edit</EditButton>
                        <ExportLink :href="route('admin.kelolasertifikasi.report.export_excel', props.sertifikasi.id)"
                            target="_blank">Export</ExportLink>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Skema</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ props.sertifikasi.skema.nama_skema }}</dd>
                    </div>
                    <div v-if="props.sertifikasi.asesor && props.sertifikasi.asesor.length > 0"
                        v-for="(asesor, index) in props.sertifikasi.asesor" :key="asesor.id">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Asesor {{ index + 1 }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ asesor.user.name }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Jadwal Pendaftaran</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ formatDate(props.sertifikasi.tgl_apply_dibuka) }} &ndash; {{
                                formatDateTime(props.sertifikasi.tgl_apply_ditutup) }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Jadwal Asesmen</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ formatDate(sertifikasi.tgl_asesmen_mulai) }}
                            {{ sertifikasi.tgl_asesmen_selesai ? ' - '
                                + formatDate(sertifikasi.tgl_asesmen_selesai) : '' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Biaya</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ formatCurrency(sertifikasi.biaya) }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">No. Rekening</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ sertifikasi.no_rek }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Bank/E-wallet</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ sertifikasi.bank }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Atas Nama Rekening</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ sertifikasi.atas_nama_rek }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ isAsesor ? 'Asesi Anda' : 'Jumlah Asesi' }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ isAsesor ? sertifikasi.asesi_asesor_count : sertifikasi.asesi_count }} terdaftar</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">TUK</dt>
                        <dd :class="['mt-1 text-sm', sertifikasi.tuk ? 'text-gray-900 dark:text-gray-100' : 'italic text-gray-500 dark:text-gray-400']">
                            {{ sertifikasi.tuk ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                        <dd class="mt-1 text-sm">
                            <StatusBadge v-if="sertifikasi.status === 'berlangsung'" variant="success">
                                Sedang Berlangsung
                            </StatusBadge>
                            <StatusBadge v-if="sertifikasi.status === 'selesai'" variant="primary">
                                Selesai
                            </StatusBadge>
                            <StatusBadge v-if="sertifikasi.status === 'dibatalkan'" variant="danger">
                                Dibatalkan
                            </StatusBadge>
                        </dd>
                    </div>
                </div>
            </div>
        </div>

    </AdminLayout>
</template>
