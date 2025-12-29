<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import FormInput from '@/components/Form/FormInput.vue';
import { useI18n } from '@/composables/useI18n';
import { useForm, usePage } from '@inertiajs/vue3';
import { Label } from '@/components/ui/label';
import { computed } from 'vue';

const { t } = useI18n();
const page = usePage();

const props = defineProps({
    maintenanceRequest: Object,
    isRedacted: Boolean,
    providers: Array,
});

const formAssign = useForm({
    assigned_to_user_id: props.maintenanceRequest.assigned_to_user_id || '',
});

const submitAssign = () => {
    formAssign.patch(route('maintenance.assign', props.maintenanceRequest.id), {
        preserveScroll: true,
    });
};

const updateStatus = (status) => {
    if (confirm(t('common.confirm_action'))) {
        useForm({ status }).patch(route('maintenance.status', props.maintenanceRequest.id));
    }
};

const canAssign = computed(() => props.providers.length > 0);
// Check if user is board or admin (creator of policy allows them to update status freely)
// Check if user is provider (can only close if assigned)
const user = page.props.auth.user; 
// Simplified role checks on frontend for UI gating. Real check is backend.
const isProvider = computed(() => page.props.auth.roles.includes('service_provider'));
const isStaff = computed(() => page.props.auth.roles.includes('board_member') || page.props.auth.roles.includes('admin'));

const showAssign = computed(() => isStaff.value && props.providers.length > 0);

</script>

<template>
    <AppLayout :title="t('maintenance.details')">
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="flex justify-between items-start">
                    <PageHeader :title="props.maintenanceRequest.title" :description="t('maintenance.details')" />
                    <Badge>{{ t('status.' + props.maintenanceRequest.status) }}</Badge>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2 space-y-6">
                        <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                             <h3 class="text-lg font-medium text-gray-900 mb-4">{{ t('maintenance.description_label') }}</h3>
                             <div class="prose max-w-none text-gray-700">
                                 <p v-if="isRedacted" class="italic text-gray-500">
                                     {{ t('maintenance.redacted') }}
                                 </p>
                                 <p v-else>
                                     {{ props.maintenanceRequest.description }}
                                 </p>
                             </div>
                        </div>
                    </div>

                    <div class="md:col-span-1 space-y-6">
                        <!-- Meta Info -->
                        <div class="bg-white p-4 shadow sm:rounded-lg">
                             <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">{{ t('common.information') }}</h3>
                             <dl class="space-y-2">
                                 <div>
                                     <dt class="text-sm font-medium text-gray-500">{{ t('maintenance.unit') }}</dt>
                                     <dd class="text-sm text-gray-900">{{ maintenanceRequest.unit ? maintenanceRequest.unit.label : '-' }}</dd>
                                 </div>
                                 <div>
                                     <dt class="text-sm font-medium text-gray-500">{{ t('maintenance.created_by') }}</dt>
                                     <dd class="text-sm text-gray-900">{{ maintenanceRequest.creator.name }}</dd>
                                 </div>
                                 <div v-if="maintenanceRequest.assignee">
                                     <dt class="text-sm font-medium text-gray-500">{{ t('maintenance.assigned_to') }}</dt>
                                     <dd class="text-sm text-gray-900">{{ maintenanceRequest.assignee.name }}</dd>
                                 </div>
                             </dl>
                        </div>

                        <!-- Actions -->
                        <div class="bg-white p-4 shadow sm:rounded-lg space-y-4">
                             <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">{{ t('common.actions') }}</h3>
                             
                             <!-- Assign Provider -->
                             <div v-if="showAssign">
                                 <form @submit.prevent="submitAssign" class="space-y-2">
                                     <Label>{{ t('maintenance.assign_provider') }}</Label>
                                     <div class="flex gap-2">
                                         <select 
                                             v-model="formAssign.assigned_to_user_id"
                                             class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                                         >
                                             <option value="" disabled>{{ t('maintenance.select_provider') }}</option>
                                             <option v-for="p in providers" :key="p.id" :value="p.id">
                                                 {{ p.name }}
                                             </option>
                                         </select>
                                         <Button type="submit" size="sm" :disabled="formAssign.processing">
                                             {{ t('common.save') }}
                                         </Button>
                                     </div>
                                 </form>
                             </div>

                             <!-- Status Transitions -->
                             <div class="space-y-2">
                                 <Label>{{ t('maintenance.change_status') }}</Label>
                                 <div class="flex flex-col gap-2">
                                     <!-- Board/Admin Controls -->
                                     <template v-if="isStaff">
                                         <Button 
                                            variant="outline" 
                                            size="sm" 
                                            v-if="maintenanceRequest.status === 'open'"
                                            @click="updateStatus('in_progress')"
                                         >
                                             Mark In Progress
                                         </Button>
                                         <Button 
                                            variant="outline" 
                                            size="sm" 
                                            v-if="maintenanceRequest.status !== 'closed'"
                                            @click="updateStatus('closed')"
                                         >
                                             Mark Closed
                                         </Button>
                                         <Button 
                                            variant="outline" 
                                            size="sm" 
                                            v-if="maintenanceRequest.status === 'closed'"
                                            @click="updateStatus('in_progress')"
                                         >
                                             Re-open
                                         </Button>
                                     </template>

                                     <!-- Provider Controls -->
                                     <template v-if="isProvider">
                                          <Button 
                                            variant="outline" 
                                            size="sm" 
                                            v-if="maintenanceRequest.status === 'in_progress'"
                                            @click="updateStatus('closed')"
                                         >
                                             Mark Closed
                                         </Button>
                                     </template>
                                 </div>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
