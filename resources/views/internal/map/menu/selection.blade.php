@if (($mapView['controls']['drawing'] && $mapView['drawing']) || $mapView['spatialSelection'])
    <fieldset class="daisy-kit-map__menu-section">
        <legend>{{ $mapView['labels']['selectionTools'] }}</legend>
        <div class="daisy-kit-map__menu-actions" role="group" aria-label="{{ $mapView['labels']['selectionDetails'] }}">
            @if ($mapView['drawing'] && $mapView['drawing']['edit'])
                <button class="btn btn-ghost btn-sm" data-daisy-kit-map-mode="edit" type="button" aria-pressed="false">{{ $mapView['labels']['editDrawing'] }}</button>
            @endif
            @if ($mapView['drawing'] && $mapView['drawing']['select'])
                <button class="btn btn-ghost btn-sm" data-daisy-kit-map-mode="select" type="button" aria-pressed="false">{{ $mapView['labels']['selectDrawing'] }}</button>
            @endif
            @if ($mapView['spatialSelection'])
                <button class="btn btn-ghost btn-sm" data-daisy-kit-map-mode="feature-select" type="button" aria-pressed="false">{{ $mapView['labels']['selectFeature'] }}</button>
                @if (in_array($mapView['spatialSelection']['mode'], ['area', 'both'], true))
                    <button class="btn btn-ghost btn-sm" data-daisy-kit-map-mode="spatial-select" type="button" aria-pressed="false">{{ $mapView['labels']['selectByArea'] }}</button>
                @endif
            @endif
            @if ($mapView['drawing'] && $mapView['drawing']['delete'])
                <button class="btn btn-ghost btn-sm" data-daisy-kit-map-delete-selected type="button" disabled>{{ $mapView['labels']['deleteSelected'] }}</button>
            @endif
        </div>

        <div class="daisy-kit-map__selection bg-primary/10" data-daisy-kit-map-selection hidden aria-live="polite">
            <p data-daisy-kit-map-selection-summary></p>
            <button class="btn btn-ghost btn-sm" data-daisy-kit-map-clear-selection type="button">{{ $mapView['labels']['clearSelection'] }}</button>
        </div>
    </fieldset>
@endif
