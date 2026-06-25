<?php

it('layers tooltip content only while the tooltip is active', function (): void {
    $css = file_get_contents(dirname(__DIR__, 2).'/resources/css/app.css');

    expect($css)
        ->toContain('--daisy-z-tooltip: 35')
        ->toContain('--daisy-z-tooltip-content: 36')
        ->toContain(':where(.tooltip):where(:hover, :focus-within, .tooltip-open)')
        ->toContain('> .tooltip-content')
        ->not->toContain('.tooltip {
  z-index');
});
