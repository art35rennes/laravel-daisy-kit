<?php

use Illuminate\Support\Facades\View;

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
        'attributes' => new \Illuminate\View\ComponentAttributeBag(['placeholder' => 'Type here']),
    ])->render();

    expect($html)
        ->toContain('input')
        ->toContain('Type here');
});

it('renders a divider component', function () {
    $html = View::make('daisy::components.ui.layout.divider', [
        'slot' => '',
    ])->render();

    expect($html)
        ->toContain('divider');
});

it('renders a link component', function () {
    $html = View::make('daisy::components.ui.advanced.link', [
        'slot' => 'Link text',
        'attributes' => new \Illuminate\View\ComponentAttributeBag(['href' => '/test']),
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
        'slot' => new \Illuminate\Support\HtmlString($inner),
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

    $html = \Illuminate\Support\Facades\Blade::render($blade);

    expect($html)
        ->toContain('.col-12')
        ->toContain('@media (min-width: 1280px)')
        ->toContain('.offset-md-3');

    $styleCount = substr_count($html, '<style>');
    expect($styleCount)->toBe(1);
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
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
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
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
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
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
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
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
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
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
    ])->render();

    expect($html)
        ->toContain('© 2023')
        ->toContain('Mon Entreprise');
});

it('renders footer-layout without divider when showDivider is false', function () {
    $html = View::make('daisy::components.ui.layout.footer-layout', [
        'showDivider' => false,
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
    ])->render();

    expect($html)
        ->not->toContain('divider');
});

it('renders a sign component', function () {
    $html = View::make('daisy::components.ui.inputs.sign', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag(['name' => 'signature']),
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
        'attributes' => new \Illuminate\View\ComponentAttributeBag(['name' => 'signature']),
    ])->render();

    expect($html)
        ->toContain('data-width="600"')
        ->toContain('data-height="300"');
});

it('renders a sign component without actions', function () {
    $html = View::make('daisy::components.ui.inputs.sign', [
        'showActions' => false,
        'attributes' => new \Illuminate\View\ComponentAttributeBag(['name' => 'signature']),
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
