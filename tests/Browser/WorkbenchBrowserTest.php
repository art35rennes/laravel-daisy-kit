<?php

declare(strict_types=1);

it('mounts the Workbench modules accessibly on desktop and mobile', function (): void {
    $desktop = $this->visit('/')->on()->desktop();

    $desktop
        ->assertSee('Daisy Kit v5 Workbench')
        ->assertCount('[data-daisy-kit-module]', 7)
        ->waitForEvent('networkidle')
        ->wait(1)
        ->assertNoSmoke()
        ->assertScript("Array.from(document.querySelectorAll('[data-daisy-kit-module]')).every((root) => ['empty', 'ready'].includes(root.dataset.daisyKitState))")
        ->click('[data-daisy-kit-tree-node="documentation"]')
        ->keys('[data-daisy-kit-tree-node="documentation"]', ['ArrowRight', 'ArrowRight'])
        ->assertScript("document.activeElement?.dataset.daisyKitTreeNode === 'getting-started'");

    $this->visit('/')->on()->mobile()
        ->assertCount('[data-daisy-kit-module]', 7)
        ->assertScript('window.innerWidth <= 430');
})->group('browser');

it('mounts the map without a browser CSP violation', function (): void {
    $this->visit('/_daisy-kit-test/csp/map')
        ->assertSee('Daisy Kit CSP Map')
        ->waitForEvent('networkidle')
        ->wait(1)
        ->assertScript("document.querySelector('[data-daisy-kit-module=map]').dataset.daisyKitState === 'ready'")
        ->assertNoSmoke();
})->group('browser');

it('isolates the file preview without a host CSP exception', function (): void {
    $this->visit('/_daisy-kit-test/csp/file-preview')
        ->assertSee('Daisy Kit CSP File Preview')
        ->waitForEvent('networkidle')
        ->wait(1)
        ->assertNoSmoke()
        ->withinFrame('[data-daisy-kit-file-preview-frame]', function ($frame): void {
            $frame->assertScript("document.documentElement.dataset.daisyKitFilePreviewFrame === 'ready'");
        })
        ->assertScript("document.querySelector('[data-daisy-kit-module=file-preview]').dataset.daisyKitState === 'ready'")
        ->assertScript("!document.querySelector('[data-daisy-kit-file-preview-frame]').sandbox.contains('allow-same-origin')")
        ->withinFrame('[data-daisy-kit-file-preview-frame]', function ($frame): void {
            $frame->assertSee('Sandboxed file preview');
        });
})->group('browser');
