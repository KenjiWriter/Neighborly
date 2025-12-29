<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { useI18n } from '@/composables/useI18n';

const { t } = useI18n();
</script>

<template>
    <AppLayout :title="t('verification.pending_title')">
        <div class="flex min-h-[50vh] flex-col items-center justify-center p-6 text-center">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 mb-4">
                {{ t('verification.pending_title') }}
            </h1>
            <p class="max-w-md text-muted-foreground mb-8">
                {{ t('verification.pending_message') }}
            </p>
            <div class="flex gap-4">
                <Button as-child variant="default">
                    <Link :href="route('account.status')">
                        {{ t('verification.check_status_btn') }}
                    </Link>
                </Button>
                <!-- Logout is handled in nav in layout usually, but we can add one here if needed -->
            </div>
        </div>
    </AppLayout>
</template>
