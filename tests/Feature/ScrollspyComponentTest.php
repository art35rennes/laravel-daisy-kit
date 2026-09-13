<?php

declare(strict_types=1);

use Art35rennes\DaisyKit\Support\JsonConfiguration;

test('the scrollspy component serializes declarative navigation safely', function (): void {
    $html = view('daisy-kit::components.scrollspy', [
        'target' => '#guide',
        'items' => [['id' => 'install', 'label' => '</script><img src=x onerror=alert(1)>']],
        'offset' => 24,
    ])->render();

    preg_match('/<script data-daisy-kit-config type="application\/json">(.*?)<\/script>/s', $html, $matches);

    expect($html)
        ->toContain('data-daisy-kit-module="scrollspy"')
        ->toContain('data-daisy-kit-scrollspy-list')
        ->toContain('aria-label="Section navigation"')
        ->not->toContain('<img')
        ->not->toContain('onclick=')
        ->not->toContain('style=')
        ->and(JsonConfiguration::decode(html_entity_decode($matches[1] ?? '')))->toBe([
            'target' => '#guide',
            'items' => [['id' => 'install', 'label' => '</script><img src=x onerror=alert(1)>']],
            'selector' => 'h2[id],h3[id]',
            'smooth' => true,
            'offset' => 24,
            'rootMargin' => '0px 0px -60% 0px',
        ]);
});
