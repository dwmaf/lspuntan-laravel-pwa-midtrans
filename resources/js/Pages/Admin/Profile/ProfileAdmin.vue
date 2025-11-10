<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';

import UpdateProfileAdmin from './Partials/UpdateProfileAdmin.vue';
import UpdatePasswordAdmin from './Partials/UpdatePasswordAdmin.vue';
import FCMPermission from '@/Components/FCMPermission.vue';


defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>

    <AdminLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Profile
            </h2>
        </template>

        <div class="flex flex-col gap-4">
            <div
                class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800"
            >
                <UpdateProfileAdmin
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="max-w-xl"
                />
            </div>

            <div
                class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800"
            >
                <UpdatePasswordAdmin class="max-w-xl" />
            </div>
            <div
                class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800"
            >
                <FCMPermission />
            </div>

        </div>
        
    </AdminLayout>
</template>
