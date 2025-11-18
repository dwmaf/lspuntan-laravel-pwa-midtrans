<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Header from '@/Components/CustomHeader.vue';
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
    isSubscribed: Boolean,
});
</script>

<template>

    <AdminLayout>
        <Header judul="Profil"/>
        <div class="flex flex-col gap-4">
            <div
                class="bg-white p-4 shadow rounded-lg sm:p-8 dark:bg-gray-800"
            >
                <UpdateProfileAdmin
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="max-w-xl"
                />
            </div>

            <div
                class="bg-white p-4 shadow rounded-lg sm:p-8 dark:bg-gray-800"
            >
                <UpdatePasswordAdmin class="max-w-xl" />
            </div>
            <div
                class="bg-white p-4 shadow rounded-lg sm:p-8 dark:bg-gray-800"
            >
                <FCMPermission :is-subscribed="isSubscribed"/>
            </div>

        </div>
        
    </AdminLayout>
</template>
