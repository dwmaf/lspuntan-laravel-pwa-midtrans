<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AdminSertifikasiMenu from "@/Components/AdminSertifikasiMenu.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import EditButton from "../../Components/EditButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import ToggleSwitch from "@/Components/ToggleSwitch.vue";
import DateInput from "../../Components/DateInput.vue";
import NumberInput from "../../Components/NumberInput.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { ref, computed, onMounted } from "vue";
const props = defineProps({
    sertification: Object,
});
const isEditing = ref(false);
const form = useForm({
    content: "",
    biaya: "",
    deadline: "",
    is_published: false,
});
const edit = () => {
    form.content = props.sertification.payment_instruction.content;
    form.biaya = props.sertification.payment_instruction.biaya;
    form.deadline = props.sertification.payment_instruction.deadline;
    form.is_published = props.sertification.payment_instruction.published_at ? true : false;
    isEditing.value = true;
};
const submit = () => {
    form.patch(
        route("admin.sertifikasi.payment-desc.update", props.sertification.id),
        {
            onSuccess: () => {
                isEditing.value = false;
            },
        }
    );
};

const formattedHarga = computed(() => {
    if (!form.biaya) return "";
    const number = parseFloat(form.biaya);
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

        <!-- Mode Tampilan -->
         
        <div v-if="!isEditing" class="py-3 px-5 bg-white dark:bg-gray-800 rounded-lg shadow-md mb-2">
            <div class="flex justify-between items-center mb-2">
                <div class="flex items-center gap-3 mb-4">
                    <div class="shrink-0">
                        <svg class="h-10 w-10 text-gray-400 dark:text-gray-600 rounded-full bg-gray-200 dark:bg-gray-700 p-1"
                            fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <h5 v-if="props.sertification.payment_instruction"
                            class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                            {{
                                props.sertification.payment_instruction.name ?? 'Admin'
                            }}
                        </h5>
                        <div class="text-xs text-gray-400">
                            {{ new Date(props.sertification.payment_instruction.updated_at).toLocaleString() }}
                            <span v-if="props.sertification.payment_instruction.updated_at">(Diedit)</span>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <span v-if="props.sertification.payment_instruction?.published_at"
                        class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-300">
                        Dipublikasikan
                    </span>
                    <span v-else
                        class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-300">
                        Draft
                    </span>
                    <EditButton @click="edit">Edit</EditButton>
                </div>
            </div>

            <div v-if="props.sertification.payment_instruction.content"
                class="font-medium text-sm text-gray-800 dark:text-gray-100 mb-2">
                {{ props.sertification.payment_instruction.content }}
            </div>
            <p v-if="!props.sertification.payment_instruction.content"
                class="text-sm font-medium text-gray-700 dark:text-gray-300">
                Admin belum memberikan rincian pembayaran sehingga Asesi belum bisa mengunggah bukti bayar.
            </p>
            <div v-if="props.sertification.payment_instruction.content" class="flex mb-2">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mr-1">
                    Biaya Sertifikasi :
                </dt>
                <dd class="text-sm text-gray-900 dark:text-gray-100">
                    Rp
                    {{ new Intl.NumberFormat('id-ID').format(props.sertification.payment_instruction.biaya) }}
                </dd>
            </div>
            <div v-if="props.sertification.payment_instruction.content" class="flex">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mr-1">
                    Batas Akhir Pembayaran :
                </dt>
                <dd class="text-sm text-gray-900 dark:text-gray-100">
                    {{ new Date(props.sertification.payment_instruction.deadline).toLocaleString() }}
                    {{ props.sertification.payment_instruction.deadline }}
                </dd>
            </div>
        </div>
        <!-- Mode edit -->
        <div v-if="isEditing" class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md flex flex-col gap-4">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                    Edit Rincian Instruksi Pembayaran
                </h3>
            </div>
            <form @submit.prevent="submit" class="flex flex-col gap-4">
                <div>
                    <InputLabel value="Rincian Pembayaran" />
                    <textarea v-model="form.content" rows="8"
                        class="mt-1 w-full text-sm p-3 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100"></textarea>
                    <InputError :message="form.errors.content" />
                </div>
                <div>
                    <InputLabel value="Biaya Sertifikasi" />
                    <p v-if="formattedHarga" class="text-sm font-medium text-gray-800 dark:text-gray-400">{{
                        formattedHarga }}
                    </p>
                    <NumberInput min="0" v-model="form.biaya" required />
                    <InputError :message="form.errors.biaya" />
                </div>
                <div>
                    <InputLabel value="Tanggal Bayar Ditutup" />
                    <DateInput v-model="form.deadline" required />
                    <InputError :message="form.errors.deadline" />
                </div>
                <div>
                    <div class="flex items-center justify-between">
                        <InputLabel for="is_published" value="Publikasikan Instruksi?" />
                        <ToggleSwitch id="is_published" v-model="form.is_published" />
                    </div>
                    <InputError :message="form.errors.is_published" />
                </div>
                <div class="flex gap-2 items-center">
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Simpan
                    </PrimaryButton>
                    <SecondaryButton @click="isEditing = false">Batal</SecondaryButton>
                </div>
            </form>
        </div>

    </AdminLayout>
</template>
