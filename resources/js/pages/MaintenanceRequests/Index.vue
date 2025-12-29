<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { useI18n } from '@/composables/useI18n';
import { Link, usePage } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';

const { t } = useI18n();
const page = usePage();

defineProps({
    requests: Object,
});

const statusVariant = (status) => {
    switch (status) {
        case 'open': return 'default'; // or specific color
        case 'in_progress': return 'secondary';
        case 'closed': return 'outline';
        default: return 'default';
    }
};
</script>

<template>
    <AppLayout :title="t('maintenance.title')">
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="flex justify-between items-center">
                    <PageHeader :title="t('maintenance.title')" :description="t('maintenance.description')" />
                    <Button 
                        v-if="page.props.auth.can.createMaintenanceRequest"
                        as-child
                    >
                        <Link :href="route('maintenance.create')">
                            {{ t('maintenance.create') }}
                        </Link>
                    </Button>
                </div>

                <div class="bg-white p-4 shadow sm:rounded-lg">
                    <div v-if="requests.data.length === 0" class="text-center py-6 text-gray-500">
                        {{ t('maintenance.no_requests') }}
                    </div>
                    <div v-else class="space-y-4">
                        <div 
                            v-for="req in requests.data" 
                            :key="req.id"
                            class="border-b pb-4 last:border-b-0 last:pb-0 flex justify-between items-start"
                        >
                            <div>
                                <h3 class="text-lg font-medium">
                                    <Link :href="route('maintenance.show', req.id)" class="hover:underline text-indigo-600">
                                        {{ req.title }}
                                    </Link>
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ t('maintenance.unit') }}: {{ req.unit ? req.unit.label : '-' }} | 
                                    {{ t('maintenance.created_by') }}: {{ req.creator.name }}
                                </p>
                            </div>
                            <Badge :variant="statusVariant(req.status)">
                                {{ t('status.' + req.status) }}
                            </Badge>
                        </div>
                    </div>
                    
                    <!-- Pagination could go here -->
                </div>
            </div>
        </div>
    </AppLayout>
</template>
