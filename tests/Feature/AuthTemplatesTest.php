<?php

use Illuminate\Support\Facades\View;

it('renders login-split template', function () {
    $html = View::make('daisy::templates.auth.login-split', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        'backgroundImage' => '/img/divers/divers-6.jpg',
    ])->render();

    expect($html)
        ->toContain('min-h-screen')
        ->toContain('form'); // contains a form
});

it('renders login-simple template', function () {
    $html = View::make('daisy::templates.auth.login-simple')->render();

    expect($html)
        ->toContain('max-w-md')
        ->toContain('form');
});

it('renders reset-password template', function () {
    $html = View::make('daisy::templates.auth.reset-password')->render();

    expect($html)
        ->toContain(__('auth.reset_password'))
        ->toContain('form');
});

it('renders verify-email template', function () {
    $html = View::make('daisy::templates.auth.verify-email')->render();

    expect($html)
        ->toContain(__('auth.verify_email'))
        ->toContain(__('auth.resend_verification'));
});

it('renders resend-verification template', function () {
    $html = View::make('daisy::templates.auth.resend-verification')->render();

    expect($html)
        ->toContain(__('auth.resend_verification'))
        ->toContain('form');
});
