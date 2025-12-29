<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { useI18n } from '@/composables/useI18n';
import { Link, usePage } from '@inertiajs/vue3';
import { FileText, Download, Upload } from 'lucide-vue-next';
import { routes } from '@/config/routes';

const { t } = useI18n();
const page = usePage();

defineProps({
    documents: Object,
    canUpload: Boolean,
});

const formatSize = (bytes) => {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};
</script>

<template>
    <AppLayout :title="t('documents.title')">
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="flex justify-between items-center">
                    <PageHeader :title="t('documents.title')" :description="t('documents.description')" />
                    <Button 
                        v-if="canUpload"
                        as-child
                    >
                        <Link :href="routes.documentsCreate">
                            <Upload class="mr-2 h-4 w-4" />
                            {{ t('documents.upload') }}
                        </Link>
                    </Button>
                </div>

                <div class="bg-white p-4 shadow sm:rounded-lg">
                    <div v-if="documents.data.length === 0" class="text-center py-6 text-gray-500">
                        {{ t('documents.no_documents') }}
                    </div>
                    <div v-else class="space-y-4">
                        <div 
                            v-for="doc in documents.data" 
                            :key="doc.id"
                            class="border-b pb-4 last:border-b-0 last:pb-0 flex justify-between items-center"
                        >
                            <div class="flex items-center space-x-3">
                                <FileText class="h-6 w-6 text-gray-400" />
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">{{ doc.original_name }}</h3>
                                    <p class="text-xs text-gray-500">
                                        {{ formatSize(doc.size_bytes) }} • {{ t('documents.uploaded_by') }} {{ doc.uploader.name }} • {{ new Date(doc.created_at).toLocaleDateString() }}
                                    </p>
                                </div>
                            </div>
                            <Button variant="outline" size="sm" as-child>
                                <a :href="routes.documentsDownload(doc.id)">
                                    <Download class="mr-2 h-4 w-4" />
                                    {{ t('documents.download') }}
                                </a>
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
