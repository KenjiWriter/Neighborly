<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import FormInput from '@/components/Form/FormInput.vue';
import { useI18n } from '@/composables/useI18n';
import { useForm } from '@inertiajs/vue3';
import { Label } from '@/components/ui/label';
// import { Textarea } from '@/components/ui/textarea'; // Assuming standard Shadcn Textarea exists or use native

const { t } = useI18n();

const props = defineProps({
    units: Array,
});

const form = useForm({
    title: '',
    description: '',
    unit_id: props.units.length === 1 ? props.units[0].id : '',
});

const submit = () => {
    form.post(route('maintenance.store'));
};
</script>

<template>
    <AppLayout :title="t('maintenance.create_new')">
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <PageHeader :title="t('maintenance.create_new')" />

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 max-w-xl">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div v-if="units.length > 0">
                            <Label for="unit_id">{{ t('maintenance.unit') }}</Label>
                            <select 
                                id="unit_id" 
                                v-model="form.unit_id"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            >
                                <option value="" disabled>{{ t('maintenance.select_unit') }}</option>
                                <option v-for="unit in units" :key="unit.id" :value="unit.id">
                                    {{ unit.label }}
                                </option>
                            </select>
                            <p v-if="form.errors.unit_id" class="text-sm text-red-600 mt-2">{{ form.errors.unit_id }}</p>
                        </div>

                        <FormInput
                            id="title"
                            v-model="form.title"
                            :label="t('maintenance.title_label')"
                            required
                            :error="form.errors.title"
                        />

                        <div class="grid gap-2">
                             <Label for="description">{{ t('maintenance.description_label') }}</Label>
                             <textarea
                                id="description"
                                v-model="form.description"
                                class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                required
                             ></textarea>
                             <p v-if="form.errors.description" class="text-sm text-red-600">{{ form.errors.description }}</p>
                        </div>

                        <div class="flex items-center justify-end">
                            <Button :disabled="form.processing">
                                {{ t('common.submit') }}
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
