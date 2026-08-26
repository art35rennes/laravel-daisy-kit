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
