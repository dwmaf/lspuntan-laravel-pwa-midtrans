<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import axios from 'axios';
import { Bell } from 'lucide-vue-next';
import { useNotification } from "@/Composables/useNotification";

const page = usePage();
const { getNotificationLink } = useNotification();
const unreadCount = computed(() => page.props.notifications?.unreadCount || 0);
const latestNotifications = ref(page.props.notifications?.latest || []);
// console.log(latestNotifications.value);
const showNotifikasi = ref(false);

const markAllRead = async () => {
    try {
        await axios.post(route('notifications.markAllRead'));
        latestNotifications.value.forEach(n => n.read_at = new Date().toISOString());
        page.props.notifications.unreadCount = 0;
    } catch (error) {
        console.error("Gagal menandai notifikasi:", error);
    }
};

</script>
<template>
    <div class="relative flex items-center ml-4">
        <div @click="showNotifikasi = !showNotifikasi">
            <button class="relative focus:outline-none cursor-pointer p-1">
                <Bell class="w-6 h-6 text-gray-700 dark:text-gray-200" />
                <span v-if="unreadCount > 0 && unreadCount <= 99"
                    class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full transform translate-x-1/4 -translate-y-1/4">
                    {{ unreadCount }}
                </span>
                <span v-else-if="unreadCount > 99"
                    class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-600 rounded-full border-2 border-white dark:border-gray-800">
                </span>
            </button>
        </div>
        <div v-show="showNotifikasi" class="fixed inset-0 z-30" @click="showNotifikasi = false"></div>
        <div v-show="showNotifikasi"
            class="absolute right-0 top-full mt-2 w-100 bg-white dark:bg-gray-700 rounded-md shadow-lg z-40 border border-gray-200 dark:border-gray-600 hidden md:block">
            <div class="max-h-64 overflow-y-auto divide-y divide-gray-200 dark:divide-gray-600">
                <div v-if="latestNotifications.length > 0">
                    <Link v-for="notif in latestNotifications" :key="notif.id" :href="getNotificationLink(notif)"
                        class=" block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600"
                        @click="showNotifikasi = false">
                        <div class="flex items-start justify-between ">
                            <div :class="[!notif.read_at ? 'text-gray-800 dark:text-gray-100' : 'text-gray-400']"
                                class="truncate">
                                {{ notif.message }}
                            </div>
                            <div class="text-xs text-gray-500 ml-2 shrink-0">{{ notif.created_at }}</div>
                        </div>
                    </Link>
                </div>
                <div v-else class="px-4 py-3 text-sm text-gray-500">Tidak ada notifikasi.</div>
            </div>
            <div
                class="border-t border-gray-100 dark:border-gray-600 flex items-center justify-between px-3 py-2 bg-gray-50 dark:bg-gray-800 rounded-b-md">
                <button @click="markAllRead"
                    class="text-xs text-gray-700 dark:text-gray-300 hover:underline cursor-pointer">Tandai dibaca
                    semua</button>
                <Link :href="route('notifications.index')"
                    class="text-xs text-blue-600 dark:text-blue-400 hover:underline cursor-pointer">Lihat semua</Link>
            </div>
        </div>
        <div v-show="showNotifikasi" class="fixed inset-0 z-40 bg-white dark:bg-gray-900 flex flex-col md:hidden">
            <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Notifikasi</h2>
                <button @click="showNotifikasi = false"
                    class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto divide-y divide-gray-200 dark:divide-gray-600">
                <div v-if="latestNotifications.length > 0">
                    <Link v-for="notif in latestNotifications" :key="notif.id" :href="getNotificationLink(notif)"
                        class="block px-4 py-3 text-sm hover:bg-gray-100 dark:hover:bg-gray-800"
                        @click="showNotifikasi = false">
                        <div class="flex items-start justify-between">
                            <div :class="[!notif.read_at ? 'text-gray-800 dark:text-gray-100' : 'text-gray-400']"
                                class="truncate pr-2">{{ notif.message }}</div>
                            <div class="text-xs text-gray-500 ml-2 shrink-0">{{ notif.created_at }}</div>
                        </div>
                    </Link>
                </div>
                <div v-else class="px-4 py-3 text-sm text-gray-500 text-center">Tidak ada notifikasi.</div>
            </div>
            <div
                class="border-t border-gray-200 dark:border-gray-700 flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900/50">
                <button @click="markAllRead" class="text-sm text-gray-700 dark:text-gray-300 hover:underline">Tandai
                    dibaca semua</button>
                <Link :href="route('notifications.index')"
                    class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Lihat semua</Link>
            </div>
        </div>
    </div>
</template>