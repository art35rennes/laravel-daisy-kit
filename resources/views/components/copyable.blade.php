@props([
    'value' => null,
    'copyLabel' => 'Copy',
    'successLabel' => 'Copied.',
    'errorLabel' => 'Copying failed.',
    'feedbackDuration' => 1000,
    'disabled' => false,
    'showIcon' => false,
    'showFeedback' => true,
])

@php
    $display = isset($slot) && $slot->isNotEmpty() ? $slot : ($value ?? '');
    $configuration = \Art35rennes\DaisyKit\Support\JsonConfiguration::encode([
        'value' => $value,
        'copyLabel' => $copyLabel,
        'successLabel' => $successLabel,
        'errorLabel' => $errorLabel,
        'feedbackDuration' => $feedbackDuration,
        'disabled' => $disabled,
        'showIcon' => $showIcon,
        'showFeedback' => $showFeedback,
    ]);
@endphp

<section {{ $attributes->class(['daisy-kit-copyable'])->merge(['data-daisy-kit-module' => 'copyable']) }}>
    <p
        @class([
            'badge badge-success daisy-kit-copyable-feedback' => $showFeedback,
            'sr-only' => ! $showFeedback,
        ])
        data-daisy-kit-status
        @if($showFeedback) data-daisy-kit-copyable-feedback @endif
        hidden
        role="status"
        aria-live="polite"
    ></p>
    <button class="btn btn-ghost btn-sm daisy-kit-copyable-button" data-daisy-kit-copyable-button type="button" @disabled($disabled)>
        @if($showIcon)
            <svg
                class="daisy-kit-copyable-icon"
                data-daisy-kit-copyable-icon
                aria-hidden="true"
                fill="none"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path d="M8.25 7.5V6A2.25 2.25 0 0 1 10.5 3.75H18A2.25 2.25 0 0 1 20.25 6v7.5A2.25 2.25 0 0 1 18 15.75h-1.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" />
                <path d="M6 8.25h7.5A2.25 2.25 0 0 1 15.75 10.5V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-7.5A2.25 2.25 0 0 1 6 8.25Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" />
            </svg>
        @endif
        {{ $display }}
    </button>
    <script data-daisy-kit-config type="application/json">{!! $configuration !!}</script>
</section>
