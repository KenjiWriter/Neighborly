<script setup>
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import InputError from '@/components/InputError.vue';
import { useVModel } from '@vueuse/core';

const props = defineProps({
    modelValue: [String, Number],
    name: String,
    label: String,
    placeholder: String,
    error: String,
    type: String,
    required: Boolean
});

const emit = defineEmits(['update:modelValue']);

const model = useVModel(props, 'modelValue', emit);
</script>

<template>
    <div class="grid w-full items-center gap-1.5">
        <Label v-if="label" :for="name" :class="{ 'text-destructive': error }">
            {{ label }} <span v-if="required" class="text-destructive">*</span>
        </Label>
        <Input
            :id="name"
            v-model="model"
            :type="type || 'text'"
            :placeholder="placeholder"
            :class="{ 'border-destructive focus-visible:ring-destructive': error }"
        />
        <InputError :message="error" />
    </div>
</template>
