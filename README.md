# Laravel Daisy Kit

Reusable Laravel package that ships Blade UI components, page templates, translations, and optional frontend assets built for **DaisyUI 5** and **Tailwind CSS 4**.

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

## Table components

The package now exposes two distinct table layers:

- `x-daisy::ui.data-display.table` is a thin DaisyUI wrapper. It only maps DaisyUI table classes (`table`, `table-zebra`, `table-pin-rows`, `table-pin-cols`, `table-xs|sm|md|lg|xl`) plus the optional responsive wrapper.
- `x-daisy::ui.advanced.table` is the package-level component for server-driven table UX such as row selection, server sort links, loading, empty states, and toolbar/after-table slots.
- `x-daisy::ui.advanced.table` now also supports server pagination, a rows-per-page selector, and Spatie Query Builder-friendly sort URL generation.

### Breaking change

The previous `x-daisy::ui.data-display.table` API based on `headers`, `rows`, `footer`, `selection`, `showRowNumbers`, `offset`, and related JS selection behavior has been removed.

Migration guidance:

- Replace old data-driven usages of `x-daisy::ui.data-display.table` with `x-daisy::ui.advanced.table`.
- Keep `x-daisy::ui.data-display.table` only for DaisyUI-native table markup where you control `<thead>`, `<tbody>`, and `<tfoot>` yourself.
- Replace legacy client-side selection hooks (`data-table-select`, `table:select`) with the new advanced table hook `advanced-table:selection`.

### Advanced table conventions

- `x-daisy::ui.advanced.table` supports both `mode="server"` and `mode="client"` (`auto` selects `server` when `queryBuilder` or `paginator` is present, otherwise `client`).
- Selection is page-scoped by default: the header checkbox selects the current visible page and exposes a mixed state when only some rows are selected.
- In `server` mode, pass a Laravel paginator through `paginator` and the current page rows through `rows`.
- In `client` mode, the component can handle global search, column filters, sorting, and pagination directly in the browser.
- For page-size control, provide `perPageOptions`; the component preserves existing query parameters in `server` mode and updates pagination in place in `client` mode.
- When `queryBuilder=true`, the component renders native Spatie Query Builder-friendly controls:
  - global search via `filter[search]` by default
  - column filters via `filter[column]`
  - sorting via `sort=name` / `sort=-name`
- If `sortUrls` is omitted and a column is marked `sortable`, the component generates Query Builder-compatible sort URLs automatically while preserving the rest of the current query string.

### Simple table pagination

- `x-daisy::ui.data-display.table` also supports pagination with `paginationMode="server"` or `paginationMode="client"`.
- Use this on the thin DaisyUI wrapper when you only need a paginated semantic table, without the richer search/filter/sort UX of `advanced.table`.
