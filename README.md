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

## AI-aware host integration

If the host application uses Laravel Boost, this package ships third-party Boost guidance and a reusable skill under `resources/boost/...` to bias AI agents toward reusing package UI instead of recreating it.

In the host application:

- run `php artisan boost:install` once if Boost has not been set up yet
- run `php artisan boost:update` after adding or updating this package so the host refreshes third-party guidelines and skills

The shipped guidance points agents toward:

- existing `x-daisy::layout.*`, `x-daisy::ui.*`, and `x-daisy::templates.*` aliases
- vendor overrides under `resources/views/vendor/daisy/...`
- a generated component and template catalog derived from the package Blade surface

For package maintainers, regenerate that catalog after any public Blade surface change:

```bash
composer ai:catalog
```

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

## Table component

The package exposes a progressive table component aligned with Blade, DaisyUI, and TanStack Table:

- `x-daisy::ui.data-display.table`

The Blade view renders semantic HTML and DaisyUI controls. The package JavaScript enhances the component on `[data-daisy-table="1"]` and uses `@tanstack/table-core` as the headless state engine for sorting, filtering, pagination, and column visibility.

### Breaking change

This release removes the DataTables/jQuery-based public API:

- `x-daisy::ui.data-display.datatable` now throws an explicit migration error
- `x-daisy::ui.advanced.table` remains removed
- DataTables options such as `ajax`, `options`, `responsive`, `layout`, `pageLength`, `ordering`, `language`, and `scrollX` are no longer supported

Migration guidance:

- Replace `x-daisy::ui.data-display.datatable` with `x-daisy::ui.data-display.table`
- Replace `data` with `rows`
- Replace `serverSide=true` with `mode="server"`
- Replace `ajax` with `endpoint` and `method`
- Replace DataTables server endpoints with the JSON contract documented below

### Props

Supported public props:

- `columns`
- `rows`
- `mode="client|server"`
- `endpoint` when `mode="server"`
- `method`
- `serverAdapter`
- `persistState`
- `stateKey`
- `globalFilterKey`
- `filters`
- `initialState`
- `pageSizeOptions`
- `search`
- `columnVisibility`
- `caption`
- `size`
- `zebra`
- `pinRows`
- `pinCols`
- `emptyLabel`
- `loadingLabel`
- `containerClass`
- `tableClass`

Column definition shape:

```php
[
    [
        'key' => 'name',
        'label' => 'Name',
        'sortable' => true,
        'filterable' => true,
        'sortKey' => 'users.name',
        'filterKey' => 'name',
        'filter' => [
            'type' => 'text',
        ],
        'visible' => true,
        'width' => '16rem',
        'cellClass' => 'font-medium',
        'headerClass' => 'whitespace-nowrap',
        'html' => false,
    ],
]
```

### Example: client-side table

```blade
<x-daisy::ui.data-display.table
    mode="client"
    :columns="[
        ['key' => 'name', 'label' => 'Name', 'sortable' => true],
        ['key' => 'email', 'label' => 'Email', 'sortable' => true],
        ['key' => 'status', 'label' => 'Status', 'html' => true],
    ]"
    :rows="$users->map(fn ($user) => [
        'name' => $user->name,
        'email' => $user->email,
        'status' => view('users.partials.status-badge', ['user' => $user])->render(),
    ])"
    :initial-state="[
        'sorting' => [['id' => 'name', 'desc' => false]],
        'pagination' => ['pageIndex' => 0, 'pageSize' => 10],
    ]"
    :page-size-options="[10, 25, 50]"
    zebra
    search
    column-visibility
/>
```

### Example: server-side table

```blade
<x-daisy::ui.data-display.table
    mode="server"
    server-adapter="spatie-query-builder"
    persist-state="url"
    state-key="users-table"
    :columns="[
        ['key' => 'name', 'label' => 'Name', 'sortable' => true, 'filterable' => true, 'sortKey' => 'name', 'filterKey' => 'name', 'filter' => ['type' => 'text']],
        ['key' => 'email', 'label' => 'Email', 'sortable' => true, 'filterable' => true, 'sortKey' => 'email', 'filterKey' => 'email', 'filter' => ['type' => 'text']],
        [
            'key' => 'status',
            'label' => 'Status',
            'html' => true,
            'sortable' => true,
            'filterable' => true,
            'sortKey' => 'status',
            'filterKey' => 'status',
            'filter' => [
                'type' => 'select',
                'options' => [
                    ['value' => 'active', 'label' => 'Active'],
                    ['value' => 'invited', 'label' => 'Invited'],
                    ['value' => 'archived', 'label' => 'Archived'],
                ],
            ],
        ],
    ]"
    :endpoint="route('users.table')"
    method="GET"
    :initial-state="[
        'sorting' => [['id' => 'name', 'desc' => false]],
        'pagination' => ['pageIndex' => 0, 'pageSize' => 25],
    ]"
    :page-size-options="[10, 25, 50]"
    zebra
    search
/>
```

### Example: Spatie Query Builder backend

```php
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

QueryBuilder::for(User::query())
    ->allowedSorts(['name', 'email', 'status'])
    ->allowedFilters([
        'name',
        'email',
        'status',
        AllowedFilter::partial('global'),
    ])
    ->paginate(request('page.size', 25))
    ->appends(request()->query());
```

### Server contract

Default package server adapter request payload:

```json
{
  "pageIndex": 0,
  "pageSize": 25,
  "sorting": [
    { "id": "name", "desc": false }
  ],
  "globalFilter": "jane",
  "columnFilters": [
    { "id": "status", "value": "active" }
  ],
  "columnVisibility": {
    "email": true,
    "status": true
  }
}
```

Response payload:

```json
{
  "rows": [
    {
      "id": 1,
      "name": "Jane Doe",
      "email": "jane@example.com",
      "status": "<span class=\"badge badge-success\">Active</span>"
    }
  ],
  "rowCount": 128,
  "pageCount": 6,
  "state": {
    "pageIndex": 0,
    "pageSize": 25
  },
  "meta": {
    "availableFilters": {
      "status": [
        {"label": "Active", "value": "active"},
        {"label": "Suspended", "value": "suspended"}
      ]
    }
  }
}
```

### Spatie Query Builder adapter contract

When `server-adapter="spatie-query-builder"` is enabled, the runtime sends:

- `sort=name,-created_at`
- `filter[status]=active`
- `filter[global]=jane`
- `page[number]=3`
- `page[size]=25`

Expected response shape:

```json
{
  "data": [
    {
      "id": 1,
      "name": "Jane Doe",
      "email": "jane@example.com",
      "status": "<span class=\"badge badge-success\">Active</span>"
    }
  ],
  "meta": {
    "current_page": 3,
    "per_page": 25,
    "total": 128,
    "last_page": 6
  }
}
```

Notes:

- `filter[global]` is the default global search key in Spatie mode and can be changed with `globalFilterKey`.
- The host app must explicitly allow every filter and sort used by the component.
- URL persistence uses the adapter-native query string so copied links stay backend-compatible.

### Upgrade notes

- There is no compatibility layer for DataTables requests or responses.
- Responsive details rows, export buttons, row selection, and virtualization are out of scope for v1.
- The runtime keeps auto-bootstrap semantics, but the global API is now `window.DaisyTable`.
- `serverAdapter="spatie-query-builder"` is additive; the package JSON server contract remains the default.
