# ADR-006: Restore the Map v5 product contract

## Status

Accepted — 2026-08-29

## Context

The first v5 Map retained Leaflet, Terra Draw and explicit ESM mounting, but reduced the
v4 product contract to a small inline-data demo. It did not expose a stable instance API,
typed overlay sources, clustering, gesture handling, complete drawing workflows or form
synchronisation. The rendered toolbar was difficult to scan and a resize race could pass
non-finite coordinates to Leaflet.

The v4 Map is the reference for user outcomes, not for architecture. Heatmaps, mini-maps,
geocoding and routes were never dependable v4 product capabilities and are not restored.

## Decision

`x-daisy-kit::map`, `@daisy-kit/map.js` and `map.css` remain the only public Map entries.
The canonical configuration uses `center`, `zoom`, typed `layers`, `basemaps`, `markers`,
controls, drawing, measurements, geolocation and local persistence. The alpha-only `wms`
property is replaced by a `wms` item in `layers`; no v4 alias, global Leaflet object or
compatibility dialect is introduced.

`mount(root)` returns a stable facade and `getInstance(root)` retrieves it. The facade
exposes view, data, layer, drawing, selection, geolocation and lifecycle operations. Its
only Leaflet escape hatch is `getLeafletMap()`. Public events use the
`daisy-kit:map:*` namespace and contain stable data snapshots rather than runtime context.

Blade normalises and validates configuration before serialising non-executable JSON.
Same-origin paths and HTTPS URLs are accepted; unsafe schemes are rejected. Popup text is
safe by default, while Blade-rendered or trusted HTML content requires an explicit
renderer. The runtime is split into internal modules and dynamically loads Terra Draw,
Turf, marker clustering and gesture handling only when configured.

The UI uses DaisyUI semantic classes and theme tokens. It provides named regions,
keyboard instructions, live feedback, touch-sized controls, a contextual drawing toolbar,
and error retry. Resize invalidation only occurs for finite, positive canvas dimensions.

## Consequences

- Existing alpha Map props and events may break during the v5.1 prerelease correction.
- Integrators can connect external filters and controls through the facade without taking
  ownership of internal state.
- OpenStreetMap is the default basemap (see ADR-007), while every provider remains
  replaceable or explicitly disabled. Automated tests use local, deterministic sources.
- `leaflet.markercluster` and `leaflet-gesture-handling` are runtime dependencies. Both are
  pinned by the lockfile to their MIT-licensed npm releases and loaded only for maps that
  request them.

## Alternatives rejected

- Copying the v4 global API: conflicts with instance isolation and ESM ownership.
- Exposing internal Blade primitives: would widen the public component surface.
- Restoring theoretical v4 plugins: would document outcomes the package cannot prove.
- Initialising Leaflet during zero-sized layout transitions: reproduces the observed
  `Invalid LatLng (NaN, NaN)` failure.
