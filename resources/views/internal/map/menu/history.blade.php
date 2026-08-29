@if ($mapView['controls']['drawing'] && $mapView['drawing'])
    <fieldset class="daisy-kit-map__menu-section">
        <legend>{{ $mapView['labels']['historyTools'] }}</legend>
        <div class="daisy-kit-map__menu-actions" role="group" aria-label="{{ $mapView['labels']['historyTools'] }}">
            <button class="btn btn-ghost btn-sm" data-daisy-kit-map-history="undo" disabled type="button">{{ $mapView['labels']['undo'] }}</button>
            <button class="btn btn-ghost btn-sm" data-daisy-kit-map-history="redo" disabled type="button">{{ $mapView['labels']['redo'] }}</button>
            <button class="btn btn-ghost btn-sm" data-daisy-kit-map-export disabled type="button">{{ $mapView['labels']['exportDrawing'] }}</button>
        </div>
    </fieldset>
@endif
