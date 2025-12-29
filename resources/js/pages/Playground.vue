<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import FormInput from '@/components/Form/FormInput.vue';
import FormSelect from '@/components/Form/FormSelect.vue';
import FormTextarea from '@/components/Form/FormTextarea.vue';
import FlashMessage from '@/components/FlashMessage.vue';
import EmptyState from '@/components/EmptyState.vue';
import { Button } from '@/components/ui/button';
import { useI18n } from '@/composables/useI18n';
import { ref } from 'vue';

const { t } = useI18n();

const form = ref({
    text: '',
    select: '',
    textarea: '',
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Playground', href: '#' },
];
</script>

<template>
    <Head title="Playground" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 max-w-4xl mx-auto space-y-12">
            
            <section>
                <div class="mb-4">
                    <h2 class="text-xl font-bold">I18n Test</h2>
                    <p class="text-muted-foreground">{{ t('app.common.dashboard') }} (Should be 'Dashboard' or 'Panel główny')</p>
                    <p class="text-muted-foreground">Dynamic: {{ t('app.common.actions') }}</p>
                </div>
            </section>

            <section>
                <PageHeader title="Component Playground" description="Testing all stubs">
                    <Button>Action Button</Button>
                </PageHeader>
            </section>

            <section class="space-y-6">
                <FlashMessage /> <!-- Will only show if flash exists, tough to test without backend flash -->
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4 border p-4 rounded-lg">
                        <h3 class="font-medium">Form Components</h3>
                        <FormInput 
                            v-model="form.text" 
                            label="Text Input" 
                            placeholder="Type something..." 
                            :error="form.text === 'error' ? 'Simulated error' : ''"
                        />
                        
                        <FormSelect
                            v-model="form.select"
                            label="Select Input"
                            placeholder="Choose an option"
                            :options="[
                                { value: '1', label: 'Option 1' },
                                { value: '2', label: 'Option 2' }
                            ]"
                        />

                        <FormTextarea
                            v-model="form.textarea"
                            label="Text Area"
                            placeholder="Long text..."
                        />
                    </div>

                    <div class="space-y-4 border p-4 rounded-lg">
                        <h3 class="font-medium">Empty State</h3>
                        <EmptyState title="No Data Found" description="This is an empty state component.">
                            <template #action>
                                <Button variant="outline">Create New</Button>
                            </template>
                        </EmptyState>
                    </div>
                </div>
            </section>

        </div>
    </AppLayout>
</template>
