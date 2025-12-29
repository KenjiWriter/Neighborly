<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { useI18n } from '@/composables/useI18n';
import { useForm, usePage } from '@inertiajs/vue3';
import { Upload } from 'lucide-vue-next';
import { routes } from '@/config/routes';

const { t } = useI18n();
const page = usePage();

// In a real app, we would fetch communities if Admin, for now assume user context.
const form = useForm({
    file: null,
    community_id: page.props.auth.user.community_id, // Default to user's community
});

const submit = () => {
    form.post(routes.documentsStore, {
        forceFormData: true,
    });
};
</script>

<template>
    <AppLayout :title="t('documents.upload')">
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <PageHeader :title="t('documents.upload')" />

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 max-w-xl">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-2">
                             <Label for="file">{{ t('documents.select_file') }}</Label>
                             <input 
                                id="file" 
                                type="file" 
                                @input="form.file = $event.target.files[0]"
                                class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                             />
                             <p v-if="form.errors.file" class="text-sm text-red-600">{{ form.errors.file }}</p>
                        </div>
                        
                        <!-- Implicit Community ID hidden input if needed, or handled in controller via user scope -->
                        <input type="hidden" v-model="form.community_id" />

                        <div class="flex items-center justify-end">
                            <Button :disabled="form.processing">
                                <Upload class="mr-2 h-4 w-4" />
                                {{ t('documents.upload_action') }}
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
