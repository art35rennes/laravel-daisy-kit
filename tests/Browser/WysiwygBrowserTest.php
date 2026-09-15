<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Vite;

it('formats, submits and resets rich text while preserving read-only content', function (): void {
    $page = $this->visit('/wysiwyg')->waitForEvent('networkidle');

    $page->assertCount('.daisy-kit-wysiwyg__editor', 2)
        ->fill('form .daisy-kit-wysiwyg__editor', 'Customer outcome')
        ->assertScript('new FormData(document.querySelector("form")).get("article_body").includes("Customer outcome")')
        ->keys('form .daisy-kit-wysiwyg__editor', 'Control+A')
        ->click('form [data-trix-attribute="bold"]')
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

it('keeps the medium editor readable and contains the link dialog', function (int $width): void {
    $page = $this->visit('/wysiwyg')->resize($width, 900)->waitForEvent('networkidle');

    $page->click('form [data-trix-action="link"]');
    $page->page()->waitForFunction('() => document.querySelector("form .daisy-kit-wysiwyg__dialog").classList.contains("trix-active")');

    $page->assertScript('getComputedStyle(document.querySelector("form .daisy-kit-wysiwyg__editor")).fontSize === "16px"')
        ->assertScript('getComputedStyle(document.querySelector("form .daisy-kit-wysiwyg__toolbar .btn")).fontSize === "14px"')
        ->assertScript('document.querySelector("form .daisy-kit-wysiwyg__dialog .input").getBoundingClientRect().width >= document.querySelector("form .daisy-kit-wysiwyg__dialog").getBoundingClientRect().width / 2')
        ->assertScript('document.querySelector("form .daisy-kit-wysiwyg__dialog").getBoundingClientRect().right <= document.querySelector("form .daisy-kit-wysiwyg").getBoundingClientRect().right')
        ->assertScript('document.documentElement.scrollWidth <= window.innerWidth')
        ->assertNoSmoke();
})->with([
    '320 pixels' => 320,
    '768 pixels' => 768,
    '1280 pixels' => 1280,
])->group('browser');

it('wraps rich text controls inside a narrow host card', function (): void {
    $page = $this->visit('/wysiwyg')->resize(390, 844)->waitForEvent('networkidle');
    $page->script("document.querySelector('form').style.width = '240px'");

    $page->assertScript("document.querySelector('form').scrollWidth <= 240")
        ->assertNoSmoke();
})->group('browser');
