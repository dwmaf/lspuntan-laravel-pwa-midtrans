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
    IconLogs,
    IconUsers,
    IconCaretLeftFilled,
    IconUserCircle
} from '@tabler/icons-vue';

const emit = defineEmits(['close']);
const props = defineProps({
    isOpen: {
        type: Boolean,
        default: true
    }
});
const page = usePage();
const user = computed(() => page.props.auth?.user);
const userRole = computed(() => {
    const roles = page.props.auth?.roles || [];
    return roles.length > 0 ? roles.join(', ') : 'user';
});
const roles = computed(() => (page.props.auth.roles ?? []).map(r => typeof r === 'string' ? r : r.name));
const hasAdminRole = computed(() => roles.value.includes('admin'));
const hasAsesorRole = computed(() => roles.value.includes('asesor'));
const hasAsesiRole = computed(() => roles.value.includes('asesi'));
// console.log("role asesi:",hasAsesiRole.value);
const navLinks = computed(() => {
    if (hasAdminRole.value || hasAsesorRole.value) {
        const links = [
            { href: route('admin.dashboard'), label: 'Dashboard', active: route().current('admin.dashboard'), icon: IconLayoutDashboard },
            { href: route('admin.kelolasertifikasi.index'), label: 'Sertifikasi', active: route().current('admin.kelolasertifikasi.*'), icon: IconCertificate },
            { href: route('profile.edit'), label: 'Profile', active: route().current('profile.edit'), icon: IconUser },
        ];
        if (hasAdminRole.value) {
            // console.log("ini harusnya cuman muncul utk yg punya role admin");
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
        <aside
            class="h-full bg-white dark:bg-gray-800 md:bg-transparent md:dark:bg-transparent pl-3 py-3 transition-all duration-300 overflow-visible z-40 fixed md:relative flex flex-col"
            :class="[props.isOpen ? 'w-60' : 'w-17',
            props.isOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0']">
            <div class="flex" v-if="props.isOpen">
                <div class="shrink-0 flex items-center">
                    <Link :href="route('admin.dashboard')">
                        <img src="/logo-lsp-resized.png" alt="Logo LSP" class="block h-15 w-auto" width="75" height="60"/>
                    </Link>
                </div>
            </div>
            <button @click="emit('close')" aria-label="Tutup Menu"
                class="text-gray-700 dark:text-gray-200 cursor-pointer mb-2 absolute top-5 right-5 flex md:hidden">
                <IconCaretLeftFilled size="20" strokeWidth="2" />
            </button>

            <div class="flex-1 pr-3" :class="props.isOpen ? 'overflow-y-auto custom-scrollbar' : 'overflow-visible'">
                <div class="sm:hidden px-3 py-3 flex items-center gap-3">
                    <IconUserCircle stroke-width="1.5" class="w-9 h-9 text-gray-500 shrink-0" />
                    <div class="flex flex-col min-w-0" v-if="props.isOpen">
                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-200 truncate uppercase">
                            {{ user?.name }}
                        </span>
                        <span
                            class="text-[10px] text-blue-600 dark:text-blue-400 font-bold tracking-widest mt-0.5 capitalize">
                            {{ userRole }}
                        </span>
                    </div>
                </div>
                <NavLink v-for="link in navLinks" :key="link.href" :href="link.href" :active="link.active"
                    :icon="link.icon" :is-open="props.isOpen" :label="link.label">{{ link.label }}
                </NavLink>

                <div class="my-2 border-t border-gray-200 dark:border-gray-600"></div>


                <NavLink :href="route('logout')" :icon="IconLogout" method="post" :is-open="props.isOpen" as="button" label="Log Out">
                    Log Out
                </NavLink>
                <!-- <DevNavLink :href="route('dev.list.sertifications')" :icon="IconCode" :is-open="props.isOpen"
                    label="Dev" badge-text="beta" /> -->
            </div>
        </aside>
    </div>
</template>
