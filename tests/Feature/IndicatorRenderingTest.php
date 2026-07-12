<?php

use Illuminate\Support\Facades\Blade;

it('renders daisyui indicator positions and content variants', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.utilities.indicator
            label="12"
            position="bottom-start"
            color="secondary"
            item-class="sm:indicator-middle"
            data-testid="inbox-indicator"
        >
            <button type="button" class="btn">Inbox</button>
        </x-daisy::ui.utilities.indicator>

        <x-daisy::ui.utilities.indicator type="status" status-color="success">
            <span>Online</span>
        </x-daisy::ui.utilities.indicator>

        <x-daisy::ui.utilities.indicator position="middle-center">
            <x-slot:indicator>
                <button type="button" class="btn btn-primary">Apply</button>
            </x-slot:indicator>

            <article>Job</article>
        </x-daisy::ui.utilities.indicator>
    BLADE);

    expect($html)
        ->toContain('class="indicator" data-testid="inbox-indicator"')
        ->toContain('indicator-item indicator-bottom indicator-start sm:indicator-middle')
        ->toContain('badge badge-secondary">12</span>')
        ->toContain('indicator-item indicator-top indicator-end')
        ->toContain('status status-success')
        ->toContain('indicator-item indicator-middle indicator-center')
        ->toContain('btn btn-primary">Apply</button>')
        ->toContain('<article>Job</article>');
});
