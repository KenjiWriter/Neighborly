import { usePage } from '@inertiajs/vue3';

export function useI18n() {
    const t = (key, replacements = {}) => {
        const page = usePage();
        const translations = page.props.translations;

        const keys = key.split('.');
        let value = translations['app'];

        for (const k of keys) {
            if (value && typeof value === 'object' && k in value) {
                value = value[k];
            } else {
                value = key;
                break;
            }
        }

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
