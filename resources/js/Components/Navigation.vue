<script setup>
import { ref, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import NavLink from "./NavLink.vue";
import {
    IconLayoutDashboard,
    IconCertificate,
    IconBook,
    IconUser,
    IconChalkboardTeacher,
    IconLogout,
    IconLayoutSidebar,
    IconLogs,
    IconUsers
} from '@tabler/icons-vue';

const open = ref(true);
const page = usePage();
const roles = computed(() => page.props.auth.roles ?? []);
const hasAdminRole = computed(() => roles.value.includes('admin'));
const hasAsesorRole = computed(() => roles.value.includes('asesor'));
const hasAsesiRole = computed(() => roles.value.includes('asesi'));
// console.log("role admin:",hasAdminRole.value);
// console.log("role asesi:",hasAsesiRole.value);
const navLinks = computed(() => {
    if (hasAdminRole.value || hasAsesorRole.value) {
        const links = [
            { href: route('admin.dashboard'), label: 'Dashboard', active: route().current('admin.dashboard'), icon:IconLayoutDashboard },
            { href: route('admin.kelolasertifikasi.index'), label: 'Sertifikasi', active: route().current('admin.kelolasertifikasi.*'), icon: IconCertificate },
            { href: route('profile.edit'), label: 'Profile', active: route().current('profile.edit'), icon:IconUser },
        ];
        if (hasAdminRole) {
            links.push(
                { href: route('admin.skema.create'), label: 'Skema', active: route().current('admin.skema.*'), icon: IconBook },
                { href: route('admin.asesor.index'), label: 'Asesor', active: route().current('admin.asesor.*'), icon: IconChalkboardTeacher },
                { href: route('admin.activity-logs.index'), label: 'Logs', active: route().current('admin.activity-logs.index'), icon: IconLogs },
                { href: route('admin.users.index'), label: 'User', active: route().current('admin.users.index'), icon: IconUsers }
            );
        }
        // console.log('role admin/asesor tereksekusi');
        return links;
    } else if (hasAsesiRole.value) {
        // console.log('role asesi tereksekusi');

        return [
            { href: route('asesi.dashboard'), label: 'Dashboard', active: route().current('asesi.dashboard'), icon: IconLayoutDashboard },
            { href: route('asesi.sertifikasi.index'), label: 'Sertifikasi', active: route().current('asesi.sertifikasi.*'), icon: IconCertificate },
            { href: route('profile_asesi.edit'), label: 'Profile', active: route().current('profile_asesi.edit'), icon: IconUser },
        ];
    }
    return [];
});
// console.log(navLinks);

</script>

<template>
    <div>
        <aside class="h-full bg-white dark:bg-gray-800 p-3 transition-all duration-300 overflow-visible z-40"
            :class="open ? 'w-48 translate-x-0' : 'w-16 translate-x-0'">
            <div class="flex" v-if="open">
                <div class="shrink-0 flex items-center">
                    <Link :href="route('admin.dashboard')">
                        <img src="/logo-lsp.png" alt="Logo LSP" class="block h-15 w-auto" />
                    </Link>
                </div>
            </div>
            <button @click="open = !open"
                class="text-gray-700 dark:text-gray-200 cursor-pointer mb-2 absolute top-5 right-5">
                <IconLayoutSidebar size="20" strokeWidth="2"/>
            </button>

            <div :class="!open? 'mt-10' : ''">
                <NavLink v-for="link in navLinks" :key="link.href" :href="link.href" :active="link.active"
                    :icon="link.icon" :is-open="open">{{ link.label }}
                </NavLink>
                <NavLink class="border-t border-gray-200 dark:border-gray-600" :href="route('logout')" :icon="IconLogout" method="post" :is-open="open"
                    as="button">
                    Log Out
                </NavLink>

            </div>
        </aside>
    </div>
</template>
