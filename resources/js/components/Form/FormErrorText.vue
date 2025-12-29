<script setup>
import InputError from '@/components/InputError.vue';

defineProps({
    message: String
});
</script>

<template>
    <InputError :message="message" />
</template>
