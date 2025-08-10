@props([
    'legend' => null,
])

<fieldset {{ $attributes->merge(['class' => 'fieldset']) }}>
    @if($legend)
        <legend class="fieldset-legend">{{ $legend }}</legend>
    @endif
    {{ $slot }}
</fieldset>
