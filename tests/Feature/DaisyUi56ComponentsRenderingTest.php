<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use Illuminate\View\ComponentAttributeBag;

it('renders a native daisyui otp input', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.inputs.otp
            name="code"
            :length="6"
            value="123456"
            size="lg"
            color="error"
            joined
            required
        />
    BLADE);

    expect($html)
        ->toContain('class="otp otp-joined otp-lg otp-error')
        ->toContain('name="code"')
        ->toContain('value="123456"')
        ->toContain('maxlength="6"')
        ->toContain('pattern="[0-9]{6}"')
        ->toContain('autocomplete="one-time-code"')
        ->toContain('inputmode="numeric"')
        ->toContain('required');

    expect(substr_count($html, '<span></span>'))->toBe(6);
});

it('renders alphanumeric otp values without a numeric pattern', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.inputs.otp name="token" :length="4" :numeric="false" />
    BLADE);

    expect($html)
        ->toContain('name="token"')
        ->not->toContain('inputmode="numeric"')
        ->not->toContain('pattern=');
});

it('renders aura variants and safe element names', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.advanced.aura as="section" variant="rainbow" size="xl">
            Highlight
        </x-daisy::ui.advanced.aura>
        <x-daisy::ui.advanced.aura as="script">Safe fallback</x-daisy::ui.advanced.aura>
    BLADE);

    expect($html)
        ->toContain('<section class="aura aura-rainbow aura-xl')
        ->toContain('<div class="aura aura-md')
        ->not->toContain('<script');
});

it('renders megamenu modes and sizes', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.navigation.megamenu mode="full" size="lg" id="main-menu">
            <button popovertarget="services">Services</button>
            <div id="services" popover>Links</div>
        </x-daisy::ui.navigation.megamenu>
    BLADE);

    expect($html)
        ->toContain('id="main-menu"')
        ->toContain('class="megamenu megamenu-full megamenu-lg')
        ->toContain('class="megamenu-active" aria-hidden="true"')
        ->toContain('popovertarget="services"')
        ->toContain('id="services" popover');
});

it('exposes daisyui 5.6 modifiers on existing components', function () {
    $range = Blade::render('<x-daisy::ui.inputs.range vertical />');
    $tooltip = Blade::render('<x-daisy::ui.overlay.tooltip alignment="end" text="Help">Trigger</x-daisy::ui.overlay.tooltip>');
    $card = Blade::render('<x-daisy::ui.layout.card selectable :checked="true">Plan</x-daisy::ui.layout.card>');

    expect($range)->toContain('range range-vertical')
        ->and($tooltip)->toContain('tooltip tooltip-top tooltip-end')
        ->and($card)->toContain('cursor-pointer')
        ->and($card)->toContain('aria-checked="true"');
});

it('renders popover modals without dialog-only markup', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.overlay.modal id="help-modal" method="popover" title="Help">
            Content
        </x-daisy::ui.overlay.modal>
    BLADE);

    expect($html)
        ->toContain('class="modal modal-middle"')
        ->toContain('id="help-modal"')
        ->toContain('popover')
        ->toContain('popovertarget="help-modal"')
        ->toContain('popovertargetaction="hide"')
        ->not->toContain('<dialog')
        ->not->toContain('method="dialog"');
});

it('escapes text props while preserving explicit html values', function () {
    $unsafe = '<img src=x onerror=alert(1)>';

    $tooltip = View::make('daisy::components.ui.overlay.tooltip', [
        'content' => $unsafe,
        'slot' => 'Trigger',
        'attributes' => new ComponentAttributeBag,
    ])->render();
    $popconfirm = View::make('daisy::components.ui.overlay.popconfirm', [
        'message' => $unsafe,
        'slot' => 'Delete',
        'attributes' => new ComponentAttributeBag,
    ])->render();
    $tabs = View::make('daisy::components.ui.navigation.tabs', [
        'items' => [['label' => 'One', 'content' => $unsafe]],
        'radioName' => 'security-tabs',
        'attributes' => new ComponentAttributeBag,
    ])->render();
    $accordion = View::make('daisy::components.ui.advanced.accordion', [
        'items' => [['title' => 'One', 'content' => $unsafe]],
        'attributes' => new ComponentAttributeBag,
    ])->render();
    $trusted = View::make('daisy::components.ui.overlay.tooltip', [
        'content' => new HtmlString('<strong>Trusted</strong>'),
        'slot' => 'Trigger',
        'attributes' => new ComponentAttributeBag,
    ])->render();

    expect($tooltip)->toContain('&lt;img src=x onerror=alert(1)&gt;')
        ->and($popconfirm)->toContain('&lt;img src=x onerror=alert(1)&gt;')
        ->and($tabs)->toContain('&lt;img src=x onerror=alert(1)&gt;')
        ->and($accordion)->toContain('&lt;img src=x onerror=alert(1)&gt;')
        ->and($trusted)->toContain('<strong>Trusted</strong>');
});
