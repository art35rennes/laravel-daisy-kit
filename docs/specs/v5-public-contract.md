# Daisy Kit v5 public contract

## Scope

This is the complete supported Blade surface. Any component outside this table is an
architecture-test failure. The detailed business outcomes and test oracle are in
[`v5-product-contract-matrix.md`](v5-product-contract-matrix.md).

| Module | Blade component | Essential contract |
| --- | --- | --- |
| Forms viewer | `x-daisy-kit::forms.viewer` | Schema-driven, recursive and progressively validated form viewer with JSONata and safe submission modes. |
| Forms builder | `x-daisy-kit::forms.builder` | Livewire 4-optional authoring surface for the same Viewer schema, including diagnostics, history and synchronized JSON; without Livewire it reports the unavailable enhancement rather than rendering a reduced editor. |
| Table | `x-daisy-kit::table` | Client/server TanStack data workbench with typed filters, persistent selection and configurable data actions. |
| Tree | `x-daisy-kit::tree` | Keyboard-accessible hierarchical selector with multiple/indeterminate selection, lazy loading and search. |
| Blueprint | `x-daisy-kit::blueprint` | Accessible directed-graph viewer/editor with inspector, history and synchronized JSON. |
| File Preview | `x-daisy-kit::file-preview` | Isolated previews and actions for validated document/media sources, without inline scripts. |
| Map | `x-daisy-kit::map` | Leaflet/Terra Draw/Turf map with layers, editable GeoJSON and spatial tools. |

## Module entry contract

Each module has independent `dist/{module}.js` and `dist/{module}.css` entries. Its JavaScript
exports `mount(root)`, `mountAll(scope = document)`, and `unmount(root)`. Mounting is idempotent,
supports multiple roots, and returns no global state. `unmount` removes listeners, observers, and
third-party instances. An optional Livewire adapter remounts roots after Livewire navigations.

The package is Composer/VCS-installed, so host Vite source must not use its Composer name as an
import specifier. Hosts configure `@daisy-kit` to resolve to
`vendor/art35rennes/laravel-daisy-kit/dist`, then import explicit module pairs as
`@daisy-kit/{module}.js` and `@daisy-kit/{module}.css`. The allowed entry stems are
`forms-viewer`, `forms-builder`, `table`, `tree`, `blueprint`, `file-preview`, and `map`.

| Module | ESM import | CSS import |
| --- | --- | --- |
| Forms viewer | `@daisy-kit/forms-viewer.js` | `@daisy-kit/forms-viewer.css` |
| Forms builder | `@daisy-kit/forms-builder.js` | `@daisy-kit/forms-builder.css` |
| Table | `@daisy-kit/table.js` | `@daisy-kit/table.css` |
| Tree | `@daisy-kit/tree.js` | `@daisy-kit/tree.css` |
| Blueprint | `@daisy-kit/blueprint.js` | `@daisy-kit/blueprint.css` |
| File Preview | `@daisy-kit/file-preview.js` | `@daisy-kit/file-preview.css` |
| Map | `@daisy-kit/map.js` | `@daisy-kit/map.css` |

Configuration is emitted in a non-executable `application/json` script element and is parsed with
strict validation. Invalid JSON activates an accessible error state. No public component emits an
inline handler, executable script, or `style` attribute.

Forms Builder emits `visibleWhen` and `computed` JSONata expressions in the canonical descriptor
shape `{ "type": "jsonata", "expression": "…" }`. Forms Viewer accepts this shape only.

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

### Map configuration

`x-daisy-kit::map` uses the canonical view, source, tile, control, drawing and persistence props
documented in [`../map.md`](../map.md). XYZ, WMS and GeoJSON overlays share the `layers` shape;
the alpha `wms` prop and every legacy global are removed. `mount(root)` returns the Map facade and
`getInstance(root)` retrieves it after automatic mounting. `getLeafletMap()` is the sole documented
third-party escape hatch; all normal integration uses serializable configuration and
`daisy-kit:map:*` events.

## Corrective development line

The corrective development contract deliberately has no compatibility layer for v5.0.0 or its
historical alpha releases. Consumers pin the current VCS prerelease and use its documentation as
one coherent contract; no alias, fallback dialect, or adapter is provided for prior v5 tags.

## Deliberate non-goals

There are no `x-daisy` aliases, DaisyUI primitive wrappers, application templates, charts,
calendars, CSRF routes, icon systems, asset publishing, CodeMirror, Trix, GridStack, or global
bundle. v4 compatibility is outside v5 and is served exclusively by `legacy/4.x` / `v4.0.0`.

## Verification matrix

- Pest: provider/discovery, public allowlist, serialized configuration, invalid JSON, semantic
  empty/loading/error markup, strict-CSP rendering, and conditional Livewire registration.
- Vitest: every entry, idempotency, multiple roots, destruction, keyboard/focus behavior, and
  Livewire remount adapter.
- Workbench browser: autonomous pages for all modules, keyboard focus, and narrow viewport smoke.
- Quality: fresh Composer installation, Pint, Larastan level max, Pest type coverage, Vitest,
  reproducible `dist` build, and zero Composer/npm audit findings.
