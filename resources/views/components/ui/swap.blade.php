@props([
    'rotate' => false,
    'flip' => false,
    'checked' => false,
    // Activer l'état via classe (sans checkbox)
    'active' => false,
    // Rendre la checkbox (true par défaut). Mettre false pour utiliser uniquement la classe active.
    'useInput' => true,
])

@php
    $classes = 'swap';
    if ($rotate) $classes .= ' swap-rotate';
    if ($flip) $classes .= ' swap-flip';
    if ($active) $classes .= ' swap-active';
@endphp

<label {{ $attributes->merge(['class' => $classes]) }}>
    @if($useInput)
        <input type="checkbox" @checked($checked) />
    @endif
    <div class="swap-on">
        {{ $on ?? '' }}
    </div>
    <div class="swap-off">
        {{ $off ?? '' }}
    </div>
    @isset($indeterminate)
        <div class="swap-indeterminate">
            {{ $indeterminate }}
        </div>
    @endisset
</label>
