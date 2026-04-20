<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;

it('renders the editable grid component with a minimal config payload', function () {
    $html = View::make('daisy::components.ui.layout.editable-grid', [
        'editable' => true,
        'columns' => 6,
        'cellHeight' => 120,
        'gap' => 12,
        'static' => false,
        'slot' => new HtmlString('<div class="grid-stack-item"></div>'),
    ])->render();

    expect($html)
        ->toContain('data-module="editable-grid"')
        ->toContain('grid-stack daisy-editable-grid')
        ->toContain('"editable":true')
        ->toContain('"columns":6')
        ->toContain('"cellHeight":120')
        ->toContain('"gap":12')
        ->toContain('"static":false');
});

it('renders editable grid v2 options in the config payload', function () {
    $html = View::make('daisy::components.ui.layout.editable-grid', [
        'editable' => true,
        'float' => true,
        'minRow' => 2,
        'acceptWidgets' => 'daisy-grid-widget',
        'layout' => 'compact',
        'responsive' => true,
    ])->render();

    expect($html)
        ->toContain('"float":true')
        ->toContain('"minRow":2')
        ->toContain('"acceptWidgets":"daisy-grid-widget"')
        ->toContain('"layout":"compact"')
        ->toContain('"responsive":{"columnWidth":320');
});

it('renders editable grid items from the items prop', function () {
    $html = View::make('daisy::components.ui.layout.editable-grid', [
        'items' => [
            [
                'id' => 'notes',
                'type' => 'list',
                'x' => 1,
                'y' => 2,
                'w' => 5,
                'h' => 3,
                'content' => new HtmlString('<p>Grid item content</p>'),
            ],
        ],
    ])->render();

    expect($html)
        ->toContain('gs-id="notes"')
        ->toContain('data-type="list"')
        ->toContain('gs-x="1"')
        ->toContain('gs-y="2"')
        ->toContain('gs-w="5"')
        ->toContain('gs-h="3"')
        ->toContain('Grid item content');
});

it('renders the editable grid item component', function () {
    $html = View::make('daisy::components.ui.layout.editable-grid-item', [
        'id' => 'kpi-users',
        'type' => 'stat',
        'x' => 2,
        'y' => 1,
        'w' => 4,
        'h' => 2,
        'meta' => ['section' => 'summary'],
        'slot' => 'Users card',
    ])->render();

    expect($html)
        ->toContain('grid-stack-item')
        ->toContain('grid-stack-item-content')
        ->toContain('gs-id="kpi-users"')
        ->toContain('data-type="stat"')
        ->toContain('data-meta=')
        ->toContain('gs-x="2"')
        ->toContain('gs-y="1"')
        ->toContain('gs-w="4"')
        ->toContain('gs-h="2"')
        ->toContain('Users card');
});

it('renders the editable dashboard template view', function () {
    $html = View::make('daisy::templates.layout.editable-grid', [
        'editable' => false,
        'static' => true,
    ])->render();

    expect($html)
        ->toContain('Editable dashboard')
        ->toContain('Team priorities')
        ->toContain('Release checklist')
        ->toContain('data-module="editable-grid"')
        ->toContain('"static":true');
});
