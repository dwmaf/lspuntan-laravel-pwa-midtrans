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
import NumberInput from "@/Components/Input/NumberInput.vue";
import DateInput from "@/Components/Input/DateInput.vue";
import Multiselect from 'vue-multiselect';
import { useForm, usePage, router } from "@inertiajs/vue3";
import { ref, computed, watch, nextTick } from "vue";
const props = defineProps({
    sertification: Object,
    asesors: Array,
    skemas: Array,
});
const isEditing = ref(false);
const form = useForm({
    asesor_ids: [],
    skema_id: "",
    tgl_apply_dibuka: "",
    tgl_apply_ditutup: "",
    deadline_bayar: "",
    tuk: "",
    biaya: 0,
    status: "",
});
form.transform((data) => ({
    ...data,
    asesor_ids: data.asesor_ids.map(asesor => asesor.id)
}));
const availableAsesors = computed(() => {
    if (!form.skema_id) {
        return [];
    }
    const filtered = props.asesors.filter(asesor =>
        asesor.skemas.some(skema => skema.id == form.skema_id)
    );
    // Format untuk vue-multiselect
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
    nextTick(() => {
        // 'props.sertification.asesors' adalah array objek dari relasi
        // Kita perlu mengekstrak ID-nya
        const selectedAsesorIds = props.sertification.asesors.map(a => a.id);

        // 'form.asesor_ids' (v-model) harus diisi dengan OBJEK LENGKAP
        // dari 'availableAsesors' yang ID-nya cocok
        form.asesor_ids = availableAsesors.value.filter(
            option => selectedAsesorIds.includes(option.id)
        );
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
                    <div>
                        <InputLabel value="Pilih Skema Sertifikasi:" required />
                        <select v-model="form.skema_id" required
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option value="" disabled>-- Silahkan pilih skema --</option>
                            <option v-for="skema in props.skemas" :key="skema.id" :value="skema.id">
                                {{ skema.nama_skema }}
                            </option>
                        </select>
                        <InputError :message="form.errors.skema_id" />
                    </div>
                    <div v-if="form.skema_id">
                        <InputLabel value="Pilih Asesor (bisa lebih dari satu):" required />
                        <Multiselect v-model="form.asesor_ids" :options="availableAsesors" :multiple="true"
                            :close-on-select="false" placeholder="Cari atau pilih asesor" label="name" track-by="id"
                            :class="{ 'border-red-500': form.errors.asesor_ids }">
                            <template #noOptions>Tidak ada asesor tersedia untuk skema ini.</template>
                        </Multiselect>
                        <InputError :message="form.errors.asesor_ids" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel value="Tanggal Daftar Dibuka" />
                        <DateInput type="date" v-model="form.tgl_apply_dibuka" required />
                        <InputError :message="form.errors.tgl_apply_dibuka" />
                    </div>
                    <div id="tanggal_apply_ditutup">
                        <InputLabel value="Tanggal Daftar Ditutup" />
                        <DateInput type="date" v-model="form.tgl_apply_ditutup" required />
                        <InputError :message="form.errors.tgl_apply_ditutup" />
                    </div>
                    <div id="tanggal_bayar_ditutup">
                        <InputLabel value="Tanggal Bayar Ditutup" />
                        <DateInput v-model="form.deadline_bayar" required />
                        <InputError :message="form.errors.deadline_bayar" />
                    </div>
                    <div id="biaya_sertifikasi">
                        <InputLabel value="Biaya Sertifikasi" />
                        <p v-if="formattedHarga" class="text-sm font-medium text-gray-800 dark:text-gray-400">
                            {{ formattedHarga }}
                        </p>
                        <NumberInput min="0" type="number" v-model="form.biaya" required />
                        <InputError :message="form.errors.biaya" />
                    </div>
                    <div id="tuk">
                        <InputLabel value="Tempat Uji Sertifikasi" />
                        <TextInput type="text" v-model="form.tuk" required />
                        <InputError :message="form.errors.tuk" />
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
                        <DeleteButton v-if="props.sertification.status == 'berlangsung'" @click="destroy">Hapus
                        </DeleteButton>
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
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Pendaftaran Dibuka</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ formatDate(props.sertification.tgl_apply_dibuka) }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Pendaftaran Ditutup
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ formatDate(props.sertification.tgl_apply_ditutup) }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Batas Akhir Pembayaran</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ formatDateTime(props.sertification.deadline_bayar) }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Biaya Sertifikasi</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">Rp
                            {{ new Intl.NumberFormat('id-ID').format(props.sertification.biaya) }}
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
                        </dd>
                    </div>
                </div>
            </div>
        </div>

    </AdminLayout>
</template>
