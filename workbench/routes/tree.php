<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Workbench\App\TreeExamples;

Route::get('/tree', function (Request $request) {
    app()->setLocale($request->query('lang') === 'fr' ? 'fr' : 'en');

    return response()->view('workbench::tree')->header('Content-Security-Policy', "default-src 'self'; base-uri 'none'; object-src 'none'; script-src 'self'; script-src-attr 'none'; style-src 'self'; style-src-attr 'none'; img-src 'self' data:; connect-src 'self'; form-action 'self'");
})->name('workbench.tree');

Route::get('/_daisy-kit-test/tree/catalogue/{region}', function (Request $request, string $region) {
    $cursor = max(0, $request->integer('cursor'));
    $centres = TreeExamples::centres($region);
    $items = array_slice($centres, $cursor, 2);
    $nextCursor = $cursor + count($items) < count($centres)
        ? (string) ($cursor + count($items))
        : null;

    return response()->json(['items' => $items, 'nextCursor' => $nextCursor]);
})->whereIn('region', ['west', 'north', 'south', 'east'])->name('workbench.tree.catalogue');

Route::get('/_daisy-kit-test/tree/catalogue-search', function (Request $request) {
    $query = mb_strtolower($request->string('query')->toString());
    $results = [];
    foreach (TreeExamples::catalogue() as $region) {
        $children = [];
        foreach (TreeExamples::centres($region['id']) as $centre) {
            $leaves = array_values(array_filter($centre['children'], fn (array $leaf): bool => str_contains(mb_strtolower($leaf['label']), $query)));

            if (str_contains(mb_strtolower($centre['label']), $query) || $leaves !== []) {
                $children[] = [...$centre, 'children' => $leaves === [] ? $centre['children'] : $leaves];
            }
        }

        if ($children !== []) {
            $results[] = [...$region, 'children' => $children];
        }
    }

    return response()->json(['items' => $results]);
})->name('workbench.tree.catalogueSearch');
