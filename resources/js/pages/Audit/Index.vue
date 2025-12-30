<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import { useI18n } from '@/composables/useI18n';
import { routes } from '@/config/routes';

// Define Props
const props = defineProps<{
    logs: {
        data: Array<{
            id: number;
            created_at: string;
            event_key: string;
            actor: { id: number; name: string } | null;
            metadata: Record<string, any>;
            ip_address: string | null;
            user_agent: string | null;
            auditable_type: string | null;
            auditable_id: number | null;
        }>;
        links: any[]; // Pagination links
    };
    filters: {
        event_key?: string;
        from_date?: string;
        to_date?: string;
    };
}>();

const { t } = useI18n();

const formatMetadata = (metadata: Record<string, any>) => {
    if (!metadata || Object.keys(metadata).length === 0) return '-';
    return JSON.stringify(metadata, null, 2);
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleString();
};

const onFilterChange = (key: string, value: string) => {
    const currentFilters = { ...props.filters, [key]: value };
    router.get(routes.auditIndex, currentFilters, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

</script>

<template>
    <Head :title="t('audit.title')" />

    <AppLayout>
        <PageHeader 
            :title="t('audit.title')" 
            :description="t('audit.description')" 
        />

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800 p-6">
                    
                    <!-- Filters -->
                    <div class="mb-6 flex gap-4 flex-wrap">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ t('audit.filters.event') }}
                            </label>
                            <select 
                                :value="props.filters.event_key"
                                @change="onFilterChange('event_key', ($event.target as HTMLSelectElement).value)"
                                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600"
                            >
                                <option value="">All Events</option>
                                <option value="maintenance.created">{{ t('audit.events.maintenance.created') }}</option>
                                <option value="maintenance.assigned">{{ t('audit.events.maintenance.assigned') }}</option>
                                <option value="maintenance.status_changed">{{ t('audit.events.maintenance.status_changed') }}</option>
                                <option value="finance.entry_created">{{ t('audit.events.finance.entry_created') }}</option>
                                <option value="documents.uploaded">{{ t('audit.events.documents.uploaded') }}</option>
                                <option value="documents.downloaded">{{ t('audit.events.documents.downloaded') }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ t('audit.filters.from') }}
                            </label>
                            <input 
                                type="date"
                                :value="props.filters.from_date"
                                @change="onFilterChange('from_date', ($event.target as HTMLInputElement).value)"
                                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ t('audit.filters.to') }}
                            </label>
                            <input 
                                type="date"
                                :value="props.filters.to_date"
                                @change="onFilterChange('to_date', ($event.target as HTMLInputElement).value)"
                                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600"
                            />
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ t('audit.date') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ t('audit.event') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ t('audit.actor') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ t('audit.details') }}
                                    </th>
                                    <th v-if="$page.props.auth.roles.includes('admin')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ t('audit.ip_address') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                <tr v-for="log in logs.data" :key="log.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ formatDate(log.created_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ t(`audit.events.${log.event_key}`) !== `audit.events.${log.event_key}` ? t(`audit.events.${log.event_key}`) : log.event_key }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        <span v-if="log.actor">{{ log.actor.name }}</span>
                                        <span v-else class="text-gray-400">System</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                        <pre class="text-xs bg-gray-50 dark:bg-gray-900 p-2 rounded max-w-md overflow-auto">{{ formatMetadata(log.metadata) }}</pre>
                                    </td>
                                    <td v-if="log.ip_address" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ log.ip_address }}
                                        <div class="text-xs text-gray-400 truncate max-w-xs" :title="log.user_agent || ''">
                                            {{ log.user_agent }}
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="logs.data.length === 0">
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                        {{ t('audit.empty') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="logs.data.length > 0" class="mt-6 flex gap-2 flex-wrap">
                        <Link 
                            v-for="link in logs.links" 
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
