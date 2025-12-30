<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import AudienceSelector from '@/components/AudienceSelector.vue';
import { useI18n } from '@/composables/useI18n';
import { routes } from '@/config/routes';

const props = defineProps<{
    poll: {
        id: number;
        title: string;
        description: string | null;
        starts_at: string;
        ends_at: string;
        audience_type: string;
    };
    roles: string[];
    buildings: Array<{ id: number; name: string }>;
    targeted_roles: string[];
    targeted_buildings: number[];
}>();

const { t } = useI18n();

const form = useForm({
    title: props.poll.title,
    description: props.poll.description,
    starts_at: props.poll.starts_at,
    ends_at: props.poll.ends_at,
    audience_type: props.poll.audience_type,
    roles_selected: props.targeted_roles,
    buildings_selected: props.targeted_buildings,
    units_selected: [] as number[],
});

const submit = () => {
    form.put(routes.pollsUpdate(props.poll.id));
};
</script>

<template>
    <Head :title="t('polls.edit_title')" />

    <AppLayout>
        <PageHeader :title="t('polls.edit_title')" />

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg dark:bg-gray-800 p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <!-- Title -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ t('polls.field_title') }}
                            </label>
                            <input
                                v-model="form.title"
                                type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 sm:text-sm"
                                required
                            />
                            <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ t('polls.field_description') }}
                            </label>
                            <textarea
                                v-model="form.description"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 sm:text-sm"
                            ></textarea>
                            <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</div>
                        </div>

                        <!-- Timeframe -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ t('polls.starts_at') }}
                                </label>
                                <input
                                    v-model="form.starts_at"
                                    type="datetime-local"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 sm:text-sm"
                                    required
                                />
                                <div v-if="form.errors.starts_at" class="text-red-500 text-sm mt-1">{{ form.errors.starts_at }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ t('polls.ends_at') }}
                                </label>
                                <input
                                    v-model="form.ends_at"
                                    type="datetime-local"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 sm:text-sm"
                                    required
                                />
                                <div v-if="form.errors.ends_at" class="text-red-500 text-sm mt-1">{{ form.errors.ends_at }}</div>
                            </div>
                        </div>

                        <div class="p-4 bg-yellow-50 dark:bg-yellow-900/10 rounded border border-yellow-200 dark:border-yellow-800">
                            <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                {{ t('polls.edit_warning_options') }}
                            </p>
                        </div>

                        <!-- Audience -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                {{ t('polls.targeting_section') }}
                            </h3>
                            <AudienceSelector
                                v-model="form.audience_type"
                                :roles="roles"
                                :buildings="buildings"
                                v-model:selectedRoles="form.roles_selected"
                                v-model:selectedBuildings="form.buildings_selected"
                            />
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <Link
                                :href="routes.pollsIndex"
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
