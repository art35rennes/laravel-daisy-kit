<?php

use Illuminate\Support\Facades\View;

describe('Menu component rendering', function () {
    it('renders a basic menu with items', function () {
        $html = View::make('daisy::components.ui.navigation.menu', [
            'slot' => new \Illuminate\Support\HtmlString('<li><a>Item 1</a></li><li><a>Item 2</a></li>'),
        ])->render();

        expect($html)
            ->toContain('menu')
            ->toContain('Item 1')
            ->toContain('Item 2');
    });

    it('renders menu with title', function () {
        $html = View::make('daisy::components.ui.navigation.menu', [
            'title' => 'Navigation',
            'slot' => new \Illuminate\Support\HtmlString('<li><a>Item</a></li>'),
        ])->render();

        expect($html)
            ->toContain('menu-title')
            ->toContain('Navigation');
    });

    it('renders vertical menu by default', function () {
        $html = View::make('daisy::components.ui.navigation.menu', [
            'slot' => new \Illuminate\Support\HtmlString('<li><a>Item</a></li>'),
        ])->render();

        expect($html)
            ->toContain('menu')
            ->not->toContain('menu-horizontal');
    });

    it('renders horizontal menu when vertical is false', function () {
        $html = View::make('daisy::components.ui.navigation.menu', [
            'vertical' => false,
            'slot' => new \Illuminate\Support\HtmlString('<li><a>Item</a></li>'),
        ])->render();

        expect($html)
            ->toContain('menu-horizontal');
    });

    it('renders menu with size classes', function () {
        $html = View::make('daisy::components.ui.navigation.menu', [
            'size' => 'sm',
            'slot' => new \Illuminate\Support\HtmlString('<li><a>Item</a></li>'),
        ])->render();

        expect($html)
            ->toContain('menu-sm');
    });

    it('renders menu with collapsible submenu using details', function () {
        $html = View::make('daisy::components.ui.navigation.menu', [
            'slot' => new \Illuminate\Support\HtmlString('
                <li><a>Item 1</a></li>
                <li>
                    <details>
                        <summary>Parent</summary>
                        <ul>
                            <li><a>Submenu 1</a></li>
                        </ul>
                    </details>
                </li>
            '),
        ])->render();

        expect($html)
            ->toContain('<details>')
            ->toContain('<summary>Parent</summary>')
            ->toContain('Submenu 1');
    });

    it('renders menu with filterable option and input', function () {
        $html = View::make('daisy::components.ui.navigation.menu', [
            'filterable' => true,
            'filterPlaceholder' => 'Search...',
            'slot' => new \Illuminate\Support\HtmlString('<li><a>Item</a></li>'),
        ])->render();

        expect($html)
            ->toContain('data-module="menu-filter"')
            ->toContain('data-menu-filter-input')
            ->toContain('data-menu-filter-target')
            ->toContain('Search...');
    });

    it('does not render filter input when filterable is false', function () {
        $html = View::make('daisy::components.ui.navigation.menu', [
            'filterable' => false,
            'slot' => new \Illuminate\Support\HtmlString('<li><a>Item</a></li>'),
        ])->render();

        expect($html)
            ->not->toContain('data-module="menu-filter"')
            ->not->toContain('data-menu-filter-input');
    });

    it('renders menu with menu-active modifier', function () {
        $html = View::make('daisy::components.ui.navigation.menu', [
            'slot' => new \Illuminate\Support\HtmlString('<li><a class="menu-active">Active Item</a></li>'),
        ])->render();

        expect($html)
            ->toContain('menu-active')
            ->toContain('Active Item');
    });

    it('renders menu with menu-disabled modifier', function () {
        $html = View::make('daisy::components.ui.navigation.menu', [
            'slot' => new \Illuminate\Support\HtmlString('<li class="menu-disabled"><a>Disabled Item</a></li>'),
        ])->render();

        expect($html)
            ->toContain('menu-disabled')
            ->toContain('Disabled Item');
    });

    it('renders menu without background when bg is false', function () {
        $html = View::make('daisy::components.ui.navigation.menu', [
            'bg' => false,
            'slot' => new \Illuminate\Support\HtmlString('<li><a>Item</a></li>'),
        ])->render();

        expect($html)
            ->not->toContain('bg-base-100');
    });

    it('renders menu without rounded when rounded is false', function () {
        $html = View::make('daisy::components.ui.navigation.menu', [
            'rounded' => false,
            'slot' => new \Illuminate\Support\HtmlString('<li><a>Item</a></li>'),
        ])->render();

        expect($html)
            ->not->toContain('rounded-box');
    });
});
