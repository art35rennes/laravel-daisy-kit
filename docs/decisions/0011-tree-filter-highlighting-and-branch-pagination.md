# ADR-0011: Extend Tree filtering and paginate lazy branches

## Status

Accepted

## Date

2026-09-02

## Context

Tree can search locally or through an application endpoint and can lazily load a branch. Its first
lazy contract assumes that one response contains every immediate child. That is unsafe for branches
with hundreds or thousands of children. Integrators also need to apply application-specific local
criteria without replacing Tree's selection, hierarchy and keyboard behavior, and standard search
needs an optional visible indication of the matched characters.

The restored Workbench catalogue exposed two related problems: its endpoint deliberately failed the
first request, and "Select loaded results" ignored a visible, completely loaded branch in
`selected-roots` mode when its leaves were collapsed.

## Decision

- Add an optional `highlightMatches` Blade configuration. Standard-rendered labels and descriptions
  use semantic `<mark>` elements for the characters matched by the applied search. Custom node views
  remain fully owned by the host and are not rewritten.
- Add `setFilter(predicate)` and `clearFilter()` to the facade. The predicate receives an immutable,
  detached node snapshot and is evaluated only against nodes already loaded in the browser. Matching
  nodes retain their ancestor paths. It composes with standard search and emits a serializable
  `filtered` event; functions never enter JSON configuration or shared state.
- Extend lazy endpoint responses additively with `nextCursor`. A branch renders the returned page and
  exposes an accessible "Load more" action while a cursor remains. `loadMore(id)` fetches only that
  branch with its cursor and appends the page. Existing `{ items: [...] }` responses remain complete
  one-page responses.
- "Select loaded results" selects the deepest visible complete results. In `selected-roots` mode a
  visible complete branch with no visible descendants represents its fully loaded subtree. It never
  selects a partially loaded branch or fetches more pages.
- The Workbench uses successful, paginated local endpoints. Operational failure/retry remains covered
  by runtime and browser fixtures, not by a failure forced during normal page opening.

## Alternatives considered

### Render thousands of immediate children

Rejected because DOM creation and repeated full-tree rendering grow linearly with an unbounded server
response and make selection/search updates unnecessarily expensive.

### Add TanStack Virtual

Rejected for this iteration. Virtualizing a hierarchical, variably sized, keyboard-navigated tree
adds significant focus and accessibility complexity and would reintroduce DOM style writes. Server
pagination bounds both transfer and rendered DOM without another dependency.

### Serialize a custom filter callback in Blade configuration

Rejected because executable configuration violates the CSP-safe JSON boundary. The callback belongs
to the host's explicit JavaScript integration through the mounted facade.

## Consequences

- Applications with large branches must paginate their endpoints and return stable cursors.
- `loadMore` is explicit, so expanding a branch does not silently fetch every page.
- Custom local filters operate only on loaded data; server-side filtering continues to use
  `searchSource` or application-owned lazy endpoints.
- Rendering remains strict-CSP compatible and no new dependency is introduced.
