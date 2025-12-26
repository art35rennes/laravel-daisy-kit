@props([
    'title' => null,
    'arrow' => true, // true => collapse-arrow, false => collapse-plus
    'open' => false,
    'disabled' => false,
    // Compact mode reduces paddings/min-height to fit in dense UIs (like sidebars).
    'compact' => false,
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
    // Construction des classes CSS selon les options (arrow/plus, force, styles).
    $root = 'collapse';
    $root .= $arrow ? ' collapse-arrow' : ' collapse-plus';
    // États forcés : override l'état par défaut (utile pour animations ou contrôle externe).
    if ($force === 'open') $root .= ' collapse-open';
    if ($force === 'close') $root .= ' collapse-close';
    if ($bordered) $root .= ' card-border';
    if ($bg) $root .= ' bg-base-100';

    if ($compact) {
        // Mode compact : réduit drastiquement les paddings et min-height pour une UI dense (sidebar).
        // Utilise !important pour override les styles daisyUI par défaut.
        $titleClass = trim($titleClass.' !min-h-0 !py-1.5 !px-2 !text-sm');
        $contentClass = trim($contentClass.' !px-2 !pb-1.5 !pt-0');
    }
@endphp
{{-- Trois méthodes d'interaction supportées : details (HTML natif), focus (clavier), checkbox (défaut) --}}
@if($method === 'details')
    {{-- Méthode details : utilise l'élément HTML <details> natif (meilleure accessibilité) --}}
    <details {{ $attributes->merge(['class' => $root]) }} @if($open) open @endif>
        <summary class="collapse-title {{ $titleClass }}">{{ $title }}</summary>
        <div class="collapse-content {{ $contentClass }}">{{ $slot }}</div>
    </details>
@elseif($method === 'focus')
    {{-- Méthode focus : toggle via focus/blur (accessible au clavier, pas de checkbox) --}}
    <div {{ $attributes->merge(['class' => $root]) }} tabindex="0">
        <div class="collapse-title {{ $titleClass }}">{{ $title }}</div>
        <div class="collapse-content {{ $contentClass }}">{{ $slot }}</div>
    </div>
@else
    {{-- Méthode checkbox (défaut) : toggle via checkbox (le plus flexible) --}}
    <div {{ $attributes->merge(['class' => $root]) }}>
        <input type="checkbox" @checked($open) @disabled($disabled) />
        <div class="collapse-title {{ $titleClass }}">{{ $title }}</div>
        <div class="collapse-content {{ $contentClass }}">{{ $slot }}</div>
    </div>
@endif
