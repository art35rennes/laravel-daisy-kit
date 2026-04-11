<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\View\ViewException;

it('renders a local datatable with DaisyUI table classes and serialized options', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.data-display.datatable
            size="sm"
            zebra
            pin-rows
            pin-cols
            caption="Users"
            :columns="[
                ['key' => 'name', 'title' => 'Name'],
                ['key' => 'role', 'title' => 'Role', 'className' => 'text-right'],
            ]"
            :data="[
                ['name' => 'Jane', 'role' => 'Admin'],
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('data-daisy-datatable="1"')
        ->toContain('table table-zebra table-sm table-pin-rows table-pin-cols w-full')
        ->toContain('rounded-box border border-base-content/5 bg-base-100 p-4')
        ->toContain('Users')
        ->toContain('"serverSide":false')
        ->toContain('"responsive":false')
        ->toContain('Jane')
        ->toContain('Admin');
});

it('renders a server-side datatable with ajax and responsive options', function () {
    $html = View::make('daisy::components.ui.data-display.datatable', [
        'serverSide' => true,
        'responsive' => true,
        'ajax' => [
            'url' => '/api/users',
            'type' => 'GET',
        ],
        'columns' => [
            ['data' => 'name', 'title' => 'Name', 'name' => 'users.name'],
            ['data' => 'email', 'title' => 'Email'],
        ],
        'options' => [
            'pageLength' => 25,
            'scrollX' => true,
        ],
    ])->render();

    expect($html)
        ->toContain('data-daisy-datatable="1"')
        ->toContain('"serverSide":true')
        ->toContain('"responsive":true')
        ->toContain('"url":"\/api\/users"')
        ->toContain('"pageLength":25')
        ->toContain('"scrollX":true')
        ->toContain('<tbody>');
});

it('renders a datatable from structured slots', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.data-display.datatable :columns="[['key' => 'name', 'title' => 'Name']]">
            <x-slot:head>
                <tr><th>Name</th></tr>
            </x-slot:head>
            <x-slot:body>
                <tr><td>Jane</td></tr>
            </x-slot:body>
        </x-daisy::ui.data-display.datatable>
    BLADE);

    expect($html)
        ->toContain('<thead>')
        ->toContain('<tbody>')
        ->toContain('<tr><th>Name</th></tr>')
        ->toContain('<tr><td>Jane</td></tr>');
});

it('requires ajax when serverSide is enabled', function () {
    $render = fn () => View::make('daisy::components.ui.data-display.datatable', [
        'serverSide' => true,
        'columns' => [
            ['data' => 'name', 'title' => 'Name'],
        ],
    ])->render();

    expect($render)->toThrow(ViewException::class);
});

it('removes the legacy table component views from the package', function () {
    expect(View::exists('daisy::components.ui.data-display.table'))->toBeFalse();
    expect(View::exists('daisy::components.ui.advanced.table'))->toBeFalse();
});
