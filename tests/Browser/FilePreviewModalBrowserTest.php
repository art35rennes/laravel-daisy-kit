<?php

declare(strict_types=1);

it('contains a DOCX preview in its modal and restores focus after dismissal', function (): void {
    $root = '[data-daisy-kit-module="file-preview"][aria-label="Product brief.docx"]';
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
                const modalBox = dialog.querySelector('[data-daisy-kit-file-preview-modal-box]');
                const frame = dialog.querySelector('[data-daisy-kit-file-preview-frame]');

                return dialog.open
                    && getComputedStyle(dialog).visibility !== 'hidden'
                    && root.dataset.daisyKitPreviewOpen === 'true'
                    && modalBox.contains(frame)
                    && dialog.contains(document.activeElement);
            })()
            JS)
        ->click("{$dialog} [data-daisy-kit-file-preview-zoom=\"in\"]")
        ->assertScript("document.querySelector('{$root}').dataset.daisyKitZoom === '110'")
        ->keys($dialog, 'Escape')
        ->assertScript(<<<JS
            (() => {
                const dialog = document.querySelector('{$dialog}');
                const root = document.querySelector('{$root}');
                const trigger = document.querySelector('{$trigger}');

                return !dialog.open
                    && root.dataset.daisyKitPreviewOpen === 'false'
                    && document.activeElement === trigger;
            })()
            JS)
        ->click($trigger)
        ->click("{$dialog} header [data-daisy-kit-file-preview-close-preview]")
        ->assertScript(<<<JS
            (() => {
                const dialog = document.querySelector('{$dialog}');
                const root = document.querySelector('{$root}');
                const trigger = document.querySelector('{$trigger}');

                return !dialog.open
                    && root.dataset.daisyKitPreviewOpen === 'false'
                    && document.activeElement === trigger;
            })()
            JS)
        ->assertNoAccessibilityIssues(1);
})->group('browser');
