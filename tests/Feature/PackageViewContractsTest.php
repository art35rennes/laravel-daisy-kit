<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\MessageBag;

it('renders the package layout component through its public alias', function () {
    config([
        'daisy-kit.auto_assets' => false,
        'daisy-kit.themes.custom' => [],
    ]);

    $html = Blade::render(<<<'BLADE'
        <x-daisy::layout.app title="Package Test">
            <p>Package body</p>
        </x-daisy::layout.app>
    BLADE);

    expect($html)
        ->toContain('<title>Package Test | Laravel</title>')
        ->toContain('Package body')
        ->toContain('name="csrf-token"');
});

it('renders every catalogued public template view directly', function () {
    config([
        'daisy-kit.auto_assets' => false,
        'daisy-kit.themes.custom' => [],
    ]);
    View::share('errors', new MessageBag);

    $catalog = json_decode(
        file_get_contents(packagePath('resources/boost/skills/daisy-kit-component-reuse/references/components.json')),
        true,
        flags: JSON_THROW_ON_ERROR,
    );

    $viewAliases = collect($catalog['templates'] ?? [])
        ->pluck('view_alias')
        ->filter()
        ->values();

    expect($viewAliases)->not->toBeEmpty();

    $viewAliases->each(function (string $viewAlias): void {
        try {
            $html = View::make($viewAlias)->render();
        } catch (Throwable $exception) {
            test()->fail(sprintf(
                'The public template view [%s] failed to render directly: %s',
                $viewAlias,
                $exception->getMessage(),
            ));
        }

        expect($html)->toBeString();
    });
});

it('applies the configured default theme to the package layout', function () {
    config([
        'daisy-kit.auto_assets' => false,
        'daisy-kit.themes.default' => 'suez',
    ]);

    $html = Blade::render(<<<'BLADE'
        <x-daisy::layout.app title="Package Test">
            <p>Package body</p>
        </x-daisy::layout.app>
    BLADE);

    expect($html)->toContain('data-theme="suez"');
});

it('lets an explicit package layout theme override the configured default theme', function () {
    config([
        'daisy-kit.auto_assets' => false,
        'daisy-kit.themes.default' => 'suez',
    ]);

    $html = Blade::render(<<<'BLADE'
        <x-daisy::layout.app title="Package Test" theme="dark">
            <p>Package body</p>
        </x-daisy::layout.app>
    BLADE);

    expect($html)
        ->toContain('data-theme="dark"')
        ->not->toContain('data-theme="suez"');
});

it('allows package layout themes to be disabled explicitly', function (string $themeExpression) {
    config([
        'daisy-kit.auto_assets' => false,
        'daisy-kit.themes.default' => 'suez',
    ]);

    $html = Blade::render(<<<BLADE
        <x-daisy::layout.app title="Package Test" {$themeExpression}>
            <p>Package body</p>
        </x-daisy::layout.app>
    BLADE);

    expect($html)->not->toContain('data-theme=');
})->with([
    'empty string' => 'theme=""',
    'false' => ':theme="false"',
]);

it('renders token-input through its public alias', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.inputs.token-input name="recipients" :values="['alice@example.com']" />
    BLADE);

    expect($html)
        ->toContain('data-module="token-input"')
        ->toContain('name="recipients[]"')
        ->toContain('alice@example.com');
});

it('renders table through its public alias', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.data-display.table
            :columns="[['key' => 'name', 'label' => 'Name']]"
            :rows="[['name' => 'Jane']]"
        />
    BLADE);

    expect($html)
        ->toContain('data-daisy-table="1"')
        ->toContain('Jane')
        ->toContain('Name');
});

it('renders ordered-list through its public alias', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.layout.ordered-list :items="[['id' => 'plan', 'label' => 'Plan V2']]" />
    BLADE);

    expect($html)
        ->toContain('Plan V2')
        ->toContain('data-ordered-list="1"');
});

it('renders truncate text through its public alias', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.utilities.truncate-text
            text="REF-2026-000001"
            max-width="max-w-48"
        />
    BLADE);

    expect($html)
        ->toContain('data-tip="REF-2026-000001"')
        ->toContain('tooltip tooltip-top')
        ->toContain('data-module="truncate-text"')
        ->toContain('data-truncate-text-title="REF-2026-000001"')
        ->toContain('data-truncate-text-reveal="tooltip"')
        ->toContain('class="min-w-0 max-w-48 truncate"')
        ->toContain('REF-2026-000001');
});

it('renders truncate text with a copyable popover interaction', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.utilities.truncate-text
            text="ORD-2026-000001 / International customer onboarding reference"
            max-width="max-w-40"
            reveal="both"
            position="bottom"
            popover-position="right"
            action-hint
        />
    BLADE);

    expect($html)
        ->toContain('data-tip="ORD-2026-000001 / International customer onboarding reference"')
        ->toContain('tooltip tooltip-bottom')
        ->toContain('data-truncate-text-reveal="both"')
        ->toContain('data-truncate-text-only-when-truncated="true"')
        ->toContain('daisy-truncate-popover')
        ->toContain('role="dialog"')
        ->toContain('select-text whitespace-normal break-words')
        ->toContain('cursor-pointer decoration-dotted underline')
        ->toContain('left-full')
        ->toContain('ORD-2026-000001 / International customer onboarding reference');
});

it('renders multiline truncate text and native title fallback', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.utilities.truncate-text
            text="A long customer-facing label"
            title="Full label"
            tag="p"
            :tooltip="false"
            :lines="3"
            class="text-sm"
        />
    BLADE);

    expect($html)
        ->toContain('<p')
        ->toContain('class="min-w-0 max-w-full line-clamp-3 text-sm"')
        ->toContain('title="Full label"')
        ->toContain('aria-label="A long customer-facing label"')
        ->not->toContain('data-module="truncate-text"')
        ->not->toContain('data-tip=');
});

it('renders the ui theme selector only when the package dev toggle is enabled', function () {
    config([
        'daisy-kit.dev.show_theme_selector' => true,
        'daisy-kit.themes.builtin' => ['light', 'dark'],
        'daisy-kit.themes.custom' => [
            'brand' => ['name' => 'brand'],
        ],
    ]);

    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.partials.theme-selector position="relative" placement="bottom-left" />
        @stack('scripts')
    BLADE);

    expect($html)
        ->toContain('relative bottom-4 left-4')
        ->toContain('theme-controller')
        ->toContain('brand')
        ->toContain('data-module="theme-controller"')
        ->not->toContain("const THEME_KEY = 'daisy-theme';");
});

it('renders the theme controller from configured themes and default theme', function () {
    config([
        'daisy-kit.themes.default' => 'brand',
        'daisy-kit.themes.builtin' => ['light', 'dark'],
        'daisy-kit.themes.custom' => [
            'brand' => ['name' => 'brand'],
        ],
    ]);

    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.advanced.theme-controller />
    BLADE);

    expect($html)
        ->toContain('data-module="theme-controller"')
        ->toContain('data-default-theme="brand"')
        ->toContain('value="light"')
        ->toContain('value="dark"')
        ->toContain('value="brand"')
        ->toContain('value="brand" class="join-item btn theme-controller btn-sm btn-ghost" aria-label="Brand" checked');
});
