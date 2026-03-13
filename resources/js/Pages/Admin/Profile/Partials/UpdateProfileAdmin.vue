<script setup>
import PrimaryButton from '@/Components/Button/PrimaryButton.vue';
import SecondaryButton from "@/Components/Button/SecondaryButton.vue";
import TextInput from '@/Components/Input/TextInput.vue';
import EditButton from "@/Components/Button/EditButton.vue";
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const isEditing = ref(false);
const form = useForm({
    name: user.name,
    email: user.email,
});

const enterEditMode = () => {
    isEditing.value = true;
};

const cancelEdit = () => {
    isEditing.value = false;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    form.patch(route('profile.update'), {
        onSuccess: () => {
            isEditing.value = false;
        },
    });
};
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
        </div>
    </section>
</template>
