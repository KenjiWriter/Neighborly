<script setup lang="ts">
import { useI18n } from '@/composables/useI18n';

const props = defineProps<{
    modelValue: string;
    roles: string[];
    buildings: Array<{ id: number; name: string }>;
    selectedRoles?: string[];
    selectedBuildings?: number[];
    selectedUnits?: number[]; // Placeholder if we implement unit selection
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
    (e: 'update:selectedRoles', value: string[]): void;
    (e: 'update:selectedBuildings', value: number[]): void;
}>();

const { t } = useI18n();

const audienceTypes = [
    'community_all',
    'residents_all',
    'staff_all',
    'roles_selected',
    'buildings_selected',
    // 'units_selected' - omitting for now as UI complexity is high without building filter
];

const updateRoles = (event: Event) => {
    const target = event.target as HTMLSelectElement;
    const values = Array.from(target.selectedOptions).map(option => option.value);
    emit('update:selectedRoles', values);
};

const updateBuildings = (event: Event) => {
    const target = event.target as HTMLSelectElement;
    const values = Array.from(target.selectedOptions).map(option => Number(option.value));
    emit('update:selectedBuildings', values);
};
</script>

<template>
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                {{ t('announcements.audience_type') }}
            </label>
            <select
                :value="modelValue"
                @input="$emit('update:modelValue', ($event.target as HTMLSelectElement).value)"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 sm:text-sm"
            >
                <option v-for="type in audienceTypes" :key="type" :value="type">
                    {{ t(`announcements.audience_types.${type}`) }}
                </option>
            </select>
        </div>

        <div v-if="modelValue === 'roles_selected'">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                {{ t('announcements.audience_types.roles_selected') }}
            </label>
            <div class="mt-2 space-y-2">
                <div v-for="role in roles" :key="role" class="flex items-center">
                    <input
                        type="checkbox"
                        :id="`role-${role}`"
                        :value="role"
                        :checked="selectedRoles?.includes(role)"
                        @change="(e) => {
                            const newRoles = [...(selectedRoles || [])];
                            if ((e.target as HTMLInputElement).checked) {
                                newRoles.push(role);
                            } else {
                                const index = newRoles.indexOf(role);
                                if (index > -1) newRoles.splice(index, 1);
                            }
                            $emit('update:selectedRoles', newRoles);
                        }"
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                    />
                    <label :for="`role-${role}`" class="ml-2 text-sm text-gray-900 dark:text-gray-100">
                        {{ role }}
                    </label>
                </div>
            </div>
            <p v-if="!selectedRoles?.length" class="text-xs text-red-500 mt-1">Select at least one role</p>
        </div>

        <div v-if="modelValue === 'buildings_selected'">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                {{ t('announcements.audience_types.buildings_selected') }}
            </label>
            <select
                multiple
                :value="selectedBuildings"
                @change="updateBuildings"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 sm:text-sm min-h-[100px]"
            >
                <option v-for="building in buildings" :key="building.id" :value="building.id">
                    {{ building.name }}
                </option>
            </select>
            <p class="text-xs text-gray-500 mt-1">{{ t('common.hold_ctrl_click') }}</p>
        </div>
    </div>
</template>
