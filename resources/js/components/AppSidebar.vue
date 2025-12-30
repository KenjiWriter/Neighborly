<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { routes } from '@/config/routes';
import { type NavItem } from '@/types';
import { usePage, Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, PieChart, FileText, ShieldCheck, UserCheck, Users } from 'lucide-vue-next';
import { computed } from 'vue';
import { useI18n } from '@/composables/useI18n';
import AppLogo from '@/components/AppLogo.vue';

const { t } = useI18n();
const page = usePage();

const mainNavItems = computed(() => {
    const items: NavItem[] = [
        {
            title: t('dashboard.title'),
            href: routes.dashboard,
            icon: LayoutGrid,
        },
    ];

    if (page.props.auth.can.viewCommunityPrimary) {
        items.push({
            title: t('community.title'),
            href: routes.communityPrimary,
            icon: Folder,
        });
    }

    if (page.props.auth.can.viewMaintenanceRequests) {
        items.push({
            title: t('maintenance.title'),
            href: routes.maintenanceIndex,
            icon: LayoutGrid, 
        });
    }

    if (page.props.auth.can.viewFinanceOverview) {
        items.push({
            title: t('finance.overview'),
            href: routes.financesOverview,
            icon: PieChart, 
        });
    }

    if (page.props.auth.can.viewDocuments) {
        items.push({
            title: t('documents.title'),
            href: routes.documentsIndex,
            icon: FileText, 
        });
    }

    // Announcements
    items.push({
        title: t('announcements.title'),
        href: routes.announcementsIndex,
        icon: BookOpen,
    });

    // Polls
    items.push({
        title: t('polls.title'),
        href: routes.pollsIndex,
        icon: PieChart,
    });

    // Audit (staff only: admin, board_member, accountant)
    if (page.props.auth.can.viewAudit && page.props.auth.roles.some((role: string) => ['admin', 'board_member', 'accountant'].includes(role))) {
        items.push({
            title: t('audit.title'),
            href: routes.auditIndex,
            icon: ShieldCheck, 
        });
    }

    // Admin section
    if (page.props.auth.can.manageUsers) {
        items.push({
            title: t('admin.pending.title'),
            href: routes.adminUsersPending,
            icon: UserCheck,
        });
        
        items.push({
            title: t('admin.users.title'),
            href: routes.adminUsersIndex,
            icon: Users,
        });
    }

    return items;
});

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="routes.dashboard">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
