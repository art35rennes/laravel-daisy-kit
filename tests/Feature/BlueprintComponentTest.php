<?php

declare(strict_types=1);

test('the blueprint component serializes nodes without executable markup', function (): void {
    $html = view('daisy-kit::components.blueprint', [
        'nodes' => [['id' => 'start', 'label' => '</script><img src=x>']],
        'edges' => [],
    ])->render();

    expect($html)
        ->toContain('data-daisy-kit-module="blueprint"')
        ->toContain('type="application/json"')
        ->not->toContain('</script><img')
        ->not->toContain('onclick=')
        ->not->toContain('style=');
});

test('the blueprint component exposes semantic empty and error states', function (): void {
    $html = view('daisy-kit::components.blueprint')->render();

    expect($html)
        ->toContain('data-daisy-kit-empty')
        ->toContain('role="alert"')
        ->toContain('data-daisy-kit-blueprint-canvas')
        ->toContain('data-daisy-kit-blueprint-value')
        ->toContain('aria-hidden="true"')
        ->toContain('focusable="false"')
        ->not->toContain('role="img"')
        ->not->toContain('tabindex="0"');
});

test('the blueprint component is available through the public blade namespace', function (): void {
    $this->blade('<x-daisy-kit::blueprint />')
        ->assertSee('data-daisy-kit-module="blueprint"', false);
});
