<script setup>
import { FileQuestion } from 'lucide-vue-next';

defineProps({
    title: {
        type: String,
        required: true
    },
    description: String
});
</script>

<template>
    <div class="flex flex-col items-center justify-center p-12 text-center border-2 border-dashed rounded-lg border-muted-foreground/25">
        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-muted/50 mb-4">
            <slot name="icon">
                <FileQuestion class="h-6 w-6 text-muted-foreground" />
            </slot>
        </div>
        <h3 class="text-lg font-medium tracking-tight">{{ title }}</h3>
        <p v-if="description" class="text-sm text-muted-foreground mt-1 max-w-sm">
            {{ description }}
        </p>
        <div class="mt-6">
            <slot name="action" />
        </div>
    </div>
</template>
