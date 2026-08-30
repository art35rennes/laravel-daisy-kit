@props([
    'target' => null,
    'items' => [],
    'selector' => 'h2[id],h3[id]',
    'smooth' => true,
    'offset' => 0,
    'rootMargin' => '0px 0px -60% 0px',
])

@php
    $configuration = \Art35rennes\DaisyKit\Support\JsonConfiguration::encode([
        'target' => $target,
        'items' => $items,
        'selector' => $selector,
        'smooth' => $smooth,
        'offset' => $offset,
        'rootMargin' => $rootMargin,
    ]);
@endphp

<nav {{ $attributes->class(['daisy-kit-scrollspy'])->merge(['data-daisy-kit-module' => 'scrollspy']) }} aria-label="Section navigation">
    <p class="alert alert-error" data-daisy-kit-status hidden role="status" aria-live="polite"></p>
    <ul class="menu" data-daisy-kit-scrollspy-list></ul>
    <script data-daisy-kit-config type="application/json">{!! $configuration !!}</script>
</nav>
