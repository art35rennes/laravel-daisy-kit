# Laravel Daisy Kit

Reusable Laravel package that ships Blade UI components, page templates, translations, and optional frontend assets built for **DaisyUI 5** and **Tailwind CSS 4**.

## Versioning

This package follows [Semantic Versioning 2.0.0](https://semver.org/lang/fr/).

- `MAJOR`: incompatible change on the public package API
- `MINOR`: backward-compatible feature
- `PATCH`: backward-compatible fix or maintenance change

The initial stable release baseline is `v1.0.0`.
See [CHANGELOG.md](CHANGELOG.md) for released versions, [UPGRADE.md](UPGRADE.md) for major-version migrations, and [CONTRIBUTING.md](CONTRIBUTING.md) for the project release rules.

## Requirements

- PHP `^8.3`
- Laravel `^13.0`
- Livewire `^4.3` when using `x-daisy::forms.builder`

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

If the host renders the Form Kit builder, make sure Livewire 4 is installed and its scripts/styles are present in the application layout. The viewer does not require Livewire; it is rendered by Blade and progressively enhanced by the package JavaScript runtime.

## What this package provides

- **Blade namespace** `daisy::` — use components such as `x-daisy::ui.inputs.button` or `x-daisy::layout.*`.
- **Templates** — reusable views under `daisy::templates.*` (also exposed as anonymous Blade components where applicable).
- **Translations** — `__('daisy::...')` namespace.
- **JavaScript** — a small bootstrap (`window.DaisyKit`) that initializes modules marked with `data-module`; Alpine.js-friendly patterns are used for simple interactions.
- **Optional heavy UI** — components like maps (Leaflet) rely on lazy-loaded chunks; publish built assets so those entry points resolve correctly.

### Triggerable toast notifications

Daisy Kit exposes reusable toast notifications through the package JavaScript runtime. Render a single configurable container when you want server-controlled placement:

```blade
<x-daisy::ui.feedback.toast triggerable vertical="top" horizontal="end" :limit="4" />
```

The runtime also creates that container on demand when none exists. Host code can trigger notifications with the global API or a DOM event:

```js
window.DaisyKit.notify({
    type: 'success',
    title: 'Saved',
    message: 'Changes persisted.',
    autoDismissMs: 4000,
    actions: [
        {
            label: 'Undo',
            name: 'undo',
            callback: ({ id }) => console.log(id),
        },
    ],
});

document.dispatchEvent(new CustomEvent('daisy:notify', {
    detail: {
        type: 'warning',
        title: 'Export queued',
        message: 'You can keep working.',
        actions: [{ label: 'Open', name: 'open' }],
    },
}));
```

Supported notification `type` values are `info`, `success`, `warning`, and `error`. `title` and `message` are rendered as text. Pass `html` only for host-trusted HTML; never pass unsanitized user input. `actions` accepts up to two buttons. Each action may provide a `callback`, or it dispatches `notify:action` with `{ id, action, notification, detail }`.

Notifications are manually dismissible by default, auto-dismiss after five seconds unless `autoDismiss: false` is passed, show the reusable alert-dismiss progress bar, pause while hovered or focused, and enforce the visible `limit`. Use popconfirm or a modal confirmation for critical destructive actions; toast actions are for reversible, low-risk follow-ups.

### Hierarchical tree selector

Tree View is a Laravel form control with a fixed node schema: `id`, `label`, optional `children`, `disabled`, `lazy`, and `expanded`. Selection state belongs to the component `value`, not to individual nodes. Multiple selection cascades through descendants, shows mixed parents, and submits selected leaves only.

```blade
<x-daisy::ui.advanced.tree-view
    id="permission-tree"
    label="Permissions"
    name="permissions"
    selection="multiple"
    :value="old('permissions', ['reports.view'])"
    :data="$permissionTree"
    :search="true"
    lazy-url="/api/permissions/children"
/>
```

Lazy endpoints return `{ "items": [...] }`. Remote search endpoints return at most 50 paths as `{ "paths": [["root", "branch", "match"]] }`. Labels and IDs are always inserted as text by the runtime.

Use `window.DaisyTreeView.get(root)` to access `getValue()`, `setValue(value)`, `expand(id)`, `collapse(id)`, `toggle(id)`, `reset()`, `reload(id?)`, and `destroy()`. The root emits `daisy:tree-change`, `daisy:tree-load`, and `daisy:tree-error`; every detail contains `value`, `nodeId`, and `source`.

### Blueprint workflow editor

Blueprint is a focused directed-workflow editor. Steps are accessible HTML cards and transitions are SVG paths. Dagre provides the hierarchical/tree layout, while the radial layout stays native to the component. Automatic layout runs only when positions are missing or when the user chooses **Arrange**. The host application owns persistence, authorization, business data, and workflow execution.

    <x-daisy::ui.advanced.blueprint
        name="publishing_workflow"
        mode="edit"
        direction="LR"
        layout="hierarchical"
        transition-shape="curve"
        transition-color="primary"
        node-color="neutral"
        height="560px"
        :value="$workflow"
        :node-categories="$stepCategories"
        :transition-categories="$transitionCategories"
    >
        <x-slot:inspector>
            <div class="grid gap-4" data-module="publishing-workflow-inspector">
                <x-daisy::ui.partials.form-field
                    id="workflow-label"
                    label="Name"
                    hint="This content belongs to the host application."
                    hint-mode="icon"
                >
                    <x-daisy::ui.inputs.input id="workflow-label" data-workflow-field="label" />
                </x-daisy::ui.partials.form-field>

                <div class="flex justify-end gap-2">
                    <button type="button" class="btn" data-workflow-action="cancel">Cancel</button>
                    <button type="button" class="btn btn-primary" data-workflow-action="commit">Save</button>
                </div>
            </div>
        </x-slot:inspector>
    </x-daisy::ui.advanced.blueprint>

The public value contains **version**, **nodes**, **transitions**, and **viewport**. Every transition is directed; a return path is represented by another transition with its source and target reversed. Parallel transitions and self-loops are supported. The optional **data** object on steps and transitions is the stable extension boundary for host-owned, JSON-serializable attributes. Unknown data is preserved by edits, history, and form synchronization.

Categories are presentation-only. Node categories accept `value`, `label`, and `color`; transition categories also accept `shape`. Blueprint never applies defaults to `data` and does not interpret form schemas, sections, tabs, help, CodeMirror, or WYSIWYG configuration.

The `inspector` slot is rendered inside Blueprint's modal shell. The host may compose any Daisy Kit components in it, including tabs, `form-field`, code editors, and WYSIWYG controls. A host module hydrates these controls when the modal opens and returns the complete `{ label, description, category, data }` value:

```js
export default function initPublishingInspector(root) {
    const blueprint = root.closest('[data-blueprint]');
    const label = root.querySelector('[data-workflow-field="label"]');
    let session = null;

    blueprint.addEventListener('daisy:blueprint:inspector-open', (event) => {
        session = event.detail;
        label.value = session.value.label;
    });

    label.addEventListener('input', () => {
        session?.setDraft({
            ...session.value,
            label: label.value,
        });
    });

    root.querySelector('[data-workflow-action="commit"]').addEventListener('click', () => {
        session?.commit({
            ...session.value,
            label: label.value,
        });
    });

    root.querySelector('[data-workflow-action="cancel"]').addEventListener('click', () => {
        session?.cancel('integrator');
    });
}
```

Calling `setDraft(value)` lets Blueprint detect unsaved changes and protect closing, Escape, backdrop clicks, and selection changes. `commit(value)` validates the generic object, updates history and the synchronized workflow, then closes the modal. When `data` is omitted from a draft or commit, Blueprint preserves the entity's existing opaque data; when it is provided, it replaces that data entirely. `cancel()` requests cancellation and displays the discard confirmation when necessary. Business validation remains the host's responsibility and should run before `commit`.

Create a transition by clicking a dot on the source step, then a dot on the target step. `transition-shape` accepts **straight**, **curve**, **s**, or **orthogonal**. `layout` accepts **hierarchical**, **tree** (an explicit Dagre alias), or **radial**; `direction="LR|TB"` applies to hierarchical/tree layouts. `transition-color` and `node-color` accept DaisyUI semantic colors. A node category may override the default card color, for example `['value' => 'published', 'label' => 'Published', 'color' => 'success']`; a transition category may override both its presentation values, for example `['value' => 'return', 'label' => 'Return', 'shape' => 'curve', 'color' => 'warning']`. These presentation values do not change the persisted workflow.

Blueprint keeps the same graphical canvas at every viewport width. On touch screens, one finger pans the workflow and a two-finger pinch zooms around the gesture center. The toolbar stacks on narrow screens and the inspector modal occupies the full mobile viewport; no alternate mobile-only workflow representation is generated.

The initialized root exposes **root.__daisyBlueprint** with: **getValue**, **setValue**, **addNode**, **updateNode**, **removeNode**, **addTransition**, **updateTransition**, **removeTransition**, **arrange**, **fit**, **undo**, **redo**, **openInspector**, **setInspectorDraft**, **commitInspector**, **cancelInspector**, and **destroy**. Integration events are **daisy:blueprint:init**, **daisy:blueprint:change**, **daisy:blueprint:select**, **daisy:blueprint:inspector-open**, **daisy:blueprint:inspector-commit**, **daisy:blueprint:inspector-cancel**, and **daisy:blueprint:error**. When **name** is provided, the synchronized hidden field also emits native **input** and **change** events for forms and Livewire.

## Security Headers And CSP

Daisy Kit is designed to work by default with strict host Content Security Policy rules such as `script-src 'self'`, `style-src 'self'`, `connect-src 'self'`, `form-action 'self'`, `object-src 'none'`, and `frame-ancestors 'none'`.

- Package interactions use published JavaScript modules loaded from the host origin through `data-module`; components must not rely on inline `onclick` handlers, inline style attributes, executable inline `<script>` blocks, `eval()`, or `new Function`.
- Dynamic visual values use package CSS classes by default. Public package views and modules must not emit or consume `data-daisy-css-*` runtime style shims.
- Static asset tags generated outside Laravel Vite accept a CSP nonce through `config('daisy-kit.csp_nonce')`. The value may be a string or a per-request callable.
- CSP nonces authorize nonce-bearing `<script>` and `<style>` tags. They do not authorize `style=""` attributes, JavaScript writes such as `element.style.width = ...`, inline event attributes such as `onclick=""`, or string evaluation. If a component needs dynamic styling under strict CSP, use package classes, semantic HTML attributes, or stylesheet-backed rules instead of inline styles.
- JSON-LD payloads generated by package components receive the same nonce support. `<script type="application/json">` payloads remain non-executable component data.
- Custom theme CSS is not emitted by default. If a host explicitly enables `daisy-kit.themes.inline_custom_css`, the generated `<style>` tag is nonceable through `daisy-kit.csp_nonce`.
- Daisy Kit does not require `unsafe-inline` or `unsafe-eval` globally. Any future module that needs runtime compilation must be loaded explicitly and documented.
- `x-daisy::layout.app` links Instrument Sans from Bunny Fonts by default. Hosts using that default font must allow `https://fonts.bunny.net` in `font-src`; pass `font-url=""` to avoid the external font link.
- `x-daisy::ui.media.leaflet` does not load external map tiles by default. To render a tiled map, pass a same-origin `tile-url`, define `basemaps`, or explicitly opt into a provider / external `tile-url` and open the matching host CSP directives, usually `img-src` and sometimes `connect-src`. Custom WMS/XYZ overlays and remote GeoJSON overlay URLs follow the same host policy.
- Leaflet drawing uses the MIT-licensed Terra Draw runtime when the `draw` prop is enabled. Hosts using drawing and measurement must include the package assets and allow the map/tile endpoints they configure; persisted geometry is emitted as GeoJSON through the optional hidden input configured with `name`. The MVP supports points, lines, polygons, and rectangles; rectangle editing is limited to selection and dragging until vertex-level rectangle editing is supported cleanly.

### Leaflet SIG API

`x-daisy::ui.media.leaflet` keeps Leaflet as the display engine and exposes a lightweight GIS surface without Geoman:

- `basemaps` accepts XYZ and WMS backgrounds with one active base layer; `overlays` accepts GeoJSON, XYZ, and WMS layers with `visible`, `editable`, `style`, and provider `options`.
- `layerControl` exposes a dedicated Daisy `Couches` menu for basemaps and overlays. `layerControl.mode` can be `multiple` for stackable overlays or `single` for one active overlay at a time. Overlays with `control: false`, `controllable: false`, `locked: true`, or an id/label listed in `layerControl.lockedOverlays` are forced visible and hidden from the user toggles. Set `layerControl.native` to `true` only when you explicitly want Leaflet's native layer control instead of the Daisy menu.
- `draw` enables the Terra Draw toolbar. `draw.groupedToolbar` groups tools into submenus, `draw.actionBadge` shows or customizes the active-tool badge, `draw.selectionDetails` shows or hides the `Détail de la sélection` action, `draw.styles` defines default point/line/polygon/rectangle/marker styles, and clicking the active tool again returns to selection.
- `objectTypes` adds business tools for point, line, or polygon objects. Toolbar icons can be supplied with `icon`, sanitized `iconSvg`, or sanitized `iconHtml`; point markers can render custom map icons with safe `markerUrl` values or sanitized `markerSvg` plus `markerWidth` and `markerHeight`. Treat custom icon markup as integrator-controlled content, not end-user input.
- `objectTypes[].style` overrides drawing style per business object. Public aliases include `color`, `width`, `dashArray`, `strokeColor`, `strokeWidth`, `fillColor`, `fillOpacity`, `markerUrl`, and `markerSvg`; the package maps them to Terra Draw adapter options.
- `drawLayers` associates newly drawn features with logical drawing layers, independently from display overlays. Use `mode: "fixed"` with `current`/`layerId` to force one layer, `mode: "select"` to let the user pick the current drawing layer in the toolbar, or `mode: "none"` to explicitly persist no layer. Set `allowNone: true` in select mode to offer a no-layer choice. Exported features receive `properties.drawLayerId` and `properties.drawLayerLabel` by default; `property` and `labelProperty` can rename those keys.
- `measure` uses Turf from GeoJSON coordinates, not screen pixels. `measure.maxLabels` limits labels rendered on dense maps and `measure.maxLabelOffsetPx` hides labels that would be placed too far from their feature; every measurement remains available in emitted event payloads.
- `geolocation` enables browser Geolocation API support. Use `geolocation: true` for an on-demand button, `geolocation.auto` to locate once on initialization, and `geolocation.watch` for realtime tracking until stopped. Options include `setView`, `zoom`, `maximumAge`, `timeout`, `enableHighAccuracy`, `showAccuracy`, and `button`. The browser permission prompt is only triggered by an explicit locate/watch request; coordinates stay client-side unless the host consumes and persists emitted events.
- Geolocation success payloads are stable integration objects with `source`, `method` (`manual`, `auto`, or `watch`), `watch`, `watching`, `lat`, `lng`, `accuracy`, `altitude`, `altitudeAccuracy`, `heading`, `speed`, `timestamp`, normalized `coords`, the request `options`, the raw browser `position`, and a GeoJSON `feature` point carrying the same metadata in `properties`.
- `name` creates the hidden GeoJSON field; `value` seeds editable features. Read-only overlays stay outside the Terra Draw store unless `editable: true`.

The root element exposes `root.daisyLeaflet` after initialization. Public methods include `map`, `context`, `exportGeoJSON()`, `setMode(mode)`, `getDrawLayer()`, `setDrawLayer(layerId|null)`, `getSelectionDetails()`, `showSelectionDetails()`, `clearSelection()`, `deleteSelected()`, `getGeolocation()`, `locate()`, `startGeolocation()`, `stopGeolocation()`, `isGeolocationWatching()`, `undo()`, `redo()`, and `destroy()`. Drawing and geolocation methods are safe no-ops when the corresponding feature is disabled.

Public events are dispatched from the root `[data-module="leaflet"]` element:

- `daisy:leaflet:init` with `{ map, config, context, exportGeoJSON, setMode, getDrawLayer, setDrawLayer, getSelectionDetails, showSelectionDetails, clearSelection, deleteSelected, getGeolocation, locate, startGeolocation, stopGeolocation, isGeolocationWatching, undo, redo, destroy }`
- `daisy:leaflet:change` with `{ value, measurements, draw }`
- `daisy:leaflet:measure` with `{ measurements, latest, draw }`
- `daisy:leaflet:geolocation:request` with `{ method, watch, options, map }`
- `daisy:leaflet:geolocation:success` with `{ source, method, watch, watching, lat, lng, accuracy, altitude, altitudeAccuracy, heading, speed, timestamp, coords, options, feature, position, map }`
- `daisy:leaflet:geolocation:error` with `{ error, method, watch, options, map }`
- `daisy:leaflet:geolocation:stop` with `{ map }`
- `daisy:leaflet:draw-layer-change` with `{ layer, layerId, draw, map }`
- `daisy:leaflet:selection-details` with `{ count, featureIds, features, primaryFeature, primaryFeatureId, exportGeoJSON, draw, map }`
- `daisy:leaflet:object-created` with `{ feature, featureId, objectType, drawLayer, drawLayerId, exportGeoJSON }`
- `daisy:leaflet:draw-finish` with `{ feature, featureId, objectType, drawLayer, drawLayerId, draw }`
- `daisy:leaflet:zone-select` with `{ type, featureIds, features, map, draw }`
- `daisy:leaflet:layer-toggle` with `{ name, type, layer, activeBasemap, activeOverlays, lockedOverlays }`

| Component family | Default CSP compatibility | Host responsibility |
| --- | --- | --- |
| Package assets | `script-src 'self'` and `style-src 'self'`; static tags can receive `daisy-kit.csp_nonce`. | Configure `daisy-kit.csp_nonce` when the host policy requires nonces on generated tags. |
| TanStack table | Published JS is loaded from `self`; table data/config is transported as non-executable JSON attributes; default table rendering does not need inline scripts. | Keep custom Blade cell/detail views free of inline event handlers and inline styles. Under strict `style-src`, do not copy TanStack sizing examples that apply widths through `style`; use the package CSP-safe sizing implementation. |
| Forms viewer and builder | No `unsafe-eval`; schema validation is structural and package JS is loaded from `self`. | Provide Livewire assets for the builder when used; keep host validation/business rules server-side. |
| Charts | Package renderer uses published JS and JSON data payloads. | Open `connect-src` only for host-provided remote data endpoints. |
| Scroll status | Uses a native `<progress>` element; package JS only updates its `value`. | For custom colors, heights, or offsets beyond package defaults, provide a host CSS class instead of relying on runtime inline styles. |
| Floating overlays, media zoom, and builder drag helpers | Published JS uses classes, attributes, and Web Animations API frames instead of inline style attributes. | Keep package assets enabled; custom host positioning or animation should be implemented in host CSS classes. |
| Leaflet maps | Same-origin by default, no external tiles unless configured; drawing is lazy-loaded only when `draw` is enabled. | Open `img-src`/`connect-src` for explicit external tile providers, WMS/XYZ overlays, remote GeoJSON overlays, and basemaps. |
| Custom themes | No inline custom theme CSS by default. | Prefer build-time themes; if `themes.inline_custom_css` is enabled, provide `daisy-kit.csp_nonce`. |
| Editors | CodeMirror/Trix chunks load from `self`; editor options are JSON payloads. | Include the package assets; avoid host-side inline editor boot code. |

Third-party runtime note: built optional chunks may include vendor code paths that manipulate runtime styles internally, notably Trix and GridStack. Keep those surfaces lazy-loaded and treat WYSIWYG/editable-grid usage as an explicit host decision when enforcing `style-src` without `unsafe-inline`. The package chart preset uses ECharts `richText` tooltips by default so normal chart rendering stays canvas-based.

Chart components expose ECharts reporting features through JSON props instead of host-side scripts. Use `drilldownUrl` and enriched points for filtered navigation, `orientation="horizontal"` for ranking bars, `markers` for targets or notable points, and `zoom` only for longer series:

```blade
<x-daisy::charts.bar
    title="Charge par agent"
    orientation="horizontal"
    drilldown-url="/interventions"
    :drilldown-params="['section' => 'terrain', 'chart' => 'agent-load']"
    :categories="['Thomas Bernard', 'Julie Dupont']"
    :series="[
        ['name' => 'Interventions', 'data' => [
            ['value' => 15, 'drilldown' => ['agent' => 'thomas-bernard']],
            ['value' => 11, 'drilldown' => ['agent' => 'julie-dupont']],
        ]],
    ]"
/>

<x-daisy::charts.line
    title="Volume"
    :zoom="true"
    zoom-mode="slider"
    :markers="[
        ['type' => 'line', 'value' => 120, 'name' => 'Objectif', 'label' => 'Objectif 120'],
        ['type' => 'point', 'coord' => ['16/06', 128], 'name' => 'Dernier total'],
    ]"
    :categories="['09/06', '16/06']"
    :series="[['name' => 'Total', 'data' => [110, 128]]]"
/>
```

Example for an ECA3-style host CSP using Bunny Fonts and OpenStreetMap tiles:

```http
script-src 'self' 'nonce-{request-nonce}';
style-src 'self' 'nonce-{request-nonce}';
img-src 'self' data: https://*.tile.openstreetmap.org;
font-src 'self' data: https://fonts.bunny.net;
connect-src 'self';
form-action 'self';
```

When a host chooses another tile provider, CDN, upload endpoint, websocket, analytics endpoint, media origin, or enables inline custom theme CSS, the host application owns the corresponding CSP extension. The package default should remain functional without those external origins or inline-policy exceptions.

## Package scope

This repository contains only package concerns:

- `src/`
- `config/daisy-kit.php`
- `resources/views`, `resources/lang`, `resources/js`, `resources/css`
- `dist/vendor/art35rennes/laravel-daisy-kit/` — prebuilt assets published to host apps via `daisy-assets`
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

`npm run build` writes the distributable Vite manifest and assets to `dist/vendor/art35rennes/laravel-daisy-kit/`. That directory is shipped with the Composer package so host apps can publish it with `daisy-assets` without rebuilding frontend tooling locally.

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

### DaisyUI 5.6 components

The package exposes the DaisyUI 5.6 additions through stable Blade aliases:

```blade
<x-daisy::ui.inputs.otp name="code" :length="6" size="lg" color="primary" required />

<x-daisy::ui.advanced.aura variant="rainbow" size="lg">
    <x-daisy::ui.layout.card>Highlighted content</x-daisy::ui.layout.card>
</x-daisy::ui.advanced.aura>

<x-daisy::ui.navigation.megamenu mode="wide" size="md">
    <button popovertarget="products-menu">Products</button>
    <div id="products-menu" popover>
        <x-daisy::ui.navigation.menu>...</x-daisy::ui.navigation.menu>
    </div>
</x-daisy::ui.navigation.megamenu>
```

OTP supports `numeric`, `joined`, semantic `color`, and `xs` through `xl` sizes. Megamenu supports `wide`, `full`, and `vertical` modes. Existing components also expose `vertical` on range inputs, `alignment="start|center|end"` on tooltips, and `method="dialog|popover"` on modals. Dialog remains the modal default. Cards accept opt-in `selectable` and `checked` props.

The two-factor template now submits a single native `code` input. The older multi-input `data-module="otp-code"` runtime remains available for existing host markup but is deprecated.

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

    <x-daisy::ui.partials.form-field name="role" label="Role">
        <x-daisy::ui.inputs.select
            name="role"
            :value="$user->role"
            :options="[
                ['value' => 'user', 'label' => 'User'],
                ['value' => 'admin', 'label' => 'Administrator'],
            ]"
        />
    </x-daisy::ui.partials.form-field>

    <x-daisy::ui.inputs.button icon-name="bi-check" loading>
        Save
    </x-daisy::ui.inputs.button>
</x-daisy::layout.app>
```

Useful defaults:

- `layout.app` accepts `htmlClass`, `bodyClass`, `fontUrl`, and `loadDefaultFont`.
- `layout.navbar-sidebar-layout` and `layout.sidebar-layout` accept `showThemeController`, `themes`, and `themeLabel`.
- `form-field` is the recommended wrapper for every label + input/select pair. It contains labels and controls in constrained grids and defaults to truncated labels; use `label-wrap="wrap"` when multiline labels are preferred.
- `input` and `select` accept `name`, `id`, `value`, `bindOld`, `error`, and accessibility attributes.
- Enhanced `select` lists show five options by default and scroll internally; use `listSize` (1–20) to change the visible option count.
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

Constrained filter grids should compose controls through `form-field` instead of adding host CSS for `.label`, `.input`, or `.select`:

```blade
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
    <x-daisy::ui.partials.form-field
        name="filter[query]"
        id="dashboard-filter-query"
        label="Search label with a deliberately long text"
    >
        <x-daisy::ui.inputs.input
            id="dashboard-filter-query"
            name="filter[query]"
        />
    </x-daisy::ui.partials.form-field>

    <x-daisy::ui.partials.form-field
        name="filter[intervention_type]"
        id="dashboard-filter-intervention-type"
        label="Type d'enquete"
    >
        <x-daisy::ui.inputs.select
            id="dashboard-filter-intervention-type"
            name="filter[intervention_type]"
        >
            <option value="long">Long select option label that remains contained</option>
        </x-daisy::ui.inputs.select>
    </x-daisy::ui.partials.form-field>

    <x-daisy::ui.partials.form-field
        name="filter[started_on]"
        id="dashboard-filter-started-on"
        label="Start date"
    >
        <x-daisy::ui.inputs.input
            type="date"
            id="dashboard-filter-started-on"
            name="filter[started_on]"
        />
    </x-daisy::ui.partials.form-field>
</div>
```

## Browser autocomplete

Daisy Kit treats the HTML `autocomplete` attribute as markup owned by the host application. The package renders semantic hints when it owns the field meaning, and otherwise passes through the host-provided attribute without adding a global policy.

Package-owned identity templates use explicit semantic values where the context is known, such as `email`, `username`, `current-password`, `new-password`, `one-time-code`, `name`, `tel`, `address-level2`, and `url`.

For business, CRUD, administrative, or sensitive workflows, choose the policy in the host application:

```blade
{{-- Disable browser autocomplete for a whole form or page-level template. --}}
<x-daisy::templates.form.form-simple autocomplete="off">
    {{-- Fields can still opt back into a semantic hint when appropriate. --}}
    <x-slot:elements>
        <x-daisy::ui.partials.form-field name="organization" label="Organization">
            <x-daisy::ui.inputs.input name="organization" autocomplete="organization" />
        </x-daisy::ui.partials.form-field>

        <x-daisy::ui.partials.form-field name="internal_reference" label="Internal reference">
            <x-daisy::ui.inputs.input name="internal_reference" autocomplete="off" />
        </x-daisy::ui.partials.form-field>
    </x-slot:elements>
</x-daisy::templates.form.form-simple>
```

Form Kit forwards compatible field attributes through `attrs.autocomplete`:

```blade
<x-daisy::forms.viewer
    autocomplete="off"
    :schema="[
        'version' => '1.0',
        'id' => 'contract',
        'fields' => [
            [
                'id' => 'contact_email',
                'type' => 'email',
                'name' => 'contact_email',
                'label' => 'Contact email',
                'attrs' => ['autocomplete' => 'email'],
            ],
        ],
    ]"
/>
```

If a host wants a broader convention, apply it in the host layout, wrapper, or published Daisy Kit override. Daisy Kit does not provide a package-wide switch that forces `autocomplete="off"`, because it cannot know whether a field belongs to personal identity, administration, business data, or another context. Also note that `autocomplete="off"` is a browser hint, not a security control; browsers and password managers may ignore it.

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

For Laravel-style non-`GET`/`POST` methods, pass `method="PUT"`, `PATCH`, or `DELETE` as usual. The viewer renders valid HTML with `method="POST"` and the hidden `_method` field, while the JavaScript runtime keeps the original verb through `data-form-method` for `fetch` submissions.

When the schema contains a `file` field, the viewer renders `enctype="multipart/form-data"`. In `fetch` mode, the runtime sends a `FormData` payload so uploaded files keep native browser semantics and preserves Laravel hidden `_token` / `_method` controls.
Laravel-style `422` JSON validation responses shaped as `{ errors: { field: [...] } }` are mapped back into the viewer errors and emit `daisy-form:invalid`.

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

The global registry is useful when the host application initializes behavior outside the viewer event lifecycle:

```js
const runtime = window.DaisyFormViewer.get('quote-viewer');
const runtimeFromElement = window.DaisyFormViewer.getByElement(document.getElementById('quote-viewer'));
const activeRuntimes = window.DaisyFormViewer.all();

runtime?.destroy();
```

`get(id)` and `getByElement(element)` return `null` when the viewer has been disconnected. `all()` prunes disconnected viewers before returning the active runtimes, and `destroy()` unregisters the runtime.

Runtime methods include:

- `getSchema()`, `getField(key)`, `getVisibleFields()`
- `getValues({ visible: true })`, `serialize()`, `getFormData({ visible: true })`, `getValue(key)`, `getInput(key)`, `getInputs(key)`, `setValue(key, value)`, `setValues(values)`, `reset(values)`
- `validate()`, `isValid()`, `getErrors()`, `setErrors(errors)`, `clearErrors()`
- `submit()`, `getSubmitMode()`, `getValidateOn()`, `isReadonly()`
- `getStep()`, `setStep(index)`, `nextStep()`, `previousStep()` for multi-step schemas
- `on(event, listener)`, `off(event, listener)`, `destroy()`

`getInput(key)` returns the first rendered control for the field. `getInputs(key)` returns every rendered control, which is useful for radio groups and future composite controls that expose multiple focusable elements for one schema field.

Viewer events bubble from the form root and include `daisy-form:ready`, `daisy-form:change`, `daisy-form:invalid`, `daisy-form:step-change`, `daisy-form:submit`, and `daisy-form:destroy`. Each event detail includes the viewer `id` and the `runtime` instance so hosts can integrate without querying global state. In `fetch` mode, successful `daisy-form:submit` details also include the `Response` object.

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

## DOCX file preview zoom

`x-daisy::ui.data-display.file-preview` can fit rendered DOCX pages to the available width while keeping a vertically scrollable viewport. The scale is calculated from the page width, so genuinely overflowing tables, images, or other incompressible content can still use horizontal scrolling.

```blade
<x-daisy::ui.data-display.file-preview
    :url="$documentUrl"
    name="contract.docx"
    preview-mode="inline"
    docx-view="fit-width"
    :docx-zoom="100"
    :docx-zoom-controls="true"
/>
```

- `docxView="page|fit-width"` selects the initial mode and defaults to `page`.
- `docxZoom` configures the manual page zoom from 10% to 100%.
- `docxZoomControls` optionally exposes zoom out, Fit, 50%, 75%, 100%, and zoom in controls. The incremental buttons use a 10-point step and stop at 10% and 100%.
- `docxPreview=false` disables DOCX rendering and its zoom behavior entirely.

The Daisy Kit toolbar and zoom implementation do not emit inline handlers or styles. However, `docx-preview` itself generates inline document styles. Applications enforcing `style-src-attr 'none'` must disable DOCX preview or convert the document server-side to a CSP-compatible PDF or sanitized HTML representation.

## Table component

The package exposes a progressive table component aligned with Blade, DaisyUI, and TanStack Table:

- `x-daisy::ui.data-display.table`

The Blade view renders semantic HTML and DaisyUI controls. The package JavaScript enhances the component on `[data-daisy-table="1"]` and uses `@tanstack/table-core` as the headless state engine for sorting, filtering, pagination, expansion, row selection, column sizing, and column visibility.

TanStack Table is intentionally headless: it owns table state and row models, but it does not guarantee the CSP properties of the DOM you render around it. The TanStack column sizing documentation commonly demonstrates `style={{ width: ... }}` and CSS-variable approaches because those are convenient in framework examples. Daisy Kit must not copy that markup directly when supporting a strict host CSP. The package should keep delegating behavior to TanStack while applying visual state through CSP-safe markup and package CSS.

### Table CSP contract

The table runtime is designed for host policies that avoid `unsafe-inline` and `unsafe-eval`:

- `@tanstack/table-core` and related table modules are bundled into published package assets and loaded from the host origin.
- The Blade component serializes configuration into HTML data attributes, not executable inline scripts.
- Public table events use `addEventListener` / `dispatchEvent`; package markup must not emit inline handlers such as `onclick`.
- A CSP nonce is useful for package-generated `<script>` or `<style>` tags, but it does not authorize `style=""` attributes or `element.style.*` writes. Column sizing and resizing must therefore avoid inline width styles when strict `style-src` is required.
- `rowDetailView`, `blade`, and `trusted-html` are explicit trusted HTML extension points. Host partials used there must escape user content and avoid inline handlers, inline styles, and scripts if the page must remain strict-CSP compatible. The `actions` renderer is structured and CSP-safe.
- Daisy Kit does not currently claim Trusted Types compatibility for the table runtime because trusted HTML extension points are inserted as HTML. Hosts enforcing `require-trusted-types-for 'script'` need a host-level Trusted Types policy or a stricter renderer contract.

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
- `persistStateFields`
- `rowKey`
- `subRowsKey`
- `subRowSelection="independent|cascade|master-only"`
- `linkPolicy`
- `globalFilterKey`
- `filters`
- `initialState`
- `pageSizeOptions`
- `search`
- `columnVisibility`
- `caption`
- `size`
- `zebra`
- `hover`: highlights the hovered row, and the row containing keyboard focus.
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
        'cell' => ['renderer' => 'text'],
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
        ['key' => 'status', 'label' => 'Status', 'cell' => ['renderer' => 'trusted-html']],
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
            'cell' => ['renderer' => 'trusted-html'],
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

### Row selection and bulk actions

Enable row selection with `selection="multiple"` and a stable `row-key`. The key must exist on every selectable row and is the only value Daisy Kit stores or emits for selection.

```blade
<x-daisy::ui.data-display.table
    selection="multiple"
    row-key="id"
    :columns="$columns"
    :rows="$users"
>
    <x-slot:bulkActions>
        <button type="button" class="btn btn-sm btn-primary" data-table-bulk-action="export">
            Export selected
        </button>
    </x-slot:bulkActions>
</x-daisy::ui.data-display.table>
```

Selection works in client and server mode:

- selecting the page toggles only currently visible rows;
- selecting all filtered results switches to a compact filtered-selection mode;
- unchecking a row after selecting all filtered results adds that row to `excludedIds`;
- the feedback says all filtered results are selected only while there are no manual exclusions;
- changing filters or search resets selection because the result set has changed;
- changing page, page size, sorting, or refreshing server data keeps selection.

When `sub-rows-key` is configured, `sub-row-selection` controls how master and sub rows participate:

- `independent` (default): every row is selectable independently, preserving the existing behavior;
- `cascade`: selecting a master selects all descendant leaves, while the master exposes checked, unchecked, or mixed state. Selection events contain only leaf IDs and filtered server-side selection is disabled because unloaded descendants cannot be normalized safely;
- `master-only`: only top-level rows are selectable; sub rows remain visible as context without a selection control.

```blade
<x-daisy::ui.data-display.table
    selection="multiple"
    row-key="id"
    sub-rows-key="children"
    sub-row-selection="cascade"
    :columns="$columns"
    :rows="$groups"
/>
```

The runtime emits `daisy:table-selection-changed` with:

```json
{
  "selectedIds": ["1", "2"],
  "excludedIds": [],
  "allFilteredSelected": false,
  "selectionScope": "page",
  "subRowSelection": "independent",
  "selectedCount": 2,
  "visibleSelectedCount": 2,
  "tableState": {
    "sorting": [],
    "pagination": { "pageIndex": 0, "pageSize": 25 },
    "globalFilter": "",
    "columnFilters": []
  },
  "actionPayload": {
    "mode": "ids",
    "ids": ["1", "2"]
  }
}
```

When all filtered results are selected, bulk action buttons receive an action payload shaped for the host backend:

```json
{
  "mode": "filtered",
  "filters": [{ "id": "status", "type": "select", "value": "active" }],
  "sorting": [{ "id": "name", "desc": false }],
  "globalFilter": "jane",
  "excludedIds": ["42"]
}
```

Daisy Kit never executes the business action itself. Buttons inside `bulkActions` should use `data-table-bulk-action`; the table emits `daisy:table-bulk-action` with the action name and payload so the host app can call Livewire, submit a form, or send a request.

The built-in selection bar stays visible when selection is enabled. Controls that do not apply to the current state are disabled instead of removed, so users keep a stable action surface. Hosts that need a fully custom bar can listen to `daisy:table-selection-changed` or use the public runtime API:

```js
const table = window.DaisyTable.table('users-table');

table.selection(); // Full UI detail: counts, ids, exclusions, table state, actionPayload.
table.selectionPayload(); // Business payload only: { mode: 'ids' } or { mode: 'filtered' }.
table.selectAllFiltered(); // Switches to filtered-selection mode.
table.clearSelection(); // Clears the current selection.
```

Custom bars should use these APIs instead of reading internal table markup. The root element also exposes `data-table-selection-*` counters for lightweight CSS states.

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

For Laravel resources, return the table keys directly in `toArray()` or map them before passing rows to the component. Keep HTML values opt-in with `cell.renderer = trusted-html`; never pass unsanitized user data through a trusted renderer.

### Custom table cells

Custom cells are configured on columns, so the same UX works with local rows, server rows, or future transport adapters. Use `cell.renderer` for the explicit contract, or `view` as the Blade shorthand:

```php
$columns = [
    ['key' => 'causer', 'label' => 'Causer'],
    [
        'key' => 'actions',
        'label' => 'Actions',
        'type' => 'actions',
        'cell' => [
            'renderer' => 'blade',
            'view' => 'support.audit._actions-cell',
        ],
    ],
    ['key' => 'profile', 'label' => 'Profile', 'type' => 'resource-link'],
    [
        'key' => 'mobile',
        'label' => 'Open in app',
        'type' => 'resource-link',
        'cell' => [
            'renderer' => 'link',
            'allowedSchemes' => ['myapp'],
        ],
    ],
];
```

```php
use Art35rennes\DaisyKit\Support\DaisyTableRows;

$rows = DaisyTableRows::for($audits, $columns)
    ->map(fn ($audit) => [
        'id' => $audit->id,
        'causer' => $audit->causer?->name,
        'actions' => $audit,
        'profile' => [
            'label' => 'Open',
            'href' => route('audits.show', $audit),
            'target' => '_blank',
        ],
    ])
    ->renderCells();
```

Blade cell views receive `item`, `row`, `value`, `column`, and `table`. Supported renderers are `text`, `trusted-html`, `blade`, `link`, and `actions`. Only `blade` and `trusted-html` cross the trusted HTML boundary.

The `actions` renderer accepts one descriptor or a list of descriptors. It does not accept HTML:

```php
[
    'id' => $user->id,
    'actions' => [
        'action' => 'remove',
        'label' => 'Remove',
        'variant' => 'error',
        'disabled' => false,
        'ariaLabel' => 'Remove this user',
    ],
]
```

Listen for `daisy:table-row-action`; its detail contains `action`, `rowId`, `row`, `column`, and the TanStack `table` instance. Variants are allowlisted and descriptor strings are escaped.

`link` and `resource-link` escape labels and validate hrefs in both the initial Blade render and JS refreshes. Relative URLs plus `http`, `https`, `mailto`, and `tel` are allowed by default. Deeplink schemes are opt-in with either a table policy or a column policy:

```blade
<x-daisy::ui.data-display.table
    :link-policy="['allowedSchemes' => ['myapp', 'intent']]"
    :columns="$columns"
    :rows="$rows"
/>
```

Column `cell.allowedSchemes` extends the table policy. `javascript:`, `data:`, `vbscript:`, and hrefs containing control characters are always blocked, even if a host configuration tries to allow them. Use `target => '_blank'` when needed; Daisy Kit adds `rel="noopener noreferrer"` for web URLs and deeplinks.

Date filters are supported with `type => 'date'` and `type => 'date-range'`. In the default JSON contract, date ranges are sent as `{ "from": "YYYY-MM-DD", "to": "YYYY-MM-DD" }`. In Spatie mode, date ranges use `filter[key_from]` and `filter[key_to]`, configurable with `filterKeyFrom` and `filterKeyTo`.

The runtime emits `daisy:table-rendered` after every stable render with `rows`, `rowCount`, `pageCount`, `state`, `meta`, and the TanStack `table` instance. Column order, column pinning, column sizing, expanded rows, and row selection are part of the normalized state and can be updated through `window.DaisyTable.table(id)`.

### Client-side data updates

Client tables can receive data after the initial Blade render without host code touching the table body. Give the table an `id`, use `mode="client"` with a stable `row-key`, then use the public runtime API:

```js
const table = window.DaisyTable.table('scope-users');

if (!table) {
    throw new Error('The table root is missing.');
}

await table.setLoading(true);
await table.setRows(users);

await table.upsertRows([user]);
await table.removeRows([userId]);
```

`setRows(rows)` replaces the client snapshot. It validates recursively that every row and sub-row has a non-empty, globally unique `rowKey`. `upsertRows(rows)` replaces or appends top-level rows by `rowKey`; `removeRows(ids)` removes top-level rows by key. The table keeps TanStack sorting, filters, pagination, column state, expansion, and selection coherent; deleted identifiers are removed from selection and expansion, and an out-of-range page is clamped automatically. Nested rows remain supported by `setRows`; incremental operations address only top-level rows.

The public facade exposes `setLoading`, `setRows`, `upsertRows`, `removeRows`, `refresh`, `getRows`, `getState`, `getTanStackTable`, and `snapshot`. Snapshots are detached from internal runtime state; `window.DaisyTable.table(id)` returns `null` when no table exists.

URL persistence uses one JSON query parameter per table, for example `daisy-table[scope-users]`, and preserves all host query parameters. Provide `state-key` or a root `id`. Sorting, filters, search, pagination, and column state persist by default; opt into transient state explicitly with `:persist-state-fields="[..., 'expanded', 'rowSelection']"`. Only differences from the normalized `initialState` are serialized, including differences inside pagination and column-state objects. An untouched table adds no query parameter; partial URL state is merged over `initialState` when the table loads. Persisted JSON is limited to 4096 bytes.

The data API is intentionally limited to client tables with a `row-key`. Server tables remain backend-authoritative: call `table.refresh()` after a host mutation or external event. `setLoading(true)` displays the standard loading row while client data is being obtained. Every data mutation emits `daisy:table-data-changed` after the stable render with `operation`, `rowIds`, `rows`, `rowCount`, `pageCount`, `state`, and the TanStack `table` instance.

Use explicit column alignment to keep application tables scannable:

```php
[
    ['key' => 'name', 'label' => 'Name', 'align' => 'left'],
    ['key' => 'visual_signal', 'label' => 'Visual signal', 'align' => 'center'],
    ['key' => 'amount', 'label' => 'Amount', 'align' => 'right'],
]
```

Use `left` for text, names, descriptions, links, and long references; `center` for badges, visual signals, statuses, booleans, actions, short counters, and dates; and `right` for comparable measures, money, decimals, and percentages.

### Inline editing and row creation

Editing and creation share a single `editable` contract. Each operation can be `remote` (the default) or `local`. Remote updates send `{ rowId, column, value, dirty }`; remote creation sends `{ values }`. Successful responses should return `{ row }`; validation errors use Laravel's `422 { message, errors }` shape.

```blade
<x-daisy::ui.data-display.table
    row-key="id"
    :editable="[
        'enabled' => true,
        'mode' => 'row',
        'columns' => ['name', 'status', 'starts_at'],
        'update' => ['strategy' => 'remote', 'endpoint' => ['url' => '/projects/{rowId}', 'method' => 'PATCH']],
        'create' => [
            'enabled' => true,
            'strategy' => 'remote',
            'endpoint' => ['url' => '/projects', 'method' => 'POST'],
            'defaults' => ['status' => 'draft'],
        ],
    ]"
    :columns="[
        ['key' => 'name', 'label' => 'Name', 'editor' => ['type' => 'text', 'required' => true]],
        ['key' => 'status', 'label' => 'Status', 'editor' => ['type' => 'select', 'options' => $statuses]],
        ['key' => 'starts_at', 'label' => 'Start', 'editor' => ['type' => 'date']],
    ]"
/>
```

Built-in editors are `text`, `textarea`, `number`, `select`, `boolean`, and `date`. A custom `editor: ['type' => 'blade', 'view' => '…']` is trusted server-rendered markup; its controls must carry `data-table-editor-input`, optionally with `data-table-column-id` for another field. The runtime hydrates values and emits `daisy:table-editor-mounted` after mount.

Only one new-row draft may exist. It stays at the top of the TanStack row model, bypasses filters, sorting, selection, and counts, and is replaced by the canonical server row on success. Use `window.DaisyTable.table(id).startCreate()`, `.saveCreate()`, or `.cancelCreate()` for a custom trigger. The runtime emits `daisy:table-create-started`, `daisy:table-create-committed`, `daisy:table-create-failed`, and `daisy:table-create-cancelled`; local operations update table rows in memory and emit the same events.

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
- Responsive details rows, export buttons, and virtualization are out of scope for v1.
- The runtime keeps auto-bootstrap semantics, but the global API is now `window.DaisyTable`.
- `serverAdapter="spatie-query-builder"` is additive; the package JSON server contract remains the default.
