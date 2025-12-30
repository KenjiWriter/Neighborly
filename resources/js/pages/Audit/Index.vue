<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useI18n } from '@/composables/useI18n';
import { watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { routes } from '@/config/routes';

// Define Props
defineProps<{
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
    };
}>();

const { t } = useI18n();

const formatMetadata = (metadata: Record<string, any>) => {
    // Simple JSON stringify for now, or use a nicer component
    return JSON.stringify(metadata, null, 2);
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleString();
};

const onFilterChange = (key: string, value: string) => {
    router.get(routes.auditIndex, { [key]: value }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

</script>

<template>
    <Head :title="t('audit.title')" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ t('audit.title') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg dark:bg-gray-800 p-6">
                    
                    <!-- Filters -->
                    <div class="mb-4 flex gap-4">
                        <Input 
                            :placeholder="t('audit.filter_event_key')" 
                            :model-value="filters.event_key"
                            @update:model-value="(v) => onFilterChange('event_key', v as string)"
                            class="max-w-xs"
                        />
                    </div>

                    <!-- Table -->
                     <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>{{ t('audit.date') }}</TableHead>
                                <TableHead>{{ t('audit.event') }}</TableHead>
                                <TableHead>{{ t('audit.actor') }}</TableHead>
                                <TableHead>{{ t('audit.details') }}</TableHead>
                                <TableHead v-if="$page.props.auth.roles.includes('admin')">{{ t('audit.ip_address') }}</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="log in logs.data" :key="log.id">
                                <TableCell>{{ formatDate(log.created_at) }}</TableCell>
                                <TableCell>{{ t(`events.${log.event_key}`) !== `events.${log.event_key}` ? t(`events.${log.event_key}`) : log.event_key }}</TableCell>
                                <TableCell>
                                    <span v-if="log.actor">{{ log.actor.name }}</span>
                                    <span v-else class="text-gray-400">System</span>
                                </TableCell>
                                <TableCell>
                                    <pre class="text-xs">{{ formatMetadata(log.metadata) }}</pre>
                                </TableCell>
                                <TableCell v-if="log.ip_address">
                                    {{ log.ip_address }} 
                                    <div class="text-xs text-gray-400 truncate w-32" :title="log.user_agent || ''">{{ log.user_agent }}</div>
                                </TableCell>
                            </TableRow>
                            <TableRow v-if="logs.data.length === 0">
                                <TableCell colspan="5" class="text-center">{{ t('audit.no_records') }}</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>

                    <!-- Pagination -->
                    <div class="mt-4 flex gap-2">
                        <Link 
                            v-for="link in logs.links" 
                            :key="link.label"
                            :href="link.url ?? '#'"
                            class="px-3 py-1 border rounded"
                            :class="{'bg-gray-200': link.active, 'opacity-50': !link.url}"
                            v-html="link.label"
                        /> 
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>
