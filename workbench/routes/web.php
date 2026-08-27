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
    ]);

    $search = mb_strtolower($request->string('filter')->toString());

    if ($search !== '') {
        $rows = $rows->filter(fn (array $row): bool => collect($row)
            ->contains(fn (mixed $value): bool => str_contains(mb_strtolower((string) $value), $search)));
    }

    $sort = $request->string('sort')->toString();

    if (in_array($sort, ['reference', 'customer', 'priority', 'status'], true)) {
        $rows = $request->string('direction')->toString() === 'desc'
            ? $rows->sortByDesc($sort)
            : $rows->sortBy($sort);
    }

    $total = $rows->count();
    $page = max($request->integer('page', 1), 1);
    $pageSize = min(max($request->integer('pageSize', 3), 1), 100);

    return response()->json([
        'rows' => $rows->forPage($page, $pageSize)->values(),
        'total' => $total,
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
