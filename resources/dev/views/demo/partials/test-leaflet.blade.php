<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Leaflet Maps</h2>

    <div class="grid md:grid-cols-2 gap-6">
        <div class="space-y-2">
            <div class="label"><span class="label-text">Basique · OSM provider par défaut</span></div>
            <x-daisy::ui.leaflet
                class="rounded-box shadow"
                aspect="16/9"
                minHeight="220px"
                :lat="48.117"
                :lng="-1.678"
                :zoom="12"
            />
        </div>

        <div class="space-y-2">
            <div class="label"><span class="label-text">Avec plugins · gestures, fullscreen, hash, scale, locate</span></div>
            <div class="grid gap-2">
                <div class="text-xs opacity-70">Disposition des inputs: 1 colonne en mobile → 2 colonnes ≥ sm</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <label class="form-control">
                        <div class="label"><span class="label-text">Hash</span></div>
                        <input id="lfHash" type="text" class="input input-bordered input-sm" readonly>
                    </label>
                    <label class="form-control">
                        <div class="label"><span class="label-text">Centre (lat,lng)</span></div>
                        <input id="lfCenter" type="text" class="input input-bordered input-sm" readonly>
                    </label>
                    <label class="form-control">
                        <div class="label"><span class="label-text">Zoom</span></div>
                        <input id="lfZoom" type="text" class="input input-bordered input-sm" readonly>
                    </label>
                    <label class="form-control sm:col-span-2">
                        <div class="label"><span class="label-text">Pointeur (lat,lng)</span></div>
                        <input id="lfPointer" type="text" class="input input-bordered input-sm" readonly>
                    </label>
                </div>
                <x-daisy::ui.leaflet
                    id="lfDemo2"
                    class="rounded-box shadow"
                    aspect="4/3"
                    minHeight="220px"
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

        <div class="space-y-2 md:col-span-2">
            <div class="label"><span class="label-text">Ratio adaptatif (auto) · 9:16 → 1:1 → 4:3 selon la largeur</span></div>
            <x-daisy::ui.leaflet
                class="rounded-box shadow"
                aspect="auto"
                minHeight="220px"
                :lat="48.117"
                :lng="-1.678"
                :zoom="12"
            />
        </div>

        <div class="space-y-2 md:col-span-2">
            <div class="label"><span class="label-text">Cluster + markers (fallback en simple markers si plugin absent)</span></div>
            <x-daisy::ui.leaflet
                class="rounded-box shadow"
                aspect="16/10"
                minHeight="240px"
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

    {{-- Exemples supplémentaires (commentés, à activer après installation des plugins correspondants) --}}
    {{--
    <div class="space-y-2">
        <div class="label"><span class="label-text">Heatmap (nécessite leaflet.heat)</span></div>
        <x-daisy::ui.leaflet
            height="280px"
            :heatmap="['points' => [[48.117,-1.678,0.6],[48.112,-1.68,0.8],[48.12,-1.675,0.4]], 'options' => ['radius' => 25]]"
        />
    </div>
    --}}
</section>


