<?php

use Illuminate\Support\Facades\Blade;

describe('Popover component rendering', function () {
    it('renders a shrink-wrapped root and optional arrow', function () {
        $html = Blade::render(<<<'BLADE'
            <x-daisy::ui.overlay.popover :arrow="true" title="Popover title">
                Popover content
            </x-daisy::ui.overlay.popover>
        BLADE);

        expect($html)
            ->toContain('popover-root relative inline-flex w-fit align-middle')
            ->toContain('data-popover="data-popover"')
            ->toContain('popover-arrow')
            ->toContain('Popover title');
    });
});
