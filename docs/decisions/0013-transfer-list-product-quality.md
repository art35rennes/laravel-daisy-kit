# ADR-0013: Restore Transfer List as a complete assignment control

## Status

Accepted — 2026-09-02

Extends ADR-0007 and ADR-0008 for the Transfer List product and integrator contracts.

## Context

The first focused v5 Transfer List proved ordered native submission, keyboard transfer and optional
drag-and-drop. Its visible product remained too close to a technical dual listbox: flat text rows,
always-enabled text actions, no selection summary, no select-all operation, no bounded large-list
navigation and weak empty states. That makes ordinary team, role and catalogue assignment slower
and less legible than the package's Table and Tree systems.

[MDBootstrap Transfer](https://mdbootstrap.com/docs/standard/plugins/transfer/) establishes a useful
baseline with two explicit panels, checked and disabled items, select-all, pagination and one-way
operation. [Ant Design Transfer](https://ant.design/components/transfer/) additionally demonstrates
that transfer rows often need a richer but still data-driven presentation. Daisy Kit must provide
those outcomes while keeping DaisyUI responsible for primitive styling, Laravel responsible for
form submission and SortableJS optional rather than essential.

## Decision

Transfer List is rebuilt as two bounded DaisyUI card panels. Each panel owns a header, selected and
total counts, optional select-all checkbox, optional search, a scrollable list, explicit empty and
no-results states, and optional local pagination. The transfer actions use semantic DaisyUI buttons,
reflect whether an operation is currently possible and become a horizontal action row when the
panels stack on narrow screens.

The item contract is extended additively to
`{ value, label, description?, meta?, avatar?, initials?, disabled? }`. These fields are rendered as
text and image attributes through DOM APIs; serialized HTML is never accepted. `oneWay=true` removes
the reverse visible operation and rejects reverse facade commands. `selectAllScope` is `page` or
`filtered`, defaulting to `page`, so bulk selection stays predictable when pagination is enabled.

The existing stable facade remains valid and gains `getSelection`, `setSelection`, `selectAll` and
`setPage`. Selection, search and page changes emit dedicated structured events. Transfer changes
identify their direction and moved values while preserving the ordered `values` payload and repeated
Laravel `name[]` inputs.

Pagination is local and opt-in. Virtualization and remote fetching remain out of scope: large remote
choice sets belong to Combobox unless a future contract defines server-owned membership and selection
semantics. Buttons and keyboard are complete interactions; SortableJS remains only an enhancement for
target ordering.

## Consequences

- Integrators can use Transfer List for people, email, permission and catalogue assignment without
  supplying unsafe markup.
- DaisyUI `card`, `input`, `checkbox`, `button`, `badge` and `join` primitives own the visible theme;
  module CSS is limited to layout, bounded scrolling and interaction states.
- Pagination limits rendered rows but does not alter the complete ordered form value.
- Select-all always states its scope and never moves disabled items.
- The Workbench demonstrates a realistic Laravel team assignment form and does not expose facade or
  event diagnostics.
- The existing SortableJS CSP exception remains unchanged.

## Alternatives rejected

- Styling the existing flat listboxes only: leaves the missing selection, scale and empty-state
  outcomes unresolved.
- Accepting arbitrary HTML item templates: weakens the CSP-safe data boundary and makes accessible
  row semantics the integrator's responsibility.
- Adding TanStack Virtual: its runtime styles widen the CSP exception and are unnecessary for bounded
  local pages.
- Adding remote pagination now: target membership across unloaded pages requires a separate transport
  and consistency contract.
