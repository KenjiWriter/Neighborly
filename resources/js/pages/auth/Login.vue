<script setup>
import FormInput from '@/components/Form/FormInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { store } from '@/routes/login';
import { Form, Head } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

const { t } = useI18n();

defineProps({
    status: String,
    canResetPassword: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <AuthBase
        :title="t('auth.login_title', 'Log in to your account')"
        :description="t('auth.login_description', 'Enter your email and password below to log in')"
    >
        <Head :title="t('auth.login')" />

        <div
            v-if="status"
            class="mb-4 text-center text-sm font-medium text-green-600"
        >
            {{ status }}
        </div>

        <Form
            v-bind="store.form()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <!-- Email -->
                <div class="grid gap-2">
                    <FormInput
                        id="email"
                        type="email"
                        name="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        :label="t('auth.email')"
                        :placeholder="t('auth.email_placeholder', 'email@example.com')"
                        :error="errors.email"
                    />
                </div>

                <!-- Password -->
                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">{{ t('auth.password') }}</Label>
                        <TextLink
                            v-if="canResetPassword"
                            href="/forgot-password"
                            class="text-sm"
                            :tabindex="5"
                        >
                            {{ t('auth.forgot_password') }}
                        </TextLink>
                    </div>
                    <FormInput
                        id="password"
                        type="password"
                        name="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        :placeholder="t('auth.password')"
                        :error="errors.password"
                    />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox id="remember" name="remember" :tabindex="3" />
                        <span>{{ t('auth.remember_me') }}</span>
                    </Label>
                </div>

                <Button
                    type="submit"
                    class="mt-4 w-full"
                    :tabindex="4"
                    :disabled="processing"
                    data-test="login-button"
                >
                    <Spinner v-if="processing" />
                    {{ t('auth.login') }}
                </Button>
            </div>

            <div
                class="text-center text-sm text-muted-foreground"
                v-if="canRegister"
            >
                {{ t('auth.not_registered', "Don't have an account?") }}
                <TextLink href="/register" :tabindex="5">{{ t('auth.register') }}</TextLink>
            </div>
        </Form>
    </AuthBase>
</template>
