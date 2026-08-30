@if ($controlsSlot)
    <section class="daisy-kit-map__menu-section daisy-kit-map__host-controls" data-daisy-kit-map-host-controls>
        <h3>{{ $mapView['labels']['customControls'] }}</h3>
        {{ $controlsSlot }}
    </section>
@endif
