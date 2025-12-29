<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import { useI18n } from '@/composables/useI18n';

const { t } = useI18n();
</script>

<template>
    <AppLayout :title="t('dashboard.title')">
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <PageHeader :title="t('dashboard.title')" :description="t('dashboard.welcome')" />
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        {{ t('dashboard.logged_in') }}
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
