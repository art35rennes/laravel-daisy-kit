<?php

declare(strict_types=1);

use Art35rennes\DaisyKit\Support\JsonConfiguration;

test('the truncate component emits a native-popover shell without executable markup', function (): void {
    $html = view('daisy-kit::components.truncate', [
        'text' => '</script><img src=x onerror=alert(1)>',
        'lines' => 2,
        'revealLabel' => 'Read full text',
    ])->render();

    preg_match('/<script data-daisy-kit-config type="application\/json">(.*?)<\/script>/s', $html, $matches);

    expect($html)
        ->toContain('data-daisy-kit-module="truncate"')
        ->toContain('data-daisy-kit-truncate-text')
        ->toContain('data-daisy-kit-truncate-reveal')
        ->toContain('popover="manual"')
        ->not->toContain('<img')
        ->not->toContain('onclick=')
        ->not->toContain('style=')
        ->and(JsonConfiguration::decode(html_entity_decode($matches[1] ?? '')))->toBe([
            'text' => '</script><img src=x onerror=alert(1)>',
            'lines' => 2,
            'revealLabel' => 'Read full text',
            'title' => null,
        ]);
});
