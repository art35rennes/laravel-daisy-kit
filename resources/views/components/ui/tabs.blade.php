@props([
    'items' => [], // [['label' => 'Tab 1', 'active' => false]]
    'variant' => null, // box|boxed|border|bordered|lifted
])

@php
    $classes = 'tabs';
    if ($variant) {
        $map = [
            'box' => 'tabs-box',
            'boxed' => 'tabs-box',
            'border' => 'tabs-border',
            'bordered' => 'tabs-border',
            'lifted' => 'tabs-lift',
        ];
        $classes .= ' '.($map[$variant] ?? '');
    }
@endphp

<div role="tablist" {{ $attributes->merge(['class' => $classes]) }}>
    @foreach($items as $tab)
        @php $isActive = !empty($tab['active']); @endphp
        <button role="tab" class="tab {{ $isActive ? 'tab-active' : '' }}">{{ $tab['label'] ?? 'Tab' }}</button>
    @endforeach
</div>
