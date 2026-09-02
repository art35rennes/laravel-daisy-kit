@props([
    'text' => '',
    'lines' => 1,
    'revealLabel' => 'Read full text',
    'hover' => true,
    'hoverDelay' => 250,
    'backdrop' => false,
    'title' => null,
])

@php
    $popoverId = 'daisy-kit-truncate-'.\Illuminate\Support\Str::uuid();
    $configuration = \Art35rennes\DaisyKit\Support\JsonConfiguration::encode([
        'text' => $text,
        'lines' => $lines,
        'revealLabel' => $revealLabel,
        'hover' => $hover,
        'hoverDelay' => $hoverDelay,
        'backdrop' => $backdrop,
        'title' => $title,
    ]);
@endphp

<section {{ $attributes->class(['daisy-kit-truncate'])->merge(['data-daisy-kit-module' => 'truncate']) }}>
    <p class="sr-only" data-daisy-kit-status hidden role="status" aria-live="polite"></p>
    <span class="daisy-kit-truncate-preview" data-daisy-kit-truncate-preview>
        <span data-daisy-kit-truncate-text></span>
        <button
            class="btn btn-ghost btn-xs daisy-kit-truncate-reveal"
            aria-controls="{{ $popoverId }}"
            aria-expanded="false"
            data-daisy-kit-truncate-reveal
            hidden
            popovertarget="{{ $popoverId }}"
            popovertargetaction="show"
            type="button"
        >&hellip;</button>
    </span>
    <div class="daisy-kit-truncate-popover" data-daisy-kit-truncate-popover id="{{ $popoverId }}" popover="auto">
        <article>
            <header>
                <h2 data-daisy-kit-truncate-title hidden></h2>
                <button class="btn btn-circle btn-ghost btn-xs" aria-label="Close" data-daisy-kit-truncate-close type="button">&times;</button>
            </header>
            <p data-daisy-kit-truncate-full-text></p>
        </article>
    </div>
    <script data-daisy-kit-config type="application/json">{!! $configuration !!}</script>
</section>
