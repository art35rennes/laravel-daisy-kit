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

    it('does not render unsafe sidebar-navigation hrefs', function () {
        $html = View::make('daisy::components.ui.navigation.sidebar-navigation', [
            'items' => [
                [
                    'label' => 'Unsafe parent',
                    'href' => 'javascript:alert(1)',
                ],
                [
                    'label' => 'Group',
                    'children' => [
                        [
                            'label' => 'Unsafe child',
                            'href' => 'javascript:alert(2)',
                        ],
                    ],
                ],
            ],
        ])->render();

        expect($html)
            ->toContain('Unsafe parent')
            ->toContain('Unsafe child')
            ->not->toContain('href="javascript:alert(1)"')
            ->not->toContain('href="javascript:alert(2)"');
    });
});
