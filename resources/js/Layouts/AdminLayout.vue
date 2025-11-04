<script setup>
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import Navigation from "../Components/Navigation.vue";
import TopNavigation from "../Components/TopNavigation.vue";
import NotificationBell from "../Components/NotificationBell.vue";
import { ref, computed, watch } from "vue";
import { usePage } from "@inertiajs/vue3";
import { UserCircle } from 'lucide-vue-next';

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
    if (newValue) {
        isNotificationVisible.value = true;
        clearTimeout(notificationTimer);
        notificationTimer = setTimeout(() => {
            isNotificationVisible.value = false;
        }, 3000);
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
        <div class="flex min-h-screen bg-gray-100 dark:bg-gray-900">
            <div class="hidden md:flex relative z-30">
                <Navigation />
            </div>

            <div class="flex-1 flex flex-col min-w-0">
                <div class="md:hidden mb-2">
                    <TopNavigation />
                </div>
                <header class="bg-white dark:bg-gray-800 shadow-sm p-4 m-2 flex items-center justify-between relative"
                    v-if="$slots.header">
                    <div class="flex-1">
                        <slot name="header" />
                    </div>

                    <NotificationBell />
                    <div class="ml-4">
                        <div v-if="userInitials"
                            class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm">
                            {{ userInitials }}
                        </div>
                        <UserCircle stroke-width="1" v-else class="w-8 h-8 text-gray-500" />
                    </div>
                </header>
                <main class="p-2">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
