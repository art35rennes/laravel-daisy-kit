@if ($mapView['drawLayers'])
    <fieldset class="daisy-kit-map__menu-section" data-daisy-kit-map-drawing-layers @disabled(! $control['enabled'])>
        <legend>{{ $control['label'] }}</legend>
        @foreach ($mapView['drawLayers'] as $layer)
            <label class="daisy-kit-map__menu-option cursor-pointer">
                <input
                    class="{{ $mapView['drawLayerSelection'] === 'multiple' ? 'checkbox checkbox-primary' : 'radio radio-primary' }}"
                    data-daisy-kit-map-draw-layer-visibility
                    name="{{ $mapId }}-drawing-layers"
                    type="{{ $mapView['drawLayerSelection'] === 'multiple' ? 'checkbox' : 'radio' }}"
                    value="{{ $layer['id'] }}"
                    @checked($layer['visible'])
                    @disabled(! $control['enabled'])
                >
                <span>{{ $layer['label'] }}</span>
            </label>
        @endforeach
    </fieldset>
@endif
