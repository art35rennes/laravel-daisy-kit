<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Illuminate\View\ComponentAttributeBag;

it('renders a button component', function () {
    $html = View::make('daisy::components.ui.inputs.button', [
        'slot' => 'Click me',
    ])->render();

    expect($html)
        ->toContain('btn')
        ->toContain('Click me');
});

it('renders a badge component', function () {
    $html = View::make('daisy::components.ui.data-display.badge', [
        'slot' => 'New',
    ])->render();

    expect($html)
        ->toContain('badge')
        ->toContain('New');
});

it('renders an alert component', function () {
    $html = View::make('daisy::components.ui.feedback.alert', [
        'slot' => 'Alert message',
    ])->render();

    expect($html)
        ->toContain('alert')
        ->toContain('Alert message');
});

it('renders an input component', function () {
    $html = View::make('daisy::components.ui.inputs.input', [
        'attributes' => new ComponentAttributeBag(['placeholder' => 'Type here']),
    ])->render();

    expect($html)
        ->toContain('input')
        ->toContain('Type here');
});

it('renders a token-input component with prefilled values and hidden inputs', function () {
    $html = View::make('daisy::components.ui.inputs.token-input', [
        'name' => 'recipients',
        'values' => ['Alice@Example.com', 'bob@example.com'],
        'placeholder' => 'Add recipients',
    ])->render();

    expect($html)
        ->toContain('data-module="token-input"')
        ->toContain('data-submit-name="recipients[]"')
        ->toContain('Add recipients')
        ->toContain('data-token-item')
        ->toContain('value="alice@example.com"')
        ->toContain('value="bob@example.com"')
        ->toContain('name="recipients[]"');
});

it('renders localized code-editor toolbar and CodeMirror phrases', function () {
    app()->setLocale('fr');

    $html = View::make('daisy::components.ui.advanced.code-editor', [
        'language' => 'json',
        'value' => '{"name":"Ada"}',
    ])->render();

    app()->setLocale('en');

    expect($html)
        ->toContain('Tout plier')
        ->toContain('Tout déplier')
        ->toContain('Formater')
        ->toContain('Copier')
        ->toContain('Tout plier récursivement')
        ->toContain('Rechercher')
        ->toContain('"regexp"')
        ->toContain('data-i18n');
});

it('renders localized default labels for public UI components', function () {
    app()->setLocale('fr');

    try {
        $errors = new ViewErrorBag;
        $errors->put('default', new MessageBag(['profile.name' => ['Missing name']]));

        $emptyState = View::make('daisy::components.ui.feedback.empty-state', [
            'preset' => 'no-permission',
        ])->render();

        $pagination = View::make('daisy::components.ui.navigation.pagination', [
            'total' => 3,
            'current' => 2,
        ])->render();

        $tabs = View::make('daisy::components.ui.navigation.tabs', [
            'errorBag' => $errors,
            'items' => [
                ['errorKey' => 'profile.name'],
            ],
        ])->render();

        $dropdown = View::make('daisy::components.ui.overlay.dropdown', [
            'id' => 'localized-dropdown',
        ])->render();

        $stepper = View::make('daisy::components.ui.navigation.stepper', [
            'items' => [[], []],
        ])->render();

        expect($emptyState)
            ->toContain('Accès indisponible');
        expect($pagination)
            ->toContain('aria-label="Précédent"')
            ->toContain('aria-label="Suivant"')
            ->toContain('Page 2 sur 3');
        expect($tabs)
            ->toContain('Onglet')
            ->toContain('Erreur');
        expect($dropdown)
            ->toContain('aria-label="Ouvrir le menu déroulant"');
        expect($stepper)
            ->toContain('Précédent')
            ->toContain('Suivant')
            ->toContain('Terminer')
            ->toContain('Etape 1');
    } finally {
        app()->setLocale('en');
    }
});

it('renders token-input suggestion and endpoint payloads for js enhancement', function () {
    $html = View::make('daisy::components.ui.inputs.token-input', [
        'name' => 'tags',
        'preset' => 'text',
        'size' => 'sm',
        'color' => 'primary',
        'suggestions' => [
            ['value' => 'laravel', 'label' => 'Laravel'],
            ['value' => 'livewire', 'label' => 'Livewire'],
        ],
        'endpoint' => '/api/tags',
        'param' => 'search',
        'debounce' => 150,
        'minChars' => 1,
    ])->render();

    expect($html)
        ->toContain('input-sm')
        ->toContain('badge-primary')
        ->toContain('data-suggestions=')
        ->toContain('data-endpoint="/api/tags"')
        ->toContain('data-param="search"')
        ->toContain('data-debounce="150"')
        ->toContain('data-min-chars="1"');
});

it('renders a divider component', function () {
    $html = View::make('daisy::components.ui.layout.divider', [
        'slot' => '',
    ])->render();

    expect($html)
        ->toContain('divider');
});

it('renders a charts.bar component', function () {
    $html = View::make('daisy::components.charts.bar', [
        'title' => 'Revenue',
        'categories' => ['Jan', 'Feb'],
        'series' => [
            ['name' => 'Revenue', 'data' => [12, 24]],
        ],
    ])->render();

    expect($html)
        ->toContain('data-daisy-chart="1"')
        ->toContain('"preset":"bar"')
        ->toContain('"categories":["Jan","Feb"]')
        ->toContain('Revenue');
});

it('renders a charts.sparkline component without legend by default', function () {
    $html = View::make('daisy::components.charts.sparkline', [
        'series' => [
            ['name' => 'Visitors', 'data' => [1, 3, 2]],
        ],
    ])->render();

    expect($html)
        ->toContain('"preset":"sparkline"')
        ->toContain('"legend":false');
});

it('renders a link component', function () {
    $html = View::make('daisy::components.ui.advanced.link', [
        'slot' => 'Link text',
        'attributes' => new ComponentAttributeBag(['href' => '/test']),
    ])->render();

    expect($html)
        ->toContain('link')
        ->toContain('Link text')
        ->toContain('/test');
});

it('renders the grid layout with correct classes', function () {
    $inner = '<div class="col-sm-12 col-xl-4">Col 1</div>';

    $html = View::make('daisy::components.ui.layout.grid-layout', [
        'gap' => 6,
        'align' => 'start',
        'slot' => new HtmlString($inner),
    ])->render();

    expect($html)
        ->toContain('daisy-grid')
        ->toContain('grid grid-cols-12')
        ->toContain('gap-6')
        ->toContain('items-start')
        ->toContain('col-sm-12')
        ->toContain('col-xl-4')
        ->toContain('Col 1');
});

it('injects grid layout CSS utilities only once', function () {
    $blade = <<<'BLADE'
<x-daisy::ui.layout.grid-layout>
  <div class="col-12">A</div>
</x-daisy::ui.layout.grid-layout>
<x-daisy::ui.layout.grid-layout>
  <div class="col-12">B</div>
</x-daisy::ui.layout.grid-layout>
@stack('styles')
BLADE;

    $html = Blade::render($blade);

    expect($html)
        ->toContain('.col-12')
        ->toContain('@media (min-width: 1280px)')
        ->toContain('.offset-md-3');

    $styleCount = substr_count($html, '<style>');
    expect($styleCount)->toBe(1);
});

it('renders transfer dnd hooks without breaking the existing API', function () {
    $html = View::make('daisy::components.ui.advanced.transfer', [
        'source' => [['data' => 'Alpha', 'customId' => 'alpha']],
        'target' => [['data' => 'Beta', 'customId' => 'beta']],
        'sortable' => true,
        'dragAndDrop' => true,
        'handle' => true,
    ])->render();

    expect($html)
        ->toContain('data-sortable="true"')
        ->toContain('data-drag-and-drop="true"')
        ->toContain('data-transfer-handle')
        ->toContain('data-id="alpha"')
        ->toContain('data-id="beta"');
});

it('renders footer-layout component with columns', function () {
    $html = View::make('daisy::components.ui.layout.footer-layout', [
        'columns' => [
            [
                'title' => 'Services',
                'links' => [
                    ['label' => 'Branding', 'href' => '#'],
                    ['label' => 'Design', 'href' => '#'],
                ],
            ],
        ],
        'copyrightText' => 'Mon Entreprise',
        'attributes' => new ComponentAttributeBag([]),
    ])->render();

    expect($html)
        ->toContain('footer')
        ->toContain('Services')
        ->toContain('Branding')
        ->toContain('Design')
        ->toContain('Mon Entreprise');
});

it('renders footer-layout with social links', function () {
    $html = View::make('daisy::components.ui.layout.footer-layout', [
        'socialLinks' => [
            ['icon' => 'facebook', 'href' => '#', 'label' => 'Facebook'],
            ['icon' => 'twitter', 'href' => '#', 'label' => 'Twitter'],
        ],
        'attributes' => new ComponentAttributeBag([]),
    ])->render();

    expect($html)
        ->toContain('footer')
        ->toContain('btn-circle');
});

it('renders footer-layout with newsletter', function () {
    $html = View::make('daisy::components.ui.layout.footer-layout', [
        'newsletter' => true,
        'newsletterTitle' => 'Newsletter',
        'newsletterDescription' => 'Restez informé',
        'newsletterAction' => '/subscribe',
        'attributes' => new ComponentAttributeBag([]),
    ])->render();

    expect($html)
        ->toContain('Newsletter')
        ->toContain('Restez informé')
        ->toContain('/subscribe')
        ->toContain('type="email"');
});

it('renders footer-layout with brand text and description', function () {
    $html = View::make('daisy::components.ui.layout.footer-layout', [
        'brandText' => 'Mon Entreprise',
        'brandDescription' => 'Créons ensemble',
        'attributes' => new ComponentAttributeBag([]),
    ])->render();

    expect($html)
        ->toContain('Mon Entreprise')
        ->toContain('Créons ensemble')
        ->toContain('footer-title');
});

it('renders footer-layout with custom copyright year', function () {
    $html = View::make('daisy::components.ui.layout.footer-layout', [
        'copyrightYear' => 2023,
        'copyrightText' => 'Mon Entreprise',
        'attributes' => new ComponentAttributeBag([]),
    ])->render();

    expect($html)
        ->toContain('© 2023')
        ->toContain('Mon Entreprise');
});

it('renders footer-layout without divider when showDivider is false', function () {
    $html = View::make('daisy::components.ui.layout.footer-layout', [
        'showDivider' => false,
        'attributes' => new ComponentAttributeBag([]),
    ])->render();

    expect($html)
        ->not->toContain('divider');
});

it('renders a sign component', function () {
    $html = View::make('daisy::components.ui.inputs.sign', [
        'attributes' => new ComponentAttributeBag(['name' => 'signature']),
    ])->render();

    expect($html)
        ->toContain('data-sign="1"')
        ->toContain('data-module="sign"')
        ->toContain('data-sign-canvas')
        ->toContain('name="signature"');
});

it('renders a sign component with custom dimensions', function () {
    $html = View::make('daisy::components.ui.inputs.sign', [
        'width' => 600,
        'height' => 300,
        'value' => 'data:image/png;base64,abc',
        'attributes' => new ComponentAttributeBag(['name' => 'signature']),
    ])->render();

    expect($html)
        ->toContain('data-width="600"')
        ->toContain('data-height="300"')
        ->toContain('value="data:image/png;base64,abc"');
});

it('renders a sign component without actions', function () {
    $html = View::make('daisy::components.ui.inputs.sign', [
        'showActions' => false,
        'attributes' => new ComponentAttributeBag(['name' => 'signature']),
    ])->render();

    expect($html)
        ->toContain('data-show-actions="false"')
        ->not->toContain('data-sign-clear')
        ->not->toContain('data-sign-download');
});

it('renders a copyable component with default props', function () {
    $html = View::make('daisy::components.ui.utilities.copyable', [
        'slot' => 'Texte à copier',
    ])->render();

    expect($html)
        ->toContain('copyable')
        ->toContain('copyable-underline')
        ->toContain('Texte à copier');
});

it('renders a copyable component without underline when explicitly disabled', function () {
    $html = View::make('daisy::components.ui.utilities.copyable', [
        'underline' => false,
        'slot' => 'Texte non souligné',
    ])->render();

    expect($html)
        ->toContain('copyable')
        ->not->toContain('copyable-underline')
        ->toContain('Texte non souligné');
});

it('renders a copyable component with value prop', function () {
    $html = View::make('daisy::components.ui.utilities.copyable', [
        'value' => 'Valeur à copier',
        'slot' => 'Texte affiché',
    ])->render();

    expect($html)
        ->toContain('data-copy-value="Valeur à copier"')
        ->toContain('Texte affiché');
});

it('renders a copyable component with copyHtml enabled', function () {
    $html = View::make('daisy::components.ui.utilities.copyable', [
        'copyHtml' => true,
        'slot' => '<strong>Texte HTML</strong>',
    ])->render();

    expect($html)
        ->toContain('data-copy-html="true"');
});

it('renders a copyable component with display prop (option mode)', function () {
    $html = View::make('daisy::components.ui.utilities.copyable', [
        'value' => 'valeur-copiee',
        'display' => 'Texte affiché',
        'slot' => 'Slot ignoré',
    ])->render();

    expect($html)
        ->toContain('data-copy-value="valeur-copiee"')
        ->toContain('Texte affiché')
        ->not->toContain('Slot ignoré');
});

it('renders inline countdown with one daisyUI countdown wrapper per segment', function () {
    $html = View::make('daisy::components.ui.advanced.countdown', [
        'values' => ['h' => 10, 'm' => 24, 's' => 59],
        'mode' => 'inline',
        'size' => 'lg',
    ])->render();

    expect(substr_count($html, '<span class="countdown">'))->toBe(3);
});

it('renders inline-colon countdown with one daisyUI countdown wrapper per segment', function () {
    $html = View::make('daisy::components.ui.advanced.countdown', [
        'values' => ['h' => 10, 'm' => 24, 's' => 59],
        'mode' => 'inline-colon',
    ])->render();

    expect(substr_count($html, '<span class="countdown">'))->toBe(3);
});

it('renders a tree view parent with an explicit mixed checkbox state', function () {
    $html = View::make('daisy::components.ui.advanced.tree-view', [
        'selection' => 'multiple',
        'data' => [
            [
                'id' => 'sandbox',
                'label' => 'Sandbox',
                'state' => 'mixed',
                'children' => [
                    ['id' => 'draft', 'label' => 'Draft.md', 'selected' => true],
                    ['id' => 'notes', 'label' => 'Notes.md'],
                ],
            ],
        ],
    ])->render();

    expect($html)
        ->toContain('data-indeterminate="true"')
        ->toContain('aria-checked="mixed"')
        ->toContain('Draft.md')
        ->toContain('Notes.md');
});

it('renders tree nodes from checked aliases used by APIs', function () {
    $html = View::make('daisy::components.ui.advanced.tree-view', [
        'selection' => 'multiple',
        'data' => [
            [
                'id' => 'docs',
                'label' => 'Documentation',
                'checked' => true,
                'children' => [
                    ['id' => 'readme', 'label' => 'README.md', 'checked' => true],
                ],
            ],
        ],
    ])->render();

    expect(substr_count($html, 'checked'))->toBeGreaterThanOrEqual(2);
});

it('derives a mixed state for parents from partially selected descendants', function () {
    $html = View::make('daisy::components.ui.advanced.tree-view', [
        'selection' => 'multiple',
        'data' => [
            [
                'id' => 'project-beta',
                'label' => 'Projet Beta',
                'children' => [
                    [
                        'id' => 'docs',
                        'label' => 'Documentation',
                        'children' => [
                            ['id' => 'readme', 'label' => 'README.md', 'selected' => true],
                            ['id' => 'install', 'label' => 'INSTALL.md'],
                        ],
                    ],
                    [
                        'id' => 'sources',
                        'label' => 'Sources',
                        'children' => [
                            ['id' => 'main', 'label' => 'main.js'],
                            ['id' => 'app', 'label' => 'app.vue'],
                        ],
                    ],
                ],
            ],
        ],
    ])->render();

    expect(substr_count($html, 'data-indeterminate="true"'))->toBeGreaterThanOrEqual(2);
});

it('renders a tree view configured for progressive lazy loading', function () {
    $html = View::make('daisy::components.ui.advanced.tree-view', [
        'data' => [
            [
                'id' => 'root',
                'label' => 'Racine',
                'children' => [
                    ['id' => 'folder-b', 'label' => 'Dossier B', 'lazy' => true],
                ],
            ],
        ],
        'lazyUrl' => '/demo/api/tree-children',
        'lazyMode' => 'progressive',
    ])->render();

    expect($html)
        ->toContain('data-lazy-url="/demo/api/tree-children"')
        ->toContain('data-lazy-mode="progressive"')
        ->toContain('data-lazy-reload="false"')
        ->toContain('data-lazy-node="1"');
});

it('renders a tree view configured for auto lazy loading', function () {
    $html = View::make('daisy::components.ui.advanced.tree-view', [
        'data' => [
            [
                'id' => 'root',
                'label' => 'Projet Alpha',
                'children' => [
                    ['id' => 'lazy-docs', 'label' => 'Documentation', 'lazy' => true],
                ],
            ],
        ],
        'lazyUrl' => '/demo/api/tree-children',
        'lazyMode' => 'auto',
    ])->render();

    expect($html)
        ->toContain('data-lazy-url="/demo/api/tree-children"')
        ->toContain('data-lazy-mode="auto"')
        ->toContain('data-lazy-reload="false"');
});

it('renders color picker as a submittable form control', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.inputs.color-picker
            id="brand-color"
            name="brand_color"
            value="#123456"
            :dropdown="true"
            :swatches="[['#123456', '#abcdef']]"
            :show-alpha="false"
        />
    BLADE);

    expect($html)
        ->toContain('id="brand-color"')
        ->toContain('data-colorpicker="1"')
        ->toContain('name="brand_color"')
        ->toContain('data-colorpicker-input')
        ->toContain('value="#123456"')
        ->toContain('data-dropdown="true"')
        ->toContain('data-show-alpha="false"')
        ->toContain('#abcdef');
});
