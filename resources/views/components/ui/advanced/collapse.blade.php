@props([
    'title' => null,
    'arrow' => true, // true => collapse-arrow, false => collapse-plus
    'open' => false,
    'disabled' => false,
    // Méthode d'interaction: checkbox | focus | details
    'method' => 'checkbox',
    // Forcer l'état visuel: null | open | close (non supporté sur details)
    'force' => null,
    // Styles utilitaires
    'bordered' => false,
    'bg' => false,
    'titleClass' => 'text-lg font-medium',
    'contentClass' => 'text-sm',
])

@php
    $root = 'collapse';
    $root .= $arrow ? ' collapse-arrow' : ' collapse-plus';
    if ($force === 'open') $root .= ' collapse-open';
    if ($force === 'close') $root .= ' collapse-close';
    if ($bordered) $root .= ' border border-base-300';
    if ($bg) $root .= ' bg-base-100';
@endphp
@if($method === 'details')
    <details {{ $attributes->merge(['class' => $root]) }} @if($open) open @endif>
        <summary class="collapse-title {{ $titleClass }}">{{ $title }}</summary>
        <div class="collapse-content {{ $contentClass }}">{{ $slot }}</div>
    </details>
@elseif($method === 'focus')
    <div {{ $attributes->merge(['class' => $root]) }} tabindex="0">
        <div class="collapse-title {{ $titleClass }}">{{ $title }}</div>
        <div class="collapse-content {{ $contentClass }}">{{ $slot }}</div>
    </div>
@else
    <div {{ $attributes->merge(['class' => $root]) }}>
        <input type="checkbox" @checked($open) @disabled($disabled) />
        <div class="collapse-title {{ $titleClass }}">{{ $title }}</div>
        <div class="collapse-content {{ $contentClass }}">{{ $slot }}</div>
    </div>
@endif
