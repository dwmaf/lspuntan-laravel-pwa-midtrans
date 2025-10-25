
<script setup>
import AsesiLayout from '@/Layouts/AsesiLayout.vue';
import UpdatePassword from './Partials/UpdatePassword.vue';
import UpdateProfileAsesi from './Partials/UpdateProfileAsesi.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
    user: Object,
    student: Object,
});



</script>

<template>
    <AsesiLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Profile
            </h2>
        </template>

        

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
        </div>
    </AsesiLayout>
</template>