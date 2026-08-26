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

Route::get('/_daisy-kit-test/csp/file-preview', function () {
    return response()
        ->view('workbench::csp-file-preview')
        ->header('Content-Security-Policy', "default-src 'none'; base-uri 'none'; object-src 'none'; script-src 'self'; style-src 'self'; style-src-attr 'none'; img-src 'self' data: blob:; connect-src 'self'; worker-src 'self'; frame-src 'self'; form-action 'none'");
})->name('workbench.csp.file-preview');
