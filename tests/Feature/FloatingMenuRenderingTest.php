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
});
