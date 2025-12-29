# Frontend Conventions

## Folder Structure
- `resources/js/Pages`: Page components (Inertia views).
- `resources/js/Components`: Reusable UI components.
    - `Components/Form`: Form-specific inputs.
- `resources/js/Layouts`: Page layouts (e.g., AppLayout).
- `resources/js/Composables`: Shared logic (Vue composables).

## Naming Conventions
- **Pages**: `PascalCase` matching the route structure (e.g., `Users/Index.vue`, `Users/Edit.vue`).
- **Components**: `PascalCase` (e.g., `PrimaryButton.vue`).
- **Composables**: `camelCase` starting with `use` (e.g., `useI18n.js`).

## Internationalization (i18n)
- **Strings**: ALL user-facing text must be translated.
- **Usage**: Use `const { t } = useI18n()` and `t('key')` in templates/scripts.
- **Keys**: snake_case keys in `resources/lang/{locale}/app.php`.
    - Example: `t('auth.login_failed')`
- **Source of Truth**: Laravel PHP files are the source. Inertia shares them.

## Stubs
- Use the templates in `resources/js/Components` as a base for new UI elements.
- Do not hardcode styles; use the existing component library (Tailwind + standardized components).

## Route Helpers
> [!IMPORTANT]
> **Do not use Wayfinder route helpers (e.g., `login.url()`, `home()`) in critical layouts (Auth layouts) until route generation is stable.**
> Use static paths (e.g., `'/login'`, `'/'`) or Inertia router for now to avoid runtime crashes (`ReferenceError: queryParams is not defined`).
