<?php

declare(strict_types=1);

use Art35rennes\DaisyKit\Support\JsonConfiguration;

test('the copyable component renders a CSP-safe interactive control', function (): void {
    $html = view('daisy-kit::components.copyable', [
        'value' => '</script><img src=x onerror=alert(1)>',
        'copyLabel' => 'Copy identifier',
        'feedbackDuration' => 1200,
    ])->render();

    preg_match('/<script data-daisy-kit-config type="application\/json">(.*?)<\/script>/s', $html, $matches);

    expect($html)
        ->toContain('data-daisy-kit-module="copyable"')
        ->toContain('data-daisy-kit-copyable-button')
        ->toContain('data-daisy-kit-status')
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
        ]);
});

test('the copyable component is available from the public Blade namespace', function (): void {
    $this->blade('<x-daisy-kit::copyable>Reference</x-daisy-kit::copyable>')
        ->assertSee('data-daisy-kit-module="copyable"', false);
});
