<script setup>
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { cn } from '@/lib/utils';
import { useVModel } from '@vueuse/core';

const props = defineProps({
    modelValue: [String, Number],
    name: String,
    label: String,
    placeholder: String,
    error: String,
    required: Boolean,
    rows: Number,
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
        <textarea
            :id="name"
            v-model="model"
            :rows="rows || 3"
            :placeholder="placeholder"
            :class="cn(
                'flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50',
                 { 'border-destructive focus-visible:ring-destructive': error },
                props.class
            )"
        ></textarea>
        <InputError :message="error" />
    </div>
</template>
