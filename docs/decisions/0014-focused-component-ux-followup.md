# ADR-0014: Clarify focused-component discovery and selection

## Status

Accepted — 2026-09-02

## Context

The first complete Workbench scenarios exposed four product gaps that isolated mount tests did not:

- Truncate placed its ellipsis at the far edge of a flex row instead of immediately after the
  visible text;
- a remote Combobox response replaced the complete option catalogue, so a selected rich token
  could fall back to its raw key after a query with no results;
- the Combobox popup participated in normal layout and pushed the form content down;
- Transfer List used a circular selection mark and kept displaying total membership as if it were
  the number of search results, which made multi-selection and filtering harder to understand.

The browser Popover API already provides the standard top-layer and light-dismiss behavior needed
by Truncate. WAI-ARIA keeps a Combobox popup separate from the current value and recommends one
selection state per listbox option. MUI distinguishes client options from server-owned filtering
and exposes a configurable option-to-search representation. WAI-ARIA similarly recommends a
consistent checked convention for multi-select listboxes without nesting interactive controls in
their options.

## Decision

- Truncate keeps `popover="auto"`. Its compact ellipsis remains adjacent to the clipped text and
  the native popover receives a responsive DaisyUI card presentation; no custom modal, tooltip
  runtime or positioning dependency is introduced.
- Combobox keeps a catalogue of every known option separately from the current suggestion result.
  Selected tokens resolve against that catalogue, including after an empty or partial remote
  response.
- Combobox suggestions are an absolutely positioned overlay below or above the field according to
  available viewport space. Opening the popup does not change the host form's layout height.
- Blade `options` and remote `source` remain the declarative client/server inputs. The stable facade
  additionally exposes `getOptions()` and `setOptions(options)` for client-owned data that changes
  after mount. Option snapshots are detached plain data and invalid updates fail with a structured
  `invalid-options` error.
- `searchFields` accepts a non-empty subset of `label`, `description`, `meta`, and `value`, defaulting
  to all four. It controls local and facade-fed option matching. A remote endpoint receives the
  existing query parameter and remains responsible for choosing its server-side searchable fields.
- Transfer List retains one ARIA `option` per item and does not nest a focusable checkbox. Its
  decorative checked affordance is square and its option state remains `aria-selected`. TanStack
  ranking is limited to case-insensitive substring matches instead of loose character sequences.
  During a search, panel copy reports the filtered result count alongside the complete side total.

## Consequences

- Existing Blade calls, remote response shapes and submitted Laravel values remain compatible.
- Hosts can replace client suggestions without remounting or reaching into private DOM.
- Remote searches no longer erase selected labels, descriptions or avatar metadata.
- Popups may visually overlap following content, so their z-index is explicit and their placement
  is recomputed while open on resize and scroll.
- Transfer List continues to use TanStack Match Sorter for ranked local results; the stricter
  threshold, visible count and deterministic tests make the result set understandable.
- The Workbench adds only representative forms and content cases. It does not add an API console,
  event log or component inspector.

## Sources

- https://developer.mozilla.org/en-US/docs/Web/API/Popover_API
- https://www.w3.org/WAI/ARIA/apg/patterns/combobox/
- https://www.w3.org/WAI/ARIA/apg/patterns/listbox/
- https://mui.com/material-ui/react-autocomplete/
- https://ant.design/components/transfer/
