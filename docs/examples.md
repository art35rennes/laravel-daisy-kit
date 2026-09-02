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
import '@daisy-kit/table.css';
import '@daisy-kit/tree.css';
import '@daisy-kit/blueprint.css';
import '@daisy-kit/file-preview.css';
import '@daisy-kit/map.css';
import '@daisy-kit/copyable.css';
import '@daisy-kit/combobox.css';
import '@daisy-kit/signature.css';
import '@daisy-kit/truncate.css';
import '@daisy-kit/scrollspy.css';
import '@daisy-kit/transfer-list.css';

import { mountAll as mountTable } from '@daisy-kit/table.js';
import { mountAll as mountTree } from '@daisy-kit/tree.js';
import { mountAll as mountBlueprint } from '@daisy-kit/blueprint.js';
import { mountAll as mountFilePreview } from '@daisy-kit/file-preview.js';
import { mountAll as mountMap } from '@daisy-kit/map.js';
import { mountAll as mountCopyable } from '@daisy-kit/copyable.js';
import { mountAll as mountCombobox } from '@daisy-kit/combobox.js';
import { mountAll as mountSignature } from '@daisy-kit/signature.js';
import { mountAll as mountTruncate } from '@daisy-kit/truncate.js';
import { mountAll as mountScrollspy } from '@daisy-kit/scrollspy.js';
import { mountAll as mountTransferList } from '@daisy-kit/transfer-list.js';

mountTable();
mountTree();
mountBlueprint();
mountFilePreview();
mountMap();
mountCopyable();
mountCombobox();
mountSignature();
mountTruncate();
mountScrollspy();
mountTransferList();
```

Every entry also offers `mount(root)`, `getInstance(root)`, and `unmount(root)`. `mount` is
idempotent and returns the same root-local facade later returned by `getInstance`; `mountAll`
returns facades in DOM order. Call `unmount` before removing an explicitly managed root. It returns
whether an instance was destroyed. Getters return detached snapshots, synchronous commands return
booleans, and asynchronous commands return `Promise<boolean>`.

Expected operational failures return `false` and emit a structured event instead of throwing.
A command may also return `false` for a documented no-op or rejected target (for example opening an
already open popover or focusing an unknown Tree item); those cases do not represent a runtime
failure and do not emit `error`:

```js
root.addEventListener('daisy-kit:combobox:error', ({ detail }) => {
    console.error(detail.code, detail.message);
});
```

The host compiles Tailwind CSS and DaisyUI; the package's CSS adds only module-specific layout and
behavior. The complete method returns and `CustomEvent.detail` shapes are normative in
[`specs/v5-public-contract.md`](specs/v5-public-contract.md).

## Focused interaction modules

### Copyable

```blade
<x-daisy-kit::copyable
    value="{{ $apiToken }}"
    show-icon
    success-label="API token copied."
>
    API token
</x-daisy-kit::copyable>
```

```js
import '@daisy-kit/copyable.css';
import { mount } from '@daisy-kit/copyable.js';

const root = document.querySelector('[data-daisy-kit-module="copyable"]');
const copyable = mount(root);

root.addEventListener('daisy-kit:copyable:copied', ({ detail }) => console.log(detail.value));
root.addEventListener('daisy-kit:copyable:error', ({ detail }) => console.error(detail.code, detail.message));
await copyable.copy();
```

`getValue()` returns the resolved plain text and `copy(value?)` returns `Promise<boolean>`.
`showIcon=false` controls the decorative copy glyph. The transient visual status is enabled by
default, reuses `successLabel` / `errorLabel`, and hides after `feedbackDuration`; pass
`:show-feedback="false"` to keep the live announcement without displaying the tooltip. Copyable
submits no field and needs no CSP exception. Clipboard refusal, an insecure context, disabled
state, or empty text emit the structured errors listed in the public contract.

### Combobox

```blade
<x-daisy-kit::combobox
    name="reviewers"
    :options="$reviewers"
    :source="route('reviewers.index')"
    :min-chars="0"
    :max-suggestions="20"
    size="lg"
    multiple
/>
```

Each reviewer may use the safe plain-data shape below. `description` is suited to an e-mail address;
`meta` carries a team or role. `avatar` is optional and falls back to `initials` when unavailable.

```php
[
    'value' => 'reviewer-42',
    'label' => 'Ada Lovelace',
    'description' => 'ada@example.test',
    'meta' => 'Platform · Maintainer',
    'initials' => 'AL',
    'avatar' => asset('people/ada.jpg'),
]
```

```js
import '@daisy-kit/combobox.css';
import { getInstance, mount } from '@daisy-kit/combobox.js';

const root = document.querySelector('[data-daisy-kit-module="combobox"]');
const combobox = mount(root) ?? getInstance(root);

root.addEventListener('daisy-kit:combobox:change', ({ detail }) => console.log(detail.value, detail.values));
root.addEventListener('daisy-kit:combobox:loading', ({ detail }) => console.log(detail.loading, detail.query));

combobox.setOptionRenderer((option, { selected }) => {
    const content = document.createElement('span');
    content.className = 'flex items-center justify-between gap-3';
    content.textContent = `${option.label} · ${option.description}`;
    if (selected) content.dataset.selected = 'true';

    return content;
});
```

Focus or pointer activation opens local suggestions. A remote source is loaded on first open when
`minChars=0`; use a positive threshold for search-only endpoints. `maxSuggestions` bounds the DOM
without imposing virtualization. `getValue`, `setValue`, `clear`, `open`, `close`,
`setOptionRenderer`, and `clearOptionRenderer` are synchronous; `refresh()` returns
`Promise<boolean>`. A custom renderer receives frozen plain data and may return a DOM node or plain
text; Daisy Kit retains the semantic option wrapper. Events are `change { value, values }`, `query { query }`,
`loading { loading, query }`, and `error { code, message, query? }`. Laravel receives repeated,
ordered `reviewers[]` fields in multiple mode (or one `reviewers` field in single mode). No CSP
exception is needed unless remote avatar URLs require a broader `img-src`. Invalid remote
responses, unavailable sources, and renderer exceptions emit structured errors.

### Signature

```blade
<x-daisy-kit::signature name="approval_signature" required />
```

```js
import '@daisy-kit/signature.css';
import { mount } from '@daisy-kit/signature.js';

const root = document.querySelector('[data-daisy-kit-module="signature"]');
const signature = mount(root);

root.addEventListener('daisy-kit:signature:change', ({ detail }) => console.log(detail.empty, detail.value));
await signature.setValue(previousSignature);
```

`clear`, `undo`, `redo`, and `isEmpty` are synchronous; `setValue()` returns
`Promise<boolean>`. `toDataURL`, `toSVG`, and `toData` return detached exports. Events are
`change { empty, value }`, `stroke-ended { value }`, `clear { empty, value }`, and
`error { code, message }`. Laravel receives a PNG Data URL under `approval_signature`. Pages using
Signature must allow `style-src-attr 'unsafe-inline'`; invalid initial or assigned values emit
`invalid-value`.

### Truncate

```blade
<td class="max-w-64">
    <x-daisy-kit::truncate
        :text="$customer->address"
        reveal-label="Show full address"
        :hover-delay="250"
        :backdrop="false"
    />
</td>
```

```js
import '@daisy-kit/truncate.css';
import { mount } from '@daisy-kit/truncate.js';

const root = document.querySelector('[data-daisy-kit-module="truncate"]');
const truncate = mount(root);

root.addEventListener('daisy-kit:truncate:opened', ({ detail }) => console.log(detail.text));
truncate.refresh();
```

`refresh`, `open`, and `close` return booleans; `isTruncated()` reports measured overflow. Events
are `opened { text }` and `closed { text }`. Truncate submits no field, requires no CSP exception,
and treats an already-open/already-closed action as a no-op returning `false`. The ellipsis appears
only when the value overflows. Hover or focus opens a temporary anchored preview; clicking or
keyboard-activating it pins the selectable text. `hover=false` disables transient pointer/focus
opening, `hoverDelay` accepts 0–2000 milliseconds, and `backdrop=true` adds a backdrop only while
the preview is pinned. Outside interaction and Escape close a pinned preview natively.

### Scrollspy

```blade
<x-daisy-kit::scrollspy target="#release-notes" :items="$sections" />
```

```js
import '@daisy-kit/scrollspy.css';
import { mount } from '@daisy-kit/scrollspy.js';

const root = document.querySelector('[data-daisy-kit-module="scrollspy"]');
const scrollspy = mount(root);

root.addEventListener('daisy-kit:scrollspy:change', ({ detail }) => console.log(detail.id));
scrollspy.scrollTo('security');
```

`refresh()` and `scrollTo(id)` return booleans; `getActive()` returns the active id or `null`.
`change { id }` is emitted when the active section changes. Scrollspy submits no value and needs no
CSP exception. An unknown section is a rejected target returning `false`, not an operational error.

### Transfer List

Transfer List presents two bounded assignment panels with selected/total counts, explicit empty
states and a select-all checkbox. Selection can apply to the current page or every filtered result.
Select target items and use the arrow controls, or press ArrowRight / ArrowLeft on an option to
transfer it directly. Use the reorder buttons or Alt+ArrowUp / Alt+ArrowDown on a target option to
change submission order without dragging. Disabled items cannot be selected or displaced.

```blade
<x-daisy-kit::transfer-list
    name="assignees"
    label="Review team"
    source-label="Company directory"
    target-label="Assigned reviewers"
    :items="$availableUsers->map(fn ($user) => [
        'value' => (string) $user->getKey(),
        'label' => $user->name,
        'description' => $user->email,
        'meta' => $user->team->name,
        'avatar' => $user->avatar_url,
        'initials' => $user->initials,
        'disabled' => ! $user->can_review,
    ])->all()"
    :value="$assignedUserIds"
    searchable
    pagination
    :page-size="10"
    select-all-scope="page"
    required
/>
```

```js
import '@daisy-kit/transfer-list.css';
import { mount } from '@daisy-kit/transfer-list.js';

const root = document.querySelector('[data-daisy-kit-module="transfer-list"]');
const transfer = mount(root);

root.addEventListener('daisy-kit:transfer-list:change', ({ detail }) => {
    console.log(detail.values, detail.direction, detail.movedValues);
});
root.addEventListener('daisy-kit:transfer-list:selection-change', ({ detail }) => {
    console.log(detail.side, detail.values);
});

transfer.setSelection('source', ['reviewer-42', 'reviewer-84']);
transfer.move('to-target');
```

`getTargetValues()` returns an ordered snapshot and `getSelection()` returns detached source and
target selections. `setTargetValues`, `setSelection`, `selectAll`, `move`, `reorder`, `setPage`, and
`clearSelection` return booleans. `selectAll(side, 'page'|'filtered')` toggles only eligible items.
Events are `selection-change { side, values }`, `search { side, query }`,
`page-change { side, page, pageSize, totalPages }`,
`change { values, direction, movedValues }`, `reorder { values }`, and
`error { code, message, values, ...context }`.

Laravel receives ordered repeated `assignees[]` fields, independently of search and pagination.
Set `one-way` when assignments may only be added through the visible controls. Pages using Transfer
List must allow `style-src-attr 'unsafe-inline'`; invalid sides, pages, directions, values, limits,
disabled commands, and invalid reorder permutations emit structured errors. Item fields are always
rendered as safe text or image attributes; Transfer List accepts no serialized HTML template.

### Combined Laravel form

```blade
<form method="POST" action="{{ route('reviews.store') }}">
    @csrf
    <x-daisy-kit::copyable value="{{ $apiToken }}" />
    <x-daisy-kit::combobox name="reviewers" :options="$reviewers" multiple allow-custom />
    <x-daisy-kit::signature name="approval_signature" required />
    <x-daisy-kit::transfer-list
        name="assignees"
        :items="$availableUsers"
        :value="$assignedUserIds"
        searchable
    />
    <button type="submit" class="btn btn-primary">Save review</button>
</form>

<x-daisy-kit::truncate :text="$releaseNotes" :lines="2" />
<x-daisy-kit::scrollspy target="#release-notes" :items="$sections" />
```

These modules are independent interaction entries. Copyable reports clipboard success or failure;
Combobox preserves native form values; Signature synchronizes its captured value; Truncate and
Scrollspy remain keyboard accessible; and Transfer list supports search, selection and optional
ordering.

Pages mounting Signature or Transfer List must allow `style-src-attr 'unsafe-inline'`, because
SignaturePad and SortableJS write runtime DOM styles. Keep those controls on the smallest practical
page surface. Copyable, Combobox, Truncate and Scrollspy need no such exception.

The form submits ordered `reviewers[]` and `assignees[]` fields and a PNG Data URL under
`approval_signature`. The component props remain `name="reviewers"` and `name="assignees"`; Daisy
Kit adds `[]` only to generated multiple-value inputs.

The same APIs can be combined without shared state:

```js
import { getInstance as getCombobox, mount as mountCombobox } from '@daisy-kit/combobox.js';
import { getInstance as getCopyable } from '@daisy-kit/copyable.js';
import { getInstance as getSignature } from '@daisy-kit/signature.js';
import { getInstance as getTruncate } from '@daisy-kit/truncate.js';
import { getInstance as getScrollspy } from '@daisy-kit/scrollspy.js';
import { getInstance as getTransferList } from '@daisy-kit/transfer-list.js';

const comboboxRoot = document.querySelector('[data-daisy-kit-module="combobox"]');
const combobox = mountCombobox(comboboxRoot) ?? getCombobox(comboboxRoot);

comboboxRoot.addEventListener('daisy-kit:combobox:change', ({ detail }) => {
    console.log(detail.value, detail.values);
});

await combobox.refresh();
getCopyable(document.querySelector('[data-daisy-kit-module="copyable"]')).copy();
getSignature(document.querySelector('[data-daisy-kit-module="signature"]')).undo();
getTruncate(document.querySelector('[data-daisy-kit-module="truncate"]')).open();
getScrollspy(document.querySelector('[data-daisy-kit-module="scrollspy"]')).scrollTo('security');
getTransferList(document.querySelector('[data-daisy-kit-module="transfer-list"]'))
    .move('to-target', ['reviewer-42']);
```

A remote Combobox performs `GET {source}?{queryParam}=...` and expects plain JSON. Previous requests
are aborted and late responses are ignored:

```json
{
    "items": [
        { "value": "reviewer-42", "label": "Ada Lovelace", "description": "Maintainer" }
    ],
    "nextCursor": null
}
```

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

To hide the initial “0 rows selected” summary without hiding selection controls:

```blade
<x-daisy-kit::table
    :columns="$columns"
    :rows="$projects"
    :selection="['mode' => 'multiple', 'summaryVisibility' => 'after-first-selection']"
/>
```

After the first non-empty selection (including initial or API selections), the summary stays
visible even after clearing. It resets on remount. Omit `summaryVisibility` to retain the default.
`pageSizeOptions` supplies the choices; the current page size is added automatically if absent.
Server endpoints must accept every advertised size and return the total filtered result count.

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

root.addEventListener('daisy-kit:table:selection-changed', ({ detail }) => {
    console.log(detail.ids);
});
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

Remote endpoints should tolerate cancellation; an unmounted Tree ignores late results. The hidden
`areas` input stays synchronized with selected loaded leaves by default. Set
`value-mode="selected-roots"` to submit whole branches instead.

```js
import { getInstance } from '@daisy-kit/tree.js';

const root = document.querySelector('[data-daisy-kit-module="tree"]');
const tree = getInstance(root);

await tree.expand('docs');
tree.setValue(['intro']);
root.addEventListener('daisy-kit:tree:change', ({ detail }) => {
    console.log(detail.value, detail.values);
});
```

In both modes Laravel receives one `areas` field containing an ordered JSON array. It contains
`["intro"]` for the selection above and `[]` when empty; decode it with Laravel's normal JSON
validation/casting boundary. The facade remains mode-sensitive: `getValue()` returns an id or
`null` in single mode and an array in multiple mode. Tree reports lazy/search failures as
`{ code, message, id?, query? }`.

Use `search-mode="manual"` to expose a Search button, `:value` for initial selection,
and `node-view` for inert custom Blade presentation. The [Tree contract](tree.md) describes
bulk selection, visible/hidden counters, translations, lazy paths and the complete integrator API.

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

```js
import { getInstance } from '@daisy-kit/blueprint.js';

const root = document.querySelector('[data-daisy-kit-module="blueprint"]');
const blueprint = getInstance(root);

blueprint.select('review');
blueprint.fit();
const graph = blueprint.getValue(); // Detached { nodes, edges } snapshot.

root.addEventListener('daisy-kit:blueprint:change', ({ detail }) => {
    console.log(detail.value.nodes, detail.value.edges);
});
```

Laravel receives the same graph serialized as JSON under `workflow_blueprint`. The facade object
remains identical when a structural update internally remounts the renderer.

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

For external controls, import `getInstance` from `@daisy-kit/file-preview.js`. The stable facade
exposes `getState()`, `open()`, `close()`, `setExpanded(boolean)`, `reload()`, `retry()`, `setZoom()`,
`zoomIn()`, `zoomOut()` and `fit()`. Commands return booleans; `reload()` returns
`Promise<boolean>` indicating whether reinitialization was accepted. Transport completion is
announced through `ready` or `error`. Reload rereads the root JSON without replacing the facade.
`setZoom()` rounds and clamps finite numbers to 25–200; read the effective zoom from `getState()`.
`fit()` requests an asynchronous fit-to-width operation whose measured zoom arrives in `zoom`.

The frozen state snapshot is `{ canDownload, canPreview, expanded, isOpen, layout, mimeType,
name, open, previewMode, status, type, zoom }`. `expanded`, `isOpen` and `open` all reflect modal
visibility; expanding never changes the configured Blade layout. Business events include this
snapshot plus the following specific fields: `loading`, `empty`, `open`, `close` and `retry` have
no extra fields; `ready` adds `{ mimeType, deferred? }`, `error` adds `{ code, message }`, and
`zoom` adds `{ zoom, mode? }` (`fit` or `manual`). `setExpanded()` also emits
`preview { open }` and `layout { layout, expanded }`. Listen under the
`daisy-kit:file-preview:` prefix. Lifecycle `mounted` and `unmounted` details are `{}`;
configuration failures use the shared structured error contract.

```js
import { getInstance } from '@daisy-kit/file-preview.js';

const root = document.querySelector('[data-daisy-kit-module="file-preview"]');
const preview = getInstance(root);

preview.open();
preview.setZoom(125);
await preview.reload();

root.addEventListener('daisy-kit:file-preview:error', ({ detail }) => {
    console.error(detail.code, detail.message);
});
```

The facade exposes serializable state only; the sandboxed iframe, frame authentication token,
fetched Blob, and document renderer are intentionally private.

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
