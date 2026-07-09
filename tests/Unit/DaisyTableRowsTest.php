<?php

use Art35rennes\DaisyKit\Support\DaisyTableRows;
use Illuminate\Support\Facades\View;

beforeEach(function (): void {
    View::addNamespace('table-test', dirname(__DIR__).'/Fixtures/views');
});

it('renders blade cells with stable table context', function (): void {
    $rows = DaisyTableRows::for([
        ['id' => 1, 'name' => 'Jane', 'actions' => 'open'],
    ], [
        ['key' => 'name', 'label' => 'Name'],
        ['key' => 'actions', 'label' => 'Actions', 'cell' => ['renderer' => 'blade', 'view' => 'table-test::table.actions']],
    ])
        ->table(['name' => 'Users'])
        ->renderCells();

    expect($rows)->toEqual([
        [
            'id' => 1,
            'name' => 'Jane',
            'actions' => '<button type="button" data-row="1" data-value="open">Jane actions Users</button>',
        ],
    ]);
});

it('supports view shorthand and preserves non custom values', function (): void {
    $rows = DaisyTableRows::for([
        (object) ['id' => 2, 'name' => 'John', 'actions' => 'edit'],
    ], [
        ['key' => 'name', 'label' => 'Name'],
        ['key' => 'actions', 'label' => 'Actions', 'view' => 'table-test::table.actions'],
    ])
        ->map(fn (object $item) => [
            'id' => $item->id,
            'name' => $item->name,
            'actions' => $item->actions,
        ])
        ->table(['name' => 'Users'])
        ->renderCells();

    expect($rows[0]['name'])->toBe('John')
        ->and($rows[0]['actions'])->toContain('data-value="edit"');
});

it('renders row detail blade views into trusted detail html', function (): void {
    $rows = DaisyTableRows::for([
        ['id' => 3, 'name' => 'June'],
    ], [
        ['key' => 'name', 'label' => 'Name'],
    ])
        ->table(['name' => 'Users'])
        ->rowDetailView('table-test::table.detail')
        ->renderCells();

    expect($rows[0]['_detailHtml'])->toBe('<aside data-row-detail="3">June Users</aside>');
});

it('fails clearly when a blade cell view is missing', function (): void {
    $render = fn () => DaisyTableRows::for([
        ['id' => 1, 'actions' => 'open'],
    ], [
        ['key' => 'actions', 'label' => 'Actions', 'view' => 'table-test::missing'],
    ])->renderCells();

    expect($render)->toThrow(InvalidArgumentException::class, 'Daisy table cell view [table-test::missing] does not exist.');
});

it('fails clearly when a row detail blade view is missing', function (): void {
    $render = fn () => DaisyTableRows::for([
        ['id' => 1, 'name' => 'Jane'],
    ], [
        ['key' => 'name', 'label' => 'Name'],
    ])
        ->rowDetailView('table-test::missing')
        ->renderCells();

    expect($render)->toThrow(InvalidArgumentException::class, 'Daisy table row detail view [table-test::missing] does not exist.');
});
