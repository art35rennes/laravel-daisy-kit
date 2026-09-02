<?php

declare(strict_types=1);

it('uses hierarchical selection, manual search and keyboard navigation', function (): void {
    $page = $this->visit('/tree?lang=fr')->on()->desktop()
        ->waitForEvent('networkidle')
        ->assertCount('[data-daisy-kit-module="tree"]', 4)
        ->assertNoSmoke()
        ->assertSee('2 sélectionné(s) · 1 visible(s) · 1 masqué(s)')
        ->fill('#permissions-tree [data-daisy-kit-tree-search]', 'Update')
        ->assertScript("document.querySelector('#permissions-tree [data-daisy-kit-tree-node=projects-records-read]').hidden === false")
        ->click('#permissions-tree [data-tree-command=applySearch]')
        ->assertScript("document.querySelector('#permissions-tree [data-daisy-kit-tree-node=projects-records-read]').hidden === true")
        ->assertSee('2 sélectionné(s) · 0 visible(s) · 2 masqué(s)')
        ->click('#permissions-tree [data-tree-command=clearSearch]')
        ->click('#permissions-tree [data-daisy-kit-tree-node=projects-records] > .daisy-kit-tree__row')
        ->assertScript("JSON.parse(document.querySelector('#permissions-tree [data-daisy-kit-tree-value]').value).length === 5")
        ->keys('#classification-tree [data-daisy-kit-tree-node=documentation]', ['Home', 'ArrowRight'])
        ->assertScript("document.activeElement.dataset.daisyKitTreeNode === 'getting-started'")
        ->keys('#classification-tree [data-daisy-kit-tree-node=getting-started]', 'ArrowDown')
        ->keys('#classification-tree [data-daisy-kit-tree-node=api-reference]', 'Enter')
        ->assertScript("document.querySelector('#classification-tree [data-daisy-kit-tree-value]').value === '[\"api-reference\"]'");

    $page->assertNoAccessibilityIssues(1)->assertNoSmoke();
})->group('browser');

it('loads and selects a paginated regional branch without touching its siblings', function (): void {
    $this->visit('/tree')->on()->desktop()
        ->waitForEvent('networkidle')
        ->click('#catalogue-tree [data-daisy-kit-tree-node=west] > .daisy-kit-tree__row [data-tree-action=toggle]')
        ->waitForEvent('networkidle')
        ->assertScript("document.querySelector('#catalogue-tree [data-daisy-kit-tree-node=west]').getAttribute('aria-expanded') === 'true'")
        ->assertScript("document.querySelectorAll('#catalogue-tree [data-daisy-kit-tree-node^=west-]').length === 10")
        ->assertScript("document.querySelector('#catalogue-tree [data-daisy-kit-tree-node^=north-]') === null")
        ->click('#catalogue-tree [data-tree-command=selectVisible]')
        ->assertScript("document.querySelector('#catalogue-tree [data-daisy-kit-tree-value]').value === '[\"west-1\",\"west-2\"]'")
        ->click('#catalogue-tree [data-tree-action=load-more]')
        ->waitForEvent('networkidle')
        ->assertScript("document.querySelectorAll('#catalogue-tree > [data-daisy-kit-content] [data-daisy-kit-tree-node^=west-]').length === 20")
        ->assertDontSee('This branch could not be loaded.')
        ->assertNoSmoke();
})->group('browser');

it('aligns search controls and highlights standard search matches', function (): void {
    $this->visit('/tree')->on()->desktop()
        ->waitForEvent('networkidle')
        ->assertScript(<<<'JS'
            (() => {
                const toolbar = document.querySelector('#permissions-tree .daisy-kit-tree__toolbar');
                const input = toolbar.querySelector('input');
                const buttons = [...toolbar.querySelectorAll(':scope > button')];
                return buttons.every((button) => Math.abs(button.getBoundingClientRect().bottom - input.getBoundingClientRect().bottom) <= 1);
            })()
            JS)
        ->fill('#classification-tree [data-daisy-kit-tree-search]', 'Plat')
        ->wait(1)
        ->assertScript("document.querySelector('#classification-tree [data-daisy-kit-tree-node=team-0] .daisy-kit-tree__title mark')?.textContent.toLowerCase() === 'plat'")
        ->assertNoAccessibilityIssues(1)
        ->assertNoSmoke();
})->group('browser');

it('keeps controls accessible inside the viewport in both themes', function (string $theme): void {
    $page = $this->visit('/tree?theme='.$theme)->on()->desktop()->waitForEvent('networkidle');

    foreach ([1440, 390] as $width) {
        $page->resize($width, $width === 390 ? 844 : 1000)
            ->assertScript('document.documentElement.scrollWidth <= window.innerWidth')
            ->assertScript("Array.from(document.querySelectorAll('[data-tree-action=toggle]')).filter((button) => button.checkVisibility()).every((button) => button.getBoundingClientRect().width >= 32 && button.getBoundingClientRect().height >= 32)")
            ->assertNoAccessibilityIssues(1)
            ->assertNoSmoke();
    }
})->with(['light', 'dark'])->group('browser');
