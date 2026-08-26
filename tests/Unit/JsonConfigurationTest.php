<?php

declare(strict_types=1);

use Art35rennes\DaisyKit\Support\JsonConfiguration;

it('encodes configuration without executable HTML delimiters', function (): void {
    $json = JsonConfiguration::encode(['label' => '</script><img src=x onerror=alert(1)>']);

    expect($json)->not->toContain('</script>')
        ->and(JsonConfiguration::decode($json))->toBe([
            'label' => '</script><img src=x onerror=alert(1)>',
        ]);
});

it('returns null for invalid configuration JSON', function (): void {
    expect(JsonConfiguration::decode('{invalid'))->toBeNull();
});
