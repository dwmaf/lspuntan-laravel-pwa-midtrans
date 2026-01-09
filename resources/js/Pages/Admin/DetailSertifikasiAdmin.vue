<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AdminSertifikasiMenu from "@/Components/AdminSertifikasiMenu.vue";
import CustomHeader from '@/Components/CustomHeader.vue';
import InputError from "@/Components/Input/InputError.vue";
import InputLabel from "@/Components/Input/InputLabel.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue";
import EditButton from "@/Components/Button/EditButton.vue";
import DeleteButton from "@/Components/Button/DeleteButton.vue";
import TextInput from "@/Components/Input/TextInput.vue";
import SelectInput from "@/Components/Input/SelectInput.vue";
import NumberInput from "@/Components/Input/NumberInput.vue";
import DateInput from "@/Components/Input/DateInput.vue";
import Multiselect from '@/Components/Input/MultiSelect.vue';
import { useForm, usePage, router } from "@inertiajs/vue3";
import { ref, computed, watch, nextTick } from "vue";
const props = defineProps({
    sertification: Object,
    asesors: Array,
    skemas: Array,
    activeSkemas: Array,
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
// form.transform((data) => ({
//     ...data,
//     asesor_ids: data.asesor_ids.map(asesor => asesor.id)
// }));
const availableAsesors = computed(() => {
    if (!form.skema_id) {
        return [];
    }
    const filtered = props.asesors.filter(asesor =>
        asesor.skemas.some(skema => skema.id == form.skema_id)
    );
    return filtered.map(asesor => ({
        id: asesor.id,
        name: asesor.user.name
    }));
});
watch(() => form.skema_id, (newSkemaId) => {
    if (isEditing.value) {
        form.asesor_ids = [];
    }
});
const skemaOptions = computed(() => {
    // Start with active skemas
    let options = props.activeSkemas.map(skema => ({ value: skema.id, text: skema.nama_skema }));

    // Check if current skema is in the active list, if not add it so it's visible while editing
    const currentSkemaId = props.sertification.skema_id;
    if (!options.find(o => o.value === currentSkemaId)) {
        const currentSkema = props.skemas.find(s => s.id === currentSkemaId);
        if (currentSkema) {
            options.push({ value: currentSkema.id, text: `${currentSkema.nama_skema} (Non-Aktif)` });
        }
    }
    return options;
});

const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};


const formatDateTime = (dateString) => {
    if (!dateString) return "N/A";
    const formatted = new Date(dateString).toLocaleString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
    }).replace('pukul', ',').replace('.', ':');
    return `${formatted} WIB`;
};

const edit = () => {
    form.skema_id = props.sertification.skema_id;
    form.tgl_apply_dibuka = props.sertification.tgl_apply_dibuka;
    form.tgl_apply_ditutup = props.sertification.tgl_apply_ditutup;
    form.tuk = props.sertification.tuk;
    form.status = props.sertification.status;
    form.biaya = props.sertification.biaya;
    form.deadline_bayar = props.sertification.deadline_bayar;
    form.no_rek = props.sertification.no_rek;
    form.bank = props.sertification.bank;
    form.atas_nama_rek = props.sertification.atas_nama_rek;
    form.tgl_asesmen_mulai = props.sertification.tgl_asesmen_mulai;
    form.tgl_asesmen_selesai = props.sertification.tgl_asesmen_selesai;
    nextTick(() => {
        // 'props.sertification.asesors' adalah array objek dari relasi
        // Kita perlu mengekstrak ID-nya
        const selectedAsesorIds = props.sertification.asesors.map(a => a.id);

        // 'form.asesor_ids' (v-model) harus diisi dengan OBJEK LENGKAP
        // dari 'availableAsesors' yang ID-nya cocok
        // form.asesor_ids = availableAsesors.value.filter(
        //     option => selectedAsesorIds.includes(option.id)
        // );
        form.asesor_ids = selectedAsesorIds;
    });
    isEditing.value = true;
};
const submit = () => {
    form.patch(
        route("admin.kelolasertifikasi.update", props.sertification.id),
        {
            onSuccess: () => {
                isEditing.value = false;
            },
        }
    );
};
const destroy = () => {
    if (confirm('Apakah Anda yakin ingin menghapus data sertifikasi ini?Ini akan menghapus semua data asesi yang mendaftar ke jadwal sertifikasi ini. Ini tidak akan menghapus skema atau asesor terkait')) {
        router.delete(route('admin.kelolasertifikasi.destroy', props.sertification.id));
    }
};

const cancel = () => {
    if (confirm('Apakah Anda yakin ingin MEMBATALKAN sertifikasi ini? Status akan berubah menjadi Dibatalkan.')) {
        router.patch(route('admin.kelolasertifikasi.cancel', props.sertification.id));
    }
};

const formattedHarga = computed(() => {
    if (!form.harga) return "";
    const number = parseFloat(form.harga);
    if (isNaN(number)) return "";
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(number);
});
</script>
<template>
    <AdminLayout>
        <CustomHeader judul="Detail Sertifikasi" />

        <AdminSertifikasiMenu :sertification-id="props.sertification.id" />
        <div class="max-w-7xl mx-auto">
            <div v-if="isEditing" class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <form @submit.prevent="submit" class="space-y-6">
                    <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Edit Sertifikasi</h2>
                    <SelectInput id="skema_id" label="Pilih Skema Sertifikasi:" v-model="form.skema_id"
                        :options="skemaOptions" :error="form.errors.skema_id" required />
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
                            :error="form.errors.tuk" required />
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

            <div v-if="!isEditing" class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 sm:mb-0">
                        Detail Sertifikasi
                    </h3>

                    <div class="flex items-center space-x-3">
                        <EditButton @click="edit">Edit</EditButton>
                        <DeleteButton
                            v-if="props.sertification.status == 'berlangsung' && props.sertification.asesis_count == 0"
                            @click="destroy">Hapus
                        </DeleteButton>
                        <SecondaryButton
                            v-if="props.sertification.status == 'berlangsung' && props.sertification.asesis_count > 0"
                            @click="cancel" class="!bg-red-100 !text-red-700 !border-red-200 hover:!bg-red-200">
                            Batalkan
                        </SecondaryButton>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Skema</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ props.sertification.skema.nama_skema }}</dd>
                    </div>
                    <div v-if="props.sertification.asesors && props.sertification.asesors.length > 0"
                        v-for="(asesor, index) in props.sertification.asesors" :key="asesor.id">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Asesor {{ index + 1 }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ asesor.user.name }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Jadwal Pendaftaran</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ formatDate(props.sertification.tgl_apply_dibuka) }} &ndash; {{
                                formatDate(props.sertification.tgl_apply_ditutup) }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Jadwal Asesmen</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ formatDate(sertification.tgl_asesmen_mulai) }}
                            {{ sertification.tgl_asesmen_selesai ? ' - '
                                + formatDate(sertification.tgl_asesmen_selesai) : '' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">No. Rekening</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ sertification.no_rek }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Bank/E-wallet</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ sertification.bank }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Atas Nama Rekening</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ sertification.atas_nama_rek }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah Asesi</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ props.sertification.asesis_count }} terdaftar</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">TUK</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ props.sertification.tuk }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                        <dd class="mt-1 text-sm">

                            <span v-if="props.sertification.status === 'berlangsung'"
                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                Sedang Berlangsung
                            </span>
                            <span v-if="props.sertification.status === 'selesai'"
                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100">
                                Selesai
                            </span>
                            <span v-if="props.sertification.status === 'dibatalkan'"
                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200">
                                Dibatalkan
                            </span>
                        </dd>
                    </div>
                </div>
            </div>
        </div>

    </AdminLayout>
</template>
