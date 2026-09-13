# ADR 0012: Make Combobox suggestions discoverable and composable

## Status

Accepted

## Date

2026-09-02

## Context

The first v5 Combobox kept selected tokens below a conventional text input and only displayed a
remote result after typing. That met the data contract but did not communicate that suggestions
were available, wasted vertical space, and could only flatten a person-like option into one text
line. Integrators need a credible recipients/assignees picker without accepting serialized HTML.

WAI-ARIA explicitly permits an editable combobox popup to open when the empty input receives focus
and keeps DOM focus on the input while `aria-activedescendant` identifies an option. MUI exposes
open-on-focus, custom option/value rendering, and a result limit when virtualization is absent. Ant
Design similarly separates option and token rendering and documents remote user search.

## Decision

- Focus, pointer activation, the disclosure button, or Arrow Down opens available suggestions.
- A remote source with `minChars=0` loads its initial suggestions on first open. Loading and empty
  states remain visible inside the popup instead of making the popup silently disappear.
- Multiple selections render as compact removable tokens inside the same bordered control as the
  text input. The input grows within that wrapping surface rather than creating a second row below.
- `size='sm'|'md'|'lg'` applies DaisyUI's field scale to the complete token surface rather than to
  an isolated text input.
- The plain-data option shape gains optional `avatar`, `initials`, and `meta` fields. The default
  renderer combines these with `label` and `description` for person/e-mail suggestions without
  allowing serialized HTML.
- The facade gains `setOptionRenderer(renderer)` and `clearOptionRenderer()`. A renderer receives a
  frozen detached option plus frozen `{ active, query, selected }` context and may return a DOM
  `Node`, a plain string, or `null`. Daisy Kit continues to own the outer `role=option` element and
  all ARIA state. Renderer failures emit `option-render-failed` and fall back to the safe renderer.
- `maxSuggestions=50` bounds the rendered list. Remote pagination and very large data exploration
  remain application concerns; the first page must stay useful without a virtualization runtime.
- In multiple mode, selecting an option keeps the popup open and returns focus to the input. An
  empty-input Backspace removes the last removable token.

## Alternatives considered

### Serialized HTML in option data

Rejected because it weakens the CSP-safe plain-data boundary and makes escaping responsibility
ambiguous.

### A named Blade view for each suggestion

Rejected as the only extension point because it cannot render options arriving later from a remote
source. The built-in rich shape covers the common person/e-mail case; the DOM renderer covers both
local and remote options.

### Virtualize every suggestion list

Rejected for this iteration. A bounded result set is simpler, preserves the full accessible
listbox, and avoids a new dependency and runtime style behavior. Hosts should rank or page large
remote collections.

## Consequences

- Existing option objects and form submission remain compatible.
- Remote sources that should populate on opening set `minChars=0`; sources that require typed input
  keep a positive threshold.
- Avatar URLs are host data and may require an appropriate `img-src` directive. Missing or failed
  images fall back to initials.
- The Workbench demonstrates a natural Laravel reviewer field; it does not expose a renderer
  laboratory or diagnostic console.

## Sources

- https://www.w3.org/WAI/ARIA/apg/patterns/combobox/
- https://mui.com/material-ui/react-autocomplete/
- https://ant.design/components/select/
