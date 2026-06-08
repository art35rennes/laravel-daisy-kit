<?php

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;

it('renders hover-gallery with images from props', function () {
    $html = View::make('daisy::components.ui.media.hover-gallery', [
        'images' => [
            ['src' => 'https://example.com/a.webp', 'alt' => 'A'],
            ['src' => 'https://example.com/b.webp', 'alt' => 'B'],
        ],
    ])->render();

    expect($html)
        ->toContain('hover-gallery')
        ->toContain('max-w-60')
        ->toContain('https://example.com/a.webp')
        ->toContain('https://example.com/b.webp');
});

it('renders fab with flower modifier', function () {
    $html = View::make('daisy::components.ui.utilities.fab', [
        'flower' => true,
        'slot' => new HtmlString('<button type="button" class="btn btn-circle">A</button>'),
    ])->render();

    expect($html)->toContain('fab fab-flower');
});

it('renders hover-3d with eight zone divs', function () {
    $html = View::make('daisy::components.ui.advanced.hover-3d', [
        'slot' => new HtmlString('<figure><img src="https://example.com/x.webp" alt="" /></figure>'),
    ])->render();

    expect($html)
        ->toContain('hover-3d')
        ->toContain('https://example.com/x.webp');

    expect(substr_count($html, '<div></div>'))->toBe(8);
});

it('renders text-rotate from words array', function () {
    $html = View::make('daisy::components.ui.advanced.text-rotate', [
        'words' => ['One', 'Two', 'Three'],
    ])->render();

    expect($html)
        ->toContain('text-rotate')
        ->toContain('One')
        ->toContain('Two')
        ->toContain('Three');
});

it('renders skeleton text variant', function () {
    $html = View::make('daisy::components.ui.feedback.skeleton', [
        'variant' => 'text',
        'width' => 'w-full',
    ])->render();

    expect($html)->toContain('skeleton skeleton-text')->toContain('w-full');
});

it('renders dropdown with dropdown-close when forceClose is true', function () {
    $html = View::make('daisy::components.ui.overlay.dropdown', [
        'id' => 'user-menu',
        'label' => 'Open',
        'triggerLabel' => 'Open user menu',
        'contentRole' => 'menu',
        'forceClose' => true,
        'slot' => new HtmlString('<li><a>Item</a></li>'),
    ])->render();

    expect($html)
        ->toContain('id="user-menu"')
        ->toContain('aria-label="Open user menu"')
        ->toContain('aria-controls="user-menu-content"')
        ->toContain('role="menu"')
        ->toContain('dropdown-close');
});

it('maps card compact to card-sm instead of removed card-compact', function () {
    $html = View::make('daisy::components.ui.layout.card', [
        'compact' => true,
        'slot' => new HtmlString('Hi'),
    ])->render();

    expect($html)
        ->toContain('card-sm')
        ->not->toContain('card-compact');
});

it('uses bg-black slash opacity for hero overlay by default', function () {
    $html = View::make('daisy::components.ui.layout.hero', [
        'overlay' => true,
        'slot' => new HtmlString('Content'),
    ])->render();

    expect($html)->toContain('bg-black/60')->not->toContain('bg-opacity-');
});

it('renders pagination with DaisyUI join and btn join-item', function () {
    $html = View::make('daisy::components.ui.navigation.pagination', [
        'total' => 12,
        'current' => 6,
        'color' => 'primary',
        'outline' => true,
    ])->render();

    expect($html)
        ->toContain('join join-horizontal')
        ->toContain('btn join-item')
        ->toContain('btn-outline')
        ->toContain('btn-active')
        ->toContain('&hellip;');
});

it('renders pagination from a Laravel paginator with page links', function () {
    $paginator = new LengthAwarePaginator(
        items: range(1, 10),
        total: 50,
        perPage: 10,
        currentPage: 3,
        options: ['path' => '/users'],
    );

    $html = View::make('daisy::components.ui.navigation.pagination', [
        'paginator' => $paginator,
        'urlWindow' => true,
    ])->render();

    expect($html)
        ->toContain('href="/users?page=2"')
        ->toContain('href="/users?page=3"')
        ->toContain('href="/users?page=4"')
        ->toContain('aria-current="page"')
        ->toContain('btn-active');
});

it('renders stepper validation hooks for guarded flows', function () {
    $html = View::make('daisy::components.ui.navigation.stepper', [
        'items' => ['Account', 'Profile'],
        'validateBeforeNext' => true,
    ])->render();

    expect($html)
        ->toContain('data-validate-before-next="true"')
        ->toContain('Account')
        ->toContain('Profile');
});
