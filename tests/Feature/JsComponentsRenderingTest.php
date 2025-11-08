<?php

use Illuminate\Support\Facades\View;

it('renders color-picker with module override', function () {
    $html = View::make('daisy::components.ui.inputs.color-picker', [
        'mode' => 'advanced',
        'value' => '#ff0000',
        'module' => 'color-picker',
    ])->render();

    expect($html)
        ->toContain('data-module="color-picker"')
        ->toContain('data-colorpicker="1"');
});

it('renders chart with module override', function () {
    $html = View::make('daisy::components.ui.advanced.chart', [
        'type' => 'bar',
        'labels' => ['A', 'B'],
        'datasets' => [['label' => 'X', 'data' => [1,2]]],
        'module' => 'chart',
    ])->render();

    expect($html)
        ->toContain('data-module="chart"')
        ->toContain('data-chart="1"');
});

it('renders table with selection attribute', function () {
    $html = View::make('daisy::components.ui.data-display.table', [
        'headers' => ['H1','H2'],
        'rows' => [['R1C1','R1C2']],
        'selection' => 'single',
    ])->render();

    expect($html)
        ->toContain('data-table-select="single"')
        ->toContain('<table');
});

it('renders calendar-full with eventsUrl', function () {
    $html = View::make('daisy::components.ui.advanced.calendar-full', [
        'eventsUrl' => '/api/events',
        'view' => 'month',
    ])->render();

    expect($html)
        ->toContain('data-module="calendar-full"')
        ->toContain('data-events-url="/api/events"');
});


