# Laravel Daisy Kit v5 corrective examples

These examples target the corrective development line beginning with
`v5.1.0-alpha.1`. Pin that VCS tag exactly while **validation propriétaire en attente**.
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

Table accepts local rows or a server `source`, typed column filters, search, sorting, pagination,
visibility/pinning, persistent selection, bulk/row actions, details and editing. `persistence`
controls URL/local state only when the host explicitly opts in.

```blade
<x-daisy-kit::table
    :columns="[
        ['id' => 'name', 'label' => 'Name', 'filter' => ['type' => 'text']],
        ['id' => 'state', 'label' => 'State', 'filter' => ['type' => 'select', 'options' => ['draft', 'ready']]],
    ]"
    :rows="$projects"
    :page-size="25"
    :selectable="true"
    :bulk-actions="[['id' => 'archive', 'label' => 'Archive selected']]"
    :row-actions="[['id' => 'open', 'label' => 'Open']]"
    :row-details="true"
    :editable="['columns' => ['name', 'state'], 'endpoint' => route('projects.update', ['project' => '{rowId}'])]"
    :persistence="['key' => 'projects', 'mode' => 'url']"
/>
```

For server data, pass `source="/projects/table"`; the endpoint receives `filter`, `page`,
`pageSize`, `sort`, `direction`, `columnFilters`, `columnPinning`, and `columnVisibility` query
parameters and returns `{ "rows": [/* rows */], "total": 42 }`. An editable endpoint may contain
`{rowId}` and returns `{ "row": { /* updated row */ } }`. Listen for
`daisy-kit:table:*` events to perform application actions.

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

File Preview accepts an application-controlled URL, validates type/size and offers metadata,
thumbnail-capable media/document rendering, preview/open/download actions,
modal/card/action-only layouts, zoom and notices. DOCX rendering remains inside a sandboxed iframe
with no same-origin permission; use a direct or signed URL that can be fetched without a redirect
instead of assuming a public route.

```blade
<x-daisy-kit::file-preview
    :src="route('documents.show', $document)"
    type="pdf"
    :name="$document->original_name"
    :max-bytes="10 * 1024 * 1024"
    layout="modal"
    notice="Preview is isolated; download for the original file."
/>
```

Set `type` to `text`, `image`, `pdf`, `video`, or `docx`; when it is omitted, a recognized file
extension selects the mode. The module validates the fetched MIME type and emits loading, empty
and error states instead of exposing untrusted content to the host page.

## Map

Map combines Leaflet, Terra Draw and Turf behind the `map` component: host-selected tiles,
markers, GeoJSON, XYZ/WMS/GeoJSON layers, basemaps, optional geolocation, drawing/editing,
spatial selection, measurements, history and GeoJSON export.

```blade
<x-daisy-kit::map
    :geojson="$projectBoundary"
    :center="[48.1173, -1.6778]"
    :zoom="12"
    tile-url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
    tile-attribution="&copy; OpenStreetMap contributors"
    :markers="[['id' => 'office', 'label' => 'Office', 'position' => [48.1173, -1.6778]]]"
    :layers="[['id' => 'zones', 'label' => 'Zoning', 'geojson' => $zoning]]"
    :wms="[['id' => 'cadastre', 'url' => config('services.maps.wms_url'), 'layers' => 'parcels']]"
    :drawing="true"
    :spatial-selection="true"
    :geolocation="true"
    label="Project map"
/>
```

Use provider URLs and attribution authorized by the host. Map's narrowly documented CSS/CSP
exception is confined to its map runtime; all configuration remains encoded JSON and its events
use `daisy-kit:map:*`.
