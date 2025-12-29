<script setup lang="ts">
import FormSelect from '@/components/Form/FormSelect.vue';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { useForm, Head } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';

const { t } = useI18n();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    community_id: '',
    building_id: '',
    unit_id: '',
});

const communities = ref<{ id: number; name: string }[]>([]);
const buildings = ref<{ id: number; name: string }[]>([]);
const units = ref<{ id: number; label: string }[]>([]);

const loadingCommunities = ref(false);
const loadingBuildings = ref(false);
const loadingUnits = ref(false);

const submit = () => {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const fetchCommunities = async () => {
    loadingCommunities.value = true;
    try {
        const response = await axios.get('/registration-options/communities');
        communities.value = response.data;
    } catch (e) {
        console.error(e);
    } finally {
        loadingCommunities.value = false;
    }
};

const fetchBuildings = async (communityId: string) => {
    if (!communityId) {
        buildings.value = [];
        return;
    }
    loadingBuildings.value = true;
    try {
        const response = await axios.get(`/registration-options/buildings`, {
            params: { community_id: communityId }
        });
        buildings.value = response.data;
    } catch (e) {
        console.error(e);
    } finally {
        loadingBuildings.value = false;
    }
};

const fetchUnits = async (buildingId: string) => {
    if (!buildingId) {
        units.value = [];
        return;
    }
    loadingUnits.value = true;
    try {
        const response = await axios.get(`/registration-options/units`, {
            params: { building_id: buildingId }
        });
        units.value = response.data;
    } catch (e) {
        console.error(e);
    } finally {
        loadingUnits.value = false;
    }
};

watch(() => form.community_id, (newVal) => {
    form.building_id = '';
    form.unit_id = '';
    buildings.value = [];
    units.value = [];
    if (newVal) fetchBuildings(newVal);
});

watch(() => form.building_id, (newVal) => {
    form.unit_id = '';
    units.value = [];
    if (newVal) fetchUnits(newVal);
});

onMounted(() => {
    fetchCommunities();
});
</script>

<template>
    <AuthBase
        title="Create an account"
        description="Enter your details below to create your account"
    >
        <Head title="Register" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <!-- Name -->
                <div class="grid gap-2">
                    <Label for="name">{{ t('auth.name') }}</Label>
                    <Input
                        id="name"
                        v-model="form.name"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="name"
                        :placeholder="t('auth.name')"
                    />
                    <InputError :message="form.errors.name" />
                </div>

                <!-- Email -->
                <div class="grid gap-2">
                    <Label for="email">{{ t('auth.email') }}</Label>
                    <Input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        :tabindex="2"
                        autocomplete="email"
                        :placeholder="t('auth.email')"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <!-- Community Select -->
                <div class="grid gap-2">
                    <FormSelect
                        v-model="form.community_id"
                        :options="communities.map(c => ({ value: c.id, label: c.name }))"
                        :label="t('registration.community')"
                        :placeholder="loadingCommunities ? t('registration.loading_communities') : t('registration.select_community')"
                        :error="form.errors.community_id"
                        :tabindex="3"
                        required
                    />
                </div>

                <!-- Building Select -->
                <div class="grid gap-2">
                    <FormSelect
                        v-model="form.building_id"
                        :options="buildings.map(b => ({ value: b.id, label: b.name }))"
                        :label="t('registration.building')"
                        :placeholder="loadingBuildings ? t('registration.loading_buildings') : (buildings.length ? t('registration.select_building') : t('registration.no_buildings'))"
                        :error="form.errors.building_id"
                        :disabled="!form.community_id"
                        :tabindex="4"
                        required
                    />
                </div>

                <!-- Unit Select -->
                <div class="grid gap-2">
                    <FormSelect
                        v-model="form.unit_id"
                        :options="units.map(u => ({ value: u.id, label: u.label }))"
                        :label="t('registration.unit')"
                        :placeholder="loadingUnits ? t('registration.loading_units') : (units.length ? t('registration.select_unit') : t('registration.no_units'))"
                        :error="form.errors.unit_id"
                        :disabled="!form.building_id"
                        :tabindex="5"
                        required
                    />
                </div>

                <!-- Password -->
                <div class="grid gap-2">
                    <Label for="password">{{ t('auth.password') }}</Label>
                    <Input
                        id="password"
                        v-model="form.password"
                        type="password"
                        required
                        :tabindex="6"
                        autocomplete="new-password"
                        :placeholder="t('auth.password')"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <!-- Confirm Password -->
                <div class="grid gap-2">
                    <Label for="password_confirmation">{{ t('auth.confirm_password') }}</Label>
                    <Input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        required
                        :tabindex="7"
                        autocomplete="new-password"
                        :placeholder="t('auth.confirm_password')"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <Button
                    type="submit"
                    class="mt-2 w-full"
                    :tabindex="8"
                    :disabled="form.processing"
                    data-test="register-user-button"
                >
                    <Spinner v-if="form.processing" />
                    {{ t('auth.register') }}
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                {{ t('auth.already_registered') }}
                <TextLink
                    href="/login"
                    class="underline underline-offset-4"
                    :tabindex="9"
                >
                    {{ t('auth.login') }}
                </TextLink>
            </div>
        </form>
    </AuthBase>
</template>
