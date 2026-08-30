<?php

declare(strict_types=1);

it('contains a DOCX preview in its modal and restores focus after dismissal', function (): void {
    $root = '[data-daisy-kit-module="file-preview"][aria-label="Product brief.docx"]';
    $pdfRoot = '[data-daisy-kit-module="file-preview"][aria-label="Release overview.pdf"]';
    $pdfDialog = "{$pdfRoot} dialog[data-daisy-kit-file-preview-modal]";
    $dialog = "{$root} dialog[data-daisy-kit-file-preview-modal]";
    $trigger = "{$root} [data-daisy-kit-file-preview-open-preview]";

    $page = $this->visit('/')->on()->desktop()
        ->waitForEvent('networkidle')
        ->assertNoSmoke();

    $page
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
                    && dialog.querySelector('[data-daisy-kit-file-preview-modal-download]')?.download === 'Product brief.docx'
                    && dialog.contains(document.activeElement);
            })()
            JS)
        ->withinFrame("{$dialog} [data-daisy-kit-file-preview-frame]", function ($frame): void {
            $frame->assertScript(<<<'JS'
                (() => {
                    const pages = [...document.querySelectorAll('.docx-wrapper > section.docx')];
                    const scrollingElement = document.scrollingElement;

                    document.documentElement.dataset.initialDocxWidth = String(pages[0]?.getBoundingClientRect().width ?? 0);

                    return pages.length >= 3
                        && scrollingElement.scrollHeight > scrollingElement.clientHeight;
                })()
                JS);
        })
        ->click("{$dialog} [data-daisy-kit-file-preview-zoom=\"fit\"]")
        ->withinFrame("{$dialog} [data-daisy-kit-file-preview-frame]", function ($frame): void {
            $frame->assertScript(<<<'JS'
                (() => {
                    const page = document.querySelector('.docx-wrapper > section.docx');
                    const pageBounds = page?.getBoundingClientRect();

                    return pageBounds
                        && pageBounds.left >= 0
                        && pageBounds.right <= document.documentElement.clientWidth + 1
                        && document.documentElement.scrollWidth <= document.documentElement.clientWidth + 1;
                })()
                JS);
        })
        ->assertScript("(() => { const root = document.querySelector('{$root}'); root.dataset.fitZoom = root.dataset.daisyKitZoom; return Number(root.dataset.fitZoom) >= 25; })()")
        ->click("{$dialog} [data-daisy-kit-file-preview-zoom=\"in\"]")
        ->assertScript("(() => { const root = document.querySelector('{$root}'); return Number(root.dataset.daisyKitZoom) === Number(root.dataset.fitZoom) + 10; })()")
        ->withinFrame("{$dialog} [data-daisy-kit-file-preview-frame]", function ($frame): void {
            $frame->assertScript(<<<'JS'
                (() => {
                    const initialWidth = Number(document.documentElement.dataset.initialDocxWidth);
                    const currentWidth = document.querySelector('.docx-wrapper > section.docx')?.getBoundingClientRect().width ?? 0;

                    return initialWidth > 0 && currentWidth > initialWidth * 1.05;
                })()
                JS);
        })
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
        ->click("{$pdfRoot} [data-daisy-kit-file-preview-open-preview]")
        ->assertScript("document.querySelector('{$pdfDialog}').open")
        ->withinFrame("{$pdfDialog} [data-daisy-kit-file-preview-frame]", function ($frame): void {
            $frame->assertScript(<<<'JS'
                (() => {
                    const pages = [...document.querySelectorAll('[data-daisy-kit-pdf-page]')];
                    const scrollingElement = document.scrollingElement;

                    return pages.length === 3
                        && pages.every((page) => page instanceof HTMLCanvasElement && page.width > 0 && page.height > 0)
                        && scrollingElement.scrollHeight > scrollingElement.clientHeight;
                })()
                JS);
        })
        ->click("{$pdfDialog} header [data-daisy-kit-file-preview-close-preview]")
        ->resize(320, 844)
        ->click($trigger)
        ->click("{$dialog} [data-daisy-kit-file-preview-zoom=\"fit\"]")
        ->assertScript(<<<JS
            (() => {
                const dialog = document.querySelector('{$dialog}');
                const controls = dialog.querySelector('[data-daisy-kit-file-preview-zoom="fit"]').parentElement;

                return document.documentElement.scrollWidth <= window.innerWidth
                    && dialog.querySelector('[data-daisy-kit-file-preview-modal-box]').getBoundingClientRect().right <= window.innerWidth
                    && controls.getBoundingClientRect().right <= dialog.getBoundingClientRect().right;
            })()
            JS)
        ->withinFrame("{$dialog} [data-daisy-kit-file-preview-frame]", function ($frame): void {
            $frame->assertScript('document.documentElement.scrollWidth <= document.documentElement.clientWidth + 1');
        })
        ->click("{$dialog} header [data-daisy-kit-file-preview-close-preview]")
        ->assertNoAccessibilityIssues(1);
})->group('browser');

it('keeps media cards and their modal inside every supported viewport', function (): void {
    $root = '[data-daisy-kit-module="file-preview"][aria-label="Product illustration.svg"]';
    $dialog = "{$root} [data-daisy-kit-file-preview-modal]";
    $video = '[data-daisy-kit-module="file-preview"][aria-label="Preview walkthrough.mp4"]';

    $page = $this->visit('/')->on()->desktop()
        ->waitForEvent('networkidle')
        ->assertNoSmoke();

    $page
        ->assertScript("document.querySelector('{$video}').dataset.daisyKitState === 'ready'")
        ->withinFrame("{$video} [data-daisy-kit-file-preview-frame]", function ($frame): void {
            $frame->assertScript("document.querySelector('video')?.src.startsWith('blob:')");
        });

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
