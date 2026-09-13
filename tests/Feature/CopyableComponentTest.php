<?php

declare(strict_types=1);

use Art35rennes\DaisyKit\Support\JsonConfiguration;

test('the copyable component renders a CSP-safe interactive control', function (): void {
    $html = view('daisy-kit::components.copyable', [
        'value' => '</script><img src=x onerror=alert(1)>',
        'copyLabel' => 'Copy identifier',
        'feedbackDuration' => 1200,
        'showIcon' => true,
    ])->render();

    preg_match('/<script data-daisy-kit-config type="application\/json">(.*?)<\/script>/s', $html, $matches);

    expect($html)
        ->toContain('data-daisy-kit-module="copyable"')
        ->toContain('data-daisy-kit-copyable-button')
        ->toContain('data-daisy-kit-status')
        ->toContain('data-daisy-kit-copyable-icon')
        ->toContain('data-daisy-kit-copyable-feedback')
        ->not->toContain('<img')
        ->not->toContain('onclick=')
        ->not->toContain('style=')
        ->and(JsonConfiguration::decode(html_entity_decode($matches[1] ?? '')))->toBe([
            'value' => '</script><img src=x onerror=alert(1)>',
            'copyLabel' => 'Copy identifier',
            'successLabel' => 'Copied.',
            'errorLabel' => 'Copying failed.',
            'feedbackDuration' => 1200,
            'disabled' => false,
            'showIcon' => true,
            'showFeedback' => true,
        ]);
});

test('the copy icon is optional and visual feedback can remain screen-reader only', function (): void {
    $html = view('daisy-kit::components.copyable', [
        'showFeedback' => false,
    ])->render();

    expect($html)
        ->not->toContain('data-daisy-kit-copyable-icon')
        ->not->toContain('data-daisy-kit-copyable-feedback')
        ->toContain('class="sr-only"')
        ->toContain('role="status"');
});

test('the copyable component is available from the public Blade namespace', function (): void {
    $this->blade('<x-daisy-kit::copyable>Reference</x-daisy-kit::copyable>')
        ->assertSee('data-daisy-kit-module="copyable"', false);
});
