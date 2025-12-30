<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import AudienceSelector from '@/components/AudienceSelector.vue';
import { useI18n } from '@/composables/useI18n';
import { routes } from '@/config/routes';

const props = defineProps<{
    roles: string[];
    buildings: Array<{ id: number; name: string }>;
}>();

const { t } = useI18n();

const form = useForm({
    title: '',
    body: '',
    audience_type: 'community_all',
    roles_selected: [] as string[],
    buildings_selected: [] as number[],
    units_selected: [] as number[],
    published: false,
});

const submit = () => {
    form.post(routes.announcementsStore);
};
</script>

<template>
    <Head :title="t('announcements.create_title')" />

    <AppLayout>
        <PageHeader :title="t('announcements.create_title')" />

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg dark:bg-gray-800 p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <!-- Title -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ t('announcements.field_title') }}
                            </label>
                            <input
                                v-model="form.title"
                                type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 sm:text-sm"
                                required
                            />
                            <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</div>
                        </div>

                        <!-- Body -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ t('announcements.field_body') }}
                            </label>
                            <textarea
                                v-model="form.body"
                                rows="6"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 sm:text-sm"
                                required
                            ></textarea>
                            <div v-if="form.errors.body" class="text-red-500 text-sm mt-1">{{ form.errors.body }}</div>
                        </div>

                        <!-- Audience -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                {{ t('announcements.targeting_section') }}
                            </h3>
                            <AudienceSelector
                                v-model="form.audience_type"
                                :roles="roles"
                                :buildings="buildings"
                                v-model:selectedRoles="form.roles_selected"
                                v-model:selectedBuildings="form.buildings_selected"
                            />
                        </div>

                        <!-- Published -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <div class="flex items-center">
                                <input
                                    id="published"
                                    v-model="form.published"
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                <label for="published" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
                                    {{ t('announcements.publish_immediately') }}
                                </label>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <Link
                                :href="routes.announcementsIndex"
                                class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
                            >
                                {{ t('common.cancel') }}
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md disabled:opacity-50"
                            >
                                {{ t('common.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
