<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AdminSertifikasiMenu from "@/Components/AdminSertifikasiMenu.vue";
import CustomHeader from '@/Components/CustomHeader.vue';
import InputError from "@/Components/Input/InputError.vue";
import InputLabel from "@/Components/Input/InputLabel.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import AddButton from "@/Components/Button/AddButton.vue";
import EditButton from "@/Components/Button/EditButton.vue";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue";
import DateInput from "@/Components/Input/DateInput.vue";
import Modal from "@/Components/Modal.vue";
import Checkbox from "@/Components/Input/Checkbox.vue";
import NumberInput from "@/Components/Input/NumberInput.vue";
import DangerButton from "@/Components/Button/DangerButton.vue";
import { useForm, usePage, router } from "@inertiajs/vue3";
import { ref, computed, onMounted } from "vue";
const props = defineProps({
    sertification: Object,
});
const isEditing = ref(false);
const isPreviewing = ref(false);
const form = useForm({
    content: "",
    biaya: "",
    deadline_bayar: "",
    send_notification: true,
});
const edit = () => {
    form.content = props.sertification.payment_instruction?.content || 'Isi instruksi pembayaran di sini';
    form.biaya = props.sertification.biaya;
    form.deadline_bayar = props.sertification.deadline_bayar;
    form.send_notification = !props.sertification.payment_instruction;
    isEditing.value = true;
};
const cancelEdit = () => {
    isEditing.value = false;
    form.reset();
    form.clearErrors();
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
const deleteInstruction = () => {
    if (confirm('Apakah Anda yakin ingin menghapus instruksi pembayaran ini? bukti pembayaran yang sudah dikumpulkan asesi akan TETAP TERSIMPAN.')) {
        router.delete(route('admin.sertifikasi.payment-desc.destroy', props.sertification.id), {
            onSuccess: () => {
                cancelEdit();
            },
        });
    }
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

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    const now = new Date();

    const isToday = date.getDate() === now.getDate() &&
        date.getMonth() === now.getMonth() &&
        date.getFullYear() === now.getFullYear();

    const isSameYear = date.getFullYear() === now.getFullYear();

    if (isToday) {
        return new Intl.DateTimeFormat('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        }).format(date);
    }

    if (isSameYear) {
        return new Intl.DateTimeFormat('id-ID', {
            day: 'numeric',
            month: 'long'
        }).format(date);
    }

    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    }).format(date);
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
</script>
<template>
    <AdminLayout>
        <CustomHeader judul="Instruksi Pembayaran" />

        <AdminSertifikasiMenu :sertification-id="props.sertification.id" />

        <!-- Mode Tampilan -->
        <div v-if="!isEditing">
            <div v-if="!sertification.payment_instruction" class="flex flex-col gap-2">
                <AddButton class="self-end" @click="edit">Buat Instruksi</AddButton>
                <div class="py-3 px-5 bg-white dark:bg-gray-800 rounded-lg shadow-md mb-2">
                    <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                        Asesor belum memberikan instruksi pembayaran, buat instruksi pembayaran agar asesi bisa
                        mengumpulkan
                        bukti pembayarannya
                    </p>
                </div>
            </div>
            <div v-else class="py-3 px-5 bg-white dark:bg-gray-800 rounded-lg shadow-md mb-2">
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
                            <h5 v-if="sertification.payment_instruction"
                                class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                {{
                                    sertification.payment_instruction.name ?? 'Admin'
                                }}
                            </h5>
                            <div class="text-xs text-gray-400">
                                {{ formatDate(sertification.payment_instruction.created_at) }}
                                <span
                                    v-if="sertification.payment_instruction.created_at !== sertification.payment_instruction.updated_at">(Diedit)</span>
                            </div>
                        </div>
                    </div>

                    <EditButton @click="edit">Edit</EditButton>
                </div>

                <div class="font-medium text-sm text-gray-800 dark:text-gray-100 mb-2 whitespace-pre-wrap">
                    {{ sertification.payment_instruction.content }}
                </div>
                <div class="flex mb-2">
                    <dt class="text-sm font-medium text-gray-800 dark:text-gray-400 mr-1">
                        Biaya Sertifikasi :
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-100">
                        Rp
                        {{ new Intl.NumberFormat('id-ID').format(sertification.biaya) }}
                    </dd>
                </div>
                <div class="flex">
                    <dt class="text-sm font-medium text-gray-800 dark:text-gray-400 mr-1">
                        Batas Akhir Pembayaran :
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-100">
                        {{ formatDateTime(sertification.deadline_bayar) }}
                    </dd>
                </div>
            </div>
        </div>
        <!-- Mode edit -->
        <div v-if="isEditing" class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md flex flex-col gap-4">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                    {{ sertification.payment_instruction ?
                        "Edit Rincian Instruksi Pembayaran" : "Buat Rincian Instruksi Pembayaran" }}
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
                    <DateInput v-model="form.deadline_bayar" required />
                    <InputError :message="form.errors.deadline_bayar" />
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex gap-2 items-center">
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Simpan
                        </PrimaryButton>
                        <SecondaryButton type="button" @click="isPreviewing = true">Preview</SecondaryButton>
                        <SecondaryButton @click="cancelEdit">Batal</SecondaryButton>
                    </div>
                    <DangerButton v-if="props.sertification.payment_instruction" type="button"
                        @click="deleteInstruction">
                        Hapus Instruksi
                    </DangerButton>
                </div>
                <div class="mt-2">
                    <label class="flex items-center">
                        <Checkbox v-model:checked="form.send_notification" />
                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Kirim Notifikasi ke Asesi?</span>
                    </label>
                </div>
            </form>
        </div>

        <Modal :show="isPreviewing" @close="isPreviewing = false">
            <div class="p-6 bg-white dark:bg-gray-800">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Preview Instruksi Pembayaran</h2>
                <div class="border-t border-gray-200 dark:border-gray-700 py-4">
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
                                <h5 class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                    {{ props.sertification.payment_isntruction?.name || 'Admin' }}
                                </h5>
                                <div class="text-xs text-gray-400">
                                    {{ formatDate(new Date()) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-html="form.content.replace(/\n/g, '<br>')"
                        class="font-medium text-sm text-gray-800 dark:text-gray-100 mb-4"></div>
                    <div class="flex mb-2">
                        <dt class="text-sm font-medium text-gray-800 dark:text-gray-400 mr-1">
                            Biaya Sertifikasi :
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-gray-100">
                            Rp
                            {{ new Intl.NumberFormat('id-ID').format(sertification.biaya) }}
                        </dd>
                    </div>
                    <div class="flex mb-2">
                        <dt class="text-sm font-medium text-gray-800 dark:text-gray-400 mr-1">Batas Akhir Pengumpulan :
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-gray-100">
                            {{ form.deadline_bayar ? formatDateTime(form.deadline_bayar) : "-" }}
                        </dd>
                    </div>

                </div>
                <div class="flex justify-end mt-4">
                    <SecondaryButton @click="isPreviewing = false">Tutup Preview</SecondaryButton>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>