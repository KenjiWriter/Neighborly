<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import { useI18n } from '@/composables/useI18n';
import { routes } from '@/config/routes';

const props = defineProps<{
    poll: {
        id: number;
        title: string;
        description: string | null;
        starts_at: string;
        ends_at: string;
        user_has_voted: boolean;
        options: Array<{
            id: number;
            label: string;
        }>;
    };
}>();

const { t } = useI18n();

const form = useForm({
    poll_option_id: '',
});

const submitVote = () => {
    form.post(routes.pollsVote(props.poll.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="poll.title" />

    <AppLayout>
        <PageHeader :title="poll.title" />

        <div class="py-6">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800 p-6">

                    <p v-if="poll.description" class="text-gray-700 dark:text-gray-300 mb-6">
                        {{ poll.description }}
                    </p>

                    <div v-if="poll.user_has_voted" class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded">
                        <p class="text-green-800 dark:text-green-200 text-sm">
                            {{ t('polls.already_voted') }}
                        </p>
                    </div>

                    <form v-else @submit.prevent="submitVote" class="space-y-4">
                        <div class="space-y-2">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ t('polls.select_option') }}
                            </p>
                            <div v-for="option in poll.options" :key="option.id" class="flex items-center">
                                <input
                                    :id="`option-${option.id}`"
                                    v-model="form.poll_option_id"
                                    :value="option.id"
                                    type="radio"
                                    name="poll_option"
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                />
                                <label
                                    :for="`option-${option.id}`"
                                    class="ml-3 block text-sm text-gray-900 dark:text-gray-100"
                                >
                                    {{ option.label }}
                                </label>
                            </div>
                        </div>

                        <div class="pt-4">
                            <button
                                type="submit"
                                :disabled="!form.poll_option_id || form.processing"
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md disabled:opacity-50"
                            >
                                {{ form.processing ? t('common.saving') : t('polls.submit_vote') }}
                            </button>
                        </div>
                    </form>

                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <Link
                            :href="routes.pollsIndex"
                            class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400"
                        >
                            ← {{ t('polls.back_to_list') }}
                        </Link>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>
