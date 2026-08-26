<?php

declare(strict_types=1);

it('mounts the Workbench modules accessibly on desktop and mobile', function (): void {
    $desktop = $this->visit('/')->on()->desktop();

    $desktop
        ->assertSee('Daisy Kit v5 Workbench')
        // The Livewire Builder owns one real Viewer preview in addition to the seven
        // Workbench mount roots. Both must participate in the normal mount contract.
        ->assertCount('[data-daisy-kit-module]', 8)
        ->waitForEvent('networkidle')
        ->wait(1)
        ->assertNoSmoke()
        ->assertCount('[data-daisy-kit-module="forms-viewer"]', 2)
        ->assertScript("Array.from(document.querySelectorAll('[data-daisy-kit-module=forms-viewer]')).every((root) => ['empty', 'ready'].includes(root.dataset.daisyKitState))")
        ->assertScript("Array.from(document.querySelectorAll('[data-daisy-kit-module]')).every((root) => ['empty', 'ready'].includes(root.dataset.daisyKitState))")
        ->click('[data-daisy-kit-tree-node="documentation"]')
        ->keys('[data-daisy-kit-tree-node="documentation"]', ['ArrowRight', 'ArrowRight'])
        ->assertScript("document.activeElement?.dataset.daisyKitTreeNode === 'getting-started'");

    $this->visit('/')->on()->mobile()
        ->assertCount('[data-daisy-kit-module]', 8)
        ->assertScript('window.innerWidth <= 430');
})->group('browser');

it('composes visible host DaisyUI primitives across themes and responsive widths', function (): void {
    $page = $this->visit('/')->on()->desktop()
        ->waitForEvent('networkidle')
        ->wait(1)
        ->assertNoSmoke();

    foreach ([320, 768, 1024, 1440] as $width) {
        $page->resize($width, 900)
            ->assertScript(<<<'JS'
                (() => {
                    const roots = [...document.querySelectorAll('[data-daisy-kit-module]')];
                    const buttons = [
                        document.querySelector('[data-daisy-kit-module="forms-viewer"] button[type="submit"]'),
                        document.querySelector('[data-daisy-kit-module="forms-builder"] button'),
                        document.querySelector('[data-daisy-kit-module="table"] button'),
                        document.querySelector('[data-daisy-kit-tree-node]'),
                        document.querySelector('[data-daisy-kit-blueprint-node-control]'),
                        document.querySelector('[data-daisy-kit-file-preview-open-preview]'),
                        document.querySelector('[data-daisy-kit-map-mode]'),
                    ];
                    const controls = [
                        document.querySelector('[data-daisy-kit-module="forms-viewer"] input'),
                        document.querySelector('[data-daisy-kit-table-filter]'),
                    ];

                    return roots.every((root) => root.classList.contains('card'))
                        && buttons.every((button) => button?.classList.contains('btn') && getComputedStyle(button).minHeight !== '0px')
                        && controls.every((control) => control?.classList.contains('input') && getComputedStyle(control).borderTopStyle !== 'none')
                        && document.documentElement.scrollWidth <= window.innerWidth;
                })()
                JS)
            ->assertNoAccessibilityIssues(1);
    }

    foreach (['light', 'dark'] as $theme) {
        $page->script("document.documentElement.dataset.theme = '{$theme}';")
            ->assertScript(<<<'JS'
                (() => {
                    const primary = document.querySelector('[data-daisy-kit-forms-actions] button[type="submit"]');
                    const warning = document.querySelector('[data-daisy-kit-file-preview-notice]');

                    return primary.classList.contains('btn-primary')
                        && getComputedStyle(primary).paddingInlineStart !== '0px'
                        && warning.classList.contains('alert-warning');
                })()
                JS);
    }
})->group('browser');

it('mounts the map without a browser CSP violation', function (): void {
    $this->visit('/_daisy-kit-test/csp/map')
        ->assertSee('Daisy Kit CSP Map')
        ->waitForEvent('networkidle')
        ->wait(1)
        ->assertScript("document.querySelector('[data-daisy-kit-module=map]').dataset.daisyKitState === 'ready'")
        ->assertNoSmoke();
})->group('browser');

it('keeps Blueprint controls outside its inert SVG and supports keyboard selection', function (): void {
    $this->visit('/')->on()->desktop()
        ->waitForEvent('networkidle')
        ->wait(1)
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues(1)
        ->assertScript("(() => { const svg = document.querySelector('[data-daisy-kit-blueprint-canvas]'); return svg.getAttribute('aria-hidden') === 'true' && !svg.hasAttribute('role') && !svg.hasAttribute('tabindex') && svg.querySelectorAll('[role=button], [tabindex]').length === 0; })()")
        ->keys('[data-daisy-kit-blueprint-node-control][data-node-id="source"]', 'ArrowRight')
        ->assertScript("document.activeElement?.dataset.nodeId === 'destination'")
        ->keys('[data-daisy-kit-blueprint-node-control][data-node-id="destination"]', 'Enter')
        ->assertScript("document.querySelector('[data-daisy-kit-blueprint-node-control][data-node-id=destination]').getAttribute('aria-pressed') === 'true'");
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
