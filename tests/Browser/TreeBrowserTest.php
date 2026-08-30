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

it('recovers a failed lazy branch without losing selected roots', function (): void {
    $this->visit('/tree')->on()->desktop()
        ->waitForEvent('networkidle')
        ->click('#catalogue-tree [data-daisy-kit-tree-node=west] > .daisy-kit-tree__row')
        ->click('#catalogue-tree [data-daisy-kit-tree-node=west] > .daisy-kit-tree__row [data-tree-action=toggle]')
        ->assertSee('This branch could not be loaded.')
        ->click('#catalogue-tree [data-tree-action=retry]')
        ->waitForEvent('networkidle')
        ->assertScript("document.querySelector('#catalogue-tree [data-daisy-kit-tree-node=west]').getAttribute('aria-expanded') === 'true'")
        ->assertScript("document.querySelector('#catalogue-tree [data-daisy-kit-tree-value]').value === '[\"west\"]'")
        ->fill('#catalogue-tree [data-daisy-kit-tree-search]', 'North')
        ->click('#catalogue-tree [data-tree-command=applySearch]')
        ->waitForEvent('networkidle')
        ->assertScript("document.querySelector('#catalogue-tree [data-daisy-kit-tree-value]').value === '[\"west\"]'")
        ->assertSee('1 selected · 0 visible · 1 hidden');
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
