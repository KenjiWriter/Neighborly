import { usePage } from '@inertiajs/vue3';

export function useI18n() {
    const t = (key, replacements = {}) => {
        const page = usePage();
        let translations = page.props.translations;

        // Handle lazy-loaded translations (function)
        if (typeof translations === 'function') {
            translations = translations();
        }

        // Ensure we have the app translations
        if (!translations || !translations.app) {
            // Fallback: return key with dev indicator
            return import.meta.env.DEV ? `__MISSING__${key}` : key;
        }

        const keys = key.split('.');
        let value = translations.app;

        for (const k of keys) {
            if (value && typeof value === 'object' && k in value) {
                value = value[k];
            } else {
                // Return key as fallback
                value = import.meta.env.DEV ? `__MISSING__${key}` : key;
                break;
            }
        }

        // Handle replacements
        if (typeof value === 'string' && Object.keys(replacements).length > 0) {
            Object.keys(replacements).forEach(r => {
                value = value.replace(`:${r}`, replacements[r]);
            });
        }

        return value;
    };

    const getLocale = () => {
        return usePage().props.locale || 'en';
    };

    return { t, getLocale };
}
