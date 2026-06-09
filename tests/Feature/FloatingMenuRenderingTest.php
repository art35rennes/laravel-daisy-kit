<?php

use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;

describe('Floating menu component rendering', function () {
    it('renders in the bottom right by default', function () {
        $html = View::make('daisy::components.ui.navigation.floating-menu', [
            'slot' => new HtmlString('<button type="button">Menu</button>'),
        ])->render();

        expect($html)
            ->toContain('fixed')
            ->toContain('bottom-4 right-4')
            ->not->toContain('left-4 top-1/2 -translate-y-1/2');
    });

    it('still supports legacy edge positions', function () {
        $html = View::make('daisy::components.ui.navigation.floating-menu', [
            'position' => 'left',
            'slot' => new HtmlString('<button type="button">Menu</button>'),
        ])->render();

        expect($html)
            ->toContain('left-4 top-1/2 -translate-y-1/2');
    });

    it('does not render unsafe hrefs or inline handlers by default', function () {
        $html = View::make('daisy::components.ui.navigation.floating-menu', [
            'groups' => [
                [
                    'items' => [
                        [
                            'icon' => 'bi-pencil',
                            'label' => 'Unsafe',
                            'href' => 'javascript:alert(1)',
                            'onclick' => 'alert(2)',
                        ],
                    ],
                ],
            ],
        ])->render();

        expect($html)
            ->toContain('Unsafe')
            ->not->toContain('href="javascript:alert(1)"')
            ->not->toContain('onclick="alert(2)"');
    });

    it('does not render inline handlers when legacy opt in is passed', function () {
        $html = View::make('daisy::components.ui.navigation.floating-menu', [
            'allowInlineHandlers' => true,
            'groups' => [
                [
                    'items' => [
                        [
                            'icon' => 'bi-pencil',
                            'label' => 'Allowed',
                            'onclick' => 'alert(1)',
                        ],
                    ],
                ],
            ],
        ])->render();

        expect($html)
            ->toContain('Allowed')
            ->not->toContain('onclick="alert(1)"');
    });
});
