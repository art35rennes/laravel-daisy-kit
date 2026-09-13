<?php

declare(strict_types=1);

it('keeps the read-only graph unchanged during keyboard selection and view actions', function (): void {
    $page = $this->visit('/_daisy-kit-test/blueprint/read-only')->waitForEvent('networkidle');

    $page->assertScript("document.querySelector('[data-daisy-kit-module=blueprint]').dataset.daisyKitState === 'ready'")
        ->assertCount('[data-daisy-kit-blueprint-structure]', 0)
        ->assertCount('[data-daisy-kit-blueprint-editor]', 0)
        ->assertCount('[data-daisy-kit-blueprint-value-editor]', 0)
        ->assertCount('[data-daisy-kit-blueprint-history]', 0);
    $page->script("window.originalReadOnlyGraph = document.querySelector('[data-daisy-kit-blueprint-value]').value");

    $page->keys('[data-daisy-kit-blueprint-node-control][data-node-id=source]', 'ArrowRight')
        ->keys('[data-daisy-kit-blueprint-node-control][data-node-id=destination]', 'Enter')
        ->assertScript("document.querySelector('[data-node-id=destination][data-daisy-kit-blueprint-node-control]').getAttribute('aria-pressed') === 'true'")
        ->keys('[data-daisy-kit-blueprint-node-control][data-node-id=destination]', ['Delete', 'Backspace'])
        ->click('[data-daisy-kit-blueprint-view=arrange]')
        ->click('[data-daisy-kit-blueprint-view=fit]')
        ->assertCount('[data-daisy-kit-blueprint-node-control]', 2)
        ->assertScript("document.querySelector('[data-daisy-kit-blueprint-value]').value === window.originalReadOnlyGraph")
        ->assertNoAccessibilityIssues(1)
        ->assertNoSmoke();
})->group('browser');
