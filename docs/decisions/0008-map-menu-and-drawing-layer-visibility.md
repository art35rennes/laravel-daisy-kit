# ADR-008: Structure Map tools and drawing-layer visibility

## Status

Accepted — 2026-08-29

## Context

The restored Map placed basemap and overlay controls in a map menu, but rendered drawing,
selection and history tools below the canvas while view actions floated beside the menu.
This fragmented the workflow, consumed vertical space and left integrators unable to order
the menu around their product terminology.

`drawLayers` also only tagged newly created features. Selecting a drawing layer did not
control which related features were visible, so the concept did not behave as an actual
business layer.

The prerelease provider vocabulary exposed CARTO implementation names alongside OSM. The
product contract instead needs a single OpenStreetMap-derived basemap family with explicit
visual modes and no compatibility aliases.

## Decision

All Map actions except Leaflet's native zoom control live inside one compact menu. Its
default sections are `basemaps`, `businessLayers`, `drawingLayers`, `create`, `selection`,
`history`, `view` and `custom`. Integrators can order or omit these sections through
`controls.sections`; the existing `controls` slot renders inside the `custom` section.

`drawLayerSelection` accepts only `single` or `multiple`:

- `single` keeps exactly one drawing layer visible;
- `multiple` keeps one or more drawing layers visible and allows them to accumulate.

Each drawing-layer item may declare initial `visible` state. Selecting the destination for
a new drawing also makes that layer visible. The facade adds `getVisibleDrawLayers()` and
`setVisibleDrawLayers(ids)`, and changes emit `daisy-kit:map:draw-layers` with a serializable
snapshot. Hidden features remain in the synchronized and exported GeoJSON value.

The only provider identifiers are `osm.standard`, `osm.light`, `osm.dark` and
`osm.voyager`. All modes use OpenStreetMap data and retain the attribution required by the
actual tile provider. No legacy provider aliases are accepted.

## Consequences

- The canvas keeps more vertical space for geography and presents one predictable control
  entry point on desktop and mobile.
- Drawing-layer visibility is a view concern and never deletes or excludes hidden features
  from form synchronization or export.
- Hosts can compose product-specific controls in the menu without gaining access to private
  Blade primitives.
- Early v5 provider identifiers such as `osm` and `cartodb.positron` are intentionally
  rejected during the prerelease correction.

