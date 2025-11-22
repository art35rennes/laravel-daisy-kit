<?php

use Illuminate\Support\Facades\View;
use Illuminate\Support\MessageBag;

beforeEach(function () {
    View::share('errors', new MessageBag);
});

it('renders profile-view template', function () {
    $html = View::make('daisy::templates.profile.profile-view', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        'profile' => ['name' => 'John Doe', 'email' => 'john@example.com'],
    ])->render();

    expect($html)
        ->toContain('John Doe')
        ->toContain('john@example.com')
        ->toContain(__('profile.profile'));
});

it('renders profile-view template with stats', function () {
    $html = View::make('daisy::templates.profile.profile-view', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        'profile' => ['name' => 'John Doe'],
        'stats' => [
            ['label' => 'Posts', 'value' => 42],
            ['label' => 'Followers', 'value' => 1234],
        ],
    ])->render();

    expect($html)
        ->toContain('Posts')
        ->toContain('42')
        ->toContain('Followers')
        ->toContain('1234');
});

it('renders profile-view template with badges', function () {
    $html = View::make('daisy::templates.profile.profile-view', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        'profile' => ['name' => 'John Doe'],
        'badges' => [
            ['label' => 'Early Adopter', 'color' => 'primary'],
            ['label' => 'Verified', 'color' => 'success'],
        ],
    ])->render();

    expect($html)
        ->toContain('Early Adopter')
        ->toContain('Verified');
});

it('renders profile-view template with timeline', function () {
    $html = View::make('daisy::templates.profile.profile-view', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        'profile' => ['name' => 'John Doe'],
        'timeline' => [
            ['date' => '2024-01-15', 'title' => 'A rejoint la plateforme'],
            ['date' => '2024-01-20', 'title' => 'Premier post publié'],
        ],
    ])->render();

    expect($html)
        ->toContain('A rejoint la plateforme')
        ->toContain('Premier post publié');
});

it('renders profile-edit template', function () {
    $html = View::make('daisy::templates.profile.profile-edit', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        'profile' => ['name' => 'John Doe', 'email' => 'john@example.com'],
    ])->render();

    expect($html)
        ->toContain(__('profile.edit_profile'))
        ->toContain('form')
        ->toContain('name')
        ->toContain('email');
});

it('renders profile-edit template in readonly mode', function () {
    $html = View::make('daisy::templates.profile.profile-edit', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        'profile' => ['name' => 'John Doe', 'email' => 'john@example.com'],
        'readonly' => true,
    ])->render();

    expect($html)
        ->toContain(__('profile.edit_profile'))
        ->not->toContain('<form')
        ->toContain('John Doe')
        ->toContain('john@example.com');
});

it('renders profile-edit template with avatar section', function () {
    $html = View::make('daisy::templates.profile.profile-edit', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        'profile' => ['name' => 'John Doe', 'avatar' => '/path/to/avatar.jpg'],
        'showAvatar' => true,
    ])->render();

    expect($html)
        ->toContain(__('profile.avatar'))
        ->toContain(__('profile.upload_avatar'));
});

it('renders profile-edit template without optional fields', function () {
    $html = View::make('daisy::templates.profile.profile-edit', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        'profile' => ['name' => 'John Doe'],
        'showPhone' => false,
        'showLocation' => false,
        'showWebsite' => false,
    ])->render();

    expect($html)
        ->not->toContain('phone')
        ->not->toContain('location')
        ->not->toContain('website');
});

it('renders profile-settings template', function () {
    $html = View::make('daisy::templates.profile.profile-settings', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
    ])->render();

    expect($html)
        ->toContain(__('profile.settings'))
        ->toContain('form')
        ->toContain(__('profile.preferences'));
});

it('renders profile-settings template in readonly mode', function () {
    $html = View::make('daisy::templates.profile.profile-settings', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        'readonly' => true,
    ])->render();

    expect($html)
        ->toContain(__('profile.settings'))
        ->not->toContain('form')
        ->toContain(__('profile.preferences'));
});

it('renders profile-settings template with preferences tab', function () {
    $html = View::make('daisy::templates.profile.profile-settings', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        'showPreferences' => true,
    ])->render();

    expect($html)
        ->toContain(__('profile.preferences'))
        ->toContain(__('profile.language'))
        ->toContain(__('profile.timezone'));
});

it('renders profile-settings template with notifications tab', function () {
    $html = View::make('daisy::templates.profile.profile-settings', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        'showNotifications' => true,
    ])->render();

    expect($html)
        ->toContain(__('profile.notifications'))
        ->toContain('notify_email')
        ->toContain('notify_push');
});

it('renders profile-settings template with security tab', function () {
    $html = View::make('daisy::templates.profile.profile-settings', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        'showSecurity' => true,
    ])->render();

    expect($html)
        ->toContain(__('profile.security'))
        ->toContain(__('profile.change_password'))
        ->toContain(__('profile.two_factor_auth'));
});

it('renders profile-settings template with appearance tab', function () {
    $html = View::make('daisy::templates.profile.profile-settings', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        'showTheme' => true,
    ])->render();

    expect($html)
        ->toContain(__('profile.appearance'))
        ->toContain(__('profile.theme'));
});

it('renders profile-settings template without privacy tab', function () {
    $html = View::make('daisy::templates.profile.profile-settings', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        'showPrivacy' => false,
    ])->render();

    expect($html)
        ->not->toContain(__('profile.privacy'));
});

it('renders profile-view template with isOwnProfile detection', function () {
    $user = (object) ['id' => 1, 'name' => 'John Doe'];

    // Pass isOwnProfile directly to avoid database dependency
    // Also provide URLs so buttons are rendered
    $html = View::make('daisy::templates.profile.profile-view', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        'profile' => $user,
        'isOwnProfile' => true,
        'profileEditUrl' => '/profile/edit',
        'profileSettingsUrl' => '/profile/settings',
    ])->render();

    expect($html)
        ->toContain(__('profile.edit_profile'))
        ->toContain(__('profile.settings'));
});

it('renders profile-edit template with old values', function () {
    request()->merge(['name' => 'Jane Doe']);

    $html = View::make('daisy::templates.profile.profile-edit', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        'profile' => ['name' => 'John Doe'],
    ])->render();

    // Old values should be used if available
    expect($html)
        ->toContain('form');
});

it('renders profile-settings template with preferences data', function () {
    $html = View::make('daisy::templates.profile.profile-settings', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        'preferences' => [
            'language' => 'en',
            'timezone' => 'America/New_York',
        ],
    ])->render();

    expect($html)
        ->toContain('selected')
        ->toContain('America/New_York');
});
