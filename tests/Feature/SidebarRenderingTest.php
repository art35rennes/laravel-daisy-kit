<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;

function daisyKitSidebarSections(): array
{
    return [
        [
            'label' => 'Administration',
            'items' => [
                [
                    'label' => 'Overview',
                    'href' => '/admin',
                    'icon' => 'grid',
                ],
                [
                    'label' => 'System',
                    'icon' => 'gear',
                    'children' => [
                        [
                            'label' => 'Health',
                            'href' => '/health',
                            'icon' => 'heart-pulse',
                            'active' => true,
                        ],
                    ],
                ],
            ],
        ],
    ];
}

it('renders translated collapse controls', function () {
    App::setLocale('fr');

    $html = View::make('daisy::components.ui.navigation.sidebar', [
        'sections' => daisyKitSidebarSections(),
    ])->render();

    expect($html)
        ->toContain('data-expanded-label="Réduire"')
        ->toContain('data-collapsed-label="Développer"')
        ->toContain('aria-label="Réduire"')
        ->toContain('Réduire')
        ->not->toContain('>Collapse<');
});

it('renders a compact collapsed navigation state', function () {
    $html = View::make('daisy::components.ui.navigation.sidebar', [
        'collapsed' => true,
        'sections' => daisyKitSidebarSections(),
    ])->render();

    expect($html)
        ->toContain('data-collapsed="1"')
        ->toContain('aria-expanded="false"')
        ->toContain('aria-hidden="true"')
        ->toContain('sidebar-section-title sidebar-label hidden')
        ->toContain('sidebar-label-toggle sr-only')
        ->toContain('data-sidebar-icon-collapsed')
        ->toContain('data-sidebar-submenu')
        ->toContain('menu-active');
});

it('renders expand on hover as a temporary compact state', function () {
    $html = View::make('daisy::components.ui.navigation.sidebar', [
        'expandOnHover' => true,
        'searchable' => true,
        'sections' => daisyKitSidebarSections(),
    ])->render();

    expect($html)
        ->toContain('data-expand-on-hover="1"')
        ->toContain('data-collapsed="1"')
        ->toContain('data-sidebar-hover-content')
        ->toContain('aria-hidden="true"')
        ->toContain('data-sidebar-submenu')
        ->not->toContain('sidebar-toggle');
});
