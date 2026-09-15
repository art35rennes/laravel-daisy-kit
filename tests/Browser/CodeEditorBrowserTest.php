<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Vite;

it('edits code with history and native reset while read-only content stays selectable', function (): void {
    $page = $this->visit('/code-editor')->waitForEvent('networkidle');
    $page->assertCount('.cm-editor', 2)
        ->fill('form .cm-content', 'const answer = 42;')
        ->assertScript('new FormData(document.querySelector("form")).get("code") === "const answer = 42;"')
        ->click('form [data-code-editor-action="undo"]')
        ->assertScript('document.querySelector("textarea").value.includes("enabled")')
        ->click('form [data-code-editor-action="redo"]')
        ->assertScript('document.querySelector("textarea").value === "const answer = 42;"')
        ->click('Reset configuration')
        ->assertScript('document.querySelector("textarea").value.includes("enabled")')
        ->assertScript('document.querySelector("[data-theme=dark] .cm-content").contentEditable === "false"')
        ->assertScript('document.querySelector("[data-theme=dark] .cm-content").tabIndex === 0')
        ->assertNoSmoke();
})->group('browser');

it('searches and replaces code and preserves keyboard exit from the expanded editor', function (): void {
    $page = $this->visit('/code-editor')->waitForEvent('networkidle');
    $page->click('form [data-code-editor-action="expand"]')
        ->click('form [data-code-editor-action="search"]')
        ->fill('form .cm-search input[name="search"]', 'enabled')
        ->fill('form .cm-search input[name="replace"]', 'active')
        ->click('form .cm-search button[name="replaceAll"]')
        ->assertScript('document.querySelector("textarea").value.includes("active")')
        ->keys('form .cm-search input[name="search"]', 'Escape')
        ->assertCount('form .cm-search', 0)
        ->assertCount('.daisy-kit-code-editor--expanded', 1)
        ->keys('form .cm-content', 'Escape')
        ->assertCount('.daisy-kit-code-editor--expanded', 0)
        ->keys('form .cm-content', 'Tab')
        ->assertScript('!document.activeElement.closest(".cm-editor")')
        ->assertNoSmoke();
})->group('browser');

it('preserves local themes and bounds rendering for large documents on mobile', function (): void {
    $page = $this->visit('/code-editor')->on()->mobile()->waitForEvent('networkidle');
    $entry = json_encode(Vite::asset('../dist/code-editor.js'), JSON_THROW_ON_ERROR);
    $result = $page->script(str_replace('__ENTRY__', $entry, <<<'JS'
        (async () => {
            const module = await import(__ENTRY__);
            const roots = document.querySelectorAll('[data-daisy-kit-module="code-editor"]');
            const editor = module.getInstance(roots[0]);
            const editorDOM = roots[0].querySelector('.cm-editor');
            const originalColor = getComputedStyle(editorDOM).backgroundColor;
            const localColor = getComputedStyle(roots[1].querySelector('.cm-editor')).backgroundColor;
            roots[0].dataset.theme = 'dark';
            const themeChanged = getComputedStyle(editorDOM).backgroundColor === localColor && originalColor !== localColor;
            const samples = [];
            for (const size of [100_000, 1_000_000]) {
                const value = 'const answer = 42;\n'.repeat(Math.ceil(size / 19)).slice(0, size);
                const start = performance.now();
                editor.setValue(value);
                await new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve)));
                samples.push({ size, duration: performance.now() - start, lines: roots[0].querySelectorAll('.cm-line').length, preserved: editor.getValue() === value });
            }
            const sameDOM = roots[0].querySelector('.cm-editor') === editorDOM;
            module.unmount(roots[0]);
            return { themeChanged, sameDOM, samples, restored: !roots[0].querySelector('.cm-editor') && roots[0].querySelector('textarea').value.length === 1_000_000 };
        })()
        JS));
    expect($result['themeChanged'])->toBeTrue()
        ->and($result['sameDOM'])->toBeTrue()
        ->and($result['restored'])->toBeTrue();
    foreach ($result['samples'] as $sample) {
        expect($sample['preserved'])->toBeTrue()
            ->and($sample['lines'])->toBeLessThan(500)
            ->and($sample['duration'])->toBeLessThan(5000);
    }
    $page->assertScript('document.documentElement.scrollWidth <= window.innerWidth')->assertNoSmoke();
})->group('browser');

it('offers local JavaScript completion and folds JSON without changing the value', function (): void {
    $page = $this->visit('/code-editor')->waitForEvent('networkidle');
    $page->click('form .cm-foldGutter [title="Fold line"]')
        ->assertCount('form .cm-foldPlaceholder', 1)
        ->assertScript('document.querySelector("textarea").value.includes("enabled")');
    $entry = json_encode(Vite::asset('../dist/code-editor.js'), JSON_THROW_ON_ERROR);
    $page->script(str_replace('__ENTRY__', $entry, <<<'JS'
        (async () => {
            const module = await import(__ENTRY__);
            const editor = module.getInstance(document.querySelector('[data-daisy-kit-module="code-editor"]'));
            await editor.setLanguage('javascript');
            editor.setValue('const answer = 42;\nans');
        })()
        JS));
    $page->keys('form .cm-content', ['Control+End', 'Control+Space'])
        ->assertSee('answer')
        ->assertCount('.cm-tooltip-autocomplete', 1);
    // CodeMirror ignores completion keys during its 75 ms accidental-acceptance guard.
    $page->wait(0.1);
    $page->keys('form .cm-content', 'Enter')
        ->assertScript('document.querySelector("textarea").value.endsWith("answer")')
        ->assertNoSmoke();
})->group('browser');

it('keeps an enlarged editor inside a mobile viewport and toggles its controls', function (): void {
    $page = $this->visit('/code-editor')->on()->mobile()->waitForEvent('networkidle');
    $page->click('form [data-code-editor-action="expand"]')
        ->assertSee('Collapse editor')
        ->click('form [data-code-editor-action="search"]')
        ->assertSee('Close search')
        ->click('form [data-code-editor-action="search"]')
        ->assertCount('form .cm-search', 0)
        ->assertScript(<<<'JS'
            (() => {
                const root = document.querySelector('.daisy-kit-code-editor--expanded');
                const shell = root.querySelector('.daisy-kit-code-editor__shell');
                const status = root.querySelector('.daisy-kit-code-editor__status').getBoundingClientRect();
                const editor = root.querySelector('.cm-editor').getBoundingClientRect();
                const bounds = root.getBoundingClientRect();
                const title = root.querySelector('legend').getBoundingClientRect();
                return status.bottom <= innerHeight && status.right <= innerWidth
                    && title.top >= bounds.top && title.bottom < editor.top
                    && editor.bottom <= status.top + 1 && editor.height > 50
                    && getComputedStyle(shell).outlineStyle === 'none'
                    && document.documentElement.scrollWidth <= innerWidth;
            })()
            JS)
        ->click('form [data-code-editor-action="expand"]')
        ->assertSee('Expand editor')
        ->assertCount('.daisy-kit-code-editor--expanded', 0)
        ->assertNoSmoke();
})->group('browser');

it('collapses using the header minus button and backdrop and pairs typed delimiters', function (): void {
    $page = $this->visit('/code-editor')->waitForEvent('networkidle');
    $page->click('form [data-code-editor-action="expand"]')
        ->click('form [data-code-editor-minimize]')
        ->assertCount('.daisy-kit-code-editor--expanded', 0)
        ->click('form [data-code-editor-action="expand"]')
        ->assertScript('document.elementFromPoint(2, 2).matches("button.daisy-kit-code-editor__backdrop:not([hidden])")');
    $page->script('document.elementFromPoint(2, 2).click()');
    $page->assertCount('.daisy-kit-code-editor--expanded', 0)
        ->fill('form .cm-content', '')
        ->keys('form .cm-content', '[')
        ->assertScript('document.querySelector("textarea").value === "[]"')
        ->keys('form .cm-content', 'Enter')
        ->assertScript('document.querySelector("textarea").value.includes("\\n") && document.querySelector("textarea").value.endsWith("]")')
        ->assertNoSmoke();
})->group('browser');

it('suggests words from the JSON document and exposes bulk folding actions', function (): void {
    $page = $this->visit('/code-editor')->waitForEvent('networkidle');
    $page->click('form [data-code-editor-action="fold-all"]')
        ->assertCount('form .cm-foldPlaceholder', 1)
        ->click('form [data-code-editor-action="unfold-all"]')
        ->assertCount('form .cm-foldPlaceholder', 0)
        ->fill('form .cm-content', "{\n  \"existingKey\": 1,\n  \"exis")
        ->keys('form .cm-content', 'Control+End')
        ->click('form [data-code-editor-action="complete"]')
        ->assertCount('.cm-tooltip-autocomplete', 1);
    $page->wait(0.1);
    $page->keys('form .cm-content', 'Enter')
        ->assertScript('document.querySelector("textarea").value.endsWith("existingKey")')
        ->assertNoSmoke();
})->group('browser');
