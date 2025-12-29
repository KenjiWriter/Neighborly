<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

const { t } = useI18n();
</script>

<template>
    <Head :title="t('error.forbidden')" />
    
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg text-center">
             <h1 class="text-4xl font-bold text-red-500 mb-4">403</h1>
             <p class="text-lg text-gray-700 mb-6">
                {{ t('error.forbidden_message', 'You do not have permission to access this page.') }}
             </p>
             <a href="/dashboard" class="text-indigo-600 hover:text-indigo-900 underline">
                {{ t('common.back_to_dashboard', 'Back to Dashboard') }}
             </a>
        </div>
    </div>
</template>
