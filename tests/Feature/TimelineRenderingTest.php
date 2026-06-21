<?php

use Illuminate\Support\Facades\View;

it('renders the existing timeline shape by default', function () {
    $html = View::make('daisy::components.ui.data-display.timeline', [
        'items' => [
            ['when' => '1984', 'title' => 'Macintosh', 'content' => 'First Macintosh computer'],
            ['when' => '1998', 'title' => 'iMac', 'content' => 'Colorful iMac'],
        ],
    ])->render();

    expect($html)
        ->toContain('timeline timeline-vertical')
        ->toContain('timeline-start')
        ->toContain('1984')
        ->toContain('timeline-middle')
        ->toContain('timeline-end timeline-box')
        ->toContain('Macintosh')
        ->toContain('First Macintosh computer');
});

it('renders horizontal bottom-side timeline items like daisyUI examples', function () {
    $html = View::make('daisy::components.ui.data-display.timeline', [
        'orientation' => 'horizontal',
        'side' => 'end',
        'items' => [
            ['title' => 'First Macintosh computer'],
            ['title' => 'iMac'],
        ],
    ])->render();

    expect($html)
        ->toContain('timeline timeline-horizontal')
        ->toContain('timeline-end timeline-box')
        ->toContain('First Macintosh computer')
        ->not->toContain('timeline-start');
});

it('renders top-side timeline items without icons', function () {
    $html = View::make('daisy::components.ui.data-display.timeline', [
        'orientation' => 'horizontal',
        'side' => 'start',
        'boxOn' => 'start',
        'showIcons' => false,
        'items' => [
            ['title' => 'First Macintosh computer'],
            ['title' => 'iMac'],
        ],
    ])->render();

    expect($html)
        ->toContain('timeline-start timeline-box')
        ->toContain('First Macintosh computer')
        ->not->toContain('timeline-middle')
        ->not->toContain('timeline-end');
});

it('renders compact snap icon and alternating timeline boxes', function () {
    $html = View::make('daisy::components.ui.data-display.timeline', [
        'compact' => true,
        'snapIcon' => true,
        'side' => 'alternate',
        'boxOn' => null,
        'items' => [
            ['title' => 'First Macintosh computer', 'boxOn' => 'start'],
            ['title' => 'iMac', 'boxOn' => 'end'],
        ],
    ])->render();

    expect($html)
        ->toContain('timeline-compact')
        ->toContain('timeline-snap-icon')
        ->toContain('timeline-start timeline-box')
        ->toContain('timeline-end timeline-box')
        ->toContain('First Macintosh computer')
        ->toContain('iMac');
});

it('renders responsive timeline orientation and colorful lines', function () {
    $html = View::make('daisy::components.ui.data-display.timeline', [
        'orientation' => 'responsive',
        'lineClass' => 'bg-primary',
        'iconClass' => 'text-primary h-5 w-5',
        'items' => [
            ['when' => '1984', 'title' => 'Macintosh'],
            ['when' => '1998', 'title' => 'iMac', 'lineClass' => 'bg-secondary', 'iconClass' => 'text-secondary h-5 w-5'],
        ],
    ])->render();

    expect($html)
        ->toContain('timeline-vertical lg:timeline-horizontal')
        ->toContain('class="bg-primary"')
        ->toContain('class="bg-secondary"')
        ->toContain('class="text-primary h-5 w-5"')
        ->toContain('class="text-secondary h-5 w-5"');
});
