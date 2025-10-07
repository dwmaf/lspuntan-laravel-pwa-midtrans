
<script setup>
import AsesiLayout from '@/Layouts/AsesiLayout.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
    user: Object,
    student: Object,
});

const page = usePage();
const notification = computed(() => page.props.flash?.message);

</script>

<template>
    <AsesiLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Profile
            </h2>
        </template>

        <div v-if="notification" class="fixed top-20 right-4 text-sm px-4 py-2 rounded bg-green-600 text-white z-50">
            {{ notification }}
        </div>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800">
                    <UpdateProfileInformationForm
                        :user="user"
                        :student="student"
                        class="max-w-xl"
                    />
                </div>

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AsesiLayout>
</template>