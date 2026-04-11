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
});
