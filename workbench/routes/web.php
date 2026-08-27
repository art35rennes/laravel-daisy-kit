<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::view('/', 'workbench::index')->name('workbench.index');

Route::get('/_daisy-kit-test/table/rows', function (Request $request) {
    $rows = collect([
        ['id' => 'case-1042', 'reference' => 'CASE-1042', 'customer' => 'Atelier 35', 'priority' => 'High', 'status' => 'Open'],
        ['id' => 'case-1043', 'reference' => 'CASE-1043', 'customer' => 'Ker Labs', 'priority' => 'Normal', 'status' => 'Review'],
        ['id' => 'case-1044', 'reference' => 'CASE-1044', 'customer' => 'Maison Delta', 'priority' => 'Urgent', 'status' => 'Open'],
        ['id' => 'case-1045', 'reference' => 'CASE-1045', 'customer' => 'Studio Armor', 'priority' => 'Low', 'status' => 'Waiting'],
        ['id' => 'case-1046', 'reference' => 'CASE-1046', 'customer' => 'Bretagne Cloud', 'priority' => 'High', 'status' => 'Review'],
        ['id' => 'case-1047', 'reference' => 'CASE-1047', 'customer' => 'Rennes Data', 'priority' => 'Normal', 'status' => 'Open'],
        ['id' => 'case-1048', 'reference' => 'CASE-1048', 'customer' => 'Océan Conseil', 'priority' => 'Low', 'status' => 'Closed'],
        ['id' => 'case-1049', 'reference' => 'CASE-1049', 'customer' => 'Lumen Studio', 'priority' => 'Urgent', 'status' => 'Review'],
        ['id' => 'case-1050', 'reference' => 'CASE-1050', 'customer' => 'Armor Habitat', 'priority' => 'High', 'status' => 'Waiting'],
        ['id' => 'case-1051', 'reference' => 'CASE-1051', 'customer' => 'Noroît Finance', 'priority' => 'Normal', 'status' => 'Closed'],
        ['id' => 'case-1052', 'reference' => 'CASE-1052', 'customer' => 'Pixel Ouest', 'priority' => 'Urgent', 'status' => 'Open'],
        ['id' => 'case-1053', 'reference' => 'CASE-1053', 'customer' => 'Korrigan Foods', 'priority' => 'Low', 'status' => 'Review'],
    ]);

    $filters = $request->array('filter');
    $search = mb_strtolower((string) ($filters['global'] ?? ''));

    if ($search !== '') {
        $rows = $rows->filter(fn (array $row): bool => collect($row)
            ->contains(fn (mixed $value): bool => str_contains(mb_strtolower((string) $value), $search)));
    }

    if (is_string($filters['customer'] ?? null) && $filters['customer'] !== '') {
        $customer = mb_strtolower($filters['customer']);
        $rows = $rows->filter(fn (array $row): bool => str_contains(mb_strtolower($row['customer']), $customer));
    }

    foreach (['priority' => 'priority', 'state' => 'status'] as $filterKey => $column) {
        if (is_string($filters[$filterKey] ?? null) && $filters[$filterKey] !== '') {
            $rows = $rows->where($column, $filters[$filterKey]);
        }
    }

    $sortExpression = $request->string('sort')->toString();
    $descending = str_starts_with($sortExpression, '-');
    $sort = [
        'cases.reference' => 'reference',
        'cases.customer' => 'customer',
        'cases.priority' => 'priority',
        'cases.status' => 'status',
    ][ltrim($sortExpression, '-')] ?? null;

    if ($sort !== null) {
        $rows = $descending
            ? $rows->sortByDesc($sort)
            : $rows->sortBy($sort);
    }

    $total = $rows->count();
    $page = $request->array('page');
    $pageNumber = max((int) ($page['number'] ?? 1), 1);
    $pageSize = min(max((int) ($page['size'] ?? 3), 1), 100);

    return response()->json([
        'data' => $rows->forPage($pageNumber, $pageSize)->values(),
        'meta' => [
            'current_page' => $pageNumber,
            'last_page' => max((int) ceil($total / $pageSize), 1),
            'per_page' => $pageSize,
            'total' => $total,
        ],
    ]);
})->name('workbench.table.rows');

Route::patch('/_daisy-kit-test/table/rows/{rowId}', function (Request $request, string $rowId) {
    $dirty = $request->array('dirty');
    $allowed = array_intersect_key($dirty, array_flip(['name', 'state', 'owner']));
    $row = [...$request->array('row'), ...$allowed, 'id' => $rowId];

    return response()->json(['row' => $row]);
})->where('rowId', '[A-Za-z0-9-]+')->name('workbench.table.update');

Route::get('/_daisy-kit-test/csp/map', function () {
    return response()
        ->view('workbench::csp-map')
        ->header('Content-Security-Policy', "default-src 'none'; base-uri 'none'; object-src 'none'; script-src 'self'; style-src 'self'; style-src-attr 'none'; img-src 'self' data: blob:; connect-src 'none'; worker-src 'self' blob:; frame-src 'self'; form-action 'none'");
})->name('workbench.csp.map');

Route::get('/_daisy-kit-test/files/preview.txt', function () {
    return response('Sandboxed file preview', 200, ['Content-Type' => 'text/plain']);
})->name('workbench.file-preview');

Route::get('/_daisy-kit-test/csp/file-preview', function () {
    return response()
        ->view('workbench::csp-file-preview')
        ->header('Content-Security-Policy', "default-src 'none'; base-uri 'none'; object-src 'none'; script-src 'self'; style-src 'self'; style-src-attr 'none'; img-src 'self' data: blob:; connect-src 'self'; worker-src 'self'; frame-src 'self'; form-action 'none'");
})->name('workbench.csp.file-preview');
