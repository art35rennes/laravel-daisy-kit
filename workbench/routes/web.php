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

Route::get('/_daisy-kit-test/map/districts.geojson', function () {
    return response()->json([
        'type' => 'FeatureCollection',
        'features' => [[
            'type' => 'Feature',
            'id' => 'district-center',
            'properties' => ['name' => 'Central district', 'popup' => 'Central district'],
            'geometry' => [
                'type' => 'Polygon',
                'coordinates' => [[
                    [-1.72, 48.09],
                    [-1.61, 48.09],
                    [-1.61, 48.16],
                    [-1.72, 48.16],
                    [-1.72, 48.09],
                ]],
            ],
        ]],
    ], headers: ['Content-Type' => 'application/geo+json']);
})->name('workbench.map.districts');

Route::get('/_daisy-kit-test/map/tiles/{style}/{z}/{x}/{y}.png', function () {
    $transparentPng = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=', true);

    return response($transparentPng, 200, [
        'Cache-Control' => 'public, max-age=3600',
        'Content-Type' => 'image/png',
    ]);
})->where([
    'style' => '[a-z-]+',
    'z' => '[0-9]+',
    'x' => '[0-9]+',
    'y' => '[0-9]+',
])->name('workbench.map.tiles');

Route::get('/_daisy-kit-test/map/wms', function () {
    $transparentPng = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=', true);

    return response($transparentPng, 200, ['Content-Type' => 'image/png']);
})->name('workbench.map.wms');

Route::get('/_daisy-kit-test/csp/map', function () {
    return response()
        ->view('workbench::csp-map')
        ->header('Content-Security-Policy', "default-src 'none'; base-uri 'none'; object-src 'none'; script-src 'self'; style-src 'self'; style-src-attr 'none'; img-src 'self' data: blob:; connect-src 'none'; worker-src 'self' blob:; frame-src 'self'; form-action 'none'");
})->name('workbench.csp.map');

$serveFilePreviewFixture = static function (string $filename, string $contentType) {
    $path = dirname(__DIR__, 2).'/workbench/resources/fixtures/file-preview/'.$filename;
    $contents = file_get_contents($path);

    abort_unless($contents !== false, 404);

    return response($contents, 200, [
        'Content-Length' => (string) filesize($path),
        'Content-Type' => $contentType,
    ]);
};

Route::get('/_daisy-kit-test/files/preview.txt', function () use ($serveFilePreviewFixture) {
    return $serveFilePreviewFixture('preview.txt', 'text/plain; charset=UTF-8');
})->name('workbench.file-preview');

Route::get('/_daisy-kit-test/files/preview.svg', function () use ($serveFilePreviewFixture) {
    return $serveFilePreviewFixture('preview.svg', 'image/svg+xml');
})->name('workbench.file-preview.image');

Route::get('/_daisy-kit-test/files/preview.wav', function () use ($serveFilePreviewFixture) {
    return $serveFilePreviewFixture('preview.wav', 'audio/wav');
})->name('workbench.file-preview.audio');

Route::get('/_daisy-kit-test/files/preview.mp4', function () use ($serveFilePreviewFixture) {
    return $serveFilePreviewFixture('preview.mp4', 'video/mp4');
})->name('workbench.file-preview.video');

Route::get('/_daisy-kit-test/files/preview.pdf', function () use ($serveFilePreviewFixture) {
    return $serveFilePreviewFixture('preview.pdf', 'application/pdf');
})->name('workbench.file-preview.pdf');

Route::get('/_daisy-kit-test/files/preview.docx', function () use ($serveFilePreviewFixture) {
    return $serveFilePreviewFixture('preview.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
})->name('workbench.file-preview.docx');

Route::get('/_daisy-kit-test/files/preview-invalid.pdf', function () {
    return response('This is deliberately not a PDF.', 200, ['Content-Type' => 'text/plain']);
})->name('workbench.file-preview.invalid');

Route::get('/_daisy-kit-test/files/forecast.xlsx', function () {
    return response('Local download-only spreadsheet fixture.', 200, [
        'Content-Disposition' => 'attachment; filename="forecast.xlsx"',
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ]);
})->name('workbench.file-preview.spreadsheet');

Route::get('/_daisy-kit-test/csp/file-preview', function () {
    return response()
        ->view('workbench::csp-file-preview')
        ->header('Content-Security-Policy', "default-src 'none'; base-uri 'none'; object-src 'none'; script-src 'self'; style-src 'self'; style-src-attr 'none'; img-src 'self' data: blob:; connect-src 'self'; worker-src 'self'; frame-src 'self'; form-action 'none'");
})->name('workbench.csp.file-preview');
