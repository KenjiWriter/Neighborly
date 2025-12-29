<script setup>
import Heading from '@/components/Heading.vue';

defineProps({
    title: {
        type: String,
        required: true
    },
    description: String
});
</script>

<template>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0 pb-6">
        <Heading :title="title" :description="description" />
        <div class="flex items-center gap-4">
            <slot />
        </div>
    </div>
</template>
