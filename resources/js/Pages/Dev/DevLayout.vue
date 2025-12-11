<script setup>
import { usePage } from "@inertiajs/vue3";
import { ref, computed, watch, onMounted } from "vue";
const page = usePage();
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

    <div class="relative min-h-screen bg-gray-100 dark:bg-gray-900 overflow-hidden">
        <Transition enter-active-class="transition ease-out duration-300"
            enter-from-class="transform opacity-0 translate-x-full" enter-to-class="transform opacity-100 translate-x-0"
            leave-active-class="transition ease-in duration-300" leave-from-class="transform opacity-100 translate-x-0"
            leave-to-class="transform opacity-0 translate-x-full">
            <div v-if="isNotificationVisible"
                class="fixed top-20 right-4 text-sm px-4 py-2 rounded-lg shadow-lg bg-green-600 text-white z-50">
                {{ notification }}
            </div>
        </Transition>
        <div class="fixed top-0 right-0 z-50 overflow-hidden w-32 h-32 pointer-events-none">
            <div
                class="absolute top-0 right-0 w-[150%] h-8 bg-yellow-400 text-yellow-900 font-bold text-center leading-8 transform translate-x-[30%] translate-y-[50%] rotate-45 shadow-md border-b border-yellow-500">
                BETA
            </div>
        </div>
        <div class="p-2 sm:p-6">
            <slot></slot>
        </div>
    </div>
</template>
