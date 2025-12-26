<?php

describe('Menu filter interaction', function () {
    it('filters menu items when typing in search input', function () {
        $page = visit('/demo/ui/menu');

        $page->assertSee('Item 1')
            ->assertSee('Item 2')
            ->assertSee('Item 3')
            ->fill('[data-menu-filter-input]', 'Item 1')
            ->waitForText('Item 1')
            ->assertDontSee('Item 2')
            ->assertDontSee('Item 3');
    });

    it('opens parent details when child matches filter', function () {
        $page = visit('/demo/ui/menu');

        // Vérifier qu'un sous-menu avec "Submenu" existe
        $page->fill('[data-menu-filter-input]', 'Submenu')
            ->waitFor(0.5) // Attendre l'animation
            ->assertSee('Submenu');

        // Vérifier que le parent est ouvert (details[open])
        $details = $page->querySelector('details[open]');
        expect($details)->not->toBeNull();
    });

    it('clears filter when Escape key is pressed', function () {
        $page = visit('/demo/ui/menu');

        $page->fill('[data-menu-filter-input]', 'Item 1')
            ->waitForText('Item 1')
            ->assertDontSee('Item 2')
            ->pressKey('Escape')
            ->waitFor(0.3)
            ->assertSee('Item 1')
            ->assertSee('Item 2')
            ->assertSee('Item 3');
    });

    it('filters nested submenus recursively', function () {
        $page = visit('/demo/ui/menu');

        // Chercher un item dans un sous-menu imbriqué
        $page->fill('[data-menu-filter-input]', 'Nested')
            ->waitFor(0.5)
            ->assertSee('Nested');

        // Vérifier que les parents sont ouverts
        $openDetails = $page->querySelectorAll('details[open]');
        expect(count($openDetails))->toBeGreaterThan(0);
    });

    it('shows all items when filter is cleared', function () {
        $page = visit('/demo/ui/menu');

        $page->fill('[data-menu-filter-input]', 'Item 1')
            ->waitFor(0.3)
            ->assertDontSee('Item 2')
            ->fill('[data-menu-filter-input]', '')
            ->waitFor(0.3)
            ->assertSee('Item 1')
            ->assertSee('Item 2')
            ->assertSee('Item 3');
    });
});
