# ADR-007: Focus v5 on eleven independently mounted modules

## Status

Accepted — 2026-08-29

Supersedes ADR-001 and ADR-004 for the public v5 surface and removes their Forms/Livewire
decisions. Historical v4 releases and their documentation remain unchanged.

## Context

V4 exposed a broad anonymous Blade surface, including generic DaisyUI wrappers, application
layouts, templates and several larger workflows. The initial v5 correction retained two Forms
entries and an optional Livewire authoring runtime. That scope conflicts with a focused package
boundary: it duplicates host form ownership, adds a framework-specific integration, and makes the
public contract larger without supplying a distinct cross-application workflow.

The v4 audit identified six small behaviours that are more cohesive than generic wrappers: copying
technical values, rich choice selection, signature capture, controlled text disclosure, in-page
section tracking, and transfer between assignment lists. These are retained only as explicit,
independently mounted modules—not as a restored v4 compatibility layer.

## Decision

V5 exposes exactly these eleven Blade components:

- `x-daisy-kit::table`, `x-daisy-kit::tree`, `x-daisy-kit::blueprint`,
  `x-daisy-kit::file-preview`, and `x-daisy-kit::map`;
- `x-daisy-kit::copyable`, `x-daisy-kit::combobox`, `x-daisy-kit::signature`,
  `x-daisy-kit::truncate`, `x-daisy-kit::scrollspy`, and
  `x-daisy-kit::transfer-list`.

Each entry owns matching `dist/{stem}.js` and `dist/{stem}.css` files and exports
`mount(root)`, `mountAll(scope = document)`, `unmount(root)`, and `getInstance(root)`. Entries do
not load each other, create a global bootstrap, or rely on v4 aliases. The exact consumer contract
and product outcomes live in `docs/specs/v5-public-contract.md` and
`docs/specs/v5-product-contract-matrix.md`.

Forms Viewer, Forms Builder, every FormKit source, the Livewire integration, its package
configuration and Livewire development dependency are removed. V5 has no Forms components and no
Livewire dependency, suggestion, conditional registration or adapter.

## Dependencies and CSP

The focused modules use browser APIs where possible: Clipboard for Copyable, native form controls
and `fetch` for Combobox, Canvas/Pointer Events for Signature, and IntersectionObserver for
Scrollspy. Combobox and Transfer List use `@tanstack/match-sorter-utils`; Signature keeps
`signature_pad ^5.1.3`; Transfer List keeps `sortablejs ^1.15.7`. Map, Table, Blueprint and File
Preview retain their documented third-party dependencies.

All configuration is escaped non-executable JSON. No Blade entry emits inline handlers,
executable scripts, `style` attributes or global state. Each `unmount` removes listeners,
observers, pending requests and third-party instances. SignaturePad and SortableJS write DOM style
properties, so pages mounting Signature or Transfer List require the page-wide
`style-src-attr 'unsafe-inline'` exception. Every other parent-page module operates with
`style-src-attr 'none'`; File Preview keeps its separately sandboxed child policy.

## Consequences

- Host applications own schema-driven forms and any Livewire integration.
- The public Blade allowlist is enforceable mechanically and cannot grow through anonymous view
  discovery alone.
- The six focused behaviours must earn their inclusion with independent Blade, ESM, CSS and
  outcome tests; none inherits v4's global auto-initialisation.
- Generic DaisyUI primitives, layouts, templates, charts, calendars, editors, CSRF refresh and
  all other v4 components remain out of scope.

## Alternatives rejected

- Retaining Forms Builder as an optional enhancement: still couples the package to Livewire and a
  second form-authoring product.
- Restoring all v4 wrappers: recreates the broad surface v5 deliberately left behind.
- Shipping the six behaviours in one interaction bundle: defeats explicit imports, lifecycle
  isolation and independent CSP verification.
