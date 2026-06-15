<script setup>
import Navigation from "@/Components/Navigation.vue";
import NotificationBell from "@/Components/NotificationBell.vue";
import UserInfo from "@/Components/UserInfo.vue";
import { ref, computed, watch, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import { IconLayoutSidebar } from '@tabler/icons-vue';

const page = usePage();
const user = computed(() => page.props?.auth?.user);
const namaUser = computed(() => user.value.name);

const userRole = computed(() => {
    const roles = page.props.auth?.roles || [];
    return roles.length > 0 ? roles.join(', ') : 'user';
});

const flashMessage = computed(() => page.props?.flash?.message);
const flashError = computed(() => page.props?.flash?.error);

const toasts = ref([]);

const showNotification = (content, type) => {
    if (!content) return;
    console.log(`[Notification] Showing ${type}: ${content}`);

    const id = crypto.randomUUID();
    toasts.value.push({
        id,
        content,
        type
    });

    setTimeout(() => {
        removeToast(id);
    }, 5000);
};

const removeToast = (id) => {
    toasts.value = toasts.value.filter(t => t.id !== id);
};

watch(() => page.props.flash, (flash) => {
    if (flash?.message) {
        showNotification(flash.message, 'success');
        page.props.flash.message = null;
    }
    if (flash?.error) {
        showNotification(flash.error, 'error');
        page.props.flash.error = null;
    }
}, { deep: true, immediate: true });
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
        <!-- Toast Container -->
        <div class="fixed top-20 right-4 z-100 flex flex-col gap-3 pointer-events-none">
            <TransitionGroup enter-active-class="transition ease-out duration-300"
                enter-from-class="transform opacity-0 translate-x-full"
                enter-to-class="transform opacity-100 translate-x-0"
                leave-active-class="transition ease-in duration-200 absolute"
                leave-from-class="transform opacity-100 translate-x-0"
                leave-to-class="transform opacity-0 translate-x-full" move-class="transition duration-300">
                <div v-for="toast in toasts" :key="toast.id" :class="[
                    'pointer-events-auto text-sm px-4 py-3 rounded-lg shadow-lg font-medium flex items-center justify-between gap-4 min-w-[280px] max-w-md border-l-4',
                    toast.type === 'success' ? 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 border-green-500 shadow-green-500/10' : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 border-red-500 shadow-red-500/10'
                ]">
                    <div class="flex items-center gap-3">
                        <div
                            :class="['p-1 rounded-full', toast.type === 'success' ? 'bg-green-100 dark:bg-green-900/30 text-green-600' : 'bg-red-100 dark:bg-red-900/30 text-red-600']">
                            <span v-if="toast.type === 'success'" class="block w-4 h-4 text-center leading-4">✓</span>
                            <span v-else class="block w-4 h-4 text-center leading-4">✕</span>
                        </div>
                        <span class="flex-1">{{ toast.content }}</span>
                    </div>
                    <button @click="removeToast(toast.id)"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        ✕
                    </button>
                </div>
            </TransitionGroup>
        </div>
        <div v-if="isSidebarOpen" @click="isSidebarOpen = false" class="fixed inset-0 z-20 bg-black/50 md:hidden">
        </div>
        <div class="flex h-screen overflow-hidden bg-gray-100 dark:bg-gray-900">
            <Navigation :is-open="isSidebarOpen" @close="isSidebarOpen = false" />
            <div class="flex-1 h-screen overflow-y-auto custom-scrollbar relative z-0">
                <header
                    class="bg-white dark:bg-gray-800 p-4 flex items-center justify-between sticky top-0 z-10 ml-2 border-b border-gray-300 dark:border-gray-700">
                    <IconLayoutSidebar @click="toggleSidebar" class="text-gray-700 dark:text-gray-200 cursor-pointer"
                        :size="20" stroke-width="2" aria-label="Buka Menu" />
                    <div class="flex items-center">
                        <NotificationBell />
                        <UserInfo :namaUser="namaUser" :userRole="userRole" />
                    </div>
                </header>
                <main class="p-2">
                    <slot></slot>
                </main>
            </div>
        </div>
    </div>
</template>
