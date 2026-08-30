@if ($mapView['controls']['drawing'] && $mapView['drawing'])
    <fieldset class="daisy-kit-map__menu-section">
        <legend>{{ $mapView['labels']['createTools'] }}</legend>

        @if ($mapView['objectTypes'])
            <label class="daisy-kit-map__menu-field">
                <span>{{ $mapView['labels']['objectType'] }}</span>
                <select class="select select-bordered select-sm" data-daisy-kit-map-object-type>
                    @foreach ($mapView['objectTypes'] as $type)
                        <option value="{{ $type['id'] }}">{{ $type['label'] }}</option>
                    @endforeach
                </select>
            </label>
        @endif

        @if ($mapView['drawLayers'])
            <label class="daisy-kit-map__menu-field">
                <span>{{ $mapView['labels']['drawingLayer'] }}</span>
                <select class="select select-bordered select-sm" data-daisy-kit-map-draw-layer>
                    @foreach ($mapView['drawLayers'] as $layer)
                        <option value="{{ $layer['id'] }}">{{ $layer['label'] }}</option>
                    @endforeach
                </select>
            </label>
        @endif

        <div class="daisy-kit-map__menu-actions" role="group" aria-label="{{ $mapView['labels']['drawingTools'] }}">
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
    </fieldset>
@endif
