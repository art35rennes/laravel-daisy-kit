@props([
    'value' => null,
    'copyLabel' => 'Copy',
    'successLabel' => 'Copied.',
    'errorLabel' => 'Copying failed.',
    'feedbackDuration' => 1000,
    'disabled' => false,
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
    ]);
@endphp

<section {{ $attributes->class(['daisy-kit-copyable'])->merge(['data-daisy-kit-module' => 'copyable']) }}>
    <p class="sr-only" data-daisy-kit-status hidden role="status" aria-live="polite"></p>
    <button class="btn btn-ghost btn-sm daisy-kit-copyable-button" data-daisy-kit-copyable-button type="button" @disabled($disabled)>
        {{ $display }}
    </button>
    <script data-daisy-kit-config type="application/json">{!! $configuration !!}</script>
</section>
