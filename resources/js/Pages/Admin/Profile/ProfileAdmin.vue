<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import CustomHeader from '@/Components/CustomHeader.vue';
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
        <CustomHeader judul="Profil" />
        <div class="flex flex-col gap-4 max-w-3xl mx-auto">
            <div class="bg-white p-4 shadow rounded-lg dark:bg-gray-800">
                <UpdateProfileAdmin :must-verify-email="mustVerifyEmail" :status="status"/>
            </div>

            <div class="bg-white p-4 shadow rounded-lg dark:bg-gray-800">
                <UpdatePasswordAdmin/>
            </div>
            <div class="bg-white p-4 shadow rounded-lg dark:bg-gray-800">
                <FCMPermission :is-subscribed="isSubscribed" />
            </div>

        </div>

    </AdminLayout>
</template>
