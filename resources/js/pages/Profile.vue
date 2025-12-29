<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import FormInput from '@/components/Form/FormInput.vue';
import { useI18n } from '@/composables/useI18n';
import { usePage } from '@inertiajs/vue3';

const { t } = useI18n();
const user = usePage().props.auth.user;
</script>

<template>
    <AppLayout :title="t('profile.title')">
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <PageHeader :title="t('profile.title')" :description="t('profile.description')" />

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 max-w-xl">
                    <section class="space-y-6">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">{{ t('profile.information') }}</h2>
                        </header>

                        <div class="space-y-4">
                            <!-- Read-only for Phase 1 -->
                            <FormInput 
                                :label="t('auth.name')" 
                                :model-value="user.name" 
                                disabled 
                            />
                            <FormInput 
                                :label="t('auth.email')" 
                                :model-value="user.email" 
                                disabled 
                            />
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
