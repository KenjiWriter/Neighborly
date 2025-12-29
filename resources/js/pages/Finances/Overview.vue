<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import { useI18n } from '@/composables/useI18n';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { usePage } from '@inertiajs/vue3';

const { t } = useI18n();
const page = usePage();

const props = defineProps({
    stats: Object,
    recentEntries: Object,
    canViewDetails: Boolean,
});

const formatCurrency = (amountCents) => {
    return new Intl.NumberFormat(page.props.locale === 'pl' ? 'pl-PL' : 'en-US', {
        style: 'currency',
        currency: page.props.locale === 'pl' ? 'PLN' : 'USD', 
    }).format(amountCents / 100);
};

</script>

<template>
    <AppLayout :title="t('finance.overview')">
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <PageHeader :title="t('finance.overview')" :description="t('finance.description')" />

                <!-- Aggregated Stats Cards -->
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                             <CardTitle class="text-sm font-medium">{{ t('finance.total_income') }}</CardTitle>
                        </CardHeader>
                        <CardContent>
                             <div class="text-2xl font-bold text-green-600">{{ formatCurrency(stats.total_income) }}</div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                             <CardTitle class="text-sm font-medium">{{ t('finance.total_expense') }}</CardTitle>
                        </CardHeader>
                        <CardContent>
                             <div class="text-2xl font-bold text-red-600">{{ formatCurrency(stats.total_expense) }}</div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Monthly Summary Chart/Table -->
                 <Card>
                    <CardHeader>
                        <CardTitle>{{ t('finance.monthly_summary') }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-2">
                            <div v-for="(data, month) in stats.monthly" :key="month" class="flex justify-between items-center border-b py-2 last:border-0">
                                <span class="font-medium">{{ month }}</span>
                                <div class="space-x-4">
                                    <span v-for="entry in data" :key="entry.type" :class="entry.type === 'income' ? 'text-green-600' : 'text-red-600'">
                                        {{ t('finance.' + entry.type) }}: {{ formatCurrency(entry.total) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Detailed Entries (Staff Only) -->
                <div v-if="canViewDetails && recentEntries" class="space-y-4">
                    <h3 class="text-lg font-medium">{{ t('finance.recent_entries') }}</h3>
                    <div class="bg-white p-4 shadow sm:rounded-lg overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ t('finance.date') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ t('finance.type') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ t('finance.category') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ t('finance.amount') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ t('finance.description_label') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="entry in recentEntries.data" :key="entry.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ entry.occurred_on }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <Badge :variant="entry.type === 'income' ? 'default' : 'destructive'">
                                            {{ t('finance.' + entry.type) }}
                                        </Badge>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ entry.category }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" :class="entry.type === 'income' ? 'text-green-600' : 'text-red-600'">
                                        {{ formatCurrency(entry.amount_cents) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ entry.description || '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <!-- Pagination would go here -->
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
