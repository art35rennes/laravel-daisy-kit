@props([
    'types' => [],
    'currentFilter' => 'all', // all, unread, or specific type
    'onFilterChange' => null, // Callback JS (optionnel)
])

@php
    $filterName = 'notification-filter-' . uniqid();
    $items = [
        ['label' => __('notifications.all'), 'value' => 'all', 'checked' => $currentFilter === 'all'],
        ['label' => __('notifications.unread'), 'value' => 'unread', 'checked' => $currentFilter === 'unread'],
    ];

    foreach ($types as $type) {
        $typeLabel = is_array($type) ? ($type['label'] ?? $type['value'] ?? $type) : $type;
        $typeValue = is_array($type) ? ($type['value'] ?? $typeLabel) : $type;
        $items[] = [
            'label' => $typeLabel,
            'value' => $typeValue,
            'checked' => $currentFilter === $typeValue,
        ];
    }
@endphp

<div {{ $attributes->merge(['class' => 'notification-filters']) }}>
    <x-daisy::ui.advanced.filter
        :name="$filterName"
        :items="$items"
        :use-form="false"
        :all-label="__('notifications.all')"
        data-filter-name="{{ $filterName }}"
        @if($onFilterChange) data-on-filter-change="{{ $onFilterChange }}" @endif
    />
</div>

