@if ($mapView['controls']['fitBounds'] || $mapView['geolocation'] || $mapView['fullscreen'])
    <fieldset class="daisy-kit-map__menu-section">
        <legend>{{ $mapView['labels']['viewTools'] }}</legend>
        <div class="daisy-kit-map__menu-actions">
            @if ($mapView['controls']['fitBounds'])
                <button class="btn btn-ghost btn-sm" data-daisy-kit-map-fit-bounds type="button">{{ $mapView['labels']['fitBounds'] }}</button>
            @endif
            @if ($mapView['geolocation'])
                <button class="btn btn-ghost btn-sm" data-daisy-kit-map-geolocate type="button">{{ $mapView['labels']['useMyLocation'] }}</button>
            @endif
            @if ($mapView['fullscreen'])
                <button class="btn btn-ghost btn-sm" data-daisy-kit-map-fullscreen type="button" aria-pressed="false">{{ $mapView['labels']['fullscreen'] }}</button>
            @endif
        </div>
    </fieldset>
@endif
