# ADR-0012: Optional Copyable icon and transient visual feedback

## Status

Accepted

## Date

2026-09-02

## Context

Copyable already announces clipboard success and failure through an accessible live region, but a
sighted user receives no immediate visual confirmation. Dense technical interfaces also benefit
from a familiar copy glyph, while text-first controls should not be forced to display one.

## Decision

Copyable adds `showIcon=false` and `showFeedback=true` Blade props. The icon is a package-owned,
decorative SVG rendered beside the existing visible content; it never replaces that content or the
button's accessible name.

The existing live region becomes a transient, tooltip-shaped visual status when `showFeedback` is
enabled. It displays `successLabel` after a successful copy and `errorLabel` after an operational
failure, then hides after `feedbackDuration`. It remains the single live announcement so assistive
technology does not hear duplicate feedback. With `showFeedback=false`, the region remains
visually hidden but continues to announce both outcomes.

Both capabilities are CSP-safe, require no global state or additional dependency, and do not alter
the `copy()` / `getValue()` facade or event payloads.

## Consequences

- Integrators can opt into a conventional copy affordance without supplying arbitrary HTML.
- Successful and failed actions have immediate visual and accessible feedback by default.
- Existing integrations keep their text content, facade, events and clipboard-only behavior.
- Unmount restores the original status element and clears pending feedback timers.
