# Upgrade Guide

## Legacy status

`v4.0.0` is the final continuity release of the current Laravel Daisy Kit architecture. Existing integrations may remain on `^4.0` without migrating while the package is redesigned separately. This legacy line has no planned feature work; migration guidance for the redesign will be published with that separate release.

## Upgrading from 3.x to 4.0

Laravel Daisy Kit 4 targets the current Laravel and Livewire generations. Before updating the package, move the host application to:

- PHP 8.3 or later
- Laravel 13
- Livewire 4.3 or later when the Form Kit builder is used

Update the host dependencies together so Composer can resolve the new baseline:

```bash
composer require laravel/framework:^13.0 livewire/livewire:^4.3 art35rennes/laravel-daisy-kit:^4.0 --with-all-dependencies
php artisan optimize:clear
```

Review the official [Livewire 4 upgrade guide](https://livewire.laravel.com/docs/4.x/upgrading), especially host-owned Livewire configuration, custom endpoints, `wire:model` modifiers, transitions, and JavaScript hooks. Daisy Kit continues to register its class-based `daisy.form-builder` component automatically.

Republish the distributable assets after the Composer update:

```bash
php artisan vendor:publish --tag=daisy-assets --force
php artisan view:clear
```

Hosts that publish and rebuild the package asset sources instead must refresh their npm dependencies. The source build now uses `docx-preview` 0.4; the prebuilt `daisy-assets` distribution already contains the compatible chunk and needs no host-side npm package.

The v4 release also contains the Tree View v2 contract described in the README and `CHANGELOG.md`. Migrate removed Tree View selection flags, configurable node keys, lazy modes, events, and implicit globals before deploying.

### Table contract v2

Republish assets and Blade views together. The serialized table configuration now contains `contractVersion: 2`; a version mismatch raises an explicit runtime error instead of continuing with incompatible assets.

Host attributes such as `id`, `class`, and `data-*` are rendered on the table component root. Internal attributes (`data-module`, `data-daisy-table`, and `data-table-config`) cannot be overridden. Use the root `id` with `window.DaisyTable.table(id)`; the method now returns `null` when the table is absent and no longer exposes mutable `.context` state.

Replace legacy trusted HTML columns:

```php
// Before
['key' => 'status', 'html' => true]

// Daisy Kit 4
['key' => 'status', 'cell' => ['renderer' => 'trusted-html']]
```

Raw HTML action cells are no longer supported. Use structured `actions` descriptors and handle `daisy:table-row-action`, or use an explicitly trusted Blade renderer when server-rendered markup is required. Structured actions require a stable `row-key`.

Client data mutations require a `row-key`; `setRows()` validates it recursively and rejects missing or duplicate identifiers. Update endpoints receive exactly `{ rowId, column, value, dirty }`, create endpoints receive `{ values }`, and successful remote responses must contain `{ row }` with a valid row key. `{rowId}` placeholders are URL-encoded. CSRF headers are only attached to same-origin requests.

URL persistence is now namespaced as `daisy-table[<state-key>]`, preserves host query parameters, and excludes selection and expansion by default. Add `expanded` or `rowSelection` to `persist-state-fields` only when sharing that transient state is intentional.
