<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import FormInput from '@/components/Form/FormInput.vue';
import FormSelect from '@/components/Form/FormSelect.vue';
import { useI18n } from '@/composables/useI18n';
import { routes } from '@/config/routes';

const props = defineProps<{
    user: {
        id: number;
        name: string;
        email: string;
        community_id: number | null;
        roles: Array<{ name: string }>;
    };
    roles: Array<{ value: string; label: string }>;
    communities: Array<{ id: number; name: string }>;
    currentRole: string;
}>();

const { t } = useI18n();

const form = useForm({
    role: props.currentRole || '',
    community_id: props.user.community_id || '',
});

const submit = () => {
    form.patch(routes.adminUsersUpdate(props.user.id), {
        preserveScroll: true,
    });
};

const staffRoles = ['board_member', 'accountant', 'service_provider'];
const requiresCommunity = staffRoles.includes(form.role);
</script>

<template>
    <Head :title="`${t('admin.users.edit_title')} - ${user.name}`" />

    <AppLayout>
        <PageHeader 
            :title="t('admin.users.edit_title')" 
            :description="`${t('common.edit')} ${user.name}`" 
        />

        <div class="py-6">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800 p-6">
                    
                    <form @submit.prevent="submit">
                        <div class="space-y-6">
                            <!-- User Info (Read-only) -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ t('admin.users.name') }}
                                    </label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ user.name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ t('admin.users.email') }}
                                    </label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ user.email }}</p>
                                </div>
                            </div>

                            <!-- Role Select -->
                            <FormSelect
                                v-model="form.role"
                                :label="t('admin.users.role')"
                                :options="roles.map(r => ({ value: r.value, label: r.label }))"
                                :error="form.errors.role"
                                name="role"
                                required
                            />

                            <!-- Community Select (conditional) -->
                            <FormSelect
                                v-if="requiresCommunity || form.role === 'admin'"
                                v-model="form.community_id"
                                :label="t('admin.users.community')"
                                :options="[{ value: '', label: t('common.select') }, ...communities.map(c => ({ value: c.id, label: c.name }))]"
                                :error="form.errors.community_id"
                                :required="requiresCommunity"
                                name="community_id"
                            />

                            <div v-if="requiresCommunity" class="text-sm text-gray-600 dark:text-gray-400">
                                {{ t('admin.users.community_required_hint') }}
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-end gap-3">
                                <button
                                    type="button"
                                    @click="router.visit(routes.adminUsersIndex)"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                                >
                                    {{ t('common.cancel') }}
                                </button>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
                                >
                                    {{ form.processing ? t('common.saving') : t('common.save') }}
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </AppLayout>
</template>
