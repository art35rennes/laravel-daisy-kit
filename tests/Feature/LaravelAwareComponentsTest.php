<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

it('lets the app layout customize body html and default font loading', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::layout.app title="Dashboard" html-class="scroll-smooth" body-class="app-shell" :load-default-font="false">
            Content
        </x-daisy::layout.app>
    BLADE);

    expect($html)
        ->toContain('class="scroll-smooth"')
        ->toContain('app-shell')
        ->not->toContain('fonts.bunny.net/css');
});

it('does not render unsafe app layout font URLs', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::layout.app title="Dashboard" font-url="javascript:alert(1)">
            Content
        </x-daisy::layout.app>
    BLADE);

    expect($html)
        ->not->toContain('href="javascript:alert(1)"')
        ->not->toContain('rel="stylesheet"');
});

it('does not render unsafe hero image URLs', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.layout.hero image-url="javascript:alert(1)">
            Content
        </x-daisy::ui.layout.hero>
    BLADE);

    expect($html)
        ->not->toContain("background-image: url('javascript:alert(1)')")
        ->not->toContain('javascript:alert(1)');
});

it('lets the navbar sidebar layout hide and configure theme controls', function () {
    $hidden = Blade::render('<x-daisy::layout.navbar-sidebar-layout :show-theme-controller="false">Content</x-daisy::layout.navbar-sidebar-layout>');

    $custom = Blade::render(<<<'BLADE'
        <x-daisy::layout.navbar-sidebar-layout :themes="['light', 'dark']" theme-label="Appearance">
            Content
        </x-daisy::layout.navbar-sidebar-layout>
    BLADE);

    expect($hidden)
        ->not->toContain('theme-controller')
        ->and($custom)
        ->toContain('Appearance')
        ->toContain('light')
        ->toContain('dark');
});

it('renders button iconName and accessible loading state', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.inputs.button icon-name="bi-check" icon-position="right" loading>
            Save
        </x-daisy::ui.inputs.button>
    BLADE);

    expect($html)
        ->toContain('aria-busy="true"')
        ->toContain('<svg')
        ->toContain('Save');
});

it('renders alert session messages validation errors roles and dismiss controls', function () {
    session()->flash('status', 'Saved');

    $errors = new ViewErrorBag;
    $errors->put('default', new MessageBag(['email' => ['Invalid email']]));

    $sessionAlert = View::make('daisy::components.ui.feedback.alert', [
        'color' => 'success',
        'sessionKey' => 'status',
        'dismissible' => true,
    ])->render();

    $errorAlert = View::make('daisy::components.ui.feedback.alert', [
        'color' => 'error',
        'showErrors' => true,
        'errors' => $errors,
    ])->render();

    expect($sessionAlert)
        ->toContain('role="status"')
        ->toContain('Saved')
        ->toContain('aria-label="Close alert"')
        ->toContain('data-module="alert-dismiss"')
        ->toContain('data-alert-dismiss')
        ->not->toContain('onclick=')
        ->and($errorAlert)
        ->toContain('role="alert"')
        ->toContain('Invalid email');
});

it('does not render an alert when the session flash is empty', function () {
    session()->flash('status', '');

    $html = View::make('daisy::components.ui.feedback.alert', [
        'color' => 'success',
        'sessionKey' => 'status',
        'dismissible' => true,
    ])->render();

    expect($html)->toBe('');
});

it('wires form field ids descriptions old input and validation state into inputs', function () {
    $this->withSession(['_old_input' => ['email' => 'old@example.com']]);

    $errors = new ViewErrorBag;
    $errors->put('default', new MessageBag(['email' => ['Email is required']]));
    view()->share('errors', $errors);

    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.partials.form-field name="email" label="Email" hint="Used for login">
            <x-daisy::ui.inputs.input name="email" :error="$errors->first('email')" />
        </x-daisy::ui.partials.form-field>
    BLADE, ['errors' => $errors]);

    expect($html)
        ->toContain('for="email"')
        ->toContain('id="email"')
        ->toContain('name="email"')
        ->toContain('value="old@example.com"')
        ->toContain('aria-invalid="true"')
        ->toContain('aria-describedby="email-error"')
        ->toContain('id="email-hint"')
        ->toContain('id="email-error"');
});

it('renders Laravel aware select options selected value and validation state', function () {
    $html = View::make('daisy::components.ui.inputs.select', [
        'name' => 'role',
        'value' => 'admin',
        'error' => 'Role is invalid',
        'options' => [
            ['value' => 'user', 'label' => 'User'],
            ['value' => 'admin', 'label' => 'Administrator'],
        ],
    ])->render();

    expect($html)
        ->toContain('id="role"')
        ->toContain('name="role"')
        ->toContain('select-error')
        ->toContain('aria-invalid="true"')
        ->toContain('value="admin" selected')
        ->toContain('Administrator');
});

it('renders Laravel aware checkbox names values old input and validation state', function () {
    $this->withSession(['_old_input' => ['terms' => 'accepted']]);

    $html = View::make('daisy::components.ui.inputs.checkbox', [
        'name' => 'terms',
        'value' => 'accepted',
        'uncheckedValue' => '0',
        'error' => 'Terms are required',
    ])->render();

    expect($html)
        ->toContain('type="hidden" name="terms" value="0"')
        ->toContain('type="checkbox"')
        ->toContain('id="terms"')
        ->toContain('name="terms"')
        ->toContain('value="accepted"')
        ->toContain('checked')
        ->toContain('checkbox-error')
        ->toContain('aria-invalid="true"')
        ->toContain('aria-describedby="terms-error"');
});

it('lets checkbox old input override an explicit checked default', function () {
    $this->withSession(['_old_input' => ['published' => '0']]);

    $html = View::make('daisy::components.ui.inputs.checkbox', [
        'name' => 'published',
        'checked' => true,
        'uncheckedValue' => '0',
        'error' => 'Choose publication state',
    ])->render();

    expect($html)
        ->toContain('type="hidden" name="published" value="0"')
        ->toContain('id="published"')
        ->toContain('name="published"')
        ->toContain('value="1"')
        ->toContain('checkbox-error')
        ->toContain('aria-invalid="true"')
        ->not->toContain('checked');
});

it('renders Laravel aware textarea old input and validation state', function () {
    $this->withSession(['_old_input' => ['bio' => 'Old biography']]);

    $html = View::make('daisy::components.ui.inputs.textarea', [
        'name' => 'bio',
        'value' => 'Stored biography',
        'error' => 'Bio is too long',
    ])->render();

    expect($html)
        ->toContain('id="bio"')
        ->toContain('name="bio"')
        ->toContain('textarea-error')
        ->toContain('aria-invalid="true"')
        ->toContain('aria-describedby="bio-error"')
        ->toContain('>Old biography</textarea>');
});

it('renders navigation tabs with visibility icons and error markers', function () {
    $errors = new ViewErrorBag;
    $errors->put('default', new MessageBag(['profile.name' => ['Required']]));

    $html = View::make('daisy::components.ui.navigation.tabs', [
        'errorBag' => $errors,
        'items' => [
            ['label' => 'Profile', 'iconName' => 'bi-person', 'errorKey' => 'profile.name'],
            ['label' => 'Hidden', 'visible' => false],
            ['label' => 'Billing', 'href' => '/billing'],
            ['label' => '<script>Unsafe</script>'],
        ],
    ])->render();

    expect($html)
        ->toContain('Profile')
        ->toContain('Billing')
        ->toContain('<svg')
        ->toContain('text-error')
        ->toContain('Error')
        ->toContain('&lt;script&gt;Unsafe&lt;/script&gt;')
        ->not->toContain('<script>Unsafe</script>')
        ->not->toContain('Hidden');
});

it('renders table toolbar and actions slots', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.data-display.table
            :columns="[['key' => 'name', 'label' => 'Name']]"
            :rows="[['name' => 'Jane']]"
        >
            <x-slot:toolbar><button type="button">Import</button></x-slot:toolbar>
            <x-slot:actions><a href="/users/create">Create</a></x-slot:actions>
        </x-daisy::ui.data-display.table>
    BLADE);

    expect($html)
        ->toContain('Import')
        ->toContain('/users/create')
        ->toContain('Jane');
});

it('renders CRUD layout and section ergonomic slots', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.layout.crud-layout actions-alignment="between">
            <x-slot:header><h1>Edit profile</h1></x-slot:header>
            <x-daisy::ui.layout.crud-section title="Profile" sticky-aside actions-alignment="start">
                <x-slot:headerActions><a href="/help">Help</a></x-slot:headerActions>
                <x-slot:aside><p>Aside help</p></x-slot:aside>
                Main form
                <x-slot:actions><button type="button">Save</button></x-slot:actions>
            </x-daisy::ui.layout.crud-section>
            <x-slot:actions><button type="button">Cancel</button><button type="submit">Save all</button></x-slot:actions>
        </x-daisy::ui.layout.crud-layout>
    BLADE);

    expect($html)
        ->toContain('Edit profile')
        ->toContain('Help')
        ->toContain('Aside help')
        ->toContain('lg:sticky lg:top-6')
        ->toContain('justify-between')
        ->toContain('Save all');
});

it('renders modal header footer and accessible labels', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.overlay.modal id="delete-user" title="Delete user" close-label="Close delete dialog" initial-focus="[data-confirm-delete]" :teleport="false" open>
            <x-slot:header><h2>Custom header</h2></x-slot:header>
            Body
            <x-slot:footer><button type="button" data-confirm-delete>Confirm</button></x-slot:footer>
        </x-daisy::ui.overlay.modal>
    BLADE);

    expect($html)
        ->toContain('id="delete-user"')
        ->toContain('Custom header')
        ->toContain('Close delete dialog')
        ->toContain('Confirm')
        ->toContain('aria-labelledby="delete-user-title"')
        ->toContain('[data-confirm-delete]')
        ->toContain('focusInitialTarget')
        ->not->toContain('document.body.appendChild');
});
