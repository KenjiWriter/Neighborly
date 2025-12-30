<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import { useI18n } from '@/composables/useI18n';
import { routes } from '@/config/routes';

defineProps<{
    announcements: {
        data: Array<{
            id: number;
            title: string;
            body: string;
            published_at: string;
            creator: { name: string };
        }>;
        links: any[];
    };
}>();

const { t } = useI18n();
</script>

<template>
    <Head :title="t('announcements.title')" />

    <AppLayout>
        <PageHeader
            :title="t('announcements.title')"
            :description="t('announcements.description')"
        >
            <Link
                v-if="$page.props.auth.roles.some((r: string) => ['admin', 'board_member'].includes(r))"
                :href="routes.announcementsCreate"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150"
            >
                {{ t('common.create') }}
            </Link>
        </PageHeader>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800 p-6">

                    <div v-if="announcements.data.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                        {{ t('announcements.empty') }}
                    </div>

                    <div v-else class="space-y-4">
                        <div v-for="announcement in announcements.data" :key="announcement.id"
                            class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex justify-between items-start">
                                <span>{{ announcement.title }}</span>
                                <span v-if="!announcement.published_at" class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    {{ t('common.draft') }}
                                </span>
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ announcement.body.substring(0, 200) }}{{ announcement.body.length > 200 ? '...' : '' }}
                            </p>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ announcement.creator.name }}
                                </span>
                                <div class="flex gap-4">
                                    <Link
                                        v-if="$page.props.auth.roles.some((r: string) => ['admin', 'board_member'].includes(r))"
                                        :href="routes.announcementsEdit(announcement.id)"
                                        class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
                                    >
                                        {{ t('common.edit') }}
                                    </Link>
                                    <Link
                                        :href="routes.announcementsShow(announcement.id)"
                                        class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400"
                                    >
                                        {{ t('announcements.read_more') }}
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="announcements.data.length > 0" class="mt-6 flex gap-2 flex-wrap">
                        <Link
                            v-for="link in announcements.links"
                            :key="link.label"
                            :href="link.url ?? '#'"
                            class="px-3 py-1 border rounded text-sm"
                            :class="{
                                'bg-indigo-600 text-white border-indigo-600': link.active,
                                'opacity-50 cursor-not-allowed': !link.url,
                                'hover:bg-gray-100 dark:hover:bg-gray-700': link.url && !link.active
                            }"
                            v-html="link.label"
                        />
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>
