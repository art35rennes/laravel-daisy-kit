# ADR-007: Use OpenStreetMap as the default Map basemap

## Status

Accepted — 2026-08-29

## Context

A Map without an explicitly configured provider renders Leaflet controls and application
data over an empty canvas. Although technically functional, this does not meet the product
expectation of a usable map and made the primary documentation example appear broken.

The OpenStreetMap standard raster tile service permits normal interactive browser viewing
when its HTTPS URL, visible attribution, browser referrer and cache headers are respected.
It does not provide an SLA and must not be used for bulk download, prefetch or offline use.

## Decision

`x-daisy-kit::map` uses the `osm` provider when no provider, `tileUrl` or basemap is
configured. Integrators can select another supported provider, configure `tileUrl`, or
disable all implicit network tiles with `:provider="false"`. Explicit basemaps always take
priority over the implicit default; overlays remain independently configurable.

The default provider uses `https://tile.openstreetmap.org/{z}/{x}/{y}.png` and keeps the
OpenStreetMap attribution visible. The package does not proxy, prefetch or cache tiles and
does not guarantee availability of the community service.

Automated workbenches and browser tests explicitly pass `provider: false` or use local
fixtures, so they never request OpenStreetMap tiles. Consuming hosts must allow the tile
origin in `img-src` and must not suppress the browser referrer.

## Consequences

- A minimal Map is geographically readable without additional configuration.
- Rendering a default Map makes a third-party request and shares the usual browser request
  metadata with OpenStreetMap; hosts with stricter privacy requirements must opt out.
- Production applications with availability, volume or offline requirements should choose
  a suitable provider or self-hosted tiles.
