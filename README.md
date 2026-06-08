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

- PHP `^8.1`
- Laravel `^10.0`, `^11.0`, `^12.0`, or `^13.0`
- Livewire `^3.6` when using `x-daisy::forms.builder`

## Installation

```bash
composer require art35rennes/laravel-daisy-kit
```

The package registers its service provider automatically. For most host applications, publish the configuration and prebuilt assets:

```bash
php artisan vendor:publish --tag=daisy-config
php artisan vendor:publish --tag=daisy-assets
```

Then include the package components in Blade:

```blade
<x-daisy::layout.app title="Dashboard">
    <x-daisy::ui.feedback.alert color="success" session-key="status" dismissible />
</x-daisy::layout.app>
```

If the host renders the Form Kit builder, make sure Livewire 3 is installed and its scripts/styles are present in the application layout. The viewer does not require Livewire; it is rendered by Blade and progressively enhanced by the package JavaScript runtime.

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

For a typical host app, publish configuration and the prebuilt Vite manifest and assets after install and after each package update:

```bash
php artisan vendor:publish --tag=daisy-config
php artisan vendor:publish --tag=daisy-assets
```

Assets are written to `public/vendor/art35rennes/laravel-daisy-kit`, which matches the default `config('daisy-kit.vite_build_directory')`. The package can load CSS/JS from that manifest without requiring Node tooling in the host.

If the host uses the builder surface, install and configure Livewire in the host application as usual. Daisy Kit registers the `daisy.form-builder` Livewire component; the host remains responsible for authentication, authorization, persistence, and where the exported schema JSON is stored.

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

## Breadcrumbs

Use `x-daisy::ui.navigation.breadcrumbs` for manual Laravel breadcrumb trails. It renders accessible `nav` markup by default and keeps the existing `items` array API.

```blade
<x-daisy::ui.navigation.breadcrumbs
    :items="[
        ['label' => 'Dashboard', 'href' => route('dashboard'), 'iconName' => 'bi-house'],
        ['label' => 'Users', 'href' => route('users.index')],
        ['label' => $user->name, 'current' => true],
    ]"
    truncate
    schema
/>
```

Supported item keys:

- `label` — visible text, escaped by default.
- `href` — link target; empty values render as text.
- `current` — marks the page item with `aria-current="page"`.
- `disabled` — renders text with `aria-disabled="true"` and no link.
- `separator` — renders a non-interactive visual separator.
- `iconName` — renders a Blade Icons icon, for example `bi-house`.
- `icon` — accepts plain text or trusted `HtmlString`; plain strings are escaped.
- `iconHtml` — explicit trusted HTML escape hatch for package-controlled icon markup.

For custom markup, provide the list items yourself:

```blade
<x-daisy::ui.navigation.breadcrumbs>
    <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li><span aria-current="page">{{ $pageTitle }}</span></li>
</x-daisy::ui.navigation.breadcrumbs>
```

## Laravel-aware component conveniences

The core layout, form, feedback, and action components expose small Laravel-friendly props so host apps do not have to repeat common wiring.

```blade
<x-daisy::layout.app
    title="Dashboard"
    body-class="app-shell"
    :load-default-font="false"
>
    <x-daisy::ui.feedback.alert color="success" session-key="status" dismissible />

    <x-daisy::ui.partials.form-field name="email" label="Email" hint="Used for login">
        <x-daisy::ui.inputs.input
            name="email"
            :error="$errors->first('email')"
        />
    </x-daisy::ui.partials.form-field>

    <x-daisy::ui.inputs.select
        name="role"
        :value="$user->role"
        :options="[
            ['value' => 'user', 'label' => 'User'],
            ['value' => 'admin', 'label' => 'Administrator'],
        ]"
    />

    <x-daisy::ui.inputs.button icon-name="bi-check" loading>
        Save
    </x-daisy::ui.inputs.button>
</x-daisy::layout.app>
```

Useful defaults:

- `layout.app` accepts `htmlClass`, `bodyClass`, `fontUrl`, and `loadDefaultFont`.
- `layout.navbar-sidebar-layout` and `layout.sidebar-layout` accept `showThemeController`, `themes`, and `themeLabel`.
- `input` and `select` accept `name`, `id`, `value`, `bindOld`, `error`, and accessibility attributes.
- `textarea` mirrors the Laravel-aware input props for old input, validation state, and described-by wiring.
- `checkbox` accepts `name`, `value`, `uncheckedValue`, `bindOld`, and validation state for common form submissions.
- `alert` can render a `sessionKey`, validation errors via `showErrors`, automatic roles, and a dismiss button.
- `empty-state` supports `preset`, `iconName`, and an `actions` slot for no-data/no-results screens.
- `pagination` accepts a Laravel paginator instance and renders page links while preserving the manual API.
- `tabs` accepts `visible`, `iconName`, and `errorKey` item keys for form tabs and conditional navigation.
- `sidebar` accepts `visible`, `activeRoute`, and `activeRoutes` item keys for route-aware menus.
- `dropdown` accepts `id`, `triggerLabel`, and `contentRole` for predictable accessible overlays.
- `stepper` exposes `validateBeforeNext` as a JavaScript data hook for guarded flows.
- `table` accepts `toolbar` and `actions` slots for page-level controls.
- `crud-layout` and `crud-section` provide `header`, `aside`, `headerActions`, and aligned `actions` slots.
- `modal` supports `header`, `footer`, `actions`, `closeLabel`, and labelled dialog markup.

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

## Form Kit

The package exposes a JSON-driven authoring and rendering surface built around one canonical `DaisyFormSchema` payload:

- `x-daisy::forms.viewer` renders a `DaisyFormSchema` `1.0` payload into a progressive HTML form.
- `x-daisy::forms.builder` uses Livewire to edit the same schema, render the real viewer preview, show diagnostics, and export canonical JSON.
- `x-daisy::templates.form.builder` wraps that Livewire builder as an embeddable authoring surface.
- JSONata powers field visibility, complex validation rules, and computed values.

The host application owns persistence, authorization, submission handling, and business workflows. Daisy Kit owns the schema contract, the Livewire builder, the Blade viewer, PHP helpers, and the browser runtime.

### Viewer usage

Use the viewer anywhere a persisted schema should be rendered for data entry:

```blade
<x-daisy::forms.viewer
    id="quote-viewer"
    :schema="$schema"
    :value="$draftValues"
    :errors="$errors"
    validate-on="change"
/>
```

Use the same component for readonly display:

```blade
<x-daisy::forms.viewer
    id="quote-summary"
    :schema="$schema"
    :value="$storedValues"
    :readonly="true"
    submit-mode="none"
/>
```

The viewer reads `schema.submit.mode` by default. Pass `submitMode="event"`, `html`, `fetch`, or `none` only when the host needs to override the schema for a specific render. If neither the prop nor the schema defines a valid mode, the final fallback is `event`.

`validateOn` supports `input`, `change`, and `submit`. Runtime validation is client-side convenience only; validate again in the host application before persisting user data.

### Builder usage

Use the builder in an authenticated authoring screen. The builder state is Livewire-owned, renders the real viewer preview, shows diagnostics, supports nested field reorder, and exports canonical JSON through the configured hidden field name:

```blade
<form method="POST" action="{{ route('forms.update', $form) }}">
    @csrf
    @method('PUT')

    <x-daisy::forms.builder
        name="schema"
        :schema="$form->schema"
        :value="$previewValues"
        :errors="$previewErrors"
        :preview="true"
        :json-editor="true"
    />

    <x-daisy::ui.inputs.button type="submit" color="primary">
        Save schema
    </x-daisy::ui.inputs.button>
</form>
```

For a ready-made authoring wrapper, use:

```blade
<x-daisy::templates.form.builder
    title="Contact form"
    schema-name="schema"
    :schema="$form->schema"
/>
```

The package does not save schemas for you. Persist the posted JSON in the host, then render that stored schema with `x-daisy::forms.viewer`.

Supported layout modes are `one-page`, `sections`, and `multi-step`. Supported field types include native inputs (`text`, `email`, `tel`, `url`, `password`, `number`, `date`, `time`, `datetime-local`, `month`, `color`), text/content controls (`textarea`, `staticText`, `hidden`), choices (`select`, `radio`, `checkbox`, `toggle`, `range`), attachments (`file`, `signature`), and containers (`section`, `tabs`, `wizardStep`).

Field component props are stored in the schema under `attrs.*` and `ui.*`. For example, `ui.width` controls the responsive grid span and signature-specific `attrs.width`, `attrs.height`, `attrs.penColor`, `attrs.showActions`, `attrs.downloadFormat`, and `attrs.downloadFilename` are forwarded to `x-daisy::ui.inputs.sign`.

Server-side JSONata execution is deliberately host-owned. Implement `Art35rennes\DaisyKit\FormKit\Contracts\JsonataEvaluator` to call your own JSONata engine, then use `FormSubmissionEvaluator` to batch visibility, JSONata validations, and computed values before persisting.

### Viewer JavaScript API

Every viewer root is identifiable through `data-form-id` and registers a runtime in `window.DaisyFormViewer`.
The registry intentionally exposes only integration hooks; schema ownership, persistence, and submission policy remain in the host application.

```js
document.getElementById('quote-viewer').addEventListener('daisy-form:ready', async (event) => {
    const runtime = event.detail.runtime;

    runtime.on('daisy-form:submit', (submitEvent) => {
        console.log(submitEvent.detail.values);
    });

    await runtime.setValue('quantity', 3);
    await runtime.validate();
});
```

Runtime methods include:

- `getSchema()`, `getField(key)`, `getVisibleFields()`
- `getValues({ visible: true })`, `getValue(key)`, `setValue(key, value)`, `setValues(values)`, `reset(values)`
- `validate()`, `isValid()`, `getErrors()`, `setErrors(errors)`, `clearErrors()`
- `submit()`, `getSubmitMode()`, `getValidateOn()`, `isReadonly()`
- `getStep()`, `setStep(index)`, `nextStep()`, `previousStep()` for multi-step schemas
- `on(event, listener)`, `off(event, listener)`, `destroy()`

Viewer events bubble from the form root and include `daisy-form:ready`, `daisy-form:change`, `daisy-form:invalid`, `daisy-form:step-change`, `daisy-form:submit`, and `daisy-form:destroy`. Each event detail includes the viewer `id` and the `runtime` instance so hosts can integrate without querying global state.

Readonly viewers keep the same schema/value contract and expose `data-readonly="true"` plus `runtime.isReadonly()`. They render disabled controls and omit submit controls, which lets hosts display stored data without forking the renderer.

```blade
<x-daisy::forms.viewer
    :schema="[
        'version' => '1.0',
        'id' => 'quote',
        'jsonata' => ['engine' => 'jsonata', 'minVersion' => '2.1.0'],
        'fields' => [
            ['id' => 'quantity', 'type' => 'number', 'name' => 'quantity', 'label' => 'Quantity', 'rules' => ['required', 'min:1']],
            ['id' => 'unit_price', 'type' => 'number', 'name' => 'unit_price', 'label' => 'Unit price'],
            [
                'id' => 'total',
                'type' => 'number',
                'name' => 'total',
                'label' => 'Total',
                'computed' => [
                    'type' => 'jsonata',
                    'expression' => '$number(values.quantity) * $number(values.unit_price)',
                    'dependsOn' => ['quantity', 'unit_price'],
                    'mode' => 'readonly',
                ],
            ],
        ],
    ]"
/>
```

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
- `errorLabel`
- `containerClass`
- `tableClass`

Named slots:

- `toolbar` replaces the default search area with host-owned controls.
- `actions` adds page-level controls before filters and pagination controls, for example a Create button.

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

Add page-level controls without forking the table:

```blade
<x-daisy::ui.data-display.table
    :columns="$columns"
    :rows="$users"
>
    <x-slot:toolbar>
        <x-daisy::ui.inputs.input name="q" placeholder="Search users" />
    </x-slot:toolbar>

    <x-slot:actions>
        <x-daisy::ui.inputs.button tag="a" :href="route('users.create')" icon-name="bi-plus">
            New user
        </x-daisy::ui.inputs.button>
    </x-slot:actions>
</x-daisy::ui.data-display.table>
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

For Laravel resources, return the table keys directly in `toArray()` or map them before passing rows to the component. Keep HTML values opt-in with the column `html` flag.

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
