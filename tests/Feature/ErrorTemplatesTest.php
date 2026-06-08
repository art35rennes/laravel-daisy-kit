<?php

use Illuminate\Support\Facades\View;

it('renders generic error template without debug details when app debug is disabled', function () {
    config(['app.debug' => false]);

    $html = View::make('daisy::templates.errors.error', [
        'statusCode' => 500,
        'showDetails' => true,
        'exception' => new RuntimeException('Sensitive backend detail'),
    ])->render();

    expect($html)
        ->toContain(__('daisy::errors.500_title'))
        ->not->toContain('Sensitive backend detail');
});

it('renders loading template with a custom page title and message', function () {
    $html = View::make('daisy::templates.errors.loading-state', [
        'title' => 'Processing export',
        'message' => 'Preparing your file',
    ])->render();

    expect($html)
        ->toContain('Processing export')
        ->toContain('Preparing your file');
});

it('renders maintenance template without exposing allowed IPs by default', function () {
    $html = View::make('daisy::templates.errors.maintenance', [
        'allowedIps' => ['10.0.0.1'],
    ])->render();

    expect($html)
        ->toContain(__('daisy::maintenance.maintenance'))
        ->not->toContain('10.0.0.1');
});

it('can intentionally render maintenance allowed IPs for trusted internal screens', function () {
    $html = View::make('daisy::templates.errors.maintenance', [
        'allowedIps' => ['10.0.0.1'],
        'showAllowedIps' => true,
    ])->render();

    expect($html)->toContain('10.0.0.1');
});
