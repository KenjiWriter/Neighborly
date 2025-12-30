<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import { useI18n } from '@/composables/useI18n';
import { routes } from '@/config/routes';

defineProps<{
    announcement: {
        id: number;
        title: string;
        body: string;
        published_at: string;
        creator: { name: string };
    };
}>();

const { t } = useI18n();
</script>

<template>
    <Head :title="announcement.title" />

    <AppLayout>
        <PageHeader
            :title="announcement.title"
        />

        <div class="py-6">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800 p-6">

                    <div class="prose dark:prose-invert max-w-none">
                        <p class="whitespace-pre-wrap text-gray-900 dark:text-gray-100">{{ announcement.body }}</p>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ t('announcements.posted_by') }}: {{ announcement.creator.name }}
                        </p>
                    </div>

                    <div class="mt-6">
                        <Link
                            :href="routes.announcementsIndex"
                            class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400"
                        >
                            ← {{ t('announcements.back_to_list') }}
                        </Link>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>
