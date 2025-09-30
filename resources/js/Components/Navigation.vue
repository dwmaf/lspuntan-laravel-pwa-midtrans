<script setup>
import { ref, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import NavLink from "./NavLink.vue";

const open = ref(false);
const page = usePage();
const roles = computed(() => page.props.auth.roles ?? []);
const hasAdminRole = computed(() => roles.value.includes('admin'));
const hasAsesorRole = computed(() => roles.value.includes('asesor'));
const hasAsesiRole = computed(() => roles.value.includes('asesi'));
const navLinks = computed(() => {
    if (hasAdminRole || hasAsesorRole) {
        const links = [
            { href: route('admin.dashboard'), label: 'Dashboard', active: route().current('admin.dashboard'), icon: 'fa-gauge' },
            { href: route('admin.kelolasertifikasi.index'), label: 'Sertifikasi', active: route().current('admin.kelolasertifikasi.*'), icon: 'fa-certificate' },
            { href: route('profile.edit'), label: 'Profile', active: route().current('profile.edit'), icon: 'fa-user' },
        ];
        if (hasAdminRole) {
            links.push(
                { href: route('admin.skema.create'), label: 'Skema', active: route().current('admin.skema.*'), icon: 'fa-book' },
                { href: route('admin.asesor.index'), label: 'Asesor', active: route().current('admin.asesor.*'), icon: 'fa-chalkboard-teacher' }
            );
        }
        return links;
    } else if (hasAsesiRole) {
        return [
            { href: route('asesi.dashboard'), label: 'Dashboard', active: route().current('asesi.dashboard'), icon: 'fa-gauge' },
            { href: route('asesi.sertifikasi.index'), label: 'Sertifikasi', active: route().current('asesi.sertifikasi.*'), icon: 'fa-certificate' },
            { href: route('profile_asesi.edit'), label: 'Profile', active: route().current('profile_asesi.edit'), icon: 'fa-user' },
        ];
    }
    return [];
});
</script>

<template>
    <div>
        <aside
            class="h-full bg-white dark:bg-gray-800 p-3 transition-all duration-300 overflow-visible z-40"
            :class="open ? 'w-48 translate-x-0' : 'w-16 translate-x-0'"
        >
            <button
                @click="open = !open"
                class="text-gray-700 dark:text-gray-200 cursor-pointer mb-2 absolute top-5 right-5"
            >
                <svg
                    v-if="open"
                    class="h-6 w-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>
                <svg
                    v-else
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M4 6h16M4 12h16M4 18h16"
                    />
                </svg>
            </button>

            <div class="mt-10">
                <NavLink
                v-for="link in navLinks"
                    :key="link.href"
                    :href="link.href"
                    :active="link.active"
                    :icon="link.icon"
                    :is-open="open"
                    >{{ link.label }}
                </NavLink>
                <NavLink :href="route('logout')" :icon="'fa-right-from-bracket'" method="post" :is-open="open" as="button">
                    Log Out
                </NavLink>
            </div>
        </aside>
    </div>
</template>
