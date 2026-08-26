<?php

declare(strict_types=1);

it('opens a file preview in a modal and restores its trigger after keyboard dismissal', function (): void {
    $root = '[data-daisy-kit-module="file-preview"]';
    $dialog = "{$root} dialog[data-daisy-kit-file-preview-modal]";
    $trigger = "{$root} [data-daisy-kit-file-preview-open-preview]";

    $this->visit('/')->on()->desktop()
        ->waitForEvent('networkidle')
        ->wait(1)
        ->assertNoSmoke()
        ->assertScript("document.querySelector('{$root}').dataset.daisyKitState === 'ready'")
        ->assertScript("document.querySelector('{$dialog}').open === false")
        ->click($trigger)
        ->assertScript(<<<JS
            (() => {
                const dialog = document.querySelector('{$dialog}');
                const root = document.querySelector('{$root}');

                return dialog.open
                    && getComputedStyle(dialog).visibility !== 'hidden'
                    && getComputedStyle(dialog).pointerEvents !== 'none'
                    && root.dataset.daisyKitPreviewOpen === 'true'
                    && dialog.contains(document.activeElement);
            })()
            JS)
        ->click("{$dialog} [data-daisy-kit-file-preview-zoom=\"in\"]")
        ->assertScript("document.querySelector('{$root}').dataset.daisyKitZoom === '125'")
        ->keys($dialog, 'Escape')
        ->assertScript(<<<JS
            (() => {
                const dialog = document.querySelector('{$dialog}');
                const root = document.querySelector('{$root}');
                const trigger = document.querySelector('{$trigger}');

                return !dialog.open
                    && root.dataset.daisyKitPreviewOpen === 'true'
                    && document.activeElement === trigger;
            })()
            JS)
        ->click("{$root} [data-daisy-kit-file-preview-layout]")
        ->assertScript(<<<JS
            (() => {
                const root = document.querySelector('{$root}');
                const control = root.querySelector('[data-daisy-kit-file-preview-layout]');

                return root.dataset.daisyKitLayout === 'expanded'
                    && control.getAttribute('aria-pressed') === 'true';
            })()
            JS)
        ->click($trigger)
        ->click("{$dialog} [data-daisy-kit-file-preview-close-preview]")
        ->assertScript(<<<JS
            (() => {
                const dialog = document.querySelector('{$dialog}');
                const root = document.querySelector('{$root}');
                const trigger = document.querySelector('{$trigger}');

                return !dialog.open
                    && root.dataset.daisyKitPreviewOpen === 'true'
                    && document.activeElement === trigger;
            })()
            JS)
        ->assertNoAccessibilityIssues(1);
})->group('browser');
