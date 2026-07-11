<?php

it('layers tooltip content only while the tooltip is active', function (): void {
    $css = file_get_contents(dirname(__DIR__, 2).'/resources/css/app.css');

    expect($css)
        ->toContain('--daisy-z-tooltip: 80')
        ->toContain('--daisy-z-tooltip-content: 81')
        ->toContain(':where(.tooltip):where(:hover, :focus-within, .tooltip-open)')
        ->toContain('> .tooltip-content')
        ->toContain('max-width: min(24rem, calc(100vw - 2rem))')
        ->toContain('overflow-wrap: anywhere')
        ->not->toContain('.tooltip {
  z-index');
});

it('ships viewport-aware tooltip portal styles', function (): void {
    $css = file_get_contents(dirname(__DIR__, 2).'/resources/css/app.css');
    $javascript = file_get_contents(dirname(__DIR__, 2).'/resources/js/app.js');

    expect($css)
        ->toContain('.daisy-tooltip-floating')
        ->toContain('[data-tooltip-ready="true"]')
        ->toContain('width: max-content')
        ->toContain('height: max-content')
        ->and($javascript)
        ->toContain("import('./modules/tooltip')")
        ->toContain('initAllTooltips(document)');

    expect(file_get_contents(dirname(__DIR__, 2).'/resources/js/modules/tooltip.js'))
        ->toContain("closest('dialog[open]')")
        ->toContain('document.body');
});
