@props([
    'items' => [],
    'current' => 0, // index 1-based of completed/current
    'vertical' => false,
])

@php
    $classes = 'steps';
    if ($vertical) $classes .= ' steps-vertical';
@endphp

<ul {{ $attributes->merge(['class' => $classes]) }}>
    @foreach($items as $index => $label)
        @php $isDone = ($index + 1) <= $current; @endphp
        <li class="step {{ $isDone ? 'step-primary' : '' }}">{{ $label }}</li>
    @endforeach
</ul>
