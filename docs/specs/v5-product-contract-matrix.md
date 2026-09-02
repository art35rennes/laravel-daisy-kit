# V5 corrective product contract matrix

This document is the sole parity oracle for the v5 corrective stream. “Parity” means
restoring differentiating business outcomes, not preserving v4 aliases, templates, generic
DaisyUI wrappers, ECharts, calendars, or undocumented quirks. Every row must have an
automated outcome test before it is considered delivered.

## Shared runtime contract

| Outcome | Required proof |
| --- | --- |
| Every public entry mounts idempotently, supports multiple roots and fully unmounts. | Vitest unit/integration and Workbench browser tests. |
| `mount` and `getInstance` expose one stable facade; getters return detached snapshots, commands report success, and operational failures are structured events rather than exceptions. | Per-module Vitest facade identity, return-value, payload and error tests. |
| An HTTP/non-Web-Crypto host gives every root a stable DOM identifier; initialization errors retain a machine-readable reason and safe message. | Red/green Vitest and host-browser simulation. |
| Configuration is escaped JSON, invalid JSON is actionable, and events use only `daisy-kit:{module}:*`. | Pest rendering plus Vitest event tests. |
| Module UI is semantic, keyboard usable, responsive and CSP compliant. | Browser axe/keyboard/narrow viewport/CSP-console tests. |
| Host owns DaisyUI/Tailwind and imports independent `@daisy-kit/{module}.{js,css}` entries. | Fresh Composer/Vite host build and served-HTTP test. |
| The Workbench remains a representative Laravel host rather than an API explorer or documentation UI. | Browser assertions cover normal Blade/forms/routes and reject facade consoles, event logs, inspectors, and visible test-only controls. |

## Focused interaction modules

| User outcome | V4 evidence | Corrective v5 proof |
| --- | --- | --- |
| A technical identifier can be copied with pointer or keyboard input, can expose a familiar copy icon, and receives transient visual plus accessible success or failure feedback. | `resources/views/components/ui/utilities/copyable.blade.php`; `resources/js/modules/copyable.js` at `v4.0.0`. | Icon rendering, timed Clipboard-success/failure feedback, keyboard and unmount tests. |
| Users can filter and select local or remote choices while the native form value remains synchronized. | `resources/views/components/ui/inputs/multi-select.blade.php`; `resources/js/modules/multi-select.js` at `v4.0.0`. | Local/remote filtering, focus, form-data and cancellation tests. |
| A user can draw, clear and export a signature while the submitted form value stays synchronized. | `resources/views/components/ui/inputs/sign.blade.php`; `resources/js/modules/sign.js` at `v4.0.0`. | Pointer, resize, reset, form-data and CSP tests. |
| Long text has an accessible expanded state without mutating the original value. | `resources/views/components/ui/utilities/truncate-text.blade.php` at `v4.0.0`. | Overflow, keyboard disclosure and multiple-root tests. |
| In-page navigation updates the active section while preserving accessible links and keyboard use. | `resources/views/components/ui/advanced/scrollspy.blade.php`; `resources/js/scrollspy.js` at `v4.0.0`. | Intersection, keyboard, teardown and multiple-root tests. |
| Users can search, select and move assignments between two legible panels, with optional ordering. | `resources/views/components/ui/advanced/transfer.blade.php`; `resources/js/transfer.js` at `v4.0.0`. | Move, search, selection, ordering, form-data and teardown tests. |
| Users understand the scope of bulk selection, the number selected and the result of an empty search. | MDBootstrap Transfer interaction baseline; ADR-0013. | Select-all scope, counts, disabled items and empty/no-results outcome tests. |
| Local assignment sets remain usable on narrow screens and with dozens of rich people or catalogue rows. | ADR-0013 corrective product decision. | Independent panel pagination, rich safe rows, keyboard and responsive Workbench browser tests. |

## Table

| User outcome | V4 evidence | Corrective v5 proof |
| --- | --- | --- |
| Client and server data sources support typed filters, search, sort and pagination. | `resources/js/table/*`, `src/Support/DaisyTable*.php` at `v4.0.0`. | TanStack fixture tests transport request/result and visible rows. |
| Users control columns (visibility/pinning), persist selection for bulk actions, open details/actions and edit data. | v4 table state/renderers. | Browser keyboard/click workflow and persisted-state tests. |
| Loading, empty and error states, URL/config persistence and separate instances remain deterministic. | v4 table runtime. | Host browser network and multiple-root tests. |

## Tree

| User outcome | V4 evidence | Corrective v5 proof |
| --- | --- | --- |
| Single and multiple selection support propagation, indeterminate parents, selected leaves/roots and one JSON-array hidden field under the exact configured `name`. | `resources/views/components/ui/advanced/tree-view.blade.php`; `resources/js/treeview.js` at `v4.0.0`. | Blade and Vitest assertions cover `[]`, single-id and ordered multiple-id submissions without adding `[]` to the field name. |
| Users expand paths, search locally or remotely with debounce, and lazily load branches. | v4 tree endpoint/search contract. | Fetch fixture and focus-preserving browser test. |
| Integrators compose a loaded-node predicate with search, optionally highlight matching characters, and page very large lazy branches without eagerly loading siblings or continuations. | Table filter integration pattern and ADR-0011. | Facade/filter, semantic highlight, cursor transport and branch-isolation tests. |
| Selection/expansion persistence is configurable and instance-local. | v4 tree persisted state. | Vitest storage/multiple-root/unmount tests. |
| Host code reads and changes selection or expansion through a stable facade without private DOM access. | v5 integrator-facade contract. | Vitest `getValue`/`setValue`/`clear`/`expand`/`collapse`/`focus` and canonical `change` payload tests. |
| Visible disclosure and selection controls, disabled subtrees, loaded-scope bulk actions and hidden-selection counts support real classification and permissions workflows. | v4 controls and corrective v5 product plan, ADR-0009. | Tree product tests and four responsive Workbench scenarios. |
| Manual/fuzzy search, branch-local retry, cached empty loads and canonical remote merging preserve context and selection. | v4 search/lazy outcomes and ADR-0009. | Transport-race, retry, search reset and lazy-persistence outcome tests. |

## Blueprint

| User outcome | V4 evidence | Corrective v5 proof |
| --- | --- | --- |
| Viewer and editor create, edit, delete and connect nodes/transitions; read-only prevents mutation. | `resources/js/blueprint/{model,runtime,interactions,history,inspector}.js` at `v4.0.0`. | Browser edit/read-only outcome fixture. |
| Typed inspector, search, arrange/fit, undo/redo and hidden JSON `name` synchronization are available. | v4 Blueprint API in README and runtime. | Vitest state/history plus native input/change test. |
| Controls remain semantic and keyboard accessible without nested interactive SVG. | v4 Blueprint interaction model; alpha.3 accessibility fix. | axe serious/critical and keyboard browser tests. |
| Host code reads, replaces, selects, arranges and traverses history through one facade that survives structural remounts. | v5 integrator-facade contract. | Vitest facade identity, detached snapshots, commands and event-payload tests. |

## File Preview

| User outcome | V4 evidence | Corrective v5 proof |
| --- | --- | --- |
| File or URL exposes validated metadata/thumbnail and previews text, image, PDF, video and DOCX when supported. | `src/Support/FilePreview.php`; v4 File Preview Blade/runtime. | Served-host fixture verifies each supported result and limitation state. |
| Card, modal and action-only layouts support preview, open/download, size/zoom and notices. | v4 file-preview component/runtime. | Browser interactions and responsive tests. |
| Untrusted document rendering stays in a sandboxed opaque-origin iframe; no public route, proxy or asset publication is required. | ADR-002 and v4 document support. | CSP/network tests, origin/source/token tests and destroy-before-ready tests. |
| Host code controls preview visibility, expansion, zoom and reload without accessing the sandbox or renderer. | v5 integrator-facade contract. | Vitest facade state/commands, reload cancellation and event-payload tests. |

## Map

| User outcome | V4 evidence | Corrective v5 proof |
| --- | --- | --- |
| A host configures tiles/providers, markers and GeoJSON, basemaps and XYZ/WMS/GeoJSON overlays. | v4 Leaflet Blade/runtime and README GIS API. | Served-host fixture with local tiles/layers. |
| Controls cover layers, optional geolocation, drawing/editing, object types, spatial selection and Turf measurements. | v4 `resources/js/leaflet/*`. | Browser interaction fixture and event assertions. |
| Hosts compose independent direct actions, menus, groups, nested menus and named control slots without executable configuration. | v4 toolbar extensibility and v5 CSP boundary. | PHP tree validation plus keyboard and responsive browser tests. |
| Users undo/redo and export GeoJSON; loading/error behaviour is clear. | v4 Leaflet API. | Vitest and browser state tests. |
| Leaflet/Terra Draw CSP exception is minimal and documented; host remains strict otherwise. | ADR-002. | CSP violation/console test with configured directives. |

## Delivery plan

1. **Foundation — shared contract and insecure-origin IDs.** Add matrix and a failing
   reproduction test, replace direct `randomUUID()` DOM identity use, preserve diagnostic
   errors, and verify in a served host. Depends on no product slice.
2. **Focused interaction slices.** Copyable, Combobox, Signature, Truncate, Scrollspy and
   Transfer list each retain an independent Blade/ESM/CSS contract and outcome tests.
3. **Data/navigation vertical slices.** Table transport/state and Tree selection/lazy
   search run independently once their configuration vocabulary is written.
4. **Visual/document/geospatial slices.** Blueprint editor, File Preview media/actions,
   and Map layers/drawing each retain their own ESM/CSS entry and CSP proof.
5. **Release checkpoint.** Rebuild `dist`, fresh host and representative Workbench browser gates, Pest
   full and TIA, Larastan, Pint, Vitest, Composer/npm audit, reproducibility and code
   review. Publish only a new immutable prerelease after the package fixture is green.

## Risks and mitigations

| Risk | Mitigation |
| --- | --- |
| Broad v4 behaviour reintroduces generic wrappers. | Matrix scopes only differentiated outcomes and public allowlist remains enforced. |
| Feature restoration creates opaque monoliths. | Small vertical commits, colocated module code and outcome-focused tests. |
| File Preview fallback weakens frame authentication. | Authenticate with frame source plus a unique per-instance token; test wrong source/token and opaque origin. |
| Leaflet third-party runtime violates strict style policy. | Use real CSP-violation browser proof and isolate/document the narrow exception. |
