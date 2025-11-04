<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import axios from 'axios';
const page = usePage();
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

const linkWithId = (notif) => {
    const baseUrl = notif.link;
    if (!baseUrl) {
        return '#';
    }
    const separator = baseUrl.includes('?') ? '&' : '?';
    return `${baseUrl}${separator}notification_id=${notif.id}`;
}

</script>
<template>
    <div class="relative flex items-center ml-4">
        <div @click="showNotifikasi = !showNotifikasi">
            <button class="relative focus:outline-none cursor-pointer">
                <svg class="w-5 h-5 text-gray-700 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M18 16.5V11C18 7.69 15.31 5 12 5S6 7.69 6 11V16.5L4 18.5V19.5H20V18.5L18 16.5ZM12 22C13.1 22 14 21.1 14 20H10C10 21.1 10.9 22 12 22Z" />
                </svg>
                <span v-if="unreadCount > 0"
                    class="absolute -top-2 -right-2 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full p-1">
                    {{ unreadCount }}
                </span>
            </button>
        </div>
        <div v-show="showNotifikasi" class="fixed inset-0 z-30" @click="showNotifikasi = false"></div>
        <div v-show="showNotifikasi"
            class="absolute right-0 top-full mt-2 w-80 bg-white dark:bg-gray-700 rounded-md shadow-lg z-40 border border-gray-200 dark:border-gray-600 hidden md:block"
            style="display: none;">
            <div class="max-h-64 overflow-y-auto divide-y divide-gray-200 dark:divide-gray-600">
                <div v-if="latestNotifications.length > 0">
                    <Link v-for="notif in latestNotifications" :key="notif.id" :href="linkWithId(notif)"
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
        <div v-show="showNotifikasi" class="fixed inset-0 z-40 bg-white dark:bg-gray-900 flex flex-col md:hidden"
            style="display: none;">
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
                    <Link v-for="notif in latestNotifications" :key="notif.id" :href="linkWithId(notif)"
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