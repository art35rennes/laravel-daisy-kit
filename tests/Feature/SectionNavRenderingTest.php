<?php

use Illuminate\Support\Facades\View;

describe('Section nav component rendering', function () {
    it('renders in the bottom right by default', function () {
        $html = View::make('daisy::components.ui.navigation.section-nav')->render();

        expect($html)
            ->toContain('fixed bottom-6 right-6')
            ->toContain('data-section-nav')
            ->toContain('data-section-nav-panel')
            ->toContain('btn btn-primary btn-circle');
    });

    it('supports alternate corner positions', function () {
        $html = View::make('daisy::components.ui.navigation.section-nav', [
            'position' => 'top-left',
        ])->render();

        expect($html)
            ->toContain('fixed top-6 left-6')
            ->toContain('absolute top-16 left-0');
    });
});
