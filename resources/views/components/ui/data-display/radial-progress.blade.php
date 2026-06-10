@props([
    'value' => 0,
    'size' => null, // e.g. 6rem, 80px
    'thickness' => null, // e.g. 4px
    'color' => null, // text-primary etc without text- prefix
    'showValue' => true,
    // Accessibilité / échelle
    'min' => 0,
    'max' => 100,
])

@php
    $lengthToken = function ($value, string $prefix, int $remMultiplier, int $maxRemToken, int $maxPxToken) {
        if (! is_string($value) && ! $value instanceof \Stringable && ! is_numeric($value)) {
            return null;
        }

        $value = trim((string) $value);

        if (preg_match('/^(\d+(?:\.\d+)?)rem$/', $value, $matches) === 1) {
            $token = (int) round(((float) $matches[1]) * $remMultiplier);

            return $token >= 1 && $token <= $maxRemToken ? "{$prefix}-rem-{$token}" : null;
        }

        if (preg_match('/^(\d+(?:\.\d+)?)px$/', $value, $matches) === 1) {
            $token = (int) round((float) $matches[1]);

            return $token >= 1 && $token <= $maxPxToken ? "{$prefix}-px-{$token}" : null;
        }

        return null;
    };

    $min = (int) $min;
    $max = max($min + 1, (int) $max);
    $rawValue = is_numeric($value) ? (float) $value : 0.0;
    $percent = (int) round(max(0, min(100, (($rawValue - $min) / ($max - $min)) * 100)));

    $classes = 'radial-progress daisy-radial-value-'.$percent;
    if ($color) $classes .= ' text-'.$color;

    if ($sizeClass = $lengthToken($size, 'daisy-radial-size', 4, 128, 512)) {
        $classes .= ' '.$sizeClass;
    }

    if ($thicknessClass = $lengthToken($thickness, 'daisy-radial-thickness', 100, 200, 64)) {
        $classes .= ' '.$thicknessClass;
    }
@endphp

<div {{ $attributes->merge(['class' => $classes, 'role' => 'progressbar', 'aria-valuemin' => $min, 'aria-valuemax' => $max, 'aria-valuenow' => $rawValue]) }}>
    @if($showValue)
        {{ $slot->isNotEmpty() ? $slot : $percent.'%' }}
    @endif
</div>
