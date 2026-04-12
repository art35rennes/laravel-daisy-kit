# Laravel Daisy Kit

Reusable Laravel package that ships Blade UI components, page templates, translations, and optional frontend assets built for **DaisyUI 5** and **Tailwind CSS 4**.

## Versioning

This package follows [Semantic Versioning 2.0.0](https://semver.org/lang/fr/).

- `MAJOR`: incompatible change on the public package API
- `MINOR`: backward-compatible feature
- `PATCH`: backward-compatible fix or maintenance change

The initial stable release baseline is `v1.0.0`.
See [CHANGELOG.md](CHANGELOG.md) for released versions and [CONTRIBUTING.md](CONTRIBUTING.md) for the project release rules.

## Requirements

- PHP `^8.2`
- Laravel `^13.0`

## Installation

```bash
composer require art35rennes/laravel-daisy-kit
```

The package registers its service provider automatically. Publish configuration and built assets in your host application (see [Host app integration](#host-app-integration)).

## What this package provides

- **Blade namespace** `daisy::` — use components such as `x-daisy::ui.inputs.button` or `x-daisy::layout.*`.
- **Templates** — reusable views under `daisy::templates.*` (also exposed as anonymous Blade components where applicable).
- **Translations** — `__('daisy::...')` namespace.
- **JavaScript** — a small bootstrap (`window.DaisyKit`) that initializes modules marked with `data-module`; Alpine.js-friendly patterns are used for simple interactions.
- **Optional heavy UI** — components like maps (Leaflet) rely on lazy-loaded chunks; publish built assets so those entry points resolve correctly.

## Package scope

This repository contains only package concerns:

- `src/`
- `config/daisy-kit.php`
- `resources/views`, `resources/lang`, `resources/js`, `resources/css`
- package tests under `tests/`

It does **not** include demo routes, documentation pages, inventory tooling, or browser tests. Those live in the separate companion application repository `laravel-daisy-kit-demo`.

Public identifiers:

- PHP namespace: `Art35rennes\DaisyKit`
- Blade namespace: `daisy::`

## Local package development

```bash
composer install
npm install
composer test
npm run build
```

## Host app integration

### Recommended: published build assets

For a typical host app, publish configuration and the prebuilt Vite manifest and assets:

```bash
php artisan vendor:publish --tag=daisy-config
php artisan vendor:publish --tag=daisy-assets
```

Assets are written to `public/vendor/art35rennes/laravel-daisy-kit`, which matches the default `config('daisy-kit.vite_build_directory')`. The package can load CSS/JS from that manifest without requiring Node tooling in the host.

### Optional publish tags

| Tag | Purpose |
| --- | --- |
| `daisy-config` | `config/daisy-kit.php` |
| `daisy-assets` | Built CSS/JS and Vite manifest under `public/vendor/art35rennes/laravel-daisy-kit` |
| `daisy-views` | Blade components to `resources/views/vendor/daisy/components` |
| `daisy-templates` | Templates to `resources/views/vendor/daisy/templates` |
| `daisy-lang` | Language files to `resources/lang/vendor/daisy` |
| `daisy-assets-source` | Package `resources/js` and `resources/css` into `resources/vendor/daisy-kit/` for a host-owned Vite pipeline |
| `daisy-src` | Same as `daisy-assets-source` (legacy alias) |

If the host rebuilds package sources (`daisy-assets-source`), it must install the matching frontend dependencies and wire its own Vite configuration.

### Configuration highlights

Key keys in `config/daisy-kit.php` (see the published file for the full schema):

- `auto_assets` — push default CSS/JS into Blade stacks when enabled.
- `use_vite` / `vite_build_directory` — resolve hashed assets from the published manifest.
- `bundle` — fallback paths when no manifest is available.
- `csrf_refresh` — optional JSON endpoint for CSRF token refresh (path, route name, middleware); can be disabled.
- `themes` — DaisyUI built-in and custom theme definitions for host Tailwind/daisyUI setup.
- `trusted_html` — documents that some props accept trusted HTML; never pass unsanitized user input.

## Security

- The package ships reusable library UI only; sanitization of user content remains the host application’s responsibility.
- When `csrf_refresh` is enabled, restrict middleware and path appropriately for your app.
- Advanced components and templates may accept trusted HTML or SVG for rich rendering. Do not pass raw user content into those surfaces without sanitizing in the host app.

## Local integration with the demo app

Clone both repositories side by side:

- `laravel-daisy-kit`
- `laravel-daisy-kit-demo`

Point the demo app’s Composer `path` repository at `../laravel-daisy-kit`. This validates the real integration surface while keeping the package installable from Packagist and versioned independently.

## Testing

Tests under `tests/` cover package-only behavior: Blade and template rendering, helpers, and package routes (for example the CSRF token endpoint when enabled). Application-level, navigation, and browser tests belong in the demo repository.

## DataTable component

The package now exposes a single DataTables-based table component:

- `x-daisy::ui.data-display.datatable`

This component follows the native DataTables 2 semantics:

- `serverSide=false`: the table rows are rendered in HTML and enhanced locally by DataTables
- `serverSide=true`: DataTables delegates paging, search, ordering, and filtering to the server through `ajax`

The host app only needs the package Vite entry. It does not manually import jQuery, DataTables CSS, or call `new DataTable(...)`.

### Breaking change

The previous public components have been removed:

- `x-daisy::ui.data-display.table`
- `x-daisy::ui.advanced.table`

Migration guidance:

- Replace locally rendered tables with `x-daisy::ui.data-display.datatable` and `serverSide=false`
- Replace dynamically loaded tables with `x-daisy::ui.data-display.datatable`, `serverSide=true`, and an `ajax` configuration compatible with DataTables
- Remove any legacy hooks or assumptions tied to `data-simple-table`, `data-advanced-table`, and `advanced-table.js`

### Supported options

The component exposes a controlled subset of DataTables options through props plus the `options` array:

- `serverSide`
- `ajax` when `serverSide=true`
- `columns`
- `data` or table slots when `serverSide=false`
- `responsive`
- `paging`
- `pageLength`
- `lengthChange`
- `searching`
- `ordering`
- `order`
- `language`
- `layout`
- `processing`
- `scrollX`

`Responsive` is included in the package integration. `Select` and `Buttons` are intentionally out of scope.

### Example: locally rendered table

```blade
<x-daisy::ui.data-display.datatable
    :columns="[
        ['data' => 'name', 'title' => 'Name'],
        ['data' => 'email', 'title' => 'Email'],
    ]"
    :data="$users"
    zebra
    responsive
/>
```

### Example: server-side DataTables endpoint

```blade
<x-daisy::ui.data-display.datatable
    :server-side="true"
    :responsive="true"
    :ajax="[
        'url' => route('users.datatable'),
        'type' => 'GET',
    ]"
    :columns="[
        ['data' => 'name', 'title' => 'Name', 'name' => 'users.name'],
        ['data' => 'email', 'title' => 'Email', 'name' => 'users.email'],
    ]"
/>
```

### Theme integration

The package ships a DaisyUI-compatible DataTables theme layer:

- generic DaisyUI control classes for search, length select, and paging
- theme-token based colors using DaisyUI surface and content variables
- responsive details styled to match DaisyUI cards and table surfaces

The goal is for DataTables controls to blend into any active DaisyUI theme without forcing a package-specific color preset.

Theme implementation notes:

- the package keeps the native DataTables 2 `layout` structure instead of rebuilding the control bar in Blade
- DataTables default pagination uses transparent backgrounds and gradient hover states, so the package explicitly remaps paging buttons to DaisyUI-compatible surface, border, hover, active, and disabled states
- the CSS layer uses DaisyUI v5 semantic variables such as `--color-base-100`, `--color-base-200`, and `--color-base-content`, with legacy fallbacks where needed
