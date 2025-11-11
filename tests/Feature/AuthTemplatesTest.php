<?php

use Illuminate\Support\Facades\View;
use Illuminate\Support\MessageBag;

beforeEach(function () {
    View::share('errors', new MessageBag);
});

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

it('renders forgot-password template', function () {
    $html = View::make('daisy::templates.auth.forgot-password')->render();

    expect($html)
        ->toContain(__('auth.forgot_password'))
        ->toContain(__('auth.forgot_password_description'))
        ->toContain('form');
});

it('renders forgot-password template with status message', function () {
    session(['status' => 'We have emailed your password reset link!']);

    $html = View::make('daisy::templates.auth.forgot-password')->render();

    expect($html)
        ->toContain('We have emailed your password reset link!')
        ->toContain('alert-success');
});

it('renders register-simple template', function () {
    $html = View::make('daisy::templates.auth.register-simple')->render();

    expect($html)
        ->toContain(__('auth.register'))
        ->toContain('form')
        ->toContain('name')
        ->toContain('email')
        ->toContain('password');
});

it('renders register-simple template without password confirmation', function () {
    $html = View::make('daisy::templates.auth.register-simple', [
        'passwordConfirmation' => false,
    ])->render();

    expect($html)
        ->not->toContain('password_confirmation');
});

it('renders register-simple template without terms acceptance', function () {
    $html = View::make('daisy::templates.auth.register-simple', [
        'acceptTerms' => false,
    ])->render();

    expect($html)
        ->not->toContain('terms');
});

it('renders register-split template', function () {
    $html = View::make('daisy::templates.auth.register-split', [
        'backgroundImage' => '/img/divers/divers-6.jpg',
    ])->render();

    expect($html)
        ->toContain(__('auth.register'))
        ->toContain('min-h-screen')
        ->toContain('grid-cols-1')
        ->toContain('lg:grid-cols-2')
        ->toContain('form');
});

it('renders register-split template with testimonial', function () {
    $html = View::make('daisy::templates.auth.register-split', [
        'backgroundImage' => '/img/divers/divers-6.jpg',
        'showTestimonial' => true,
        'testimonial' => [
            'quote' => 'Great service!',
            'author' => 'John Doe',
            'role' => 'CEO',
            'avatar' => '/img/people/people-1.jpg',
            'rating' => 5,
        ],
    ])->render();

    expect($html)
        ->toContain('Great service!')
        ->toContain('John Doe')
        ->toContain('CEO')
        ->toContain('rating');
});

it('renders two-factor template', function () {
    $html = View::make('daisy::templates.auth.two-factor')->render();

    expect($html)
        ->toContain(__('auth.two_factor'))
        ->toContain(__('auth.two_factor_description'))
        ->toContain(__('auth.two_factor_instructions'))
        ->toContain('form')
        ->toContain('maxlength="6"')
        ->toContain('pattern="[0-9]{6}"');
});

it('renders two-factor template without recovery link', function () {
    $html = View::make('daisy::templates.auth.two-factor', [
        'showRecovery' => false,
    ])->render();

    expect($html)
        ->not->toContain(__('auth.two_factor_recovery'));
});

it('renders two-factor template without logout link', function () {
    $html = View::make('daisy::templates.auth.two-factor', [
        'showLogout' => false,
    ])->render();

    expect($html)
        ->not->toContain(__('auth.two_factor_logout'));
});
