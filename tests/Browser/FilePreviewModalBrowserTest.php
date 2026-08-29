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

it('keeps media cards and their modal inside every supported viewport', function (): void {
    $root = '[data-daisy-kit-module="file-preview"][aria-label="Product illustration.svg"]';
    $dialog = "{$root} [data-daisy-kit-file-preview-modal]";

    $page = $this->visit('/')->on()->desktop()
        ->waitForEvent('networkidle')
        ->wait(1)
        ->assertNoSmoke();

    foreach ([320, 390, 768, 1024, 1440] as $width) {
        $page->resize($width, 900)
            ->click("{$root} [data-daisy-kit-file-preview-open-preview]")
            ->assertScript(<<<JS
                (() => {
                    const root = document.querySelector('{$root}');
                    const dialog = document.querySelector('{$dialog}');
                    const box = dialog.querySelector('[data-daisy-kit-file-preview-modal-box]');
                    const content = dialog.querySelector('[data-daisy-kit-file-preview-modal-content]');
                    const frame = dialog.querySelector('[data-daisy-kit-file-preview-frame]');
                    const boxBounds = box.getBoundingClientRect();

                    return document.documentElement.scrollWidth <= window.innerWidth
                        && root.getBoundingClientRect().right <= window.innerWidth
                        && boxBounds.left >= 0
                        && boxBounds.right <= window.innerWidth
                        && content.contains(frame)
                        && frame.getBoundingClientRect().width <= boxBounds.width;
                })()
                JS)
            ->withinFrame("{$dialog} [data-daisy-kit-file-preview-frame]", function ($frame): void {
                $frame->assertScript("document.querySelector('img')?.getBoundingClientRect().width <= document.documentElement.clientWidth");
            })
            ->click("{$dialog} header [data-daisy-kit-file-preview-close-preview]");
    }

    foreach (['light', 'dark'] as $theme) {
        $page->script("document.documentElement.dataset.theme = '{$theme}';");
        $page->assertScript(<<<JS
            (() => {
                const root = document.querySelector('{$root}');
                const style = getComputedStyle(root);

                return root.classList.contains('bg-base-100')
                    && root.classList.contains('border-base-300')
                    && style.backgroundColor !== 'rgba(0, 0, 0, 0)';
            })()
            JS);
    }
})->group('browser');
