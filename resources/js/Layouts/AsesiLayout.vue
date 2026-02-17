<script setup>
import NotificationBell from "@/Components/NotificationBell.vue";
import Navigation from "@/Components/Navigation.vue";
import { ref, computed, watch, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import { UserCircle } from "lucide-vue-next";
import { IconLayoutSidebar } from '@tabler/icons-vue';
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";

const page = usePage();
const user = computed(() => page.props?.auth?.user)
const namaUser = computed(() => user.value.name);

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

const userRole = computed(() => {
    const roles = page.props.auth?.roles || [];
    return roles.length > 0 ? roles.join(', ').toUpperCase() : 'USER';
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

// Watch flash messages and clear them after showing to allow sequential identical notifications
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
        <div class="fixed top-20 right-4 z-[100] flex flex-col gap-3 pointer-events-none">
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
        <div class="flex min-h-screen overflow-hidden bg-gray-100 dark:bg-gray-900">
            <Navigation :is-open="isSidebarOpen" @close="isSidebarOpen = false" />

            <div class="flex-1 h-screen overflow-y-auto custom-scrollbar relative z-0">

                <header
                    class="bg-white dark:bg-gray-800 shadow-sm p-4 flex items-center justify-between sticky top-0 z-10 ml-2 border-b border-gray-300 dark:border-gray-700">
                    <IconLayoutSidebar @click="toggleSidebar" class="text-gray-700 dark:text-gray-200 cursor-pointer"
                        :size="20" stroke-width="2" />
                    <div class="flex items-center">
                        <NotificationBell />
                        <div class="ml-4">
                            <Dropdown>
                                <template #trigger>
                                    <div class="cursor-pointer">
                                        <div v-if="userInitials" :title="`${namaUser}`"
                                            class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm">
                                            {{ userInitials }}
                                        </div>
                                        <UserCircle stroke-width="1" v-else class="w-8 h-8 text-gray-500" />
                                    </div>
                                </template>

                                <template #content>
                                    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-600">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-200 truncate">
                                            {{ namaUser }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                            {{ userRole }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                            {{ user.email }}
                                        </p>
                                    </div>
                                    <DropdownLink :href="route('profile_asesi.edit')"> Profile </DropdownLink>
                                    <DropdownLink :href="route('logout')" method="post" as="button"> Log Out
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                </header>
                <main class="p-2">
                    <slot></slot>
                </main>
            </div>
        </div>
    </div>
</template>
