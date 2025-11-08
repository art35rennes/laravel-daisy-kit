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
