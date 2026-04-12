# Laravel Daisy Kit

Laravel Daisy Kit is a reusable UI package for Laravel applications built with **Blade**, **DaisyUI 5**, and **Tailwind CSS 4**.

It provides:

- Blade components under the `daisy::` namespace
- reusable page templates
- package translations
- optional prebuilt frontend assets
- a DataTables-based table component styled for DaisyUI

This package is designed to be installed directly in supported Laravel applications with a standard `composer require`, without version bypasses.

## Support

- PHP `^8.1`
- Laravel `10.x`, `11.x`, `12.x`, and `13.x`

Compatibility follows the Laravel major in use:

- Laravel 10: PHP 8.1+
- Laravel 11: PHP 8.2+
- Laravel 12: PHP 8.2+
- Laravel 13: PHP 8.3+

The Composer constraints intentionally include all minor and patch releases inside those major versions.

## Installation

Install the package:

```bash
composer require art35rennes/laravel-daisy-kit
```

The service provider is registered automatically via Laravel package discovery.

## Quick start

Publish the package configuration:

```bash
php artisan vendor:publish --tag=daisy-config
```

If you want to use the prebuilt assets shipped by the package, publish them as well:

```bash
php artisan vendor:publish --tag=daisy-assets
```

Assets are published to `public/vendor/art35rennes/laravel-daisy-kit`, which matches the default `daisy-kit.vite_build_directory` configuration.

You can then use the package Blade namespace in your views:

```blade
<x-daisy::ui.inputs.button>
    Save
</x-daisy::ui.inputs.button>
```

## Publishing assets and views

The package supports several publish tags depending on how much of the UI layer you want to own in the host application.

| Tag | Purpose |
| --- | --- |
| `daisy-config` | `config/daisy-kit.php` |
| `daisy-assets` | Built CSS/JS and Vite manifest under `public/vendor/art35rennes/laravel-daisy-kit` |
| `daisy-views` | Blade components to `resources/views/vendor/daisy/components` |
| `daisy-templates` | Templates to `resources/views/vendor/daisy/templates` |
| `daisy-lang` | Language files to `resources/lang/vendor/daisy` |
| `daisy-assets-source` | Package `resources/js` and `resources/css` into `resources/vendor/daisy-kit/` for a host-owned Vite pipeline |
| `daisy-src` | Same as `daisy-assets-source` (legacy alias) |

If the host application republishes source assets with `daisy-assets-source`, it becomes responsible for rebuilding those assets in its own frontend pipeline.

## Configuration

The published `config/daisy-kit.php` file contains the full configuration surface. The most relevant options are:

- `auto_assets` — push default CSS/JS into Blade stacks when enabled.
- `use_vite` / `vite_build_directory` — resolve hashed assets from the published manifest.
- `bundle` — fallback paths when no manifest is available.
- `csrf_refresh` — optional JSON endpoint for CSRF token refresh (path, route name, middleware); can be disabled.
- `themes` — DaisyUI built-in and custom theme definitions for host Tailwind/daisyUI setup.
- `trusted_html` — documents that some props accept trusted HTML; never pass unsanitized user input.

## Package contents

This repository contains package code only:

- `src/`
- `config/daisy-kit.php`
- `resources/views`, `resources/lang`, `resources/js`, `resources/css`
- tests in `tests/`

Application-specific pages, documentation screens, and browser-level integration live in the companion repository `laravel-daisy-kit-demo`.

Public identifiers:

- PHP namespace: `Art35rennes\DaisyKit`
- Blade namespace: `daisy::`

## Security

- The package ships reusable library UI only; sanitization of user content remains the host application’s responsibility.
- When `csrf_refresh` is enabled, restrict middleware and path appropriately for your app.
- Advanced components and templates may accept trusted HTML or SVG for rich rendering. Do not pass raw user content into those surfaces without sanitizing in the host app.

## DataTable

The package exposes a single DataTables-based component:

- `x-daisy::ui.data-display.datatable`

It is intended to cover both simple enhanced tables and server-side DataTables integrations while keeping a package-owned styling layer consistent with DaisyUI.

This component follows native DataTables 2 semantics:

- `serverSide=false`: the table rows are rendered in HTML and enhanced locally by DataTables
- `serverSide=true`: DataTables delegates paging, search, ordering, and filtering to the server through `ajax`

The host application only needs the package assets. It does not manually import jQuery, DataTables CSS, or instantiate `new DataTable(...)`.

### Migration note

Older table components have been removed:

- `x-daisy::ui.data-display.table`
- `x-daisy::ui.advanced.table`

Migrate them to `x-daisy::ui.data-display.datatable`:

- Replace locally rendered tables with `x-daisy::ui.data-display.datatable` and `serverSide=false`
- Replace dynamically loaded tables with `x-daisy::ui.data-display.datatable`, `serverSide=true`, and an `ajax` configuration compatible with DataTables
- Remove any legacy hooks or assumptions tied to `data-simple-table`, `data-advanced-table`, and `advanced-table.js`

### Supported options

The component exposes a controlled subset of DataTables options through component props and the `options` array:

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

### Local table example

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

### Server-side example

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

The goal is to keep DataTables visually aligned with the active DaisyUI theme without forcing a package-specific design preset.

Implementation notes:

- the package keeps the native DataTables 2 `layout` structure instead of rebuilding the control bar in Blade
- DataTables default pagination uses transparent backgrounds and gradient hover states, so the package explicitly remaps paging buttons to DaisyUI-compatible surface, border, hover, active, and disabled states
- the CSS layer uses DaisyUI v5 semantic variables such as `--color-base-100`, `--color-base-200`, and `--color-base-content`, with legacy fallbacks where needed

## TreeView lazy loading

The tree view component supports two lazy-loading strategies when `lazyUrl` is provided.

### Progressive loading on node expand

Use `lazyMode="progressive"` to fetch a lazy branch the first time the user opens that node.

```blade
<x-daisy::ui.advanced.tree-view
    :data="[
        [
            'id' => 'root',
            'label' => 'Racine',
            'children' => [
                ['id' => 'folder-a', 'label' => 'Dossier A'],
                ['id' => 'folder-b', 'label' => 'Dossier B', 'lazy' => true],
            ],
        ],
    ]"
    lazy-url="/demo/api/tree-children"
    lazy-mode="progressive"
/>
```

### Automatic preload

Use `lazyMode="auto"` to preload lazy branches as soon as the tree is initialized. Loaded groups stay visually collapsed until the user opens them.

```blade
<x-daisy::ui.advanced.tree-view
    :data="[
        [
            'id' => 'alpha',
            'label' => 'Projet Alpha',
            'children' => [
                ['id' => 'docs', 'label' => 'Documentation', 'lazy' => true],
                ['id' => 'src', 'label' => 'Sources', 'lazy' => true],
            ],
        ],
    ]"
    lazy-url="/demo/api/tree-children"
    lazy-mode="auto"
/>
```

### Optional branch reload

By default, a lazy branch is fetched once, then reused. Set `lazyReload` if you want a fresh request on every reopen.

```blade
<x-daisy::ui.advanced.tree-view
    :data="$nodes"
    :lazy-url="route('demo.api.tree-children')"
    lazy-mode="progressive"
    :lazy-reload="true"
/>
```

## Testing

Tests in `tests/` cover package-level behavior such as component rendering, helpers, service provider registration, and package routes.

For local package development:

```bash
composer install
npm install
composer test
npm run build
```

When changing compatibility constraints, validate the package against a real host application on each supported Laravel major.

## Demo app

For full application-level integration, use the companion repository `laravel-daisy-kit-demo`.

To work on both side by side, place the repositories next to each other and point the demo application's Composer `path` repository to `../laravel-daisy-kit`.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for released versions.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for development and release guidelines.
