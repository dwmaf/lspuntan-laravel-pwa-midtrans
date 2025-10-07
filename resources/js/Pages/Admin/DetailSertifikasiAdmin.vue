<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AdminSertifikasiMenu from "@/Components/AdminSertifikasiMenu.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import EditButton from "@/Components/EditButton.vue";
import DeleteButton from "@/Components/DeleteButton.vue";
import TextInput from "@/Components/TextInput.vue";
import NumberInput from "@/Components/NumberInput.vue";
import { useForm, usePage, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
const props = defineProps({
    sertification: Object,
    asesors: Array,
    skemas: Array,
});
const asesorSkemaOptions = computed(() => {
    const options = [];
    props.asesors.forEach(asesor => {
        asesor.skemas.forEach(skema => {
            options.push({
                value: `${asesor.id},${skema.id}`,
                text: `${asesor.user.name} - ${skema.nama_skema}`
            });
        });
    });
    return options;
});
const isEditing = ref(false);
const form = useForm({
    asesor_skema: "",
    tgl_apply_dibuka: "",
    tgl_apply_ditutup: "",
    tgl_bayar_ditutup: "",
    tuk: "",
    harga: 0,
    status: "",
});

const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};

// Fungsi helper untuk format tanggal dan waktu
const formatDateTime = (dateString) => {
    if (!dateString) return "N/A";
    return new Date(dateString).toLocaleString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
    }).replace('pukul', ',');
};

const edit = () => {
    form.asesor_skema = `${props.sertification.asesor_id},${props.sertification.skema_id}`;
    form.tgl_apply_dibuka = props.sertification.tgl_apply_dibuka;
    form.tgl_apply_ditutup = props.sertification.tgl_apply_ditutup;
    form.harga = props.sertification.harga;
    form.tuk = props.sertification.tuk;
    form.status = props.sertification.status;
    form.tgl_bayar_ditutup = props.sertification.tgl_bayar_ditutup ? new Date(props.sertification.tgl_bayar_ditutup)
        .toISOString()
        .split("T")[0]
        : "";
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
    if (confirm('Apakah Anda yakin ingin menghapus data sertifikasi ini? Ini tidak akan menghapus skema atau asesor terkait, hanya jadwal sertifikasi ini.')) {
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
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Sertifikasi
            </h2>
        </template>
        
        <AdminSertifikasiMenu :sertification-id="props.sertification.id" />
        <div class="max-w-7xl mx-auto">
            <div v-if="isEditing" class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <form @submit.prevent="submit" class="space-y-6">
                    <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Edit Sertifikasi</h2>
                    <div id="asesor dan skema">
                        <InputLabel value="Pilih Skema dan Asesor:" />
                        <select v-model="form.asesor_skema" required
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option value="" disabled>--Silahkan pilih asesor dan skema--</option>
                            <option v-for="option in asesorSkemaOptions" :key="option.value" :value="option.value">
                                {{ option.text }}
                            </option>
                            

                        </select>
                        <InputError :message="form.errors.asesor_skema" />
                    </div>
                    <div>
                        <InputLabel value="Tanggal Daftar Dibuka" />
                        <TextInput type="date" v-model="form.tgl_apply_dibuka" required />
                        <InputError :message="form.errors.tgl_apply_dibuka" />
                    </div>
                    <div id="tanggal_apply_ditutup">
                        <InputLabel value="Tanggal Daftar Ditutup" />
                        <TextInput type="date" v-model="form.tgl_apply_ditutup" required />
                        <InputError :message="form.errors.tgl_apply_ditutup" />
                    </div>
                    <div id="tanggal_bayar_ditutup">
                        <InputLabel value="Tanggal Bayar Ditutup" />
                        <TextInput type="date" v-model="form.tgl_bayar_ditutup" required />
                        <InputError :message="form.errors.tgl_bayar_ditutup" />
                    </div>
                    <div id="biaya_sertifikasi">
                        <InputLabel value="Biaya Sertifikasi" />
                        <p v-if="formattedHarga" class="text-sm font-medium text-gray-800 dark:text-gray-400">
                            {{ formattedHarga }}
                        </p>
                        <NumberInput min="0" type="number" v-model="form.harga" required />
                        <InputError :message="form.errors.harga" />
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
                        <PrimaryButton :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing">
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
                        <DeleteButton v-if="props.sertification.status == 'berlangsung'" @click="destroy">Hapus</DeleteButton>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Skema</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ props.sertification.skema.nama_skema }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Asesor</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ props.sertification.asesor.user.name }}
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
                            {{ formatDateTime(props.sertification.tgl_bayar_ditutup) }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Biaya Sertifikasi</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">Rp
                            {{ new Intl.NumberFormat('id-ID').format(props.sertification.harga) }}</dd>
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
                            <span v-if="!props.sertification.status === 'berlangsung'"
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
