<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::view('/', 'workbench::index')->name('workbench.index');

Route::get('/_daisy-kit-test/csp/map', function () {
    return response()
        ->view('workbench::csp-map')
        ->header('Content-Security-Policy', "default-src 'none'; base-uri 'none'; object-src 'none'; script-src 'self'; style-src 'self'; style-src-attr 'none'; img-src 'self' data: blob:; connect-src 'none'; worker-src 'self' blob:; frame-src 'self'; form-action 'none'");
})->name('workbench.csp.map');

Route::get('/_daisy-kit-test/files/preview.txt', function () {
    return response('Sandboxed file preview', 200, ['Content-Type' => 'text/plain']);
})->name('workbench.file-preview');

Route::get('/file-preview-frame.html', function () {
    return response(file_get_contents(dirname(__DIR__, 2).'/dist/file-preview-frame.html'), 200, ['Content-Type' => 'text/html']);
});

Route::get('/file-preview-frame.js', function () {
    return response(file_get_contents(dirname(__DIR__, 2).'/dist/file-preview-frame.js'), 200, ['Content-Type' => 'text/javascript']);
});

Route::get('/file-preview-frame-bootstrap.js', function () {
    return response(file_get_contents(dirname(__DIR__, 2).'/dist/file-preview-frame-bootstrap.js'), 200, ['Content-Type' => 'text/javascript']);
});

Route::get('/chunks/{asset}', function (string $asset) {
    if (basename($asset) !== $asset || ! str_ends_with($asset, '.js')) {
        abort(404);
    }

    $path = dirname(__DIR__, 2).'/dist/chunks/'.$asset;

    abort_unless(is_file($path), 404);

    return response()->file($path, ['Content-Type' => 'text/javascript']);
})->where('asset', '[A-Za-z0-9._-]+');

Route::get('/_daisy-kit-test/csp/file-preview', function () {
    return response()
        ->view('workbench::csp-file-preview')
        ->header('Content-Security-Policy', "default-src 'none'; base-uri 'none'; object-src 'none'; script-src 'self'; style-src 'self'; style-src-attr 'none'; img-src 'self' data: blob:; connect-src 'self'; worker-src 'self'; frame-src 'self'; form-action 'none'");
})->name('workbench.csp.file-preview');
