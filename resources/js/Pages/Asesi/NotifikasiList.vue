<script setup>
import AsesiLayout from "@/Layouts/AsesiLayout.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import CustomHeader from "@/Components/CustomHeader.vue";
import Pagination from "@/Components/Pagination.vue";
import { useFormat } from "@/Composables/useFormat";
import { useNotification } from "@/Composables/useNotification";
import { Link, usePage } from "@inertiajs/vue3";
import { Bell, CheckCircle, Clock } from "lucide-vue-next";
import { computed } from "vue";

const props = defineProps({
    allNotifications: Object,
});

const page = usePage();
const { formatDateTime } = useFormat();
const { getNotificationLink } = useNotification();

const layout = computed(() => {
    const roles = page.props.auth?.roles || [];
    if (roles.includes('admin') || roles.includes('asesor')) {
        return AdminLayout;
    }
    return AsesiLayout;
});
</script>

<template>
    <component :is="layout">
        <CustomHeader judul="Daftar Notifikasi" />
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 py-6">
            <div
                class="bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden border border-gray-100 dark:border-gray-700">

                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    <template v-if="allNotifications.data.length > 0">
                        <Link v-for="notif in allNotifications.data" :key="notif.id" :href="getNotificationLink(notif)"
                            class="block p-2 sm:p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150 relative group items-center"
                            :class="{ 'bg-blue-50/50 dark:bg-blue-900/10': !notif.read_at }">
                            <div class="flex gap-4 items-center">
                                <div class="flex-shrink-0 mt-1">
                                    <span v-if="!notif.read_at"
                                        class="inline-flex items-center justify-center p-2 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 rounded-full">
                                        <Bell class="w-5 h-5" />
                                    </span>
                                    <span v-else
                                        class="inline-flex items-center justify-center p-2 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-full">
                                        <CheckCircle class="w-5 h-5" />
                                    </span>
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start flex-col sm:flex-row justify-between sm:gap-4">
                                        <div class="block focus:outline-none">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white mb-1"
                                                :class="{ 'font-bold': !notif.read_at }">
                                                {{ notif.message || notif.data?.message }}
                                            </p>
                                            <p v-if="notif.description || notif.data?.description"
                                                class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2 mb-2">
                                                {{ notif.description || notif.data?.description }}
                                            </p>
                                        </div>

                                        <span
                                            class="flex-shrink-0 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap flex items-center gap-1">
                                            <Clock class="w-3 h-3" />
                                            {{ formatDateTime(notif.created_at, 'short') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </Link>
                    </template>

                    <div v-else class="p-12 text-center">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
                            <Bell class="w-8 h-8 text-gray-400 dark:text-gray-500" />
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">Belum ada notifikasi</h3>
                        <p class="text-gray-500 dark:text-gray-400">Anda belum menerima notifikasi apapun saat ini.</p>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="allNotifications.links && allNotifications.links.length > 3"
                    class="px-4 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex justify-center">
                    <Pagination :links="allNotifications.links" />
                </div>
            </div>
        </div>
    </component>
</template>
