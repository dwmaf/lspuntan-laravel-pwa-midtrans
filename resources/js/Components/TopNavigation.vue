<!-- filepath: d:\Laravel-App\lsp-untan-laravel-pwa\resources\js\Components\TopNavigation.vue -->
<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';


const open = ref(false);
const page = usePage();
const roles = computed(() => page.props.auth.roles ?? []);
const hasAdminRole = computed(() => roles.value.includes('admin'));
const hasAsesorRole = computed(() => roles.value.includes('asesor'));
const hasAsesiRole = computed(() => roles.value.includes('asesi'));
const navLinks = computed(() => {
    if (hasAdminRole.value || hasAsesorRole.value) {
        const links = [
            { href: route('admin.dashboard'), label: 'Dashboard', active: route().current('admin.dashboard') },
            { href: route('admin.kelolasertifikasi.index'), label: 'Sertifikasi', active: route().current('admin.kelolasertifikasi.*') },
            { href: route('profile.edit'), label: 'Profile', active: route().current('profile.edit') },
        ];
        if (hasAdminRole.value) {
            links.push(
                { href: route('admin.skema.create'), label: 'Skema', active: route().current('admin.skema.*') },
                { href: route('admin.asesor.index'), label: 'Asesor', active: route().current('admin.asesor.*') }
            );
        }
        return links;
    } else if (hasAsesiRole.value) {
        return [
            { href: route('asesi.dashboard'), label: 'Dashboard', active: route().current('asesi.dashboard') },
            { href: route('asesi.sertifikasi.index'), label: 'Sertifikasi', active: route().current('asesi.sertifikasi.*') },
            { href: route('profile_asesi.edit'), label: 'Profile', active: route().current('profile_asesi.edit') },
        ];
    }
    return [];
});
</script>

<template>
<nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <Link >
                        <img src="/logo-lsp.png" alt="Logo LSP" class="block h-9 w-auto" />
                    </Link>
                </div>
            </div>
            <!-- Hamburger -->
            <div class="-mr-2 flex items-center">
                <button @click="open = !open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path v-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path v-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div v-show="open" class="block">
        <div class="pt-2 pb-3 space-y-1">
            <ResponsiveNavLink
                v-for="link in navLinks"
                :key="link.href"
                :href="link.href"
                :active="link.active"
            >
                {{ link.label }}
            </ResponsiveNavLink>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="space-y-1">
                <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                    Log Out
                </ResponsiveNavLink>
                <!-- <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink> -->
            </div>
        </div>
    </div>
</nav>
</template>