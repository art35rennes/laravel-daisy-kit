@props([
    'items' => [], // [['title' => '...', 'content' => '...', 'checked' => false, 'open' => false, 'close' => false]]
    'arrow' => true, // true => collapse-arrow, false => collapse-plus
    'name' => 'accordion',
    'openIndex' => null, // 0-based index
    // Style/utilitaires
    'bgClass' => 'bg-base-100',
    'bordered' => true,
    'itemClass' => '',
    'titleClass' => 'text-lg font-medium',
    'contentClass' => 'text-sm',
])

@php
    // Sélection du modificateur d'icône : flèche (arrow) ou plus (plus).
    $collapseModifier = $arrow ? ' collapse-arrow' : ' collapse-plus';
@endphp

{{-- Accordion : utilise join-vertical pour joindre les items collapse (pattern daisyUI) --}}
<div class="join join-vertical w-full {{ $bgClass }}">
    @foreach($items as $index => $item)
        @php
            // Gestion de l'état forcé : open/close a priorité sur checked (pour contrôle visuel).
            $forcedState = '';
            if (!empty($item['open'])) $forcedState = ' collapse-open';
            if (!empty($item['close'])) $forcedState = ' collapse-close';
            // Classes de bordure optionnelles.
            $borderClasses = $bordered ? ' card-border' : '';
        @endphp
        {{-- Item collapse : utilise radio pour l'exclusivité (un seul item ouvert à la fois) --}}
        <div class="collapse{{ $collapseModifier }}{{ $forcedState }} join-item{{ $borderClasses }} {{ $itemClass }}">
            {{-- Radio input : contrôle l'état open/close (checked = ouvert) --}}
            <input type="radio" name="{{ $name }}" @checked(($openIndex === $index) || (!is_null($item['checked'] ?? null) && $item['checked'])) />
            <div class="collapse-title {{ $titleClass }}">{{ $item['title'] ?? '' }}</div>
            <div class="collapse-content {{ $contentClass }}">{!! $item['content'] ?? '' !!}</div>
        </div>
    @endforeach
</div>
