<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;

it('renders an ordered list with array items', function () {
    $html = View::make('daisy::components.ui.layout.ordered-list', [
        'items' => [
            ['id' => 'spec', 'label' => 'Define the spec', 'content' => 'Capture the UX contract'],
            ['id' => 'build', 'label' => 'Implement', 'content' => 'Ship the feature'],
        ],
    ])->render();

    expect($html)
        ->toContain('<ol')
        ->toContain('data-ordered-list="1"')
        ->toContain('Define the spec')
        ->toContain('Capture the UX contract')
        ->toContain('Implement');
});

it('renders ordered list persistence hooks when requested', function () {
    $html = View::make('daisy::components.ui.layout.ordered-list', [
        'items' => [
            ['id' => 'a', 'label' => 'A'],
        ],
        'sortable' => true,
        'persist' => true,
        'name' => 'ordered_ids',
    ])->render();

    expect($html)
        ->toContain('data-sortable="true"')
        ->toContain('data-persist="true"')
        ->toContain('data-ordered-list-handle')
        ->toContain('name="ordered_ids"');
});

it('renders ordered-list through its public alias', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.layout.ordered-list :items="[['id' => 'one', 'label' => 'First item']]" />
    BLADE);

    expect($html)
        ->toContain('First item')
        ->toContain('data-module="ordered-list"');
});
