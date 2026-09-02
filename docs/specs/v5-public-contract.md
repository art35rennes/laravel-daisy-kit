# Daisy Kit v5 public contract

## Scope

This is the complete supported Blade surface. Any component outside this table is an
architecture-test failure. The detailed business outcomes and test oracle are in
[`v5-product-contract-matrix.md`](v5-product-contract-matrix.md).

| Module | Blade component | Essential contract |
| --- | --- | --- |
| Table | `x-daisy-kit::table` | Client/server TanStack data workbench with typed filters, persistent selection and configurable data actions. |
| Tree | `x-daisy-kit::tree` | Keyboard-accessible hierarchical selector with multiple/indeterminate selection, lazy loading and search. |
| Blueprint | `x-daisy-kit::blueprint` | Accessible directed-graph viewer/editor with inspector, history and synchronized JSON. |
| File Preview | `x-daisy-kit::file-preview` | Isolated previews and actions for validated document/media sources, without inline scripts. |
| Map | `x-daisy-kit::map` | Leaflet/Terra Draw/Turf map with layers, editable GeoJSON and spatial tools. |
| Copyable | `x-daisy-kit::copyable` | Keyboard-accessible copy control for a technical value, with explicit success and failure feedback. |
| Combobox | `x-daisy-kit::combobox` | Local or remote multi-selection with keyboard navigation and native form-value synchronization. |
| Signature | `x-daisy-kit::signature` | Pointer-driven signature capture with a synchronized form value, reset and download controls. |
| Truncate | `x-daisy-kit::truncate` | Accessible disclosure of overflowing text without changing its source value. |
| Scrollspy | `x-daisy-kit::scrollspy` | Accessible in-page navigation that tracks the active section and preserves keyboard navigation. |
| Transfer list | `x-daisy-kit::transfer-list` | Two-list assignment control with search, selection and optional ordering. |

## Module entry contract

Each module has independent `dist/{module}.js` and `dist/{module}.css` entries. Its JavaScript
exports `mount(root)`, `mountAll(scope = document)`, `unmount(root)`, and `getInstance(root)`.
The lifecycle is normative:

- `mount(root)` accepts an `Element`, mounts it once, and returns its stable facade. A repeated call
  returns the same object. Invalid configuration or initialization returns `null` after an
  accessible error and a namespaced error event.
- `mountAll(scope = document)` mounts matching roots in DOM order and returns their
  facade-or-`null` results. Roots outside `scope` are untouched.
- `getInstance(root)` returns the exact mounted facade, or `null` when the root is not mounted.
- `unmount(root)` returns `true` when it destroyed an instance and `false` otherwise. It releases
  listeners, observers, requests, and third-party instances and restores DOM owned by the module.

`destroy` is an internal lifecycle hook and is never exposed on a public facade; integrators use
`unmount(root)` exclusively.

Facade getters return detached snapshots rather than private mutable state. A synchronous command
returns `true` only when it applies the operation; an asynchronous command returns
`Promise<boolean>`. An expected operational failure returns `false` and emits
`daisy-kit:{module}:error` with `{ code: string, message: string, ...context }`; it does not throw.
Documented no-ops and rejected command targets also return `false`, but do not emit `error` because
no runtime operation failed. Examples include opening an already open surface, selecting an unknown
id, or supplying a value outside a component's canonical domain. Passing a non-Element root is a
programmer error and may throw `TypeError`. Every facade is root-local and no module creates global
state. The rationale and Workbench boundary are recorded in
[ADR-0008](../decisions/0008-stable-integrator-facades-and-workbench-boundary.md).

Every module emits `mounted {}` after a successful mount and `unmounted {}` after destruction;
Map's asynchronous `mounted` detail additionally contains `{ state }`. Configuration failures use
`error { code: 'missing-configuration'|'invalid-configuration', message }`, initialization failures
use `error { code: 'initialization-failed', message }`, and module-specific operational errors keep
the same required `code`/`message` prefix.

The package is Composer/VCS-installed, so host Vite source must not use its Composer name as an
import specifier. Hosts configure `@daisy-kit` to resolve to
`vendor/art35rennes/laravel-daisy-kit/dist`, then import explicit module pairs as
`@daisy-kit/{module}.js` and `@daisy-kit/{module}.css`. The allowed entry stems are
`table`, `tree`, `blueprint`, `file-preview`, `map`, `copyable`, `combobox`, `signature`,
`truncate`, `scrollspy`, and `transfer-list`.

| Module | ESM import | CSS import |
| --- | --- | --- |
| Table | `@daisy-kit/table.js` | `@daisy-kit/table.css` |
| Tree | `@daisy-kit/tree.js` | `@daisy-kit/tree.css` |
| Blueprint | `@daisy-kit/blueprint.js` | `@daisy-kit/blueprint.css` |
| File Preview | `@daisy-kit/file-preview.js` | `@daisy-kit/file-preview.css` |
| Map | `@daisy-kit/map.js` | `@daisy-kit/map.css` |
| Copyable | `@daisy-kit/copyable.js` | `@daisy-kit/copyable.css` |
| Combobox | `@daisy-kit/combobox.js` | `@daisy-kit/combobox.css` |
| Signature | `@daisy-kit/signature.js` | `@daisy-kit/signature.css` |
| Truncate | `@daisy-kit/truncate.js` | `@daisy-kit/truncate.css` |
| Scrollspy | `@daisy-kit/scrollspy.js` | `@daisy-kit/scrollspy.css` |
| Transfer list | `@daisy-kit/transfer-list.js` | `@daisy-kit/transfer-list.css` |

Configuration is emitted in a non-executable `application/json` script element and is parsed with
strict validation. Invalid JSON activates an accessible error state. No public Blade view emits an
inline handler, executable script, or `style` attribute.

Signature and Transfer List depend respectively on SignaturePad and SortableJS, which write DOM
style properties while active. A host page using either module must allow
`style-src-attr 'unsafe-inline'`; the directive is page-wide, not scoped to the component. The
other nine modules retain `style-src-attr 'none'`. TanStack Virtual is deliberately not shipped.

### Table configuration

`x-daisy-kit::table` is the only public table component. Its product vocabulary is based on v4:
`mode`/`endpoint`, keyed `columns`, `rows`, typed `filters`, `search`, `pageSizeOptions`,
`columnVisibility`, `selection`/`rowKey`, `persistState`/`stateKey`, presentation options, row
actions/details and editing. The corrective line does not preserve the smaller v5 alpha dialect.

The package normalizes this data in PHP before emitting CSP-safe JSON. Internal toolbar, filter,
selection, table and pagination views live outside the anonymous component namespace and are not
public aliases. The runtime remains TanStack v9 with explicit `mount`, `mountAll`, and `unmount`,
instance-local state and `daisy-kit:table:*` events. `mount(root)` returns the table's stable facade;
`getInstance(root)` retrieves the same facade after automatic mounting. It exposes state snapshots,
visible rows, refresh, filters, pagination, sorting, visibility and selection controls without leaking
the underlying TanStack instance or creating global state. `filterMode="manual"` stages column filters
until the translated Apply filters action or facade `applyFilters()` method is invoked.

`selection.summaryVisibility` accepts `always` (default) or `after-first-selection`.
The latter hides only the initial zero-selection summary, leaving multiple-selection
controls available. Initial, user and API selections reveal it; clearing keeps it visible
until unmount. This visibility state is instance-local and is not persisted.
The page-size control always includes the effective size, even when restored or set through
the API to a value absent from `pageSizeOptions`.

The facade getters are `getState()` and `getVisibleRows()`. Its commands are `refresh()`,
`clearFilters()`, `setGlobalFilter(value)`, `setColumnFilter(columnId, value)`, `setPage(page)`,
`setPageSize(size)`, `setSorting(columnId, direction)`, `setColumnVisibility(columnId, visible)`,
`applyFilters()`, `selectRow(rowId, selected=true)`, `selectPage()`, `selectAllResults()`, and
`clearSelection()`. `refresh()` is asynchronous; the other commands return booleans. Events carry
serializable snapshots: `filtered { query?, filters? }`, `filters-applied { filters }`,
`page-changed { page }`, `sorted { column, direction }`, `selection-changed { ids, ...selection }`,
`row-action { action, row }`, `bulk-action { action, ids?, allFilteredSelected?, excludedIds? }`,
`edited { column, row, rowId, value }`, and `error { code, message, ...context }`.

### Tree configuration

The complete selector contract and examples are specified in [Tree](../tree.md).
Tree additionally supports initial values, disabled subtrees, `valueMode="leaves|selected-roots"`,
initial expansion paths, manual/automatic and includes/fuzzy search, opt-in match highlighting,
translated labels and inert Blade node presentation. Its additional facade methods are `getState`,
`setSearch`, `applySearch`, `clearSearch`, `setFilter`, `clearFilter`, `expandPath`, `expandAll`,
`collapseAll`, `selectVisible`, `loadMore`, and `reloadBranch`. `applySearch`, `expandPath`,
`loadMore`, and `reloadBranch` are asynchronous. Lazy branches accept paginated `{ items,
nextCursor? }` responses and fetch each continuation explicitly. Bulk actions only affect loaded
items; selected-root values can explicitly represent unloaded subtrees. Search merges remote paths
without dropping hidden selections. The selection footer counts submitted ids, including hidden ids.

Tree retains the selection, expansion, lazy loading, search and persistence configuration described
by the product matrix. Its facade exposes `getValue()`, `setValue(value)`, `clear()`, `expand(id)`,
`collapse(id)`, and `focus(id)`. `getValue()` returns one selected id or `null` in single mode and an
ordered id array in multiple mode; `expand()` returns `Promise<boolean>` because it may load a lazy
branch, while the other commands return booleans. Its canonical selection event is
`change { value, values }`. Expansion events are `expanded { id, label }` and
`collapsed { id, label }`; failures use `error { code, message, id?, query? }`. The earlier
`selection-changed` and `selected` event suffixes are not part of the corrective v5 contract.
When `name` is set, both modes submit one hidden field under that exact name. Its value is always
an ordered JSON array: `[]` when empty, `["node-id"]` for a single selection, and multiple ids in
selection order for multiple mode. Laravel integrators decode that field as JSON; Tree never adds
`[]` to its configured name.

### Blueprint configuration

Blueprint's facade exposes `getValue()`, `setValue(value)`, `getSelected()`, `select(id)`, `undo()`,
`redo()`, `arrange()`, and `fit()`. `getValue()` returns a detached `{ nodes, edges }` graph;
`getSelected()` returns a detached selected node or `null`; commands return booleans. Structural
remounts preserve the facade's object identity. Events are `change { value }`, where `value` is the
same graph shape, `select { id, node }`, `search { query }`, `arrange { viewBox }`,
`fit { viewBox }`, `empty {}`, and `error { code, message, id? }`.

### File Preview configuration

`x-daisy-kit::file-preview` accepts a `file` metadata value or a safe `url`, optional
preview/download URLs, MIME and display metadata. `layout` selects `card`, `compact-list`
or `action-only`; `previewMode` independently selects `auto`, `inline`, `modal` or
`download`. Image, video, audio, PDF, text and DOCX are previewable. Other recognized file
families render an explicit metadata/download state instead of a broken preview.

Private Blade views own the default trigger, metadata, actions, notice and modal footer;
named slots may replace those regions without adding another public component. The runtime
keeps its opaque sandbox and emits `daisy-kit:file-preview:*` events. `mount(root)` returns
the stable File Preview facade and `getInstance(root)` retrieves the same facade after
automatic mounting. The facade exposes snapshots, open/close, retry and zoom controls but
does not expose the iframe or renderer internals. Its zoom controls include `fit()` for an
explicit fit-to-width operation. A validated download remains available from the modal footer;
multipage DOCX and internally rendered PDF pages scroll within the bounded isolated frame.

File Preview also exposes `setExpanded(expanded)`, `setZoom(percent)` (25–200), and
asynchronous `reload()`. Commands return booleans (or `Promise<boolean>` for reload).
State and event payloads are documented in the integrator guide; no renderer internals are exposed.

### Map configuration

`x-daisy-kit::map` uses the canonical view, source, tile, control, drawing and persistence props
documented in [`../map.md`](../map.md). XYZ, WMS and GeoJSON overlays share the `layers` shape;
the alpha `wms` prop and every legacy global are removed. `mount(root)` returns the Map facade and
`getInstance(root)` retrieves it after automatic mounting. `getLeafletMap()` is the sole documented
third-party escape hatch; all normal integration uses serializable configuration and
`daisy-kit:map:*` events.

`controls` accepts `true`, `false`, or an immutable `MapControls` tree composed with
`MapControl` factories. Root actions are direct controls; menus, nested menus, groups and
named Blade slots follow the tree. An explicit tree is exhaustive, and every standard node
can be independently omitted, disabled or hidden without enabling a globally disabled Map
capability. The alpha `controls.sections` shape and single `controls` slot are removed.

The Map facade is documented in [`../map.md`](../map.md). Its getters return detached state,
GeoJSON, draw-layer, and selection snapshots through `getState()`, `getDrawLayer()`,
`getSelection()`, `exportGeoJSON()`, and `getLeafletMap()`. Its commands are `setView()`,
`fitBounds()`, `invalidateSize()`, `setGeoJSON()`, `setMarkers()`, `setLayerData()`,
`refreshLayer()`, `setBasemap()`, `setLayerVisibility()`, `setMode()`, `setDrawLayer()`,
`clearSelection()`, `deleteSelected()`, `undo()`, `redo()`, `locate()`, `startGeolocation()`, and
`stopGeolocation()`. Synchronous commands return booleans; `setLayerData()`, `refreshLayer()`, and
`locate()` return `Promise<boolean>`. Map
events expose these serializable details: `mounted|ready|empty { state }`, `unmounted {}`,
`error { code, message }`, `view { center, zoom }`, `tools { visible }`,
`marker { id, label, position, properties }`, `markers { markers }`, `data { geojson }`,
`layer { id, visible }`, `layer-data { id, data }`, `layer-refresh { id, status }`,
`layer-error { code, id, message }`, `basemap { id }`, `mode { mode, objectType, drawLayer }`,
`geometry { geojson }`, `geometry-finish { id, feature, measurement }`,
`selection { ids, features, source? }`, `spatial-selection { area, features }`,
`measurement { id, value }`, `history { action }`, `export { geojson }`,
`action { id, state }`,
`geolocation { accuracy, center, tracked }`, `geolocation-start|geolocation-stop {}`, and
`geolocation-error { code, message }`. `getLeafletMap()` is the only getter allowed to return a
private third-party instance.

### Copyable configuration

`x-daisy-kit::copyable` accepts `value`, `copyLabel`, `successLabel`, `errorLabel`,
`feedbackDuration=1000`, `disabled=false`, `showIcon=false`, and `showFeedback=true`. The optional
decorative icon supplements, but never replaces, the visible content. Feedback uses the existing
live status as a transient visual tooltip; disabling visual feedback keeps the accessible
announcement. Without `value`, it copies the displayed
`textContent`; an explicit value is always copied as plain text. It uses only
`navigator.clipboard.writeText()` after user action. Its facade exposes `copy(value?)` and
`getValue()`. `copy(value?)` returns `Promise<boolean>` and `getValue()` returns resolved plain text.
It emits `copied { value }` on success or `error { code, message, value }` with code `disabled`,
`empty-value`, `clipboard-unavailable`, or `clipboard-rejected`.

### Combobox configuration

`x-daisy-kit::combobox` accepts `name`, `label`, `options`, `value`, `multiple`, `allowCustom`,
`tokenSeparators`, `maxItems`, `source`, `queryParam='query'`, `debounce=200`, `minChars`,
`maxSuggestions=50`, `size='md'`, `required`, `disabled`, `readonly`, and `placeholder`. `size`
accepts `sm`, `md`, or `lg`. Options have the
plain-data shape `{ value, label, description?, disabled?, avatar?, initials?, meta? }`; no field
accepts HTML. Local results are ranked with TanStack Match Sorter and the rendered result list is
bounded by `maxSuggestions`.
A remote source receives GET queries and returns `{ items, nextCursor? }`; superseded requests are
aborted. With `minChars=0`, the first focus or pointer activation loads and opens initial remote
suggestions. Loading and empty results are visible in the popup. Single values submit under `name`,
multiple values as ordered repeated `name[]` fields.
Custom tokens, paste and separators are enabled only by `allowCustom`. The facade exposes
`getValue`, `setValue`, `clear`, `open`, `close`, `refresh`, `setOptionRenderer`, and
`clearOptionRenderer`; events are `change`, `query`, `loading`, and `error` in the module namespace.
`getValue()` returns a string or `null` in single mode and an ordered string array in multiple mode.
`setValue(value)`, `clear()`, `open()`, `close()`, `setOptionRenderer(renderer)`, and
`clearOptionRenderer()` return booleans; `refresh()` returns `Promise<boolean>`.
The option renderer receives a frozen detached option snapshot and frozen
`{ active, query, selected }` context, and returns a DOM `Node`, plain text, or `null`; the module
retains ownership of the outer option and its ARIA attributes. Event details are
`change { value, values }`, `query { query }`,
`loading { loading, query }`, and `error { code, message, query? }`. Native `change` continues to
bubble after the submitted value changes. Renderer exceptions emit `error` with code
`option-render-failed` and use the default safe option renderer.

### Signature configuration

`x-daisy-kit::signature` accepts `name`, `label`, `value`, logical `width`/`height`, `penColor`,
`backgroundColor`, `minWidth`, `maxWidth`, `velocityFilterWeight`, `throttle`, `minDistance`,
`required`, `disabled`, and `showUndo`/`showRedo`/`showClear`/`showDownload`. Its submitted value is
a PNG Data URL; the facade exposes `clear`, `undo`, `redo`, `isEmpty`, `setValue`, `toDataURL`,
`toSVG`, and `toData`. Events are `change`, `stroke-ended`, `clear`, and `error`. Resize preserves
point groups and uses the device-pixel ratio.
`clear()`, `undo()`, and `redo()` return booleans; `setValue(value)` returns `Promise<boolean>`;
`isEmpty()` returns a boolean. `toDataURL(type?, encoderOptions?)` returns a Data URL,
`toSVG(options?)` returns SVG text, and `toData()` returns detached point groups. Event details are
`change { empty, value }`, `stroke-ended { value }`, `clear { empty: true, value: '' }`, and
`error { code, message }`.

### Truncate configuration

`x-daisy-kit::truncate` accepts `text`, `lines=1`, `revealLabel`, and `title`. It measures actual
overflow and omits the reveal control when the text fits. The native popover contains the complete
plain text as selectable content. The facade exposes `refresh`, `isTruncated`, `open`, and `close`;
events are `opened` and `closed`.
`refresh()`, `open()`, and `close()` return booleans; `isTruncated()` returns a boolean. Event details
are `opened { text }` and `closed { text }`.

### Scrollspy configuration

`x-daisy-kit::scrollspy` accepts `target`, `items`, `selector='h2[id],h3[id]'`, `smooth=true`,
`offset=0`, and `rootMargin`. Items use `{ id, label }`; absent items are discovered from the
target headings. Page and scroll-container roots are supported, the active link uses
`aria-current="location"`, and nested navigation parents are updated. The facade exposes
`refresh`, `getActive`, and `scrollTo`; the event is `change`.
`refresh()` and `scrollTo(id)` return booleans; `getActive()` returns the active id or `null`.
The event detail is `change { id }`.

### Transfer List configuration

`x-daisy-kit::transfer-list` accepts `name`, `label`, `items`, `value`, `sourceLabel`,
`targetLabel`, `searchable`, `maxItems`, `disabled`, `required`, and `sortable=true`. Items use
`{ value, label, description?, disabled? }`; `value` is the ordered target key list. Ranked local
search uses TanStack Match Sorter. Buttons and keyboard remain complete alternatives to optional
SortableJS drag-and-drop. Ordered target values submit as repeated `name[]` fields. The facade
exposes `getTargetValues`, `setTargetValues`, `move`, `reorder`, and `clearSelection`; events are
`change`, `reorder`, and `error`. Pagination and virtualization are deliberately out of scope.
`getTargetValues()` returns an ordered string array. `setTargetValues(values)`,
`move(direction, values?)`, `reorder(values)`, and `clearSelection()` return booleans; direction is
`to-target` or `to-source`. Event details are `change { values }`, `reorder { values }`, and
`error { code, message, maxItems?, values }`. `reorder(values)` accepts only a complete permutation
of the current target values. Disabled items cannot be selected, transferred, or repositioned by
buttons, keyboard, façade, or drag-and-drop. Native `change` continues to bubble when the ordered
submitted value changes.

## Corrective development line

The corrective development contract deliberately has no compatibility layer for v5.0.0 or its
historical alpha releases. Consumers pin the current VCS prerelease and use its documentation as
one coherent contract; no alias, fallback dialect, or adapter is provided for prior v5 tags.

## Deliberate non-goals

There are no `x-daisy` aliases, additional DaisyUI primitive wrappers, application templates,
forms or Livewire integration, charts, calendars, CSRF routes, icon systems, asset publishing,
CodeMirror, Trix, GridStack, or global bundle. The eleven entries above are the complete public
surface. v4 compatibility is outside v5 and is served exclusively by `legacy/4.x` / `v4.0.0`.

## Verification matrix

- Pest: provider/discovery, public allowlist, serialized configuration, invalid JSON, semantic
  empty/loading/error markup, and strict-CSP rendering.
- Vitest: every entry, idempotency, multiple roots, destruction, and keyboard/focus behavior.
- Workbench browser: representative Laravel-host pages, native forms, keyboard focus, local routes,
  and narrow viewport smoke. The Workbench is not an API explorer and contains no facade console,
  event logger, or visible test-only control.
- Quality: fresh Composer installation, Pint, Larastan level max, Pest type coverage, Vitest,
  reproducible `dist` build, and zero Composer/npm audit findings.
