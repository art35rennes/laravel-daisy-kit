<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;

describe('Breadcrumbs component rendering', function () {
    it('renders breadcrumb links and current item', function () {
        $html = View::make('daisy::components.ui.navigation.breadcrumbs', [
            'items' => [
                ['label' => 'Dashboard', 'href' => '/dashboard'],
                ['label' => 'Users', 'href' => '/users'],
                ['label' => 'Ada Lovelace'],
            ],
        ])->render();

        expect($html)
            ->toContain('class="breadcrumbs text-sm"')
            ->toContain('<ul>')
            ->toContain('href="/dashboard"')
            ->toContain('Dashboard')
            ->toContain('href="/users"')
            ->toContain('Users')
            ->toContain('aria-current="page"')
            ->toContain('Ada Lovelace');
    });

    it('uses accessible nav markup by default', function () {
        $html = View::make('daisy::components.ui.navigation.breadcrumbs', [
            'label' => 'Page path',
            'items' => [
                ['label' => 'Home', 'href' => '/'],
                ['label' => 'Settings', 'current' => true],
            ],
        ])->render();

        expect($html)
            ->toContain('<nav aria-label="Page path"')
            ->toContain('aria-current="page"')
            ->not->toContain('href=""');
    });

    it('supports div wrapper for legacy layouts', function () {
        $html = View::make('daisy::components.ui.navigation.breadcrumbs', [
            'as' => 'div',
            'items' => [
                ['label' => 'Home', 'href' => '/'],
            ],
        ])->render();

        expect($html)
            ->toContain('<div class="breadcrumbs text-sm"')
            ->not->toContain('aria-label=');
    });

    it('renders configured sizes and falls back to small text', function (string $size, string $expectedClass) {
        $html = View::make('daisy::components.ui.navigation.breadcrumbs', [
            'size' => $size,
            'items' => [
                ['label' => 'Home', 'href' => '/'],
            ],
        ])->render();

        expect($html)->toContain($expectedClass);
    })->with([
        'small' => ['sm', 'text-sm'],
        'medium' => ['md', 'text-base'],
        'large' => ['lg', 'text-lg'],
        'invalid fallback' => ['xxl', 'text-sm'],
    ]);

    it('accepts array and object items with safe fallbacks', function () {
        $html = View::make('daisy::components.ui.navigation.breadcrumbs', [
            'items' => [
                (object) ['label' => 'Projects', 'href' => '/projects'],
                ['href' => ''],
            ],
        ])->render();

        expect($html)
            ->toContain('href="/projects"')
            ->toContain('Projects')
            ->toContain('aria-current="page"')
            ->not->toContain('href=""');
    });

    it('escapes labels and plain string icons', function () {
        $html = View::make('daisy::components.ui.navigation.breadcrumbs', [
            'items' => [
                [
                    'label' => '<script>alert("label")</script>',
                    'icon' => '<svg><script>alert("icon")</script></svg>',
                    'current' => true,
                ],
            ],
        ])->render();

        expect($html)
            ->toContain('&lt;script&gt;alert(&quot;label&quot;)&lt;/script&gt;')
            ->toContain('&lt;svg&gt;&lt;script&gt;alert(&quot;icon&quot;)&lt;/script&gt;&lt;/svg&gt;')
            ->not->toContain('<script>alert("label")</script>')
            ->not->toContain('<svg><script>alert("icon")</script></svg>');
    });

    it('renders trusted icon markup from HtmlString', function () {
        $html = View::make('daisy::components.ui.navigation.breadcrumbs', [
            'items' => [
                [
                    'label' => 'Dashboard',
                    'icon' => new HtmlString('<svg data-icon="dashboard"></svg>'),
                    'current' => true,
                ],
            ],
        ])->render();

        expect($html)
            ->toContain('<svg data-icon="dashboard"></svg>')
            ->toContain('inline-flex items-center gap-2');
    });

    it('renders explicit iconHtml markup as trusted content', function () {
        $html = View::make('daisy::components.ui.navigation.breadcrumbs', [
            'items' => [
                [
                    'label' => 'Dashboard',
                    'iconHtml' => '<svg data-icon-html="dashboard"></svg>',
                    'current' => true,
                ],
            ],
        ])->render();

        expect($html)->toContain('<svg data-icon-html="dashboard"></svg>');
    });

    it('renders through the public Blade component alias', function () {
        $items = [
            ['label' => 'Dashboard', 'href' => '/dashboard'],
            ['label' => 'Settings', 'current' => true],
        ];

        $html = Blade::render('<x-daisy::ui.navigation.breadcrumbs :items="$items" />', compact('items'));

        expect($html)
            ->toContain('class="breadcrumbs text-sm"')
            ->toContain('href="/dashboard"')
            ->toContain('aria-current="page"')
            ->toContain('Settings');
    });

    it('supports manually composed slot content', function () {
        $html = Blade::render(<<<'BLADE'
            <x-daisy::ui.navigation.breadcrumbs>
                <li><a href="/">Home</a></li>
                <li><span aria-current="page">Manual</span></li>
            </x-daisy::ui.navigation.breadcrumbs>
        BLADE);

        expect($html)
            ->toContain('<li><a href="/">Home</a></li>')
            ->toContain('<li><span aria-current="page">Manual</span></li>');
    });

    it('renders disabled and separator items without making them current links', function () {
        $html = View::make('daisy::components.ui.navigation.breadcrumbs', [
            'items' => [
                ['label' => 'Home', 'href' => '/'],
                ['label' => '/', 'separator' => true],
                ['label' => 'Archive', 'href' => '/archive', 'disabled' => true],
                ['label' => 'Current', 'current' => true],
            ],
        ])->render();

        expect($html)
            ->toContain('aria-hidden="true"')
            ->toContain('aria-disabled="true"')
            ->not->toContain('href="/archive"')
            ->toContain('Current');
    });

    it('renders iconName with the Blade Icons component', function () {
        $html = View::make('daisy::components.ui.navigation.breadcrumbs', [
            'items' => [
                ['label' => 'Dashboard', 'iconName' => 'bi-house', 'current' => true],
            ],
        ])->render();

        expect($html)
            ->toContain('<svg')
            ->toContain('inline-flex items-center gap-2');
    });

    it('can truncate middle items on small screens', function () {
        $html = View::make('daisy::components.ui.navigation.breadcrumbs', [
            'truncate' => true,
            'items' => [
                ['label' => 'Home', 'href' => '/'],
                ['label' => 'Section', 'href' => '/section'],
                ['label' => 'Subsection', 'href' => '/section/subsection'],
                ['label' => 'Current', 'current' => true],
            ],
        ])->render();

        expect($html)
            ->toContain('class="sm:hidden"')
            ->toContain('class="hidden sm:list-item"')
            ->toContain('Current');
    });

    it('renders JSON-LD breadcrumb schema when requested', function () {
        $html = View::make('daisy::components.ui.navigation.breadcrumbs', [
            'schema' => true,
            'items' => [
                ['label' => 'Home', 'href' => '/'],
                ['label' => 'Users', 'href' => '/users'],
                ['label' => 'Ada', 'current' => true],
            ],
        ])->render();

        expect($html)
            ->toContain('type="application/ld+json"')
            ->toContain('"@type":"BreadcrumbList"')
            ->toContain('"position":1')
            ->toContain('"name":"Users"');
    });
});
