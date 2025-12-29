# Agent Rules

## Code Quality
- **Clarity over Cleverness**: distinct, readable code is preferred over one-liners.
- **Atomic Commits**: Group changes logically.
- **No Unused Code**: Clean up debug artifacts.

## Internationalization (i18n)
- **Strict Rule**: No hardcoded English strings in Vue `template` or `script` tags for UI.
- **Exceptions**: Logging, internal error codes, or developer-only text.
- **Validation**: Backend validation messages should use Laravel's validation translation files.

## Workflow
- **Phase-Locked**: Do not implement features from future phases.
- **Playground**: Use `Pages/Playground.vue` for testing components locally.
