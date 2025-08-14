@props([
    // Identifiant optionnel du conteneur de carte (auto-généré si null)
    'id' => null,
    // Dimensions et style
    'class' => '',                 // classes utilitaires (DaisyUI/Tailwind)
    'style' => '',                 // styles inline additionnels
    'height' => '400px',           // hauteur du conteneur carte (fallback si pas d'aspect)
    // Responsive
    'responsive' => true,
    // Ratio CSS: null | 'auto' (adapts 1:1, 4:3, 9:16) | explicit ex. '16/9'
    'aspect' => null,
    // Hauteur minimale utile lorsque l'aspect réduit trop la hauteur (petits écrans)
    'minHeight' => '240px',
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

  Exemple d'usage minimal
  <x-daisy::ui.leaflet class="rounded-box shadow" height="480px" :zoom="13" :lat="48.11" :lng="-1.67" />

  Activation de plugins
  <x-daisy::ui.leaflet :gestureHandling="true" :fullscreen="true" :hash="true" :scale="true"
                      :locateControl="true" :cluster="true" :geocoder="'osm'" />

  Données
  <x-daisy::ui.leaflet :markers="[[48.11,-1.67,'<b>Centre</b>']]" :geojson="$monGeojson" />
--}}

@php
    $mapId = $id ?: 'leaflet-'.\Illuminate\Support\Str::uuid()->toString();
    $rootClasses = trim('relative w-full bg-base-200 '.$class);
    $useAdaptive = $responsive && is_string($aspect) && strtolower($aspect) === 'auto';
    $hasAspect = $responsive && (!$useAdaptive) && !empty($aspect);
    $rootStyle = '';
    if ($hasAspect) {
        $rootStyle = 'aspect-ratio: '.$aspect.'; min-height: '.$minHeight.';';
    }
    $mapStyle = $hasAspect
        ? (($style ? $style : ''))
        : ('height: '.$height.';'.($style ? ' '.$style : ''));

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

<div {{ $attributes->merge(['class' => $rootClasses, 'data-leaflet' => '1', 'style' => $rootStyle]) }} data-responsive="{{ $responsive ? '1' : '0' }}" data-aspect="{{ $aspect ?? '' }}" data-minh="{{ $minHeight }}" data-adaptive="{{ $useAdaptive ? '1' : '0' }}">
    <div id="{{ $mapId }}" class="w-full {{ ($hasAspect || $useAdaptive) ? 'h-full absolute inset-0' : 'h-full' }}" style="{{ $mapStyle }}"></div>
    <script type="application/json" data-config>@json($config)</script>
    @if($useAdaptive)
    <script>
    (function(){
      try {
        var root = document.currentScript && document.currentScript.parentElement ? document.currentScript.parentElement : null;
        if (!root) return;
        var minH = root.getAttribute('data-minh') || '240px';
        var asq = '1/1';
        var asl = '4/3';
        var asp = '9/16';
        function chooseRatio(width){
          try {
            var vw = window.innerWidth || width;
            // Heuristique simple: très étroit => portrait, étroit => carré, sinon paysage
            if (width <= 360) return asp;       // 9:16
            if (width <= 520) return asq;       // 1:1
            return asl;                         // 4:3
          } catch(_) { return asl; }
        }
        function apply(){
          var rect = root.getBoundingClientRect();
          var ratio = chooseRatio(rect.width || 0);
          root.style.aspectRatio = ratio;
          root.style.minHeight = minH;
        }
        var ro;
        try { ro = new ResizeObserver(apply); ro.observe(root); } catch(_) { window.addEventListener('resize', apply); }
        apply();
      } catch(_) {}
    })();
    </script>
    @endif
    {{ $slot }}
    {{-- Les barres d'outils/options personnalisées peuvent être passées via le slot --}}
</div>


