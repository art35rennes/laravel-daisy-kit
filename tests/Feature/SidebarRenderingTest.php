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

it('centers the sidebar footer and its collapse control', function () {
    $html = View::make('daisy::components.ui.navigation.sidebar', [
        'footer' => 'Environment footer',
        'sections' => daisyKitSidebarSections(),
    ])->render();

    expect($html)
        ->toContain('flex shrink-0 flex-col items-center')
        ->toContain('w-full text-center')
        ->toContain('justify-center gap-2 sidebar-toggle')
        ->not->toContain('w-full justify-start gap-2');
});

it('renders a compact collapsed navigation state', function () {
    $html = View::make('daisy::components.ui.navigation.sidebar', [
        'collapsed' => true,
        'sections' => daisyKitSidebarSections(),
    ])->render();

    expect($html)
        ->toContain('data-collapsed="1"')
        ->toContain('aria-expanded="false"')
        ->toContain('data-sidebar-section-title')
        ->toContain('sidebar-label')
        ->toContain('data-sidebar-icon-collapsed')
        ->toContain('data-sidebar-submenu')
        ->toContain('menu-active');
});

it('renders nested sidebar items recursively', function () {
    $html = View::make('daisy::components.ui.navigation.sidebar', [
        'sections' => [[
            'label' => 'Workspace',
            'items' => [[
                'label' => 'Settings',
                'icon' => 'gear',
                'children' => [[
                    'label' => 'Access',
                    'icon' => 'shield-lock',
                    'children' => [[
                        'label' => 'Roles',
                        'href' => '/settings/access/roles',
                    ]],
                ]],
            ]],
        ]],
    ])->render();

    expect(substr_count($html, '<details'))->toBe(2)
        ->and($html)
        ->toContain('data-sidebar-depth="0"')
        ->toContain('data-sidebar-depth="1"')
        ->toContain('data-sidebar-depth="2"')
        ->toContain('href="/settings/access/roles"');
});

it('renders the sidebar name logo and footer', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.navigation.sidebar name="Daisy Admin" logo="/images/daisy.svg">
            <x-slot:footer><span data-environment>Production</span></x-slot:footer>
        </x-daisy::ui.navigation.sidebar>
    BLADE);

    expect($html)
        ->toContain('Daisy Admin')
        ->toContain('src="/images/daisy.svg"')
        ->toContain('data-sidebar-logo')
        ->toContain('data-environment');
});

it('renders a persistent recursive search surface', function () {
    $html = View::make('daisy::components.ui.navigation.sidebar', [
        'collapsed' => true,
        'searchable' => true,
        'sections' => daisyKitSidebarSections(),
    ])->render();

    expect($html)
        ->toContain('data-sidebar-search')
        ->toContain('data-sidebar-search-status')
        ->toContain('data-sidebar-search-empty')
        ->not->toContain('data-sidebar-search-trigger')
        ->toContain('data-menu-filter-target');
});

it('renders direction and responsive state hooks', function () {
    $html = View::make('daisy::components.ui.navigation.sidebar', [
        'end' => true,
        'collapseAt' => 'md',
        'sections' => daisyKitSidebarSections(),
    ])->render();

    expect($html)
        ->toContain('data-sidebar-side="end"')
        ->toContain('data-collapse-at="md"')
        ->toContain('md:flex');
});

it('passes the complete sidebar api through the navbar sidebar layout', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::layout.navbar-sidebar-layout
            collapsed
            searchable
            storage-key="admin-navigation"
            name="Admin"
            logo="/logo.svg"
            :show-theme-controller="false"
        >
            Content
        </x-daisy::layout.navbar-sidebar-layout>
    BLADE);

    expect($html)
        ->toContain('data-collapsed="1"')
        ->toContain('data-sidebar-search')
        ->toContain('data-storage-key="admin-navigation"')
        ->toContain('Admin')
        ->toContain('src="/logo.svg"');
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
        ->toContain('justify-center gap-2')
        ->toContain('justify-center gap-2 sidebar-toggle')
        ->toContain('data-sidebar-scroll-region')
        ->toContain('data-sidebar-footer');
});

it('passes compact behavior through the sidebar layout', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::layout.sidebar-layout
            title="Dashboard"
            variant="slim"
            :force-collapsed="true"
            :collapsible="false"
            :sections="[['items' => [['label' => 'Home', 'href' => '/home', 'icon' => 'house']]]]"
        >
            Content
        </x-daisy::layout.sidebar-layout>
    BLADE);

    expect($html)
        ->toContain('data-collapsed="1"')
        ->toContain('data-force-collapsed="1"')
        ->toContain('data-sidebar-scroll-region')
        ->not->toContain('data-sidebar-toggle');
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
        ->toContain('data-sidebar-search-region')
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

it('opens a parent when a child is active without marking the parent active', function () {
    $html = View::make('daisy::components.ui.navigation.sidebar', [
        'sections' => [
            [
                'items' => [
                    [
                        'label' => 'Configuration applicative',
                        'icon' => 'gear',
                        'children' => [
                            [
                                'label' => 'Périmètres',
                                'href' => '/settings/scopes',
                                'icon' => 'diagram-3',
                                'active' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ])->render();

    preg_match('/<summary\s+class="([^"]*)"[^>]*aria-label="Configuration applicative"/s', $html, $parentSummary);

    expect($html)
        ->toMatch('/<details\s+open\s+data-sidebar-details/')
        ->toContain('<ul data-sidebar-submenu>')
        ->toContain('aria-label="Configuration applicative"')
        ->and($parentSummary[1] ?? null)->not->toContain('menu-active')
        ->and(preg_match('/href="\/settings\/scopes"\s+class="flex items-center gap-2 menu-active"/', $html))->toBe(1);
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
        ->and(preg_match('/href="\/settings"\s+class="flex items-center gap-2 menu-active"/', $usersHtml))->toBe(0);
    expect($settingsHtml)
        ->toContain('Settings')
        ->and(preg_match('/href="\/settings"\s+class="flex items-center gap-2 menu-active"/', $settingsHtml))->toBe(1);
});
