@props([
    'name',
    'label' => 'Signature',
    'value' => null,
    'width' => 640,
    'height' => 240,
    'penColor' => 'black',
    'backgroundColor' => 'rgba(0,0,0,0)',
    'minWidth' => 0.5,
    'maxWidth' => 2.5,
    'velocityFilterWeight' => 0.7,
    'throttle' => 16,
    'minDistance' => 5,
    'required' => false,
    'disabled' => false,
    'showUndo' => true,
    'showRedo' => true,
    'showClear' => true,
    'showDownload' => true,
])

@php
    $configuration = \Art35rennes\DaisyKit\Support\JsonConfiguration::encode([
        'value' => $value,
        'width' => $width,
        'height' => $height,
        'penColor' => $penColor,
        'backgroundColor' => $backgroundColor,
        'minWidth' => $minWidth,
        'maxWidth' => $maxWidth,
        'velocityFilterWeight' => $velocityFilterWeight,
        'throttle' => $throttle,
        'minDistance' => $minDistance,
        'required' => $required,
        'disabled' => $disabled,
    ]);
@endphp

<fieldset {{ $attributes->only(['id', 'class', 'aria-describedby'])->class(['fieldset', 'daisy-kit-signature']) }} data-daisy-kit-module="signature" @disabled($disabled)>
    <legend class="fieldset-legend">{{ $label }}</legend>
    <p class="alert alert-error" data-daisy-kit-status hidden role="alert"></p>
    <div class="daisy-kit-signature__canvas-wrap">
        <canvas data-daisy-kit-signature-canvas width="{{ (int) $width }}" height="{{ (int) $height }}" aria-label="{{ $label }}"></canvas>
    </div>
    <input class="sr-only" data-daisy-kit-signature-value name="{{ $name }}" type="text" value="{{ is_string($value) ? $value : '' }}" tabindex="-1" aria-label="{{ $label }} value" @required($required) @disabled($disabled)>
    <div class="flex flex-wrap gap-2" data-daisy-kit-signature-actions>
        @if ($showUndo)<button class="btn btn-sm" data-daisy-kit-signature-undo type="button" @disabled($disabled)>Undo</button>@endif
        @if ($showRedo)<button class="btn btn-sm" data-daisy-kit-signature-redo type="button" @disabled($disabled)>Redo</button>@endif
        @if ($showClear)<button class="btn btn-sm" data-daisy-kit-signature-clear type="button" @disabled($disabled)>Clear</button>@endif
        @if ($showDownload)<button class="btn btn-sm" data-daisy-kit-signature-download type="button" @disabled($disabled)>Download</button>@endif
    </div>
    <script data-daisy-kit-config type="application/json">{!! $configuration !!}</script>
</fieldset>
