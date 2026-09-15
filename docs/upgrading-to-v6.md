# Upgrading to v6

v6.0.0 is a breaking release relative to v5.0.0 and earlier releases. Test the
upgrade on a branch before changing a production application's lock file.

1. Inventory package component tags and imports. The complete supported surface
   is the thirteen entries in [the v6 contract](specs/v6-public-contract.md).
   Remove `forms.viewer`, `forms.builder`, their imports and package-specific
   Livewire integration. Reimplement required forms in the host application.
2. Keep PHP 8.4+ and Laravel 13, with host-owned Tailwind CSS and DaisyUI.
   Declare the GitHub VCS repository and require `art35rennes/laravel-daisy-kit`
   at `^6.0`. Update Composer and commit its lock file. No npm package exists.
3. Configure the stable `@daisy-kit` Vite alias to the Composer package's `dist`
   directory. Import matching JS/CSS pairs and explicitly mount the modules.
   Remove old auto-bootstrap code, aliases and published package assets.
4. Adopt the current props, facade methods, event payloads and submission shapes
   from the contract. Historical alpha dialects are unsupported; consult the
   Table, Tree and Map examples rather than retaining old configuration keys.
5. Apply each module's documented CSP policy. Signature, Transfer List and WYSIWYG need
   the style-attribute exception. WYSIWYG also needs a Trix style nonce and optional
   `img-src blob:`. File Preview renders documents in its own
   sandbox. Do not add a proxy, public route or manual asset copy for previews.
6. Build the application and exercise keyboard, native form submission, loading,
   empty/error states and responsive layouts. Unmount modules when their host
   DOM is removed. Test optional host Livewire navigation explicitly.

The package does not provide migration scripts, fallback aliases or adapters.
Existing v4/v5 tags remain available. If the application cannot yet migrate,
retain its previous lock file rather than mixing old templates with v6 assets.
