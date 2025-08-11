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
    $collapseModifier = $arrow ? ' collapse-arrow' : ' collapse-plus';
@endphp

<div class="join join-vertical w-full {{ $bgClass }}">
    @foreach($items as $index => $item)
        @php
            $forcedState = '';
            if (!empty($item['open'])) $forcedState = ' collapse-open';
            if (!empty($item['close'])) $forcedState = ' collapse-close';
            $borderClasses = $bordered ? ' border border-base-300' : '';
        @endphp
        <div class="collapse{{ $collapseModifier }}{{ $forcedState }} join-item{{ $borderClasses }} {{ $itemClass }}">
            <input type="radio" name="{{ $name }}" @checked(($openIndex === $index) || (!is_null($item['checked'] ?? null) && $item['checked'])) />
            <div class="collapse-title {{ $titleClass }}">{{ $item['title'] ?? '' }}</div>
            <div class="collapse-content {{ $contentClass }}">{!! $item['content'] ?? '' !!}</div>
        </div>
    @endforeach
</div>
