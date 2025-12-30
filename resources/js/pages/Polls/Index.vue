<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import { useI18n } from '@/composables/useI18n';
import { routes } from '@/config/routes';

defineProps<{
    polls: {
        data: Array<{
            id: number;
            title: string;
            description: string | null;
            starts_at: string;
            ends_at: string;
            user_has_voted: boolean;
        }>;
        links: any[];
    };
}>();

const { t } = useI18n();
</script>

<template>
    <Head :title="t('polls.title')" />

    <AppLayout>
        <PageHeader
            :title="t('polls.title')"
            :description="t('polls.description')"
        >
            <Link
                v-if="$page.props.auth.roles.some((r: string) => ['admin', 'board_member'].includes(r))"
                :href="routes.pollsCreate"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150"
            >
                {{ t('common.create') }}
            </Link>
        </PageHeader>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800 p-6">

                    <div v-if="polls.data.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                        {{ t('polls.empty') }}
                    </div>

                    <div v-else class="space-y-4">
                        <div v-for="poll in polls.data" :key="poll.id"
                            class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex justify-between items-start">
                                <span>{{ poll.title }}</span>
                                <span v-if="new Date(poll.starts_at) > new Date()" class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    {{ t('common.scheduled') }}
                                </span>
                                <span v-else-if="new Date(poll.ends_at) < new Date()" class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                    {{ t('common.ended') }}
                                </span>
                                <span v-else class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    {{ t('common.active') }}
                                </span>
                            </h3>
                            <p v-if="poll.description" class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ poll.description }}
                            </p>
                            <div class="mt-3 flex items-center justify-between">
                                <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                    <span v-if="poll.user_has_voted" class="px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded">
                                        {{ t('polls.already_voted') }}
                                    </span>
                                </div>
                                <div class="flex gap-4">
                                    <Link
                                        v-if="$page.props.auth.roles.some((r: string) => ['admin', 'board_member'].includes(r))"
                                        :href="routes.pollsEdit(poll.id)"
                                        class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
                                    >
                                        {{ t('common.edit') }}
                                    </Link>
                                    <Link
                                        :href="routes.pollsShow(poll.id)"
                                        class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400"
                                    >
                                        {{ poll.user_has_voted ? t('polls.view') : t('polls.vote') }}
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="polls.data.length > 0" class="mt-6 flex gap-2 flex-wrap">
                        <Link
                            v-for="link in polls.links"
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
