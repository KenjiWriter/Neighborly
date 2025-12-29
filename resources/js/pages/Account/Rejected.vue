<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';
import { usePage } from '@inertiajs/vue3';

const { t } = useI18n();
const page = usePage();
const user = page.props.auth.user;
</script>

<template>
    <AppLayout :title="t('verification.rejected_title')">
        <div class="flex min-h-[50vh] flex-col items-center justify-center p-6 text-center">
            <h1 class="text-2xl font-bold tracking-tight text-destructive mb-4">
                {{ t('verification.rejected_title') }}
            </h1>
            <p class="max-w-md text-muted-foreground mb-4">
                {{ t('verification.rejected_message') }}
            </p>
            <div v-if="user.rejection_reason" class="mb-8 p-4 bg-red-50 border border-red-100 rounded-md text-left w-full max-w-md">
                <p class="text-sm font-medium text-red-800 mb-1">{{ t('verification.reason_label') }}:</p>
                <p class="text-sm text-red-600">{{ user.rejection_reason }}</p>
            </div>
            
            <p class="text-sm text-gray-500">
                {{ t('verification.contact_support') }}
            </p>
        </div>
    </AppLayout>
</template>
