# Map

`x-daisy-kit::map` is the only public Map component. It combines Leaflet for rendering,
Terra Draw for editing and Turf for measurements and spatial selection. Its UI uses DaisyUI
semantic classes and theme tokens; the host remains responsible for compiling DaisyUI and
importing `@daisy-kit/map.css`.

## Configuration

The canonical props are grouped by outcome:

- view: `center`, `zoom`, `minZoom`, `maxZoom`, `fitBounds`, `preferCanvas`, `label`;
- sources: `geojson`, `markers`, `basemaps`, `layers`;
- tiles: `provider`, `tileUrl`, `tileAttribution`, `tileOptions`;
- controls: `controls`, `scale`, `fullscreen`, `gestureHandling`, `geolocation`, `cluster`;
- editing: `drawing`, `measure`, `objectTypes`, `drawLayers`, `spatialSelection`, `name`, `value`;
- state: `persistState`, `stateKey`.

Overlays use one `layers` shape. `type` is `geojson`, `xyz`, or `wms`; every layer has a
unique `id` and accepts `label`, `data|url`, `options`, `style`, `visible`, `controllable`,
`selectable`, and `editable`. Basemaps use the same shape with `xyz` or `wms`, plus `selected`.

```blade
<x-daisy-kit::map
    id="asset-map"
    label="Assets and service districts"
    :center="[48.1173, -1.6778]"
    :zoom="12"
    :markers="[
        ['id' => 'office', 'label' => 'Office', 'position' => [48.1173, -1.6778],
         'popup' => ['renderer' => 'text', 'content' => 'Operations office']],
    ]"
    :cluster="['maxClusterRadius' => 60]"
    :basemaps="[
        ['id' => 'local', 'label' => 'Local tiles', 'type' => 'xyz',
         'url' => '/maps/tiles/{z}/{x}/{y}.png', 'selected' => true],
    ]"
    :layers="[
        ['id' => 'districts', 'label' => 'Districts', 'type' => 'geojson',
         'url' => '/maps/districts.geojson', 'selectable' => true],
        ['id' => 'cadastre', 'label' => 'Cadastre', 'type' => 'wms',
         'url' => config('services.maps.wms_url'),
         'options' => ['layers' => 'parcels', 'format' => 'image/png', 'transparent' => true]],
    ]"
    :drawing="true"
    :measure="true"
    :spatial-selection="['mode' => 'both']"
    :object-types="[
        ['id' => 'hydrant', 'label' => 'Hydrant', 'geometry' => 'point'],
        ['id' => 'pipe', 'label' => 'Pipe', 'geometry' => 'line'],
    ]"
    :draw-layers="[['id' => 'water', 'label' => 'Water network']]"
    name="geometry"
    :persist-state="true"
    state-key="project-assets"
/>
```

`spatialSelection.mode` is `click`, `area`, or `both`. Drawing changes keep the hidden
`name` input synchronized as a GeoJSON FeatureCollection and dispatch native `input` and
`change` events. The `controls`, `empty`, and `error` slots let a host compose its own controls
and local states without exposing additional Blade components.

## Popup and URL safety

Popup strings and `renderer: text` are inserted as text. `renderer: blade` renders a named
server-side view; `renderer: trusted-html` is an explicit trust decision by the integrator.
Marker images, tiles and remote sources accept same-origin absolute paths or HTTPS URLs.
Dangerous schemes are rejected by Blade normalization, and dynamic marker updates ignore
invalid coordinates and image URLs.

## JavaScript facade

`mount(root)` returns the stable instance facade. `getInstance(root)` retrieves an already
mounted instance; `mountAll(scope)` and `unmount(root)` retain the common module lifecycle.

| Area | Methods |
| --- | --- |
| View/state | `getState()`, `setView(center, zoom, options)`, `fitBounds(options)`, `invalidateSize()` |
| Data | `setGeoJSON(data)`, `setMarkers(markers)`, `setLayerData(id, data)`, `refreshLayer(id)` |
| Layers | `setBasemap(id)`, `setLayerVisibility(id, visible)` |
| Editing | `setMode(mode, options)`, `getDrawLayer()`, `setDrawLayer(id)`, `getSelection()`, `clearSelection()`, `deleteSelected()`, `exportGeoJSON()`, `undo()`, `redo()` |
| Geolocation | `locate(options)`, `startGeolocation(options)`, `stopGeolocation()` |
| Extension/lifecycle | `getLeafletMap()`, `destroy()` |

`getLeafletMap()` is the sole Leaflet escape hatch for an integrator-owned plugin. The host
owns that plugin's cleanup and must not mutate Daisy Kit's internal layer collections.

```js
import { getInstance, mountAll } from '@daisy-kit/map.js';
import '@daisy-kit/map.css';

mountAll();

const root = document.querySelector('#asset-map');
const map = getInstance(root);

document.querySelector('#focus-assets').addEventListener('click', () => {
    map.setView([48.1173, -1.6778], 14);
});
```

## Events

Events are `CustomEvent`s named `daisy-kit:map:*`. Payloads contain serializable snapshots,
not private runtime objects. Supported suffixes are:

- lifecycle/state: `mounted`, `unmounted`, `ready`, `empty`, `error`, `view`, `tools`;
- sources: `marker`, `markers`, `data`, `layer`, `layer-data`, `layer-refresh`, `layer-error`, `basemap`;
- editing: `mode`, `geometry`, `geometry-finish`, `selection`, `spatial-selection`, `measurement`, `history`, `export`;
- geolocation: `geolocation`, `geolocation-start`, `geolocation-stop`, `geolocation-error`.

Listen on the component root so multiple maps remain isolated.

## Network and CSP

OpenStreetMap is the default basemap when no provider, `tileUrl` or basemap is configured.
Disable implicit network tiles with `:provider="false"`, or select a provider suited to the
host's availability, privacy and volume requirements.
The default keeps the required attribution visible and follows the
[OpenStreetMap tile usage policy](https://operations.osmfoundation.org/policies/tiles/); it must
not be used for prefetch, bulk download or offline maps. Automated fixtures explicitly disable
the provider and use deterministic local tiles and GeoJSON.

The module supports `script-src 'self'`, `script-src-attr 'none'`, `style-src 'self'`, and
`style-src-attr 'none'`; Leaflet's CSSOM updates do not require executable inline markup. Add tile
origins to `img-src` and remote GeoJSON origins to `connect-src`. Local marker images also require
`img-src 'self' data: blob:`. The component emits only non-executable JSON configuration.

## Migration from v4 and early v5 alphas

- Keep the v4 product outcomes, but rename the public component to `x-daisy-kit::map` and import
  the explicit `map.js`/`map.css` entries.
- Move every old or alpha `wms` entry into `layers` with `type: 'wms'`; the `wms` prop is removed.
- Replace `window.L`, `window.DaisyMap`, and legacy aliases with the facade, namespaced events, or
  the documented `getLeafletMap()` escape hatch.
- Replace executable callbacks in Blade data with serializable configuration and root event
  listeners. Trusted popup HTML must now be explicit.
- Do not expect heatmaps, mini-maps, geocoding, or routing: they were not dependable v4 outcomes
  and are intentionally outside the v5 contract.
