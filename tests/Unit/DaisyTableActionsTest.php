<?php

use Art35rennes\DaisyKit\Support\DaisyTableActions;
use Illuminate\Support\Facades\View;

it('renders structured actions with escaped consumer values', function (): void {
    $actions = DaisyTableActions::normalize([
        'action' => 'open" data-injected="true',
        'label' => '<Open>',
        'variant' => 'error',
        'ariaLabel' => 'Open "record"',
        'disabled' => true,
    ]);
    $html = View::make('daisy::partials.table-actions', [
        'actions' => $actions,
        'rowId' => 'row"1',
        'columnId' => 'actions"column',
    ])->render();

    expect($html)
        ->toContain('class="btn btn-xs btn-error"')
        ->toContain('disabled')
        ->toContain('aria-disabled="true"')
        ->toContain('data-table-row-action="open&quot; data-injected=&quot;true"')
        ->toContain('data-table-row-id="row&quot;1"')
        ->toContain('data-table-column-id="actions&quot;column"')
        ->toContain('aria-label="Open &quot;record&quot;"')
        ->toContain('&lt;Open&gt;')
        ->not->toContain('data-injected="true"');
});

it('normalizes action lists and rejects invalid descriptors', function (): void {
    expect(DaisyTableActions::normalize([
        ['action' => 'archive', 'variant' => 'unknown'],
    ]))->toBe([[
        'action' => 'archive',
        'label' => 'archive',
        'variant' => 'ghost',
        'disabled' => false,
        'ariaLabel' => '',
    ]]);

    expect(fn (): array => DaisyTableActions::normalize('<button>Unsafe</button>'))
        ->toThrow(InvalidArgumentException::class, 'descriptor')
        ->and(fn (): array => DaisyTableActions::normalize([['label' => 'Missing action']]))
        ->toThrow(InvalidArgumentException::class, 'non-empty action')
        ->and(fn (): array => DaisyTableActions::normalize([['action' => 'valid'], 'invalid']))
        ->toThrow(InvalidArgumentException::class, 'descriptor');
});
