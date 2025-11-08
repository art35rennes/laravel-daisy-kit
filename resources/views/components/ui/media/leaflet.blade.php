@props([
    // Identifiant optionnel du conteneur de carte (auto-généré si null)
    'id' => null,
    // Style
    'class' => '',                 // classes utilitaires (DaisyUI/Tailwind)
    'style' => '',                 // styles inline additionnels
    // Vue initiale
    'lat' => 48.117266,            // latitude par défaut (Rennes)
    'lng' => -1.6777926,           // longitude par défaut (Rennes)
    'zoom' => 12,
    'minZoom' => null,
    'maxZoom' => null,
    // Fond de carte (leaflet-providers)
    'provider' => 'OpenStreetMap.Mapnik',
    'tileUrl' => null,             // alternative directe à provider (URL gabarit)
    'tileOptions' => [],           // options passées à L.tileLayer (attribution, subdomains, etc.)
    // Performances
    'preferCanvas' => false,       // rendu Canvas
    // Essentiels
    'gestureHandling' => true,
    'locateControl' => false,
    'fullscreen' => true,
    'hash' => false,
    'scale' => true,               // true|false ou tableau d'options { position, metric, imperial }
    // Données/visualisation
    'cluster' => false,            // active marker clustering
    'clusterOptions' => [],
    'heatmap' => false,            // { points: [[lat,lng,intensity], ...], options: {...} }
    'miniMap' => false,            // true|{ provider: string, tileUrl: string, options: {...} }
    // Édition/mesures
    'draw' => false,               // true|{ options... }
    'measure' => false,            // true|{ options... }
    // Recherche/géocodage/itinéraires
    'geocoder' => false,           // true|'osm'|'esri'|{ provider: 'osm'|'esri', options: {...} }
    'routing' => false,            // true|{ service: 'osrm'|'graphhopper'|'mapbox', serviceUrl, apiKey, options }
    // Données simples
    'markers' => [],               // [[lat, lng, popupHtml?], ...] ou [{lat, lng, popup, icon, options}]
    'geojson' => null,             // objet/array GeoJSON ou JSON string
    // Surcharge du nom de module JS (optionnel)
    'module' => null,
])

{{--
  Composant Leaflet (Daisy Kit)

  Objectif
  - Fournir un conteneur de carte Leaflet prêt à l'emploi avec activation optionnelle des plugins
    fréquents (providers, gesture-handling, locate, fullscreen, hash, scale, clustering, heatmap,
    minimap, draw, measure, geocoder, routing).

  Remarques importantes
  - Aucune dépendance NPM n'est installée automatiquement ici. Le code JS détecte les plugins
    via window.L et applique un dégradé silencieux si absent. Vous pourrez installer les paquets
    puis activer leurs fonctionnalités.
  - Le CSS de Leaflet est requis pour un rendu correct. À ajouter plus tard via NPM:
      import 'leaflet/dist/leaflet.css';
    ou via CDN si besoin temporaire.
  - Le composant émet un <script data-config> JSON lu par le module JS DaisyLeaflet.
  - La carte s'adapte automatiquement à la taille de son parent.

  Exemple d'usage minimal
  <x-daisy::ui.media.leaflet class="rounded-box shadow" :zoom="13" :lat="48.11" :lng="-1.67" />

  Activation de plugins
  <x-daisy::ui.media.leaflet :gestureHandling="true" :fullscreen="true" :hash="true" :scale="true"
                      :locateControl="true" :cluster="true" :geocoder="'osm'" />

  Données
  <x-daisy::ui.media.leaflet :markers="[[48.11,-1.67,'<b>Centre</b>']]" :geojson="$monGeojson" />
--}}

@php
    $mapId = $id ?: 'leaflet-'.\Illuminate\Support\Str::uuid()->toString();
    $rootClasses = trim('relative w-full h-full bg-base-200 '.$class);

    // Normalisation des options simples
    $scaleOptions = is_array($scale) ? $scale : ($scale ? ['metric' => true, 'imperial' => true] : []);
    $miniMapConfig = is_array($miniMap) ? $miniMap : ($miniMap ? ['provider' => $provider] : []);
    $geocoderConfig = is_array($geocoder) ? $geocoder : ($geocoder ? ['provider' => (is_string($geocoder) ? $geocoder : 'osm')] : []);
    $routingConfig = is_array($routing) ? $routing : ($routing ? ['service' => 'osrm'] : []);

    $config = [
        'containerId' => $mapId,
        'center' => ['lat' => (float) $lat, 'lng' => (float) $lng],
        'zoom' => (int) $zoom,
        'minZoom' => $minZoom !== null ? (int) $minZoom : null,
        'maxZoom' => $maxZoom !== null ? (int) $maxZoom : null,
        'preferCanvas' => (bool) $preferCanvas,
        'tiles' => [
            'provider' => $provider,
            'url' => $tileUrl,
            'options' => (object) $tileOptions,
        ],
        'plugins' => [
            'gestureHandling' => (bool) $gestureHandling,
            'locateControl' => (bool) $locateControl,
            'fullscreen' => (bool) $fullscreen,
            'hash' => (bool) $hash,
            'scale' => $scaleOptions,
            'cluster' => (bool) $cluster,
            'clusterOptions' => (object) $clusterOptions,
            'heatmap' => $heatmap,
            'miniMap' => $miniMapConfig,
            'draw' => $draw,
            'measure' => $measure,
            'geocoder' => $geocoderConfig,
            'routing' => $routingConfig,
        ],
        'data' => [
            'markers' => $markers,
            'geojson' => $geojson,
        ],
    ];
@endphp

<div {{ $attributes->merge(['class' => $rootClasses, 'data-module' => ($module ?? 'leaflet'), 'data-leaflet' => '1', 'style' => $style]) }}>
    <div id="{{ $mapId }}" class="w-full h-full"></div>
    <script type="application/json" data-config>@json($config)</script>
    {{ $slot }}
    {{-- Les barres d'outils/options personnalisées peuvent être passées via le slot --}}
</div>


