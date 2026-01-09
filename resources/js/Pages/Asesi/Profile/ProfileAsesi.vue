
<script setup>
import AsesiLayout from '@/Layouts/AsesiLayout.vue';
import FCMPermission from '@/Components/FCMPermission.vue';
import CustomHeader from '@/Components/CustomHeader.vue';
import UpdatePassword from './Partials/UpdatePassword.vue';
import UpdateProfileAsesi from './Partials/UpdateProfileAsesi.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
    user: Object,
    student: Object,
    isSubscribed: Boolean,
});



</script>

<template>
    <AsesiLayout>
        <CustomHeader judul="Profil"/>
        <div class="flex flex-col gap-4">
            <div class="bg-white p-4 rounded-lg dark:bg-gray-800">
                <UpdateProfileAsesi
                    :user="props.user"
                    :student="props.student"
                />
            </div>
            <div class="bg-white p-4 rounded-lg dark:bg-gray-800">
                <UpdatePassword />
            </div>
            <div class="bg-white p-4 rounded-lg dark:bg-gray-800">
                <FCMPermission :is-subscribed="isSubscribed"/>
            </div>
        </div>
    </AsesiLayout>
</template>