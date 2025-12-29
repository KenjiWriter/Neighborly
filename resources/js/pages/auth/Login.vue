<script setup lang="ts">
import FormInput from '@/components/Form/FormInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

const { t } = useI18n();

defineProps<{
    status?: string;
    canResetPassword?: boolean;
    canRegister?: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <AuthBase
        :title="t('auth.login.title', 'Log in to your account')"
        :description="t('auth.login.description', 'Enter your email and password below to log in')"
    >
        <Head :title="t('auth.login.title')" />

        <div
            v-if="status"
            class="mb-4 text-center text-sm font-medium text-green-600"
        >
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <!-- Email -->
                <div class="grid gap-2">
                    <FormInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        name="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        :label="t('auth.fields.email')"
                        :placeholder="t('auth.email_placeholder', 'email@example.com')"
                        :error="form.errors.email"
                    />
                </div>

                <!-- Password -->
                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">{{ t('auth.fields.password') }}</Label>
                        <TextLink
                            v-if="canResetPassword"
                            href="/forgot-password"
                            class="text-sm"
                            :tabindex="5"
                        >
                            {{ t('auth.links.forgot_password') }}
                        </TextLink>
                    </div>
                    <FormInput
                        id="password"
                        v-model="form.password"
                        type="password"
                        name="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        :placeholder="t('auth.fields.password')"
                        :error="form.errors.password"
                    />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox 
                            id="remember" 
                            name="remember" 
                            :checked="form.remember" 
                            @update:checked="form.remember = $event"
                            :tabindex="3" 
                        />
                        <span>{{ t('auth.remember_me') }}</span>
                    </Label>
                </div>

                <Button
                    type="submit"
                    class="mt-4 w-full"
                    :tabindex="4"
                    :disabled="form.processing"
                    data-test="login-button"
                >
                    <Spinner v-if="form.processing" />
                    {{ t('auth.actions.login') }}
                </Button>
            </div>

            <div
                class="text-center text-sm text-muted-foreground"
                v-if="canRegister"
            >
                {{ t('auth.links.not_registered', "Don't have an account?") }}
                <TextLink href="/register" :tabindex="5">{{ t('auth.actions.register') }}</TextLink>
            </div>
        </form>
    </AuthBase>
</template>
