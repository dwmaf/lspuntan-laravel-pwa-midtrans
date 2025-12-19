<script setup>
import Navigation from "../Components/Navigation.vue";
import NotificationBell from "../Components/NotificationBell.vue";
import { ref, computed, watch, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import { UserCircle } from 'lucide-vue-next';
import {
    IconLayoutDashboard, IconLayoutSidebar
} from '@tabler/icons-vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const userInitials = computed(() => {
    if (user.value && user.value.name) {
        const parts = user.value.name.split(' ');
        if (parts.length === 1) {
            return parts[0][0].toUpperCase();
        }
        return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
    }
    return null;
});

const notification = computed(() => page.props.flash?.message);
const isNotificationVisible = ref(false);
let notificationTimer = null;

watch(notification, (newValue) => {
    console.log('Flash message received:', newValue); 
    console.log('Full flash props:', page.props.flash);
    if (newValue) {
        isNotificationVisible.value = true;
        clearTimeout(notificationTimer);
        notificationTimer = setTimeout(() => {
            isNotificationVisible.value = false;
        }, 3000);
    }
}, { immediate: true }); 
const isSidebarOpen = ref(true);

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

onMounted(() => {
    if (window.innerWidth < 768) {
        isSidebarOpen.value = false;
    }
});
</script>

<template>
    <div>
        <Transition enter-active-class="transition ease-out duration-300"
            enter-from-class="transform opacity-0 translate-x-full" enter-to-class="transform opacity-100 translate-x-0"
            leave-active-class="transition ease-in duration-300" leave-from-class="transform opacity-100 translate-x-0"
            leave-to-class="transform opacity-0 translate-x-full">
            <div v-if="isNotificationVisible"
                class="fixed top-20 right-4 text-sm px-4 py-2 rounded-lg shadow-lg bg-green-600 text-white z-50">
                {{ notification }}
            </div>
        </Transition>
        <div v-if="isSidebarOpen" @click="isSidebarOpen = false" class="fixed inset-0 z-20 bg-black/50 md:hidden">
        </div>
        <div class="flex h-screen overflow-hidden bg-gray-100 dark:bg-gray-900">
            <Navigation :is-open="isSidebarOpen" @close="isSidebarOpen = false" />
            <div class="flex-1 h-screen overflow-y-auto custom-scrollbar relative z-0">
                <header
                    class="bg-white dark:bg-gray-800 p-4 flex items-center justify-between sticky top-0 z-10 ml-2 border-b border-gray-300 dark:border-gray-700">
                    <IconLayoutSidebar @click="toggleSidebar" class="text-gray-700 dark:text-gray-200 cursor-pointer"
                        :size="20" stroke-width="2" />
                    <div class="flex items-center">
                        <NotificationBell />
                        <div class="ml-4">
                            <div v-if="userInitials"
                                class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm">
                                {{ userInitials }}
                            </div>
                            <UserCircle stroke-width="1" v-else class="w-8 h-8 text-gray-500" />
                        </div>
                    </div>
                </header>
                <main class="p-2">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
