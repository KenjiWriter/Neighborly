<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';
import { routes } from '@/config/routes';

const { t } = useI18n();
const page = usePage();

defineProps<{
    users: {
        data: Array<{
            id: number;
            name: string;
            email: string;
            address_line1: string | null;
            city: string | null;
            created_at: string;
        }>;
        links: any[]; 
    };
}>();

const formatDate = (value: string | null) => {
    if (!value) return '—';
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return '—';
    const locale = page.props.locale ?? 'en';
    return new Intl.DateTimeFormat(locale, {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date);
};

const approve = (userId: number) => {
    if (confirm(t('admin.pending.confirm_approve'))) {
        router.patch(routes.adminUsersApprove(userId), {}, {
            preserveScroll: true,
        });
    }
};

const reject = (userId: number) => {
    const reason = prompt(t('admin.pending.reason'));
    if (reason !== null) {
        router.patch(routes.adminUsersReject(userId), { reason }, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head :title="t('admin.pending.title')" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ t('admin.pending.title') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    
                    <div v-if="users.data.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                        {{ t('admin.pending.empty') }}
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                        {{ t('audit.date') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                        {{ t('audit.actor') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                        Address
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                        {{ t('common.actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                <tr v-for="user in users.data" :key="user.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ formatDate(user.created_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ user.name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ user.email }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                        <div v-if="user.address_line1">
                                            {{ user.address_line1 }}<br/>
                                            <span v-if="user.city">{{ user.city }}</span>
                                        </div>
                                        <span v-else class="text-gray-400">-</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                                        <button
                                            @click="approve(user.id)"
                                            class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md"
                                        >
                                            {{ t('admin.pending.approve') }}
                                        </button>
                                        <button
                                            @click="reject(user.id)"
                                            class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md"
                                        >
                                            {{ t('admin.pending.reject') }}
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>
