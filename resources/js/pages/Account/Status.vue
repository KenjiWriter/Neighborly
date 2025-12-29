<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3'; // Added proper import
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button'; // Added import for Button
import { useI18n } from '@/composables/useI18n';
import { routes } from '@/config/routes';

const { t } = useI18n();
const page = usePage();
const user = page.props.auth.user;

const statusVariant = (status: string) => {
    switch(status) {
        case 'approved': return 'default';
        case 'rejected': return 'destructive';
        default: return 'secondary';
    }
}
</script>

<template>
    <AppLayout :title="t('verification.status_title')">
        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-6">{{ t('verification.account_status') }}</h2>

                    <div class="space-y-6">
                        <div class="flex items-center justify-between border-b pb-4">
                            <span class="text-gray-600">{{ t('verification.current_status') }}</span>
                            <Badge :variant="statusVariant(user.verification_status)" class="capitalize">
                                {{ user.verification_status }}
                            </Badge>
                        </div>

                        <div v-if="user.verification_status === 'approved'" class="text-center py-4">
                            <p class="text-green-600 font-medium mb-4">{{ t('verification.approved_message') }}</p>
                            <Button as-child>
                                <Link :href="routes.dashboard">{{ t('audit.go_dashboard') }}</Link> <!-- Reusing existing key or will add new -->
                            </Button>
                        </div>

                        <div v-else-if="user.verification_status === 'rejected'" class="text-center py-4">
                            <p class="text-red-600 font-medium mb-2">{{ t('verification.rejected_message') }}</p>
                            <p v-if="user.rejection_reason" class="text-sm text-gray-500 bg-gray-50 p-2 rounded">
                                {{ user.rejection_reason }}
                            </p>
                        </div>

                        <div v-else class="text-center py-4">
                            <p class="text-gray-600 mb-2">{{ t('verification.pending_long_message') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
