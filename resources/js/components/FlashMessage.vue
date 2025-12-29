<script setup>
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { CheckCircle2, AlertTriangle, Info } from 'lucide-vue-next';

const page = usePage();
const flash = computed(() => page.props.flash);
const show = computed(() => !!(flash.value?.success || flash.value?.error || flash.value?.message));

const type = computed(() => {
    if (flash.value?.error) return 'destructive';
    if (flash.value?.success) return 'success';
    return 'default';
});

const icon = computed(() => {
    if (type.value === 'destructive') return AlertTriangle;
    if (type.value === 'success') return CheckCircle2;
    return Info;
});
</script>

<template>
    <div v-if="show" class="mb-6">
        <Alert :variant="type === 'destructive' ? 'destructive' : 'default'" :class="{ 'border-green-500 text-green-700 dark:border-green-400 dark:text-green-300': type === 'success' }">
            <component :is="icon" class="h-4 w-4" />
            <AlertTitle v-if="type === 'success'">Success</AlertTitle>
            <AlertTitle v-if="type === 'destructive'">Error</AlertTitle>
            <AlertDescription>
                {{ flash.success || flash.error || flash.message }}
            </AlertDescription>
        </Alert>
    </div>
</template>
