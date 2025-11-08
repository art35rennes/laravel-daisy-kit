<section class="space-y-6 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Leaflet Maps</h2>

    <div class="space-y-6">
        <!-- Carte basique -->
        <div class="space-y-3">
            <h3 class="text-lg font-medium">Basique · OSM provider par défaut</h3>
            <div style="height: 300px;">
                <x-daisy::ui.media.leaflet
                    class="rounded-box shadow"
                    :lat="48.117"
                    :lng="-1.678"
                    :zoom="12"
                />
            </div>
        </div>

        <!-- Carte avec plugins -->
        <div class="space-y-3">
            <h3 class="text-lg font-medium">Avec plugins · gestures, fullscreen, hash, scale, locate</h3>
            
            <!-- Inputs de monitoring -->
            <div class="bg-base-100 p-4 rounded-box space-y-3">
                <div class="text-xs opacity-70">Monitoring de la carte (mise à jour en temps réel)</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="form-control">
                        <x-daisy::ui.advanced.label class="text-xs">Hash</x-daisy::ui.advanced.label>
                        <input id="lfHash" type="text" class="input input-bordered input-xs" readonly>
                    </div>
                    <div class="form-control">
                        <x-daisy::ui.advanced.label class="text-xs">Centre (lat,lng)</x-daisy::ui.advanced.label>
                        <input id="lfCenter" type="text" class="input input-bordered input-xs" readonly>
                    </div>
                    <div class="form-control">
                        <x-daisy::ui.advanced.label class="text-xs">Zoom</x-daisy::ui.advanced.label>
                        <input id="lfZoom" type="text" class="input input-bordered input-xs" readonly>
                    </div>
                    <div class="form-control">
                        <x-daisy::ui.advanced.label class="text-xs">Pointeur (lat,lng)</x-daisy::ui.advanced.label>
                        <input id="lfPointer" type="text" class="input input-bordered input-xs" readonly>
                    </div>
                </div>
            </div>

            <!-- Carte -->
            <div style="height: 350px;">
                <x-daisy::ui.media.leaflet
                    id="lfDemo2"
                    class="rounded-box shadow"
                    :lat="48.117"
                    :lng="-1.678"
                    :zoom="13"
                    :gestureHandling="true"
                    :fullscreen="true"
                    :hash="true"
                    :scale="true"
                    :locateControl="true"
                    :markers="[[48.112,-1.68,'<b>Point d\'intérêt</b>']]"
                />
            </div>
            
            <script>
            (function(){
              document.addEventListener('DOMContentLoaded', () => {
                const root = document.querySelector('#lfDemo2')?.closest('[data-leaflet="1"]');
                if (!root) return;
                const $hash = document.getElementById('lfHash');
                const $center = document.getElementById('lfCenter');
                const $zoom = document.getElementById('lfZoom');
                const $pointer = document.getElementById('lfPointer');
                function fmtLatLng(latlng){ if(!latlng) return ''; return latlng.lat.toFixed(5)+', '+latlng.lng.toFixed(5); }
                function readHash(){ try { return location.hash || ''; } catch(_) { return ''; } }
                root.addEventListener('daisy:leaflet:init', (e) => {
                  try {
                    const map = e.detail?.map; if (!map) return;
                    const updateView = () => {
                      try {
                        if ($center) $center.value = fmtLatLng(map.getCenter());
                        if ($zoom) $zoom.value = String(map.getZoom());
                        if ($hash) $hash.value = readHash();
                      } catch(_) {}
                    };
                    updateView();
                    map.on('moveend zoomend', updateView);
                    window.addEventListener('hashchange', () => { if ($hash) $hash.value = readHash(); });
                    map.on('mousemove', (ev) => { if ($pointer) $pointer.value = fmtLatLng(ev.latlng); });
                  } catch(_) {}
                }, { once: true });
              });
            })();
            </script>
        </div>

        <!-- Carte responsive -->
        <div class="space-y-3">
            <h3 class="text-lg font-medium">Responsive · Hauteur adaptative selon l'écran</h3>
            <div class="h-64 sm:h-80 md:h-96">
                <x-daisy::ui.media.leaflet
                    class="rounded-box shadow"
                    :lat="48.117"
                    :lng="-1.678"
                    :zoom="12"
                />
            </div>
        </div>

        <!-- Carte avec cluster -->
        <div class="space-y-3">
            <h3 class="text-lg font-medium">Cluster + markers (fallback en simple markers si plugin absent)</h3>
            <div style="height: 400px;">
                <x-daisy::ui.media.leaflet
                    class="rounded-box shadow"
                    :lat="48.11"
                    :lng="-1.68"
                    :zoom="12"
                    :cluster="true"
                    :clusterOptions="['disableClusteringAtZoom' => 15]"
                    :markers="[
                        [48.116,-1.675,'<b>Centre</b>'],
                        [48.121,-1.682,'<b>Spot 1</b>'],
                        [48.108,-1.669,'<b>Spot 2</b>'],
                        [48.111,-1.685,'<b>Spot 3</b>'],
                        [48.12,-1.672,'<b>Spot 4</b>']
                    ]"
                />
            </div>
        </div>
    </div>

    {{-- Exemples supplémentaires (commentés, à activer après installation des plugins correspondants) --}}
    {{--
    <div class="space-y-2">
        <div class="label"><span class="label-text">Heatmap (nécessite leaflet.heat)</span></div>
        <div class="h-64 min-h-[300px]">
            <x-daisy::ui.media.leaflet
                class="rounded-box shadow"
                :heatmap="['points' => [[48.117,-1.678,0.6],[48.112,-1.68,0.8],[48.12,-1.675,0.4]], 'options' => ['radius' => 25]]"
            />
        </div>
    </div>
    --}}
</section>


