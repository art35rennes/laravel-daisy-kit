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

it('renders hero backgrounds with cover utilities when an image is provided', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.layout.hero image-url="/img/example.jpg">
            Content
        </x-daisy::ui.layout.hero>
    BLADE);

    expect($html)
        ->toContain('src="/img/example.jpg"')
        ->toContain('object-cover')
        ->not->toContain('background-image')
        ->not->toContain('style=');
});

it('renders auth shell with optional background cover and overlay mask', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::layout.auth-shell background-image="/img/auth.jpg" overlay-class="bg-black/45">
            <p>Sign in</p>
        </x-daisy::layout.auth-shell>
    BLADE);

    expect($html)
        ->toContain('src="/img/auth.jpg"')
        ->toContain('object-cover')
        ->not->toContain('background-image')
        ->not->toContain('style=')
        ->toContain('bg-black/45')
        ->toContain('Sign in');
});

it('does not render unsafe auth shell image URLs', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::layout.auth-shell background-image="javascript:alert(1)">
            Content
        </x-daisy::layout.auth-shell>
    BLADE);

    expect($html)
        ->not->toContain("background-image: url('javascript:alert(1)')")
        ->not->toContain('javascript:alert(1)');
});

it('renders auth shell with a host background class', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::layout.auth-shell background-class="auth-brand-background">
            Content
        </x-daisy::layout.auth-shell>
    BLADE);

    expect($html)
        ->toContain('auth-brand-background')
        ->toContain('bg-cover')
        ->toContain('Content');
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

it('renders navbar sidebar layout topbar inside drawer content with independent slots', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::layout.navbar-sidebar-layout :show-theme-controller="false">
            <x-slot:navbarStart><span data-navbar-start>Start</span></x-slot:navbarStart>
            <x-slot:navbarCenter><span data-navbar-center>Center</span></x-slot:navbarCenter>
            <x-slot:navbarEnd><span data-navbar-end>End</span></x-slot:navbarEnd>
            Content
        </x-daisy::layout.navbar-sidebar-layout>
    BLADE);

    $drawerContentPosition = strpos($html, 'drawer-content');
    $topbarPosition = strpos($html, 'data-navbar-sidebar-topbar');
    $drawerSidePosition = strpos($html, 'drawer-side');

    expect($html)
        ->toContain('data-navbar-start')
        ->toContain('data-navbar-center')
        ->toContain('data-navbar-end')
        ->toContain('navbar-center')
        ->not->toContain('theme-controller')
        ->and($drawerContentPosition)->toBeInt()->toBeLessThan($topbarPosition)
        ->and($topbarPosition)->toBeInt()->toBeLessThan($drawerSidePosition);
});

it('passes an optional footer slot to the sidebar', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::layout.navbar-sidebar-layout :show-theme-controller="false">
            <x-slot:sidebarFooter>Environment footer</x-slot:sidebarFooter>
            Content
        </x-daisy::layout.navbar-sidebar-layout>
    BLADE);

    expect($html)
        ->toContain('Environment footer')
        ->toContain('data-sidebar-footer')
        ->toContain('text-base-content/50');
});

it('opens external sidebar entries in a separate tab securely', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.navigation.sidebar :sections="$sections" />
    BLADE, [
        'sections' => [[
            'label' => null,
            'items' => [
                [
                    'label' => 'External direct',
                    'href' => 'https://example.test/direct',
                    'external' => true,
                ],
                [
                    'label' => 'Help',
                    'children' => [[
                        'label' => 'External help',
                        'href' => 'https://example.test/help',
                        'external' => true,
                    ]],
                ],
            ],
        ]],
    ]);

    expect($html)
        ->toContain('href="https://example.test/direct"')
        ->toContain('href="https://example.test/help"')
        ->toContain('target="_blank"')
        ->toContain('rel="noopener noreferrer"');
});

it('can constrain navbar sidebar layout topbar content inside an inner container', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::layout.navbar-sidebar-layout :show-theme-controller="false" navbar-container="mx-auto max-w-screen-xl px-4">
            <x-slot:navbarStart><span data-navbar-start>Start</span></x-slot:navbarStart>
            <x-slot:navbarCenter><span data-navbar-center>Center</span></x-slot:navbarCenter>
            <x-slot:navbarEnd><span data-navbar-end>End</span></x-slot:navbarEnd>
            Content
        </x-daisy::layout.navbar-sidebar-layout>
    BLADE);

    expect($html)
        ->toContain('data-navbar-container')
        ->toContain('daisy-navbar-container mx-auto max-w-screen-xl px-4')
        ->toContain('data-navbar-sidebar-topbar');
});

it('renders optional navbar headings before navbar center content', function (string $component) {
    $html = Blade::render(<<<BLADE
        <x-daisy::layout.{$component} :show-theme-controller="false">
            <x-slot:navbarStart><span data-navbar-start>Start</span></x-slot:navbarStart>
            <x-slot:navbarHeading>
                <h1>Suivi et validation des interventions</h1>
                <p>Controlez la qualite des donnees recues</p>
            </x-slot:navbarHeading>
            <x-slot:navbarCenter><span data-navbar-center>Search</span></x-slot:navbarCenter>
            Content
        </x-daisy::layout.{$component}>
    BLADE);

    $startPosition = strpos($html, 'data-navbar-start');
    $headingPosition = strpos($html, 'data-navbar-heading');
    $centerPosition = strpos($html, 'data-navbar-center');

    expect($html)
        ->toContain('Suivi et validation des interventions')
        ->toContain('Controlez la qualite des donnees recues')
        ->toContain('data-navbar-heading')
        ->toContain('sm:flex')
        ->and($startPosition)->toBeInt()->toBeLessThan($headingPosition)
        ->and($headingPosition)->toBeInt()->toBeLessThan($centerPosition);
})->with([
    'navbar-layout',
    'navbar-sidebar-layout',
]);

it('ships navbar heading display rules in package css', function () {
    $css = file_get_contents(__DIR__.'/../../resources/css/app.css');

    expect($css)
        ->toContain('[data-navbar-heading]')
        ->toContain('@media (min-width: 640px)')
        ->toContain('display: flex');
});

it('keeps navbar headings absent unless a host provides the slot', function (string $component) {
    $html = Blade::render(<<<BLADE
        <x-daisy::layout.{$component} :show-theme-controller="false">
            <x-slot:navbarCenter><span data-navbar-center>Search</span></x-slot:navbarCenter>
            Content
        </x-daisy::layout.{$component}>
    BLADE);

    expect($html)
        ->not->toContain('data-navbar-heading')
        ->toContain('data-navbar-center');
})->with([
    'navbar-layout',
    'navbar-sidebar-layout',
]);

it('renders optional navbar heading in the sidebar layout topbar', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::layout.sidebar-layout :show-theme-controller="false">
            <x-slot:navbarHeading>
                <h1>Interventions</h1>
                <p>Suivi operationnel</p>
            </x-slot:navbarHeading>
            Content
        </x-daisy::layout.sidebar-layout>
    BLADE);

    expect($html)
        ->toContain('Interventions')
        ->toContain('Suivi operationnel')
        ->toContain('data-navbar-heading')
        ->toContain('md:flex')
        ->not->toContain('theme-controller');
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

it('renders alert auto dismiss progress and remaining time controls', function () {
    $html = View::make('daisy::components.ui.feedback.alert', [
        'color' => 'success',
        'text' => 'Saved',
        'autoDismissAfter' => 4,
        'showDismissRemaining' => true,
    ])->render();

    expect($html)
        ->toContain('data-module="alert-dismiss"')
        ->toContain('data-alert-auto-dismiss="4000"')
        ->toContain('data-alert-progress')
        ->toContain('data-alert-remaining')
        ->toContain('4s')
        ->not->toContain('style=')
        ->not->toContain('onclick=');
});

it('passes callout auto dismiss options to the underlying alert', function () {
    $html = View::make('daisy::components.ui.feedback.callout', [
        'variant' => 'success',
        'text' => 'Saved',
        'autoDismissMs' => 2500,
        'dismissible' => true,
    ])->render();

    expect($html)
        ->toContain('data-module="alert-dismiss"')
        ->toContain('data-alert-auto-dismiss="2500"')
        ->toContain('data-alert-dismiss')
        ->toContain('progress-success');
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

it('renders form fields with constrained grid alignment classes for labels inputs selects and dates', function () {
    $html = Blade::render(<<<'BLADE'
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
            <x-daisy::ui.partials.form-field
                name="filter[query]"
                id="dashboard-filter-query"
                label="Libelle de recherche volontairement tres long pour verifier la troncature"
            >
                <x-daisy::ui.inputs.input
                    id="dashboard-filter-query"
                    name="filter[query]"
                />
            </x-daisy::ui.partials.form-field>

            <x-daisy::ui.partials.form-field
                name="filter[intervention_type]"
                id="dashboard-filter-intervention-type"
                label="Type d'enquete tres detaille"
            >
                <x-daisy::ui.inputs.select
                    id="dashboard-filter-intervention-type"
                    name="filter[intervention_type]"
                >
                    <option value="long">Option select avec un libelle tres long qui doit rester dans le champ</option>
                </x-daisy::ui.inputs.select>
            </x-daisy::ui.partials.form-field>

            <x-daisy::ui.partials.form-field
                name="filter[started_on]"
                id="dashboard-filter-started-on"
                label="Date de debut"
            >
                <x-daisy::ui.inputs.input
                    type="date"
                    id="dashboard-filter-started-on"
                    name="filter[started_on]"
                />
            </x-daisy::ui.partials.form-field>
        </div>
    BLADE);

    expect(substr_count($html, 'daisy-form-field min-w-0 max-w-full'))->toBe(3)
        ->and(substr_count($html, 'daisy-form-field-control w-full min-w-0 max-w-full'))->toBe(3)
        ->and(substr_count($html, 'daisy-form-field-label'))->toBe(3)
        ->and(substr_count($html, 'data-label-wrap="truncate"'))->toBe(3)
        ->and($html)->toContain('for="dashboard-filter-intervention-type"')
        ->and($html)->toContain('id="dashboard-filter-intervention-type"')
        ->and($html)->toContain('select w-full')
        ->and($html)->toContain('input w-full')
        ->and($html)->toContain('daisy-native-picker-date')
        ->and($html)->toContain('Option select avec un libelle tres long qui doit rester dans le champ');
});

it('ships scoped form field containment styles without targeting host pages', function () {
    $css = file_get_contents(dirname(__DIR__, 2).'/resources/css/app.css');

    expect($css)
        ->toContain('.daisy-form-field {')
        ->toContain('.daisy-form-field-control {')
        ->toContain('.daisy-form-field-control > :is(.input, .select, .textarea, .file-input, input, select, textarea, .dropdown)')
        ->toContain('.daisy-form-field-control > :is(select, .select),')
        ->toContain('text-overflow: ellipsis;')
        ->toContain('.daisy-form-field-label[data-label-wrap="truncate"]')
        ->toContain('min-inline-size: 0;');
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
        ->toContain('space-y-12')
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
        ->toContain('data-module="modal"')
        ->toContain('data-modal-close')
        ->not->toContain('focusInitialTarget')
        ->not->toContain('document.body.appendChild');
});

it('renders start and end modals as full-height side panels', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.overlay.modal id="side-panel" title="Side panel" horizontal="end" :teleport="false" open>
            Body
        </x-daisy::ui.overlay.modal>
    BLADE);

    expect($html)
        ->toContain('modal modal-middle modal-end')
        ->toContain('h-[100svh] max-h-[100svh] rounded-none')
        ->toContain('overflow-y-auto')
        ->not->toContain('max-h-[calc(100svh-4rem)]');
});
