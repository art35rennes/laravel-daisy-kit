@props([
    'text' => '',
    'lines' => 1,
    'revealLabel' => 'Read full text',
    'title' => null,
])

@php
    $configuration = \Art35rennes\DaisyKit\Support\JsonConfiguration::encode([
        'text' => $text,
        'lines' => $lines,
        'revealLabel' => $revealLabel,
        'title' => $title,
    ]);
@endphp

<section {{ $attributes->class(['daisy-kit-truncate'])->merge(['data-daisy-kit-module' => 'truncate']) }}>
    <p class="sr-only" data-daisy-kit-status hidden role="status" aria-live="polite"></p>
    <p data-daisy-kit-truncate-text></p>
    <button class="btn btn-link btn-sm" data-daisy-kit-truncate-reveal hidden type="button"></button>
    <div class="daisy-kit-truncate-popover" data-daisy-kit-truncate-popover popover="manual">
        <article>
            <h2 data-daisy-kit-truncate-title hidden></h2>
            <p data-daisy-kit-truncate-full-text></p>
            <button class="btn btn-sm" data-daisy-kit-truncate-close type="button">Close</button>
        </article>
    </div>
    <script data-daisy-kit-config type="application/json">{!! $configuration !!}</script>
</section>
