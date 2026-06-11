<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
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

it('renders configured collapsed widths and collapsed brand content', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.navigation.sidebar expanded-width="w-72" collapsed-width="w-16" collapsed :sections="[['items' => [['label' => 'Home', 'href' => '/home', 'icon' => 'house']]]]">
            <x-slot:brand>
                <span data-expanded-brand>Expanded brand</span>
            </x-slot:brand>
            <x-slot:brandCollapsed>
                <span data-collapsed-brand>DK</span>
            </x-slot:brandCollapsed>
        </x-daisy::ui.navigation.sidebar>
    BLADE);

    expect($html)
        ->toContain('data-width-strategy="configured"')
        ->toContain('data-wide-class="w-72"')
        ->toContain('data-collapsed-class="w-16"')
        ->toContain('w-16')
        ->toContain('data-sidebar-brand-collapsed')
        ->toContain('data-collapsed-brand')
        ->toContain('justify-center gap-0')
        ->toContain('btn-square mx-auto justify-center')
        ->toContain('sidebar-menu-collapsed')
        ->toContain('data-sidebar-footer');
});

it('wraps a custom brand slot when a brand url is provided', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.navigation.sidebar brand-url="/dashboard">
            <x-slot:brand>
                <span data-expanded-brand>Expanded brand</span>
            </x-slot:brand>
        </x-daisy::ui.navigation.sidebar>
    BLADE);

    expect($html)
        ->toContain('href="/dashboard"')
        ->toContain('data-expanded-brand')
        ->not->toContain('href="#"');
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

it('hides sidebar items marked as not visible', function () {
    $sections = daisyKitSidebarSections();
    $sections[0]['items'][] = [
        'label' => 'Hidden item',
        'href' => '/hidden',
        'visible' => false,
    ];
    $sections[0]['items'][1]['children'][] = [
        'label' => 'Hidden child',
        'href' => '/hidden-child',
        'visible' => false,
    ];

    $html = View::make('daisy::components.ui.navigation.sidebar', [
        'sections' => $sections,
    ])->render();

    expect($html)
        ->not->toContain('Hidden item')
        ->not->toContain('Hidden child')
        ->toContain('Overview')
        ->toContain('Health');
});

it('does not render unsafe sidebar hrefs', function () {
    $html = View::make('daisy::components.ui.navigation.sidebar', [
        'brandHref' => 'javascript:alert(1)',
        'sections' => [
            [
                'items' => [
                    [
                        'label' => 'Unsafe',
                        'href' => 'javascript:alert(2)',
                    ],
                    [
                        'label' => 'Parent',
                        'children' => [
                            [
                                'label' => 'Unsafe child',
                                'href' => 'javascript:alert(3)',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ])->render();

    expect($html)
        ->toContain('Unsafe')
        ->toContain('Unsafe child')
        ->not->toContain('href="javascript:alert(1)"')
        ->not->toContain('href="javascript:alert(2)"')
        ->not->toContain('href="javascript:alert(3)"');
});

it('does not wrap a custom sidebar brand without an explicit url', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.navigation.sidebar>
            <x-slot:brand>
                <a href="/dashboard" class="brand-link">Acme</a>
            </x-slot:brand>
        </x-daisy::ui.navigation.sidebar>
    BLADE);

    expect($html)
        ->toContain('<a href="/dashboard" class="brand-link">Acme</a>')
        ->not->toContain('href="#"');
});

it('links the sidebar brand only when a brand url is provided', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.navigation.sidebar brand="Acme" brand-url="/dashboard" />
    BLADE);

    expect($html)
        ->toContain('href="/dashboard"')
        ->toContain('Acme')
        ->not->toContain('href="#"');
});

it('marks sidebar items active from named routes', function () {
    $renderSidebar = function () {
        return View::make('daisy::components.ui.navigation.sidebar', [
            'sections' => [
                [
                    'items' => [
                        [
                            'label' => 'Users',
                            'href' => '/users',
                            'activeRoute' => 'sidebar.users.index',
                        ],
                        [
                            'label' => 'Settings',
                            'href' => '/settings',
                            'activeRoutes' => ['sidebar.settings.*'],
                        ],
                    ],
                ],
            ],
        ])->render();
    };

    Route::get('/sidebar-active-route-test', $renderSidebar)->name('sidebar.users.index');
    Route::get('/sidebar-active-routes-test', $renderSidebar)->name('sidebar.settings.edit');

    $usersHtml = $this->get('/sidebar-active-route-test')->getContent();
    $settingsHtml = $this->get('/sidebar-active-routes-test')->getContent();

    expect($usersHtml)
        ->toContain('Users')
        ->toContain('menu-active')
        ->toContain('Settings')
        ->not->toContain('href="/settings" class="flex items-center gap-2 menu-active"');
    expect($settingsHtml)
        ->toContain('Settings')
        ->toContain('href="/settings" class="flex items-center gap-2 menu-active"');
});
