<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

$workbenchModules = [
    'table' => 'Table',
    'tree' => 'Tree',
    'blueprint' => 'Blueprint',
    'file-preview' => 'File Preview',
    'map' => 'Map',
    'copyable' => 'Copyable',
    'combobox' => 'Combobox',
    'signature' => 'Signature',
    'truncate' => 'Truncate',
    'scrollspy' => 'Scrollspy',
    'transfer-list' => 'Transfer List',
];

Route::view('/', 'workbench::index', [
    'module' => null,
    'modules' => $workbenchModules,
])->name('workbench.index');

require __DIR__.'/tree.php';

foreach (array_keys(array_diff_key($workbenchModules, ['tree' => true])) as $module) {
    Route::view($module, 'workbench::index', [
        'module' => $module,
        'modules' => $workbenchModules,
    ])->name('workbench.'.lcfirst(str_replace('-', '', ucwords($module, '-'))));
}

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

Route::get('/_daisy-kit-test/combobox/reviewers', function (Request $request) {
    $query = mb_strtolower($request->string('query')->trim()->toString());
    $reviewers = collect([
        ['value' => 'ada', 'label' => 'Ada Lovelace', 'description' => 'Platform'],
        ['value' => 'grace', 'label' => 'Grace Hopper', 'description' => 'Infrastructure'],
        ['value' => 'margaret', 'label' => 'Margaret Hamilton', 'description' => 'Flight software'],
        ['value' => 'katherine', 'label' => 'Katherine Johnson', 'description' => 'Research'],
    ]);

    if ($query !== '') {
        $reviewers = $reviewers->filter(fn (array $reviewer): bool => str_contains(
            mb_strtolower("{$reviewer['label']} {$reviewer['description']}"),
            $query,
        ));
    }

    return response()->json([
        'items' => $reviewers->values(),
        'nextCursor' => null,
    ]);
})->name('workbench.combobox.reviewers');

Route::get('/_daisy-kit-test/tree/search', function (Request $request) {
    $query = mb_strtolower($request->string('query')->trim()->toString());
    $items = [[
        'id' => 'documentation',
        'label' => 'Documentation',
        'expanded' => true,
        'children' => [
            ['id' => 'getting-started', 'label' => 'Getting started'],
            ['id' => 'api-reference', 'label' => 'API reference'],
        ],
    ], [
        'id' => 'packages',
        'label' => 'Packages',
        'children' => [
            ['id' => 'daisy-kit', 'label' => 'Laravel Daisy Kit'],
            ['id' => 'demo', 'label' => 'Demo application'],
        ],
    ]];

    if ($query === '') {
        return response()->json(['items' => $items]);
    }

    $filterItems = function (array $branches) use (&$filterItems, $query): array {
        return collect($branches)
            ->map(function (array $branch) use (&$filterItems, $query): ?array {
                $children = $filterItems($branch['children'] ?? []);

                if ($children === [] && ! str_contains(mb_strtolower($branch['label']), $query)) {
                    return null;
                }

                if ($children !== []) {
                    $branch['children'] = $children;
                    $branch['expanded'] = true;
                }

                return $branch;
            })
            ->filter()
            ->values()
            ->all();
    };

    return response()->json(['items' => $filterItems($items)]);
})->name('workbench.tree.search');

Route::post('/_daisy-kit-test/reviews', function (Request $request) {
    $returnRoute = [
        'combobox' => 'workbench.combobox',
        'signature' => 'workbench.signature',
        'transfer-list' => 'workbench.transferList',
    ][$request->string('return_to')->toString()] ?? 'workbench.combobox';

    $review = $request->validate([
        'reviewers' => ['array'],
        'reviewers.*' => ['string', 'max:100'],
        'assignees' => ['array'],
        'assignees.*' => ['string', 'max:100'],
        'approval_signature' => ['nullable', 'string', 'starts_with:data:image/png;base64,'],
    ]);

    return redirect()
        ->route($returnRoute)
        ->with('workbench.review.saved', $review);
})->name('workbench.reviews.store');

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

Route::get('/_daisy-kit-test/csp/strict', function () {
    return response()
        ->view('workbench::csp-strict')
        ->header('Content-Security-Policy', "default-src 'none'; base-uri 'none'; object-src 'none'; script-src 'self'; script-src-attr 'none'; style-src 'self'; style-src-attr 'none'; img-src 'self' data: blob:; connect-src 'self'; worker-src 'self' blob:; frame-src 'self'; form-action 'none'");
})->name('workbench.csp.strict');

Route::get('/_daisy-kit-test/csp/dependency-styles', function () {
    return response()
        ->view('workbench::csp-dependency-styles')
        ->header('Content-Security-Policy', "default-src 'none'; base-uri 'none'; object-src 'none'; script-src 'self'; script-src-attr 'none'; style-src 'self'; style-src-attr 'unsafe-inline'; img-src 'self' data: blob:; connect-src 'self'; worker-src 'self' blob:; frame-src 'self'; form-action 'none'");
})->name('workbench.csp.dependencyStyles');
