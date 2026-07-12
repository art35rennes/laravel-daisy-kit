<?php

use Illuminate\Support\Facades\View;

describe('Theme selector component rendering', function () {
    it('supports placement offsets for fixed layouts', function () {
        config()->set('daisy-kit.dev.show_theme_selector', true);

        $html = View::make('daisy::components.ui.partials.theme-selector', [
            'offsetClass' => 'top-20',
        ])->render();

        expect($html)
            ->toContain('fixed z-50')
            ->toContain('top-4 right-4 top-20');
    });

    it('keeps the legacy alias synchronized with configured themes', function () {
        config()->set('daisy-kit.dev.show_theme_selector', true);
        config()->set('daisy-kit.themes.builtin', ['light', 'dark']);
        config()->set('daisy-kit.themes.custom', [
            'brand' => ['name' => 'brand'],
        ]);

        $html = View::make('daisy::components.partials.theme-selector')->render();

        expect($html)
            ->toContain('value="light"')
            ->toContain('value="dark"')
            ->toContain('value="brand"')
            ->not->toContain('value="cupcake"');
    });
});
