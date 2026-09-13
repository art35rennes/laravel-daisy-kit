# ADR-010: Compose Map controls as a typed tree

## Status

Accepted — 2026-09-02

Supersedes the single-menu control decision in ADR-008. Drawing-layer visibility and the
OpenStreetMap provider vocabulary from ADR-008 remain accepted.

## Context

One fixed Map menu made the component compact, but forced unrelated layer, drawing,
selection, history and view workflows into the same disclosure. Reordering fixed sections
could not create several menus, nested navigation or direct map actions. The single
`controls` slot also gave hosts only one insertion point.

Executable callbacks cannot be serialized in CSP-safe configuration. The component instead
needs a declarative public contract whose structure determines presentation while the Map
runtime retains ownership of standard commands.

## Decision

`controls` accepts `true`, `false` or an immutable `MapControls` tree made of `MapControl`
descriptors. Root actions render directly on the map, root menus render as dropdowns,
nested menus render as submenus and groups render as titled sections. Omitting a node omits
that feature from the UI; `enabled` and `visible` independently disable or hide it.

Standard descriptors map to bounded runtime commands. A `customAction` emits
`daisy-kit:map:action` with an identifier and serializable state snapshot. Named slot
descriptors insert matching `map*` Blade slots without putting markup in JSON. Identifiers
are unique, menus are non-empty, nesting is limited to three menu levels and trees to one
hundred nodes.

`controls=true` resolves to the package preset: separate layer, drawing, selection and
history menus followed by direct fit, geolocation and fullscreen actions when their
capabilities exist. At small widths, direct view actions are presented in a compact View
menu. An explicit tree is exhaustive and receives no implicit controls.

## Consequences

- Integrators can independently include, order, disable and group every standard control.
- JSON remains non-executable and no JavaScript or Blade primitive becomes public.
- Capability props remain authoritative; a control cannot enable a disabled feature.
- The alpha `controls.sections` array and single `controls` slot are intentionally removed.
- Menu-open state remains ephemeral and is not persisted with geographic state.
