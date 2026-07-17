<script setup>
import PrimaryButton from '@/Components/Button/PrimaryButton.vue';
import SecondaryButton from "@/Components/Button/SecondaryButton.vue";
import TextInput from '@/Components/Input/TextInput.vue';
import EditButton from "@/Components/Button/EditButton.vue";
import { useFormat } from "@/Composables/useFormat";
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = computed(() => usePage().props.auth.user);

const isEditing = ref(false);
const form = useForm({
    name: user.value.name,
    email: user.value.email,
    _method: 'PATCH',
});

const enterEditMode = () => {
    form.name = user.value.name;
    form.email = user.value.email;
    isEditing.value = true;
};

const cancelEdit = () => {
    isEditing.value = false;
    form.clearErrors();
};

const submit = () => {
    form.patch(route('profile.update'), {
        preserveState: true,
        onSuccess: () => {
            cancelEdit();
        },
    });
};

const { formatDate } = useFormat();
</script>

<template>
    <section>
        <div v-if="isEditing">
            <header>
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Profile Information
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Update your account's profile information and email address.
                </p>
            </header>

            <form @submit.prevent="submit" class="mt-6 space-y-6">
                <TextInput id="name" label="Nama" type="text" v-model="form.name" required autofocus autocomplete="name"
                    :error="form.errors.name" />
                <TextInput id="email" label="Email" type="email" v-model="form.email" required autocomplete="username"
                    :error="form.errors.email" />
                <div v-if="mustVerifyEmail && user.email_verified_at === null">
                    <p class="mt-2 text-sm text-gray-800 dark:text-gray-200">
                        Your email address is unverified.
                        <Link :href="route('verification.send')" method="post" as="button"
                            class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800">
                            Click here to re-send the verification email.
                        </Link>
                    </p>

                    <div v-show="status === 'verification-link-sent'"
                        class="mt-2 text-sm font-medium text-green-600 dark:text-green-400">
                        A new verification link has been sent to your email address.
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
                    <SecondaryButton type="button" @click="cancelEdit">Batal</SecondaryButton>
                </div>
            </form>
        </div>

        <div v-else>
            <div class="flex justify-between mb-4">
                <header>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Profile Information
                    </h2>
                </header>
                <EditButton @click="enterEditMode">Edit Profil</EditButton>
            </div>

            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                <div>
                    <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Nama Lengkap</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ user.name }}
                    </dd>
                </div>
                <div>
                    <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ user.email }}
                    </dd>
                </div>
            </dl>
            <div v-if="user.asesor">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mt-3">
                    Informasi Asesor (Read-Only)
                </h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">No. Registrasi MET</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ user.asesor.no_reg_met || 'Belum Diatur' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Status Akun Asesor</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            <span v-if="user.asesor.is_active" class="px-2 py-1 bg-green-100 text-green-800 rounded-lg text-xs font-semibold">Aktif</span>
                            <span v-else class="px-2 py-1 bg-red-100 text-red-800 rounded-lg text-xs font-semibold">Non-Aktif</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Masa Berlaku Sertifikat Teknis</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ formatDate(user.asesor.masa_berlaku_sertif_teknis) }}
                        </dd>
                    </div>
                    <div>
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400">Masa Berlaku Sertifikat Asesor</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ formatDate(user.asesor.masa_berlaku_sertif_asesor) }}
                        </dd>
                    </div>
                    <div class="md:col-span-2 mt-2">
                        <dt class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Skema yang Diampu</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            <ul class="list-disc ml-5 w-fit" v-if="user.asesor.skema && user.asesor.skema.length > 0">
                                <li v-for="skema in user.asesor.skema" :key="skema.id" class="text-sm">
                                    {{ skema.nama_skema }}
                                </li>
                            </ul>
                            <p v-else class="text-xs italic text-gray-500">Belum ada skema yang ditugaskan.</p>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>
</template>
