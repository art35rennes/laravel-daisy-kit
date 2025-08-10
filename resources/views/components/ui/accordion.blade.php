@props([
    'items' => [], // [['title' => '...', 'content' => '...', 'checked' => false]]
    'arrow' => true,
    'name' => 'accordion',
    'openIndex' => null, // 0-based index
])

@php
    $collapseModifier = $arrow ? ' collapse-arrow' : ' collapse-plus';
@endphp

<div class="join join-vertical w-full">
    @foreach($items as $index => $item)
        <div class="collapse{{ $collapseModifier }} join-item border border-base-300">
            <input type="radio" name="{{ $name }}" @checked(($openIndex === $index) || (!is_null($item['checked'] ?? null) && $item['checked'])) />
            <div class="collapse-title text-lg font-medium">{{ $item['title'] ?? '' }}</div>
            <div class="collapse-content">{!! $item['content'] ?? '' !!}</div>
        </div>
    @endforeach
</div>
