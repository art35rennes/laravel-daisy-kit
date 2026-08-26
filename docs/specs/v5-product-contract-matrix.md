# V5 corrective product contract matrix

This document is the sole parity oracle for the v5 corrective stream. “Parity” means
restoring differentiating business outcomes, not preserving v4 aliases, templates, generic
DaisyUI wrappers, ECharts, calendars, or undocumented quirks. Every row must have an
automated outcome test before it is considered delivered.

## Shared runtime contract

| Outcome | Required proof |
| --- | --- |
| Every public entry mounts idempotently, supports multiple roots and fully unmounts. | Vitest unit/integration and Workbench browser tests. |
| An HTTP/non-Web-Crypto host gives every root a stable DOM identifier; initialization errors retain a machine-readable reason and safe message. | Red/green Vitest and host-browser simulation. |
| Configuration is escaped JSON, invalid JSON is actionable, and events use only `daisy-kit:{module}:*`. | Pest rendering plus Vitest event tests. |
| Module UI is semantic, keyboard usable, responsive and CSP compliant. | Browser axe/keyboard/narrow viewport/CSP-console tests. |
| Host owns DaisyUI/Tailwind and imports independent `@daisy-kit/{module}.{js,css}` entries. | Fresh Composer/Vite host build and served-HTTP test. |

## Forms Viewer

| User outcome | V4 evidence | Corrective v5 proof |
| --- | --- | --- |
| A nested schema renders sections, columns and multi-step progression without losing fields. | `resources/views/components/forms/viewer.blade.php`; `forms/partials/field.blade.php` at `v4.0.0`. | Canonical nested/wizard fixture renders and keyboard advances only valid steps. |
| Users complete supported text, number, email, date, select, checkbox, radio, file and textarea fields with attributes, options and rules. | `FormKit` field partials at `v4.0.0`. | Viewer DOM/form-data contract fixture; unsupported type reports a diagnostic rather than becoming text. |
| Laravel values/errors, readonly and progressive validation remain visible and accessible. | v4 Viewer props and `FormErrorBagMapper`. | Pest Blade contract plus browser validation/error focus tests. |
| JSONata visibility/computed values and submit modes `none`, `event`, `html`, `fetch` work safely. | v4 schema/runtime contract. | Vitest evaluates canonical expressions and asserts transport/events/multipart behavior. |
| Runtime API exposes current value, validation and destruction through module-local state only. | v4 viewer runtime. | Multiple-root/unmount API tests. |

## Forms Builder (Livewire 4 optional)

| User outcome | V4 evidence | Corrective v5 proof |
| --- | --- | --- |
| With Livewire 4 installed, an author can add, remove and reorder a field from a catalogue. | `src/Livewire/FormsBuilder.php`; v4 builder view. | Livewire/Pest interaction test and browser keyboard test. |
| The author edits attributes, options, rules, JSONata, sections and steps; invalid schema/expression diagnostics are visible. | v4 FormBuilder state/validation. | Canonical builder fixture and validation tests. |
| Preview uses the actual Viewer contract; undo/redo and JSON import/export stay synchronized with `name`/`value`. | v4 Builder preview and hidden input. | Browser outcome test and native input/change assertions. |
| Without Livewire 4, the public component is safe and clearly reports the unavailable enhancement. | v4 conditional integration intent. | Provider/Blade Pest test. |

## Table

| User outcome | V4 evidence | Corrective v5 proof |
| --- | --- | --- |
| Client and server data sources support typed filters, search, sort and pagination. | `resources/js/table/*`, `src/Support/DaisyTable*.php` at `v4.0.0`. | TanStack fixture tests transport request/result and visible rows. |
| Users control columns (visibility/pinning), persist selection for bulk actions, open details/actions and edit data. | v4 table state/renderers. | Browser keyboard/click workflow and persisted-state tests. |
| Loading, empty and error states, URL/config persistence and separate instances remain deterministic. | v4 table runtime. | Host browser network and multiple-root tests. |

## Tree

| User outcome | V4 evidence | Corrective v5 proof |
| --- | --- | --- |
| Single and multiple selection support propagation, indeterminate parents, selected leaves/roots and hidden form binding. | `resources/views/components/ui/advanced/tree-view.blade.php`; `resources/js/treeview.js` at `v4.0.0`. | Browser keyboard/form submission fixture. |
| Users expand paths, search locally or remotely with debounce, and lazily load branches. | v4 tree endpoint/search contract. | Fetch fixture and focus-preserving browser test. |
| Selection/expansion persistence is configurable and instance-local. | v4 tree persisted state. | Vitest storage/multiple-root/unmount tests. |

## Blueprint

| User outcome | V4 evidence | Corrective v5 proof |
| --- | --- | --- |
| Viewer and editor create, edit, delete and connect nodes/transitions; read-only prevents mutation. | `resources/js/blueprint/{model,runtime,interactions,history,inspector}.js` at `v4.0.0`. | Browser edit/read-only outcome fixture. |
| Typed inspector, search, arrange/fit, undo/redo and hidden JSON `name` synchronization are available. | v4 Blueprint API in README and runtime. | Vitest state/history plus native input/change test. |
| Controls remain semantic and keyboard accessible without nested interactive SVG. | v4 Blueprint interaction model; alpha.3 accessibility fix. | axe serious/critical and keyboard browser tests. |

## File Preview

| User outcome | V4 evidence | Corrective v5 proof |
| --- | --- | --- |
| File or URL exposes validated metadata/thumbnail and previews text, image, PDF, video and DOCX when supported. | `src/Support/FilePreview.php`; v4 File Preview Blade/runtime. | Served-host fixture verifies each supported result and limitation state. |
| Card, modal and action-only layouts support preview, open/download, size/zoom and notices. | v4 file-preview component/runtime. | Browser interactions and responsive tests. |
| Untrusted document rendering stays in a sandboxed opaque-origin iframe; no public route, proxy or asset publication is required. | ADR-002 and v4 document support. | CSP/network tests, origin/source/token tests and destroy-before-ready tests. |

## Map

| User outcome | V4 evidence | Corrective v5 proof |
| --- | --- | --- |
| A host configures tiles/providers, markers and GeoJSON, basemaps and XYZ/WMS/GeoJSON overlays. | v4 Leaflet Blade/runtime and README GIS API. | Served-host fixture with local tiles/layers. |
| Controls cover layers, optional geolocation, drawing/editing, object types, spatial selection and Turf measurements. | v4 `resources/js/leaflet/*`. | Browser interaction fixture and event assertions. |
| Users undo/redo and export GeoJSON; loading/error behaviour is clear. | v4 Leaflet API. | Vitest and browser state tests. |
| Leaflet/Terra Draw CSP exception is minimal and documented; host remains strict otherwise. | ADR-002. | CSP violation/console test with configured directives. |

## Delivery plan

1. **Foundation — shared contract and insecure-origin IDs.** Add matrix and a failing
   reproduction test, replace direct `randomUUID()` DOM identity use, preserve diagnostic
   errors, and verify in a served host. Depends on no product slice.
2. **Forms vertical slices.** Viewer schema/field/submit first, then optional Livewire
   Builder authoring and preview. Depends on the shared configuration/runtime contract.
3. **Data/navigation vertical slices.** Table transport/state and Tree selection/lazy
   search run independently once their configuration vocabulary is written.
4. **Visual/document/geospatial slices.** Blueprint editor, File Preview media/actions,
   and Map layers/drawing each retain their own ESM/CSS entry and CSP proof.
5. **Release checkpoint.** Rebuild `dist`, fresh host and Workbench browser gates, Pest
   full and TIA, Larastan, Pint, Vitest, Composer/npm audit, reproducibility and code
   review. Publish only a new immutable prerelease after the package fixture is green.

## Risks and mitigations

| Risk | Mitigation |
| --- | --- |
| Broad v4 behaviour reintroduces generic wrappers. | Matrix scopes only differentiated outcomes and public allowlist remains enforced. |
| Feature restoration creates opaque monoliths. | Small vertical commits, colocated module code and outcome-focused tests. |
| File Preview fallback weakens frame authentication. | Authenticate with frame source plus a unique per-instance token; test wrong source/token and opaque origin. |
| Leaflet third-party runtime violates strict style policy. | Use real CSP-violation browser proof and isolate/document the narrow exception. |
