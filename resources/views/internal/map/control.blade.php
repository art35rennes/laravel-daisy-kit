@if ($isRenderableControl($control))
    @if ($control['type'] === 'menu')
        <details
            @class([
                'daisy-kit-map__menu',
                'daisy-kit-map__menu--root dropdown dropdown-end' => $rootLevel,
                'dropdown-top' => $rootLevel && str_starts_with($mapView['controls']['position'], 'bottom'),
                'daisy-kit-map__menu--align-start' => $rootLevel && str_ends_with($mapView['controls']['position'], 'left'),
                'daisy-kit-map__submenu' => ! $rootLevel,
                'daisy-kit-map__mobile-view' => $responsiveView ?? false,
            ])
            data-daisy-kit-map-menu="{{ $control['id'] }}"
            @if (! $control['enabled']) data-daisy-kit-map-control-disabled @endif
        >
            <summary
                @class([
                    'btn btn-sm' => true,
                    'btn-square bg-base-100' => $rootLevel,
                    'btn-ghost daisy-kit-map__submenu-trigger' => ! $rootLevel,
                ])
                aria-label="{{ $control['label'] }}"
                aria-disabled="{{ $control['enabled'] ? 'false' : 'true' }}"
                aria-expanded="false"
                @if (! $control['enabled']) tabindex="-1" @endif
                title="{{ $control['label'] }}"
            >
                @if ($rootLevel)
                    @include('daisy-kit::internal.map.icon', ['icon' => $control['icon'] ?? 'menu'])
                @else
                    <span>{{ $control['label'] }}</span>
                    <span aria-hidden="true">›</span>
                @endif
            </summary>
            <div @class([
                'daisy-kit-map__menu-panel rounded-box border border-base-300 bg-base-100 p-2 text-sm shadow-lg',
                'dropdown-content' => $rootLevel,
                'daisy-kit-map__submenu-panel' => ! $rootLevel,
            ])>
                @foreach ($control['items'] as $childControl)
                    @include('daisy-kit::internal.map.control', [
                        'control' => $childControl,
                        'controlSlots' => $controlSlots,
                        'depth' => $depth + 1,
                        'mapId' => $mapId,
                        'mapView' => $mapView,
                        'rootLevel' => false,
                    ])
                @endforeach
            </div>
        </details>
    @elseif ($control['type'] === 'group')
        <fieldset class="daisy-kit-map__menu-section" data-daisy-kit-map-group="{{ $control['id'] }}" @disabled(! $control['enabled'])>
            <legend>{{ $control['label'] }}</legend>
            @foreach ($control['items'] as $childControl)
                @include('daisy-kit::internal.map.control', [
                    'control' => $childControl,
                    'controlSlots' => $controlSlots,
                    'depth' => $depth,
                    'mapId' => $mapId,
                    'mapView' => $mapView,
                    'rootLevel' => false,
                ])
            @endforeach
        </fieldset>
    @elseif ($control['type'] === 'slot')
        @php($slotName = \Illuminate\Support\Str::camel('map-'.$control['slot']))
        @if (isset($controlSlots[$slotName]))
            <fieldset class="daisy-kit-map__menu-section daisy-kit-map__host-controls" data-daisy-kit-map-slot="{{ $control['slot'] }}" @disabled(! $control['enabled'])>
                {{ $controlSlots[$slotName] }}
            </fieldset>
        @endif
    @elseif ($control['type'] === 'collection')
        @include('daisy-kit::internal.map.menu.'.$control['action'], ['control' => $control, 'mapId' => $mapId, 'mapView' => $mapView])
    @elseif ($control['type'] === 'selector')
        @if ($control['action'] === 'objectTypeSelector' && $mapView['objectTypes'])
            <label class="daisy-kit-map__menu-field">
                <span>{{ $control['label'] }}</span>
                <select class="select select-bordered select-sm" data-daisy-kit-map-object-type @disabled(! $control['enabled'])>
                    @foreach ($mapView['objectTypes'] as $type)
                        <option value="{{ $type['id'] }}">{{ $type['label'] }}</option>
                    @endforeach
                </select>
            </label>
        @elseif ($control['action'] === 'drawLayerSelector' && $mapView['drawLayers'])
            <label class="daisy-kit-map__menu-field">
                <span>{{ $control['label'] }}</span>
                <select class="select select-bordered select-sm" data-daisy-kit-map-draw-layer @disabled(! $control['enabled'])>
                    @foreach ($mapView['drawLayers'] as $layer)
                        <option value="{{ $layer['id'] }}">{{ $layer['label'] }}</option>
                    @endforeach
                </select>
            </label>
        @endif
    @elseif ($control['type'] === 'action')
        <button
            @class([
                'btn btn-sm' => true,
                'btn-square bg-base-100' => $rootLevel,
                'btn-ghost daisy-kit-map__menu-action' => ! $rootLevel,
                'daisy-kit-map__direct-view-action' => $rootLevel && in_array($control['action'], ['fitBounds', 'geolocate', 'fullscreen'], true),
            ])
            @switch($control['action'])
                @case('drawPoint') data-daisy-kit-map-mode="point" aria-pressed="false" @break
                @case('drawLine') data-daisy-kit-map-mode="linestring" aria-pressed="false" @break
                @case('drawPolygon') data-daisy-kit-map-mode="polygon" aria-pressed="false" @break
                @case('drawRectangle') data-daisy-kit-map-mode="rectangle" aria-pressed="false" @break
                @case('edit') data-daisy-kit-map-mode="edit" aria-pressed="false" @break
                @case('select') data-daisy-kit-map-mode="select" aria-pressed="false" @break
                @case('selectFeature') data-daisy-kit-map-mode="feature-select" aria-pressed="false" @break
                @case('selectByArea') data-daisy-kit-map-mode="spatial-select" aria-pressed="false" @break
                @case('deleteSelected') data-daisy-kit-map-delete-selected @break
                @case('clearSelection') data-daisy-kit-map-clear-selection @break
                @case('undo') data-daisy-kit-map-history="undo" @break
                @case('redo') data-daisy-kit-map-history="redo" @break
                @case('export') data-daisy-kit-map-export @break
                @case('fitBounds') data-daisy-kit-map-fit-bounds @break
                @case('geolocate') data-daisy-kit-map-geolocate @break
                @case('fullscreen') data-daisy-kit-map-fullscreen aria-pressed="false" @break
                @case('custom') data-daisy-kit-map-action="{{ $control['customId'] }}" @break
            @endswitch
            aria-label="{{ $control['label'] }}"
            @disabled(! $control['enabled'] || in_array($control['action'], ['deleteSelected', 'undo', 'redo', 'export'], true))
            title="{{ $control['label'] }}"
            type="button"
        >
            @if ($rootLevel)
                @include('daisy-kit::internal.map.icon', ['icon' => $control['icon'] ?? $control['action']])
            @else
                <span>{{ $control['label'] }}</span>
            @endif
        </button>
    @endif
@endif
