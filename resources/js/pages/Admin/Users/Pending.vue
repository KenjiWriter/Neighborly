<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { useI18n } from '@/composables/useI18n';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { format } from 'date-fns';
import { routes } from '@/config/routes';

const { t } = useI18n();

defineProps<{
    users: {
        data: Array<{
            id: number;
            name: string;
            email: string;
            address_line1: string | null;
            city: string | null;
            created_at: string;
        }>;
        links: any[]; 
    };
}>();

const approve = (userId: number) => {
    if (confirm(t('verification.confirm_approve'))) {
        router.patch(routes.adminUsersApprove(userId), {}, {
            preserveScroll: true,
        });
    }
};

const reject = (userId: number) => {
    const reason = prompt(t('verification.enter_rejection_reason'));
    if (reason !== null) {
        router.patch(routes.adminUsersReject(userId), { reason }, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <AppLayout :title="t('verification.admin_pending_title')">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ t('verification.admin_pending_title') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>{{ t('audit.date') }}</TableHead>
                                <TableHead>{{ t('audit.actor') }}</TableHead>
                                <TableHead>Address</TableHead>
                                <TableHead class="text-right">{{ t('common.actions') }}</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="user in users.data" :key="user.id">
                                <TableCell>{{ format(new Date(user.created_at), 'PPP') }}</TableCell>
                                <TableCell>
                                    <div class="font-medium">{{ user.name }}</div>
                                    <div class="text-sm text-gray-500">{{ user.email }}</div>
                                </TableCell>
                                <TableCell>
                                    <div v-if="user.address_line1">
                                        {{ user.address_line1 }}<br/>
                                        <span v-if="user.city">{{ user.city }}</span>
                                    </div>
                                    <span v-else class="text-gray-400">-</span>
                                </TableCell>
                                <TableCell class="text-right space-x-2">
                                    <Button size="sm" variant="default" @click="approve(user.id)">
                                        {{ t('verification.approve') }}
                                    </Button>
                                    <Button size="sm" variant="destructive" @click="reject(user.id)">
                                        {{ t('verification.reject') }}
                                    </Button>
                                </TableCell>
                            </TableRow>
                            <TableRow v-if="users.data.length === 0">
                                <TableCell colspan="4" class="text-center py-6 text-gray-500">
                                    {{ t('audit.no_records') }}
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
