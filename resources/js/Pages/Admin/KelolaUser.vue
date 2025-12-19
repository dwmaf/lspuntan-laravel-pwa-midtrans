<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import CustomHeader from '@/Components/CustomHeader.vue';
import EditButton from '@/Components/Button/EditButton.vue';
import InputLabel from '@/Components/Input/InputLabel.vue';
import DeleteButton from '@/Components/Button/DeleteButton.vue';
import Modal from '@/Components/Modal.vue';
import ToggleSwitch from '@/Components/ToggleSwitch.vue';
import TextInput from '@/Components/Input/TextInput.vue';
import InputError from '@/Components/Input/InputError.vue';
import PrimaryButton from '@/Components/Button/PrimaryButton.vue';
import MultiSelect from '@/Components/Input/MultiSelect.vue';
import SecondaryButton from '@/Components/Button/SecondaryButton.vue';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import { ref, computed, watch, reactive } from 'vue';
import SuccessButton from '@/Components/Button/SuccessButton.vue';
import Pagination from '@/Components/Pagination.vue';
import SelectInput from '@/Components/Input/SelectInput.vue';
import { MoveRight, FunnelIcon, X } from 'lucide-vue-next';

const authUser = usePage().props.auth.user;
const props = defineProps({
    users: Object,
    roles: Array,
    filters: Object,
});

const filtersForm = reactive({
    search: props.filters.search || '',
    role: props.filters.role || '',
    status: props.filters.status || '',
    verified: props.filters.verified || '',
});
const hasActiveFilters = computed(() => {
    const { search, ...advancedFilters } = filtersForm;
    return Object.values(advancedFilters).some(value => value !== '' && value !== null);
});
const showFilterModal = ref(false);
const openFilterModal = () => {
    showFilterModal.value = true;
};
const closeFilterModal = () => {
    showFilterModal.value = false;
};
let searchTimeoutId = null;
watch(() => filtersForm.search, (newValue) => {
    clearTimeout(searchTimeoutId);
    searchTimeoutId = setTimeout(() => {
        router.get(route('admin.users.index'), filtersForm, {
            preserveState: true,
            replace: true,
        });
    }, 500);
});
const applyFilters = () => {
    router.get(route('admin.users.index'), filtersForm, {
        preserveState: true,
        replace: true,
    });
    closeFilterModal();
};
const resetFilters = () => {
    filtersForm.search = '';
    filtersForm.role = '';
    filtersForm.status = '';
    filtersForm.verified = '';
    applyFilters();
};

const viewMode = ref('list');
const selectedUser = ref(null);

const showBanModal = ref(false);
const userToBan = ref(null);

const confirmBanUser = (user) => {
    userToBan.value = user;
    showBanModal.value = true;
};

const closeModal = () => {
    userToBan.value = null;
    showBanModal.value = false;
};

const banUser = () => {
    const banForm = useForm({});
    banForm.post(route('admin.users.ban', userToBan.value.id), {
        onSuccess: () => closeModal(),
    });
};

const form = useForm({
    _method: 'PATCH',
    name: '',
    email: '',
    no_tlp_hp: '',
    selectedRoles: [],
    is_verified: false,
});
const roleOptions = computed(() => {
    if (!selectedUser.value) {
        return [];
    }
    const isAsesi = selectedUser.value.roles.some(role => role.name === 'asesi');
    if (isAsesi) {
        return props.roles.filter(role => role === 'asesi')
            .map(role => ({ value: role, text: role }));
    } else {
        return props.roles.filter(role => role !== 'asesi').map(role => ({ value: role, text: role }));
    }
});

const isRoleEditingDisabled = computed(() => {
    if (!selectedUser.value) return true;
    return selectedUser.value.roles.some(role => role.name === 'asesi');
})
const enterEditMode = (user) => {
    form.name = user.name || '';
    form.email = user.email;
    form.no_tlp_hp = user.no_tlp_hp;
    form.selectedRoles = user.roles.map(role => role.name);
    form.is_verified = !!user.email_verified_at;
    selectedUser.value = user;
    viewMode.value = 'edit';
};
const cancelEdit = () => {
    viewMode.value = 'list';
    form.reset();
    form.clearErrors();
};
const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const formatDateTime = (dateString) => {
    if (!dateString) return;
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

const save = () => {
    form.post(route('admin.users.update', selectedUser.value.id), {
        onSuccess: () => {
            cancelEdit();
        }
    });
};
</script>

<template>



    <AdminLayout>
        <CustomHeader judul="Manajemen Akun" />
        <div v-if="viewMode === 'list'" class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="flex justify-end items-center gap-2 mb-4">
                <div class="w-[243px]">
                    <TextInput v-model="filtersForm.search" type="text" placeholder="Cari nama atau email..." />
                </div>
                <button @click="openFilterModal"
                    class="relative mt-1 inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-500 text-sm font-medium rounded-md text-gray-500 dark:text-gray-300 bg-white dark:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none transition ease-in-out duration-150">
                    <FunnelIcon class="w-4" />
                    <span v-if="hasActiveFilters"
                        class="absolute -top-1 -right-1 h-2 w-2 bg-blue-500 rounded-full"></span>
                </button>
            </div>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                No
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Nama
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Email
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Role
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Tanggal Bergabung
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Email Verified
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="(user, index) in users.data" :key="user.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ index + users.from }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div
                                    :class="['text-sm', user.name ? 'font-medium text-gray-900 dark:text-gray-100' : 'italic text-gray-400 dark:text-gray-500']">
                                    {{ user.name || 'Belum diisi' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ user.email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <div v-if="user.roles.length > 0" class="flex flex-wrap gap-1">
                                    <span v-for="role in user.roles" :key="role.id"
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 capitalize">
                                        {{ role.name }}
                                    </span>
                                </div>
                                <span v-else class="text-xs text-gray-400 italic">No Role</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ formatDate(user.created_at) }}
                            </td>
                            <td
                                :class="['px-6 py-4 whitespace-nowrap text-sm', user.email_verified_at ? ' text-gray-500 dark:text-gray-400' : 'italic text-gray-400 dark:text-gray-500']">
                                {{ formatDateTime(user.email_verified_at) || 'Belum terverifikasi' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex gap-2">
                                <EditButton @click="enterEditMode(user)">Edit</EditButton>
                                <template v-if="user.id !== authUser.id">
                                    <DeleteButton v-if="!user.banned_at" @click="confirmBanUser(user)">Ban
                                    </DeleteButton>
                                    <SuccessButton v-else @click="confirmBanUser(user)">Un-ban</SuccessButton>
                                </template>
                            </td>
                        </tr>
                        <tr v-if="users.data.length === 0">
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                Tidak ada data pengguna.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex justify-between items-center">
                <span v-if="users.total > 0" class="text-sm text-gray-700 dark:text-gray-400 hidden lg:flex">
                    Menampilkan {{ users.from }} sampai {{ users.to }} dari {{ users.total }} hasil
                </span>
                <span v-else></span>
                <Pagination :links="users.links" />
            </div>
        </div>
        <div v-if="viewMode === 'edit'" class="p-6 bg-white dark:bg-gray-800 shadow-md rounded-lg mt-2">
            <form @submit.prevent="save" class="flex flex-col gap-4">
                <div class="relative">
                    <InputLabel value="Role" required />
                    <MultiSelect v-model="form.selectedRoles" placeholder="Pilih role user" :options="roleOptions"
                        :disabled="isRoleEditingDisabled" />
                    <InputError :message="form.errors.selectedRoles" />
                    <p v-if="isRoleEditingDisabled" class="text-xs text-gray-500 dark:text-gray-400 ">
                        Peran untuk Asesi tidak dapat diubah.
                    </p>
                </div>
                <div>
                    <InputLabel value="Nama" required />
                    <TextInput v-model="form.name" required />
                    <InputError :message="form.errors.name" />
                </div>
                <div>
                    <InputLabel value="Email" required />
                    <TextInput v-model="form.email" required disabled />
                    <InputError :message="form.errors.email" />
                </div>
                <div>
                    <InputLabel value="No WA" />
                    <TextInput v-model="form.no_tlp_hp" />
                    <InputError :message="form.errors.no_tlp_hp" />
                </div>

                <div v-if="!selectedUser.email_verified_at" class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <div class="flex items-center justify-between">
                        <span class="flex flex-col">
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Verifikasi Manual</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Aktifkan untuk memverifikasi email
                                pengguna
                                ini secara manual.</span>
                        </span>

                        <ToggleSwitch v-model="form.is_verified" />
                    </div>
                    <InputError :message="form.errors.is_verified" />
                </div>
                <div class="flex items-center gap-2">
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Simpan
                    </PrimaryButton>
                    <SecondaryButton type="button" @click="cancelEdit">Batal</SecondaryButton>
                </div>
            </form>
        </div>
    </AdminLayout>
    <Modal :show="showBanModal" @close="showBanModal = false">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Apakah Anda yakin ingin {{ userToBan?.banned_at ? 'mengaktifkan kembali' : 'menangguhkan' }} akun ini?
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Pengguna <span class="font-bold">{{ userToBan?.email }}</span>
                {{ userToBan?.banned_at ? 'akan bisa login kembali.' : 'tidak akan bisa login.' }}
            </p>

            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="closeModal"> Batal </SecondaryButton>

                <DeleteButton v-if="!userToBan?.banned_at" class="ml-3" @click="banUser">
                    Ya, Tangguhkan Akun
                </DeleteButton>
                <PrimaryButton v-else class="ml-3" @click="banUser">
                    Ya, Aktifkan Kembali
                </PrimaryButton>
            </div>
        </div>
    </Modal>
    <Modal :show="showFilterModal" @close="showFilterModal = false">
        <div class="flex justify-end p-2">
            <button @click="closeFilterModal">
                <X class="w-4 dark:text-white" />
            </button>
        </div>
        <div class="p-6">
            <div class="flex flex-col gap-4">
                <div>
                    <InputLabel value="Role" />
                    <SelectInput v-model="filtersForm.role"
                        :options="[{ value: '', text: 'Semua' }, ...roles.map(r => ({ value: r, text: r }))]" />
                </div>
                <div>
                    <InputLabel value="Status Akun" />
                    <SelectInput v-model="filtersForm.status"
                        :options="[{ value: '', text: 'Semua' }, { value: 'active', text: 'Aktif' }, { value: 'banned', text: 'Ditangguhkan' },]" />
                </div>
                <div>
                    <InputLabel value="Verifikasi Email" />
                    <SelectInput v-model="filtersForm.verified"
                        :options="[{ value: '', text: 'Semua' }, { value: 'true', text: 'Terverifikasi' }, { value: 'false', text: 'Belum Terverifikasi' },]" />
                </div>
            </div>
            <div class="my-4 border-t border-gray-200 dark:border-gray-600"></div>
            <div class=" flex gap-3">
                <SecondaryButton @click="resetFilters"> Reset </SecondaryButton>
                <PrimaryButton @click="applyFilters">Apply Filter</PrimaryButton>
            </div>
        </div>
    </Modal>
</template>