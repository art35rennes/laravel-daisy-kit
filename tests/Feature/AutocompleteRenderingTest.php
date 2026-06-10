<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Illuminate\View\ComponentAttributeBag;

it('passes browser autocomplete attributes through package input components', function () {
    $html = View::make('daisy::components.ui.inputs.input', [
        'name' => 'contract_reference',
        'attributes' => new ComponentAttributeBag(['autocomplete' => 'off']),
    ])->render();

    expect($html)
        ->toContain('name="contract_reference"')
        ->toContain('autocomplete="off"');
});

it('passes browser autocomplete attributes through form templates for page level control', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::templates.form.form-simple id="simple-business-form" autocomplete="off" />

        <x-daisy::templates.form.form-inline id="inline-business-form" autocomplete="off" />

        <x-daisy::templates.form.form-with-tabs
            id="tabs-business-form"
            autocomplete="off"
            :tabs="[['id' => 'general', 'label' => 'General']]"
        />

        <x-daisy::templates.form.form-wizard
            id="wizard-business-form"
            autocomplete="off"
            :steps="[['key' => 'details', 'label' => 'Details']]"
        />
    BLADE);

    expect((bool) preg_match('/<form(?=[^>]*id="simple-business-form")(?=[^>]*autocomplete="off")[^>]*>/', $html))->toBeTrue()
        ->and((bool) preg_match('/<form(?=[^>]*id="inline-business-form")(?=[^>]*autocomplete="off")[^>]*>/', $html))->toBeTrue()
        ->and((bool) preg_match('/<form(?=[^>]*id="tabs-business-form")(?=[^>]*autocomplete="off")[^>]*>/', $html))->toBeTrue()
        ->and((bool) preg_match('/<form(?=[^>]*id="wizard-business-form")(?=[^>]*autocomplete="off")[^>]*>/', $html))->toBeTrue();
});

it('allows form kit forms to disable autocomplete in bulk while fields keep explicit hints', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::forms.viewer
            id="business-viewer"
            autocomplete="off"
            :schema="[
                'version' => '1.0',
                'id' => 'business',
                'fields' => [
                    [
                        'id' => 'organization',
                        'type' => 'text',
                        'name' => 'organization',
                        'label' => 'Organization',
                        'attrs' => ['autocomplete' => 'organization'],
                    ],
                ],
            ]"
        />
    BLADE);

    expect((bool) preg_match('/<form(?=[^>]*id="business-viewer")(?=[^>]*autocomplete="off")[^>]*>/', $html))->toBeTrue()
        ->and((bool) preg_match('/<input(?=[^>]*data-form-input="organization")(?=[^>]*autocomplete="organization")[^>]*>/', $html))->toBeTrue();
});

it('keeps semantic autocomplete hints in package owned identity templates', function () {
    View::share('errors', (new ViewErrorBag())->put('default', new MessageBag()));

    $login = Blade::render('<x-daisy::templates.auth.login-simple />');
    $resetPassword = Blade::render('<x-daisy::templates.auth.reset-password />');
    $twoFactor = Blade::render('<x-daisy::templates.auth.two-factor use-recovery-code />');
    $profileEdit = Blade::render(<<<'BLADE'
        <x-daisy::templates.profile.profile-edit
            :show-phone="true"
            :show-location="true"
            :show-website="true"
            :profile="[
                'name' => 'Ada Lovelace',
                'email' => 'ada@example.com',
                'phone' => '+33123456789',
                'location' => 'Rennes',
                'website' => 'https://example.com',
            ]"
        />
    BLADE);
    $profileSettings = Blade::render(<<<'BLADE'
        <x-daisy::templates.profile.profile-settings>
            <x-slot:summary><span>Summary</span></x-slot:summary>
        </x-daisy::templates.profile.profile-settings>
    BLADE);

    expect($login)
        ->toContain('autocomplete="email"')
        ->toContain('autocomplete="current-password"');

    expect($resetPassword)
        ->toContain('autocomplete="username"')
        ->toContain('autocomplete="new-password"');

    expect($twoFactor)->toContain('autocomplete="one-time-code"');

    expect($profileEdit)
        ->toContain('autocomplete="name"')
        ->toContain('autocomplete="email"')
        ->toContain('autocomplete="tel"')
        ->toContain('autocomplete="address-level2"')
        ->toContain('autocomplete="url"');

    expect($profileSettings)
        ->toContain('autocomplete="current-password"')
        ->toContain('autocomplete="new-password"');
});
