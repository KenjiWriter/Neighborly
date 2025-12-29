<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import FormInput from '@/components/Form/FormInput.vue';
import { useI18n } from '@/composables/useI18n';

const { t } = useI18n();

defineProps({
    community: Object,
});
</script>

<template>
    <AppLayout :title="t('community.title')">
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <PageHeader :title="community.name" :description="t('community.summary')" />

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 max-w-xl">
                    <section class="space-y-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                             <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">{{ t('community.buildings') }}</label>
                                <div class="mt-1 text-2xl font-semibold text-gray-900">{{ community.buildings_count }}</div>
                             </div>
                             <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">{{ t('community.units') }}</label>
                                <div class="mt-1 text-2xl font-semibold text-gray-900">{{ community.units_count }}</div>
                             </div>
                        </div>

                        <div class="pt-4 border-t border-gray-200">
                             <FormInput 
                                label="Slug" 
                                :model-value="community.slug" 
                                disabled 
                            />
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
