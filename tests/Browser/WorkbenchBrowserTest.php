<?php

declare(strict_types=1);

it('presents the Workbench module directory accessibly on desktop and mobile', function (): void {
    $desktop = $this->visit('/')->on()->desktop();

    $desktop
        ->assertSee('Daisy Kit v5 Workbench')
        ->assertSee('Component modules')
        ->assertCount('nav a.btn', 11)
        ->assertCount('[data-daisy-kit-module]', 0)
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues(1);

    $this->visit('/')->on()->mobile()
        ->assertCount('nav a.btn', 11)
        ->assertCount('[data-daisy-kit-module]', 0)
        ->assertScript('document.documentElement.scrollWidth <= window.innerWidth')
        ->assertScript('window.innerWidth <= 430');
})->group('browser');

it('composes the Table page with host DaisyUI primitives across themes and responsive widths', function (): void {
    $page = $this->visit('/table')->on()->desktop()
        ->waitForEvent('networkidle')
        ->wait(1)
        ->assertNoSmoke();

    foreach ([320, 768, 1024, 1440] as $width) {
        $page->resize($width, 900);

        $diagnostics = $page->script(<<<'JS'
                (() => {
                    const roots = [...document.querySelectorAll('[data-daisy-kit-module]')];
                    const modules = [['table', '[data-daisy-kit-module="table"] button']];
                    const controls = [
                        document.querySelector('[data-daisy-kit-table-filter]'),
                    ];
                    const failures = [];

                    roots.forEach((root) => {
                        const actionOnlyPreview = root.dataset.daisyKitModule === 'file-preview'
                            && root.dataset.daisyKitLayout === 'action-only';
                        if (!actionOnlyPreview && !root.classList.contains('card')) {
                            failures.push(`root:${root.dataset.daisyKitModule}:missing-card`);
                        }
                    });
                    modules.forEach(([module, selector]) => {
                        const button = document.querySelector(selector);
                        if (!button) failures.push(`button:${module}:missing`);
                        else if (!button.classList.contains('btn')) failures.push(`button:${module}:missing-btn`);
                        else {
                            const style = getComputedStyle(button);
                            if (Number.parseFloat(style.height) < 32 || style.paddingInlineStart === '0px' || style.borderTopStyle === 'none') {
                                failures.push(`button:${module}:unstyled`);
                            }
                        }
                    });
                    controls.forEach((control) => {
                        if (!control) failures.push('control:missing');
                        else if (!control.classList.contains('input')) failures.push('control:missing-input');
                        else if (getComputedStyle(control).borderTopStyle === 'none') failures.push('control:unstyled');
                    });
                    if (document.documentElement.scrollWidth > window.innerWidth) failures.push(`responsive:overflow:${document.documentElement.scrollWidth}>${window.innerWidth}`);

                    return failures;
                })()
                JS);

        expect($diagnostics)->toBe([]);

        $page
            ->assertNoAccessibilityIssues(1);
    }

    foreach (['light', 'dark'] as $theme) {
        $page->script("document.documentElement.dataset.theme = '{$theme}';");

        $page->assertScript(<<<'JS'
                (() => {
                    const table = document.querySelector('[data-daisy-kit-module="table"]');
                    const tablePage = table.querySelector('[data-daisy-kit-table-page-status]');
                    const tableResults = table.querySelector('[data-daisy-kit-table-results]');
                    const tableApply = document.querySelector('[data-daisy-kit-table-apply-filters]');

                    return table.classList.contains('bg-base-100')
                        && table.classList.contains('border-base-300')
                        && tablePage.classList.contains('text-base-content/70')
                        && tableResults.classList.contains('text-base-content/70')
                        && tableApply.classList.contains('btn-primary')
                        && getComputedStyle(table).backgroundColor !== 'rgba(0, 0, 0, 0)';
                })()
                JS);
    }
})->group('browser');

it('applies the Workbench server filters only on request', function (): void {
    $table = '#server-queue-table';

    $this->visit('/table')->on()->desktop()
        ->waitForEvent('networkidle')
        ->wait(1)
        ->fill("{$table} [data-daisy-kit-table-filter=customer]", 'Maison')
        ->assertScript("document.querySelector('{$table} [data-daisy-kit-table-apply-filters]').disabled === false")
        ->assertCount("{$table} tbody tr", 3)
        ->click("{$table} [data-daisy-kit-table-apply-filters]")
        ->wait(1)
        ->assertCount("{$table} tbody tr", 1)
        ->assertSee('CASE-1044')
        ->assertNoSmoke();
})->group('browser');

it('uses the remote Combobox in a native Laravel review form', function (): void {
    $combobox = '[data-daisy-kit-module="combobox"]';

    $this->visit('/combobox')->on()->desktop()
        ->waitForEvent('networkidle')
        ->wait(1)
        ->fill("{$combobox} [data-daisy-kit-combobox-input]", 'Grace')
        ->wait(1)
        ->assertSee('Grace Hopper')
        ->click("{$combobox} [role=option]")
        ->assertScript("document.querySelector('{$combobox} input[name=\"reviewers[]\"][value=grace]') !== null")
        ->click('button[type=submit]')
        ->waitForEvent('networkidle')
        ->assertSee('The review assignment was saved.')
        ->assertNoSmoke();
})->group('browser');

it('anchors Truncate disclosure to its ellipsis and supports pinned light dismiss', function (): void {
    $trigger = '[data-daisy-kit-truncate-reveal][aria-label^="Show Grace"]';
    $popover = '[data-daisy-kit-module="truncate"]:has([aria-label^="Show Grace"]) [data-daisy-kit-truncate-popover]';
    $page = $this->visit('/truncate')->on()->desktop()
        ->waitForEvent('networkidle')
        ->wait(1)
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues(1)
        ->assertScript("document.querySelector('{$trigger}').hidden === false");

    $page->script("document.querySelector('{$trigger}').dispatchEvent(new PointerEvent('pointerenter'))");
    $page->wait(1)
        ->assertScript("document.querySelector('{$popover}').matches(':popover-open')")
        ->assertScript(<<<'JS'
            (() => {
                const trigger = document.querySelector('[data-daisy-kit-truncate-reveal][aria-label^="Show Grace"]').getBoundingClientRect();
                const popover = document.querySelector('[data-daisy-kit-module="truncate"]:has([aria-label^="Show Grace"]) [data-daisy-kit-truncate-popover]').getBoundingClientRect();
                const horizontalGap = Math.min(Math.abs(trigger.left - popover.left), Math.abs(trigger.right - popover.right));
                const verticalGap = Math.min(Math.abs(trigger.bottom - popover.top), Math.abs(trigger.top - popover.bottom));

                return horizontalGap < 32
                    && verticalGap < 32
                    && popover.top > 0
                    && popover.left > 0;
            })()
            JS)
        ->click($trigger)
        ->assertScript("document.querySelector('{$popover}').dataset.daisyKitTruncatePinned === 'true'")
        ->assertScript("document.querySelector('{$popover}').dataset.daisyKitTruncateBackdrop === 'true'")
        ->keys($trigger, 'Escape')
        ->assertScript("!document.querySelector('{$popover}').matches(':popover-open')")
        ->assertNoSmoke();
})->group('browser');

it('mounts the map without a browser CSP violation', function (): void {
    $this->visit('/_daisy-kit-test/csp/map')
        ->assertSee('Daisy Kit CSP Map')
        ->waitForEvent('networkidle')
        ->wait(1)
        ->assertScript("document.querySelector('[data-daisy-kit-module=map]').dataset.daisyKitState === 'ready'")
        ->assertNoSmoke();
})->group('browser');

it('runs the four Map product scenarios without host-specific map logic', function (): void {
    $page = $this->visit('/map')->on()->desktop()
        ->waitForEvent('networkidle')
        ->wait(2)
        ->assertNoSmoke()
        ->assertCount('[data-daisy-kit-module="map"]', 4)
        ->assertScript("Array.from(document.querySelectorAll('[data-daisy-kit-module=map]')).every((root) => root.dataset.daisyKitState === 'ready')")
        ->assertScript("Array.from(document.querySelectorAll('[data-daisy-kit-map-measurement]')).every((output) => output.hidden)")
        ->assertCount('#map-layers [data-daisy-kit-map-layer]', 3)
        ->assertScript("Boolean(document.querySelector('#map-cluster .marker-cluster'))")
        ->assertCount('#map-drawing [data-daisy-kit-map-object-type]', 1)
        ->assertCount('#map-drawing [data-daisy-kit-map-draw-layer]', 1)
        ->assertCount('#map-drawing [data-daisy-kit-map-mode="spatial-select"]', 1)
        ->assertScript("Array.from(document.querySelectorAll('#map-controlled .leaflet-tile')).every((tile) => !tile.src.includes('tile.openstreetmap.org'))")
        ->click('#map-drawing [data-daisy-kit-map-menu] summary')
        ->click('#map-drawing [data-daisy-kit-map-mode="point"]')
        ->assertScript("document.querySelector('#map-drawing [data-daisy-kit-map-mode=point]').getAttribute('aria-pressed') === 'true'")
        ->assertScript("document.querySelector('#map-controlled [data-daisy-kit-map-geolocate]') instanceof HTMLButtonElement");

    foreach ([320, 390, 768, 1024, 1440] as $width) {
        $page->resize($width, 1000)
            ->assertScript('document.documentElement.scrollWidth <= window.innerWidth')
            ->assertScript("Array.from(document.querySelectorAll('[data-daisy-kit-module=map]')).every((root) => { const viewport = root.querySelector('.daisy-kit-map__viewport').getBoundingClientRect(); return Array.from(root.querySelectorAll('.leaflet-control-container, [data-daisy-kit-map-menu], [data-daisy-kit-map-measurement], [data-daisy-kit-map-active-mode]')).every((control) => control.hidden || (control.getBoundingClientRect().left >= viewport.left && control.getBoundingClientRect().right <= viewport.right)); })")
            ->assertNoSmoke();
    }
})->group('browser');

it('keeps Blueprint controls outside its inert SVG and supports keyboard selection', function (): void {
    $this->visit('/blueprint')->on()->desktop()
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

it('persists value-backed Blueprint structure through history and remounts', function (): void {
    $blueprint = '[data-daisy-kit-module="blueprint"]';
    $hiddenGraph = "JSON.parse(document.querySelector('{$blueprint} [data-daisy-kit-blueprint-value]').value)";

    $this->visit('/blueprint')->on()->desktop()
        ->waitForEvent('networkidle')
        ->wait(1)
        ->assertNoSmoke()
        ->click("{$blueprint} [data-daisy-kit-blueprint-structure=add-node]")
        ->assertCount("{$blueprint} [data-daisy-kit-blueprint-node-control]", 3)
        ->assertScript("{$hiddenGraph}.nodes.some((node) => node.id === 'node-3')")
        ->click("{$blueprint} [data-daisy-kit-blueprint-history=undo]")
        ->assertCount("{$blueprint} [data-daisy-kit-blueprint-node-control]", 2)
        ->click("{$blueprint} [data-daisy-kit-blueprint-history=redo]")
        ->assertCount("{$blueprint} [data-daisy-kit-blueprint-node-control]", 3)
        ->click("{$blueprint} [data-daisy-kit-blueprint-node-control][data-node-id=source]")
        ->select("{$blueprint} [data-daisy-kit-blueprint-transition-target]", 'destination')
        ->click("{$blueprint} [data-daisy-kit-blueprint-structure=add-transition]")
        ->assertScript("{$hiddenGraph}.edges.some((edge) => edge.source === 'source' && edge.target === 'destination')")
        ->click("{$blueprint} [data-daisy-kit-blueprint-node-control][data-node-id=node-3]")
        ->click("{$blueprint} [data-daisy-kit-blueprint-structure=remove-node]")
        ->assertCount("{$blueprint} [data-daisy-kit-blueprint-node-control]", 2)
        ->assertScript("{$hiddenGraph}.nodes.map((node) => node.id).join(',') === 'source,destination'")
        ->assertScript("{$hiddenGraph}.edges.length === 1")
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
            $frame->assertSee('Daisy Kit File Preview');
        });
})->group('browser');

it('mounts the nine strict modules without a CSP violation', function (): void {
    $this->visit('/_daisy-kit-test/csp/strict')
        ->assertSee('Daisy Kit strict CSP fixture')
        ->waitForEvent('networkidle')
        ->wait(1)
        ->assertCount('[data-daisy-kit-module]', 9)
        ->assertScript("Array.from(document.querySelectorAll('[data-daisy-kit-module]')).every((root) => ['empty', 'ready'].includes(root.dataset.daisyKitState))")
        ->assertNoSmoke();
})->group('browser');

it('mounts Signature and Transfer List under their documented CSP policy', function (): void {
    $this->visit('/_daisy-kit-test/csp/dependency-styles')
        ->assertSee('Daisy Kit dependency style CSP fixture')
        ->waitForEvent('networkidle')
        ->wait(1)
        ->assertCount('[data-daisy-kit-module]', 2)
        ->assertScript("Array.from(document.querySelectorAll('[data-daisy-kit-module]')).every((root) => root.dataset.daisyKitState === 'ready')")
        ->assertNoSmoke();
})->group('browser');
