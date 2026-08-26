<?php

declare(strict_types=1);

it('composes host DaisyUI primitives in every non-table public module shell', function (): void {
    $viewer = view('daisy-kit::components.forms.viewer')->render();
    $builder = view('daisy-kit::components.forms.builder')->render();
    $tree = view('daisy-kit::components.tree', ['searchable' => true])->render();
    $blueprint = view('daisy-kit::components.blueprint')->render();
    $preview = view('daisy-kit::components.file-preview')->render();
    $map = view('daisy-kit::components.map', ['drawing' => true, 'geolocation' => true])->render();

    expect($viewer)->toContain('class="card')
        ->and($builder)->toContain('class="card')
        ->and($tree)->toContain('class="input')
        ->and($blueprint)->toContain('class="card')
        ->and($preview)->toContain('class="card')
        ->and($map)->toContain('class="btn');
});
