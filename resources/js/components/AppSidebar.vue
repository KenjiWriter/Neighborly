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
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from '@/composables/useI18n';

const { t } = useI18n();
const page = usePage();

const mainNavItems = computed(() => {
    const items: NavItem[] = [
        {
            title: t('dashboard.title', 'Dashboard'),
            href: dashboard(),
            icon: LayoutGrid,
        },
    ];

    if (page.props.auth.can.viewCommunityPrimary) {
        items.push({
            title: t('community.title', 'Community'),
            href: route('communities.show'),
            icon: Folder,
        });
    }

    if (page.props.auth.can.viewMaintenanceRequests) {
        items.push({
            title: t('maintenance.title', 'Maintenance'),
            href: route('maintenance.index'),
            icon: LayoutGrid, // Keeping generic icon or import Wrench? 
                              // Sidebar imports specific icons. Let's stick to Folder or LayoutGrid 
                              // unless I check imports. Step 452 showed BookOpen, Folder, LayoutGrid.
                              // Let's use LayoutGrid or Folder for now to avoid import errors.
                              // Or better: Re-check imports in AppSidebar.vue to see if Wrench is available?
                              // Step 452: import { BookOpen, Folder, LayoutGrid } from 'lucide-vue-next';
                              // So Wrench is NOT imported. I will use LayoutGrid for now.
        });
    }

    if (page.props.auth.can.viewFinanceOverview) {
        items.push({
            title: t('finance.overview', 'Finance'),
            href: route('finances.overview'),
            icon: PieChart, 
        });
    }

    if (page.props.auth.can.viewDocuments) {
        items.push({
            title: t('documents.title', 'Documents'),
            href: route('documents.index'),
            icon: FileText, 
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
                        <Link :href="dashboard()">
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
