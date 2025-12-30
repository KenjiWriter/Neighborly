<script setup>
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { cn } from '@/lib/utils';
import { useVModel } from '@vueuse/core';

const props = defineProps({
    modelValue: [String, Number],
    name: String,
    label: String,
    options: {
        type: Array, // Expected: [{ value, label }]
        required: true
    },
    placeholder: String,
    error: String,
    required: Boolean,
    disabled: Boolean,
    tabindex: [String, Number],
    class: String
});

const emit = defineEmits(['update:modelValue']);

const model = useVModel(props, 'modelValue', emit);
</script>

<template>
    <div class="grid w-full items-center gap-1.5">
        <Label v-if="label" :for="name" :class="{ 'text-destructive': error }">
            {{ label }} <span v-if="required" class="text-destructive">*</span>
        </Label>
        <select
            :id="name"
            v-model="model"
            :disabled="disabled"
            :tabindex="tabindex"
            :class="cn(
                'flex h-9 w-full items-center justify-between rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50',
                { 'border-destructive focus-visible:ring-destructive': error },
                props.class
            )"
        >
            <option v-if="placeholder" value="" disabled selected>{{ placeholder }}</option>
            <option v-for="option in options" :key="option.value" :value="option.value">
                {{ option.label }}
            </option>
        </select>
        <InputError :message="error" />
    </div>
</template>
