@if ($mapView['drawing'] || $mapView['geolocation'])
    <div class="daisy-kit-map__tool-region">
        @if ($mapView['drawing'])
            <fieldset class="daisy-kit-map__tools" data-daisy-kit-map-tools>
                <legend class="daisy-kit-map__tools-title">{{ $mapView['labels']['drawingTools'] }}</legend>

                <div class="daisy-kit-map__tool-group" role="group" aria-label="{{ $mapView['labels']['drawingTools'] }}">
                    @if ($mapView['drawing']['point'])
                        <button class="btn btn-outline btn-sm" data-daisy-kit-map-mode="point" type="button" aria-pressed="false">{{ $mapView['labels']['drawPoint'] }}</button>
                    @endif
                    @if ($mapView['drawing']['line'])
                        <button class="btn btn-outline btn-sm" data-daisy-kit-map-mode="linestring" type="button" aria-pressed="false">{{ $mapView['labels']['drawLine'] }}</button>
                    @endif
                    @if ($mapView['drawing']['polygon'])
                        <button class="btn btn-outline btn-sm" data-daisy-kit-map-mode="polygon" type="button" aria-pressed="false">{{ $mapView['labels']['drawArea'] }}</button>
                    @endif
                    @if ($mapView['drawing']['rectangle'])
                        <button class="btn btn-outline btn-sm" data-daisy-kit-map-mode="rectangle" type="button" aria-pressed="false">{{ $mapView['labels']['drawRectangle'] }}</button>
                    @endif
                </div>

                <div class="daisy-kit-map__tool-group" role="group" aria-label="{{ $mapView['labels']['selectionDetails'] }}">
                    @if ($mapView['drawing']['edit'])
                        <button class="btn btn-ghost btn-sm" data-daisy-kit-map-mode="edit" type="button" aria-pressed="false">{{ $mapView['labels']['editDrawing'] }}</button>
                    @endif
                    @if ($mapView['drawing']['select'])
                        <button class="btn btn-ghost btn-sm" data-daisy-kit-map-mode="select" type="button" aria-pressed="false">{{ $mapView['labels']['selectDrawing'] }}</button>
                    @endif
                    @if ($mapView['drawing']['delete'])
                        <button class="btn btn-ghost btn-sm" data-daisy-kit-map-delete-selected type="button" disabled>{{ $mapView['labels']['deleteSelected'] }}</button>
                    @endif
                </div>

                <div class="daisy-kit-map__tool-group" role="group" aria-label="{{ $mapView['labels']['activeMode'] }}">
                    <button class="btn btn-ghost btn-sm" data-daisy-kit-map-history="undo" disabled type="button">{{ $mapView['labels']['undo'] }}</button>
                    <button class="btn btn-ghost btn-sm" data-daisy-kit-map-history="redo" disabled type="button">{{ $mapView['labels']['redo'] }}</button>
                    <button class="btn btn-ghost btn-sm" data-daisy-kit-map-export disabled type="button">{{ $mapView['labels']['exportDrawing'] }}</button>
                </div>
            </fieldset>
        @endif
    </div>
@endif
