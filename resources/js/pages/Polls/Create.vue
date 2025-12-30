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
    description: '',
    starts_at: '',
    ends_at: '',
    options: ['', ''],
    audience_type: 'community_all',
    roles_selected: [] as string[],
    buildings_selected: [] as number[],
    units_selected: [] as number[],
});

const addOption = () => form.options.push('');
const removeOption = (index: number) => form.options.splice(index, 1);

const submit = () => {
    form.post(routes.pollsStore);
};
</script>

<template>
    <Head :title="t('polls.create_title')" />

    <AppLayout>
        <PageHeader :title="t('polls.create_title')" />

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

                        <!-- Options -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('polls.options_label') }}
                            </label>
                            <div class="space-y-3">
                                <div v-for="(option, index) in form.options" :key="index" class="flex gap-2">
                                    <input
                                        v-model="form.options[index]"
                                        type="text"
                                        placeholder="Option text..."
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 sm:text-sm"
                                        required
                                    />
                                    <button
                                        type="button"
                                        @click="removeOption(index)"
                                        class="text-red-600 hover:text-red-800"
                                        v-if="form.options.length > 2"
                                    >
                                        &times;
                                    </button>
                                </div>
                            </div>
                            <button
                                type="button"
                                @click="addOption"
                                class="mt-2 text-sm text-indigo-600 hover:text-indigo-800"
                            >
                                + {{ t('polls.add_option') }}
                            </button>
                            <div v-if="form.errors.options" class="text-red-500 text-sm mt-1">{{ form.errors.options }}</div>
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
