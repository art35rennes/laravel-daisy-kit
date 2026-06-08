<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\MessageBag;
use Illuminate\View\ComponentAttributeBag;

beforeEach(function () {
    View::share('errors', new MessageBag);
});

it('renders login-split template', function () {
    $html = View::make('daisy::templates.auth.login-split', [
        'attributes' => new ComponentAttributeBag([]),
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
        ->toContain(__('daisy::auth.reset_password'))
        ->toContain(__('daisy::auth.reset_password_description'))
        ->toContain('name="password"')
        ->toContain('name="password_confirmation"')
        ->toContain('form');
});

it('renders login-simple password label with forgot password link separated', function () {
    $html = View::make('daisy::templates.auth.login-simple', [
        'forgotPasswordUrl' => '/forgot-password',
        'showSignup' => false,
    ])->render();

    expect($html)
        ->toContain('justify-between')
        ->toContain('shrink-0')
        ->toContain('Forgot password?')
        ->not->toContain(__('daisy::auth.first_time'));
});

it('renders login-split public component alias from the template source', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::templates.auth.login-split
            forgot-password-url="/forgot-password"
            :show-signup="false"
        />
    BLADE);

    expect($html)
        ->toContain('shrink-0')
        ->toContain('/forgot-password')
        ->not->toContain(__('daisy::auth.first_time'));
});

it('renders login-split without signup when disabled', function () {
    $html = View::make('daisy::templates.auth.login-split', [
        'showSignup' => false,
    ])->render();

    expect($html)->not->toContain(__('daisy::auth.first_time'));
});

it('renders reset-password with token and prefilled email', function () {
    $html = View::make('daisy::templates.auth.reset-password', [
        'token' => 'reset-token',
        'email' => 'jane@example.com',
    ])->render();

    expect($html)
        ->toContain('name="token" value="reset-token"')
        ->toContain('value="jane@example.com"')
        ->toContain('name="password"')
        ->toContain('name="password_confirmation"');
});

it('spoofs non-post auth template methods through Laravel form method spoofing', function () {
    $html = View::make('daisy::templates.auth.login-simple', [
        'method' => 'PATCH',
        'showSignup' => false,
    ])->render();

    expect($html)
        ->toContain('method="POST"')
        ->toContain('name="_method"')
        ->toContain('value="PATCH"')
        ->toContain('name="_token"');
});

it('omits csrf tokens for get auth template methods', function () {
    $html = View::make('daisy::templates.auth.forgot-password', [
        'method' => 'GET',
    ])->render();

    expect($html)
        ->toContain('method="GET"')
        ->not->toContain('name="_token"');
});

it('renders verify-email template', function () {
    $html = View::make('daisy::templates.auth.verify-email')->render();

    expect($html)
        ->toContain('Verify your email')
        ->toContain(__('daisy::auth.verify_email'))
        ->not->toContain('daisy::auth.verify_email')
        ->toContain(__('daisy::auth.resend_verification'));
});

it('renders resend-verification template', function () {
    $html = View::make('daisy::templates.auth.resend-verification')->render();

    expect($html)
        ->toContain(__('daisy::auth.resend_verification'))
        ->toContain('form');
});

it('renders forgot-password template', function () {
    $html = View::make('daisy::templates.auth.forgot-password')->render();

    expect($html)
        ->toContain(__('daisy::auth.forgot_password'))
        ->toContain(__('daisy::auth.forgot_password_description'))
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
        ->toContain(__('daisy::auth.register'))
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

it('renders registration policy links with blank-target rel protection', function () {
    $html = View::make('daisy::templates.auth.register-simple', [
        'termsUrl' => 'https://example.com/terms',
        'privacyUrl' => 'https://example.com/privacy',
    ])->render();

    expect($html)
        ->toContain('target="_blank" rel="noopener noreferrer"')
        ->toContain('https://example.com/terms')
        ->toContain('https://example.com/privacy');
});

it('renders register-split template', function () {
    $html = View::make('daisy::templates.auth.register-split', [
        'backgroundImage' => '/img/divers/divers-6.jpg',
    ])->render();

    expect($html)
        ->toContain(__('daisy::auth.register'))
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
        ->toContain('Two-factor authentication')
        ->toContain(__('daisy::auth.two_factor'))
        ->toContain(__('daisy::auth.two_factor_description'))
        ->toContain(__('daisy::auth.two_factor_instructions'))
        ->toContain('form')
        ->toContain('data-module="otp-code"')
        ->toContain('data-otp-digit');

    // Vérifier qu'il y a 6 inputs pour le code OTP en comptant les balises <input avec data-otp-digit
    preg_match_all('/<input[^>]*data-otp-digit[^>]*>/i', $html, $matches);
    expect(count($matches[0]))->toBe(6);
});

it('renders two-factor template without recovery link', function () {
    $html = View::make('daisy::templates.auth.two-factor', [
        'showRecovery' => false,
    ])->render();

    expect($html)
        ->not->toContain(__('daisy::auth.two_factor_recovery'));
});

it('renders two-factor template without logout link', function () {
    $html = View::make('daisy::templates.auth.two-factor', [
        'showLogout' => false,
    ])->render();

    expect($html)
        ->not->toContain(__('daisy::auth.two_factor_logout'));
});
