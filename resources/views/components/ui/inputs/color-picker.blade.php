@props([
    // Mode: native (input type=color) | advanced (palette + sliders)
    'mode' => 'advanced',
    // Valeur initiale (hex|rgb|hsl) - stock interne en HSLA
    'value' => '#563d7c',
    'name' => null,
    // Readonly/disabled
    'disabled' => false,
    // Afficher comme dropdown attaché à un bouton/trigger
    'dropdown' => false,
    // Palette d'échantillons (swatches): tableau de lignes de couleurs
    'swatches' => [],
    // Hauteur max des swatches
    'swatchesHeight' => 0,
    // Désactiver sections
    'showPalette' => true,
    'showInputs' => true,
    'showFormatToggle' => true,
    'showAlpha' => true,
    'showHue' => true,
    // Surcharge du nom de module JS (optionnel)
    'module' => null,
])

@php
    $id = $attributes->get('id') ?? 'colorpicker-'.uniqid();
    $isNative = ($mode === 'native');
    $chipValue = preg_match('/^#[0-9a-fA-F]{6}$/', (string) $value) === 1 ? (string) $value : '#563d7c';
@endphp

@if($isNative)
    <input type="color" id="{{ $id }}" @if($name) name="{{ $name }}" @endif value="{{ $value }}" {{ $attributes->merge(['class' => 'input w-32']) }} @disabled($disabled) />
@else
    <div id="{{ $id }}" data-module="{{ $module ?? 'color-picker' }}" data-colorpicker="1"
         data-value="{{ $value }}"
         data-disabled="{{ $disabled ? 'true' : 'false' }}"
         data-dropdown="{{ $dropdown ? 'true' : 'false' }}"
         data-swatches='@json($swatches)'
         data-swatches-height="{{ (int)$swatchesHeight }}"
         data-show-palette="{{ $showPalette ? 'true' : 'false' }}"
         data-show-inputs="{{ $showInputs ? 'true' : 'false' }}"
         data-show-format-toggle="{{ $showFormatToggle ? 'true' : 'false' }}"
         data-show-alpha="{{ $showAlpha ? 'true' : 'false' }}"
         data-show-hue="{{ $showHue ? 'true' : 'false' }}"
         {{ $attributes->merge(['class' => 'inline-block']) }}>
        @if($name)
            <input type="hidden" name="{{ $name }}" value="{{ $value }}" data-colorpicker-input>
        @endif

        @if($dropdown)
            <div class="dropdown">
                <div tabindex="0" role="button" class="btn btn-sm btn-ghost" data-colorpicker-trigger>
                    <input type="color" value="{{ $chipValue }}" disabled class="daisy-color-chip-input daisy-color-chip-input-sm mr-2 align-middle" data-colorchip aria-hidden="true" tabindex="-1">
                    <span data-colortext class="align-middle text-sm">{{ $value }}</span>
                </div>
                <div tabindex="0" class="dropdown-content bg-base-100 rounded-box shadow p-3 z-[1] w-72 max-w-[calc(100vw-2rem)]" data-colorpicker-panel></div>
            </div>
        @else
            <div class="w-72 max-w-full rounded-box bg-base-100 shadow p-3" data-colorpicker-panel></div>
        @endif
    </div>
@endif
