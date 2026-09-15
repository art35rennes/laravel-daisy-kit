<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Vite;

it('formats, submits and resets rich text while preserving read-only content', function (): void {
    $page = $this->visit('/wysiwyg')->waitForEvent('networkidle');

    $page->assertCount('.daisy-kit-wysiwyg__editor', 2)
        ->fill('form .daisy-kit-wysiwyg__editor', 'Customer outcome')
        ->keys('form .daisy-kit-wysiwyg__editor', 'Control+A')
        ->click('form [data-trix-attribute="bold"]')
        ->assertScript('new FormData(document.querySelector("form")).get("article_body").includes("Customer outcome")')
        ->assertScript('document.querySelector("input[name=article_body]").value.includes("<strong>")')
        ->click('Reset article')
        ->assertScript('document.querySelector("input[name=article_body]").value.includes("Release notes")')
        ->assertScript('document.querySelector("[data-theme=dark] trix-editor").contentEditable === "false"')
        ->assertNoSmoke();
})->group('browser');

it('sanitizes host values, exposes Trix and remains responsive under its CSP policy', function (): void {
    $page = $this->visit('/wysiwyg')->on()->mobile()->waitForEvent('networkidle');
    $entry = json_encode(Vite::asset('../dist/wysiwyg.js'), JSON_THROW_ON_ERROR);
    $result = $page->script(str_replace('__ENTRY__', $entry, <<<'JS'
        (async () => {
            const module = await import(__ENTRY__);
            const root = document.querySelector('[data-daisy-kit-module="wysiwyg"]');
            const editor = module.getInstance(root);
            const exposesTrix = editor.getTrixEditor() === root.querySelector('trix-editor').editor;
            editor.setValue('<p>Safe</p><img src="/_daisy-kit-test/files/preview.svg" onerror="alert(1)"><script>alert(2)</script><a href="javascript:alert(3)">Link</a>');
            await new Promise((resolve) => requestAnimationFrame(() => requestAnimationFrame(resolve)));
            editor.getTrixEditor().setSelectedRange([0, 0]);
            editor.getTrixEditor().insertHTML('<p>Pasted</p><img src="/_daisy-kit-test/files/preview.svg" onerror="alert(4)"><script>alert(5)</script>');
            await new Promise((resolve) => setTimeout(resolve));
            return {
                exposesTrix,
                globalRemoved: window.Trix === undefined,
                value: editor.getValue(),
            };
        })()
        JS));

    expect($result['exposesTrix'])->toBeTrue()
        ->and($result['globalRemoved'])->toBeTrue()
        ->and($result['value'])->toContain('Safe')
        ->and($result['value'])->toContain('Pasted')
        ->and($result['value'])->not->toMatch('/<script|onerror|javascript:/i');

    $page->assertScript('document.documentElement.scrollWidth <= window.innerWidth')
        ->assertNoAccessibilityIssues(1)
        ->assertNoSmoke();
})->group('browser');

it('uses DaisyUI themes without overflowing supported widths', function (int $width, string $theme): void {
    $page = $this->visit('/wysiwyg')->resize($width, 900)->waitForEvent('networkidle');
    $page->script('document.documentElement.dataset.theme = '.json_encode($theme, JSON_THROW_ON_ERROR));

    $page->assertScript('document.documentElement.scrollWidth <= window.innerWidth')
        ->assertScript('getComputedStyle(document.querySelector(".daisy-kit-wysiwyg__editor")).backgroundColor !== "rgba(0, 0, 0, 0)"')
        ->assertNoSmoke();
})->with([
    '320 light' => [320, 'light'],
    '768 dark' => [768, 'dark'],
    '1440 corporate' => [1440, 'corporate'],
])->group('browser');
