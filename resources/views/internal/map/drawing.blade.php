@if ($mapView['drawing'] || $mapView['spatialSelection'])
    <div class="daisy-kit-map__tool-region">
        <fieldset class="daisy-kit-map__tools" data-daisy-kit-map-tools>
            <legend class="daisy-kit-map__tools-title">{{ $mapView['labels']['drawingTools'] }}</legend>

            @if ($mapView['objectTypes'] || $mapView['drawLayers'])
                <div class="daisy-kit-map__tool-settings">
                    @if ($mapView['objectTypes'])
                        <label class="form-control">
                            <span class="label">{{ $mapView['labels']['objectType'] }}</span>
                            <select class="select select-bordered select-sm" data-daisy-kit-map-object-type>
                                @foreach ($mapView['objectTypes'] as $type)
                                    <option value="{{ $type['id'] }}">{{ $type['label'] }}</option>
                                @endforeach
                            </select>
                        </label>
                    @endif

                    @if ($mapView['drawLayers'])
                        <label class="form-control">
                            <span class="label">{{ $mapView['labels']['drawingLayer'] }}</span>
                            <select class="select select-bordered select-sm" data-daisy-kit-map-draw-layer>
                                @foreach ($mapView['drawLayers'] as $layer)
                                    <option value="{{ $layer['id'] }}">{{ $layer['label'] }}</option>
                                @endforeach
                            </select>
                        </label>
                    @endif
                </div>
            @endif

            @if ($mapView['drawing'])
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
            @endif

            <div class="daisy-kit-map__tool-group" role="group" aria-label="{{ $mapView['labels']['selectionDetails'] }}">
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

            @if ($mapView['drawing'])
                <div class="daisy-kit-map__tool-group" role="group" aria-label="{{ $mapView['labels']['activeMode'] }}">
                    <button class="btn btn-ghost btn-sm" data-daisy-kit-map-history="undo" disabled type="button">{{ $mapView['labels']['undo'] }}</button>
                    <button class="btn btn-ghost btn-sm" data-daisy-kit-map-history="redo" disabled type="button">{{ $mapView['labels']['redo'] }}</button>
                    <button class="btn btn-ghost btn-sm" data-daisy-kit-map-export disabled type="button">{{ $mapView['labels']['exportDrawing'] }}</button>
                </div>
            @endif
        </fieldset>
    </div>
@endif
