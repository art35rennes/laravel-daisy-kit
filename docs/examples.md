# Laravel Daisy Kit v5 corrective examples

These examples target the corrective development line beginning with
`v5.1.0-alpha.2`. Pin that VCS tag exactly while **validation propriétaire en attente**.
They intentionally do not provide compatibility with v5.0.0 or its historical alpha releases.

## One host Vite alias, explicit module imports

Laravel Daisy Kit is a Composer/VCS package, not an npm package. Configure the alias once in the
host application's `vite.config.js`, then import only the module pairs rendered on a page:

```js
import { defineConfig } from 'vite';
import { resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = fileURLToPath(new URL('.', import.meta.url));

export default defineConfig({
    resolve: {
        alias: {
            '@daisy-kit': resolve(__dirname, 'vendor/art35rennes/laravel-daisy-kit/dist'),
        },
    },
});
```

```js
import '@daisy-kit/forms-viewer.css';
import '@daisy-kit/forms-builder.css';
import '@daisy-kit/table.css';
import '@daisy-kit/tree.css';
import '@daisy-kit/blueprint.css';
import '@daisy-kit/file-preview.css';
import '@daisy-kit/map.css';

import { mountAll as mountFormsViewer } from '@daisy-kit/forms-viewer.js';
import { mountAll as mountFormsBuilder } from '@daisy-kit/forms-builder.js';
import { mountAll as mountTable } from '@daisy-kit/table.js';
import { mountAll as mountTree } from '@daisy-kit/tree.js';
import { mountAll as mountBlueprint } from '@daisy-kit/blueprint.js';
import { mountAll as mountFilePreview } from '@daisy-kit/file-preview.js';
import { mountAll as mountMap } from '@daisy-kit/map.js';

mountFormsViewer();
mountFormsBuilder();
mountTable();
mountTree();
mountBlueprint();
mountFilePreview();
mountMap();
```

Every entry also offers `mount(root)` and `unmount(root)`. Mounting is idempotent; call
`unmount` before removing an explicitly managed root. The host compiles Tailwind CSS and DaisyUI;
the package's CSS adds only module-specific layout and behavior.

## Forms Viewer

Use a schema for recursive sections or steps, field attributes/options/rules, Laravel values and
errors. JSONata conditions use only the non-executable descriptor shown below. `submitMode` may be
`event`, `html`, `fetch`, or `none`; use `event` when the host owns submission handling.

```blade
<x-daisy-kit::forms.viewer
    :schema="[
        'fields' => [[
            'type' => 'section',
            'label' => 'Profile',
            'fields' => [
                ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'rules' => ['required']],
                ['name' => 'role', 'label' => 'Role', 'type' => 'select', 'options' => ['Author', 'Reviewer']],
                ['name' => 'summary', 'label' => 'Summary', 'type' => 'textarea',
                    'visibleWhen' => ['type' => 'jsonata', 'expression' => 'role = \"Author\"']],
            ],
        ]],
        'submit' => ['label' => 'Save profile'],
    ]"
    :value="old() + ['name' => $profile->name, 'role' => $profile->role]"
    :errors="$errors"
    submit-mode="event"
    validate-on="input"
/>
```

Set `readonly` for a non-editable view. For an HTML or fetch submission, also supply `action` and
`method`; file fields use the same schema and automatically select multipart behavior.

## Forms Builder (optional Livewire 4)

Builder is a Livewire 4 authoring enhancement for exactly the Viewer schema. It supports field
catalogue add/remove/reorder, attribute/options/rules/JSONata editing, sections/steps, preview,
JSON import/export and undo/redo. It is intentionally unavailable rather than reduced when the
host has not installed Livewire 4.

```blade
<x-daisy-kit::forms.builder
    :schema="[
        'fields' => [
            ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'rules' => ['required', 'email']],
        ],
    ]"
    name="registration_schema"
    :value="$registrationSchema"
    :errors="$errors"
    :preview="true"
    :json-editor="true"
/>
```

The submitted hidden value and preview use the same canonical schema. JSONata is always
`{ "type": "jsonata", "expression": "…" }`; do not send a string dialect.

## Table

Table uses the restored v4 product vocabulary on the v5 runtime. Use `mode="client"` with local
rows or `mode="server"` with an `endpoint`. Search, typed filters, sorting, pagination, column
controls and selection remain isolated per table. `persist-state` opts into URL or local state.

```blade
<x-daisy-kit::table
    :columns="[
        ['key' => 'name', 'label' => 'Name', 'sortable' => true],
        ['key' => 'state', 'label' => 'State', 'sortable' => true],
    ]"
    :rows="$projects"
    :filters="[[
        'id' => 'state',
        'label' => 'State',
        'type' => 'select',
        'options' => [
            ['value' => 'draft', 'label' => 'Draft'],
            ['value' => 'ready', 'label' => 'Ready'],
        ],
    ]]"
    :page-size="25"
    :page-size-options="[10, 25, 50]"
    selection="multiple"
    row-key="id"
    persist-state="url"
    state-key="projects"
    caption="Projects"
    :bulk-actions="[['id' => 'archive', 'label' => 'Archive selected']]"
    :row-actions="[['id' => 'open', 'label' => 'Open']]"
    :row-details="true"
    :editable="['columns' => ['name', 'state'], 'endpoint' => url('/projects/{rowId}')]"
/>
```

For server data, pass `mode="server" endpoint="/projects/table"`; the default transport receives
`filter`, `page`, `pageSize`, `sort`, `direction`, `columnFilters`, `columnPinning`, and
`columnVisibility` query parameters and returns `{ "rows": [/* rows */], "total": 42 }`.

The Spatie Query Builder adapter emits its native `filter[...]`, signed `sort`, `page[number]`, and
`page[size]` vocabulary and accepts Laravel paginator resources with `data` plus pagination `meta`:

```blade
<x-daisy-kit::table
    mode="server"
    endpoint="/projects/table"
    server-adapter="spatie-query-builder"
    global-filter-key="global"
    filter-mode="manual"
    :columns="[['key' => 'name', 'sortKey' => 'users.name']]"
    :filters="[['id' => 'status', 'filterKey' => 'state', 'type' => 'select']]"
/>
```

Register the corresponding allowed filters and sorts in the endpoint's Spatie Query Builder; the
adapter intentionally leaves authorization and allowed-field policy server-side. An editable endpoint may contain
`{rowId}` and returns `{ "row": { /* updated row */ } }`. Same-origin mutations use the host's
`meta[name="csrf-token"]` value when present. Listen for
`daisy-kit:table:*` events to perform application actions.

Host controls can drive one table instance without querying or mutating its private DOM. `mount(root)`
returns the same facade later available through `getInstance(root)`:

```js
import { getInstance, mount } from '@daisy-kit/table.js';

const root = document.querySelector('#people-table');
const table = mount(root) ?? getInstance(root);

externalSearch.addEventListener('input', (event) => table.setGlobalFilter(event.currentTarget.value));
teamFilter.addEventListener('change', (event) => table.setColumnFilter('team', event.currentTarget.value));
```

The facade provides `getState()`, `getVisibleRows()`, `refresh()`, `clearFilters()`,
`setGlobalFilter()`, `setColumnFilter()`, `setPage()`, `setPageSize()`, `setSorting()`,
`setColumnVisibility()`, `applyFilters()`, `selectRow()`, `selectPage()`, `selectAllResults()`, and
`clearSelection()`. Each method remains scoped to its root; business notifications still use the
`daisy-kit:table:*` event family.

Filters are instant by default. `filter-mode="manual"` stages toolbar and column-filter changes,
shows a translated DaisyUI primary button, and applies the whole set in one client update or server
request. External controls use the same staging contract through `setColumnFilter()` followed by
`applyFilters()`.

Custom cell layouts remain server-owned. A Blade renderer receives `$item`, `$row`, `$value`, `$column`,
and `$table`, so it may compose host Blade components without exposing an additional Daisy Kit alias:

```blade
:columns="[[
    'key' => 'owner',
    'label' => 'Owner',
    'cell' => ['renderer' => 'blade', 'view' => 'tables.cells.owner'],
]]"
```

Plain cells are always escaped. Raw markup requires the explicit
`['cell' => ['renderer' => 'trusted-html']]` boundary. Built-in labels use the `daisy-kit::table`
translation namespace and may be overridden through Laravel's normal package translation mechanism.

## Tree

Tree is a keyboard-accessible selector with single/multiple selection, indeterminate propagation,
hidden form binding, expanded paths, local or remote search, lazy children and optional
persistence.

```blade
<x-daisy-kit::tree
    :items="[
        ['id' => 'docs', 'label' => 'Documentation', 'expanded' => true, 'children' => [
            ['id' => 'intro', 'label' => 'Introduction'],
        ]],
        ['id' => 'remote', 'label' => 'Remote branch', 'source' => '/tree/remote'],
    ]"
    label="Project areas"
    :multiple="true"
    name="areas"
    persistence-key="project-areas"
    :searchable="true"
    search-source="/tree/search"
/>
```

Remote endpoints should tolerate cancellation; a destroyed Tree ignores late results. The hidden
`areas` input stays synchronized with the selected roots.

## Blueprint

Blueprint renders and edits a directed graph using the supplied node/edge data. `editable` enables
node/transition CRUD, typed inspection, connections, arrange/fit, history and synchronized hidden
JSON for normal form submission.

```blade
<x-daisy-kit::blueprint
    :nodes="[
        ['id' => 'draft', 'label' => 'Draft', 'value' => ['owner' => 'Ada']],
        ['id' => 'review', 'label' => 'Review', 'value' => ['owner' => 'Grace']],
    ]"
    :edges="[['id' => 'draft-review', 'source' => 'draft', 'target' => 'review', 'label' => 'submit']]"
    label="Publication workflow"
    :editable="true"
    name="workflow_blueprint"
    :value="$workflowBlueprint"
/>
```

The visual SVG is non-interactive; semantic controls own focus, keyboard editing and
`daisy-kit:blueprint:*` events. The `workflow_blueprint` hidden field is the synchronized JSON
value.

## File Preview

File Preview accepts application metadata or a safe URL, validates MIME and size, and renders
images, audio, video, PDF, text and DOCX. The shell uses DaisyUI theme tokens; the document itself
remains in an opaque iframe without same-origin permission. Office formats other than DOCX,
spreadsheets, presentations and archives receive an explicit download-only state.

```blade
<x-daisy-kit::file-preview
    :file="$document"
    :url="route('documents.show', $document)"
    :download-url="route('documents.download', $document)"
    type="pdf"
    :name="$document->original_name"
    :file-size="$document->size"
    :max-preview-bytes="10 * 1024 * 1024"
    layout="card"
    preview-mode="modal"
    notice="Preview is isolated; download for the original file."
/>
```

`layout` controls the surrounding UI (`card`, `compact-list`, `action-only`) while `preview-mode`
controls the interaction (`auto`, `inline`, `modal`, `download`). These concerns are deliberately
separate. Named `trigger`, `metadata`, `actions`, `notice` and `modalFooter` slots customize regions
without exposing another public Blade component. When download is enabled, the modal footer also
contains the validated download action. Multipage DOCX previews scroll inside the isolated frame;
PDF pages are rendered internally and scroll vertically without depending on the browser's PDF plugin.

```blade
<x-daisy-kit::file-preview
    :url="$document->temporaryUrl()"
    type="docx"
    layout="action-only"
    preview-mode="modal"
    docx-view="width"
    :docx-zoom="90"
>
    <x-slot:trigger>
        <button class="btn btn-secondary" type="button">Inspect the brief</button>
    </x-slot:trigger>
</x-daisy-kit::file-preview>
```

For external controls, import `getInstance` from `@daisy-kit/file-preview.js`. The returned facade
exposes `getState()`, `open()`, `close()`, `retry()`, `setZoom()`, `zoomIn()` and `zoomOut()`. Listen
It also exposes `fit()` to fit a DOCX page to the available width. Listen for
`daisy-kit:file-preview:loading`, `ready`, `empty`, `error`, `open`, `close`, `zoom` and `retry`
to synchronize application UI without depending on iframe internals.

## Map

Map combines Leaflet, Terra Draw and Turf behind the `map` component: host-selected tiles,
markers, GeoJSON, XYZ/WMS/GeoJSON layers, basemaps, optional geolocation, drawing/editing,
spatial selection, measurements, history and GeoJSON export.

```blade
<x-daisy-kit::map
    :geojson="$projectBoundary"
    :center="[48.1173, -1.6778]"
    :zoom="12"
    tile-url="/maps/tiles/{z}/{x}/{y}.png"
    tile-attribution="Local map tiles"
    :markers="[['id' => 'office', 'label' => 'Office', 'position' => [48.1173, -1.6778]]]"
    :layers="[
        ['id' => 'zones', 'label' => 'Zoning', 'type' => 'geojson', 'data' => $zoning],
        ['id' => 'cadastre', 'label' => 'Cadastre', 'type' => 'wms',
         'url' => config('services.maps.wms_url'), 'options' => ['layers' => 'parcels']],
    ]"
    :drawing="true"
    :spatial-selection="true"
    :geolocation="true"
    label="Project map"
/>
```

Use provider URLs and attribution authorized by the host. All configuration remains encoded JSON
and events use `daisy-kit:map:*`. The complete layer shapes, facade, events, CSP directives and
migration notes are in [`map.md`](map.md).
