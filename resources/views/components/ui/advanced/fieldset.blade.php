@props([
    'legend' => null,
    // Helpers pour fond/bordure arrondi, afin de refléter la doc plus facilement
    'bg' => null,        // ex: base-200
    'bordered' => false, // ajoute border + border-base-300
    'rounded' => true,   // rounded-box
    'padding' => 'p-4',
    'width' => null,     // ex: w-xs
])

@php
    $classes = 'fieldset';
    if ($bg) $classes .= ' bg-'.$bg;
    if ($bordered) $classes .= ' border border-base-300';
    if ($rounded) $classes .= ' rounded-box';
    if ($width) $classes .= ' '.$width;
    if ($padding) $classes .= ' '.$padding;
@endphp

<fieldset {{ $attributes->merge(['class' => $classes]) }}>
    @if($legend)
        <legend class="fieldset-legend">{{ $legend }}</legend>
    @endif
    {{ $slot }}
</fieldset>
