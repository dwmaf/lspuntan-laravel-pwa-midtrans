<script setup>
import { ref } from "vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import Navigation from "../Components/Navigation.vue";
import TopNavigation from "../Components/TopNavigation.vue";

import { Link } from "@inertiajs/vue3";

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="flex min-h-screen bg-gray-100 dark:bg-gray-900">
            <div class="hidden md:flex">
                <Navigation/>
            </div>

            <div class="flex-1 flex flex-col min-w-0">
                <div class="md:hidden mb-2">
                    <TopNavigation/>
                </div>
                <header
                    class="bg-white dark:bg-gray-800 shadow-sm p-4 m-2 flex items-center justify-between relative z-10"
                    v-if="$slots.header"
                >
                    <div class="flex-1">
                        <slot name="header" />
                    </div>
                </header>
                <main class="p-2">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
